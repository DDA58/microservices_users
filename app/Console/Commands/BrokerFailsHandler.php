<?php

namespace App\Console\Commands;

use App\Models\FailingsBrokerJob;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Console\Command;
use Illuminate\Foundation\Application;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use PhpAmqpLib\Exception\AMQPIOException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\Connectors\RabbitMQConnector;
use App\Services\JobDispatcher\IJobDispatcher;

class BrokerFailsHandler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'broker_fails_handler:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start local worker for failings broker jobs';

    private Application $app;
    private IJobDispatcher $dispatcher;
    private Client $client;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Application $app, IJobDispatcher $dispatcher, Client $client)
    {
        parent::__construct();
        $this->app = $app;
        $this->dispatcher = $dispatcher;
        $this->client = $client;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $hosts = config('queue.connections.' . config('queue.default') . '.hosts');

        while (true) {
            try {
                foreach ($hosts as $host) {
                    Http::withHeaders([
                        'Host' => 'rabbitmq:5672',
                        'User-Agent' => 'curl/7.64.0',
                        'Accept' => '*/*',
                    ])->withOptions([
                        //'debug' => true,
                        //'allow_redirects' => true,
                        //'proxy' => 'rabbitmq:5672',
                        'verify' => false
                    ])->get($host['host'] . ':' . $host['port'] . '/');
                }
            } catch (ConnectionException $e) {
                sleep(1);
                continue;
            } catch (RequestException $e) {
                if ($e->getHandlerContext()['errno'] != 56) {
                    sleep(5);
                    continue;
                }
            } catch (\Throwable $t) {
                sleep(5);
                continue;
            }

            $fails = FailingsBrokerJob::orderByPriority()->get();

            if (!$fails->count()) {
                sleep(5);
                continue;
            }

            /** @var FailingsBrokerJob $fail */
            foreach ($fails as $fail) {
                try {
                    $this->dispatcher->dispatch(unserialize($fail->getSerializedObject()));
                    $fail->delete();
                } catch (\Throwable $t) {
                }
            }

            sleep(5);
        }
    }
}

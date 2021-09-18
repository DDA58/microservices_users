<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string priority
 * @property string serialized_object
 * @property string exception
 * @property string created_at
 *
 * @method static Builder orderByPriority
 */
class FailingsBrokerJob extends Model
{
    public $timestamps = false;

    public function getPriority(): string {
        return $this->priority;
    }

    public function getSerializedObject(): string {
        return $this->serialized_object;
    }

    public function getException(): string {
        return $this->exception;
    }

    /**
     * Scope a query to only include active users.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeOrderByPriority(Builder $query): Builder
    {
        return $query->orderByRaw('
                CASE WHEN priority = :high THEN 1
                    WHEN priority = :middle THEN 2
                    WHEN priority = :low THEN 3
                    ELSE 4
                END
            ', ['high' => 'high', 'middle' => 'middle', 'low']);
    }
}

<?php

namespace App\Traits;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use DateTimeImmutable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;

trait Syncable
{
    /**
     * Initialize the trait
     *
     * @return void
     */
    protected function initializeSyncable()
    {
        $this->append('_deleted');
    }

    /**
     * Prepare a date for array / JSON serialization.
     * Converts dates to millisecond precision for RxDB compatibility.
     *
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        $instance = $date instanceof DateTimeImmutable
            ? CarbonImmutable::instance($date)
            : Carbon::instance($date);

        return $instance->toDateTimeString('millisecond');
    }

    /**
     * Determine if model is deleted
     * Used by RxDB for soft delete synchronization
     *
     * @return bool
     */
    protected function getDeletedAttribute()
    {
        return null !== $this->deleted_at;
    }

    /**
     * Scope a query to only include models changed after given value.
     * This is used by rxdb-orion for incremental sync with checkpointing.
     */
    public function scopeMinUpdatedAt(Builder $query, string $updatedAt, ?string $id = null): Builder
    {
        return $query->where('updated_at', '>', $updatedAt)
            ->when($id, fn ($query) => $query->orWhere(function ($query) use ($updatedAt, $id) {
                $query->where('updated_at', $updatedAt)->where('id', '>', $id);
            })
            )
            ->orderBy('updated_at')
            ->orderBy('id');
    }
}

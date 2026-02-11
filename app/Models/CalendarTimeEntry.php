<?php

namespace App\Models;

use App\Models\Concerns\Blameable;
use App\Models\Concerns\HasVisibility;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $calendar_id
 * @property ?int $entity_id
 * @property int $day
 * @property int $month
 * @property int $year
 * @property int $start_hour
 * @property int $start_minute
 * @property int $duration
 * @property string $name
 * @property ?string $comment
 * @property ?string $colour
 * @property ?Calendar $calendar
 * @property ?Entity $entity
 */
class CalendarTimeEntry extends Model
{
    use Blameable;
    use HasFactory;
    use HasVisibility;

    protected $fillable = [
        'calendar_id',
        'entity_id',
        'day',
        'month',
        'year',
        'start_hour',
        'start_minute',
        'duration',
        'name',
        'comment',
        'colour',
        'visibility_id',
    ];

    /**
     * @return BelongsTo<Calendar, $this>
     */
    public function calendar(): BelongsTo
    {
        return $this->belongsTo(Calendar::class);
    }

    /**
     * @return BelongsTo<Entity, $this>
     */
    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }

    /**
     * Get formatted start time string (e.g. "14:30")
     */
    public function startTime(): string
    {
        return sprintf('%d:%02d', $this->start_hour, $this->start_minute);
    }

    /**
     * Calculate end hour from start + duration
     */
    public function endHour(): int
    {
        return $this->start_hour + intdiv($this->start_minute + $this->duration, 60);
    }

    /**
     * Calculate end minute from start + duration
     */
    public function endMinute(): int
    {
        return ($this->start_minute + $this->duration) % 60;
    }

    /**
     * Get formatted end time string (e.g. "16:15")
     */
    public function endTime(): string
    {
        return sprintf('%d:%02d', $this->endHour(), $this->endMinute());
    }

    /**
     * Get human-readable duration (e.g. "1h 30m")
     */
    public function durationFormatted(): string
    {
        $hours = intdiv($this->duration, 60);
        $minutes = $this->duration % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours}h {$minutes}m";
        }
        if ($hours > 0) {
            return "{$hours}h";
        }

        return "{$minutes}m";
    }
}

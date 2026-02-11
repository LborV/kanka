<?php

namespace App\Models;

use App\Models\Concerns\Blameable;
use App\Models\Concerns\HasVisibility;
use App\Models\Concerns\Sanitizable;
use App\Models\Scopes\CalendarWeatherScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class CalendarWeather
 *
 * @property int $id
 * @property int $calendar_id
 * @property string $weather
 * @property string $temperature
 * @property string $precipitation
 * @property string $wind
 * @property string $effect
 * @property int $year
 * @property int $month
 * @property int $day
 * @property ?int $hour
 * @property string $name
 * @property bool $is_generated
 * @property Calendar $calendar
 */
class CalendarWeather extends Model
{
    use Blameable;
    use CalendarWeatherScopes;
    use HasVisibility;
    use Sanitizable;

    public $table = 'calendar_weather';

    public $fillable = [
        'calendar_id',
        'weather',
        'temperature',
        'precipitation',
        'wind',
        'effect',
        'day',
        'month',
        'year',
        'visibility_id',
        'name',
        'hour',
        'is_generated',
    ];

    protected array $sanitizable = [
        'weather',
        'temperature',
        'precipitation',
        'wind',
        'effect',
        'name',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Calendar, $this>
     */
    public function calendar(): BelongsTo
    {
        return $this->belongsTo(Calendar::class);
    }

    public static array $periods = [
        0 => 'morning',
        1 => 'midday',
        2 => 'evening',
        3 => 'night',
    ];

    public function periodName(): string
    {
        if ($this->hour === null) {
            return __('calendars/weather.periods.whole_day');
        }

        return __('calendars/weather.periods.' . (self::$periods[$this->hour] ?? 'morning'));
    }

    public function tooltip(): string
    {
        $period = $this->hour !== null ? '<strong>' . $this->periodName() . "</strong><br />\n" : '';

        return $period .
            (! empty($this->temperature) ? __('calendars/weather.fields.temperature') . ': ' . e($this->temperature) . "<br />\n" : null) .
            (! empty($this->precipitation) ? __('calendars/weather.fields.precipitation') . ': ' . e($this->precipitation) . "<br />\n" : null) .
            (! empty($this->wind) ? __('calendars/weather.fields.wind') . ': ' . e($this->wind) . "<br />\n" : null) .
            (! empty($this->effect) ? __('calendars/weather.fields.effect') . ': ' . e($this->effect) . "<br />\n" : null);
    }

    public function weatherName(): string
    {
        if (! empty($this->name)) {
            return $this->name;
        }

        return __('calendars/weather.options.weather.' . $this->weather);
    }
}

<?php

namespace App\Services\Calendars;

use App\Models\CalendarWeather;
use App\Traits\CalendarAware;
use Illuminate\Support\Collection;

class WeatherService
{
    use CalendarAware;

    /** @var array<string, CalendarWeather> Primary weather per day (first period or whole-day) */
    protected array $effects = [];

    /** @var array<string, Collection<int, CalendarWeather>> All periods per day */
    protected array $periods = [];

    protected int $currentYear;

    public function currentYear(int $currentYear): self
    {
        $this->currentYear = $currentYear;

        return $this;
    }

    public function has(string $day): bool
    {
        return isset($this->effects[$day]);
    }

    public function get(string $day): CalendarWeather
    {
        return $this->effects[$day];
    }

    /**
     * Check if a day has multiple weather periods
     */
    public function hasPeriods(string $day): bool
    {
        return isset($this->periods[$day]) && $this->periods[$day]->count() > 1;
    }

    /**
     * Get all weather periods for a day
     *
     * @return Collection<int, CalendarWeather>
     */
    public function getPeriods(string $day): Collection
    {
        return $this->periods[$day] ?? collect();
    }

    public function build(): void
    {
        // First build parent weather, and override with local weather
        if ($this->calendar->calendar) {
            $this->loadWeather($this->calendar->calendar);
        }

        $this->loadWeather($this->calendar);
    }

    /**
     * Restrict weather visibility for players: only past and up to N days in the future
     * relative to the calendar's current date.
     */
    public function restrictForPlayer(int $maxFutureDays = 3): self
    {
        $currentDate = $this->calendar->date;
        if (empty($currentDate)) {
            return $this;
        }

        $months = $this->calendar->months();
        $currentAbsolute = $this->absoluteDay($currentDate, $months);
        $maxAbsolute = $currentAbsolute + $maxFutureDays;

        foreach (array_keys($this->effects) as $key) {
            if ($this->absoluteDay($key, $months) > $maxAbsolute) {
                unset($this->effects[$key]);
            }
        }

        foreach (array_keys($this->periods) as $key) {
            if ($this->absoluteDay($key, $months) > $maxAbsolute) {
                unset($this->periods[$key]);
            }
        }

        return $this;
    }

    /**
     * Check if a specific date is within the player-visible range
     * (past or up to N days in the future from calendar's current date)
     */
    public static function isDateVisibleForPlayer(
        \App\Models\Calendar $calendar,
        int $year,
        int $month,
        int $day,
        int $maxFutureDays = 3
    ): bool {
        $currentDate = $calendar->date;
        if (empty($currentDate)) {
            return true;
        }

        $months = $calendar->months();
        $dateKey = $year . '-' . $month . '-' . $day;
        $instance = new self();

        $currentAbsolute = $instance->absoluteDay($currentDate, $months);
        $dateAbsolute = $instance->absoluteDay($dateKey, $months);

        return $dateAbsolute <= $currentAbsolute + $maxFutureDays;
    }

    /**
     * Convert a date string (year-month-day) to an absolute day number for comparison.
     * Works with custom calendar month lengths and negative years.
     */
    protected function absoluteDay(string $date, array $months): int
    {
        $parts = explode('-', $date);

        // Handle negative years: "-5-3-10" splits to ["", "5", "3", "10"]
        if (str_starts_with($date, '-')) {
            $year = -(int) $parts[1];
            $month = (int) ($parts[2] ?? 1);
            $day = (int) ($parts[3] ?? 1);
        } else {
            $year = (int) $parts[0];
            $month = (int) ($parts[1] ?? 1);
            $day = (int) ($parts[2] ?? 1);
        }

        $daysPerYear = 0;
        foreach ($months as $m) {
            $daysPerYear += (int) $m['length'];
        }

        $total = $year * $daysPerYear;
        for ($i = 0; $i < $month - 1; $i++) {
            if (isset($months[$i])) {
                $total += (int) $months[$i]['length'];
            }
        }
        $total += $day;

        return $total;
    }

    protected function loadWeather(\App\Models\Calendar $calendar): void
    {
        $weathers = $calendar->calendarWeather()
            ->year($this->currentYear)
            ->orderBy('hour')
            ->get();

        /** @var CalendarWeather $weather */
        foreach ($weathers as $weather) {
            $key = $weather->year . '-' . $weather->month . '-' . $weather->day;

            // Group by day for period access
            if (! isset($this->periods[$key])) {
                $this->periods[$key] = collect();
            }
            $this->periods[$key]->push($weather);

            // Use the first entry (or whole-day entry) as the primary weather
            if (! isset($this->effects[$key]) || $weather->hour === null) {
                $this->effects[$key] = $weather;
            }
        }
    }
}

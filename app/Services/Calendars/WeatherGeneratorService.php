<?php

namespace App\Services\Calendars;

use App\Models\Calendar;
use App\Models\CalendarWeather;
use App\Traits\CalendarAware;

class WeatherGeneratorService
{
    use CalendarAware;

    /**
     * Weather types available for auto-generation (meteor excluded)
     */
    protected const WEATHER_TYPES = [
        'sun',
        'cloud-sun',
        'cloud',
        'wind',
        'smog',
        'cloud-sun-rain',
        'cloud-rain',
        'cloud-showers-heavy',
        'bolt',
        'snowflake',
    ];

    /**
     * Base transition probabilities: given current weather, probability of each next weather.
     * Values are relative weights (will be normalized).
     *
     * @var array<string, array<string, int>>
     */
    protected const TRANSITIONS = [
        'sun' => [
            'sun' => 50, 'cloud-sun' => 25, 'cloud' => 10, 'wind' => 5,
            'smog' => 3, 'cloud-sun-rain' => 4, 'cloud-rain' => 2,
            'cloud-showers-heavy' => 0, 'bolt' => 0, 'snowflake' => 1,
        ],
        'cloud-sun' => [
            'sun' => 25, 'cloud-sun' => 30, 'cloud' => 20, 'wind' => 5,
            'smog' => 3, 'cloud-sun-rain' => 10, 'cloud-rain' => 5,
            'cloud-showers-heavy' => 1, 'bolt' => 0, 'snowflake' => 1,
        ],
        'cloud' => [
            'sun' => 10, 'cloud-sun' => 15, 'cloud' => 30, 'wind' => 10,
            'smog' => 5, 'cloud-sun-rain' => 8, 'cloud-rain' => 12,
            'cloud-showers-heavy' => 5, 'bolt' => 3, 'snowflake' => 2,
        ],
        'wind' => [
            'sun' => 10, 'cloud-sun' => 15, 'cloud' => 25, 'wind' => 20,
            'smog' => 2, 'cloud-sun-rain' => 8, 'cloud-rain' => 10,
            'cloud-showers-heavy' => 5, 'bolt' => 3, 'snowflake' => 2,
        ],
        'smog' => [
            'sun' => 10, 'cloud-sun' => 15, 'cloud' => 30, 'wind' => 10,
            'smog' => 20, 'cloud-sun-rain' => 5, 'cloud-rain' => 5,
            'cloud-showers-heavy' => 2, 'bolt' => 1, 'snowflake' => 2,
        ],
        'cloud-sun-rain' => [
            'sun' => 15, 'cloud-sun' => 20, 'cloud' => 15, 'wind' => 5,
            'smog' => 2, 'cloud-sun-rain' => 15, 'cloud-rain' => 18,
            'cloud-showers-heavy' => 5, 'bolt' => 3, 'snowflake' => 2,
        ],
        'cloud-rain' => [
            'sun' => 5, 'cloud-sun' => 10, 'cloud' => 15, 'wind' => 5,
            'smog' => 2, 'cloud-sun-rain' => 12, 'cloud-rain' => 28,
            'cloud-showers-heavy' => 15, 'bolt' => 6, 'snowflake' => 2,
        ],
        'cloud-showers-heavy' => [
            'sun' => 2, 'cloud-sun' => 5, 'cloud' => 12, 'wind' => 5,
            'smog' => 1, 'cloud-sun-rain' => 8, 'cloud-rain' => 25,
            'cloud-showers-heavy' => 25, 'bolt' => 15, 'snowflake' => 2,
        ],
        'bolt' => [
            'sun' => 3, 'cloud-sun' => 5, 'cloud' => 15, 'wind' => 8,
            'smog' => 1, 'cloud-sun-rain' => 8, 'cloud-rain' => 25,
            'cloud-showers-heavy' => 20, 'bolt' => 12, 'snowflake' => 3,
        ],
        'snowflake' => [
            'sun' => 5, 'cloud-sun' => 8, 'cloud' => 20, 'wind' => 10,
            'smog' => 2, 'cloud-sun-rain' => 3, 'cloud-rain' => 5,
            'cloud-showers-heavy' => 2, 'bolt' => 0, 'snowflake' => 45,
        ],
    ];

    /**
     * Season type multipliers for weather probabilities.
     * Values > 1.0 increase probability, < 1.0 decrease it.
     *
     * @var array<string, array<string, float>>
     */
    protected const SEASON_MODIFIERS = [
        'warm' => [
            'sun' => 1.8, 'cloud-sun' => 1.5, 'cloud' => 1.0, 'wind' => 0.8,
            'smog' => 1.2, 'cloud-sun-rain' => 1.0, 'cloud-rain' => 0.7,
            'cloud-showers-heavy' => 0.5, 'bolt' => 0.8, 'snowflake' => 0.0,
        ],
        'cold' => [
            'sun' => 0.4, 'cloud-sun' => 0.6, 'cloud' => 1.3, 'wind' => 1.5,
            'smog' => 0.8, 'cloud-sun-rain' => 0.5, 'cloud-rain' => 0.7,
            'cloud-showers-heavy' => 0.4, 'bolt' => 0.3, 'snowflake' => 3.0,
        ],
        'wet' => [
            'sun' => 0.3, 'cloud-sun' => 0.6, 'cloud' => 1.2, 'wind' => 0.8,
            'smog' => 0.5, 'cloud-sun-rain' => 1.5, 'cloud-rain' => 2.0,
            'cloud-showers-heavy' => 1.8, 'bolt' => 1.3, 'snowflake' => 0.5,
        ],
        'dry' => [
            'sun' => 2.0, 'cloud-sun' => 1.5, 'cloud' => 1.0, 'wind' => 1.3,
            'smog' => 1.5, 'cloud-sun-rain' => 0.3, 'cloud-rain' => 0.2,
            'cloud-showers-heavy' => 0.1, 'bolt' => 0.2, 'snowflake' => 0.1,
        ],
        'temperate' => [
            'sun' => 1.0, 'cloud-sun' => 1.0, 'cloud' => 1.0, 'wind' => 1.0,
            'smog' => 1.0, 'cloud-sun-rain' => 1.0, 'cloud-rain' => 1.0,
            'cloud-showers-heavy' => 1.0, 'bolt' => 1.0, 'snowflake' => 0.5,
        ],
    ];

    /**
     * Temperature ranges by season type [min, max] in °C
     *
     * @var array<string, array{int, int}>
     */
    protected const TEMP_RANGES = [
        'warm' => [20, 35],
        'cold' => [-15, 5],
        'wet' => [10, 25],
        'dry' => [25, 40],
        'temperate' => [5, 25],
    ];

    /**
     * Temperature adjustments by weather type
     *
     * @var array<string, int>
     */
    protected const TEMP_ADJUSTMENTS = [
        'sun' => 3,
        'cloud-sun' => 1,
        'cloud' => -1,
        'wind' => -2,
        'smog' => 0,
        'cloud-sun-rain' => -1,
        'cloud-rain' => -3,
        'cloud-showers-heavy' => -5,
        'bolt' => -4,
        'snowflake' => -8,
    ];

    /**
     * Generate weather for a range of months in a given year
     */
    public function generate(int $year, int $monthStart, int $monthEnd, bool $overwrite = false): int
    {
        $months = $this->calendar->months();
        $count = 0;
        $previousWeather = 'cloud-sun';

        for ($m = $monthStart; $m <= $monthEnd; $m++) {
            if (! isset($months[$m - 1])) {
                continue;
            }
            $daysInMonth = (int) $months[$m - 1]['length'];
            $seasonType = $this->seasonForDate($m, 1);

            for ($d = 1; $d <= $daysInMonth; $d++) {
                $seasonType = $this->seasonForDate($m, $d);
                $count += $this->generateDay($year, $m, $d, $previousWeather, $seasonType, $overwrite);

                // Get the last period's weather for continuity into next day
                $lastPeriodWeather = CalendarWeather::datedForDay($this->calendar->id, $year, $m, $d)->latest('hour')->first();
                if ($lastPeriodWeather) {
                    $previousWeather = $lastPeriodWeather->weather;
                }
            }
        }

        return $count;
    }

    /**
     * Generate 4 periods of weather for a single day
     */
    public function generateDay(int $year, int $month, int $day, string $previousWeather, string $seasonType, bool $overwrite = false): int
    {
        // Seed the random generator for deterministic results
        $seed = crc32($this->calendar->id . '-' . $year . '-' . $month . '-' . $day);
        mt_srand($seed);

        $count = 0;
        $currentWeather = $previousWeather;

        for ($period = 0; $period <= 3; $period++) {
            // Check if entry already exists
            $existing = CalendarWeather::dated($this->calendar->id, $year, $month, $day)
                ->where('hour', $period)
                ->first();

            if ($existing && ! $overwrite) {
                $currentWeather = $existing->weather;

                continue;
            }

            // If existing and not generated, skip (manual entry)
            if ($existing && ! $existing->is_generated && ! $overwrite) {
                $currentWeather = $existing->weather;

                continue;
            }

            $currentWeather = $this->nextWeather($currentWeather, $seasonType);
            $temperature = $this->temperatureForWeather($currentWeather, $seasonType);
            $wind = $this->windForWeather($currentWeather);
            $precipitation = $this->precipitationForWeather($currentWeather);

            $data = [
                'calendar_id' => $this->calendar->id,
                'weather' => $currentWeather,
                'temperature' => $temperature,
                'precipitation' => $precipitation,
                'wind' => $wind,
                'effect' => null,
                'day' => $day,
                'month' => $month,
                'year' => $year,
                'hour' => $period,
                'is_generated' => true,
                'visibility_id' => 1,
                'name' => null,
            ];

            if ($existing) {
                $existing->update($data);
            } else {
                CalendarWeather::create($data);
            }
            $count++;
        }

        // Reset random seed
        mt_srand();

        return $count;
    }

    /**
     * Determine the next weather state using Markov chain transitions
     */
    public function nextWeather(string $current, string $seasonType): string
    {
        $baseWeights = self::TRANSITIONS[$current] ?? self::TRANSITIONS['cloud'];
        $modifiers = self::SEASON_MODIFIERS[$seasonType] ?? self::SEASON_MODIFIERS['temperate'];

        // Apply season modifiers
        $weights = [];
        foreach ($baseWeights as $weather => $weight) {
            $modifier = $modifiers[$weather] ?? 1.0;
            $weights[$weather] = max(0, (int) round($weight * $modifier));
        }

        return $this->weightedRandom($weights);
    }

    /**
     * Determine which season type applies for a given month/day
     */
    public function seasonForDate(int $month, int $day): string
    {
        $seasons = $this->calendar->seasons();
        if (empty($seasons)) {
            return 'temperate';
        }

        // Sort seasons by their start date
        usort($seasons, function ($a, $b) {
            $aVal = ($a['month'] * 1000) + $a['day'];
            $bVal = ($b['month'] * 1000) + $b['day'];

            return $aVal - $bVal;
        });

        // Find the season that contains this date
        $currentSeason = 'temperate';
        $dateVal = ($month * 1000) + $day;

        foreach ($seasons as $season) {
            $seasonStart = ($season['month'] * 1000) + $season['day'];
            if ($dateVal >= $seasonStart) {
                $currentSeason = $season['type'] ?? 'temperate';
            }
        }

        // If no season matched (date is before first season), use the last season (wraps around)
        if ($currentSeason === 'temperate' && ! empty($seasons)) {
            $lastSeason = end($seasons);
            $currentSeason = $lastSeason['type'] ?? 'temperate';
        }

        return $currentSeason;
    }

    /**
     * Generate a temperature string based on weather and season
     */
    public function temperatureForWeather(string $weather, string $seasonType): string
    {
        $range = self::TEMP_RANGES[$seasonType] ?? self::TEMP_RANGES['temperate'];
        $adjustment = self::TEMP_ADJUSTMENTS[$weather] ?? 0;

        $baseTemp = mt_rand($range[0], $range[1]);
        $temp = $baseTemp + $adjustment + mt_rand(-3, 3);

        $high = $temp + mt_rand(2, 5);
        $low = $temp - mt_rand(2, 5);

        return $low . '°C / ' . $high . '°C';
    }

    /**
     * Generate a wind description based on weather type
     */
    public function windForWeather(string $weather): string
    {
        $windDescriptions = [
            'sun' => ['Calm', 'Light breeze', 'Gentle breeze'],
            'cloud-sun' => ['Light breeze', 'Gentle breeze', 'Moderate breeze'],
            'cloud' => ['Light breeze', 'Moderate breeze', 'Fresh breeze'],
            'wind' => ['Strong breeze', 'Near gale', 'Gale', 'Strong gale'],
            'smog' => ['Calm', 'Light breeze'],
            'cloud-sun-rain' => ['Light breeze', 'Moderate breeze', 'Fresh breeze'],
            'cloud-rain' => ['Moderate breeze', 'Fresh breeze', 'Strong breeze'],
            'cloud-showers-heavy' => ['Fresh breeze', 'Strong breeze', 'Near gale'],
            'bolt' => ['Strong breeze', 'Near gale', 'Gale', 'Strong gale'],
            'snowflake' => ['Light breeze', 'Moderate breeze', 'Fresh breeze', 'Strong breeze'],
        ];

        $options = $windDescriptions[$weather] ?? ['Moderate breeze'];

        return $options[mt_rand(0, count($options) - 1)];
    }

    /**
     * Generate a precipitation description based on weather type
     */
    public function precipitationForWeather(string $weather): string
    {
        $descriptions = [
            'sun' => ['None'],
            'cloud-sun' => ['None', 'Trace'],
            'cloud' => ['None', 'Trace'],
            'wind' => ['None', 'Trace'],
            'smog' => ['None'],
            'cloud-sun-rain' => ['Light drizzle', 'Light rain', 'Scattered showers'],
            'cloud-rain' => ['Light rain', 'Moderate rain', 'Steady rain'],
            'cloud-showers-heavy' => ['Heavy rain', 'Downpour', 'Torrential rain'],
            'bolt' => ['Heavy rain', 'Downpour', 'Torrential rain with hail'],
            'snowflake' => ['Light snow', 'Moderate snow', 'Heavy snow', 'Blizzard'],
        ];

        $options = $descriptions[$weather] ?? ['None'];

        return $options[mt_rand(0, count($options) - 1)];
    }

    /**
     * Select a random item based on weighted probabilities
     *
     * @param array<string, int> $weights
     */
    protected function weightedRandom(array $weights): string
    {
        $total = array_sum($weights);
        if ($total <= 0) {
            return 'cloud';
        }

        $roll = mt_rand(1, $total);
        $cumulative = 0;

        foreach ($weights as $item => $weight) {
            $cumulative += $weight;
            if ($roll <= $cumulative) {
                return $item;
            }
        }

        return array_key_first($weights);
    }
}

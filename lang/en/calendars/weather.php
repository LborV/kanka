<?php

return [
    'create'        => [
        'helper'    => 'Add weather information that will show up on the calendar.',
        'success'   => 'Weather information added.',
        'title'     => 'Weather',
    ],
    'destroy'       => [
        'success'   => 'Weather information removed.',
    ],
    'edit'          => [
        'success'   => 'Weather information updated.',
        'title'     => 'Update Weather',
    ],
    'actions'       => [
        'generate'  => 'Generate weather',
    ],
    'fields'        => [
        'effect'        => 'Effect',
        'name'          => 'Name',
        'period'        => 'Period',
        'precipitation' => 'Precipitation',
        'temperature'   => 'Temperature',
        'weather'       => 'Weather',
        'wind'          => 'Wind',
    ],
    'generate'      => [
        'helper'        => 'Automatically generate weather for a range of months using the calendar\'s seasons.',
        'month_end'     => 'To month',
        'month_start'   => 'From month',
        'months'        => 'From month :start to month :end',
        'overwrite'     => 'Overwrite existing weather',
        'success'       => ':count weather entries generated.',
        'title'         => 'Generate Weather',
        'year'          => 'Year',
    ],
    'options'       => [
        'weather'   => [
            'bolt'                  => 'Thunder',
            'cloud'                 => 'Cloudy',
            'cloud-rain'            => 'Rainy',
            'cloud-showers-heavy'   => 'Heavy Rain',
            'cloud-sun'             => 'Cloudy and Sunny',
            'cloud-sun-rain'        => 'Cloud, Sun and Rain',
            'meteor'                => 'Meteor',
            'smog'                  => 'Smog',
            'snowflake'             => 'Snow',
            'sun'                   => 'Sunny',
            'wind'                  => 'Windy',
        ],
    ],
    'periods'       => [
        'evening'   => 'Evening',
        'midday'    => 'Midday',
        'morning'   => 'Morning',
        'night'     => 'Night',
        'whole_day' => 'Whole Day',
    ],
    'placeholders'  => [
        'effect'        => 'Magical or natural effect',
        'name'          => 'Optional custom weather text',
        'precipitation' => 'Amount of water',
        'temperature'   => 'Daily high and low',
        'wind'          => 'Wind speeds',
    ],
];

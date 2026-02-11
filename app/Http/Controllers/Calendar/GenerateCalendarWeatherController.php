<?php

namespace App\Http\Controllers\Calendar;

use App\Http\Controllers\Controller;
use App\Http\Requests\GenerateCalendarWeather;
use App\Models\Calendar;
use App\Models\Campaign;
use App\Services\Calendars\WeatherGeneratorService;

class GenerateCalendarWeatherController extends Controller
{
    public function __construct(
        protected WeatherGeneratorService $generator
    ) {}

    public function create(Campaign $campaign, Calendar $calendar)
    {
        $this->authorize('update', $calendar->entity);

        $months = $calendar->months();
        $year = request()->get('year', explode('-', $calendar->date ?? '1-1-1')[0]);

        return view('calendars.weather.generate', compact(
            'campaign',
            'calendar',
            'months',
            'year',
        ));
    }

    public function store(GenerateCalendarWeather $request, Campaign $campaign, Calendar $calendar)
    {
        $this->authorize('update', $calendar->entity);

        $count = $this->generator
            ->calendar($calendar)
            ->generate(
                (int) $request->input('year'),
                (int) $request->input('month_start'),
                (int) $request->input('month_end'),
                (bool) $request->input('overwrite', false)
            );

        $routeOptions = [$campaign, $calendar->entity, 'year' => $request->input('year')];
        if ($request->has('layout')) {
            $routeOptions['layout'] = $request->get('layout');
        }

        return redirect()->route('entities.show', $routeOptions)
            ->with('success', __('calendars/weather.generate.success', ['count' => $count]));
    }
}

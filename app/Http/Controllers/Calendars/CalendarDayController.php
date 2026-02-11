<?php

namespace App\Http\Controllers\Calendars;

use App\Http\Controllers\Controller;
use App\Models\Calendar;
use App\Models\Campaign;
use App\Traits\CampaignAware;
use App\Traits\GuestAuthTrait;
use Illuminate\Support\Str;

class CalendarDayController extends Controller
{
    use CampaignAware;
    use GuestAuthTrait;

    public function show(Campaign $campaign, Calendar $calendar)
    {
        $this->campaign($campaign)->authEntityView($calendar->entity);

        $date = request()->get('date', '1-1-1');
        [$year, $month, $day] = explode('-', $date);
        if (Str::startsWith($date, '-')) {
            [$year, $month, $day] = explode('-', mb_trim($date, '-'));
            $year = "-{$year}";
        }

        $year = (int) $year;
        $month = (int) $month;
        $day = (int) $day;

        $canEdit = auth()->check() && auth()->user()->can('update', $calendar->entity);

        $entries = $calendar->calendarTimeEntries()
            ->where('day', $day)
            ->where('month', $month)
            ->where('year', $year)
            ->with('entity')
            ->orderBy('start_hour')
            ->orderBy('start_minute')
            ->get();

        $hoursCount = $calendar->hoursInDay();

        return view('calendars.day', compact(
            'campaign',
            'calendar',
            'year',
            'month',
            'day',
            'entries',
            'hoursCount',
            'canEdit',
        ));
    }
}

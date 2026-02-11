<?php

namespace App\Http\Controllers\Calendars;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCalendarTimeEntry;
use App\Models\Calendar;
use App\Models\CalendarTimeEntry;
use App\Models\Campaign;

class CalendarTimeEntryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create(Campaign $campaign, Calendar $calendar)
    {
        $this->authorize('update', $calendar->entity);

        $date = request()->get('date', '1-1-1');
        [$year, $month, $day] = $this->parseDate($date);
        $hour = (int) request()->get('hour', 0);
        $entry = new CalendarTimeEntry();

        return view('calendars.time-entries.create', compact(
            'campaign',
            'calendar',
            'year',
            'month',
            'day',
            'hour',
            'entry',
        ));
    }

    public function store(StoreCalendarTimeEntry $request, Campaign $campaign, Calendar $calendar)
    {
        $this->authorize('update', $calendar->entity);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        $data = $request->validated();
        $data['calendar_id'] = $calendar->id;
        CalendarTimeEntry::create($data);

        $routeOptions = [
            $campaign,
            $calendar,
            'date' => "{$data['year']}-{$data['month']}-{$data['day']}",
        ];

        return redirect()->route('calendars.day', $routeOptions)
            ->with('success', __('calendars/time-entries.create.success'));
    }

    public function edit(Campaign $campaign, Calendar $calendar, CalendarTimeEntry $calendarTimeEntry)
    {
        $this->authorize('update', $calendar->entity);

        $entry = $calendarTimeEntry;
        $year = $entry->year;
        $month = $entry->month;
        $day = $entry->day;
        $hour = $entry->start_hour;

        return view('calendars.time-entries.edit', compact(
            'campaign',
            'calendar',
            'entry',
            'year',
            'month',
            'day',
            'hour',
        ));
    }

    public function update(StoreCalendarTimeEntry $request, Campaign $campaign, Calendar $calendar, CalendarTimeEntry $calendarTimeEntry)
    {
        $this->authorize('update', $calendar->entity);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        $calendarTimeEntry->update($request->validated());

        $routeOptions = [
            $campaign,
            $calendar,
            'date' => "{$calendarTimeEntry->year}-{$calendarTimeEntry->month}-{$calendarTimeEntry->day}",
        ];

        return redirect()->route('calendars.day', $routeOptions)
            ->with('success', __('calendars/time-entries.edit.success'));
    }

    public function destroy(Campaign $campaign, Calendar $calendar, CalendarTimeEntry $calendarTimeEntry)
    {
        $this->authorize('update', $calendar->entity);

        $routeOptions = [
            $campaign,
            $calendar,
            'date' => "{$calendarTimeEntry->year}-{$calendarTimeEntry->month}-{$calendarTimeEntry->day}",
        ];

        $calendarTimeEntry->delete();

        return redirect()->route('calendars.day', $routeOptions)
            ->with('success', __('calendars/time-entries.destroy.success'));
    }

    /**
     * Parse a date string (handles negative years)
     *
     * @return array{int, int, int}
     */
    protected function parseDate(string $date): array
    {
        if (str_starts_with($date, '-')) {
            [$year, $month, $day] = explode('-', mb_trim($date, '-'));

            return [(int) ('-' . $year), (int) $month, (int) $day];
        }

        [$year, $month, $day] = explode('-', $date);

        return [(int) $year, (int) $month, (int) $day];
    }
}

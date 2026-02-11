<?php

namespace App\Livewire\Widgets;

use App\Facades\CampaignCache;
use App\Facades\UserCache;
use App\Models\Calendar;
use App\Models\CalendarTimeEntry;
use App\Models\Campaign;
use App\Models\CampaignDashboardWidget;
use Illuminate\Support\Collection;
use Livewire\Component;

class ScheduleWidget extends Component
{
    public CampaignDashboardWidget $widget;

    public Campaign $campaign;

    public bool $readyToLoad = false;

    public function mount(CampaignDashboardWidget $widget, Campaign $campaign): void
    {
        $this->widget = $widget;
        $this->campaign = $campaign;
    }

    public function loadSchedule(): void
    {
        $this->readyToLoad = true;

        request()->route()->setParameter('campaign', $this->campaign);
        UserCache::campaign($this->campaign);
        CampaignCache::campaign($this->campaign);
    }

    public function render()
    {
        $entries = collect();
        $calendar = null;
        $canEdit = false;
        $year = $month = $day = null;

        if ($this->readyToLoad && $this->widget->entity) {
            $calendar = $this->widget->entity->child;
            if ($calendar instanceof Calendar && ! $calendar->missingDetails()) {
                [$year, $month, $day] = explode('-', $calendar->date);
                $year = (int) $year;
                $month = (int) $month;
                $day = (int) $day;

                $entries = $calendar->calendarTimeEntries()
                    ->where('day', $day)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->with('entity')
                    ->orderBy('start_hour')
                    ->orderBy('start_minute')
                    ->get();

                $canEdit = auth()->check() && auth()->user()->can('update', $calendar->entity);
            }
        }

        return view('livewire.widgets.schedule-widget', compact(
            'entries',
            'calendar',
            'canEdit',
            'year',
            'month',
            'day',
        ));
    }
}

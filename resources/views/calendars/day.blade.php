<?php
/**
 * @var \App\Models\Calendar $calendar
 * @var \App\Models\Campaign $campaign
 * @var \Illuminate\Support\Collection $entries
 * @var \Illuminate\Support\Collection $weatherPeriods
 * @var int $year
 * @var int $month
 * @var int $day
 * @var int $hoursCount
 * @var bool $canEdit
 */
$months = $calendar->months();
$monthName = isset($months[$month - 1]) ? $months[$month - 1]['name'] : $month;
$dateLabel = $calendar->niceDate("{$year}-{$month}-{$day}");
?>
@extends('layouts.app', [
    'title' => __('calendars/time-entries.day.title', ['name' => $calendar->name]),
    'breadcrumbs' => [
        Breadcrumb::campaign($campaign)->entity($calendar->entity)->list(),
        Breadcrumb::show(),
        __('calendars/time-entries.day.breadcrumb'),
    ],
    'canonical' => true,
])

@section('content')
<div class="flex flex-col gap-4">
    <div class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('entities.show', [$campaign, $calendar->entity, 'year' => $year, 'month' => $month]) }}" class="btn2 btn-sm">
                <x-icon class="fa-solid fa-chevron-left" />
                {{ __('calendars/time-entries.day.back') }}
            </a>
            <h3 class="text-lg font-bold m-0">
                {{ $dateLabel }}
            </h3>
        </div>
        @if ($canEdit)
            <a href="#" class="btn2 btn-sm btn-primary" data-toggle="dialog" data-url="{{ route('calendars.time-entries.create', [$campaign, $calendar, 'date' => "{$year}-{$month}-{$day}"]) }}">
                <x-icon class="plus" />
                {{ __('calendars/time-entries.actions.add') }}
            </a>
        @endif
    </div>

    @if ($weatherPeriods->isNotEmpty())
        <x-box>
            <div class="flex flex-col gap-2">
                <h4 class="text-sm font-bold m-0">{{ __('calendars/weather.fields.weather') }}</h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach ($weatherPeriods as $wp)
                        <div class="flex items-center gap-2 p-2 rounded bg-base-200/50">
                            <x-icon class="fa-solid fa-{{ $wp->weather }} text-lg" />
                            <div class="flex flex-col">
                                <span class="text-sm font-medium">{{ $wp->periodName() }}</span>
                                <span class="text-xs text-neutral-content">{{ $wp->weatherName() }}</span>
                                @if (!empty($wp->temperature))
                                    <span class="text-xs text-neutral-content">{{ $wp->temperature }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </x-box>
    @endif

    <x-box :padding="false">
        <div class="overflow-x-auto">
            <table class="table table-fixed w-full">
                <thead>
                    <tr>
                        <th class="w-24">{{ __('calendars/time-entries.day.hour') }}</th>
                        <th>{{ __('calendars/time-entries.day.entries') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($h = 0; $h < $hoursCount; $h++)
                        @php
                            $hourEntries = $entries->filter(function ($entry) use ($h) {
                                $endHour = $entry->endHour();
                                return $entry->start_hour == $h || ($entry->start_hour < $h && $endHour > $h);
                            });
                        @endphp
                        <tr class="h-16 border-b hover:bg-base-200/50 @if ($canEdit) cursor-pointer @endif"
                            @if ($canEdit) data-toggle="dialog" data-url="{{ route('calendars.time-entries.create', [$campaign, $calendar, 'date' => "{$year}-{$month}-{$day}", 'hour' => $h]) }}" @endif>
                            <td class="text-sm text-neutral-content align-top p-2 border-r">
                                {{ $calendar->hourName($h) }}
                            </td>
                            <td class="p-1.5 align-top">
                                <div class="flex flex-col gap-1">
                                    @foreach ($hourEntries as $entry)
                                        @if ($entry->start_hour == $h)
                                            <div class="calendar-time-entry rounded p-2 text-sm flex items-center gap-2 cursor-pointer {{ !empty($entry->colour) ? '' : 'bg-primary/10 border border-primary/20' }}"
                                                @if (!empty($entry->colour)) style="background-color: {{ $entry->colour }}20; border: 1px solid {{ $entry->colour }}40;" @endif
                                                @if ($canEdit) data-toggle="dialog" data-url="{{ route('calendars.time-entries.edit', [$campaign, $calendar, $entry]) }}" @endif
                                                onclick="event.stopPropagation()">
                                                <div class="flex flex-col gap-0.5 grow">
                                                    <div class="flex items-center gap-2">
                                                        <span class="font-medium">{{ $entry->name }}</span>
                                                        @if ($entry->entity)
                                                            <a href="{{ $entry->entity->url() }}" class="text-link text-xs" onclick="event.stopPropagation()">
                                                                {{ $entry->entity->name }}
                                                            </a>
                                                        @endif
                                                    </div>
                                                    <span class="text-xs text-neutral-content">
                                                        {{ $entry->startTime() }} - {{ $entry->endTime() }}
                                                        ({{ $entry->durationFormatted() }})
                                                    </span>
                                                    @if ($entry->comment)
                                                        <span class="text-xs text-neutral-content">{{ $entry->comment }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </x-box>
</div>
@endsection

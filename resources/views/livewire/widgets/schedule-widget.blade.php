<?php
/**
 * @var \App\Models\CampaignDashboardWidget $widget
 * @var \App\Models\Campaign $campaign
 * @var \App\Models\Calendar|null $calendar
 * @var \Illuminate\Support\Collection $entries
 * @var bool $canEdit
 * @var int|null $year
 * @var int|null $month
 * @var int|null $day
 */
?>
<div wire:init="loadSchedule">
    @if (!$readyToLoad)
        <div class="text-center py-10 text-2xl">
            <x-icon class="load" />
        </div>
    @elseif ($calendar)
        <x-widgets.previews.head :widget="$widget" :campaign="$campaign" :entity="$widget->entity" />
        <div class="p-4">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-bold m-0">
                    {{ $calendar->niceDate("{$year}-{$month}-{$day}") }}
                </h4>
                <div class="flex gap-2">
                    @if ($canEdit)
                        <a href="#" class="btn2 btn-xs btn-primary" data-toggle="dialog" data-url="{{ route('calendars.time-entries.create', [$campaign, $calendar, 'date' => "{$year}-{$month}-{$day}"]) }}">
                            <x-icon class="plus" />
                        </a>
                    @endif
                    <a href="{{ route('calendars.day', [$campaign, $calendar, 'date' => "{$year}-{$month}-{$day}"]) }}" class="btn2 btn-xs" title="{{ __('calendars/time-entries.day.breadcrumb') }}">
                        <x-icon class="fa-regular fa-expand" />
                    </a>
                </div>
            </div>

            @if ($entries->isEmpty())
                <p class="text-sm text-neutral-content">
                    {{ __('calendars/time-entries.widget.empty') }}
                </p>
            @else
                <div class="flex flex-col gap-1.5">
                    @foreach ($entries as $entry)
                        <div class="rounded p-2 text-sm flex items-center gap-2 {{ !empty($entry->colour) ? '' : 'bg-base-200' }} @if ($canEdit) cursor-pointer @endif"
                            @if (!empty($entry->colour)) style="background-color: {{ $entry->colour }}20;" @endif
                            @if ($canEdit) data-toggle="dialog" data-url="{{ route('calendars.time-entries.edit', [$campaign, $calendar, $entry]) }}" @endif>
                            <span class="text-xs text-neutral-content whitespace-nowrap">
                                {{ $entry->startTime() }}
                            </span>
                            <span class="grow truncate font-medium">{{ $entry->name }}</span>
                            <span class="text-xs text-neutral-content">{{ $entry->durationFormatted() }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @else
        <p class="p-4 text-neutral-content">{{ __('calendars/time-entries.widget.missing') }}</p>
    @endif
</div>

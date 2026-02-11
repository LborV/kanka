<?php
/**
 * @var \App\Models\CalendarTimeEntry $entry
 * @var \App\Models\Calendar $calendar
 * @var \App\Models\Campaign $campaign
 * @var int $year
 * @var int $month
 * @var int $day
 * @var int $hour
 */
$hoursCount = $calendar->hoursInDay();
$hourOptions = [];
for ($h = 0; $h < $hoursCount; $h++) {
    $hourOptions[$h] = $calendar->hourName($h);
}
?>
<x-grid type="1/1">
    <x-forms.field field="name" required :label="__('calendars/time-entries.fields.name')">
        <input type="text" name="name" value="{{ old('name', $entry->name ?? null) }}" maxlength="191" class="w-full" placeholder="{{ __('calendars/time-entries.placeholders.name') }}" required />
    </x-forms.field>

    @include('cruds.fields.entity', [
        'required' => false,
        'allowClear' => true,
        'allowNew' => false,
        'preset' => $entry->entity ?? null,
        'dropdownParent' => request()->ajax() ? '#primary-dialog' : null,
    ])

    <x-grid type="3/3">
        <x-forms.field field="year" :label="__('calendars.fields.year')">
            <input type="number" name="year" value="{{ old('year', $year) }}" />
        </x-forms.field>

        <x-forms.field field="month" :label="__('calendars.fields.month')">
            <x-forms.select
                name="month"
                :options="$calendar->monthList()"
                :selected="old('month', $month)"
            />
        </x-forms.field>

        <x-forms.field field="day" :label="__('calendars.fields.day')">
            <x-forms.select
                name="day"
                :options="$calendar->dayList($month)"
                :selected="old('day', $day)"
            />
        </x-forms.field>
    </x-grid>

    <x-grid>
        <x-forms.field field="start_hour" required :label="__('calendars/time-entries.fields.start_hour')">
            <x-forms.select
                name="start_hour"
                :options="$hourOptions"
                :selected="old('start_hour', $entry->start_hour ?? $hour ?? 0)"
            />
        </x-forms.field>

        <x-forms.field field="start_minute" required :label="__('calendars/time-entries.fields.start_minute')">
            <input type="number" name="start_minute" value="{{ old('start_minute', $entry->start_minute ?? 0) }}" min="0" max="59" class="w-full" />
        </x-forms.field>

        <x-forms.field
            field="duration"
            required
            :label="__('calendars/time-entries.fields.duration')"
            :helper="__('calendars/time-entries.hints.duration')"
            tooltip>
            <input type="number" name="duration" value="{{ old('duration', $entry->duration ?? 60) }}" min="1" class="w-full" placeholder="{{ __('calendars/time-entries.placeholders.duration') }}" />
        </x-forms.field>

        @include('cruds.fields.colour_picker', ['default' => '#cccccc', 'model' => $entry ?? null])

        @include('cruds.fields.visibility_id', ['model' => $entry ?? null])
    </x-grid>

    <x-forms.field field="comment" :label="__('calendars/time-entries.fields.comment')">
        <input type="text" name="comment" value="{{ old('comment', $entry->comment ?? null) }}" maxlength="191" class="w-full" placeholder="{{ __('calendars/time-entries.placeholders.comment') }}" />
    </x-forms.field>
</x-grid>

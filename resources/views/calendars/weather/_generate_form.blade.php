<?php
/**
 * @var \App\Models\Calendar $calendar
 * @var \App\Models\Campaign $campaign
 * @var array $months
 * @var int|string $year
 */
$monthOptions = [];
foreach ($months as $index => $month) {
    $monthOptions[$index + 1] = $month['name'];
}
?>
<x-grid type="1/1">
    <x-helper>
        <p>{{ __('calendars/weather.generate.helper') }}</p>
    </x-helper>

    <x-forms.field
        field="year"
        required
        :label="__('calendars/weather.generate.year')">
        <input type="number" name="year" value="{{ old('year', $year) }}" required />
    </x-forms.field>

    <x-grid>
        <x-forms.field
            field="month_start"
            required
            :label="__('calendars/weather.generate.month_start')">
            <x-forms.select name="month_start" :options="$monthOptions" :selected="old('month_start', 1)" />
        </x-forms.field>

        <x-forms.field
            field="month_end"
            required
            :label="__('calendars/weather.generate.month_end')">
            <x-forms.select name="month_end" :options="$monthOptions" :selected="old('month_end', count($months))" />
        </x-forms.field>
    </x-grid>

    <x-forms.field
        field="overwrite"
        :label="__('calendars/weather.generate.overwrite')">
        <input type="checkbox" name="overwrite" value="1" @if (old('overwrite', false)) checked="checked" @endif />
        {{ __('calendars/weather.generate.overwrite') }}
    </x-forms.field>
</x-grid>

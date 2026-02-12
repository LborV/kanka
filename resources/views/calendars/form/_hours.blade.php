<?php /** @var \App\Models\Calendar $model */?>
<x-grid type="1/1">
    <x-forms.field field="hours" :label="__('calendars.fields.hours')" :helper="__('calendars.hints.hours')">
        <input type="hidden" name="hour_name" />
    </x-forms.field>

    <button class="btn2 btn-sm btn-outline dynamic-row-add" data-template="template_hour" data-target="calendar-hours" title="{{ __('calendars.actions.add_hour') }}">
        <x-icon class="plus" /> {{ __('calendars.actions.add_hour') }}
    </button>

    <?php
    $hours = [];
    $names = old('hour_name');
    if (!empty($names)) {
        foreach ($names as $name) {
            if (!empty($name)) {
                $hours[] = ['name' => $name];
            }
        }
    } elseif (isset($model)) {
        $hours = $model->hours();
    } elseif (isset($source)) {
        $hours = $source->child->hours();
    } ?>
    <div class="calendar-hours sortable-elements" data-handle=".sortable-handler">
        @foreach ($hours as $hour)
            <div class="parent-delete-row">
                <div class="flex items-center gap-2">
                    <div class="sortable-handler p-2 cursor-move">
                        <x-icon class="fa-solid fa-grip-vertical" />
                    </div>
                    <div class="grow field">
                        <label class="sr-only">{{ __('calendars.parameters.hours.name') }}</label>
                        <input type="text" name="hour_name[]" value="{{ $hour['name'] }}" placeholder="{{ __('calendars.parameters.hours.name') }}" aria-label="{{ __('calendars.parameters.hours.name') }}" maxlength="191" class="w-full" />
                    </div>
                    <div class="dynamic-row-delete btn2 btn-error btn-outline btn-sm" title="{{ __('crud.remove') }}">
                        <x-icon class="trash" />
                        <span class="sr-only">{{ __('crud.remove') }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</x-grid>

@section('modals')
    @parent
    <template id="template_hour">
        <div class="parent-delete-row">
            <div class="flex items-center gap-2">
                <div class="sortable-handler p-2 cursor-move">
                    <x-icon class="fa-solid-grip-vertical" />
                </div>
                <div class="grow field">
                    <label class="sr-only">{{ __('calendars.parameters.hours.name') }}</label>
                    <input type="text" name="hour_name[]" value="" placeholder="{{ __('calendars.parameters.hours.name') }}" aria-label="{{ __('calendars.parameters.hours.name') }}" maxlength="191" class="w-full" />
                </div>
                <div class="dynamic-row-delete btn2 btn-error btn-outline btn-sm" title="{{ __('crud.remove') }}">
                    <x-icon class="trash" />
                </div>
            </div>
        </div>
    </template>
@endsection

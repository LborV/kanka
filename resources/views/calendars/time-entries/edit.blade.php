<?php /** @var \App\Models\CalendarTimeEntry $entry */ ?>
@extends('layouts.' . (request()->ajax() ? 'ajax' : 'app'), [
    'title' => __('calendars/time-entries.edit.title'),
    'breadcrumbs' => [
        Breadcrumb::campaign($campaign)->entity($calendar->entity)->list(),
        Breadcrumb::show(),
        __('calendars/time-entries.edit.breadcrumb'),
    ],
    'canonical' => true,
    'centered' => true,
])

@section('content')
    <x-form method="PUT" :action="['calendars.time-entries.update', $campaign, $calendar->id, $entry->id]">

    @include('partials.forms._dialog', [
        'title' => __('calendars/time-entries.edit.title'),
        'content' => 'calendars.time-entries._form',
        'deleteID' => '#delete-time-entry-' . $entry->id,
    ])

    </x-form>

    <x-form method="DELETE" :action="['calendars.time-entries.destroy', $campaign, $calendar->id, $entry->id]" id="delete-time-entry-{{ $entry->id }}">
    </x-form>
@endsection

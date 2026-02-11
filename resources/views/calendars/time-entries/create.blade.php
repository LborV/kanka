@extends('layouts.' . (request()->ajax() ? 'ajax' : 'app'), [
    'title' => __('calendars/time-entries.create.title', ['name' => $calendar->name]),
    'breadcrumbs' => [
        Breadcrumb::campaign($campaign)->entity($calendar->entity)->list(),
        Breadcrumb::show(),
        __('calendars/time-entries.create.breadcrumb'),
    ],
    'canonical' => true,
    'centered' => true,
])

@section('content')
    <x-form :action="['calendars.time-entries.store', $campaign, $calendar->id]">

    @include('partials.forms._dialog', [
        'title' => __('calendars/time-entries.create.title', ['name' => $calendar->name]),
        'content' => 'calendars.time-entries._form',
    ])

    </x-form>
@endsection

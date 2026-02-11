@extends('layouts.' . (request()->ajax() ? 'ajax' : 'app'), [
    'title' => __('calendars/weather.generate.title'),
    'breadcrumbs' => [
        Breadcrumb::campaign($campaign)->entity($calendar->entity)->list(),
        Breadcrumb::show(),
        __('calendars/weather.generate.title'),
    ],
    'canonical' => true,
    'centered' => true,
])

@section('content')
    <x-form :action="['calendars.generate-weather.store', $campaign, $calendar->id]">

    @include('partials.forms._dialog', [
        'title' => __('calendars/weather.generate.title'),
        'content' => 'calendars.weather._generate_form',
    ])

    @if (request()->has('layout'))
        <input type="hidden" name="layout" value="{{ request()->get('layout') }}" />
    @endif

    </x-form>
@endsection

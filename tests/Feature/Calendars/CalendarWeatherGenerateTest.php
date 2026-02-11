<?php

use App\Models\Calendar;
use App\Models\CalendarWeather;
use App\Models\Campaign;

beforeEach(function () {
    $this->asUser()->withCampaign()->withMember();
});

it('can view the generate weather dialog', function () {
    $calendar = Calendar::factory()->create(['campaign_id' => 1]);

    $this->get(route('calendars.generate-weather.create', [Campaign::find(1), $calendar, 'year' => 1]))
        ->assertStatus(200)
        ->assertSee(__('calendars/weather.generate.title'));
});

it('can generate weather for a month range', function () {
    $calendar = Calendar::factory()->create(['campaign_id' => 1]);

    $this->post(route('calendars.generate-weather.store', [Campaign::find(1), $calendar]), [
        'year' => 1,
        'month_start' => 1,
        'month_end' => 1,
        'overwrite' => 0,
    ])->assertRedirect();

    // January has 31 days, 4 periods each = 124 weather entries
    $this->assertDatabaseHas('calendar_weather', [
        'calendar_id' => $calendar->id,
        'year' => 1,
        'month' => 1,
        'day' => 1,
        'hour' => 0,
        'is_generated' => true,
    ]);
});

it('can generate weather for multiple months', function () {
    $calendar = Calendar::factory()->create(['campaign_id' => 1]);

    $this->post(route('calendars.generate-weather.store', [Campaign::find(1), $calendar]), [
        'year' => 1,
        'month_start' => 1,
        'month_end' => 3,
    ])->assertRedirect();

    // Check entries exist for all three months
    expect(CalendarWeather::where('calendar_id', $calendar->id)->where('month', 1)->count())->toBeGreaterThan(0);
    expect(CalendarWeather::where('calendar_id', $calendar->id)->where('month', 2)->count())->toBeGreaterThan(0);
    expect(CalendarWeather::where('calendar_id', $calendar->id)->where('month', 3)->count())->toBeGreaterThan(0);
});

it('does not overwrite existing manual weather by default', function () {
    $calendar = Calendar::factory()->create(['campaign_id' => 1]);

    // Create a manual weather entry
    CalendarWeather::create([
        'calendar_id' => $calendar->id,
        'weather' => 'meteor',
        'temperature' => 'Hot',
        'precipitation' => 'None',
        'wind' => 'Calm',
        'effect' => null,
        'day' => 1,
        'month' => 1,
        'year' => 1,
        'hour' => 0,
        'is_generated' => false,
        'visibility_id' => 1,
    ]);

    $this->post(route('calendars.generate-weather.store', [Campaign::find(1), $calendar]), [
        'year' => 1,
        'month_start' => 1,
        'month_end' => 1,
    ])->assertRedirect();

    // Manual entry should not be overwritten
    $this->assertDatabaseHas('calendar_weather', [
        'calendar_id' => $calendar->id,
        'year' => 1,
        'month' => 1,
        'day' => 1,
        'hour' => 0,
        'weather' => 'meteor',
        'is_generated' => false,
    ]);
});

it('can overwrite existing weather when flag is set', function () {
    $calendar = Calendar::factory()->create(['campaign_id' => 1]);

    // Create a generated weather entry
    CalendarWeather::create([
        'calendar_id' => $calendar->id,
        'weather' => 'sun',
        'temperature' => '20°C',
        'precipitation' => 'None',
        'wind' => 'Calm',
        'effect' => null,
        'day' => 1,
        'month' => 1,
        'year' => 1,
        'hour' => 0,
        'is_generated' => true,
        'visibility_id' => 1,
    ]);

    $this->post(route('calendars.generate-weather.store', [Campaign::find(1), $calendar]), [
        'year' => 1,
        'month_start' => 1,
        'month_end' => 1,
        'overwrite' => 1,
    ])->assertRedirect();

    // Entry should still exist (overwritten, not duplicated)
    expect(CalendarWeather::where('calendar_id', $calendar->id)
        ->where('year', 1)->where('month', 1)->where('day', 1)->where('hour', 0)
        ->count())->toBe(1);
});

it('validates required fields when generating', function () {
    $calendar = Calendar::factory()->create(['campaign_id' => 1]);

    $this->post(route('calendars.generate-weather.store', [Campaign::find(1), $calendar]), [])
        ->assertSessionHasErrors(['year', 'month_start', 'month_end']);
});

it('validates month_end must be greater than or equal to month_start', function () {
    $calendar = Calendar::factory()->create(['campaign_id' => 1]);

    $this->post(route('calendars.generate-weather.store', [Campaign::find(1), $calendar]), [
        'year' => 1,
        'month_start' => 5,
        'month_end' => 2,
    ])->assertSessionHasErrors(['month_end']);
});

it('shows success message with count after generation', function () {
    $calendar = Calendar::factory()->create(['campaign_id' => 1]);

    $this->post(route('calendars.generate-weather.store', [Campaign::find(1), $calendar]), [
        'year' => 1,
        'month_start' => 1,
        'month_end' => 1,
    ])->assertRedirect()
        ->assertSessionHas('success');
});

it('generates 4 periods per day', function () {
    $calendar = Calendar::factory()->create(['campaign_id' => 1]);

    $this->post(route('calendars.generate-weather.store', [Campaign::find(1), $calendar]), [
        'year' => 1,
        'month_start' => 1,
        'month_end' => 1,
    ])->assertRedirect();

    // Day 1 should have 4 periods (0, 1, 2, 3)
    $periods = CalendarWeather::where('calendar_id', $calendar->id)
        ->where('year', 1)
        ->where('month', 1)
        ->where('day', 1)
        ->orderBy('hour')
        ->pluck('hour')
        ->toArray();

    expect($periods)->toBe([0, 1, 2, 3]);
});

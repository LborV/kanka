<?php

use App\Models\Calendar;
use App\Models\CalendarTimeEntry;
use App\Models\Campaign;

beforeEach(function () {
    $this->asUser()->withCampaign()->withMember();
});

it('can view the day view page', function () {
    $calendar = Calendar::factory()->create(['campaign_id' => 1]);

    $this->get(route('calendars.day', [Campaign::find(1), $calendar, 'date' => '1-1-1']))
        ->assertStatus(200);
});

it('can create a time entry via dialog', function () {
    $calendar = Calendar::factory()->create(['campaign_id' => 1]);

    $this->get(route('calendars.time-entries.create', [Campaign::find(1), $calendar, 'date' => '1-1-1', 'hour' => 5]))
        ->assertStatus(200);
});

it('can store a new time entry', function () {
    $calendar = Calendar::factory()->create(['campaign_id' => 1]);

    $this->post(route('calendars.time-entries.store', [Campaign::find(1), $calendar]), [
        'name' => 'Party rests at inn',
        'day' => 1,
        'month' => 1,
        'year' => 1,
        'start_hour' => 8,
        'start_minute' => 0,
        'duration' => 60,
    ])->assertRedirect();

    $this->assertDatabaseHas('calendar_time_entries', [
        'calendar_id' => $calendar->id,
        'name' => 'Party rests at inn',
        'start_hour' => 8,
        'duration' => 60,
    ]);
});

it('can edit a time entry', function () {
    $calendar = Calendar::factory()->create(['campaign_id' => 1]);
    $entry = CalendarTimeEntry::factory()->create(['calendar_id' => $calendar->id]);

    $this->get(route('calendars.time-entries.edit', [Campaign::find(1), $calendar, $entry]))
        ->assertStatus(200);
});

it('can update a time entry', function () {
    $calendar = Calendar::factory()->create(['campaign_id' => 1]);
    $entry = CalendarTimeEntry::factory()->create(['calendar_id' => $calendar->id]);

    $this->put(route('calendars.time-entries.update', [Campaign::find(1), $calendar, $entry]), [
        'name' => 'Updated entry',
        'day' => $entry->day,
        'month' => $entry->month,
        'year' => $entry->year,
        'start_hour' => 10,
        'start_minute' => 30,
        'duration' => 90,
    ])->assertRedirect();

    $this->assertDatabaseHas('calendar_time_entries', [
        'id' => $entry->id,
        'name' => 'Updated entry',
        'start_hour' => 10,
        'start_minute' => 30,
        'duration' => 90,
    ]);
});

it('can delete a time entry', function () {
    $calendar = Calendar::factory()->create(['campaign_id' => 1]);
    $entry = CalendarTimeEntry::factory()->create(['calendar_id' => $calendar->id]);

    $this->delete(route('calendars.time-entries.destroy', [Campaign::find(1), $calendar, $entry]))
        ->assertRedirect();

    $this->assertDatabaseMissing('calendar_time_entries', [
        'id' => $entry->id,
    ]);
});

it('validates required fields when storing', function () {
    $calendar = Calendar::factory()->create(['campaign_id' => 1]);

    $this->post(route('calendars.time-entries.store', [Campaign::find(1), $calendar]), [])
        ->assertSessionHasErrors(['name', 'day', 'month', 'year', 'start_hour', 'start_minute', 'duration']);
});

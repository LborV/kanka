<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('calendar_weather', function (Blueprint $table) {
            $table->unsignedSmallInteger('hour')->nullable()->after('day');
            $table->boolean('is_generated')->default(false)->after('name');

            $table->index(['calendar_id', 'year', 'month', 'day', 'hour'], 'calendar_weather_date_hour_index');
        });
    }

    public function down(): void
    {
        Schema::table('calendar_weather', function (Blueprint $table) {
            $table->dropIndex('calendar_weather_date_hour_index');
            $table->dropColumn(['hour', 'is_generated']);
        });
    }
};

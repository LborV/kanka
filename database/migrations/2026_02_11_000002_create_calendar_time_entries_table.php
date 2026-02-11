<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calendar_time_entries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('calendar_id');
            $table->unsignedInteger('entity_id')->nullable();
            $table->unsignedSmallInteger('day');
            $table->unsignedSmallInteger('month');
            $table->integer('year');
            $table->unsignedSmallInteger('start_hour');
            $table->unsignedSmallInteger('start_minute')->default(0);
            $table->unsignedInteger('duration');
            $table->string('name', 191);
            $table->text('comment')->nullable();
            $table->string('colour', 20)->nullable();
            $table->unsignedBigInteger('visibility_id')->default(1);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index(['calendar_id', 'year', 'month', 'day']);

            $table->foreign('calendar_id')->references('id')->on('calendars')->onDelete('cascade');
            $table->foreign('entity_id')->references('id')->on('entities')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('visibility_id')->references('id')->on('visibilities')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_time_entries');
    }
};

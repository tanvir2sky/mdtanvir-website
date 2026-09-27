<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('availability_rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('weekday'); // 0 = Sunday … 6 = Saturday
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
        });

        Schema::create('blocked_dates', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->string('reason')->nullable();
            $table->timestamps();
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name', 120);
            $table->string('email', 190);
            $table->string('topic', 1000);
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->string('visitor_timezone', 64);
            $table->string('locale', 5)->default('en');
            $table->string('status', 20)->default('pending');
            $table->string('decline_reason', 500)->nullable();
            $table->string('cancel_token', 64)->unique();
            $table->string('visitor_hash', 64)->index();
            $table->timestamps();
            $table->index(['starts_at', 'status']);
        });

        Schema::table('site_settings', function (Blueprint $table) {
            $table->boolean('booking_enabled')->default(false);
            $table->string('booking_timezone', 64)->nullable();
            $table->string('booking_meeting_url')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['booking_enabled', 'booking_timezone', 'booking_meeting_url']);
        });
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('blocked_dates');
        Schema::dropIfExists('availability_rules');
    }
};

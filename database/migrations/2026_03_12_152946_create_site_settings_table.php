<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('enable_gtm')->default(false);
            $table->string('gtm_id')->nullable();
            $table->boolean('enable_ga')->default(false);
            $table->string('ga_measurement_id')->nullable();
            $table->boolean('enable_clarity')->default(false);
            $table->string('clarity_project_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};

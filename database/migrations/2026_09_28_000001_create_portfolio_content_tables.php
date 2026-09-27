<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Translatable columns hold JSON keyed by locale, e.g. {"en": "...", "de": "..."}.

        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->string('company');
            $table->string('url')->nullable();
            $table->json('role');
            $table->string('period', 60);
            $table->boolean('is_current')->default(false);
            $table->json('focus')->nullable();
            $table->json('highlights')->nullable();
            $table->json('tags')->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('skill_groups', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->string('icon', 60)->default('fas fa-code');
            $table->json('items');
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->json('title');
            $table->json('category')->nullable();
            $table->json('summary');
            $table->string('icon', 60)->default('fas fa-cube');
            $table->string('accent', 20)->default('sky');
            $table->json('tags')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_visible')->default(true);
            $table->unsignedInteger('sort_order')->default(0)->index();

            // Case study
            $table->json('role')->nullable();
            $table->string('year', 20)->nullable();
            $table->string('duration', 60)->nullable();
            $table->string('live_url')->nullable();
            $table->string('repo_url')->nullable();
            $table->json('challenge')->nullable();
            $table->json('approach')->nullable();
            $table->json('outcome')->nullable();
            $table->json('body')->nullable();
            $table->string('cover_image')->nullable();
            $table->boolean('case_study_published')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
        Schema::dropIfExists('skill_groups');
        Schema::dropIfExists('experiences');
    }
};

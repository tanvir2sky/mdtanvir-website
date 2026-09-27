<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('category', 60)->nullable()->after('slug')->index();
            $table->json('tags')->nullable()->after('category');
            $table->boolean('is_featured')->default(false)->after('is_published');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex(['category']);
            $table->dropColumn(['category', 'tags', 'is_featured']);
        });
    }
};

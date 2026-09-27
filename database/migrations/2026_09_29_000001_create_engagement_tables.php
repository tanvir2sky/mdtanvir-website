<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_checks', function (Blueprint $table) {
            $table->id();
            $table->string('host');
            $table->boolean('is_shopify')->default(false);
            $table->unsignedTinyInteger('score')->nullable();
            $table->string('visitor_hash', 64)->index();
            $table->string('locale', 5)->default('en');
            $table->timestamp('created_at')->useCurrent()->index();
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->unsignedInteger('views_count')->default(0)->index()->after('newsletter_sent_at');
        });

        Schema::create('post_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->string('type', 10);
            $table->string('visitor_hash', 64);
            $table->timestamps();
            $table->unique(['post_id', 'type', 'visitor_hash']);
        });

        Schema::create('guestbook_entries', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60);
            $table->string('message', 500);
            $table->string('website')->nullable();
            $table->string('locale', 5)->default('en');
            $table->timestamp('approved_at')->nullable()->index();
            $table->string('visitor_hash', 64)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guestbook_entries');
        Schema::dropIfExists('post_reactions');
        Schema::table('posts', fn (Blueprint $table) => $table->dropColumn('views_count'));
        Schema::dropIfExists('store_checks');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email', 190)->unique();
            $table->string('locale', 5)->default('en');
            $table->string('token', 64)->unique();
            $table->timestamp('confirmed_at')->nullable()->index();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->string('consent_ip', 45)->nullable();
            $table->string('source', 60)->nullable();
            $table->timestamps();
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->boolean('notify_subscribers')->default(true)->after('is_featured');
            $table->timestamp('newsletter_sent_at')->nullable()->after('notify_subscribers');
        });

        // Posts that already exist were published before the newsletter; never email them.
        DB::table('posts')->update(['notify_subscribers' => false]);
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['notify_subscribers', 'newsletter_sent_at']);
        });

        Schema::dropIfExists('subscribers');
    }
};

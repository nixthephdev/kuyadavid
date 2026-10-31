<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Simple key/value store for everything the family edits once:
        // fundraising goal, donation details, headline, story text, etc.
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key', 100)->primary();
            $table->longText('value')->nullable();
            $table->timestamps();
        });

        Schema::create('needs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('amount', 12, 2)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Journal entries, shown on the site as "Updates".
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('body');
            $table->json('photos')->nullable();
            $table->timestamp('published_at')->index();
            $table->timestamps();
        });

        // Transparency: what donations were used for.
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->date('spent_on')->index();
            $table->string('item');
            $table->decimal('amount', 12, 2);
            $table->string('receipt')->nullable();
            $table->string('note')->nullable();
            $table->timestamps();
        });

        Schema::create('prayers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60)->nullable();
            $table->string('country', 60);
            $table->text('message');
            $table->string('ip_hash', 64)->nullable();
            $table->timestamp('approved_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email');
            $table->string('phone', 40)->nullable();
            $table->string('topic', 30);
            $table->text('message');
            $table->string('ip_hash', 64)->nullable();
            $table->timestamp('read_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('timeline_events', function (Blueprint $table) {
            $table->id();
            $table->string('when_label', 60);
            $table->string('title');
            $table->text('body')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timeline_events');
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('prayers');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('needs');
        Schema::dropIfExists('settings');
    }
};

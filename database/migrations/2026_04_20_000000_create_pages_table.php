<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('slug');
            $table->index('is_active');
        });

        Schema::create('page_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('pages')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->integer('order')->default(0);
            $table->string('type')->default('content'); // hero, content, features, stats, gallery
            $table->json('data')->nullable(); // additional data
            $table->timestamps();

            $table->index(['page_id', 'order']);
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('subject')->nullable();
            $table->longText('message');
            $table->string('ip_address')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->index('email');
            $table->index('is_read');
        });
    }

    public function down()
    {
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('page_sections');
        Schema::dropIfExists('pages');
    }
};

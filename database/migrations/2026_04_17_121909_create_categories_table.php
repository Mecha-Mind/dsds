<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id('category_id');
            $table->boolean('category_delete')->default(0);
            $table->string('category_name');
            $table->string('category_symbol')->nullable();
            $table->text('category_description')->nullable();
            $table->boolean('category_displaystatus')->default(1);
            $table->boolean('category_appearonhomepage')->default(1);
            $table->string('category_image')->nullable();
            $table->string('user_name')->nullable();
            $table->string('user_branch')->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};

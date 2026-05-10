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
        Schema::create('subcategories', function (Blueprint $table) {
            $table->id('subcategory_id');
            $table->boolean('subcategory_delete')->default(0);
            $table->string('subcategory_name');
            $table->text('subcategory_description')->nullable();
            $table->boolean('subcategory_displaystatus')->default(1);
            
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')
                ->references('category_id')
                ->on('categories')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subcategories');
    }
};

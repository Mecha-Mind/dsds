<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('category_slug')->unique()->nullable()->after('category_name');
        });

        Schema::table('subcategories', function (Blueprint $table) {
            $table->string('subcategory_slug')->unique()->nullable()->after('subcategory_name');
        });
    }

    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('category_slug');
        });

        Schema::table('subcategories', function (Blueprint $table) {
            $table->dropColumn('subcategory_slug');
        });
    }
};

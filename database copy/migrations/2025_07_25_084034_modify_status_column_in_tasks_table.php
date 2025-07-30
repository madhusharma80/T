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
    Schema::table('tasks', function (Blueprint $table) {
        $table->string('status')->nullable()->change();
    });
}


public function down()
{
    Schema::table('tasks', function (Blueprint $table) {
        // You can revert this to the original type if needed
        $table->string('status')->change();
    });
}

};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTodosTable extends Migration

{
    public function up()
    {
    Schema::create('todos', function (Blueprint $table) {
        $table->id();
        $table->string('task');
        $table->unsignedBigInteger('assigned_to')->nullable();  // Define this column only once
        $table->unsignedBigInteger('department_id')->nullable();
        $table->timestamps();
        });
    }
    public function down()
    {
    Schema::table('todos', function (Blueprint $table) {
        $table->dropForeign(['assigned_to']);
        $table->dropColumn('assigned_to'); 
        });
    }
}


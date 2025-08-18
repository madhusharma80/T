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
        $table->string('task'); // Ensure task column exists
        $table->unsignedBigInteger('assigned_to')->nullable();
        $table->unsignedBigInteger('department_id')->nullable();
        $table->timestamps();
    });
}


    public function down()
    {
        Schema::dropIfExists('todos');
    }
}

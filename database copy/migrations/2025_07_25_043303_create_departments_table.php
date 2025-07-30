<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('todos', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email');
        $table->text('description');
        $table->foreignId('assigned_to')->constrained('users');
        $table->foreignId('department_id')->constrained('departments');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('departments');
    }
}

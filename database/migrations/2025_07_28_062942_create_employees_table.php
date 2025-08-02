<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    public function up()
    {
       Schema::create('employees', function (Blueprint $table) {
    $table->id();
    $table->string('email');
    $table->unsignedBigInteger('department_id'); // This links to the Department
    $table->unsignedBigInteger('designation_id');
    $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
    $table->timestamps();
});
    }

    public function down()
    {
        Schema::dropIfExists('employees');
    }
}


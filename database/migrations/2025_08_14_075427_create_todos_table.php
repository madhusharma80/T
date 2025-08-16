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
    Schema::create('todos', function (Blueprint $table) {
        $table->id(); // Auto-increment ID
        $table->string('Task'); // Task title
        $table->unsignedBigInteger('assigned_to')->nullable(); 
        $table->unsignedBigInteger('department_id')->nullable(); // FK to Department (nullable in case unassigned)
        $table->timestamps(); // To track created_at and updated_at

        // Foreign keys
        $table->foreign('assigned_to')->references('id')->on('employees')->onDelete('set null');
        $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
    });
}

public function down()
{
    Schema::dropIfExists('todos');
}

};

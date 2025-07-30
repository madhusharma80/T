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
     */
    public function down(): void
    {
        //
    }
};

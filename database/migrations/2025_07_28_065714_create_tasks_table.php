<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
   // database/migrations/xxxx_xx_xx_create_tasks_table.php

public function up()
{
    Schema::create('tasks', function (Blueprint $table) {
        $table->id();
        $table->string('task_name');
        $table->text('description');
        $table->date('due_date');
        $table->string('status');
        $table->foreignId('user_id')->constrained()->onDelete('cascade');  // Foreign key to users table
        $table->timestamps();
    });
}
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}

<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_designations_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDesignationsTable extends Migration
{
    public function up()
    {
        Schema::create('designations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Designation Name
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('designations');
    }
}

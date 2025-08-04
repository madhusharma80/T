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
   Schema::table('employees', function (Blueprint $table) {
    $table->bigInteger('assigned_to')->unsigned()->nullable()->change();
});
}

public function down()
{
    Schema::table('employees', function (Blueprint $table) {
        $table->dropColumn('assigned_to');
    });
}
};

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
    Schema::create('personal_access_tokens', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('token');
        $table->json('abilities');
        $table->timestamp('expires_at');
        $table->morphs('tokenable'); 
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
    }
};

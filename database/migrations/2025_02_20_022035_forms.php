<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        Schema::create('forms', function (Blueprint $table) {
            $table->bigInteger('id', true, true);
            $table->string('name', 255);
            $table->string('slug', 255);
            $table->string('description', 255);
            $table->tinyInteger('limit_one_response');
            $table->foreignId('creator_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('forms');
    }
};

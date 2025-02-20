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
        Schema::create('questions', function (Blueprint $table) {
            $table->bigInteger('id', true, true);
            $table->string('name', 255);
            $table->enum('choice_type', ['short answer', 'paragraph', 'date', 'time', 'multiple choice', 'dropdown', 'checkboxes']);
            $table->string('choices', 255)->nullable(true);
            $table->tinyInteger('is_required');
            $table->foreignId('form_id')->constrained('forms')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('questions');
    }
};

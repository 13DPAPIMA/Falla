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
        Schema::create('clothing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('style_id')->references('id')->on('styles');
            $table->foreignId('photo_id')->nullable()->constrained('clothing_photos');
            $table->foreignId('type_id')->constrained('clothing_types');
            $table->foreignId('temperature_range_id')->constrained('temperature_ranges');
            $table->foreignId('material_id')->constrained('clothing_materials');
            $table->enum('gender', ['male', 'female', 'neutral']);
            $table->string('color', 255);
            $table->boolean('water_resistant');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clothing');
    }
};

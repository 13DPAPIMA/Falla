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
        Schema::create('clothing_photos', function (Blueprint $table) {
            $table->id();
            $table->string('cloudinary_public_id');
            $table->string('photo_url')->default("photo/url/photo.jpg");
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clothing_photos');
    }
};

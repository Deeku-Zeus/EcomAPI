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
        Schema::connection('mediaEcom')->create('tags', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->string('tag_name')->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mediaEcom')->dropIfExists('tags');
    }
};

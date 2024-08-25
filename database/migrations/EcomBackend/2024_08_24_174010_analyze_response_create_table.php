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
        Schema::connection('ecomBackend')->create('analyze_responses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('requestId')->foreign('requestId')->references('id')->on('analyze_requests')->onDelete('cascade');
            $table->string('coordinatees')->nullable(false);
            $table->string('object')->nullable(false);
            $table->longText('responseData')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('ecomBackend')->dropIfExists('analyze_responses');
    }
};

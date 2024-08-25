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
            Schema::connection('ecomBackend')->create('analyze_requests', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_profile_id')->foreign('user_profile_id')->references('id')->on('user_profiles')->onDelete('cascade');
                $table->string('image')->nullable(false);
                $table->string('timestamp');
                $table->string('videoName');
                $table->boolean('is_analyzed')->default(false)->nullable(false)->index('is_analyzed');
                $table->dateTime('responseTime')->nullable();
                $table->string('request_token')->unique();
                $table->timestamps();
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::connection('ecomBackend')->dropIfExists('analyze_requests');
        }
    };

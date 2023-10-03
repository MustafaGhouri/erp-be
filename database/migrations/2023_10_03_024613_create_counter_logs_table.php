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
        Schema::create('counter_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('printer');
            $table->integer('user_id');
            $table->integer('complaint_id')->nullable();
            $table->string('before_counter');
            $table->string('counter');
            $table->string('counter_file');
            $table->string('log_type');
            $table->integer('region')->nullable()->default(0);
            $table->integer('customer')->nullable()->default(0);
            $table->integer('location')->nullable()->default(0);
            $table->integer('department')->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('counter_logs');
    }
};

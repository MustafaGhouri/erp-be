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
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('complain_category');
            $table->string('problem');
            $table->string('screenshot')->nullable();
            $table->string('priority');
            $table->string('printer');
            $table->string('description')->nullable();
            $table->string('remarks')->nullable();
            $table->integer('region');
            $table->integer('customer');
            $table->integer('location');
            $table->integer('department');
            $table->integer('requester');
            $table->integer('tech')->nullable();
            $table->string('complete_date')->nullable();
            $table->enum('status', ['unAssign', 'pending', 'inProgress', 'complete']);
            $table->string('counter')->nullable();
            $table->string('counter_file')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaint');
    }
};

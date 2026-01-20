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
    Schema::create('tickets', function (Blueprint $table) {
        $table->id();
        $table->string('ticket_code')->unique();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->string('subject');
        $table->string('category');
        $table->enum('priority', ['Low', 'Medium', 'High'])->default('Low');
        $table->text('description');
        $table->enum('status', ['Open', 'In Progress', 'Resolved', 'Closed'])->default('Open');
        $table->string('attachment')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};

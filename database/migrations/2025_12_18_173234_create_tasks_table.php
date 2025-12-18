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
        Schema::create('tasks', function (Blueprint $table) {
    $table->id();

    // Pemilik task
    $table->foreignId('user_id')
        ->constrained()
        ->cascadeOnDelete();

    // Konten task
    $table->string('title');
    $table->text('description')->nullable();

    // Status Kanban
    $table->enum('status', [
        'todo',
        'in_progress',
        'done',
        'canceled',
    ])->default('todo');

    // Atribut tambahan
    $table->enum('priority', ['low', 'medium', 'high'])
        ->default('medium');

    $table->date('deadline')->nullable();

    // Cancel info (opsional)
    $table->timestamp('canceled_at')->nullable();
    $table->text('canceled_reason')->nullable();

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};

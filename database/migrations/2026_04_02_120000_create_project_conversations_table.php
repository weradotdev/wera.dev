<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_conversations', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('task_id')->nullable()->constrained()->nullOnDelete();
            $table->string('channel', 50)->default('mobile');
            $table->string('conversation_id', 36);
            $table->foreign('conversation_id')
                ->references('id')
                ->on('agent_conversations')
                ->cascadeOnDelete();
            $table->timestamps();

            $table->index(['project_id', 'user_id', 'updated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_conversations');
    }
};

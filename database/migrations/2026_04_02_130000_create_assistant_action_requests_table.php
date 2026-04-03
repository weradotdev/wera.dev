<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assistant_action_requests', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('conversation_id')->nullable()->constrained('project_conversations')->nullOnDelete();
            $table->string('channel', 50)->default('mobile');
            $table->string('action', 100);
            $table->json('parameters');
            $table->string('status', 20)->default('pending'); // pending|approved|rejected|expired
            $table->string('confirmation_code', 10)->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assistant_action_requests');
    }
};

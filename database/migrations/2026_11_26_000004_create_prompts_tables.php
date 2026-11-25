<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prompt_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('current_version_id')->nullable();
            $table->timestamps();
        });

        Schema::create('prompt_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prompt_template_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('version_number');
            $table->text('system_prompt');
            $table->text('user_prompt');
            $table->json('detected_variables')->nullable();
            $table->string('change_notes', 255)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['prompt_template_id', 'version_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prompt_versions');
        Schema::dropIfExists('prompt_templates');
    }
};

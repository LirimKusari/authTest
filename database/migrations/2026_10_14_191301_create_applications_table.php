<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('job_postings')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('cover_letter');
            $table->string('resume_url')->nullable();
            $table->string('status')->index();
            $table->text('internal_notes')->nullable();
            $table->unsignedTinyInteger('score')->nullable();
            $table->timestamps();

            $table->unique(['job_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};

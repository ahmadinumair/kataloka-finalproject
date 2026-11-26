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
        // 1. Tabel Survei Utama
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('target_audience')->nullable(); // misal: 'Mahasiswa Semua Semester'
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // 2. Pertanyaan Survei (Pilihan Ganda / Skala 1-5 / Skala Kepuasan)
        Schema::create('survey_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained('surveys')->cascadeOnDelete();
            $table->text('question');
            $table->string('type')->default('scale_1_5'); // 'scale_1_5', 'satisfaction_4', 'satisfaction_5'
            $table->json('options')->nullable(); // Opsi jawaban yang disediakan
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // 3. Respon / Partisipasi Mahasiswa dalam Survei
        Schema::create('survey_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained('surveys')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('additional_feedback')->nullable();
            $table->timestamps();

            // Mencegah mahasiswa mengisi survei yang sama lebih dari 1 kali
            $table->unique(['survey_id', 'user_id']);
        });

        // 4. Jawaban Spesifik per Pertanyaan
        Schema::create('survey_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_response_id')->constrained('survey_responses')->cascadeOnDelete();
            $table->foreignId('survey_question_id')->constrained('survey_questions')->cascadeOnDelete();
            $table->string('answer');
            $table->unsignedTinyInteger('score')->nullable(); // 1 - 5 untuk kalkulasi rata-rata statistik
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_answers');
        Schema::dropIfExists('survey_responses');
        Schema::dropIfExists('survey_questions');
        Schema::dropIfExists('surveys');
    }
};

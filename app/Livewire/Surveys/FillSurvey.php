<?php

namespace App\Livewire\Surveys;

use App\Models\Survey;
use App\Models\SurveyAnswer;
use App\Models\SurveyResponse;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Form Kuesioner & Survei Mahasiswa - Kataloka')]
class FillSurvey extends Component
{
    public Survey $survey;
    public array $answers = []; // question_id => answer
    public string $additionalFeedback = '';
    public bool $hasSubmitted = false;

    public function mount(Survey $survey)
    {
        $this->survey = $survey->load('questions');

        if (Auth::check()) {
            $this->hasSubmitted = $this->survey->isCompletedBy(Auth::user());
        }

        // Inisialisasi jawaban default kosong untuk tiap pertanyaan
        foreach ($this->survey->questions as $question) {
            $this->answers[$question->id] = '';
        }
    }

    public function submitSurvey()
    {
        if (!Auth::check()) {
            session()->flash('error', 'Silakan masuk terlebih dahulu untuk mengisi kuesioner.');
            return $this->redirect(route('login'), navigate: true);
        }

        if (!$this->survey->is_active) {
            session()->flash('error', 'Maaf, survei ini telah ditutup.');
            return;
        }

        $userId = Auth::id();

        // Cek jika sudah pernah mengisi
        if ($this->survey->isCompletedBy(Auth::user())) {
            session()->flash('info', 'Anda sudah pernah mengisi survei ini sebelumnya.');
            $this->hasSubmitted = true;
            return;
        }

        // Validasi: semua pertanyaan wajib diisi
        $rules = [];
        $messages = [];
        foreach ($this->survey->questions as $question) {
            $rules["answers.{$question->id}"] = 'required';
            $messages["answers.{$question->id}.required"] = 'Pertanyaan "' . $question->question . '" wajib dijawab.';
        }

        $this->validate($rules, $messages);

        // Simpan Respon Utama
        $response = SurveyResponse::create([
            'survey_id' => $this->survey->id,
            'user_id' => $userId,
            'additional_feedback' => trim($this->additionalFeedback),
        ]);

        // Simpan Jawaban per Pertanyaan
        foreach ($this->answers as $questionId => $answerVal) {
            $question = $this->survey->questions->firstWhere('id', $questionId);
            $score = null;

            // Menghitung skor numerik untuk analisis kepuasan
            if (is_numeric($answerVal)) {
                $score = (int) $answerVal;
            } else {
                $score = match ($answerVal) {
                    'Sangat Puas' => 5,
                    'Puas' => 4,
                    'Cukup Puas' => 3,
                    'Kurang Puas' => 2,
                    'Sangat Tidak Puas' => 1,
                    default => null,
                };
            }

            SurveyAnswer::create([
                'survey_response_id' => $response->id,
                'survey_question_id' => $questionId,
                'answer' => (string) $answerVal,
                'score' => $score,
            ]);
        }

        $this->hasSubmitted = true;
        session()->flash('success', 'Terima kasih banyak! Tanggapan kuesioner Anda berhasil disimpan.');
    }

    public function render()
    {
        $isAdmin = Auth::check() && Auth::user()->role === 'admin';

        return view('livewire.surveys.fill-survey', [
            'survey' => $this->survey,
            'totalResponses' => $this->survey->responses()->count(),
            'averageScore' => $isAdmin ? $this->survey->average_score : null,
            'isAdmin' => $isAdmin,
        ]);
    }
}

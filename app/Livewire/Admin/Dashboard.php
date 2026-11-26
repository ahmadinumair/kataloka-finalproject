<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Feedback;
use App\Models\FeedbackType;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard Analitik & Statistik - Kataloka')]
class Dashboard extends Component
{
    // State Form Buat Survei Baru
    public bool $showSurveyForm = false;
    public string $surveyTitle = '';
    public string $surveyDescription = '';
    public string $surveyTargetAudience = 'Seluruh Mahasiswa Aktif';
    public array $surveyQuestions = [];

    public function mount()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Akses terbatas hanya untuk administrator.');
        }

        $this->initDefaultSurveyForm();
    }

    public function initDefaultSurveyForm()
    {
        $this->surveyTitle = '';
        $this->surveyDescription = '';
        $this->surveyTargetAudience = 'Seluruh Mahasiswa Aktif';
        $this->surveyQuestions = [
            [
                'question' => 'Bagaimana mutu pengajaran dan kejelasan materi dosen di kelas?',
                'type' => 'scale_1_5',
            ],
            [
                'question' => 'Seberapa puas Anda terhadap pengalaman belajar selama 3 bulan ini?',
                'type' => 'satisfaction_4',
            ],
        ];
    }

    public function openCreateSurveyForm()
    {
        $this->initDefaultSurveyForm();
        $this->showSurveyForm = true;
    }

    public function closeCreateSurveyForm()
    {
        $this->showSurveyForm = false;
    }

    public function addQuestion()
    {
        $this->surveyQuestions[] = [
            'question' => '',
            'type' => 'scale_1_5',
        ];
    }

    public function removeQuestion(int $index)
    {
        if (count($this->surveyQuestions) > 1) {
            unset($this->surveyQuestions[$index]);
            $this->surveyQuestions = array_values($this->surveyQuestions);
        }
    }

    /**
     * Admin Simpan Survei Baru
     */
    public function saveSurvey()
    {
        $this->validate([
            'surveyTitle' => 'required|min:5|max:200',
            'surveyDescription' => 'nullable|max:1000',
            'surveyQuestions' => 'required|array|min:1',
            'surveyQuestions.*.question' => 'required|min:5|max:255',
            'surveyQuestions.*.type' => 'required|in:scale_1_5,satisfaction_4,satisfaction_5',
        ], [
            'surveyTitle.required' => 'Judul survei wajib diisi.',
            'surveyTitle.min' => 'Judul survei minimal 5 karakter.',
            'surveyQuestions.*.question.required' => 'Kalimat pertanyaan kuesioner wajib diisi.',
            'surveyQuestions.*.question.min' => 'Pertanyaan minimal 5 karakter.',
        ]);

        $survey = Survey::create([
            'title' => trim($this->surveyTitle),
            'description' => trim($this->surveyDescription),
            'target_audience' => trim($this->surveyTargetAudience),
            'is_active' => true,
            'created_by' => Auth::id(),
        ]);

        foreach ($this->surveyQuestions as $index => $qData) {
            SurveyQuestion::create([
                'survey_id' => $survey->id,
                'question' => trim($qData['question']),
                'type' => $qData['type'],
                'order' => $index + 1,
            ]);
        }

        $this->showSurveyForm = false;
        $this->initDefaultSurveyForm();
        session()->flash('success', 'Form survei kepuasan mahasiswa baru berhasil diterbitkan ke dashboard!');
    }

    /**
     * Admin Hapus Survei
     */
    public function deleteSurvey(int $surveyId)
    {
        $survey = Survey::find($surveyId);
        if ($survey) {
            $survey->delete();
            session()->flash('success', 'Form survei berhasil dihapus dari sistem.');
        }
    }

    /**
     * Admin Toggle Status Aktif Survei
     */
    public function toggleSurveyStatus(int $surveyId)
    {
        $survey = Survey::find($surveyId);
        if ($survey) {
            $survey->update(['is_active' => !$survey->is_active]);
            $statusLabel = $survey->is_active ? 'diaktifkan kembali' : 'ditutup';
            session()->flash('success', "Status survei berhasil diubah menjadi: {$statusLabel}.");
        }
    }

    public function render()
    {
        // 1. Counter Metrics
        $totalFeedbacks = Feedback::count();
        $pendingFeedbacks = Feedback::where('status', 'pending')->count();
        $inProgressFeedbacks = Feedback::where('status', 'in_progress')->count();
        $resolvedFeedbacks = Feedback::where('status', 'resolved')->count();
        $urgentFeedbacks = Feedback::whereIn('urgency', ['urgent', 'high'])->where('status', '!=', 'resolved')->count();
        $totalUsers = User::where('role', 'student')->count();

        // 2. Data Statistik Kategori untuk Chart.js
        $categories = Category::withCount('feedbacks')->get();
        $categoryLabels = $categories->pluck('name')->toArray();
        $categoryCounts = $categories->pluck('feedbacks_count')->toArray();

        // 3. Data Statistik Status untuk Chart.js
        $statusLabels = ['Pending', 'Sedang Diproses', 'Selesai', 'Ditolak'];
        $statusCounts = [
            Feedback::where('status', 'pending')->count(),
            Feedback::where('status', 'in_progress')->count(),
            Feedback::where('status', 'resolved')->count(),
            Feedback::where('status', 'rejected')->count(),
        ];

        // 4. Data Tipe Masukan
        $types = FeedbackType::withCount('feedbacks')->get();
        $typeLabels = $types->pluck('name')->toArray();
        $typeCounts = $types->pluck('feedbacks_count')->toArray();

        // 5. Aspirasi Mendesak Terbaru
        $recentUrgent = Feedback::with(['category', 'user'])
            ->whereIn('urgency', ['urgent', 'high'])
            ->where('status', '!=', 'resolved')
            ->latest()
            ->take(5)
            ->get();

        // 6. Data Survei Mahasiswa untuk Dashboard
        $surveys = Survey::with(['questions.answers', 'responses'])
            ->latest()
            ->get();

        return view('livewire.admin.dashboard', [
            'totalFeedbacks' => $totalFeedbacks,
            'pendingFeedbacks' => $pendingFeedbacks,
            'inProgressFeedbacks' => $inProgressFeedbacks,
            'resolvedFeedbacks' => $resolvedFeedbacks,
            'urgentFeedbacks' => $urgentFeedbacks,
            'totalUsers' => $totalUsers,
            'categoryLabels' => json_encode($categoryLabels),
            'categoryCounts' => json_encode($categoryCounts),
            'statusLabels' => json_encode($statusLabels),
            'statusCounts' => json_encode($statusCounts),
            'typeLabels' => json_encode($typeLabels),
            'typeCounts' => json_encode($typeCounts),
            'recentUrgent' => $recentUrgent,
            'surveys' => $surveys,
        ]);
    }
}

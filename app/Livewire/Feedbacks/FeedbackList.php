<?php

namespace App\Livewire\Feedbacks;

use App\Models\Category;
use App\Models\Feedback;
use App\Models\FeedbackType;
use App\Models\FeedbackUpvote;
use App\Models\Survey;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Papan Aspirasi & Feedback Kampus - Kataloka')]
class FeedbackList extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $category = '';

    #[Url(history: true)]
    public string $type = '';

    #[Url(history: true)]
    public string $status = '';

    #[Url(history: true)]
    public string $tab = 'all'; // 'all' or 'my'

    #[Url(history: true)]
    public string $sort = 'latest'; // 'latest', 'popular', 'urgent'

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategory()
    {
        $this->resetPage();
    }

    public function updatingType()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingTab()
    {
        $this->resetPage();
    }

    public function updatingSort()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'category', 'type', 'status', 'tab', 'sort']);
        $this->resetPage();
    }

    /**
     * Fitur Upvoting: Dukung aspirasi agar mendapatkan perhatian lebih cepat dari pihak kampus
     */
    public function toggleUpvote(int $feedbackId)
    {
        if (!Auth::check()) {
            session()->flash('error', 'Silakan masuk terlebih dahulu untuk memberikan dukungan (upvote).');
            return $this->redirect(route('login'), navigate: true);
        }

        $userId = Auth::id();
        $upvote = FeedbackUpvote::where('feedback_id', $feedbackId)
            ->where('user_id', $userId)
            ->first();

        if ($upvote) {
            $upvote->delete();
        } else {
            FeedbackUpvote::create([
                'feedback_id' => $feedbackId,
                'user_id' => $userId,
            ]);
        }
    }

    /**
     * Hapus / Batalkan Aspirasi (Bisa dilakukan oleh pemilik aspirasi atau admin)
     */
    public function deleteFeedback(int $feedbackId)
    {
        if (!Auth::check()) {
            abort(403);
        }

        $feedback = Feedback::findOrFail($feedbackId);
        $user = Auth::user();
        $isAuthor = $user->id === $feedback->user_id;
        $isAdmin = $user->role === 'admin';

        if (!$isAuthor && !$isAdmin) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus aspirasi ini.');
        }

        $feedback->delete();

        Cache::forget('total_feedbacks_count');
        Cache::forget('resolved_feedbacks_count');
        Cache::forget('pending_feedbacks_count');

        session()->flash('success', $isAuthor
            ? 'Aspirasi Anda berhasil dibatalkan dan dihapus.'
            : 'Aspirasi berhasil dihapus oleh admin.');
    }

    public function render()
    {
        $userId = Auth::id();

        // Optimized Query: Hanya memuat relasi penting dan hitungan dalam 1 kali proses
        $query = Feedback::with([
            'user:id,name',
            'category:id,name',
            'feedbackType:id,name',
            'upvotes' => function ($q) use ($userId) {
                if ($userId) {
                    $q->where('user_id', $userId)->select('id', 'feedback_id', 'user_id');
                } else {
                    $q->whereRaw('1 = 0');
                }
            }
        ])->withCount(['upvotes', 'responses']);

        // Tab Filter: Semua vs Aspirasi Saya
        if ($this->tab === 'my') {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $this->tab = 'all';
            }
        }

        // Live Search
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('ai_summary', 'like', '%' . $this->search . '%');
            });
        }

        // Filter Category
        if (!empty($this->category)) {
            $query->where('category_id', $this->category);
        }

        // Filter Type
        if (!empty($this->type)) {
            $query->where('type_id', $this->type);
        }

        // Filter Status
        if (!empty($this->status)) {
            $query->where('status', $this->status);
        }

        // Sorting
        if ($this->sort === 'popular') {
            $query->orderByDesc('upvotes_count');
        } elseif ($this->sort === 'urgent') {
            $query->orderByRaw("FIELD(urgency, 'urgent', 'high', 'normal', 'low')");
        } else {
            $query->latest();
        }

        $feedbacks = $query->paginate(9);

        // Ambil data kategori & tipe langsung dari Eloquent (hindari serialisasi cache yang menghasilkan __PHP_Incomplete_Class)
        $categories = Category::all();
        $feedbackTypes = FeedbackType::all();
        $totalCount = Feedback::count();
        $resolvedCount = Feedback::where('status', 'resolved')->count();
        $pendingCount = Feedback::where('status', 'pending')->count();

        $activeSurvey = Survey::with(['questions', 'responses' => function ($q) use ($userId) {
            if ($userId) {
                $q->where('user_id', $userId);
            } else {
                $q->whereRaw('1 = 0');
            }
        }])->where('is_active', true)->latest()->first();

        return view('livewire.feedbacks.feedback-list', [
            'feedbacks' => $feedbacks,
            'categories' => $categories,
            'feedbackTypes' => $feedbackTypes,
            'totalCount' => $totalCount,
            'resolvedCount' => $resolvedCount,
            'pendingCount' => $pendingCount,
            'activeSurvey' => $activeSurvey,
        ]);
    }
}

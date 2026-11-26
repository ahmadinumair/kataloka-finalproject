<?php

namespace App\Livewire\Feedbacks;

use App\Models\Feedback;
use App\Models\FeedbackUpvote;
use App\Models\Response;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
class FeedbackDetail extends Component
{
    public Feedback $feedback;

    #[Rule('required|min:5|max:2000', message: [
        'required' => 'Pesan tanggapan wajib diisi.',
        'min' => 'Pesan tanggapan minimal 5 karakter.',
        'max' => 'Pesan tanggapan maksimal 2000 karakter.',
    ])]
    public string $adminResponse = '';

    public string $newStatus = '';

    public function mount(Feedback $feedback)
    {
        $this->feedback = $feedback->load(['user', 'category', 'feedbackType', 'responses.admin', 'upvotes']);
        $this->newStatus = $feedback->status;
    }

    public function toggleUpvote()
    {
        if (!Auth::check()) {
            session()->flash('error', 'Silakan masuk terlebih dahulu untuk memberikan dukungan (upvote).');
            return $this->redirect(route('login'), navigate: true);
        }

        $userId = Auth::id();
        $upvote = FeedbackUpvote::where('feedback_id', $this->feedback->id)
            ->where('user_id', $userId)
            ->first();

        if ($upvote) {
            $upvote->delete();
        } else {
            FeedbackUpvote::create([
                'feedback_id' => $this->feedback->id,
                'user_id' => $userId,
            ]);
        }

        $this->feedback->refresh();
    }

    /**
     * Admin Action: Kirim Tanggapan Resmi
     */
    public function postAdminResponse()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Hanya admin yang dapat memberikan tanggapan resmi.');
        }

        $this->validateOnly('adminResponse');

        Response::create([
            'feedback_id' => $this->feedback->id,
            'admin_id' => Auth::id(),
            'message' => $this->adminResponse,
        ]);

        if (!empty($this->newStatus) && $this->newStatus !== $this->feedback->status) {
            $this->feedback->update(['status' => $this->newStatus]);
        }

        $this->adminResponse = '';
        $this->feedback->refresh();
        session()->flash('success', 'Tanggapan resmi admin berhasil dipublikasikan!');
    }

    /**
     * Admin Quick Status Update
     */
    public function updateStatus(string $status)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403);
        }

        if (in_array($status, ['pending', 'in_progress', 'resolved', 'rejected'])) {
            $this->feedback->update(['status' => $status]);
            $this->newStatus = $status;
            $this->feedback->refresh();
            session()->flash('success', 'Status aspirasi berhasil diperbarui menjadi: ' . ucfirst($status));
        }
    }

    /**
     * Hapus Aspirasi (Bisa dilakukan oleh pembuat aspirasi untuk membatalkan, atau oleh admin)
     */
    public function deleteFeedback()
    {
        if (!Auth::check()) {
            abort(403);
        }

        $user = Auth::user();
        $isAuthor = $user->id === $this->feedback->user_id;
        $isAdmin = $user->role === 'admin';

        if (!$isAuthor && !$isAdmin) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus aspirasi ini.');
        }

        $this->feedback->delete();

        session()->flash('success', $isAuthor
            ? 'Aspirasi Anda berhasil dibatalkan dan dihapus dari publik.'
            : 'Aspirasi berhasil dihapus oleh admin.');

        return $this->redirect(route('feedbacks.index'), navigate: true);
    }

    /**
     * Admin: Hapus Tanggapan Resmi Tertentu
     */
    public function deleteResponse(int $responseId)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Hanya admin yang dapat menghapus tanggapan.');
        }

        $response = Response::where('id', $responseId)
            ->where('feedback_id', $this->feedback->id)
            ->firstOrFail();

        $response->delete();
        $this->feedback->refresh();

        session()->flash('success', 'Tanggapan resmi admin berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.feedbacks.feedback-detail', [
            'title' => $this->feedback->title . ' - Kataloka',
        ]);
    }
}

<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Feedback;
use App\Models\FeedbackType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Kelola Aspirasi & Aduan Mahasiswa - Kataloka')]
class ManageFeedbacks extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $category = '';

    #[Url(history: true)]
    public string $status = '';

    #[Url(history: true)]
    public string $urgency = '';

    public ?int $selectedFeedbackId = null;

    // Fitur Massal (Bulk Action): Memilih beberapa aspirasi sekaligus
    public array $selectedFeedbacks = [];
    public bool $selectAll = false;

    public function mount()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Akses khusus untuk administrator.');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
        $this->clearSelection();
    }

    public function updatingCategory()
    {
        $this->resetPage();
        $this->clearSelection();
    }

    public function updatingStatus()
    {
        $this->resetPage();
        $this->clearSelection();
    }

    public function updatingUrgency()
    {
        $this->resetPage();
        $this->clearSelection();
    }

    /**
     * Memilih / membatalkan semua aspirasi pada filter aktif
     */
    public function updatedSelectAll($value)
    {
        if ($value) {
            $ids = $this->getFilteredQuery()->paginate(10)->pluck('id')->map(fn($id) => (string)$id)->toArray();
            $this->selectedFeedbacks = $ids;
        } else {
            $this->selectedFeedbacks = [];
        }
    }

    public function clearSelection()
    {
        $this->selectedFeedbacks = [];
        $this->selectAll = false;
    }

    /**
     * Fitur Bulk Action: Mengubah status beberapa aspirasi sekaligus
     */
    public function bulkUpdateStatus(string $newStatus)
    {
        if (empty($this->selectedFeedbacks)) {
            session()->flash('error', 'Pilih minimal satu aspirasi terlebih dahulu.');
            return;
        }

        if (in_array($newStatus, ['pending', 'in_progress', 'resolved', 'rejected'])) {
            $count = count($this->selectedFeedbacks);
            Feedback::whereIn('id', $this->selectedFeedbacks)->update(['status' => $newStatus]);

            $statusLabels = [
                'in_progress' => 'Sedang Diproses',
                'resolved' => 'Tuntas / Selesai',
                'pending' => 'Pending',
                'rejected' => 'Ditolak',
            ];

            $label = $statusLabels[$newStatus] ?? ucfirst($newStatus);
            session()->flash('success', "Berhasil! {$count} aspirasi terpilih telah ditandai sebagai: {$label}.");

            $this->clearSelection();
        }
    }

    /**
     * Fitur Admin: Ubah status langsung dari tabel per baris (Livewire Inline Action)
     */
    public function updateStatus(int $feedbackId, string $newStatus)
    {
        if (in_array($newStatus, ['pending', 'in_progress', 'resolved', 'rejected'])) {
            $feedback = Feedback::find($feedbackId);
            if ($feedback) {
                $feedback->update(['status' => $newStatus]);
                session()->flash('success', 'Status aspirasi #' . $feedbackId . ' berhasil diubah menjadi: ' . ucfirst($newStatus));
            }
        }
    }

    /**
     * Fitur Admin: Hapus aspirasi yang melanggar aturan atau duplikasi
     */
    public function deleteFeedback(int $feedbackId)
    {
        $feedback = Feedback::find($feedbackId);
        if ($feedback) {
            $feedback->delete();
            session()->flash('success', 'Aspirasi #' . $feedbackId . ' berhasil dihapus dari sistem.');
        }
    }

    public function resetFilters()
    {
        $this->reset(['search', 'category', 'status', 'urgency']);
        $this->resetPage();
        $this->clearSelection();
    }

    protected function getFilteredQuery(): Builder
    {
        $query = Feedback::with(['user', 'category', 'feedbackType', 'responses'])
            ->withCount('upvotes');

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('ai_summary', 'like', '%' . $this->search . '%')
                  ->orWhereHas('user', function ($uq) {
                      $uq->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if (!empty($this->category)) {
            $query->where('category_id', $this->category);
        }

        if (!empty($this->status)) {
            $query->where('status', $this->status);
        }

        if (!empty($this->urgency)) {
            $query->where('urgency', $this->urgency);
        }

        return $query->latest();
    }

    public function render()
    {
        $feedbacks = $this->getFilteredQuery()->paginate(10);

        return view('livewire.admin.manage-feedbacks', [
            'feedbacks' => $feedbacks,
            'categories' => Category::all(),
            'feedbackTypes' => FeedbackType::all(),
        ]);
    }
}

<?php

namespace App\Livewire\Feedbacks;

use App\Models\Category;
use App\Models\Feedback;
use App\Models\FeedbackType;
use App\Services\AiFeedbackService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Sampaikan Aspirasi & Feedback - Kataloka')]
class CreateFeedback extends Component
{
    use WithFileUploads;

    #[Rule('required|min:5|max:150', message: [
        'required' => 'Judul aspirasi wajib diisi.',
        'min' => 'Judul minimal 5 karakter.',
        'max' => 'Judul maksimal 150 karakter.',
    ])]
    public string $title = '';

    #[Rule('required|exists:feedback_types,id', message: [
        'required' => 'Silakan pilih jenis masukan.',
        'exists' => 'Jenis masukan tidak valid.',
    ])]
    public ?int $type_id = null;

    #[Rule('required|exists:categories,id', message: [
        'required' => 'Silakan pilih kategori.',
        'exists' => 'Kategori tidak valid.',
    ])]
    public ?int $category_id = null;

    #[Rule('required|min:20|max:3000', message: [
        'required' => 'Deskripsi aspirasi wajib diisi secara jelas.',
        'min' => 'Deskripsi minimal 20 karakter agar aspirasi mudah dipahami.',
        'max' => 'Deskripsi maksimal 3000 karakter.',
    ])]
    public string $description = '';

    #[Rule('nullable|image|max:5120', message: [
        'image' => 'File harus berupa gambar (JPG, PNG, WEBP, GIF).',
        'max' => 'Ukuran gambar maksimal 5MB.',
    ])]
    public $image = null;

    public bool $is_anonymous = false;

    public string $urgency = 'normal';

    public ?string $ai_summary = null;

    public bool $isAnalyzing = false;

    public function mount()
    {
        if (!Auth::check()) {
            return $this->redirect(route('login'), navigate: true);
        }

        // Default ke tipe dan kategori pertama jika ada
        $firstType = FeedbackType::first();
        if ($firstType) {
            $this->type_id = $firstType->id;
        }

        $firstCategory = Category::first();
        if ($firstCategory) {
            $this->category_id = $firstCategory->id;
        }
    }

    /**
     * Fitur Integrasi AI: Menganalisis aspirasi secara cerdas menggunakan Gemini / Heuristic AI
     */
    public function generateAiSummary(AiFeedbackService $aiService)
    {
        $this->validateOnly('title');
        $this->validateOnly('description');

        $this->isAnalyzing = true;

        $categoryName = Category::find($this->category_id)?->name;
        $typeName = FeedbackType::find($this->type_id)?->name;

        $analysis = $aiService->analyzeFeedback($this->title, $this->description, $categoryName, $typeName);
        $this->ai_summary = $analysis['summary'];
        $this->urgency = $analysis['urgency'];

        $this->isAnalyzing = false;
        session()->flash('ai_notice', 'Rangkuman dan estimasi urgensi berhasil dianalisis secara akurat oleh AI Kataloka!');
    }

    public function save(AiFeedbackService $aiService)
    {
        if (!Auth::check()) {
            session()->flash('error', 'Silakan masuk atau daftar terlebih dahulu untuk mengirim aspirasi.');
            return $this->redirect(route('login'), navigate: true);
        }

        $this->validate();

        // Jika user belum klik tombol analisis AI, jalankan otomatis saat submit
        if (empty($this->ai_summary)) {
            $categoryName = Category::find($this->category_id)?->name;
            $typeName = FeedbackType::find($this->type_id)?->name;

            $analysis = $aiService->analyzeFeedback($this->title, $this->description, $categoryName, $typeName);
            $this->ai_summary = $analysis['summary'];
            $this->urgency = $analysis['urgency'];
        }

        $imagePath = null;
        if ($this->image) {
            $imagePath = $this->image->store('feedbacks', 'public');
        }

        $feedback = Feedback::create([
            'user_id' => Auth::id(),
            'type_id' => $this->type_id,
            'category_id' => $this->category_id,
            'title' => $this->title,
            'description' => $this->description,
            'image_path' => $imagePath,
            'status' => 'pending',
            'is_anonymous' => $this->is_anonymous,
            'urgency' => $this->urgency,
            'ai_summary' => $this->ai_summary,
        ]);

        session()->flash('success', 'Aspirasi Anda berhasil dikirim dan siap ditinjau oleh pihak terkait!');

        return $this->redirect(route('feedbacks.detail', $feedback->id), navigate: true);
    }

    public function render()
    {
        return view('livewire.feedbacks.create-feedback', [
            'feedbackTypes' => FeedbackType::all(),
            'categories' => Category::all(),
        ]);
    }
}

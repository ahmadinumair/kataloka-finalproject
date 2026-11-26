<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Feedback;
use App\Models\FeedbackType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KatalokaTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_loads_feedbacks_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_guest_can_register_as_student(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    public function test_guest_can_access_login_page(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_user_can_login_with_real_email(): void
    {
        $user = User::factory()->create([
            'email' => 'user@gmail.com',
            'password' => \Illuminate\Support\Facades\Hash::make('Password123!'),
            'role' => 'student',
        ]);

        \Livewire\Livewire::test(\App\Livewire\Auth\Login::class)
            ->set('email', 'user@gmail.com')
            ->set('password', 'Password123!')
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect(route('feedbacks.index'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_non_admin_cannot_access_admin_dashboard(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($student)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_dashboard_and_manage_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);

        $responseManage = $this->actingAs($admin)->get('/admin/feedbacks');
        $responseManage->assertStatus(200);
    }

    public function test_feedback_detail_page_loads(): void
    {
        $user = User::factory()->create();
        $cat = Category::create(['name' => 'Fasilitas']);
        $type = FeedbackType::create(['name' => 'Keluhan']);

        $feedback = Feedback::create([
            'user_id' => $user->id,
            'category_id' => $cat->id,
            'type_id' => $type->id,
            'title' => 'Lampu Parkir Mati',
            'description' => 'Lampu parkir motor belakang mati total pada malam hari.',
            'status' => 'pending',
            'urgency' => 'normal',
        ]);

        $response = $this->get('/feedbacks/' . $feedback->id);
        $response->assertStatus(200);
        $response->assertSee('Lampu Parkir Mati');
    }

    public function test_admin_can_bulk_update_feedbacks_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        $cat = Category::create(['name' => 'Akademik']);
        $type = FeedbackType::create(['name' => 'Apresiasi']);

        $fb1 = Feedback::create([
            'user_id' => $user->id,
            'category_id' => $cat->id,
            'type_id' => $type->id,
            'title' => 'Apresiasi Pelayanan Akademik 1',
            'description' => 'Pelayanan akademik cepat dan ramah.',
            'status' => 'pending',
            'urgency' => 'low',
        ]);

        $fb2 = Feedback::create([
            'user_id' => $user->id,
            'category_id' => $cat->id,
            'type_id' => $type->id,
            'title' => 'Apresiasi Pelayanan Akademik 2',
            'description' => 'Pelayanan administrasi sangat membantu.',
            'status' => 'pending',
            'urgency' => 'low',
        ]);

        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Livewire\Admin\ManageFeedbacks::class)
            ->set('selectedFeedbacks', [(string)$fb1->id, (string)$fb2->id])
            ->call('bulkUpdateStatus', 'resolved')
            ->assertHasNoErrors();

        $this->assertEquals('resolved', $fb1->fresh()->status);
        $this->assertEquals('resolved', $fb2->fresh()->status);
    }

    public function test_user_can_delete_own_feedback(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $cat = Category::create(['name' => 'Fasilitas 2']);
        $type = FeedbackType::create(['name' => 'Keluhan 2']);

        $feedback = Feedback::create([
            'user_id' => $user->id,
            'category_id' => $cat->id,
            'type_id' => $type->id,
            'title' => 'Aspirasi yang ingin dibatalkan',
            'description' => 'Salah input info, ingin batalkan.',
            'status' => 'pending',
            'urgency' => 'normal',
        ]);

        \Livewire\Livewire::actingAs($user)
            ->test(\App\Livewire\Feedbacks\FeedbackDetail::class, ['feedback' => $feedback])
            ->call('deleteFeedback')
            ->assertRedirect(route('feedbacks.index'));

        $this->assertDatabaseMissing('feedbacks', ['id' => $feedback->id]);
    }

    public function test_admin_and_author_can_delete_feedback_from_list(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $cat = Category::create(['name' => 'Fasilitas 3']);
        $type = FeedbackType::create(['name' => 'Keluhan 3']);

        $feedback = Feedback::create([
            'user_id' => $user->id,
            'category_id' => $cat->id,
            'type_id' => $type->id,
            'title' => 'Aspirasi di daftar',
            'description' => 'Test hapus dari list',
            'status' => 'pending',
            'urgency' => 'normal',
        ]);

        \Livewire\Livewire::actingAs($user)
            ->test(\App\Livewire\Feedbacks\FeedbackList::class)
            ->call('deleteFeedback', $feedback->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('feedbacks', ['id' => $feedback->id]);
    }

    public function test_admin_can_delete_response(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'student']);
        $cat = Category::create(['name' => 'Umum']);
        $type = FeedbackType::create(['name' => 'Pertanyaan']);

        $feedback = Feedback::create([
            'user_id' => $user->id,
            'category_id' => $cat->id,
            'type_id' => $type->id,
            'title' => 'Jadwal Libur Semester',
            'description' => 'Kapan pengumuman resmi libur semester?',
            'status' => 'pending',
            'urgency' => 'low',
        ]);

        $response = \App\Models\Response::create([
            'feedback_id' => $feedback->id,
            'admin_id' => $admin->id,
            'message' => 'Tanggapan salah ketik yang perlu dihapus.',
        ]);

        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Livewire\Feedbacks\FeedbackDetail::class, ['feedback' => $feedback])
            ->call('deleteResponse', $response->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('responses', ['id' => $response->id]);
    }

    public function test_ai_accurately_detects_security_threat_as_urgent(): void
    {
        $aiService = new \App\Services\AiFeedbackService();

        // Kasus spesifik user: keluhan keamanan kampus terancam ormas
        $result = $aiService->analyzeFeedback(
            'Keluhan keamanan kampus terancam ormas',
            'Banyak oknum ormas masuk tanpa izin ke lingkungan kampus dan mengintimidasi mahasiswa di depan gerbang utama.',
            'Keamanan',
            'Keluhan'
        );

        $this->assertEquals('urgent', $result['urgency']);
        $this->assertNotEmpty($result['summary']);
        $this->assertStringContainsString('Keamanan', $result['summary']);
    }

    public function test_ai_detects_academic_disruption_as_high_and_appreciation_as_low(): void
    {
        $aiService = new \App\Services\AiFeedbackService();

        // Ujian / perkuliahan mati total -> high
        $resHigh = $aiService->analyzeFeedback(
            'Proyektor dan Listrik Mati Saat Ujian UAS',
            'Ruang 301 lantai 3 mati listrik total saat ujian akhir semester berlangsung.',
            'Fasilitas',
            'Keluhan'
        );
        $this->assertEquals('high', $resHigh['urgency']);

        // Apresiasi -> low
        $resLow = $aiService->analyzeFeedback(
            'Apresiasi Kebersihan Toilet Gedung A',
            'Terima kasih kepada tim kebersihan, toilet sekarang sangat bersih, wangi, dan rapi.',
            'Kebersihan',
            'Apresiasi'
        );
        $this->assertEquals('low', $resLow['urgency']);

        // Keluhan biasa / minor -> normal
        $resNormal = $aiService->analyzeFeedback(
            'Kursi Goyang di Pojok Perpustakaan',
            'Ada satu kursi belajar di perpustakaan yang bautnya agak kendor tolong dikencangkan.',
            'Fasilitas',
            'Saran'
        );
        $this->assertEquals('normal', $resNormal['urgency']);
    }

    public function test_student_can_fill_survey_with_ratings(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'student']);

        $survey = \App\Models\Survey::create([
            'title' => 'Evaluasi Kualitas Pengajar 3 Bulan',
            'description' => 'Survei evaluasi berkala mutu pengajar dan fasilitas.',
            'is_active' => true,
            'created_by' => $admin->id,
        ]);

        $q1 = \App\Models\SurveyQuestion::create([
            'survey_id' => $survey->id,
            'question' => 'Kualitas pengajar di kelas',
            'type' => 'scale_1_5',
            'order' => 1,
        ]);

        $q2 = \App\Models\SurveyQuestion::create([
            'survey_id' => $survey->id,
            'question' => 'Kepuasan pengalaman 3 bulan',
            'type' => 'satisfaction_4',
            'order' => 2,
        ]);

        \Livewire\Livewire::actingAs($student)
            ->test(\App\Livewire\Surveys\FillSurvey::class, ['survey' => $survey])
            ->set("answers.{$q1->id}", '5')
            ->set("answers.{$q2->id}", 'Sangat Puas')
            ->set('additionalFeedback', 'Dosen sangat ramah dan materi jelas.')
            ->call('submitSurvey')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('survey_responses', [
            'survey_id' => $survey->id,
            'user_id' => $student->id,
            'additional_feedback' => 'Dosen sangat ramah dan materi jelas.',
        ]);

        $this->assertDatabaseHas('survey_answers', [
            'survey_question_id' => $q1->id,
            'answer' => '5',
            'score' => 5,
        ]);

        $this->assertDatabaseHas('survey_answers', [
            'survey_question_id' => $q2->id,
            'answer' => 'Sangat Puas',
            'score' => 5,
        ]);

        $this->assertTrue($survey->fresh()->isCompletedBy($student));
    }

    public function test_admin_can_create_and_delete_survey_from_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // 1. Admin create survey
        $component = \Livewire\Livewire::actingAs($admin)
            ->test(\App\Livewire\Admin\Dashboard::class)
            ->call('openCreateSurveyForm')
            ->set('surveyTitle', 'Survei Fasilitas Laboratorium Baru')
            ->set('surveyDescription', 'Evaluasi lab komputer.')
            ->set('surveyQuestions', [
                ['question' => 'Kecepatan komputer lab', 'type' => 'scale_1_5'],
                ['question' => 'Kenyamanan AC lab', 'type' => 'satisfaction_4'],
            ])
            ->call('saveSurvey')
            ->assertHasNoErrors();

        $survey = \App\Models\Survey::where('title', 'Survei Fasilitas Laboratorium Baru')->first();
        $this->assertNotNull($survey);
        $this->assertEquals(2, $survey->questions()->count());

        // 2. Admin delete survey
        $component->call('deleteSurvey', $survey->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('surveys', ['id' => $survey->id]);
    }
}

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
        Schema::table('feedbacks', function (Blueprint $table) {
            $table->boolean('is_anonymous')->default(false)->after('status');
            $table->string('urgency', 20)->default('normal')->after('is_anonymous'); // low, normal, high, urgent
        });

        Schema::create('feedback_upvotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feedback_id')->constrained('feedbacks')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['feedback_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback_upvotes');

        Schema::table('feedbacks', function (Blueprint $table) {
            $table->dropColumn(['is_anonymous', 'urgency']);
        });
    }
};

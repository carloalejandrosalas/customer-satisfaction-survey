<?php

use App\QuestionTypeEnum;
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
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->index();
            $table->foreignId('creator_id')->index(); // user_id
            $table->string('name');
            $table->text('description');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('surveys_customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id');
            $table->foreignId('customer_id');
            $table->foreignId('assigned_user_id');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('survey_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id');
            $table->string('text');
            $table->enum('type', QuestionTypeEnum::cases());
            $table->timestamps();
        });

        Schema::create('survey_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_customer_id');
            $table->foreignId('survey_question_id');
            $table->string('value');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_answers');
        Schema::dropIfExists('survey_questions');
        Schema::dropIfExists('surveys_customers');
        Schema::dropIfExists('surveys');
    }
};

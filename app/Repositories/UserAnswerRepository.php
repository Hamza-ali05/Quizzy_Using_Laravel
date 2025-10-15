<?php

namespace App\Repositories;

use App\Models\UserAnswer;

class UserAnswerRepository
{
    /**
     * Get all user answers with related attempt, question, and option.
     */
    public function all()
    {
        return UserAnswer::with(['attempt', 'question', 'option'])->get();
    }

    /**
     * Find a single answer by ID with relations.
     */
    public function findById($id)
    {
        return UserAnswer::with(['attempt', 'question', 'option'])->findOrFail($id);
    }

    /**
     * Get all answers for a specific attempt and quiz, keyed by question_id.
     */
    public function getAnswersForAttempt($attemptId, $quizId)
    {
        return UserAnswer::where('attempt_id', $attemptId)
                         ->where('quiz_id', $quizId)
                         ->get()
                         ->keyBy('question_id');
    }

    /**
     * Save or update an answer for a given attempt, user, quiz, and question.
     */
    public function saveAnswer($attemptId, $userId, $quizId, $questionId, $optionId)
    {
        return UserAnswer::updateOrCreate(
            [
                'attempt_id'  => $attemptId,
                'member_id'   => $userId,
                'quiz_id'     => $quizId,
                'question_id' => $questionId,
            ],
            [
                'option_id' => $optionId,
            ]
        );
    }

    /**
     * Create or update an answer using a data array.
     */
    public function updateOrCreate($data)
    {
        return UserAnswer::updateOrCreate(
            [
                'member_id'   => $data['member_id'],
                'quiz_id'     => $data['quiz_id'],
                'question_id' => $data['question_id'],
                'attempt_id'  => $data['attempt_id'] ?? null,
            ],
            [
                'option_id' => $data['option_id'],
            ]
        );
    }

    /**
     * Update an existing answer.
     */
    public function update($userAnswer, $data)
    {
        $userAnswer->update($data);
        return $userAnswer;
    }

    /**
     * Delete a user answer.
     */
    public function delete($userAnswer)
    {
        $userAnswer->delete();
    }
}

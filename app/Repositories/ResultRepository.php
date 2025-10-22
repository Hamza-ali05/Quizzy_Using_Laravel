<?php

namespace App\Repositories;
use App\Models\Result;
use App\Models\Attempt;

class ResultRepository
{
    public function storeResult($quizId, $studentId, $questionId, $optionId, $correct)
    {
        return Result::create([
            'quiz_id'     => $quizId,
            'student_id'  => $studentId,
            'question_id' => $questionId,
            'option_id'   => $optionId,
            'correct'     => $correct
        ]);
    }

    // Get all results for quizzes created by an admin
    public function allForAdmin($adminId)
    {
        return Attempt::with(['member', 'quiz'])
            ->whereHas('quiz', function ($q) use ($adminId) {
                $q->where('created_by', $adminId);
            })
            ->get();
    }

    // Get all results for a specific student
    public function allForStudent($studentId)
    {
        return Attempt::with(['member', 'quiz'])
            ->where('member_id', $studentId)
            ->get();
    }

    // Store multiple results for a quiz attempt
    public function storeResults($quiz, $studentId, $answers)
    {
        $score = 0;
        $total = $quiz->questions()->count();

        foreach ($quiz->questions as $question) {
            $chosen = $answers["question_{$question->id}"] ?? null;

            $isCorrect = $question->options()->where('id', $chosen)->where('correct_option', 1)->exists();

            if ($isCorrect) $score++;

            // Use the existing storeResult method
            $this->storeResult($quiz->id, $studentId, $question->id, $chosen, (int)$isCorrect);
        }

        return [
            'score' => $score,
            'total' => $total
        ];
    }

    // Find a single result by ID
    public function findById($id)
    {
        return Result::with(['attempt', 'question', 'option'])->findOrFail($id);
    }

    // Update a result
    public function update($result, $data)
    {
        $result->update($data);
        return $result;
    }

    // Delete a result
    public function delete($result)
    {
        $result->delete();
    }
}

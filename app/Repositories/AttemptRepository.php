<?php

namespace App\Repositories;

use App\Models\Attempt;

class AttemptRepository
{
    // =================== Existing methods ===================

    // Find active attempt for a user & quiz
    public function findActive($userId, $quizId)
    {
        return Attempt::where('member_id', $userId)
                      ->where('quiz_id', $quizId)
                      ->whereNull('finished_at')
                      ->first();
    }

    // Finalize an attempt (marks and finished_at)
    public function finalizeAttempt($attempt, $score)
    {
        $attempt->update([
            'marks' => $score,
            'finished_at' => now(),
        ]);
        return $attempt;
    }

    // =================== New CRUD / Admin methods ===================

    // Get all attempts for quizzes created by an admin
    public function allForAdmin($adminId)
    {
        return Attempt::with(['member', 'quiz'])
            ->whereHas('quiz', function($q) use ($adminId) {
                $q->where('created_by', $adminId);
            })
            ->get();
    }

    // Find attempt by ID with all relations
    public function findById($id)
    {
        return Attempt::with(['member', 'quiz', 'answers'])->findOrFail($id);
    }

    // Create a new attempt
    public function create($data)
    {
        return Attempt::create($data);
    }

    // Update an existing attempt
    public function update($attempt, $data)
    {
        $attempt->update($data);
        return $attempt;
    }

    // Delete an attempt
    public function delete($attempt)
    {
        $attempt->delete();
    }
}

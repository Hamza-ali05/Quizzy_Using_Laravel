<?php

namespace App\Repositories;

use App\Models\Question;

class QuestionRepository
{
    public function allByAdmin($adminId)
    {
        return Question::with('options', 'quiz')
                       ->whereHas('quiz', fn($q) => $q->where('created_by', $adminId))
                       ->get();
    }

    public function findById($id)
    {
        return Question::with(['quiz', 'options'])->findOrFail($id);
    }

    public function create($quiz, $data)
    {
        return $quiz->questions()->create([
            'questions_text' => $data['text']
        ]);
    }

    public function update($question, $data)
    {
        return $question->update([
            'quiz_id' => $data['quiz_id'],
            'questions_text' => $data['questions_text']
        ]);
    }

    public function delete($question)
    {
        return $question->delete();
    }
}

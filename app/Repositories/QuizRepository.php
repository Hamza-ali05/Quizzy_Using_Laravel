<?php

namespace App\Repositories;
use App\Models\Quiz;
class QuizRepository
{
    public function allWithCreator()
    {
        return Quiz::with('creator')->get();
    }

    public function findById($id, $creatorId = null)
    {
        $query = Quiz::with('questions.options')->where('id', $id);
        if ($creatorId) {
            $query->where('created_by', $creatorId);
        }
        return $query->firstOrFail();
    }

    public function create($data, $creatorId)
    {
        return Quiz::create(array_merge($data, ['created_by' => $creatorId]));
    }

    public function update(Quiz $quiz, $data)
    {
        $quiz->update($data);
        return $quiz;
    }

    public function delete(Quiz $quiz)
    {
        $quiz->delete();
    }
}

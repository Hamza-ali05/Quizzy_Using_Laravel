<?php

namespace App\Repositories;

use App\Models\Option;

class OptionRepository
{
    
    public function createOptions($question, $options, $correctIndex)
    {
        foreach ($options as $i => $opt) {
            $question->options()->create([
                'option_s' => $opt['text'],
                'correct_option' => ($correctIndex == $i) ? 1 : 0
            ]);
        }
    }

    public function deleteOptions($question)
    {
        $question->options()->delete();
    }

    public function allWithQuestions()
    {
        return Option::with('question', 'question.quiz')->get();
    }

    public function findById($id)
    {
        return Option::with('question')->findOrFail($id);
    }

    public function create($data)
    {
        return Option::create($data);
    }

    public function update($option, $data)
    {
        $option->update($data);
        return $option;
    }

    public function delete($option)
    {
        $option->delete();
    }
}

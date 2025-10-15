<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Question;
use App\Repositories\OptionRepository;
use Illuminate\Http\Request;

class OptionController extends Controller
{
    protected $optionRepo;

    public function __construct(OptionRepository $optionRepo)
    {
        $this->optionRepo = $optionRepo;
    }

    public function index()
    {
        $quizzes = Quiz::with('questions.options')->get();
        $questions = Question::with('options','quiz')->get();
        $options = $this->optionRepo->allWithQuestions();

        return view('admin.options.index', compact('quizzes', 'questions', 'options'));
    }

    public function create()
    {
        $questions = Question::all();
        return view('admin.options.create', compact('questions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'question_id'    => 'required|exists:questions,id',
            'option_s'       => 'required|string|max:255',
            'correct_option' => 'required|boolean',
        ]);

        $this->optionRepo->create($request->all());

        return redirect()->route('options.index')->with('success', 'Option created successfully.');
    }

    public function show($id)
    {
        $option = $this->optionRepo->findById($id);
        return view('admin.options.show', compact('option'));
    }

    public function edit($id)
    {
        $option = $this->optionRepo->findById($id);
        $questions = Question::all();
        return view('admin.options.edit', compact('option', 'questions'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'question_id'    => 'required|exists:questions,id',
            'option_s'       => 'required|string|max:255',
            'correct_option' => 'required|boolean',
        ]);

        $option = $this->optionRepo->findById($id);
        $this->optionRepo->update($option, $request->all());

        return redirect()->route('options.index')->with('success', 'Option updated successfully.');
    }

    public function destroy($id)
    {
        $option = $this->optionRepo->findById($id);
        $this->optionRepo->delete($option);

        return redirect()->route('options.index')->with('success', 'Option deleted successfully.');
    }
}

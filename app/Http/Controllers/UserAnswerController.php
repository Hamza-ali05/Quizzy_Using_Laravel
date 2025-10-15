<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Http\Request;
use App\Repositories\UserAnswerRepository;

class UserAnswerController extends Controller
{
    protected $userAnswerRepo;

    public function __construct(UserAnswerRepository $userAnswerRepo)
    {
        $this->userAnswerRepo = $userAnswerRepo;
    }

    public function index()
    {
        $answers = $this->userAnswerRepo->all();
        return response()->json($answers);
    }

    public function create()
    {
        $attempts  = Attempt::all();
        $questions = Question::all();
        $options   = Option::all();

        return view('student.attempt', compact('attempts', 'questions', 'options'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'member_id'   => 'required|exists:members,id',
            'quiz_id'     => 'required|exists:quizzes,id',
            'question_id' => 'required|exists:questions,id',
            'option_id'   => 'required|exists:options,id',
            'attempt_id'  => 'nullable|exists:attempts,id',
        ]);

        $answer = $this->userAnswerRepo->updateOrCreate($request->all());

        return response()->json([
            'message' => 'Answer saved successfully',
            'data'    => $answer
        ], 201);
    }

    public function show($id)
    {
        $userAnswer = $this->userAnswerRepo->findById($id);
        return response()->json($userAnswer);
    }

    public function edit($id)
    {
        $userAnswer = $this->userAnswerRepo->findById($id);

        $attempts  = Attempt::all();
        $questions = Question::all();
        $options   = Option::all();

        return view('student.editoptions', compact('userAnswer', 'attempts', 'questions', 'options'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'option_id' => 'required|exists:options,id',
        ]);

        $userAnswer = $this->userAnswerRepo->findById($id);
        $updated = $this->userAnswerRepo->update($userAnswer, $request->all());

        return response()->json([
            'message' => 'Answer updated successfully!',
            'data'    => $updated
        ]);
    }

    public function destroy($id)
    {
        $userAnswer = $this->userAnswerRepo->findById($id);
        $this->userAnswerRepo->delete($userAnswer);

        return response()->json([
            'message' => 'Answer deleted successfully!'
        ]);
    }
}

<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Repositories\QuestionRepository;
use App\Repositories\OptionRepository;

class QuestionController extends Controller
{
    protected $questionRepo;
    protected $optionRepo;

    public function __construct(QuestionRepository $questionRepo, OptionRepository $optionRepo)
    {
        $this->questionRepo = $questionRepo;
        $this->optionRepo = $optionRepo;
    }

    public function index(Request $request)
    {
        $quizzes = Quiz::where('created_by', auth()->id())->get();
        $selectedQuiz = null;
        if ($request->has('quiz_id')) {
            $selectedQuiz = Quiz::where('id', $request->quiz_id)
                                ->where('created_by', auth()->id())
                                ->first();
        }

        $questions = $this->questionRepo->allByAdmin(auth()->id());

        return view('admin.options.index', compact('questions', 'selectedQuiz', 'quizzes'));
    }

    public function create(Quiz $quiz)
    {
        return view('admin.questions.create', compact('quiz'));
    }

    public function store(Request $request, Quiz $quiz)
    {
        $request->validate([
            'text' => 'required|string|max:255',
            'options' => 'required|array|min:2',
            'options.*.text' => 'required|string|max:255',
            'correct_option' => 'required|integer',
        ]);

        $question = $this->questionRepo->create($quiz, $request->all());

        $this->optionRepo->createOptions($question, $request->options, $request->correct_option);

        return redirect()->route('questions.create', $quiz->id)
                         ->with('success', 'Question added successfully! Add another or finish.');
    }

    public function show($id)
    {
        $question = $this->questionRepo->findById($id);
        return response()->json($question);
    }

    public function edit($id)
    {
        $question = $this->questionRepo->findById($id);
        $quizzes = Quiz::all();
        return view('admin.questions.edit', compact('question', 'quizzes'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'questions_text' => 'required|string|max:255',
            'options' => 'required|array|min:2',
            'options.*.text' => 'required|string|max:255',
            'correct_option' => 'required|integer',
        ]);

        $question = $this->questionRepo->findById($id);
        $this->questionRepo->update($question, $request->all());

        $this->optionRepo->deleteOptions($question);
        $this->optionRepo->createOptions($question, $request->options, $request->correct_option);

        return redirect()->route('questions.index')->with('success', 'Question updated successfully!');
    }

    public function destroy($id)
    {
        $question = $this->questionRepo->findById($id);
        $this->optionRepo->deleteOptions($question);
        $this->questionRepo->delete($question);

        return redirect()->route('questions.index')->with('success', 'Question deleted successfully!');
    }
}

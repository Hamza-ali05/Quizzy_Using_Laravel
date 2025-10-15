<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Quiz;
use App\Models\Option;
use App\Repositories\QuizRepository;
use App\Repositories\AttemptRepository;
use App\Repositories\UserAnswerRepository;
use App\Repositories\ResultRepository;

class QuizController extends Controller
{
    protected $quizRepo;
    protected $attemptRepo;
    protected $userAnswerRepo;
    protected $resultRepo;

    public function __construct(
        QuizRepository $quizRepo,
        AttemptRepository $attemptRepo,
        UserAnswerRepository $userAnswerRepo,
        ResultRepository $resultRepo
    ) {
        $this->quizRepo = $quizRepo;
        $this->attemptRepo = $attemptRepo;
        $this->userAnswerRepo = $userAnswerRepo;
        $this->resultRepo = $resultRepo;
    }

    public function index()
    {
        $quizzes = $this->quizRepo->allWithCreator();
        return view('admin.quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        return view('admin.quizzes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:200',
            'duration' => 'required|integer|min:1'
        ]);

        $quiz = $this->quizRepo->create($validated, auth()->id());

        return redirect()->route('questions.create', $quiz->id)
                         ->with('success', 'Quiz created successfully! Now add questions.');
    }

    public function show($id)
    {
        $quiz = $this->quizRepo->findById($id, auth()->id());
        return view('admin.quizzes.show', compact('quiz'));
    }

    public function edit(Quiz $quiz)
    {
        $members = Member::all();
        return view('admin.questions.edit', compact('quiz', 'members'));
    }

    public function update(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string|max:200',
            'duration' => 'required|integer|min:1'
        ]);

        $quiz = $this->quizRepo->update($quiz, $validated);

        return response()->json([
            'message' => 'Quiz updated successfully!',
            'data' => $quiz
        ]);
    }

    public function submit(Request $request, Quiz $quiz)
    {
        $request->validate(['attempt_id' => 'required|integer']);
        $attemptId = $request->attempt_id;

        $answers = $this->userAnswerRepo->getAnswersForAttempt($attemptId, $quiz->id);
        $score = 0;

        foreach ($quiz->questions as $question) {
            $ua = $answers->get($question->id);
            $chosenOptionId = $ua ? $ua->option_id : null;
            $correct = $chosenOptionId && Option::where('id', $chosenOptionId)->where('correct_option', 1)->exists();

            if ($correct) $score++;

            $this->resultRepo->storeResult($quiz->id, auth()->id(), $question->id, $chosenOptionId, (int)$correct);
        }

        $this->attemptRepo->finalizeAttempt($attemptId, $score);

        return redirect()->route('quizzes.result', $quiz->id);
    }

    public function destroy(Quiz $quiz)
    {
        $this->quizRepo->delete($quiz);
        return redirect()->route('admin.dashboard')->with('success', 'Quiz deleted successfully.');
    }
}

<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Repositories\ResultRepository;

class ResultController extends Controller
{
    protected $resultRepo;

    public function __construct(ResultRepository $resultRepo)
    {
        $this->resultRepo = $resultRepo;
    }

    // Display results
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            $attempts = $this->resultRepo->allForAdmin($user->id);
        } elseif ($user->role === 'student') {
            $attempts = $this->resultRepo->allForStudent($user->id);
        } else {
            $attempts = collect();
        }

        return view('results.index', compact('attempts'));
    }

    // Store results for a quiz attempt
    public function store(Request $request, Quiz $quiz)
    {
        $student = auth()->user();
        $resultData = $this->resultRepo->storeResults($quiz, $student->id, $request->all());

        return view('student.results.show', [
            'score' => $resultData['score'],
            'total' => $resultData['total']
        ]);
    }

    // Show a single result
    public function show($id)
    {
        $result = $this->resultRepo->findById($id);
        return view('results.show', compact('result'));
    }

    // Delete a result
    public function destroy($id)
    {
        $result = $this->resultRepo->findById($id);
        $this->resultRepo->delete($result);

        return redirect()->route('results.index')->with('success', 'Result deleted successfully.');
    }
}


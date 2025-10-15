<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Quiz;
use App\Models\Attempt;
use App\Repositories\AttemptRepository;
use Illuminate\Http\Request;

class QuizAttemptController extends Controller
{
    protected $attemptRepo;

    public function __construct(AttemptRepository $attemptRepo)
    {
        $this->attemptRepo = $attemptRepo;
    }

    // Display all attempts of quizzes created by admin
    public function index()
    {
        $attempts = $this->attemptRepo->allForAdmin(auth()->id());
        return view('results.index', compact('attempts'));
    }

    // Show form for creating new attempt
    public function create()
    {
        $members = Member::all();
        $quizzes = Quiz::all();
        return view('attempts.create', compact('members', 'quizzes'));
    }

    // Store new attempt
    public function store(Request $request)
    {
        $request->validate([
            'member_id'  => 'required|exists:members,id',
            'quiz_id'    => 'required|exists:quizzes,id',
            'started_at' => 'nullable|date',
            'end_at'     => 'nullable|date|after_or_equal:started_at',
            'marks'      => 'nullable|integer|min:0',
        ]);

        $attempt = $this->attemptRepo->create($request->all());

        return response()->json([
            'message' => 'Attempt created successfully!',
            'data' => $attempt
        ], 201);
    }

    // Show specific attempt
    public function show($id)
    {
        $attempt = $this->attemptRepo->findById($id);
        return response()->json($attempt);
    }

    // Show form for editing
    public function edit($id)
    {
        $attempt = $this->attemptRepo->findById($id);
        $members = Member::all();
        $quizzes = Quiz::all();
        return view('attempts.edit', compact('attempt', 'members', 'quizzes'));
    }

    // Update attempt
    public function update(Request $request, $id)
    {
        $request->validate([
            'member_id'  => 'required|exists:members,id',
            'quiz_id'    => 'required|exists:quizzes,id',
            'started_at' => 'nullable|date',
            'end_at'     => 'nullable|date|after_or_equal:started_at',
            'marks'      => 'nullable|integer|min:0',
        ]);

        $attempt = $this->attemptRepo->findById($id);
        $this->attemptRepo->update($attempt, $request->all());

        return response()->json([
            'message' => 'Attempt updated successfully!',
            'data' => $attempt
        ]);
    }

    // Delete attempt
    public function destroy($id)
    {
        $attempt = $this->attemptRepo->findById($id);
        $this->attemptRepo->delete($attempt);

        return response()->json([
            'message' => 'Attempt deleted successfully!'
        ]);
    }
}

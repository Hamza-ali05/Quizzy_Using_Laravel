@extends('layouts.app')

@section('content')
    <h1>All Quizzes</h1>
    <ul>
        @foreach ($quizzes as $quiz)
            <li>{{ $quiz->title }}</li>
        @endforeach
    </ul>
@endsection

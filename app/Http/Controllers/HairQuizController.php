<?php

namespace App\Http\Controllers;

use App\Services\HairQuiz\Diagnoser;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class HairQuizController extends Controller
{
    public function show(): View
    {
        return view('pages.quiz', [
            'types'       => Diagnoser::TYPES,
            'problems'    => Diagnoser::PROBLEMS,
            'treatments'  => Diagnoser::TREATMENTS,
            'frequencies' => Diagnoser::FREQUENCIES,
        ]);
    }

    public function result(Request $request, Diagnoser $diagnoser): View
    {
        $data = $request->validate([
            'type'      => 'required|in:' . implode(',', array_keys(Diagnoser::TYPES)),
            'problem'   => 'required|in:' . implode(',', array_keys(Diagnoser::PROBLEMS)),
            'treatment' => 'required|in:' . implode(',', array_keys(Diagnoser::TREATMENTS)),
            'frequency' => 'required|in:' . implode(',', array_keys(Diagnoser::FREQUENCIES)),
        ]);

        $diagnosis = $diagnoser->diagnose($data);

        return view('pages.quiz-result', [
            'answers'   => $data,
            'line'      => $diagnosis['line'],
            'ritual'    => $diagnosis['ritual'],
            'rationale' => $diagnosis['rationale'],
        ]);
    }
}

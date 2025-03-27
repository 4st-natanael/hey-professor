<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException; // Importe a exceção de validação

class QuestionController extends Controller
{
    public function store(): RedirectResponse
    {
        try {
            $attributes = request()->validate([
                'question' => [
                    'required',
                    'min:10',
                    function ($attribute, $value, $fail) {
                        if (substr($value, -1) !== '?') { // Use substr para verificar o último caractere
                            $fail('Are you sure that is a question? It is missing the question mark in the end.');
                        }
                    },
                ],
            ]);

            // Lógica para salvar a pergunta no banco de dados
            // Assumindo que você tem um modelo chamado Question
            \App\Models\Question::create($attributes);

            return to_route('dashboard')->with('success', 'Question created successfully!');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }
}

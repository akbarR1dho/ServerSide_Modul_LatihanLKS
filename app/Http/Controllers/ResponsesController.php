<?php

namespace App\Http\Controllers;

use App\Models\AnswersModel;
use App\Models\FormsModel;
use App\Models\ResponsesModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ResponsesController extends Controller
{
    //
    public function post(Request $req, $slug)
    {

        if (!auth()->check()) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        $validator = Validator::make($req->all(), [
            'answers' => 'required_if:sets,1|array|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid field',
                'errors' => $validator->errors()
            ], 422);
        }

        $form = FormsModel::where('slug', $slug)->first();
        $domain_allowed = $form->allowed_domains()->where('form_id', $form->id)->first();

        if (explode('@', auth()->user()->email)[1] != $domain_allowed->domain) {
            return response()->json([
                'message' => 'Forbidden access',
            ], 403);
        };

        $response = new ResponsesModel();

        $response->user_id = auth()->user()->id;
        $response->form_id = $form->id;

        if ($response::where('form_id', $response->form_id)->where('user_id', $response->user_id)->exists()) {
            return response()->json([
                'message' => 'You can not submit form twice'
            ], 422);
        };

        $response->save();

        foreach ($req->answers as $value) {
            $response->answers()->create([
                'value' => $value['value'],
                'question_id' => $value['question_id'],
                'response_id' => $response->id
            ]);
        }

        return response()->json([
            'message' => 'Post answer success',
        ], 200);
    }

    public function getAll($slug)
    {
        if (!auth()->check()) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        $form = FormsModel::with([
            'responses.user',
            'responses.answers.question'
        ])->where('slug', $slug)->first();

        if ($form->creator_id != auth()->user()->id) {
            return response()->json([
                'message' => 'Forbidden access',
            ], 403);
        };

        // Format ulang data agar sesuai dengan JSON yang diinginkan
        $formattedResponses = $form->responses->map(function ($response) {
            return [
                "date" => $response->created_at,
                "user" => [
                    "id" => $response->user->id,
                    "name" => $response->user->name,
                    "email" => $response->user->email,
                ],
                "answers" => $response->answers->map(function ($answer) {
                    return [
                        "question_id" => $answer->question->id,
                        "question_name" => $answer->question->name,
                        "answer_value" => $answer->value,
                    ];
                })
            ];
        });

        return response()->json([
            'message' => 'Get all responses success',
            'responses' => $formattedResponses
        ], 200);
    }
}

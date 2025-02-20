<?php

namespace App\Http\Controllers;

use App\Models\FormsModel;
use App\Models\QuestionsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuestionsController extends Controller
{
    //
    public function post(Request $req, $slug)
    {
        if (!auth()->check()) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        $form = FormsModel::where('slug', $slug)->first();

        if ($form->creator_id != auth()->user()->id) {
            return response()->json([
                'message' => 'Forbidden access',
            ], 403);
        };

        if (!$form) {
            return response()->json([
                'message' => 'Form not found',
            ], 404);
        }

        $validator = Validator::make($req->all(), [
            'name' => 'required',
            'choice_type' => 'required|in:short answer,paragraph,date,time,multiple choice,dropdown,checkboxes',
            'choices' => 'required_if:choice_type,multiple choice,dropdown,checkboxes|array|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid field',
                'errors' => $validator->errors()
            ], 422);
        }

        $question = new QuestionsModel();

        $question->name = $req->name;
        $question->choice_type = $req->choice_type;
        $question->choices = is_array($req->choices) ? implode(',', $req->choices) : ($req->choices ?? null);
        $question->is_required = $req->is_required;
        $question->form_id = $form->id;

        $question->save();

        return response()->json([
            'message' => 'Create question success',
            'question' => $question
        ], 200);
    }

    public function delete($slug, $id)
    {
        if (!auth()->check()) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        $form = FormsModel::where('slug', $slug)->first();

        if (!$form) {
            return response()->json([
                'message' => 'Form not found',
            ], 404);
        }

        if ($form->creator_id != auth()->user()->id) {
            return response()->json([
                'message' => 'Forbidden access',
            ], 403);
        }

        $question = QuestionsModel::where('id', $id)->first();

        if (!$question) {
            return response()->json([
                'message' => 'Question not found',
            ], 404);
        }

        $question->delete();

        return response()->json([
            'message' => 'Remove question success',
        ], 200);
    }
}

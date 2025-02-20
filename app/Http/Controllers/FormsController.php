<?php

namespace App\Http\Controllers;

use App\Models\FormsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FormsController extends Controller
{
    //
    public function post(Request $req)
    {
        if (!auth()->check()) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        $validator = Validator::make($req->all(), [
            'name' => 'required',
            'slug' => 'required|unique:forms,slug|regex:/^[a-zA-Z0-9.-]+$/',
            'allowed_domains' => 'required|array|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid field',
                'errors' => $validator->errors()
            ], 422);
        }

        $form = new FormsModel();

        $form->name = $req->name;
        $form->slug = $req->slug;
        $form->description = $req->description;
        $form->limit_one_response = $req->limit_one_response;
        $form->creator_id = auth()->user()->id;

        $form->save();

        foreach ($req->allowed_domains as $domain) {
            $form->allowed_domains()->create([
                'domain' => $domain,
                'form_id' => $form->id
            ]);
        }

        return response()->json([
            'message' => 'Created form success',
            'form' => $form
        ], 200);
    }

    public function getAll()
    {
        if (!auth()->check()) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        return response()->json([
            'message' => 'Get all forms success',
            'forms' => FormsModel::where('creator_id', auth()->user()->id)->get()
        ], 200);
    }

    public function getDetail($slug)
    {
        if (!auth()->check()) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        $data = FormsModel::where('slug', $slug)->first();

        $domain_allowed = $data->allowed_domains()->where('form_id', $data->id)->first();

        if (explode('@', auth()->user()->email)[1] != $domain_allowed->domain) {
            return response()->json([
                'message' => 'Forbidden access',
            ], 403);
        };

        if (!$data) {
            return response()->json([
                'message' => 'Form not found',
            ], 404);
        }

        return response()->json([
            'message' => 'Get form success',
            'form' => $data->load('questions')
        ]);
    }
}

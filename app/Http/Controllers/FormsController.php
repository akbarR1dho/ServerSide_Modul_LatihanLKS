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

        $user = auth()->user();

        $form = new FormsModel();

        $form->name = $req->name;
        $form->slug = $req->slug;
        $form->description = $req->description;
        $form->limit_one_response = $req->limit_one_response;
        $form->creator_id = $user->id;

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
        $forms = FormsModel::where('creator_id', auth()->user()->id)->get();

        return response()->json([
            'message' => 'Get all forms success',
            'total' => $forms->count(),
            'forms' => $forms,
        ], 200);
    }

    public function getDetail($slug)
    {
        $data = FormsModel::where('slug', $slug)->first();

        // Ambil daftar domain yang diizinkan untuk form tersebut
        $allowedDomains = $data->allowed_domains()->where('form_id', $data->id)->pluck('domain');

        // Ambil domain dari email user yang login
        $userDomain = explode('@', auth()->user()->email)[1];

        // Cek apakah domain user ada dalam daftar domain yang diizinkan
        if (!$allowedDomains->contains($userDomain)) {
            return response()->json([
                'message' => 'Forbidden access',
            ], 403);
        }

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

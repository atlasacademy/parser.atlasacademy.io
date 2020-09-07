<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

class SubmissionController extends Controller
{

    public function submit(Request $request)
    {
        $data = $request->all(['key']);

        $validator = Validator::make($data, [
            'key' => 'required|alpha_num',
            'event' => 'required',
            'node' => 'required',
            'image' => 'required|url',
            'type' => 'required|in:simple,full',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Failure',
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        if ($data['key'] !== env('SUBMISSION_KEY')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }
    }

}

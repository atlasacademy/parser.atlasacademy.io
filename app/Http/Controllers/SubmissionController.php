<?php

namespace App\Http\Controllers;

use App\Event;
use App\Node;
use App\Submission;
use Illuminate\Http\Request;
use Validator;

class SubmissionController extends Controller
{

    private const RULES = [
        'key' => 'required|alpha_num',
        'event' => 'required',
        'node' => 'required',
        'image' => 'required|url',
        'type' => 'required|in:simple,full',
        'filename' => 'required|string',
        'submitter' => 'max:50',
    ];

    public function submit(Request $request)
    {
        $data = $request->all(array_keys(self::RULES));

        $validator = Validator::make($data, self::RULES);

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

        $event = Event::query()->where('uid', '=', $data['event'])->first();
        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Event not found'
            ], 422);
        }

        $node = Node::query()
            ->where('event_id', '=', $event->id)
            ->where('uid', '=', $data['node'])
            ->first();
        if (!$node) {
            return response()->json([
                'success' => false,
                'message' => 'Node not found'
            ], 422);
        }

        $submission = Submission::create(
            $node,
            $data['type'],
            $data['image'],
            $data['filename'],
            $data['submitter'] ?? null
        );

        Submission::parse($submission);

        return response()->json([
            'success' => true,
            'message' => 'Created submission'
        ]);
    }

}

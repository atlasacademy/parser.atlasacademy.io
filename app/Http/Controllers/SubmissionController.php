<?php

namespace App\Http\Controllers;

use App\Event;
use App\Node;
use App\Submission;
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
            'filename' => 'required|string',
            'submitter' => 'string|max:50',
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

        $submission = new Submission();
        $submission->node_id = $node->id;
        $submission->type = $data['type'];
        $submission->image = $data['image'];
        $submission->filename = $data['filename'];
        $submission->submitter = $data['submitter'] ?? null;
        $submission->save();

        Submission::parse($submission);

        return response()->json([
            'success' => true,
            'message' => 'Created submission'
        ]);
    }

}

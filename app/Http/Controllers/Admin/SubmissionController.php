<?php

namespace App\Http\Controllers\Admin;

use App\Drop;
use App\Http\Controllers\Controller;
use App\Node;
use App\Parser\ParseWrapper;
use App\Submission;
use App\SubmissionStatus;
use Illuminate\Http\Request;
use Validator;

class SubmissionController extends Controller
{

    public function search(Request $request)
    {
        $query = Submission::query();

        $node = $request->get('node');
        $filter = $request->get('filter');
        $page = $request->get('page');

        if ($node) {
            $query->where('node_id', '=', $node);
        }

        switch ($filter) {
            case "success":
                $query->where('status', '>=', 30);
                break;
            case "failed":
                $query->whereBetween('status', [20, 29]);
                break;
            case "pending":
                $query->whereBetween('status', [10, 19]);
                break;
            case "removed":
                $query->where('status', '=', SubmissionStatus::REMOVED()->getValue());
                break;
        }

        $submissions = $query->simplePaginate(100, ['*'], 'page', $page);

        return response()->view('admin-submission-search', [
            'paginator' => $submissions,
            'submissions' => $submissions->items(),
        ]);
    }

    public function show(Submission $submission)
    {
        $eventDrops = $submission->node->drops
            ->filter(function (Drop $drop) {
                return $drop->isEventOnly();
            })
            ->values();

        $parseWrapper = $submission->parse ? new ParseWrapper($submission->parse) : null;

        return response()->view('admin-submission-show', [
            'submission' => $submission,
            'eventDrops' => $eventDrops,
            'parseWrapper' => $parseWrapper
        ]);
    }

    public function create(Request $request)
    {
        $rules = [
            'node_id' => 'required|exists:event_nodes,id',
            'type' => 'required|in:full,simple',
            'image' => 'required|url',
            'submitter' => 'required|string|max:255',
        ];

        $data = $request->all(array_keys($rules));
        $validator = Validator::make($data, $rules);
        $nodeId = $data['node_id'] ?? null;

        if ($validator->fails()) {
            return $this->redirectWithError(
                $nodeId ? "/admin/node/{$nodeId}" : "/admin",
                json_encode($validator->messages()->toArray())
            );
        }

        $node = Node::query()->where('id', '=', $nodeId)->first();
        $submission = Submission::create($node, $data['type'], $data['image'], $data['submitter']);
        Submission::parse($submission);

        return $this->redirectWithSuccess("/admin/node/{$nodeId}", "Successfully created submission");
    }

}

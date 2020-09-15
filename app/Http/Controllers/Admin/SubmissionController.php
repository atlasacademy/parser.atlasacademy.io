<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\CheckParseResultJob;
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
                $query->whereBetween('status', [1, 9]);
                break;
        }

        $submissions = $query
            ->orderBy('id', 'DESC')
            ->simplePaginate(100, ['*'], 'page', $page);

        if ($node !== null)
            $submissions->appends('node', $node);
        if ($filter !== null)
            $submissions->appends('filter', $filter);

        return response()->view('admin-submission-search', [
            'paginator' => $submissions,
            'submissions' => $submissions->items(),
        ]);
    }

    public function show(Submission $submission)
    {
        $drops = $submission->node->drops->pluck('uid')->unique()->values();
        $parseWrapper = ParseWrapper::create($submission);

        return response()->view('admin-submission-show', [
            'submission' => $submission,
            'drops' => $drops,
            'parseWrapper' => $parseWrapper
        ]);
    }

    public function reparse(Submission $submission)
    {
        Submission::parse($submission);

        return $this->redirectWithSuccess(
            url()->previous("/admin/submission/{$submission->id}"),
            "Reparsing Submission"
        );
    }

    public function remove(Submission $submission)
    {
        $submission->status = SubmissionStatus::REMOVED();
        $submission->save();

        return $this->redirectWithSuccess(
            url()->previous("/admin/submission/{$submission->id}"),
            "Removed Submission"
        );
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
        $submission = Submission::create(
            $node,
            $data['type'],
            $data['image'],
            "manual.png",
            $data['submitter']
        );
        Submission::parse($submission);

        return $this->redirectWithSuccess(
            "/admin/submission/{$submission->id}",
            "Successfully created submission"
        );
    }

    public function overrideDropCount(Submission $submission, Request $request)
    {
        $data = $request->validate([
            'drop_count' => 'required|integer|min:0',
        ]);

        $parseWrapper = ParseWrapper::create($submission);
        $dropCount = intval($data['drop_count']);

        if ($parseWrapper->dropCount() === $dropCount) {
            return $this->redirectWithError(
                url()->previous("/admin/submission/{$submission->id}"),
                "Drop count did not change"
            );
        }

        $parse = json_decode($submission->parse, true);
        $parse['drop_count'] = $dropCount;
        Submission::populateParse($submission, json_encode($parse));
        $submission->save();

        CheckParseResultJob::dispatch($submission);

        return $this->redirectWithSuccess(
            url()->previous("/admin/submission/{$submission->id}"),
            "Updated drop count"
        );
    }

    public function overrideDropStack(Submission $submission, Request $request)
    {
        $data = $request->validate([
            'x' => 'required|integer|min:0',
            'y' => 'required|integer|min:0',
            'stack' => 'required|integer|min:1',
        ]);

        $x = intval($data['x']);
        $y = intval($data['y']);
        $stack = intval($data['stack']);
        $found = false;

        $parse = json_decode($submission->parse, true);
        foreach ($parse['drops'] as $k => $drop) {
            // if not matching, skip
            if ($drop['x'] !== $x || $drop['y'] !== $y)
                continue;

            // if already found, means double match. exit
            if ($found) {
                return $this->redirectWithError(
                    url()->previous("/admin/submission/{$submission->id}"),
                    "Found two drops with those coordinates"
                );
            }

            // no change
            if ($drop['stack'] === $stack) {
                return $this->redirectWithError(
                    url()->previous("/admin/submission/{$submission->id}"),
                    "Drop stack did not change"
                );
            }

            $parse['drops'][$k]['stack'] = $stack;
            $found = true;
        }

        if (!$found) {
            return $this->redirectWithError(
                url()->previous("/admin/submission/{$submission->id}"),
                "Those coordinates could not be found"
            );
        }

        Submission::populateParse($submission, json_encode($parse));
        $submission->save();

        CheckParseResultJob::dispatch($submission);

        return $this->redirectWithSuccess(
            url()->previous("/admin/submission/{$submission->id}"),
            "Updated drop stack"
        );
    }

    public function overrideQpTotal(Submission $submission, Request $request)
    {
        $data = $request->validate([
            'qp_total' => 'required|integer|min:0',
        ]);

        $parseWrapper = ParseWrapper::create($submission);
        $qpTotal = intval($data['qp_total']);

        if ($parseWrapper->totalQp() === $qpTotal) {
            return $this->redirectWithError(
                url()->previous("/admin/submission/{$submission->id}"),
                "QP Total did not change"
            );
        }

        $parse = json_decode($submission->parse, true);
        $parse['qp_total'] = $qpTotal;
        Submission::populateParse($submission, json_encode($parse));
        $submission->save();

        CheckParseResultJob::dispatch($submission);

        return $this->redirectWithSuccess(
            url()->previous("/admin/submission/{$submission->id}"),
            "Updated QP Total"
        );
    }

}

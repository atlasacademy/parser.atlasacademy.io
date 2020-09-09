<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Node;
use App\Submission;
use App\SubmissionStatus;
use Illuminate\Http\Request;

class NodeController extends Controller
{

    public function show(Node $node)
    {
        return response()->view('admin-node-show', ['node' => $node]);
    }

    public function updateQp(Node $node, Request $request)
    {
        $data = $request->validate(['qp' => 'required|integer|min:0']);

        $node->qp = $data['qp'];
        $node->save();

        $submissions = Submission::query()
            ->where('node_id', '=', $node->id)
            ->where('status', '=', SubmissionStatus::ERROR_QP_MISMATCH()->getValue())
            ->get();

        foreach ($submissions as $submission) {
            Submission::parse($submission);
        }

        return $this->redirectWithSuccess(
            url()->previous("/admin/node/{$node->id}"),
            'Successfully updated QP'
        );
    }

}

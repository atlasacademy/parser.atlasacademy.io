<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Parser\ParserAdapter;
use App\Submission;
use App\SubmissionStatus;
use Illuminate\Http\Request;

class ParserController extends Controller
{

    public function fixUnknown(Request $request, ParserAdapter $parserAdapter)
    {
        $data = $request->validate([
            "from" => "required|alpha_num",
            "to" => "required|alpha_num"
        ]);

        if (!$parserAdapter->hasUnknownCode($data['from'])) {
            return $this->redirectWithError('/admin', 'From code not found');
        }

        $parserAdapter->renameUnknownCode($data['from'], $data['to']);

        $submissions = Submission::query()
            ->where('status', '=', SubmissionStatus::ERROR_UNKNOWN_DROPS()->getValue())
            ->get();

        foreach ($submissions as $submission) {
            Submission::parse($submission);
        }

        return $this->redirectWithSuccess('/admin', 'Successfully renamed template');
    }

}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\AppendSubmissionsJob;
use App\Parser\ParserAdapter;
use App\Submission;
use App\SubmissionStatus;
use App\TemplateMap;
use Illuminate\Http\Request;

class ParserController extends Controller
{

    public function fixUnknown(Request $request, ParserAdapter $parserAdapter)
    {
        $data = $request->validate([
            "name" => "required",
            "code" => "required|alpha_num"
        ]);

        $map = TemplateMap::query()->where('name', '=', $data['name'])->first();
        if (!$map) {
            $map = new TemplateMap();
            $map->name = $data['name'];
        }

        $map->code = $data['code'];
        $map->save();

        $submissions = Submission::query()
            ->where('status', '=', SubmissionStatus::ERROR_UNKNOWN_DROPS()->getValue())
            ->get();

        foreach ($submissions as $submission) {
            Submission::parse($submission);
        }

        return $this->redirectWithSuccess(
            url()->previous('/admin'),
            'Successfully renamed template'
        );
    }

    public function parseFailed()
    {
        $submissions = Submission::query()->whereBetween('status', [20, 29])->get();

        foreach ($submissions as $submission) {
            Submission::parse($submission);
        }

        return $this->redirectWithSuccess(
            url()->previous('/admin'),
            'Starting parse'
        );
    }

    public function startMatch()
    {
        AppendSubmissionsJob::dispatch();

        return $this->redirectWithSuccess(
            url()->previous('/admin'),
            'Starting match'
        );
    }

}

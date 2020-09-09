<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
            "id" => "required|integer",
            "code" => "required|alpha_num"
        ]);

        $map = TemplateMap::query()->where('id', '=', $data['id'])->first();
        if (!$map) {
            $map = new TemplateMap();
            $map->id = $data['id'];
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

}

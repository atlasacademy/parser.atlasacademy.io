<?php

namespace App\Http\Controllers\Admin;

use App\Drop;
use App\Http\Controllers\Controller;
use App\Parser\ParserAdapter;
use App\Submission;
use App\SubmissionStatus;
use App\TemplateMap;
use Illuminate\Http\Request;

class TemplateController extends Controller
{

    /**
     * @var ParserAdapter
     */
    private $parserAdapter;

    public function __construct(ParserAdapter $parserAdapter)
    {
        $this->parserAdapter = $parserAdapter;
    }

    public function show(string $code)
    {
        $templateMaps = TemplateMap::query()->where('code', '=', $code)->get();
        $templates = [];

        foreach ($templateMaps as $templateMap) {
            $templates[] = [
                "name" => $templateMap->name,
                "data" => $this->parserAdapter->getTemplateData($templateMap->name),
            ];
        }

        return response()->view('admin-template-show', [
            'code' => $code,
            'templates' => $templates
        ]);
    }

    public function create(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|alpha_num',
            'file' => 'required|file|mimes:png'
        ]);

        $template = new TemplateMap();
        $template->name = 'temp';
        $template->code = $data['code'];
        $template->save();

        $name = "manual" . str_pad($template->id, 3, "0", STR_PAD_LEFT);
        $filename = $name . ".png";
        $data['file']->move(ParserAdapter::TEMPLATE_DIRECTORY . "/equip", $filename);

        $template->name = $name;
        $template->save();

        $nodeIds = Drop::query()
            ->where('uid', '=', $data['code'])
            ->pluck('node_id');

        $submissions = Submission::query()
            ->whereIn('status', [
                SubmissionStatus::ERROR_INVALID_DROPS()->getValue(),
                SubmissionStatus::ERROR_UNKNOWN_DROPS()->getValue(),
                SubmissionStatus::ERROR_MISSING_DROPS()->getValue(),
            ])
            ->whereIn('node_id', $nodeIds)
            ->get();

        foreach ($submissions as $submission) {
            Submission::parse($submission);
        }

        return $this->redirectWithSuccess(
            url()->previous('/admin/template/' . $data['code']),
            "Successfully created template"
        );
    }

    public function remove(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|alpha_num',
            'name' => 'required'
        ]);

        $template = TemplateMap::query()
            ->where('code', '=', $data['code'])
            ->where('name', '=', $data['name'])
            ->first();

        if (!$template) {
            return $this->redirectWithError(
                url()->previous('/admin/template/' . $data['code']),
                "Failed to find template"
            );
        }

        // Do not delete actual template file. fgosccnt has the template in memory and will just cause issues
        // $this->parserAdapter->deleteTemplate($template->name);

        $template->delete();

        return $this->redirectWithSuccess(
            url()->previous('/admin/template/' . $data['code']),
            "Successfully deleted template"
        );
    }
}

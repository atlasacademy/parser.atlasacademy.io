<?php

namespace App\Http\Controllers\Admin;

use App\Export;
use App\Http\Controllers\Controller;
use App\Parser\ParseWrapper;

class ExportController extends Controller
{

    public function show(Export $export)
    {
        $parseWrapper = new ParseWrapper($export->type, $export->parse);

        return response()->view('admin-export-show', [
            'export' => $export,
            'parseWrapper' => $parseWrapper,
            'node' => $export->node,
        ]);
    }

}

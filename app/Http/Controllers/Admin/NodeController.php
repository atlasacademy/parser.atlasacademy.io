<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Node;

class NodeController extends Controller
{

    public function show(Node $node)
    {
        return response()->view('admin-node-show', ['node' => $node]);
    }

}

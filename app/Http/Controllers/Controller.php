<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function redirectWithError(string $url, string $message): RedirectResponse
    {
        return response()->redirectTo($url)->with('message.error', $message);
    }

    protected function redirectWithSuccess(string $url, string $message): RedirectResponse
    {
        return response()->redirectTo($url)->with('message.success', $message);
    }
}

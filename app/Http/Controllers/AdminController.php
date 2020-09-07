<?php

namespace App\Http\Controllers;

use App\Event;
use App\Jobs\ImportEventJob;
use App\Submission\SubmissionApi;
use Illuminate\Http\Request;

class AdminController extends Controller
{

    public function index()
    {
        return response()->view('admin-index');
    }

    public function createEvent(Request $request, SubmissionApi $submissionApi)
    {
        $data = $request->validate([
            "uid" => "required|alpha_num"
        ]);
        $uid = $data['uid'];

        $submissionEvent = $submissionApi->event($uid);
        if (!$submissionEvent)
            return $this->redirectWithError('/admin', 'Event not found');

        if (Event::query()->where('uid', '=', $uid)->count())
            return $this->redirectWithError('/admin', 'Event already exists');

        $event = new Event();
        $event->uid = $submissionEvent->uid();
        $event->name = $submissionEvent->name();
        if (!$event->save())
            return $this->redirectWithError('/admin', 'Failed to create event');

        ImportEventJob::dispatch($event)->onQueue('priority');

        return $this->redirectWithSuccess('/admin', 'Successfully created event');
    }

}

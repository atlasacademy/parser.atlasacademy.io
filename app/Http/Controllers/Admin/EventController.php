<?php

namespace App\Http\Controllers\Admin;

use App\Event;
use App\Http\Controllers\Controller;
use App\Jobs\ImportEventJob;
use App\Submission\SubmissionApi;
use Illuminate\Http\Request;

class EventController extends Controller
{

    public function index()
    {
        $events = Event::all();

        return response()->view('admin-event-index', ['events' => $events]);
    }

    public function show(Event $event)
    {
        return response()->view('admin-event-show', ['event' => $event]);
    }

    public function create(Request $request, SubmissionApi $submissionApi)
    {
        $data = $request->validate([
            "uid" => "required|alpha_num"
        ]);
        $uid = $data['uid'];

        $submissionEvent = $submissionApi->event($uid);
        if (!$submissionEvent)
            return $this->redirectWithError('/admin/events', 'Event not found');

        if (Event::query()->where('uid', '=', $uid)->count())
            return $this->redirectWithError('/admin/events', 'Event already exists');

        $event = new Event();
        $event->uid = $submissionEvent->uid();
        $event->name = $submissionEvent->name();
        if (!$event->save())
            return $this->redirectWithError('/admin/events', 'Failed to create event');

        ImportEventJob::dispatch($event)->onQueue('priority');

        return $this->redirectWithSuccess('/admin/events', 'Successfully created event');
    }

}

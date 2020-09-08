<?php
/**
 * @var \App\Event[] $events
 */
?>

@include('_messages')

<ul>
    @foreach($events as $event)
        <li>
            <a href="/admin/event/{{ $event->id }}">{{ $event->name }}</a>
        </li>
    @endforeach
</ul>

<form method="post" action="/admin/event/create">
    Create New Event:
    <input type="text" name="uid" value="" autocomplete="off" placeholder="UID"/>
    <input type="submit" value="Create"/>
</form>

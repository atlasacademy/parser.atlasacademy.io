<?php
/**
 * @var \App\Event $event
 */
?>

@include('_messages')

<ul>
    <li>
        <a href="/admin/event/{{ $event->id }}/refresh">Refresh Event</a>
    </li>
</ul>

<p>Nodes</p>

<ul>
    @foreach ($event->nodes as $node)
        <li>
            <a href="/admin/node/{{ $node->id }}">{{ $node->name }}</a>
        </li>
    @endforeach
</ul>

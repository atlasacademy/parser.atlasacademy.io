<?php
/**
 * @var \App\Event $event
 */
?>

@include('_messages')

<p>Nodes</p>

<ul>
    @foreach ($event->nodes as $node)
        <li>
            <a href="/admin/node/{{ $node->id }}">{{ $node->name }}</a>
        </li>
    @endforeach
</ul>

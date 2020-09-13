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

<table border="1" cellpadding="5px">
    <tr>
        <th>ID</th>
        <td>{{ $event->uid }}</td>
    </tr>
    <tr>
        <th>Name</th>
        <td>{{ $event->name }}</td>
    </tr>
    <tr>
        <th colspan="2">
            Nodes
        </th>
    </tr>
    @foreach ($event->nodes as $node)
        <tr>
            <th>{{ $node->uid }}</th>
            <td>
                <a href="/admin/node/{{ $node->id }}">{{ $node->name }}</a>
            </td>
        </tr>
    @endforeach
</table>

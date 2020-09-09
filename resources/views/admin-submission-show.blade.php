<?php
/**
 * @var \App\Submission $submission
 * @var string[] $drops
 * @var \App\Parser\ParseWrapper|null $parseWrapper
 */
?>

@include('_messages')

<table border="1" cellpadding="5px">
    <tr>
        <th>ID</th>
        <td>{{ $submission->id }}</td>
    </tr>
    <tr>
        <th>Node</th>
        <td>
            <a href="/admin/node/{{ $submission->node->id }}">Link</a>
        </td>
    </tr>
    <tr>
        <th>Export</th>
        <td>
            @if ($submission->export_id)
                <a href="/admin/export/{{ $submission->export_id }}">Link</a>
            @endif
        </td>
    </tr>
    <tr>
        <th>Source</th>
        <td>{{ $submission->image }}</td>
    </tr>
    <tr>
        <th>Submitter</th>
        <td>{{ $submission->submitter }}</td>
    </tr>
    <tr>
        <th>Status</th>
        <td>{{ $submission->status->getKey() }}</td>
    </tr>
    <tr>
        <th>Actions</th>
        <td>
            <ul>
                @if ($submission->status->getValue() >= 20 && $submission->status->getValue() < 30)
                    <li>
                        <a href="/admin/submission/{{ $submission->id }}/reparse">Reparse</a>
                    </li>
                @endif
            </ul>
        </td>
    </tr>
    <tr>
        <th>Image</th>
        <td>
            <img src="{{ $submission->image }}" width="80%">
        </td>
    </tr>
    <tr>
        <th>Parse</th>
        <td>{{ $submission->parse }}</td>
    </tr>
    <tr>
        <th>Map</th>
        <td>
            @if ($parseWrapper)
                @include('parse-map', ['parseWrapper' => $parseWrapper, 'node' => $submission->node, 'drops' => $drops])
            @endif
        </td>
    </tr>
</table>

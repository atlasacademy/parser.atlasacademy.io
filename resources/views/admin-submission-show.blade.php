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
            <a href="/admin/node/{{ $submission->node->id }}">{{ $submission->node->name }}</a>
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
                @if ($submission->isErrorStatus())
                    <li>
                        <a href="/admin/submission/{{ $submission->id }}/reparse">Reparse</a>
                    </li>
                    <li>
                        <a href="/admin/submission/{{ $submission->id }}/remove">Remove</a>
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
            @if ($parseWrapper && $parseWrapper->isValid())
                @include('parse-map', [
                    'parseWrapper' => $parseWrapper,
                    'node' => $submission->node,
                    'drops' => $drops,
                    'override' => $submission->isErrorStatus(),
                    'submissionId' => $submission->id
                ])
            @endif
        </td>
    </tr>
    @if ($parseWrapper && $parseWrapper->isValid() && $submission->isErrorStatus())
        <tr>
            <th>Override</th>
            <td>
                <form method="post" action="/admin/submission/{{ $submission->id }}/override-drop-count">
                    Drop Count:
                    <input type="text" name="drop_count" value="{{ $parseWrapper->dropCount() }}"/>
                    <input type="submit" value="Update"/>
                </form>

                <form method="post" action="/admin/submission/{{ $submission->id }}/override-qp-total">
                    QP Total:
                    <input type="text" name="qp_total" value="{{ $parseWrapper->totalQp() }}"/>
                    <input type="submit" value="Update"/>
                </form>

                <form method="post" action="/admin/submission/{{ $submission->id }}/override-scroll-position">
                    Scroll Position:
                    <input type="text" name="scroll_position" value="{{ $parseWrapper->scrollPosition() }}"/>
                    <input type="submit" value="Update"/>
                </form>
            </td>
        </tr>
    @endif
</table>

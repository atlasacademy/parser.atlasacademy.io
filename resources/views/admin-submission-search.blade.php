<?php
/**
 * @var \Illuminate\Contracts\Pagination\Paginator $paginator
 * @var \App\Submission[] $submissions
 */
?>

@include('_messages')

<table border="1">
    <thead>
    <tr>
        <th>ID</th>
        <th>Image</th>
        <th>Type</th>
        <th>Node</th>
        <th>Submitter</th>
        <th>Status</th>
        <th>Created</th>
        <th>Updated</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($submissions as $submission)
        <tr>
            <td>
                <a href="/admin/submission/{{ $submission->id }}">{{ $submission->id }}</a>
            </td>
            <td>
                <a href="{{ $submission->image }}" target="_blank">Link</a>
            </td>
            <td>{{ $submission->type }}</td>
            <td>{{ $submission->node_id }}</td>
            <td>{{ $submission->submitter }}</td>
            <td>{{ $submission->status->getKey() }}</td>
            <td>{{ $submission->created_at }}</td>
            <td>{{ $submission->updated_at }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

{{ $paginator->render() }}

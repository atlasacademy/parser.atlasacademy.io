@include('_messages')

<ul>
    <li>
        <a href="/admin/events">Events</a>
    </li>
    <li>
        <a href="/admin/submission/search?filter=pending">Pending Submissions</a>
    </li>
    <li>
        <a href="/admin/submission/search?filter=failed">Failed Submissions</a>
    </li>
    <li>
        <a href="/admin/submission/search?filter=removed">Removed Submissions</a>
    </li>
    <li>
        <a href="/admin/submission/search">All Submissions</a>
    </li>
    <li>
        <a href="/admin/parser/start-match">Trigger Match Job</a>
    </li>
    <li>
        <a href="/admin/parser/parse-failed">ReParse Failed</a>
    </li>
    <li>
        <a href="/admin/parser/remove-failed">Remove Failed</a>
    </li>
</ul>

<table border="1" cellpadding="5px">
    <tbody>
    <tr>
        <th>Failed Submissions</th>
        <td>{{ $failedCount }}</td>
    </tr>
    <tr>
        <th>Pending Submissions</th>
        <td>{{ $pendingCount }}</td>
    </tr>
    <tr>
        <th>Jobs</th>
        <td>{{ $jobCount }}</td>
    </tr>
    </tbody>
</table>

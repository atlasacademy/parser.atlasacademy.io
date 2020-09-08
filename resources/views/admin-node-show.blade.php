<?php
/**
 * @var \App\Node $node
 */
?>

@include('_messages')

<ul>
    <li>
        <a href="/admin/submission/search?node={{ $node->id }}&filter=failed">Failed Submissions</a>
    </li>
    <li>
        <a href="/admin/submission/search?node={{ $node->id }}&filter=success">Successful Submissions</a>
    </li>
    <li>
        <a href="/admin/submission/search?node={{ $node->id }}&filter=removed">Removed Submissions</a>
    </li>
    <li>
        <a href="/admin/submission/search?node={{ $node->id }}">All Submissions</a>
    </li>
</ul>

<hr />

<p>Drops</p>

<table border="1" cellpadding="5px">
    <thead>
    <tr>
        <th>Code</th>
        <th>Quantity</th>
        <th>Event</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($node->drops as $drop)
        <tr>
            <td>{{ $drop->uid }}</td>
            <td>{{ $drop->quantity }}</td>
            <td>{{ $drop->isEventOnly() ? "YES" : "NO" }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<hr />

<form method="post" action="/admin/submission/create">
    <input type="hidden" name="node_id" value="{{ $node->id }}" />
    <p><b>Create New Submission</b></p>
    <table>
        <tbody>
        <tr>
            <td>Type:</td>
            <td>
                <select name="type">
                    <option>full</option>
                    <option>simple</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>Image:</td>
            <td>
                <input type="text" name="image" autocomplete="off" />
            </td>
        </tr>
        <tr>
            <td>Submitter:</td>
            <td>
                <input type="text" name="submitter" autocomplete="off" />
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
                <input type="submit" value="Create"/>
            </td>
        </tr>
        </tbody>
    </table>
</form>

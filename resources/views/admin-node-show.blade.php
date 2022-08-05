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

<table border="1" cellpadding="5px">
    <tr>
        <th colspan="3">Details</th>
    </tr>
    <tr>
        <th>Event</th>
        <td colspan="2">
            <a href="/admin/event/{{ $node->event->id }}">{{ $node->event->name }}</a>
        </td>
    </tr>
    <tr>
        <th>ID</th>
        <td colspan="2">{{ $node->uid }}</td>
    </tr>
    <tr>
        <th>Name</th>
        <td colspan="2">{{ $node->name }}</td>
    </tr>
    <tr>
        <th>QP</th>
        <td colspan="2">{{ $node->qp }}</td>
    </tr>
    <tr>
        <th colspan="3">Drops</th>
    </tr>
    @foreach ($node->drops as $drop)
        <tr>
            <td>{{ $drop->quantity }} ×</td>
            <td><img src="https://assets.atlasacademy.io/drops/{{ $drop->uid }}.png" style="height: 3em;"/></td>
            <td>
                <a href="/admin/template/{{ $drop->uid }}">{{ $drop->uid }}</a>
            </td>
        </tr>
    @endforeach
</table>

<hr />

<form method="post" action="/admin/node/{{ $node->id }}/update-qp">
    <p><b>Update QP Reward</b></p>
    <table>
        <tbody>
        <tr>
            <td>Amount:</td>
            <td>
                <input type="text" name="qp" value="{{ $node->qp }}"/>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
                <input type="submit" value="Update"/>
            </td>
        </tr>
        </tbody>
    </table>
</form>

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

<?php
/**
 * @var \App\Export $export
 */
?>

@include('_messages')

<table border="1" cellpadding="5px">
    <tr>
        <th>ID</th>
        <td>{{ $export->id }}</td>
    </tr>
    <tr>
        <th>Submitter</th>
        <td>{{ $export->submitter }}</td>
    </tr>
    <tr>
        <th>Receipt</th>
        <td>{{ $export->receipt }}</td>
    </tr>
    <tr>
        <th>Token</th>
        <td>{{ $export->token }}</td>
    </tr>
    <tr>
        <th>Payload</th>
        <td>{{ $export->payload }}</td>
    </tr>
    <tr>
        <th>Parse</th>
        <td>{{ $export->parse }}</td>
    </tr>
    @foreach ($export->submissions as $k=>$submission)
        <tr>
            <th>#{{ $k }}</th>
            <td>
                <img src="{{ $submission->image }}" width="80%"/>
            </td>
        </tr>
    @endforeach
    <tr>
        <th>Map</th>
        <td>
            @include('parse-map', ['parseWrapper' => $parseWrapper, 'node' => $node, 'drops' => [], 'override' => false])
        </td>
    </tr>
</table>

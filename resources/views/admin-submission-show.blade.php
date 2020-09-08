<?php
/**
 * @var \App\Submission $submission
 * @var \App\Drop[]|\Illuminate\Database\Eloquent\Collection $drops
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
                <table border="1" cellpadding="5px" width="100%">
                    <tbody>
                    <?php for ($y = 0; $y <= $parseWrapper->lastLine(); $y++) : ?>
                    <?php $dropLine = $parseWrapper->dropLine($y); ?>
                    <tr>
                        <?php for ($x = 0; $x < count($dropLine); $x++) : ?>
                        <td>
                            {{ $dropLine[$x]->code() }} x {{ $dropLine[$x]->stack() }}

                            @if ($dropLine[$x]->isUnknown())
                                <br/>
                                <form method="post" action="/admin/parser/fix-unknown" style="margin-bottom: 0;">
                                    <input type="hidden" name="id" value="{{ $dropLine[$x]->id() }}"/>
                                    <select name="code">
                                        @foreach ($drops as $drop)
                                            <option>{{ $drop->uid }}</option>
                                        @endforeach
                                    </select>
                                    <input type="submit" value="Fix"/>
                                </form>
                            @endif

                            @if (!$dropLine[$x]->isInNode($submission->node))
                                <br/>
                                <span style="color: red">INVALID</span>
                            @endif
                        </td>
                        <?php endfor; ?>
                    </tr>
                    <?php endfor; ?>
                    </tbody>
                </table>
            @endif
        </td>
    </tr>
</table>

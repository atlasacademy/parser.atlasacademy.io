<?php
/**
 * @var \App\Parser\ParseWrapper $parseWrapper
 * @var \App\Node $node
 * @var string[] $drops
 */
?>

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
                    <input type="hidden" name="name" value="{{ $dropLine[$x]->code() }}"/>
                    <select name="code">
                        @foreach ($drops as $drop)
                            <option>{{ $drop }}</option>
                        @endforeach
                    </select>
                    <input type="submit" value="Fix"/>
                </form>
            @endif

            @if ($submission->type === "full" && !$dropLine[$x]->isInNode($node))
                <br/>
                <span style="color: red">INVALID</span>
            @elseif ($submission->type === "simple" && !$dropLine[$x]->isDefaultDrop())
                <br/>
                <span style="color: gray">IGNORED</span>
            @elseif ($submission->type === "simple" && $dropLine[$x]->isDefaultDrop() && !$dropLine[$x]->isInNode($node))
                <br/>
                <span style="color: red">INVALID</span>
            @endif
        </td>
        <?php endfor; ?>
    </tr>
    <?php endfor; ?>
    </tbody>
</table>

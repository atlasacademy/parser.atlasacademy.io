@include('_messages')

<table border="1" cellpadding="5px">
    <tr>
        <th>Code</th>
        <td>{{ $code }}</td>
        <td></td>
    </tr>
    <tr>
        <th>Original</th>
        <td>
            <img src="https://assets.atlasacademy.io/drops/{{ $code }}.png"/>
        </td>
        <td></td>
    </tr>
    @foreach ($templates as $template)
        <tr>
            <th>{{ $template['name'] }}</th>
            <td>
                @if ($template['data'] !== null)
                    <img src="data:image/png;base64,{{ $template['data'] }}"/>
                @endif
            </td>
            <td>
                <form method="post" action="/admin/template/remove">
                    <input type="hidden" name="code" value="{{ $code }}"/>
                    <input type="hidden" name="name" value="{{ $template['name'] }}"/>
                    <input type="submit" value="Remove"/>
                </form>
            </td>
        </tr>
    @endforeach
</table>

<hr/>

<form method="post" action="/admin/template/create" enctype="multipart/form-data">
    <input type="hidden" name="code" value="{{ $code }}"/>
    <p><b>Create New Template</b></p>
    <p>
        <input type="file" name="file"/>
    </p>
    <p>
        <input type="submit" value="Create"/>
    </p>
</form>

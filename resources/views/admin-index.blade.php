@if (session('message.success'))
    <p class="success">SUCCESS: {{ session('message.success') }}</p>
@endif

@if (session('message.error'))
    <p class="success">ERROR: {{ session('message.error') }}</p>
@endif

<ul>
    <li>
    </li>
</ul>

<form method="post" action="/admin/create-event">
    Create New Event:
    <input type="text" name="uid" value="" autocomplete="off" placeholder="UID"/>
    <input type="submit" value="Create"/>
</form>

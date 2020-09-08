@if (session('message.success'))
    <p class="success">SUCCESS: {{ session('message.success') }}</p>
@endif

@if (session('message.error'))
    <p class="success">ERROR: {{ session('message.error') }}</p>
@endif

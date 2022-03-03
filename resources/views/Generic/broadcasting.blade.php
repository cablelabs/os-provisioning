<script type="module">
    import Echo from "{{ asset('vendor/echo.js') }}"

    let config = @json(config('broadcasting.connections.pusher'));

    window.echo = new Echo({
            broadcaster: 'pusher',
            key: config.key,
            wsHost: window.location.hostname,
            wsPort: config.options.port,
            wssPort: config.options.port,
            forceTLS: config.options.encrypted,
            disableStats: true,
            enabledTransports: ['ws', 'wss'],
        });
</script>

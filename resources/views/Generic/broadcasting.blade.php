<script type="module">
    import Echo from "{{ asset('vendor/echo.js') }}"

    var config = @json(config('broadcasting.connections.pusher'));

    window.echo = new Echo({
        broadcaster: 'pusher',
        key: config.key,
        wsHost: window.location.hostname,
        wsPort: config.options.port,
        wssPort: config.options.port,
        forceTLS: config.options.encrypted,
        disableStats: true,
        enabledTransports: ['ws', 'wss'],
    })


    /**
     * Unsubscribe from channel
     *
     */
    window.unsubscribe = function unsubscribe()
    {
        echo.leave(channel)
        console.log('Leave channel ' + channel)
    }

    // Vue mixin that performs ajax if route is defined in component data
    window.broadcastingAxiosCall = {
        data: function () {
            return {
                route: null
            }
        },
        mounted () {
            axios.post(this.route)
                .finally(() => {
                    console.log('Vue mounted')
                })
        }
    }
</script>

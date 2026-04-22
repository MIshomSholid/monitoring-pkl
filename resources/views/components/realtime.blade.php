<script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>

<script>
    window.REALTIME_CONFIG = {
        userId: "{{ auth()->id() }}",
        role: "{{ auth()->user()->role }}"
    };
</script>

<script src="{{ asset('js/realtime.js') }}"></script>
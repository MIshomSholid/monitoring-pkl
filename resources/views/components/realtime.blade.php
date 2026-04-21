<script src="http://localhost:3001/socket.io/socket.io.js"></script>

<script>
    window.REALTIME_CONFIG = {
        userId: "{{ auth()->id() }}",
        role: "{{ auth()->user()->role }}"
    };
</script>

<script src="{{ asset('js/realtime.js') }}"></script>
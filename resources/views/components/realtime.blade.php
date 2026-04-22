<script src="https://accomplished-truth-production.up.railway.app/socket.io/socket.io.js"></script>

<script>
    window.REALTIME_URL = "https://accomplished-truth-production.up.railway.app";
</script>

<script>
    window.REALTIME_CONFIG = {
        userId: "{{ auth()->id() }}",
        role: "{{ auth()->user()->role }}"
    };
</script>

<script src="{{ asset('js/realtime.js') }}"></script>
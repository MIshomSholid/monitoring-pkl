<script>
    window.REALTIME_URL = "https://valiant-laughter-production.up.railway.app";

    window.REALTIME_CONFIG = {
        userId: "{{ auth()->id() }}",
        role: "{{ auth()->user()->role }}"
    };
</script>

<!-- LOAD SOCKET.IO -->
<script src="https://valiant-laughter-production.up.railway.app/socket.io/socket.io.js" defer></script>

<!-- LOAD SCRIPT KAMU -->
<script src="{{ asset('js/realtime.js') }}" defer></script>
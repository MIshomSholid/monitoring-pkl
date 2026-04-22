/**
 * socket.js
 * Handle realtime presensi (Socket.IO Client)
 */

let socket = null;

/**
 * Inisialisasi koneksi socket
 * @param {string} url - URL server Node.js
 */
export function initSocket(url = window.REALTIME_URL) {
    if (socket) return socket;

    socket = io(url, {
        transports: ['websocket'],
        reconnection: true,
        reconnectionAttempts: 5,
        reconnectionDelay: 2000,
    });

    socket.on('connect', () => {
        console.log('✅ Socket connected:', socket.id);
    });

    socket.on('disconnect', () => {
        console.warn('⚠️ Socket disconnected');
    });

    socket.on('connect_error', err => {
        console.error('❌ Socket error:', err.message);
    });

    return socket;
}

/**
 * Emit event presensi baru
 * Dipanggil SETELAH presensi sukses disimpan
 * @param {Object} data
 */
export function emitPresensiCreated(data) {
    if (!socket) {
        console.warn('Socket belum diinisialisasi');
        return;
    }

    socket.emit('presensi.created', data);
}

/**
 * Listen realtime presensi (admin / guru / pembimbing)
 * @param {Function} callback
 */
export function onPresensiCreated(callback) {
    if (!socket) {
        console.warn('Socket belum diinisialisasi');
        return;
    }

    socket.on('presensi.created', callback);
}

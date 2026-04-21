export function startGPS({ onUpdate, onError }) {
    if (!navigator.geolocation) {
        onError('Browser tidak mendukung GPS');
        return null;
    }

    return navigator.geolocation.watchPosition(
        pos => {
            onUpdate({
                lat: pos.coords.latitude,
                lng: pos.coords.longitude
            });
        },
        () => onError('GPS tidak aktif'),
        {
            enableHighAccuracy: true,
            timeout: 10000
        }
    );
}

export function stopGPS(watchId) {
    if (watchId) {
        navigator.geolocation.clearWatch(watchId);
    }
}

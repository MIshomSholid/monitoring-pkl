let stream = null;

export function startCamera(videoEl) {
    return navigator.mediaDevices.getUserMedia({
        video: { facingMode: "user" }
    }).then(s => {
        stream = s;
        videoEl.srcObject = s;
    });
}

export function stopCamera() {
    if (stream) {
        stream.getTracks().forEach(t => t.stop());
        stream = null;
    }
}
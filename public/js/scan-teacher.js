let html5QrCode;

function initializeScanner() {
    if (typeof Html5Qrcode === "undefined") {
        console.error("Html5Qrcode is not defined. Retrying in 1 second...");
        setTimeout(initializeScanner, 1000);
        return;
    }

    html5QrCode = new Html5Qrcode("reader");

    html5QrCode.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: { width: 250, height: 250 } },
        onScanSuccess,
        onScanError
    ).catch(err => {
        console.error('Error starting QR Code scanner:', err);
        alert('Failed to start QR Code scanner. Please check your camera permissions.');
    });

}

function onScanSuccess(decodedText) {
    const qr_code_id = parseInt(decodedText.trim(), 10);
    if (isNaN(qr_code_id)) {
        alert('Error: Invalid QR code format.');
        return;
    }
    fetch('/dashboard/teacher/scan-qr', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            qr_code_id: qr_code_id,
        })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw err; });
        }
        return response.json();
    })
    .then(data => {
        console.log('Server response:', data);
        alert(data.success || 'Data successfully recorded!');
    })
    .catch(error => {
        console.error('Error:', error);
        alert(error.error || 'Failed to record data. Please try again.');
    });
}

function extractQrCodeId(decodedText) {
    const parts = decodedText.split(' ');
    const id = parseInt(parts[0], 10);

    if (isNaN(id)) {
        console.error('Invalid QR code ID:', decodedText);
        return null;
    }
    return id;
}

function onScanError(errorMessage) {
    console.error('QR Code scan error:', errorMessage);
}

document.addEventListener('DOMContentLoaded', initializeScanner);
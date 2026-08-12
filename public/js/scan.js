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

// Function to handle successful QR code scan
function onScanSuccess(decodedText) {
    console.log('Scanned QR code data:', decodedText);

    const qr_code_id = extractQrCodeId(decodedText);
    console.log('Extracted QR code ID:', qr_code_id);

    if (qr_code_id === null) {
        alert('Error: Missing required data.');
        return;
    }

    fetch('/dashboard/scan-qr', {
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

// Function to extract qr_code_id from the QR code data
function extractQrCodeId(decodedText) {
    // Misalkan data QR code adalah "25 2024-10-09T13:45"
    const parts = decodedText.split(' '); // Memisahkan bagian menggunakan spasi
    const id = parseInt(parts[0], 10); // Mengambil bagian pertama sebagai ID

    if (isNaN(id)) {
        console.error('Invalid QR code ID:', decodedText);
        return null; // Mengembalikan null jika ID tidak valid
    }
    return id; // Mengembalikan ID yang berhasil diekstrak
}

// Function to handle QR code scan errors
function onScanError(errorMessage) {
    console.error('QR Code scan error:', errorMessage);
}

// Initialize the HTML5 QR code scanner when the DOM is fully loaded
document.addEventListener('DOMContentLoaded', initializeScanner);

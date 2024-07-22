async function computeSHA256(text) {
    const encoder = new TextEncoder();
    const data = encoder.encode(text);

    const hashBuffer = await crypto.subtle.digest('SHA-256', data);
    const hashArray = Array.from(new Uint8Array(hashBuffer));
    const hashHex = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');

    return hashHex;
}

function arrayBufferToBase64(buffer) {
    let binary = '';
    let bytes = new Uint8Array(buffer);
    let len = bytes.byteLength;
    for (let i = 0; i < len; i++) {
        binary += String.fromCharCode(bytes[i]);
    }
    return btoa(binary);
}

function formatAsPEM(label, base64) {
    let formatted = `-----BEGIN ${label}-----\n`;
    for (let i = 0; i < base64.length; i += 64) {
        formatted += base64.substring(i, i + 64) + '\n';
    }
    formatted += `-----END ${label}-----\n`;
    return formatted;
}

async function generateKeys() {
    const keyPair = await window.crypto.subtle.generateKey(
        {
            name: "RSA-OAEP",
            modulusLength: 2048,
            publicExponent: new Uint8Array([1, 0, 1]),
            hash: { name: "SHA-256" }
        },
        true,
        ["encrypt", "decrypt"]
    );

    const publicKeyBuffer = await window.crypto.subtle.exportKey("spki", keyPair.publicKey);
    const privateKeyBuffer = await window.crypto.subtle.exportKey("pkcs8", keyPair.privateKey);

    const publicKeyBase64 = arrayBufferToBase64(publicKeyBuffer);
    const privateKeyBase64 = arrayBufferToBase64(privateKeyBuffer);

    const publicKeyPEM = formatAsPEM('PUBLIC KEY', publicKeyBase64);
    const privateKeyPEM = formatAsPEM('PRIVATE KEY', privateKeyBase64);

    return [publicKeyPEM, privateKeyPEM];
}

async function encrypt(text, publicKey) {
    const encoder = new TextEncoder();
    const encodedText = encoder.encode(text);

    const publicKeyBase64 = publicKey.replace(/-----.*?-----|\s+/g, '');
    const publicKeyBuffer = base64ToArrayBuffer(publicKeyBase64);

    const key = await window.crypto.subtle.importKey(
        "spki",
        publicKeyBuffer,
        {
            name: "RSA-OAEP",
            hash: { name: "SHA-256" }
        },
        true,
        ["encrypt"]
    );

    const encrypted = await window.crypto.subtle.encrypt(
        {
            name: "RSA-OAEP"
        },
        key,
        encodedText
    );

    return encrypted;
}

async function decrypt(encrypted, privateKey) {
    const privateKeyBase64 = privateKey.replace(/-----.*?-----|\s+/g, '');
    const privateKeyBuffer = base64ToArrayBuffer(privateKeyBase64);

    const key = await window.crypto.subtle.importKey(
        "pkcs8",
        privateKeyBuffer,
        {
            name: "RSA-OAEP",
            hash: { name: "SHA-256" }
        },
        true,
        ["decrypt"]
    );

    const decrypted = await window.crypto.subtle.decrypt(
        {
            name: "RSA-OAEP"
        },
        key,
        encrypted
    );

    const decoder = new TextDecoder();
    return decoder.decode(decrypted);
}

function base64ToArrayBuffer(base64) {
    let binary_string = atob(base64);
    let len = binary_string.length;
    let bytes = new Uint8Array(len);
    for (let i = 0; i < len; i++) {
        bytes[i] = binary_string.charCodeAt(i);
    }
    return bytes.buffer;
}

async function createRoom(i) {
    if (i.disabled) {
        return false;
    }
    i.disabled = true;

    const keys = await generateKeys();
    const hashed = await computeSHA256(keys[0] + keys[1]);

    fetch('/api/new-room.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          roomkey: hashed
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.status=="success"){
            document.getElementById("rmk").value = hashed;
            document.getElementById("pbk").value = keys[0];
            document.getElementById("prk").value = keys[1];
        }else {
            document.getElementById("rmk").value = "ERROR!";
            document.getElementById("pbk").value = "ERROR!";
            document.getElementById("prk").value = "ERROR!";
        }
    })
    .catch(error => console.error('Error:', error));

    i.disabled = false;
    return true;
}


async function joinroom(i) {
    window.location.href = "/read.php?roomkey=" + document.getElementById("roomkeyjoin").value;
}

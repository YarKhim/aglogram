function encryptMessage(message, public_key) {
    const publicKey = forge.pki.publicKeyFromPem(public_key);
    const encrypted = publicKey.encrypt(message, 'RSA-OAEP');
    return forge.util.encode64(encrypted); // Кодируем в base64
}

function generateGUID() {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        const r = Math.random() * 16 | 0;
        const v = c === 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);
    });
}

function decryptMessage(encryptedMessage, private_key) {
    const privateKey = forge.pki.privateKeyFromPem(private_key);
    const decodedMessage = forge.util.decode64(encryptedMessage);
    const decrypted = privateKey.decrypt(decodedMessage, 'RSA-OAEP');
    return decrypted;
}

function dataURLtoBlob(dataURL) {
    const byteString = atob(dataURL.split(',')[1]);
    const mimeString = dataURL.split(',')[0].split(':')[1].split(';')[0];
    const ab = new ArrayBuffer(byteString.length);
    const ia = new Uint8Array(ab);

    for (let i = 0; i < byteString.length; i++) {
        ia[i] = byteString.charCodeAt(i);
    }


    return new Blob([ab], {
        type: mimeString
    });
}

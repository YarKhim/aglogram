const forge = require('node-forge');

// Пример использования node-forge
function encryptData(data, publicKeyPem) {
    const publicKey = forge.pki.publicKeyFromPem(publicKeyPem);
    const encrypted = publicKey.encrypt(forge.util.encodeUtf8(data), 'RSA-OAEP');
    return forge.util.encode64(encrypted);
}

module.exports = { encryptData };

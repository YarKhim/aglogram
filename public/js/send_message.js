function send_message() {
    data = {
        'chat_id': selected_chat,
        'user_id': this_user_id,
        'type_message': 'isnt_typing',
    }
    sendMessage(JSON.stringify(data));
    if (document.getElementById('message_input').value.trim() || message_send_type == 'file') {
        const encrypt = new JSEncrypt();
        addressee_id = document.querySelector('.user_info_header_foto').id;
        let data_get_keys = {
            addressee: document.querySelector('.user_info_header_foto').id,
        };
        if (!imageInput.files.length) {
            message_send_type = 'text';
        } else {
            message_send_type = 'file';
        }
        // console.log(message_send_type);
        $.ajax({
            url: '/get_keys', // URL вашего маршрута
            method: 'GET', // Метод запроса (GET или POST)
            data: data_get_keys,
            success: function(response) {
                const publicKeyPem = response['public_key'];
                const privateKeyPem = response['private_key'];
                const secretKey = response['chat_key'];

                function encryptMessage(message, public_key) {
                    const publicKey = forge.pki.publicKeyFromPem(public_key);
                    const encrypted = publicKey.encrypt(message, 'RSA-OAEP');
                    return forge.util.encode64(encrypted); // Кодируем в base64
                }

                function decryptMessage(encryptedMessage, private_key) {
                    const privateKey = forge.pki.privateKeyFromPem(private_key);
                    const decodedMessage = forge.util.decode64(encryptedMessage);
                    const decrypted = privateKey.decrypt(decodedMessage, 'RSA-OAEP');
                    return decrypted;
                }
                message = [];
                label_text = [];
                image_src = null;
                guid = generateGUID();



                // #TODO
                if (message_send_type == 'file') {
                    function loadImageAndEncrypt(file, secretKey) {
                        return new Promise((resolve, reject) => {
                            const reader = new FileReader();

                            reader.onload = function(event) {
                                try {
                                    function splitData(data, chunkSize) {
                                        const chunks = [];
                                        for (let i = 0; i < data.length; i += chunkSize) {
                                            chunks.push(data.slice(i, i + chunkSize));
                                        }
                                        return chunks;
                                    }
                                    image_src_res = event.target.result;
                                    // console.log("🚀 ~ returnnewPromise ~ image_src_res:",
                                    //     image_src_res.toString());
                                    const encryptedImage = CryptoJS.AES.encrypt(event.target
                                        .result, secretKey).toString();
                                    const chunkSize = 500;
                                    const dataChunks = splitData(encryptedImage, chunkSize);
                                    // console.log("🚀 ~ returnnewPromise ~ dataChunks:",
                                    //     dataChunks);

                                    resolve(
                                        dataChunks, image_src_res
                                    ); // Разрешаем промис с полученными данными
                                } catch (error) {
                                    console.error('Ошибка при обработке изображения:',
                                        error);
                                    reject(error); // Отклоняем промис при ошибке
                                }
                            };

                            reader.onerror = function(error) {
                                console.error('Ошибка чтения файла:', error);
                                reject(error); // Отклоняем промис при ошибке чтения
                            };

                            reader.readAsDataURL(file); // Или другой метод чтения файла
                        });
                    }
                    // console.log('lbl_2: ',
                    //     document.getElementById('message_input').value);
                    // console.log('trim',document.getElementById('message_input').value.trim());
                    if (document.getElementById('message_input').value.trim()) {

                        if (document.getElementById('message_input').value.length < 100) {
                            label_text[0] = document.getElementById('message_input').value;
                        } else {
                            label_text = splitString(document.getElementById('message_input').value);
                        }

                        for (let i = 0; i < label_text.length; i++) {
                            encryptedMessage = CryptoJS.AES.encrypt(label_text[i], secretKey)
                                .toString();
                            // console.log("🚀 ~ send_message ~ encryptedMessage:", encryptedMessage)
                            label_text[i] = encryptedMessage;
                            encryptedMessage = null;
                        }
                    } else {
                        // console.log(123433245);
                        label_text = '';
                    }
                    // console.log('Должно быть загифровано: ',
                    //     label_text);
                    const fileInput = document.getElementById('imageInput');
                    const file = fileInput.files[0];
                    loadImageAndEncrypt(file, secretKey)
                        .then(dataChunks => {
                            const addressee = document.querySelector('.user_info_header_foto').id;

                            const now = new Date();
                            const hours = now.getHours();
                            const minutes = now.getMinutes();
                            const formattedMinutes = minutes < 10 ? '0' + minutes : minutes;
                            let data = {
                                type_message: 'message',
                                message: dataChunks,
                                addressee: addressee,
                                encrypted_key: encryptMessage(secretKey, publicKeyPem),
                                chat_id: response['chat_id'],
                                privateKey: privateKeyPem,
                                guid: guid,
                                message_data: 'file',
                                label: label_text,
                            };
                            // console.log("🚀 ~ send_message ~ data:", data)
                            sendMessage(JSON.stringify(data));
                            // console.log('23432', image_src_res);
                            if (addressee_id != this_user_id) {
                                // вёрстка отображения фото с подписью
                                const now = new Date();
                                const hours = now.getHours();
                                const minutes = now.getMinutes();
                                chat_tab_div = `<div class="message border_debug" id="` +
                                    guid +
                                    `">
                                            <div class="my_message_with_media my_message border_debug" id="my_message_` +
                                    guid +
                                    `">

                                                <div class='my_message_data_with_media'><div class= 'message_media_photo'><img id="media_message_` +
                                    guid +
                                    `" src='` + image_src_res +
                                    `'></div>
                                                    <div class='my_message_text_data_with_media'><div class='my_message_text'><p class='message_p'>` +
                                    document.getElementById('message_input').value +
                                    `
                                                </p></div>

                                                <div class="message_time no_select" ><p id="my_message_time_` +
                                    guid +
                                    `">` + hours + ':' + minutes +
                                    `</p></div>

                                                <div class='my_message_read_state border_debug no_select'><p id='my_message_read_state_` +
                                    guid + `'>` + ("✓".repeat(1)) + `</p></div</div>
                                                </div>

                                            </div>
                                            </div>
                                        </div>
                                        `;
                                $('#messages_all').append(chat_tab_div);
                                document.getElementById('message_input').value = null;
                                document.getElementById('imageInput').value = null;
                                // document.getElementById("my_message_" + guid).style.padding = '0';
                                // document.getElementById("media_message_" + guid).style
                                //     .borderRadius = '15px';
                                // document.getElementById(media_message_` +
                            //     guid +
                            //     `).src = image_src_res;
                            }
                            // Здесь вы можете работать с dataChunks
                        })
                        // .then(image_src_res => {
                        //     console.log(image_src_res);
                        // })
                        .catch(error => {
                            console.error('Ошибка:', error);
                        });
                    // });
                    // console.log('file: ', file);


                    document.getElementById('last_message_' + response['chat_id']).innerText =
                        'Файл';
                    message_send_type = null;
                    // return;
                }
                if (message_send_type == 'text') {
                    if (document.getElementById('message_input').value.length < 100) {
                        message[0] = document.getElementById('message_input').value;
                    } else {
                        message = splitString(document.getElementById('message_input').value);
                    }

                    for (let i = 0; i < message.length; i++) {
                        encryptedMessage = CryptoJS.AES.encrypt(message[i], secretKey).toString();
                        message[i] = (encryptedMessage);
                        encryptedMessage = null;
                    }
                    document.getElementById('last_message_' + response['chat_id']).innerText =
                        document
                        .getElementById('message_input').value;
                    all_message = document.getElementById('message_input').value;
                    const addressee = document.querySelector('.user_info_header_foto').id;

                    const now = new Date();
                    const hours = now.getHours();
                    const minutes = now.getMinutes();
                    const formattedMinutes = minutes < 10 ? '0' + minutes : minutes;
                    let data = {
                        type_message: 'message',
                        message: message,
                        addressee: addressee,
                        encrypted_key: encryptMessage(secretKey, publicKeyPem),
                        chat_id: response['chat_id'],
                        privateKey: privateKeyPem,
                        guid: guid,
                        message_data: 'text',
                    };
                    // console.log("🚀 ~ send_message ~ data:", data)
                    sendMessage(JSON.stringify(data));
                    if (addressee_id != this_user_id) { // вёрстка отображения текстового сообщения
                        chat_tab_div = `<div class="message border_debug" id="` +
                            guid +
                            `">
                                            <div class="my_message border_debug">
                                                <div class='my_message_data'>
                                                    <div class='my_message_text'><p class='message_p'>` +
                            document.getElementById('message_input').value +
                            `
                                                </p></div>

                                                <div class="message_time no_select" ><p id="my_message_time_` +
                            guid +
                            `">` + hours + ':' + formattedMinutes +
                            `</p></div>

                                                <div class='my_message_read_state border_debug no_select'><p id='my_message_read_state_` +
                            guid + `'>` + ("✓".repeat(1)) + `</p></div></div>

                                            </div>
                                            </div>
                                        </div>
                                        `;
                        $('#messages_all').append(chat_tab_div);
                        document.getElementById('message_input').value = null;
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText); // Обработка ошибки
            }
        });
    }
}

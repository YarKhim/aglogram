<x-app-layout>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="http://peterolson.github.com/BigInteger.js/BigInteger.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsencrypt/3.0.0/jsencrypt.min.js"></script>
    <script src="https://raw.githubusercontent.com/benjaminBrownlee/RSA/master/RSA.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/forge/0.10.0/forge.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.0.0/crypto-js.min.js"></script>
    {{-- <link href="/app.css" rel="stylesheet"> --}}
    <script>
        var selected_chat = null;
        const token = 'YOUR_AUTH_TOKEN';
        const socket = new WebSocket('ws://neptune:8888');
        let unreadChatsData = null;
        let offset = 0;
        const limit = 10
        this_user_id = null;
        favorite_id_chat = null;
        unread_chats = {};
        read_messaegs = {};
        var user_id_opened_chat = null;
        message_send_type = 'text';
        var selectedImage = null;
        document.addEventListener('contextmenu', function(event) {
            event.preventDefault();
        });

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

        function send_message() {

            if (document.getElementById('message_input').value.trim() || message_send_type == 'file') {
                const encrypt = new JSEncrypt();
                addressee_id = document.querySelector('.user_info_header_foto').id;
                let data_get_keys = {
                    addressee: document.querySelector('.user_info_header_foto').id,
                };
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
                            console.log('Должно быть загифровано: ',
                                label_text);
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
                            console.log("🚀 ~ send_message ~ data:", data)
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

                        // console.log(document.getElementById('message_input').value);

                        // console.log('message: ', message)




                        // if (addressee_id != this_user_id) {
                        // #TODOНеобходимо сделать создания временного id для элемента сообщения пока websocket_server не вернёт его постояный id my_message_time_ guid my_message_read_state_
                        // // вёрстка отображения текстового сообщения
                        // chat_tab_div = `<div class="message border_debug" id="` +
                        //     guid +
                        //     `">
                    //                         <div class="my_message border_debug">
                    //                             <div class='my_message_data'>
                    //                                 <div class='my_message_text'><p class='message_p'>` +
                        //     document.getElementById('message_input').value +
                        //     `
                    //                             </p></div>

                    //                             <div class="message_time no_select" ><p id="my_message_time_` +
                        //     guid +
                        //     `">` + hours + ':' + formattedMinutes +
                        //     `</p></div>

                    //                             <div class='my_message_read_state border_debug no_select'><p id='my_message_read_state_` +
                        //     guid + `'>` + ("✓".repeat(1)) + `</p></div></div>

                    //                         </div>
                    //                         </div>
                    //                     </div>
                    //                     `;
                        // /storage/3K4xEsRsU8RGtY6iMmY0GzqDXdoR7DHWHmLXUHch.jpg
                        // https://avatars.mds.yandex.net/i?id=bd14a4ae75206cfb8758fdbc9cd88e9dab833744-5843498-images-thumbs&n=13


                        // вёрстка отображения фото с подписью
                        // chat_tab_div = `<div class="message border_debug" id="` +
                        //     guid +
                        //     `">
                    //                             <div class="my_message border_debug" id="my_message_` + guid +
                        //     `">

                    //                                 <div class='my_message_data_with_media'><div class= 'message_media_photo'><img id="media_message_` +
                        //     guid +
                        //     `" src='/storage/3K4xEsRsU8RGtY6iMmY0GzqDXdoR7DHWHmLXUHch.jpg'></div>
                    //                                     <div class='my_message_text_data_with_media'><div class='my_message_text'><p class='message_p'>` +
                        //     document.getElementById('message_input').value +
                        //     `
                    //                                 </p></div>

                    //                                 <div class="message_time no_select" ><p id="my_message_time_` +
                        //     guid +
                        //     `">` + hours + ':' + formattedMinutes +
                        //     `</p></div>

                    //                                 <div class='my_message_read_state border_debug no_select'><p id='my_message_read_state_` +
                        //     guid + `'>` + ("✓".repeat(1)) + `</p></div</div>
                    //                                 </div>

                    //                             </div>
                    //                             </div>
                    //                         </div>
                    //                         `;
                        // $('#messages_all').append(chat_tab_div);
                        // document.getElementById("my_message_" + guid).style.padding = '0';
                        // document.getElementById("media_message_" + guid).style.borderRadius = '15px';
                        // }
                        //  else {
                        //     chat_tab_div = `<div class="message border_debug" id="` +
                        //         guid +
                        //         `">
                    //                                 <div class="my_message border_debug">
                    //                                     <div class='my_message_data'><div class='my_message_text'><p class='message_p'>` +
                        //         document.getElementById('message_input').value +
                        //         `
                    //                                     </p></div>

                    //                                     <div class="message_time no_select" ><p id="my_message_time_` +
                        //         guid +
                        //         `">` + hours + ':' + formattedMinutes +
                        //         `</p></div>

                    //                                     <div class='my_message_read_state border_debug no_select'><p id='my_message_read_state_` +
                        //         guid + `'>` + ("✓".repeat(2)) + `</p></div></div>

                    //                                 </div>
                    //                                 </div>
                    //                             </div>
                    //                             `;
                        //     $('#messages_all').append(chat_tab_div);
                        // }

                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText); // Обработка ошибки
                    }
                });
            }
        }
        socket.onopen = function(event) {
            console.log('Подключено к WebSocket серверу');
        };
        socket.onmessage = function(event) {
            // update_online_state
            if (JSON.parse(event.data)['type'] == 'update_online_state') {
                // console.log("🚀 ~ JSON:", JSON.parse(event.data)['user_id']);
                // console.log("🚀 ~ JSON:", JSON.parse(event.data)['is_online']);
                if (user_id_opened_chat == JSON.parse(event.data)['user_id']) {
                    // console.log(12);
                    if (JSON.parse(event.data)['is_online'] == true) {
                        // user_info_header_is_online_` + user_id_chat + `
                        document.getElementById('user_info_header_is_online_' + JSON.parse(event.data)['user_id'])
                            .innerText =
                            'online';
                        document.getElementById('user_info_header_is_online_' + JSON.parse(event.data)['user_id'])
                            .style
                            .color =
                            'green';
                    }
                    if (JSON.parse(event.data)['is_online'] == false) {
                        document.getElementById('user_info_header_is_online_' + JSON.parse(event.data)['user_id'])
                            .innerText =
                            'offline';
                        document.getElementById('user_info_header_is_online_' + JSON.parse(event.data)['user_id'])
                            .style
                            .color =
                            'gray';
                    }
                    if (JSON.parse(event.data)['is_online'] == true) {
                        document.getElementById('online_in_chats_list_' + JSON.parse(event.data)['user_id'])
                            .innerText =
                            'online';
                        document.getElementById('online_in_chats_list_' + JSON.parse(event.data)['user_id']).style
                            .color =
                            'green';
                    }
                    if (JSON.parse(event.data)['is_online'] == false) {
                        document.getElementById('online_in_chats_list_' + JSON.parse(event.data)['user_id'])
                            .innerText =
                            'offline';
                        document.getElementById('online_in_chats_list_' + JSON.parse(event.data)['user_id']).style
                            .color =
                            'gray';
                    }
                } else {
                    if (JSON.parse(event.data)['is_online'] == true) {
                        // console.log(1);
                        document.getElementById('online_in_chats_list_' + JSON.parse(event.data)['user_id'])
                            .innerText =
                            'online';
                        document.getElementById('online_in_chats_list_' + JSON.parse(event.data)['user_id']).style
                            .color =
                            'green';
                    }
                    if (JSON.parse(event.data)['is_online'] == false) {
                        document.getElementById('online_in_chats_list_' + JSON.parse(event.data)['user_id'])
                            .innerText =
                            'offline';
                        document.getElementById('online_in_chats_list_' + JSON.parse(event.data)['user_id']).style
                            .color =
                            'gray';
                    }
                }


            }
            if (JSON.parse(event.data)['flag'] == 'new_message') {
                const privateKey = forge.pki.privateKeyFromPem(JSON.parse(event.data)['privateKey']);
                const decodedMessage = forge.util.decode64(JSON.parse(event.data)['encrypted_key']);
                const decrypted = privateKey.decrypt(decodedMessage, 'RSA-OAEP');
                var message_result = '';

                // console.log(222222);

                got_chat_id = JSON.parse(event.data)['chat_id'];
                console.log(JSON.parse(event.data));
                // message_data
                if (JSON.parse(event.data)['message_data'] == 'text') {
                    console.log(111111);
                    for (i = 0; i < JSON.parse(event.data)['message'].length; i++) {
                        message_result += CryptoJS.AES.decrypt(JSON.parse(event.data)['message'][i], decrypted)
                            .toString(
                                CryptoJS
                                .enc.Utf8);
                    }
                    document.getElementById('last_message_' + got_chat_id).innerText = message_result;

                    // last_message_time_
                    const date = new Date(JSON.parse(event.data)['created_at']);

                    // Получаем часы и минуты
                    const hours = date.getHours(); // Часы
                    const minutes = date.getMinutes(); // Минуты
                    document.getElementById('last_message_time_' + got_chat_id).innerText = hours + ':' + minutes;
                    if (got_chat_id == selected_chat) {
                        console.log("В откртый чат пришло собщение!!!");
                        console.log(JSON.parse(event.data))

                        if (JSON.parse(event.data)['chat_id'] != favorite_id_chat) {
                            console.log(JSON.parse(event.data));

                            if (JSON.parse(event.data)['chat_id'] in unread_chats) {
                                unread_chats[JSON.parse(event.data)['chat_id']] += 1;

                            } else {
                                unread_chats[JSON.parse(event.data)['chat_id']] = 1;

                            }
                            document.getElementById('unread_messages_counter_' + JSON.parse(event.data)['chat_id'])
                                .style
                                .display =
                                'flex';
                            console.log(document.getElementById('unread_messages_counter_' + JSON.parse(event.data)[
                                    'chat_id'])
                                .style.display);
                            document.getElementById('unread_messages_counter_' + JSON.parse(event.data)['chat_id'])
                                .innerText =
                                unread_chats[JSON.parse(event.data)['chat_id']];
                            new_message_chat = document.querySelector('.chat_' + JSON.parse(event.data)['chat_id']);
                            // new_message_chat = document.querySelector('.chat_' + selected_chat).classList.add('blink-background');
                            if (new_message_chat.classList.contains('blink-background')) {
                                new_message_chat.classList.remove('blink-background'); // Удаляем класс, если он есть
                            } else {
                                new_message_chat.classList.add('blink-background'); // Добавляем класс, если его нет
                            }
                            new_message_chat.classList.add('blink-background');
                            setTimeout(() => {
                                new_message_chat.classList.remove('blink-background');
                            }, 1000);
                            chat_tab_div = `<div class="message border_debug" id="` + JSON.parse(event.data)[
                                    'message_id_new'
                                ] + `">
                                        <div class="recived_message border_debug">
                                            <div class='recived_message_text'><p class='message_p'>` + message_result + `
                                            </p></div>
                                            <div class="message_time no_select" ><p id="my_message_time_` +
                                JSON.parse(event.data)[
                                    'message_id_new'
                                ] +
                                `">` + hours + ":" + minutes + `</p></div>
                                        </div>
                                    </div>`;
                            $('#messages_all').append(chat_tab_div);

                            function handleIntersection(entries, observer) {
                                entries.forEach(entry => {
                                    if (entry.isIntersecting) {
                                        unread_chats[JSON.parse(event.data)['chat_id']]--;
                                        if (unread_chats[JSON.parse(event.data)['chat_id']] != 0) {
                                            document.getElementById(
                                                    'unread_messages_counter_' +
                                                    JSON.parse(event.data)['chat_id']).innerText =
                                                unread_chats[JSON.parse(event.data)['chat_id']];
                                        } else {
                                            document.getElementById(
                                                    'unread_messages_counter_' +
                                                    JSON.parse(event.data)['chat_id']).style
                                                .display =
                                                'none';
                                        }
                                        let data = {
                                            type_message: 'read_sate_update',
                                            message_id: JSON.parse(event.data)[
                                                'message_id_new'
                                            ],
                                        };
                                        // console.log(
                                        //     "🚀 ~ handleIntersection ~ data:",
                                        //     data)

                                        sendMessage(JSON.stringify(
                                            data));
                                        observer.unobserve(entry.target);
                                    }
                                });
                            }

                            // Создание экземпляра Intersection Observer
                            const observer = new IntersectionObserver(
                                handleIntersection);
                            const targetElement = document.getElementById(
                                JSON.parse(event.data)[
                                    'message_id_new'
                                ]);
                            observer.observe(targetElement);
                        } else {
                            console.log(JSON.parse(event.data)['message']);
                            chat_tab_div = `<div class="message border_debug" id="` + JSON.parse(event.data)[
                                'message_id_new'
                            ] + `">
            <div class="my_message border_debug">
                <p>` + message_result + `
                </p>
            </div>
        </div>
        `;
                            $('#messages_all').append(chat_tab_div);
                        }

                        message_tabs = document.querySelectorAll('.message_tab');
                        // console.log(message_tabs);
                        message_tabs.forEach(function(message_tab) {
                            message_tab.addEventListener('contextmenu', function(event) {});
                        });

                    } else {
                        if (JSON.parse(event.data)['chat_id'] in unread_chats) {
                            unread_chats[JSON.parse(event.data)['chat_id']] += 1;

                        } else {
                            unread_chats[JSON.parse(event.data)['chat_id']] = 1;

                        }
                        document.getElementById('unread_messages_counter_' + JSON.parse(event.data)['chat_id']).style
                            .display =
                            'flex';
                        // console.log(document.getElementById('unread_messages_counter_' + JSON.parse(event.data)['chat_id'])
                        //     .style.display);
                        document.getElementById('unread_messages_counter_' + JSON.parse(event.data)['chat_id'])
                            .innerText =
                            unread_chats[JSON.parse(event.data)['chat_id']];
                        new_message_chat = document.querySelector('.chat_' + JSON.parse(event.data)['chat_id']);
                        // new_message_chat = document.querySelector('.chat_' + selected_chat).classList.add('blink-background');
                        if (new_message_chat.classList.contains('blink-background')) {
                            new_message_chat.classList.remove('blink-background'); // Удаляем класс, если он есть
                        } else {
                            new_message_chat.classList.add('blink-background'); // Добавляем класс, если его нет
                        }
                        new_message_chat.classList.add('blink-background');
                        setTimeout(() => {
                            new_message_chat.classList.remove('blink-background');
                        }, 1000);
                    }
                }

            }
            if (JSON.parse(event.data)['flag'] == 'remove_guid') {
                if (JSON.parse(event.data)['chat_id'] == selected_chat) {
                    // console.log(JSON.parse(event.data)['message_id_new']);
                    // console.log(JSON.parse(event.data)['message']);
                    // my_message_time_ guid my_message_read_state_
                    document.getElementById('my_message_time_' + JSON.parse(event.data)['guid']).id =
                        'my_message_time_' + JSON.parse(event.data)['message_id_new']
                    document.getElementById(JSON.parse(event.data)['guid']).id = JSON.parse(event.data)[
                        'message_id_new']
                    document.getElementById('my_message_read_state_' + JSON.parse(event.data)['guid']).id =
                        'my_message_read_state_' + JSON.parse(event.data)['message_id_new']
                }

            }
            // #TODOДелаем обновление статуса прочтения сообщения в формате онлайн
            if (JSON.parse(event.data)['type_message'] == 'read_sate_update') {

                if (selected_chat == JSON.parse(event.data)['chat_id']) {
                    console.log(JSON.parse(event.data)[
                        'message_id']);
                    document.getElementById('my_message_read_state_' + JSON.parse(event.data)[
                        'message_id']).innerText = '✓✓';
                    // document.getElementById('my_message_read_state_' + JSON.parse(event.data)['message_id']).style.display = 'none';
                }
                // document.getElementById(JSON.parse(event.data)['message_id']).classList.add('read_message')
            }
            // read_sate_update

        };

        socket.onclose = function(event) {
            console.log('Соединение закрыто');
            alert('Соединение закрыто');
        };

        function sendMessage(message) {
            // socket.send(JSON.stringify({
            //     'message': message,
            //     'type_query': type_query
            // }));
            socket.send(message);
        }

        function select_chat(user_id, chat_id) {

            return function() {
                // console.log("🚀 ~ select_chat ~ user_id:", user_id)
                user_id_opened_chat = user_id;
                $('#messages_all').empty();
                document.getElementById('message_input').value = '';
                document.getElementById('message_input').focus();
                selected_chat = chat_id;
                document.getElementById('messages_plane').style.visibility = 'visible';
                let data = {
                    id: user_id,
                    chat_id: chat_id,
                };
                $.ajax({
                    url: '/get_messages_from_chat',
                    method: 'GET',
                    data: {
                        chat_id: chat_id,
                        // offset: offset
                    },
                    success: function(response) {
                        // console.log(1);\
                        flag = false;
                        isReadMessages_id = [];
                        this_unread_message = null;

                        function decryptMessage(encryptedMessage, private_key) {
                            const privateKey = forge.pki.privateKeyFromPem(private_key);
                            const decodedMessage = forge.util.decode64(encryptedMessage);
                            const decrypted = privateKey.decrypt(decodedMessage, 'RSA-OAEP');
                            return decrypted;
                        }
                        all_messages = response['all_messages'];
                        console.log(all_messages);
                        this_user = response['this_user'];
                        second_user = response['second_user'];
                        console.log(all_messages);
                        const scrollContainer = document.getElementById('messages_all');
                        all_messages.forEach(element => {
                            // console.log('qeeh34q0jhg934jtu30950: ',element);
                            sender_id = element['sender_id'];
                            message_result = '';
                            // if (JSON.parse(event.data)['message_data'] == 'text'){}

                            if (sender_id == this_user_id) {
                                key_string = decryptMessage(element['key_string'], second_user[
                                    'private_key']);
                                console.log('element: ', element);
                                if (element['type_message'] == 'text') {
                                    // console.log(1)
                                    JSON.parse(element['message']).forEach(element_message => {
                                        message_result += CryptoJS.AES.decrypt(
                                                element_message,
                                                key_string)
                                            .toString(
                                                CryptoJS
                                                .enc.Utf8);
                                    });
                                    chat_tab_div = `<div class="message border_debug" id="` +
                                        element[
                                            'message_id'] +
                                        `">
                                                        <div class="my_message border_debug" id="my_message_` +
                                        element[
                                            'message_id'] + `">
                                                            <div class='my_message_data'>
                                                                <div class='my_message_text'><p class='message_p'>` +
                                        message_result +
                                        `
                                                            </p></div>

                                                            <div class="message_time no_select" ><p id="my_message_time_` +
                                        element[
                                            'message_id'] +
                                        `">23:40</p></div>

                                                            <div class='my_message_read_state border_debug no_select'><p id='my_message_read_state_` +
                                        element['message_id'] + `'>` + ("✓".repeat((parseInt(
                                            element[
                                                'isRead']) + 1))) + `</p></div>
                                                            </div>
                                                        </div>
                                                        </div>
                                                    </div>
                                                    `;
                                    $('#messages_all').append(chat_tab_div);
                                }
                                if (element['type_message'] == 'file') {
                                    console.log("Дешифруем как изображение");
                                    key_string = decryptMessage(element['key_string'], second_user[
                                        'private_key']);
                                    // console.log('werw: ', element);
                                    const url = 'http://neptune:8000' + element['message'];
                                    console.log("🚀 ~ returnfunction ~ url:", url);
                                    encryptedImg = '';
                                    const controller =
                                        new AbortController(); // Создаем экземпляр AbortController
                                    const signal = controller.signal; // Получаем сигнал
                                    fetch(url)
                                        .then(response => {
                                            if (!response.ok) {
                                                throw new Error('Сеть не в порядке: ' + response
                                                    .statusText);
                                            }
                                            // console.log(response.text());
                                            return response.text(); // Получаем текст
                                        })
                                        .then(data => {
                                            encryptedImg = '';

                                            // #TODOсделать дешифровку сообщения с фото
                                            // Обрабатываем данные
                                            encryptedImg_array = JSON.parse(data);
                                            // console.log(
                                            //     "🚀 ~ returnfunction ~ encryptedImg_array:",
                                            //     encryptedImg_array[0])
                                            console.log(
                                                "🚀 ~ returnfunction ~ encryptedImg_array:",
                                                encryptedImg_array[0]);
                                            // console.log('element[label]: ', element['label']);
                                            // if (element['label'] != "") {
                                            // console.log(123456);
                                            encryptedlabel_array = JSON.parse(element[
                                                'label']);
                                            // console.log(
                                            //     "🚀 ~ returnfunction ~ encryptedlabel_array:",
                                            //     encryptedlabel_array.length);
                                            if (encryptedlabel_array.length != 0) {
                                                encryptedlabel_array.forEach(element => {
                                                    decryptedlabel = CryptoJS.AES
                                                        .decrypt(
                                                            element, key_string)
                                                        .toString(
                                                            CryptoJS.enc.Utf8);
                                                });


                                            } else {
                                                decryptedlabel = '';
                                            }
                                            // console.log(
                                            //     "🚀 ~ returnfunction ~ encryptedlabel:",
                                            //     decryptedlabel);
                                            // }
                                            encryptedImg_array.forEach(element => {
                                                encryptedImg += element;
                                            });
                                            // console.log("🚀 ~ returnfunction ~ encryptedImg:",
                                            //     encryptedImg);
                                            decryptedImg_bytes = CryptoJS.AES.decrypt(
                                                encryptedImg, key_string);
                                            // console.log(
                                            //     "🚀 ~ returnfunction ~ decryptedImg_bytes:",
                                            //     decryptedImg_bytes);
                                            decryptedImg = decryptedImg_bytes.toString(
                                                CryptoJS.enc.Utf8);
                                            // console.log("🚀 ~ returnfunction ~ decryptedImg:",
                                            //     decryptedImg);
                                            decryptedImageBlob = dataURLtoBlob(
                                                decryptedImg); // Convert to Blob
                                            decryptedImageURL = URL.createObjectURL(
                                                decryptedImageBlob);
                                            console.log(
                                                "🚀 ~ returnfunction ~ decryptedImageURL:",
                                                decryptedImageURL);
                                            const now = new Date();
                                            const hours = now.getHours();
                                            const minutes = now.getMinutes();
                                            chat_tab_div =
                                                `<div class="message border_debug" id="` +
                                                element['message_id'] +
                                                `">
                                                    <div class="my_message_with_media my_message border_debug" id="my_message_` +
                                                element['message_id'] +
                                                `">

                                                        <div class='my_message_data_with_media'><div class= 'message_media_photo'><img id="media_message_` +
                                                element['message_id'] +
                                                `" src='` + decryptedImageURL +
                                                `'></div>
                                                            <div class='my_message_text_data_with_media'><div class='my_message_text'><p class='message_p'>` +
                                                decryptedlabel +
                                                `
                                                        </p></div>

                                                        <div class="message_time no_select" ><p id="my_message_time_` +
                                                element['message_id'] +
                                                `">` + hours + ':' + minutes +
                                                `</p></div>

                                                        <div class='my_message_read_state border_debug no_select'><p id='my_message_read_state_` +
                                                element['message_id'] + `'>` + ("✓".repeat(1)) + `</p></div</div>
                                                        </div>

                                                    </div>
                                                    </div>
                                                </div>
                                                `;
                                            $('#messages_all').append(chat_tab_div);
                                            // break

                                        })
                                        .catch(error => {
                                            console.error('Ошибка:', error);
                                        });
                                    // controller.abort();
                                }

                            } else {
                                // #TODOСделать прочтение обновляющееся через вебсокет
                                function handleIntersection(entries, observer) {
                                    entries.forEach(entry => {
                                        if (entry.isIntersecting) {
                                            unread_chats[element['chat_id']]--;
                                            if (unread_chats[element['chat_id']] != 0) {
                                                document.getElementById(
                                                        'unread_messages_counter_' +
                                                        element[
                                                            'chat_id']).innerText =
                                                    unread_chats[element[
                                                        'chat_id']];
                                            } else {
                                                document.getElementById(
                                                        'unread_messages_counter_' +
                                                        element[
                                                            'chat_id']).style
                                                    .display =
                                                    'none';
                                            }
                                            let data = {
                                                type_message: 'read_sate_update',
                                                message_id: element[
                                                    'message_id'
                                                ],
                                            };
                                            sendMessage(JSON.stringify(
                                                data));
                                            observer.unobserve(entry.target);
                                        }
                                    });
                                }


                                if (element['isRead'] == 0) {
                                    if (flag == false) {
                                        flag = true
                                        $('#messages_all').append(
                                            '<h1 style="text-align: center;" id="unread_messages_mark">непрочитанные сообщения!!</h1>'
                                        );
                                    }

                                }
                                // unread_chats
                                // console.log('el: ', element);
                                key_string = decryptMessage(element['key_string'], this_user[
                                    'private_key']);
                                if (element['type_message'] == 'text') {
                                    JSON.parse(element['message']).forEach(element_message => {
                                        message_result += CryptoJS.AES.decrypt(
                                                element_message,
                                                key_string)
                                            .toString(
                                                CryptoJS
                                                .enc.Utf8);
                                    });

                                    // console.log("✓".repeat((parseInt(element['isRead']) + 1)));
                                    chat_tab_div = `<div class="message border_debug" id="` +
                                        element[
                                            'message_id'] +
                                        `" >
                                                        <div class="recived_message border_debug">
                                                            <div class='recived_message_text ' ><p class='message_p'>` +
                                        message_result +
                                        `
                                                            </p></div>
                                                            <div class="message_time no_select" ><p>23:40</p></div>
                                                        </div>
                                                    </div>
                                                    `;
                                    $('#messages_all').append(chat_tab_div);
                                    if (element['isRead'] == 0) {
                                        const observer = new IntersectionObserver(
                                            handleIntersection);
                                        const targetElement = document.getElementById(
                                            element[
                                                'message_id']);
                                        observer.observe(targetElement);
                                    }
                                }
                                // if (element['type_message'] == 'file'){}

                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Ошибка AJAX:', error);
                    }
                });
                $.ajax({
                    url: '/get_user', // URL вашего маршрута
                    method: 'GET', // Метод запроса (GET или POST)
                    data: data,
                    success: function(response) {
                        console.log(response['user']['avatar']);
                        user_id_chat = response['user']['id'];
                        console.log('response', response['user']['isOnline']);
                        user_online = false;
                        if (response['user']['isOnline']) {
                            user_online = 'online';
                        } else {
                            user_online = 'offline';
                        }
                        $('#user_info_header').empty();
                        const all_chats_list = document.getElementById(
                            'user_info_header');
                        if (user_id_chat != parseInt(this_user_id)) {
                            console.log(response.user
                                .avatar);
                            user_chats_header =
                                `<div class="user_info_header_foto border_debug" id=` +
                                response
                                .user
                                .id +
                                `><img src='.` + response.user
                                .avatar + `'></div>
    <div class="border_debug user_info_header_without_foto" id='user_info_header_without_foto'>
        <div class="user_info_header_name border_debug"> <i>` + response.user.name + ' ' +
                                response.user.lastname + ' @' + response.user.username + `<i>
            </div>
            <div class="user_info_header_is_online border_debug" id="user_info_header_is_online_` + user_id_chat +
                                `">` + user_online + `</div>
        </div>`;
                            $('#user_info_header').append(user_chats_header);
                            if (response['user']['isOnline']) {
                                document.getElementById('user_info_header_is_online_' + user_id_chat)
                                    .style
                                    .color = 'green';
                            } else {
                                document.getElementById('user_info_header_is_online_' + user_id_chat)
                                    .style
                                    .color = 'gray';
                            }
                        } else {
                            favorite_id_chat = chat_id
                            user_chats_header =
                                `<div class="user_info_header_foto border_debug" id=` +
                                response
                                .user
                                .id +
                                `><img src='/storage/EKzXqTT0PRHe6OpYr5iN0VF14kAhm9qpaG48iZrd.png'></div>
    <div class="border_debug user_info_header_without_foto" id='user_info_header_without_foto'>
        <div class="user_info_header_name border_debug"> <i>` + `Избранное` + ' ' + `<i>
            </div>
            <div class="user_info_header_is_online border_debug"></div>
        </div>`;
                            $('#user_info_header').append(user_chats_header);
                        }


                        document.getElementById('user_info_header_without_foto')
                            .addEventListener(
                                'click',
                                function() {
                                    window.open('/user_profile?id= ' + response.user
                                        .username)
                                });


                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText); // Обработка ошибки
                    }
                })

            }
        }
    </script>
    <style>
        .p-6 {
            height: 85vh;
        }
    </style>
    <div class="py-12">
        <div class="max-w-10xl mx-auto sm:px-3 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="border_debug main_plane">
                        <div class="border_debug all_chats_list" id='all_chats_list'>
                        </div>
                        <div class="border_debug messages_plane" id="messages_plane">
                            <div class=" user_info_header border_debug" id="user_info_header">
                            </div>
                            <div class="border_debug messages" id="messages_all">
                                {{-- <div class="message border_debug">
                                    <div class="my_message border_debug">
                                        <div class='my_message_data'>
                                            <div class='my_message_text'>
                                                <p class='message_p'>
                                                    4к34е34е
                                                </p>
                                            </div>

                                            <div class="message_time no_select">
                                                <p id="my_message_time_"></p>
                                            </div>

                                            <div class='my_message_read_state border_debug no_select'>
                                                <p id='my_message_read_state_'></p>
                                            </div>
                                        </div>

                                    </div>
                                </div> --}}
                            </div>

                            <div class="border_debug input">
                                <form>
                                    @csrf
                                    <input type="text" autocomplete="off" class="message_input" id='message_input'>
                                    <button type="button" id="send_message">Отправить</button>
                                    <input type="file" id="imageInput">
                                    <div id="encryptedText"></div>
                                    {{-- <input type="text" id="decryptionKey" value="123456"> --}}
                                    <button id="decryptButton">Decrypt</button>
                                    <div id="decryptedImage"></div>
                                </form>
                                <script>
                                    // #TODO
                                    const imageInput = document.getElementById('imageInput');
                                    // console.log("🚀 ~ imageInput:", imageInput);
                                    imageInput.addEventListener('change', (event) => {
                                        message_send_type = 'file';
                                        selectedImage = event.target.files[0];
                                        // const key =
                                        //     "def00000dbda26f0f4311181889a467e81f0be06906eff82735beaa82d452e38284f4ff888a2be04d80454bb8b524e1c0f14d917f23810436a219ae4ca8fd0f1963795ad";


                                    });
                                </script>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
    <script>
        $.ajax({
            url: '/get_chats', // URL вашего маршрута
            method: 'GET', // Метод запроса (GET или POST)
            success: function(response) {
                online = null;
                console.log("response", response.сhats);
                for (let i = 0; i < response.сhats.length; i++) {
                    id = 'last_message_' + response['chat_id'][i]['id'];


                    if (response['chat_id'][i]['creator'] != response['chat_id'][i]['invted']) {
                        user_2 = null;
                        console.log("response", response.сhats[i]);
                        if (response.сhats[i]['isOnline'] == 0) {
                            online = 'offline';
                        } else {
                            online = 'online';
                        }
                        if (response['chat_id'][i]['creator'] == this_user_id) {
                            user_2 = response['chat_id'][i]['invted'];
                        } else {
                            user_2 = response['chat_id'][i]['creator'];

                        }
                        // console.log("🚀 ~ user_2:", user_2);
                        chat_tab_div = `<div class="border_debug chat_tab chat_` + response['chat_id'][i][
                                'id'
                            ] +
                            `" id=` + response.сhats[i]['id'] + `>
                                <div class="border_debug chat_foto"><img src=` + response.сhats[i][
                                'avatar'
                            ] + `></div>
                                <div class="border_debug right_side_chat_tab">
                                    <div class="border_debug chat_info">
                                        <div class="border_debug chat_name">` + response.сhats[i]['name'] +
                            `<div id='online_in_chats_list_` + user_2 + `' class = 'online_in_chats_list'>` +
                            online + `</div></div>
                                        <div class="border_debug read_receipts" id="read_receipts_` + response[
                                'chat_id'][i][
                                'id'
                            ] +
                            `"></div>
                                        <div class="border_debug last_message_time" id="last_message_time_` + response[
                                'chat_id'][i]['id'] +
                            `"></div>

                                    </div>
                                    <div class='border_debug about_messaegs' ><div class="border_debug last_message" id='` +
                            id +
                            `'></div> <div class='unread_messages'><div class='unread_messages_counter' id='unread_messages_counter_` +
                            response['chat_id'][i][
                                'id'
                            ] + `'> 1</div></div></div>
                                </div>

                            </div>`;
                        $('#all_chats_list').append(chat_tab_div);
                        if (online == 'online') {
                            document.getElementById('online_in_chats_list_' + user_2).style.color = 'green';
                        } else {
                            document.getElementById('online_in_chats_list_' + user_2).style.color = 'gray';
                        }
                        // console.log(document.getElementById('online_in_chats_list_' + user_2).style.color);
                        document.getElementById(response.сhats[i]['id']).addEventListener('click', select_chat(
                            response.сhats[i]['id'], response['chat_id'][i]['id']));

                    } else {
                        // console.log(response['chat_id'][i])
                        this_user_id = response['chat_id'][i]['creator'];
                        chat_tab_div = `<div class="border_debug chat_tab chat_` + response['chat_id'][i][
                                'id'
                            ] +
                            `" id=` + response.сhats[i]['id'] + `>
                                <div class="border_debug chat_foto"><img src='/storage/EKzXqTT0PRHe6OpYr5iN0VF14kAhm9qpaG48iZrd.png'></div>
                                <div class="border_debug right_side_chat_tab">
                                    <div class="border_debug chat_info">
                                        <div class="border_debug chat_name">Избранное</div>
                                        <div class="border_debug read_receipts" id="read_receipts_` + response[
                                'chat_id'][i][
                                'id'
                            ] + `"></div>
                                        <div class="border_debug last_message_time" id="last_message_time_` + response[
                                'chat_id'][i]['id'] +
                            `"></div>

                                    </div>
                                    <div class="border_debug last_message" id='` + id + `'></div>
                                </div>

                            </div>`;
                        $('#all_chats_list').append(chat_tab_div);
                        document.getElementById(response.сhats[i]['id']).addEventListener('click', select_chat(
                            response.сhats[i]['id'], response['chat_id'][i]['id']));
                    }

                }
                $.ajax({
                    url: '/unread_chats', // URL вашего маршрута
                    method: 'GET', // Метод запроса (GET или POST)
                    success: function(response) {
                        unread_chats = response['chats_id'];
                        last_messages = response['last_messsages'];
                        last_messages_keys = Object.keys(last_messages);
                        unread_chats_keys = Object.keys(unread_chats);
                        // console.log(response);
                        last_messages_keys.forEach(element => {
                            message_object = last_messages[element];
                            const date = new Date(message_object[
                                'last_message']['created_at']);

                            // Получаем часы и минуты
                            const hours_last = date.getHours(); // Часы
                            const minutes_last = date.getMinutes(); // Минуты
                            // console.log("🚀 ~ message_object:", message_object[
                            //     'last_message']['chat_id']);
                            document.getElementById('last_message_time_' + message_object[
                                    'last_message']['chat_id']).innerText = hours_last +
                                ":" + minutes_last;



                            if (last_messages[element]['addressee']['id'] != this_user_id &&
                                last_messages[element]['last_message']['isRead'] == true
                            ) {
                                document.getElementById(
                                    'read_receipts_' + element).innerText = '✓✓';
                            }
                            if (last_messages[element]['addressee']['id'] != this_user_id &&
                                last_messages[element]['last_message']['isRead'] == false
                            ) {
                                document.getElementById(
                                    'read_receipts_' + element).innerText = '✓';
                            }
                            if (last_messages[element]['addressee']['id'] ==
                                last_messages[element]['last_message']['sender_id']) {
                                document.getElementById(
                                    'read_receipts_' + element).innerText = '✓✓';
                            }
                            key_string = decryptMessage(message_object['last_message'][
                                    'key_string'
                                ],
                                message_object['addressee']['private_key']);
                            message_result = '';
                            // console.log('2131: ', message_object['last_message'][
                            //     'type_message'
                            // ]);
                            if (message_object['last_message'][
                                    'type_message'
                                ] == 'file') {
                                document.getElementById(
                                        'last_message_' + element).innerText =
                                    'Файл';
                            }
                            if (message_object['last_message'][
                                    'type_message'
                                ] == 'text') {
                                message_json = JSON.parse(message_object['last_message']
                                    ['message']);

                                message_json.forEach(message_text => {
                                    message_result += CryptoJS.AES.decrypt(
                                        message_text,
                                        key_string).toString(CryptoJS.enc.Utf8);
                                })
                                if (message_result.length > 34) {
                                    document.getElementById('last_message_' + element)
                                        .innerText =
                                        message_result.slice(0, 34) + '...';
                                } else {
                                    document.getElementById(
                                            'last_message_' + element).innerText =
                                        message_result;
                                }
                                var message_result = '';
                            }
                            // if()

                        });

                        unread_chats_keys.forEach(element => {
                            document.getElementById('unread_messages_counter_' + element)
                                .style
                                .display =
                                'flex';
                            document.getElementById('unread_messages_counter_' + element)
                                .innerText =
                                unread_chats[element];
                        });
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText); // Обработка ошибки
            }
        });

        function splitString(input) {
            const maxLength = 100;
            const result = [];
            for (let i = 0; i < input.length; i += maxLength) {
                result.push(input.slice(i, i + maxLength));
            }
            return result;
        }

        document.getElementById('send_message').addEventListener('click', function() {
            send_message()
        });
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault(); // Блокируем стандартное поведение
                send_message(); // Выполняем определенное действие
            }
        });
    </script>
</x-app-layout>

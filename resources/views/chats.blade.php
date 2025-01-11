<x-app-layout>
    {{-- <style></style> --}}
    {{-- <link href="{{ asset('css/app.css') }}" rel="stylesheet"> --}}
    <script src="{{ asset('js/BigInteger.js') }}"></script>
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/jsencrypt.min.js') }}"></script>
    <script src="{{ asset('js/RSA.min.js') }}"></script>
    <script src="{{ asset('js/forge.min.js') }}"></script>
    <script src="{{ asset('js/crypto-js.min.js') }}"></script>
    <script src="{{ asset('js/func.js') }}" defe></script>


    <script>
        var selected_chat = null;
        const token = 'YOUR_AUTH_TOKEN';
        var last_messages = {};
        const socket = new WebSocket('ws://localhost:8888');
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
        // document.addEventListener('contextmenu', function(event) {
        //     event.preventDefault();
        // });

        // function getQueryParams() {
        //     const params = {};
        //     const queryString = window.location.search.substring(1);
        //     const regex = /([^&=]+)=([^&]*)/g;
        //     let m;

        //     while (m = regex.exec(queryString)) {
        //         params[decodeURIComponent(m[1])] = decodeURIComponent(m[2]);
        //     }

        //     return params;
        // }

        // // Использование функции
        // const queryParams = getQueryParams();
        // console.log(queryParams);

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
                            // console.log('Должно быть зашифровано: ',
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
                                        chat_tab_div = `<div class="message" id="` +
                                            guid +
                                            `">
                                                    <div class="my_message_with_media my_message" id="my_message_` +
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

                                                        <div class='my_message_read_state no_select'><p id='my_message_read_state_` +
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
                                chat_tab_div = `<div class="message" id="` +
                                    guid +
                                    `">
                                                    <div class="my_message">
                                                        <div class='my_message_data'>
                                                            <div class='my_message_text'><p class='message_p'>` +
                                    document.getElementById('message_input').value +
                                    `
                                                        </p></div>

                                                        <div class="message_time no_select" ><p id="my_message_time_` +
                                    guid +
                                    `">` + hours + ':' + formattedMinutes +
                                    `</p></div>

                                                        <div class='my_message_read_state no_select'><p id='my_message_read_state_` +
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
                got_chat_id = JSON.parse(event.data)['chat_id'];
                // console.log(JSON.parse(event.data));
                // message_data
                if (JSON.parse(event.data)['message_data'] == 'text') {
                    // console.log(111111);
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
                        // console.log("В откртый чат пришло собщение!!!");
                        // console.log(JSON.parse(event.data))

                        if (JSON.parse(event.data)['chat_id'] != favorite_id_chat) {
                            console.log(JSON.parse(event.data));

                            if (JSON.parse(event.data)['chat_id'] in unread_chats) {
                                unread_chats[JSON.parse(event.data)['chat_id']] += 1;
                            } else {
                                unread_chats[JSON.parse(event.data)['chat_id']] = 1;
                            }
                            document.getElementById('unread_messages_counter_' + JSON.parse(event.data)['chat_id'])
                                .style.display = 'flex';
                            // console.log(document.getElementById('unread_messages_counter_' + JSON.parse(event.data)[
                            //     'chat_id']).style.display);
                            document.getElementById('unread_messages_counter_' + JSON.parse(event.data)['chat_id'])
                                .innerText = unread_chats[JSON.parse(event.data)['chat_id']];
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

                            chat_tab_div = `<div class="message" id="` + JSON.parse(event.data)[
                                    'message_id_new'
                                ] + `">
                                        <div class="recived_message">
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
                            chat_tab_div =
                                `<div class="message border_debug" id="` +
                                JSON.parse(event.data)[
                                    'message_id_new'
                                ] +
                                `">
                                                    <div class="my_message">
                                                        <div class='my_message_data'>
                                                            <div class='my_message_text'><p class='message_p'>` +
                                document.getElementById('message_input').value +
                                `
                                                        </p></div>

                                                        <div class="message_time no_select" ><p id="my_message_time_` +
                                JSON.parse(event.data)[
                                    'message_id_new'
                                ] +
                                `">` + hours + ':' + minutes +
                                `</p></div>

                                                        <div class='my_message_read_state no_select'><p id='my_message_read_state_` +
                                JSON.parse(event.data)[
                                    'message_id_new'
                                ] + `'>` + ("✓".repeat(2)) + `</p></div></div>

                                                    </div>
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
                if (JSON.parse(event.data)['message_data'] == 'file') {
                    // #TODOdecrypted
                    if (got_chat_id == selected_chat) {
                        // console.log('Дешифруеем как фото СОБЕСЕДНИКА!!!');
                        document.getElementById('last_message_' + got_chat_id).innerText = "Файл";
                        console.log('Структура данных', JSON.parse(event.data));
                        // key_string = decryptMessage(JSON.parse(event.data)['key_string'], this_user[
                        //     'private_key']);
                        // console.log(JSON.parse(event.data)['message_url']);
                        key_string = decrypted;
                        const url = 'http://localhost:8000' + JSON.parse(event.data)['message_url'];
                        // console.log("🚀 ~ returnfunction ~ url:", url);
                        encryptedImg = ''
                        // console.log("JSON.parse(event.data)['message_id']: ", JSON.parse(event.data)['message_id']);
                        message_class_div = `<div class="message border_debug" id="` +
                            JSON.parse(event.data)['message_id'] +
                            `"> </div>`;
                        // console.log("🚀 ~ returnfunction ~ message_class_div:",
                        //     message_class_div);
                        $('#messages_all').append(message_class_div);
                        // console.log(document.getElementById(JSON.parse(event.data)['message_id']));
                        // из за асинхронного процесса следующие сообщения грузятся не дожидаясь загрузки прошлых,
                        // в частоности фото, которые дешифруются дольше, поэтому оставим под них пустые места <div class='message'></div>
                        function fetchImage(url) {
                            fetch(url)
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error('Сеть не в порядке: ' +
                                            response
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
                                    //     encryptedImg_array[0]);
                                    encryptedlabel_array = JSON.parse(event.data)[
                                        'label'];
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
                                    encryptedImg_array.forEach(element => {
                                        encryptedImg += element;
                                    });
                                    decryptedImg_bytes = CryptoJS.AES.decrypt(
                                        encryptedImg, key_string);
                                    decryptedImg = decryptedImg_bytes.toString(
                                        CryptoJS.enc.Utf8);
                                    decryptedImageBlob = dataURLtoBlob(
                                        decryptedImg);
                                    decryptedImageURL = URL.createObjectURL(
                                        decryptedImageBlob);
                                    // console.log(
                                    //     "🚀 ~ returnfunction ~ decryptedImageURL:",
                                    //     decryptedImageURL);
                                    const now = new Date(JSON.parse(event.data)['created_at']);
                                    const hours = now.getHours();
                                    const minutes = now.getMinutes();
                                    // #TODO
                                    chat_tab_div =
                                        `
                                                <div class="recived_message_with_media recived_message " id="recived_message_` +
                                        JSON.parse(event.data)['message_id'] +
                                        `">

                                                    <div class='my_message_data_with_media'><div class= 'message_media_photo'><img id="media_message_` +
                                        JSON.parse(event.data)['message_id'] +
                                        `" src='` + decryptedImageURL +
                                        `'></div>
                                                        <div class='my_message_text_data_with_media'><div class='recived_message_text'><p class='message_p label '>` +
                                        decryptedlabel +
                                        `
                                                    </p></div>

                                                    <div class="message_time no_select" ><p id="recived_message_time_` +
                                        JSON.parse(event.data)['message_id'] +
                                        `">` + hours + ':' + minutes +
                                        `</p></div>

                                                    </div>
                                                    </div>

                                                </div>
                                                </div>

                                            `;
                                    $('#' + JSON.parse(event.data)['message_id']).append(
                                        chat_tab_div);

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
                                                sendMessage(JSON.stringify(
                                                    data));
                                                observer.unobserve(entry.target);
                                            }
                                        });
                                    }
                                    const observer = new IntersectionObserver(
                                        handleIntersection);
                                    const targetElement = document.getElementById(
                                        JSON.parse(event.data)[
                                            'message_id']);
                                    console.log("🚀 ~ fetchImage ~ targetElement:", targetElement)
                                    observer.observe(targetElement);
                                    // }
                                    document.getElementById('recived_message_' +
                                            JSON.parse(event.data)['message_id']).style.padding =
                                        '0';
                                    // break

                                })

                                .catch(error => {
                                    console.error('Ошибка:', error);
                                });
                        }
                        fetchImage(url);
                        // controller.abort();

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
                //получаем id сообщения вместо временного guid-а
                if (JSON.parse(event.data)['chat_id'] == selected_chat) {
                    if (document.getElementById('media_message_' + JSON.parse(event.data)['guid'])) {
                        document.getElementById('media_message_' + JSON.parse(event.data)['guid']).id =
                            'media_message_' + JSON.parse(event.data)['message_id_new'];
                    }
                    document.getElementById('my_message_time_' + JSON.parse(event.data)['guid']).id =
                        'my_message_time_' + JSON.parse(event.data)['message_id_new'];
                    document.getElementById(JSON.parse(event.data)['guid']).id = JSON.parse(event.data)[
                        'message_id_new'];
                    document.getElementById('my_message_read_state_' + JSON.parse(event.data)['guid']).id =
                        'my_message_read_state_' + JSON.parse(event.data)['message_id_new'];
                }

            }
            // #TODOДелаем обновление статуса прочтения сообщения в формате онлайн
            // if (JSON.parse(event.data)['type_message'] == 'read_sate_update') {

            //     // JSON.parse(event.data)['chat_id']
            //     if (selected_chat == JSON.parse(event.data)['chat_id']) {
            //         console.log('Прочитано: ', JSON.parse(event.data)[
            //             'message_id']);
            //         document.getElementById('my_message_read_state_' + JSON.parse(event.data)[
            //             'message_id']).innerText = '✓✓';
            //     }
            // }
            const eventData = JSON.parse(event.data);
            const {
                type_message,
                chat_id,
                message_id
            } = eventData;

            if (type_message == 'read_sate_update') {
                if (selected_chat == chat_id) {
                    console.log('Прочитано: ', message_id);
                    document.getElementById('my_message_read_state_' + message_id).innerText = '✓✓';
                }
            }
            // is_typing #TODOделаем обновление статуса того, что пользователь печатает

            if (JSON.parse(event.data)['type'] == 'is_typing') {
                document.getElementById('last_message_' + JSON.parse(event.data)['chat_id'])
                    .innerText = 'Печатает...';
                if (selected_chat == JSON.parse(event.data)['chat_id']) {
                    document.getElementById('is_typing_' + JSON.parse(event.data)['user_id']).innerText = 'Печатает...';
                    console.log('Открытый чат пользователь печатает');
                } else {
                    console.log('Какой-то пользователь печатает');
                }
            }
            if (JSON.parse(event.data)['type'] == 'isnt_typing') {
                document.getElementById('last_message_' + JSON.parse(event.data)['chat_id'])
                    .innerText = last_messages[JSON.parse(event.data)['chat_id']];
                if (selected_chat == JSON.parse(event.data)['chat_id']) {
                    document.getElementById('is_typing_' + JSON.parse(event.data)['user_id']).innerText = '';
                    console.log('Открытый чат пользователь перестал печатать');
                } else {
                    console.log('Какой-то пользователь перестал печатает');
                }
            }
        };


        socket.onclose = function(event) {
            console.log('Соединение закрыто');
            alert('Соединение закрыто');
        };

        function sendMessage(message) {
            socket.send(message);
        }

        function is_typing() {
            if (document.getElementById('message_input').value.trim()) {
                console.log('typing');
                data = {
                    'chat_id': selected_chat,
                    'user_id': this_user_id,
                    'type_message': 'typing',
                }
                sendMessage(JSON.stringify(data));
            } else {
                console.log('isnt_typing');
                data = {
                    'chat_id': selected_chat,
                    'user_id': this_user_id,
                    'type_message': 'isnt_typing',
                }
                sendMessage(JSON.stringify(data));
            }
        }

        function select_chat(user_id, chat_id) {

            return function() {
                user_id_opened_chat = user_id;
                $('#messages_all').empty();
                // #TODOделаем в режиме онлайн печатает пользователь пользователь в этом чате или нет через websocket
                message_input_elem = document.getElementById('message_input');
                message_input_elem.addEventListener('input', is_typing);
                console.log("🚀 ~ returnfunction ~ message_input_elem:", message_input_elem)
                document.getElementById('message_input').value = '';
                document.getElementById('message_input').focus();
                console.log(selected_chat)
                // background-color: rgb(0 77 225 / 30%);
                if (selected_chat != null) {
                    document.querySelector('.chat_' + selected_chat).style.backgroundColor = 'rgb(0,0,0,0)'
                }
                // console.log(selected_chat);
                selected_chat = chat_id;
                document.querySelector('.chat_' + selected_chat).style.backgroundColor = 'rgb(0 78 227 / 24%)'
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
                    },
                    success: function(response) {
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
                            const date = new Date(element['created_at']);

                            // Получаем часы и минуты
                            const hours_last = date.getHours(); // Часы
                            const minutes_last = date.getMinutes(); // Минуты
                            sender_id = element['sender_id'];
                            message_result = '';
                            if (sender_id == this_user_id) {
                                key_string = decryptMessage(element['key_string'], second_user[
                                    'private_key']);
                                // console.log('element: ', element);
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
                                    chat_tab_div = `<div class="message " id="` +
                                        element[
                                            'message_id'] +
                                        `">
                                                        <div class="my_message " id="my_message_` +
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
                                        `">` + hours_last + ':' + minutes_last +
                                        `</p></div>

                                                            <div class='my_message_read_state  no_select'><p id='my_message_read_state_` +
                                        element['message_id'] + `'>` + ("✓".repeat((parseInt(
                                            element[
                                                'isRead']) + 1))) + `</p></div>
                                                            </div>
                                                        </div>
                                                        </div>
                                                    </div>
                                                    `;
                                    console.log('isread: ', element[
                                        'isRead']);
                                    $('#messages_all').append(chat_tab_div);
                                }
                                if (element['type_message'] == 'file') {
                                    key_string = decryptMessage(element['key_string'], second_user[
                                        'private_key']);
                                    const url = 'http://localhost:8000' + element['message'];
                                    encryptedImg = ''
                                    message_class_div = `<div class="message" id="` +
                                        element['message_id'] +
                                        `"> </div>`;
                                    $('#messages_all').append(message_class_div);
                                    // из за асинхронного процесса следующие сообщения грузятся не дожидаясь загрузки прошлых,
                                    // в частоности фото, которые дешифруются дольше, поэтому оставим под них пустые места <div class='message'></div>
                                    function fetchImage(url) {
                                        fetch(url)
                                            .then(response => {
                                                if (!response.ok) {
                                                    throw new Error('Сеть не в порядке: ' +
                                                        response
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
                                                //     encryptedImg_array[0]);
                                                encryptedlabel_array = JSON.parse(element[
                                                    'label']);
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
                                                encryptedImg_array.forEach(element => {
                                                    encryptedImg += element;
                                                });
                                                decryptedImg_bytes = CryptoJS.AES.decrypt(
                                                    encryptedImg, key_string);
                                                decryptedImg = decryptedImg_bytes.toString(
                                                    CryptoJS.enc.Utf8);
                                                decryptedImageBlob = dataURLtoBlob(
                                                    decryptedImg);
                                                decryptedImageURL = URL.createObjectURL(
                                                    decryptedImageBlob);
                                                console.log(
                                                    "🚀 ~ returnfunction ~ decryptedImageURL:",
                                                    decryptedImageURL);
                                                // const now = new Date();
                                                // const hours = now.getHours();
                                                // const minutes = now.getMinutes();
                                                chat_tab_div =
                                                    `
                                                    <div class="my_message_with_media my_message " id="my_message_` +
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
                                                    `">` + hours_last + ':' + minutes_last +
                                                    `</p></div>

                                                        <div class='my_message_read_state no_select'><p id='my_message_read_state_` +
                                                    element['message_id'] + `'>` + ("✓".repeat(
                                                        parseInt(element['isRead']) + 1)) + `</p></div</div>
                                                        </div>

                                                    </div>
                                                    </div>

                                                `;
                                                $('#' + element['message_id'] + '').append(
                                                    chat_tab_div);
                                                // break

                                            })
                                            .catch(error => {
                                                console.error('Ошибка:', error);
                                            });
                                    }
                                    fetchImage(url);
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
                                            console.log('Прочитано у получателя:', element[
                                                'message_id'
                                            ]);
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
                                    chat_tab_div = `<div class="message" id="` +
                                        element[
                                            'message_id'] +
                                        `" >
                                                        <div class="recived_message">
                                                            <div class='recived_message_text ' ><p class='message_p'>` +
                                        message_result +
                                        `
                                                            </p></div>
                                                            <div class="message_time no_select" ><p>` + hours_last +
                                        ':' + minutes_last +
                                        `</p></div>
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
                                if (element['type_message'] == 'file') {
                                    // console.log('Дешифруеем как фото СОБЕСЕДНИКА!!!');
                                    key_string = decryptMessage(element['key_string'], this_user[
                                        'private_key']);
                                    const url = 'http://localhost:8000' + element['message'];
                                    // console.log("🚀 ~ returnfunction ~ url:", url);
                                    encryptedImg = '';
                                    is_this_message_read = parseInt(element['isRead']);
                                    // console.log("🚀 ~ returnfunction ~ is_this_message_read_" +
                                    //     element['message_id'] + ":",
                                    //     is_this_message_read);
                                    // console.log("element['message_id']: ", element['message_id']);
                                    message_class_div = `<div class="message" id="` +
                                        element['message_id'] +
                                        `"> </div>`;
                                    // console.log("🚀 ~ returnfunction ~ message_class_div:",
                                    //     message_class_div);
                                    $('#messages_all').append(message_class_div);
                                    // console.log(document.getElementById(element['message_id']));
                                    // из за асинхронного процесса следующие сообщения грузятся не дожидаясь загрузки прошлых,
                                    // в частоности фото, которые дешифруются дольше, поэтому оставим под них пустые места <div class='message'></div>
                                    function fetchImage(url) {
                                        fetch(url)
                                            .then(response => {
                                                if (!response.ok) {
                                                    throw new Error('Сеть не в порядке: ' +
                                                        response
                                                        .statusText);
                                                }
                                                return response.text(); // Получаем текст
                                            })
                                            .then(data => {
                                                encryptedImg = '';

                                                // #TODOсделать дешифровку сообщения с фото
                                                // Обрабатываем данные
                                                encryptedImg_array = JSON.parse(data);
                                                console.log(
                                                    "🚀 ~ returnfunction ~ encryptedImg_array:",
                                                    encryptedImg_array[0]);
                                                encryptedlabel_array = JSON.parse(element[
                                                    'label']);
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
                                                encryptedImg_array.forEach(element => {
                                                    encryptedImg += element;
                                                });
                                                decryptedImg_bytes = CryptoJS.AES.decrypt(
                                                    encryptedImg, key_string);
                                                decryptedImg = decryptedImg_bytes.toString(
                                                    CryptoJS.enc.Utf8);
                                                decryptedImageBlob = dataURLtoBlob(
                                                    decryptedImg);
                                                decryptedImageURL = URL.createObjectURL(
                                                    decryptedImageBlob);
                                                console.log(
                                                    "🚀 ~ returnfunction ~ decryptedImageURL:",
                                                    decryptedImageURL);
                                                // const now = new Date();
                                                // const hours = now.getHours();
                                                // const minutes = now.getMinutes();
                                                // #TODO
                                                chat_tab_div =
                                                    `
                                                    <div class="recived_message_with_media recived_message" id="recived_message_` +
                                                    element['message_id'] +
                                                    `">

                                                        <div class='my_message_data_with_media'><div class= 'message_media_photo'><img id="media_message_` +
                                                    element['message_id'] +
                                                    `" src='` + decryptedImageURL +
                                                    `'></div>
                                                            <div class='my_message_text_data_with_media'><div class='recived_message_text'><p class='message_p label '>` +
                                                    decryptedlabel +
                                                    `
                                                        </p></div>

                                                        <div class="message_time no_select" ><p id="recived_message_time_` +
                                                    element['message_id'] +
                                                    `">` + hours_last + ':' + minutes_last +
                                                    `</p></div>

                                                        </div>
                                                        </div>

                                                    </div>
                                                    </div>

                                                `;
                                                $('#' + element['message_id'] + '').append(
                                                    chat_tab_div);
                                                if (element['isRead'] == 0) {
                                                    const observer = new IntersectionObserver(
                                                        handleIntersection);
                                                    const targetElement = document
                                                        .getElementById(
                                                            element[
                                                                'message_id']);
                                                    // console.log("🚀 ~ returnfunction ~ targetElement:",
                                                    //     targetElement);
                                                    observer.observe(targetElement);
                                                }
                                                document.getElementById('recived_message_' +
                                                        element['message_id']).style.padding =
                                                    '0';
                                                // break

                                            })
                                            .catch(error => {
                                                console.error('Ошибка:', error);
                                            });
                                    }
                                    fetchImage(url);
                                    // controller.abort();

                                }

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
                        // console.log('response', response['user']['isOnline']);
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
                                `<div class="user_info_header_foto" id=` +
                                response
                                .user
                                .id +
                                `><img src='.` + response.user
                                .avatar + `'></div>
    <div class="user_info_header_without_foto" id='user_info_header_without_foto'>
        <div class="user_info_header_name"> <i>` + response.user.name + ' ' +
                                response.user.lastname + ' @' + response.user.username + `<i>
        </div>
                <div class="user_info_head">
                    <div class="user_info_header_is_online" id="user_info_header_is_online_` +
                                user_id_chat +
                                `">` + user_online + `
                    </div>
                    <div class="user_info_header_is_typing" id="is_typing_` + response
                                .user
                                .id + `">
                    </div>
                </div>

    </div>
    <div class= 'heeader_buttons'>
        <div class='call_button' id='start_call_button'>✆</div>
    </div>

`;
                            $('#user_info_header').append(user_chats_header);
                            document.getElementById('start_call_button').addEventListener('click',
                                function() {
                                    resp = confirm('Начать звонок?');
                                    var isDragging = false;
                                    var offset = {
                                        x: 0,
                                        y: 0
                                    };
                                    if (resp) {
                                        console.log('Звонок начат');
                                        div = `<div class="call_window" id="call_window">
                                                    <div class='border_debug call_div_info'>
                                                        <div class = 'call_info_header border_debug'>
                                                            <div class= 'call_header_avatar border_debug'><img src='` +
                                            response.user.avatar + `'></div>
                                                            <div class='call_header_name border_debug'>Хаймусов Ярослав @YarKhim</div>
                                                        </div>
                                                        <div class='call_info_body border_debug'>
                                                            <div class='call_state border_debug'>Ждём ответа...</div>
                                                        </div>
                                                        <div class='body'>
                                                            <div class='call_video'>
                                                                <video id="localVideo"  autoplay muted></video>
                                                                <video id="remoteVideo" autoplay></video>
                                                            </div>
                                                            <div class='call_options'><input type='submit'></div>

                                                        </div>

                                                    </div>
                                                </div>`;
                                        $('body').append(div);
                                        let mediaStream;
                                        const myVideo = document.getElementById('localVideo');
                                        if (navigator.mediaDevices && navigator.mediaDevices
                                            .getUserMedia) {
                                            async function startMedia() {
                                                try {
                                                    // Запрашиваем доступ к камере и микрофону
                                                    mediaStream = await navigator.mediaDevices
                                                        .getUserMedia({
                                                            video: true,
                                                            audio: true
                                                        });
                                                    myVideo.srcObject =
                                                        mediaStream; // Устанавливаем поток в элемент video
                                                    const iframes = document.querySelectorAll(
                                                        'iframe');

                                                    iframes.forEach(iframe => {
                                                        iframe.style.pointerEvents =
                                                            'none'; // Запретить взаимодействие
                                                        iframe.src =
                                                            ''; // Очистить источник, чтобы видео не загружалось
                                                    });
                                                } catch (error) {
                                                    console.error(
                                                        'Ошибка доступа к медиа-устройствам:',
                                                        error);
                                                }
                                            }
                                            startMedia();
                                        } else {
                                            alert(
                                                'Ваш браузер не поддерживает доступ к камере и микрофону.'
                                            );
                                        }

                                    }
                                })
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
                                `<div class="user_info_header_foto" id=` +
                                response
                                .user
                                .id +
                                `><img src='/storage/EKzXqTT0PRHe6OpYr5iN0VF14kAhm9qpaG48iZrd.png'></div>
    <div class="user_info_header_without_foto" id='user_info_header_without_foto'>
        <div class="user_info_header_name"> <i>` + `Избранное` + ' ' + `<i>
            </div>
            <div class="user_info_header_is_online"></div>
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
                    <div class="main_plane">
                        <div class="all_chats_list" id='all_chats_list'>
                        </div>
                        <div class="messages_plane" id="messages_plane">
                            <div class=" user_info_header" id="user_info_header">
                            </div>
                            <div class="messages" id="messages_all">
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

                            <div class="input">
                                <form class="message_input_form">
                                    @csrf
                                    <input type="text" autocomplete="off" class="message_input" id='message_input'>

                                    <label for="imageInput" class="custom-file-upload">
                                        📎
                                    </label>
                                    <input type="file" id="imageInput">
                                    <button type="button" id="send_message">Send</button>
                                    <div id="encryptedText"></div>

                                    <div id="decryptedImage"></div>
                                </form>
                                <script>
                                    const imageInput = document.getElementById('imageInput');
                                    imageInput.addEventListener('change', (event) => {
                                        message_send_type = 'file';
                                        selectedImage = event.target.files[0];
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
        get_chat_flag_finished = false;
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
                        chat_tab_div = `<div class="chat_tab chat_` + response['chat_id'][i][
                                'id'
                            ] +
                            `" id=` + response.сhats[i]['id'] + `>
                                <div class="chat_foto"><img src=` + response.сhats[i][
                                'avatar'
                            ] + `></div>
                                <div class=" right_side_chat_tab">
                                    <div class=" chat_info">
                                        <div class=" chat_name">` + response.сhats[i]['name'] +
                            `<div id='online_in_chats_list_` + user_2 + `' class = 'online_in_chats_list'>` +
                            online + `</div></div>
                                        <div class=" read_receipts" id="read_receipts_` + response[
                                'chat_id'][i][
                                'id'
                            ] +
                            `"></div>
                                        <div class=" last_message_time" id="last_message_time_` + response[
                                'chat_id'][i]['id'] +
                            `"></div>

                                    </div>
                                    <div class=' about_messaegs' ><div class=" last_message" id='` +
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
                        chat_tab_div = `<div class="chat_tab chat_` + response['chat_id'][i][
                                'id'
                            ] +
                            `" id=` + response.сhats[i]['id'] + `>
                                <div class=" chat_foto"><img src='/storage/EKzXqTT0PRHe6OpYr5iN0VF14kAhm9qpaG48iZrd.png'></div>
                                <div class=" right_side_chat_tab">
                                    <div class=" chat_info">
                                        <div class=" chat_name">Избранное</div>
                                        <div class=" read_receipts" id="read_receipts_` + response[
                                'chat_id'][i][
                                'id'
                            ] + `"></div>
                                        <div class=" last_message_time" id="last_message_time_` + response[
                                'chat_id'][i]['id'] +
                            `"></div>

                                    </div>
                                    <div class=" last_message" id='` + id + `'></div>
                                </div>

                            </div>`;
                        $('#all_chats_list').append(chat_tab_div);
                        document.getElementById(response.сhats[i]['id']).addEventListener('click', select_chat(
                            response.сhats[i]['id'], response['chat_id'][i]['id']));
                    }

                }
                // #TODO

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
                                last_messages[message_object[
                                    'last_message']['chat_id']] = message_result;
                                console.log(last_messages);
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

                user_open_id  = window.location.search.replace('?new_chat_user_id=','');
                    console.log("🚀 ~ user_open_id:", user_open_id)
                    $(document).ready(function(){
                        if(user_open_id != ''){
                        console.log(String( user_open_id));
                        // console.log($('#'+String( user_open_id)));
                        new_chat_tab = document.getElementById(user_open_id);
                        // console.log("🚀 ~ $ ~ new_chat_tab:", new_chat_tab)
                        // $('#'+user_open_id).classList.add();
                        // setTimeout(() => {
                        //     console.log();
                        // }, 1000);
                        new_chat_tab.classList.add('new_post');
                        setTimeout(() => {
                            new_chat_tab.classList.remove('new_post');
                        }, 5000);
                    }
                })
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText); // Обработка ошибки
            },
            // complete: function(){

            // }
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
        // console.log(window.location.search.replace('?new_chat_user_id=',''));

        // console.log(document.getElementById('57'));
        // $(document).ready(function() {
        //     user_open_id  = window.location.search.replace('?new_chat_user_id=','');
        //     if(user_open_id != ''){
        //         console.log(String( user_open_id));
        //         console.log($('#'+String( user_open_id)));
        //         // new_chat_tab = document.getElementById(toString( user_open_id));
        //         // console.log("🚀 ~ $ ~ new_chat_tab:", new_chat_tab)
        //         // $('#'+user_open_id).classList.add();
        //         // new_chat_tab.classList.add('blink-background');
        //         // setTimeout(() => {
        //         //     new_chat_tab.classList.remove('blink-background');
        //         // }, 1000);
        //     }
        // });
    </script>
</x-app-layout>

<x-app-layout>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="http://peterolson.github.com/BigInteger.js/BigInteger.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsencrypt/3.0.0/jsencrypt.min.js"></script>
    <script src="https://raw.githubusercontent.com/benjaminBrownlee/RSA/master/RSA.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/forge/0.10.0/forge.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.0.0/crypto-js.min.js"></script>
    <script>
        var selected_chat = null;
        const token = 'YOUR_AUTH_TOKEN';
        const socket = new WebSocket('ws://localhost:8888');
        let unreadChatsData = null;
        this_user_id = null;
        favorite_id_chat = null;
        unread_chats = {};

        document.addEventListener('contextmenu', function(event) {
            event.preventDefault();
        });

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
        // $.ajax({
        //     url: '/unread_chats',
        //     method: 'GET',
        //     success: function(data) {
        //         // unreadChatsData = data; // Сохраняем данные в глобальную переменную
        //         console.log(data); // Выводим данные в консоль для проверки
        //         // Здесь вы можете обновить интерфейс пользователя с полученными данными
        //     },
        //     error: function(jqXHR, textStatus, errorThrown) {
        //         console.error('Ошибка при получении чатов:', textStatus, errorThrown);
        //     }
        // });

        // console.log(unreadChatsData)
        // unreadChatsData_keys = (Object.keys(unreadChatsData));
        // console.log(unreadChatsData_keys);
        // unreadChatsData_keys.forEach(element => {
        //     console.log(unreadChatsData[element]);
        //     unread_chats[element] = unreadChatsData[element];
        // });
        // console.log('Список непрочитаных сообщений: ', unread_chats);
        // for (i = 0; i < unreadChatsData.length; i++) {
        //     console.log(unreadChatsData[i])
        // }
        // unreadChatsData.forEach(element => {
        //     console.log(element);
        // });
        function send_message() {

            if (document.getElementById('message_input').value.trim()) {
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

                        if (document.getElementById('message_input').value.length < 450) {
                            message[0] = document.getElementById('message_input').value;
                        } else {
                            message = splitString(document.getElementById('message_input').value);
                        }
                        for (let i = 0; i < message.length; i++) {
                            encryptedMessage = CryptoJS.AES.encrypt(message[i], secretKey).toString();
                            message[i] = (encryptedMessage);
                            encryptedMessage = null;
                        }
                        const addressee = document.querySelector('.user_info_header_foto').id;
                        let data = {
                            message: message,
                            addressee: addressee,
                            encrypted_key: encryptMessage(secretKey, publicKeyPem),
                            chat_id: response['chat_id'],
                            privateKey: privateKeyPem,
                        };
                        // console.log(data);

                        // console.log(data.message);
                        sendMessage(JSON.stringify(data));
                        all_message = document.getElementById('message_input').value;
                        if (addressee_id != this_user_id) {
                            chat_tab_div = `<div class="message border_debug">
                                            <div class="my_message border_debug">
                                                <p>` + all_message + `
                                                </p>
                                            </div>
                                        </div>`;
                            $('#messages_all').append(chat_tab_div);
                        }

                        document.getElementById('message_input').value = null;
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
            const privateKey = forge.pki.privateKeyFromPem(JSON.parse(event.data)['privateKey']);
            const decodedMessage = forge.util.decode64(JSON.parse(event.data)['encrypted_key']);
            const decrypted = privateKey.decrypt(decodedMessage, 'RSA-OAEP');
            var message_result = '';



            got_chat_id = JSON.parse(event.data)['chat_id'];
            // console.log(document.getElementById('last_message_' + got_chat_id));
            // console.log(JSON.parse(event.data)['chat_id'])
            // console.log(favorite_id_chat);

            // console.log(select_chat);
            // console.log('last_message_'+got_chat_id);
            for (i = 0; i < JSON.parse(event.data)['message'].length; i++) {
                message_result += CryptoJS.AES.decrypt(JSON.parse(event.data)['message'][i], decrypted).toString(
                    CryptoJS
                    .enc.Utf8);
            }
            document.getElementById('last_message_' + got_chat_id).innerText = message_result;
            // if (select_chat != got_chat_id) {

            // }

            if (got_chat_id == selected_chat) {
                console.log("В откртый чат пришло собщение!!!");


                if (JSON.parse(event.data)['chat_id'] != favorite_id_chat) {

                    chat_tab_div = `<div class="message border_debug">
            <div class="recived_message border_debug">
                <p>` + message_result + `
                </p>
            </div>
        </div>
        `;
                } else {
                    chat_tab_div = `<div class="message border_debug">
            <div class="my_message border_debug">
                <p>` + message_result + `
                </p>
            </div>
        </div>
        `;
                }
                // document.getElementById('last_message_' + got_chat_id).innerText = message_result;

                $('#messages_all').append(chat_tab_div);
                // document.addEventListener('DOMContentLoaded', function() {
                message_tabs = document.querySelectorAll('.message_tab');
                console.log(message_tabs);
                message_tabs.forEach(function(message_tab) {
                    message_tab.addEventListener('contextmenu', function(event) {
                        // alert(1);
                    });
                });
                // });

            } else {
                // alert(document.getElementById('unread_messages_counter_' + JSON.parse(event.data)['chat_id']).style
                //     .visibility);
                // document.getElementById('unread_messages_counter_' + JSON.parse(event.data)['chat_id']).style
                //     .visibility == 'visible';
                // alert(document.getElementById('unread_messages_counter_' + JSON.parse(event.data)['chat_id']).style
                //     .visibility);
                //     .visibility)
                if (JSON.parse(event.data)['chat_id'] in unread_chats) {
                    unread_chats[JSON.parse(event.data)['chat_id']] += 1;

                } else {
                    unread_chats[JSON.parse(event.data)['chat_id']] = 1;

                }
                document.getElementById('unread_messages_counter_' + JSON.parse(event.data)['chat_id']).style.display =
                    'flex';
                console.log(document.getElementById('unread_messages_counter_' + JSON.parse(event.data)['chat_id'])
                    .style.display);
                document.getElementById('unread_messages_counter_' + JSON.parse(event.data)['chat_id']).innerText =
                    unread_chats[JSON.parse(event.data)['chat_id']];
                // if (document.getElementById('unread_messages_counter_' + JSON.parse(event.data)['chat_id']).style.display == 'none') {
                //     document.getElementById('unread_messages_counter_' + JSON.parse(event.data)['chat_id']).style.display = 'block';
                //     document.getElementById('unread_messages_counter_' + JSON.parse(event.data)['chat_id']).innerText = unread_chats[JSON.parse(event.data)['chat_id']];
                // }
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

                // console.log("В один из чатов пришло сообщение!!!");
                // console.log(unread_chats);
            }
            // new_message_chat = document.querySelector('.chat_' + selected_chat).classList.remove('blink-background');

        };

        socket.onclose = function(event) {
            console.log('Соединение закрыто');
        };

        // Отправка сообщения на сервер
        function sendMessage(message) {
            // console.log(JSON.parse(message)['message']);

            socket.send(message);
        }

        function select_chat(user_id, chat_id) {
            return function() {
                $('#messages_all').empty();
                document.getElementById('message_input').value = '';
                document.getElementById('message_input').focus();
                selected_chat = chat_id;
                // console.log(selected_chat)
                document.getElementById('messages_plane').style.visibility = 'visible';

                let data = {
                    id: user_id,
                };
                $.ajax({
                    url: '/get_user', // URL вашего маршрута
                    method: 'GET', // Метод запроса (GET или POST)
                    data: data,
                    success: function(response) {
                        user_id_chat = response['user']['id']
                        $('#user_info_header').empty();
                        $.ajax({

                            url: '/get_messages_from_chat', // URL вашего маршрута
                            method: 'GET', // Метод запроса (GET или POST)
                            data: data,
                            success: function(response) {
                                console.log(response.success)
                            }
                        });
                        const all_chats_list = document.getElementById('user_info_header');
                        if (user_id_chat != parseInt(this_user_id)) {
                            user_chats_header =
                                `<div class="user_info_header_foto border_debug" id=` + response
                                .user
                                .id +
                                `><img src=` + response.user
                                .avatar + `></div>
    <div class="border_debug user_info_header_without_foto" id='user_info_header_without_foto'>
        <div class="user_info_header_name border_debug"> <i>` + response.user.name + ' ' +
                                response.user.lastname + ' @' + response.user.username + `<i>
            </div>
            <div class="user_info_header_is_online border_debug"></div>
        </div>`;
                        } else {
                            favorite_id_chat = chat_id
                            user_chats_header =
                                `<div class="user_info_header_foto border_debug" id=` + response
                                .user
                                .id +
                                `><img src=` + response.user
                                .avatar + `></div>
    <div class="border_debug user_info_header_without_foto" id='user_info_header_without_foto'>
        <div class="user_info_header_name border_debug"> <i>` + `Избранное` + ' ' + `<i>
            </div>
            <div class="user_info_header_is_online border_debug"></div>
        </div>`;
                        }

                        $('#user_info_header').append(user_chats_header);
                        document.getElementById('user_info_header_without_foto').addEventListener(
                            'click',
                            function() {
                                window.open('/user_profile?id= ' + response.user.username)
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
                            </div>

                            <div class="border_debug input">
                                <form>
                                    @csrf
                                    <input type="text" autocomplete="off" class="message_input" id='message_input'>
                                    <button type="button" id="send_message">Отправить</button>
                                </form>

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

                for (let i = 0; i < response.сhats.length; i++) {
                    console.log(response['chat_id'][i]['creator'], ' ', response['chat_id'][i]['invted'])
                    if (response['chat_id'][i]['creator'] != response['chat_id'][i]['invted']) {
                        id = 'last_message_' + response['chat_id'][i]['id'];
                        console.log(id)
                        chat_tab_div = `<div class="border_debug chat_tab chat_` + response['chat_id'][i][
                                'id'
                            ] +
                            `" id=` + response.сhats[i]['id'] + `>
                                <div class="border_debug chat_foto"><img src=` + response.сhats[i]['avatar'] + `></div>
                                <div class="border_debug right_side_chat_tab">
                                    <div class="border_debug chat_info">
                                        <div class="border_debug chat_name">` + response.сhats[i]['name'] +
                            `</div>
                                        <div class="border_debug read_receipts">&#10003;</div>
                                        <div class="border_debug last_message_time">41.23</div>

                                    </div>
                                    <div class='border_debug about_messaegs' ><div class="border_debug last_message" id='` +
                            id +
                            `'></div> <div class='unread_messages'><div class='unread_messages_counter' id='unread_messages_counter_` +
                            response['chat_id'][i][
                                'id'
                            ] + `'> 1</div></div></div>
                                </div>

                            </div>`;
                    } else {
                        this_user_id = response['chat_id'][i]['creator'];
                        chat_tab_div = `<div class="border_debug chat_tab chat_` + response['chat_id'][i][
                                'id'
                            ] +
                            `" id=` + response.сhats[i]['id'] + `>
                                <div class="border_debug chat_foto"><img src=` + response.сhats[i]['avatar'] + `></div>
                                <div class="border_debug right_side_chat_tab">
                                    <div class="border_debug chat_info">
                                        <div class="border_debug chat_name">Избранное</div>
                                        <div class="border_debug read_receipts">&#10003;</div>
                                        <div class="border_debug last_message_time">41.23</div>

                                    </div>
                                    <div class="border_debug last_message">message</div>
                                </div>

                            </div>`;
                    }
                    $('#all_chats_list').append(chat_tab_div);
                    // console.log(response['chat_id'][i]['id'])
                    document.getElementById(response.сhats[i]['id']).addEventListener('click', select_chat(
                        response.сhats[i]['id'], response['chat_id'][i]['id']));
                }
                $.ajax({
                    url: '/unread_chats', // URL вашего маршрута
                    method: 'GET', // Метод запроса (GET или POST)
                    success: function(response) {
                        // console.log(response)
                        unread_chats = response['chats_id'];
                        console.log(unread_chats);
                        unread_chats_keys = Object.keys(unread_chats);
                        unread_chats_keys.forEach(element => {
                            document.getElementById('unread_messages_counter_' + element)
                                .style
                                .display =
                                'flex';
                            document.getElementById('unread_messages_counter_' + element)
                                .innerText =
                                unread_chats[element];
                            // console.log(unreadChatsData[element]);
                            // unread_chats[element] = unreadChatsData[element];
                        });
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText); // Обработка ошибки
            }
        });

        function splitString(input) {
            const maxLength = 450;
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
        // document.getElementById('send_message').addEventListener('click', );
    </script>
</x-app-layout>

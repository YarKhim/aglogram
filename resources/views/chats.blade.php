<x-app-layout>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="http://peterolson.github.com/BigInteger.js/BigInteger.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsencrypt/3.0.0/jsencrypt.min.js"></script>
    <script src="https://raw.githubusercontent.com/benjaminBrownlee/RSA/master/RSA.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/forge/0.10.0/forge.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.0.0/crypto-js.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        var selected_chat;
        const token = 'YOUR_AUTH_TOKEN';
        const socket = new WebSocket('ws://localhost:8888');

        socket.onopen = function(event) {
            console.log('Подключено к WebSocket серверу');
        };
        socket.onmessage = function(event) {
            console.log('Сообщение от сервера:', event.data);
        };

        socket.onclose = function(event) {
            console.log('Соединение закрыто');
        };

        // Отправка сообщения на сервер
        function sendMessage(message) {
            socket.send(message);
        }

        function select_chat(user_id) {
            return function() {
                document.getElementById('messages_plane').style.visibility = 'visible';
                let data = {
                    id: user_id,
                };
                $.ajax({
                    url: '/get_user', // URL вашего маршрута
                    method: 'GET', // Метод запроса (GET или POST)
                    data: data,
                    success: function(response) {

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
                        const user_chats_header =
                            `<div class="user_info_header_foto border_debug" id=` + response.user.id +
                            `><img src=` + response.user
                            .avatar + `></div>
                                <div class="border_debug user_info_header_without_foto" id='user_info_header_without_foto'>
                                    <div class="user_info_header_name border_debug"> <i>` + response.user.name + ' ' +
                            response.user.lastname + ' @' + response.user.username + `<i>
                                    </div>
                                    <div class="user_info_header_is_online border_debug"></div>
                                </div>`;
                        $('#user_info_header').append(user_chats_header);
                        document.getElementById('user_info_header_without_foto').addEventListener('click',
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

    {{-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Чаты') }}
        </h2>
    </x-slot> --}}
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
                            {{-- <div class="border_debug chat_tab">
                                <div class="border_debug chat_foto"></div>
                                <div class="border_debug right_side_chat_tab">
                                    <div class="border_debug chat_info">
                                        <div class="border_debug chat_name">chat_name</div>
                                        <div class="border_debug read_receipts">&#10003;</div>
                                        <div class="border_debug last_message_time">41.23</div>

                                    </div>
                                    <div class="border_debug last_message">message</div>
                                </div>

                            </div> --}}

                        </div>
                        <div class="border_debug messages_plane" id="messages_plane">
                            <div class=" user_info_header border_debug" id="user_info_header">
                                {{-- <div class="user_info_header_foto border_debug"><img></div>
                                <div class="border_debug user_info_header_without_foto">
                                    <div class="user_info_header_name border_debug"> <i><i>
                                    </div>
                                    <div class="user_info_header_is_online border_debug"></div>
                                </div> --}}

                            </div>
                            <div class="border_debug messages">

                                {{-- <div class="message border_debug">
                                    <div class="recived_message border_debug">
                                        <p>Привет!Привет!Привет!Привет!Привет!Привет!Привет!Привет!Привет!Привет!Привет!Привет!Привет!Привет!Привет!Привет!Привет!Привет!Привет!Привет!Привет!Привет!Привет!Привет!Привет!Привет!Привет!Привет!Привет!Привет!
                                        </p>
                                    </div>
                                </div>
                                <div class="message border_debug">
                                    <div class="recived_message border_debug">
                                        <p>324m89898989898989898989898989898989 </p>
                                    </div>
                                </div> --}}
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
                // console.log(response.сhats); // Обработка успешного ответа
                // console.log(response.сhats)
                for (let i = 0; i < response.сhats.length; i++) {
                    chat_tab_div = `<div class="border_debug chat_tab" id=` + response.сhats[i]['id'] + `>
                                <div class="border_debug chat_foto"><img src=` + response.сhats[i]['avatar'] + `></div>
                                <div class="border_debug right_side_chat_tab">
                                    <div class="border_debug chat_info">
                                        <div class="border_debug chat_name">` + response.сhats[i]['name'] + `</div>
                                        <div class="border_debug read_receipts">&#10003;</div>
                                        <div class="border_debug last_message_time">41.23</div>

                                    </div>
                                    <div class="border_debug last_message">message</div>
                                </div>

                            </div>`;
                    // console.log(chat_tab_div);
                    $('#all_chats_list').append(chat_tab_div);
                    document.getElementById(response.сhats[i]['id']).addEventListener('click', select_chat(
                        response.сhats[i]['id']));
                }
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
            if (document.getElementById('message_input').value.trim()) {
                const encrypt = new JSEncrypt();
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


                        function encryptMessage(message) {
                            const publicKey = forge.pki.publicKeyFromPem(publicKeyPem);
                            const encrypted = publicKey.encrypt(message, 'RSA-OAEP');
                            return forge.util.encode64(encrypted); // Кодируем в base64
                        }

                        function decryptMessage(encryptedMessage) {
                            const privateKey = forge.pki.privateKeyFromPem(privateKeyPem);
                            const decodedMessage = forge.util.decode64(encryptedMessage);
                            const decrypted = privateKey.decrypt(decodedMessage, 'RSA-OAEP');
                            return decrypted;
                        }
                        // console.log('private_key: ', privateKeyPem);
                        // console.log('public_key: ', publicKeyPem);
                        // console.log('chat_key: ', secretKey);
                        // console.log('encrypted_chat_key: ', encryptMessage(secretKey));

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
                        // console.log('message', message);
                        // console.log(encryptMessage(secretKey));
                        // console.log(toString(encryptMessage(secretKey)));
                        const addressee = document.querySelector('.user_info_header_foto').id;
                        let data = {
                            message: message,
                            addressee: addressee,
                            encrypted_key: encryptMessage(secretKey),
                        };
                        // console.log(data);
                        document.getElementById('message_input').value = null;
                        // console.log(data.message);
                        sendMessage(JSON.stringify(data));
                        // $.ajax({
                        //     url: '/send_message', // URL вашего маршрута
                        //     method: 'GET', // Метод запроса (GET или POST)
                        //     data: data,
                        //     success: function(response) {},
                        //     error: function(xhr, status, error) {
                        //         console.error(xhr.responseText); // Обработка ошибки
                        //     }
                        // });

                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText); // Обработка ошибки
                    }
                });
                // console.log(a);

                //
                //                 // Функция для дешифрования
                //                 function decryptMessage(encryptedMessage) {
                //                     const privateKey = forge.pki.privateKeyFromPem(privateKeyPem);
                //                     const decodedMessage = forge.util.decode64(encryptedMessage);
                //                     const decrypted = privateKey.decrypt(decodedMessage, 'RSA-OAEP');
                //                     return decrypted;
                //                 }
                //                 encryptedMessageArray = [];
                //                 try {
                //                     if (document.getElementById('message_input').value.length < 450) {
                //                         const message = document.getElementById('message_input').value;

                //                         encryptedMessage = encryptMessage(message);
                //                         encryptedMessageArray.push(encryptedMessage);
                //                     } else {
                //                         message_array = splitString(document.getElementById('message_input').value);
                //                         message_array.forEach((element) => {
                //                             encryptedMessage = encryptMessage(element);
                //                             encryptedMessageArray.push(encryptedMessage);

                //                         });
                //                     }
                //                     console.log(encryptedMessageArray);
                //                     document.getElementById('message_input').value = null;
                //                     // Дешифруем сообщение
                //                     // const decryptedMessage = decryptMessage(encryptedMessage);
                //                     // console.log('Decrypted Message:\n', decryptedMessage);
                //                 } catch (error) {
                //                     console.error('Error:', error);
                //                 }
            }


        });
    </script>
</x-app-layout>

<x-app-layout>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="http://peterolson.github.com/BigInteger.js/BigInteger.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsencrypt/3.0.0/jsencrypt.min.js"></script>
    <script src="https://raw.githubusercontent.com/benjaminBrownlee/RSA/master/RSA.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/forge/0.10.0/forge.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.0.0/crypto-js.min.js"></script>
    <script>
        var selected_chat;

        function select_chat(user_id) {
            return function() {
                let data = {
                    id: user_id,
                };
                $.ajax({
                    url: '/get_user', // URL вашего маршрута
                    method: 'GET', // Метод запроса (GET или POST)
                    data: data,
                    success: function(response) {
                        $('#user_info_header').empty();
                        const all_chats_list = document.getElementById('user_info_header');
                        const user_chats_header =
                            `<div class="user_info_header_foto border_debug"><img src=` + response.user
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
                        <div class="border_debug messages_plane">
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
                console.log(response.сhats); // Обработка успешного ответа
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
        // console.log(CryptoJS);
        // use Defuse\ Crypto\ Crypto;
        // use Defuse\ Crypto\ Key;

        // $key = Key::createNewRandomKey();
        // $data = 'Ваши данные';

        // // Шифрование
        // $encrypted = Crypto::encrypt($data, $key);

        // // Дешифрование
        // $decrypted = Crypto::decrypt($encrypted, $key);


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
                publicKeyPem = `-----BEGIN PUBLIC KEY-----
                MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEAzHLgkIBdO9sgJhB4RZaO
                eZ+3qH1gOyp1qSoBa1gP0c27a6SaYvwC0OC7mm7BjMhT3KDM/U3pld2eUoKML9lt
                QtDFV82ktpc2DaubP9NvVC/lseO6Q4FdcIRtEvSUMhoNb/Rx6a7qPeoaAC2os7m8
                QrgstfcY0hyeOAuou8ihBT0Kyp9vf4aLUttDBsW7yZ9atPGcmDc441WLMTMj+jNx
                KlzsEbO6jynOE2D0mkDxmk2oLp1yXDz74MwkRm2TVVK2hp5qsicwb+A06ehD+5rE
                A08ZiJmzT+3wUj9LtOro5mkC/GCgPn9gYQ1M6xEGLWyvCD7LVZWEi+itGS3/LnG5
                7m6FOvitg7KAlPH4SkChgsbBJFwIPobMmOt5vXcVHKTE3rXHMQQxZQbttFoSF9zD
                xACvfjHQONG1mHXDZ1ULBaK8fLsfZRi8c94vzDNngmatrLtGSxFEFxl3eGB2GF7u
                V9mTUFefSnzpjs7oybsdrpecuv8DlclI1LDYEQ+rRFgMoAT1frAs6iWNYB4oA6Ch
                yFjEo74+Ssm/2U28IDRnB69qjbdJrfhrkZizwBBLRSr9BWWtMPZUaNuvVyD72IZp
                M1YKdoZRgsKGscuAstb40k6f9Pb7FVxYaO3Cd7lDOJR65jNQwgh5q1HYMo8VirpR
                LqFanCmuIQskJAIsP8UD/18CAwEAAQ==
                -----END PUBLIC KEY-----`;
                privateKeyPem = `-----BEGIN PRIVATE KEY-----
                MIIJQgIBADANBgkqhkiG9w0BAQEFAASCCSwwggkoAgEAAoICAQDMcuCQgF072yAm
                EHhFlo55n7eofWA7KnWpKgFrWA/RzbtrpJpi/ALQ4LuabsGMyFPcoMz9TemV3Z5S
                gowv2W1C0MVXzaS2lzYNq5s/029UL+Wx47pDgV1whG0S9JQyGg1v9HHpruo96hoA
                LaizubxCuCy19xjSHJ44C6i7yKEFPQrKn29/hotS20MGxbvJn1q08ZyYNzjjVYsx
                MyP6M3EqXOwRs7qPKc4TYPSaQPGaTagunXJcPPvgzCRGbZNVUraGnmqyJzBv4DTp
                6EP7msQDTxmImbNP7fBSP0u06ujmaQL8YKA+f2BhDUzrEQYtbK8IPstVlYSL6K0Z
                Lf8ucbnuboU6+K2DsoCU8fhKQKGCxsEkXAg+hsyY63m9dxUcpMTetccxBDFlBu20
                WhIX3MPEAK9+MdA40bWYdcNnVQsForx8ux9lGLxz3i/MM2eCZq2su0ZLEUQXGXd4
                YHYYXu5X2ZNQV59KfOmOzujJux2ul5y6/wOVyUjUsNgRD6tEWAygBPV+sCzqJY1g
                HigDoKHIWMSjvj5Kyb/ZTbwgNGcHr2qNt0mt+GuRmLPAEEtFKv0FZa0w9lRo269X
                IPvYhmkzVgp2hlGCwoaxy4Cy1vjSTp/09vsVXFho7cJ3uUM4lHrmM1DCCHmrUdgy
                jxWKulEuoVqcKa4hCyQkAiw/xQP/XwIDAQABAoICAHy4p93/MOFO7/HIolZxXkE7
                +iJDOe1eHaExCuSdOClZRDiKldREwMMEFe5EGrbzjpVNU0BDw3e1Vtwm37ZhAZJ9
                IaZKwWzSGhuE0JTDO2s0PP+kWQDNbl8xqgiiQ7W8xu3BRay1FBjpMytgr0XUzNA2
                4q+vKekjpDG0ix7jabd0YZzyXmaBgYTuVZlQDxsUp5Uyv8DsDzw/90XwWMZNk3+w
                aCL0bZ7gdeLEhvODIuBq92pYimdSnKWqY4bDWdn3N3owMg60cYwOTrlTBAX9iA4s
                tvrqKMVd9fl2u4yGscW90iB8IcV+lyEdqArOu+ICJ1T0Wb7AE7f2HYL62G1Q24dN
                Z1Z9gu3j7Q4E+M1GV5XQVym5URfWy/PKuiN8qqXl1tiFRRofNBT7snGeRcm0FoMz
                XxpsV/48jY+TlvEmvutXbqW/9JGzVNmxw459mxn3QJjriD/HrDPyE8DCLwyfUwtz
                cPZqrcovqM7255lR++CKEu9Y8bxald2xCp+xSZZsyS+yZNt4OpWTeJNNl2BbYA9J
                I0RaxI32VxeCUuRVZUfXnlDcryJhNOoO20lHg69yh8gHYIa8wXlar04sc6AHHKyp
                MYvzISgSYgBi8FouNA7yFCWqyxvbv9+uQl/8XgJgRCzhymNHgdWsPIB1y6/K8WIs
                VfOHEcyNsh3ioaBEZ6eBAoIBAQDvBCFfI8l9Y8xjbUonnjuyt3hytjJBH0Zqx1MB
                qUt7iQ/beBNU7xr8g/+AWIyPMLi9I2rfb67o99JTxj2SNl7igQg/1JjerB1/Fibi
                zq2BV74wGkgAyDh6SJ+JFZzK7G0K0i14ipw7g+oY8palaBbFOsQUFp5tOvgR6xap
                Jr2qIq63AhEjeGwZTipDEVPv4ytltq7iFK4m+XbVThAcCVyri0u8anGAQhxEjRTp
                JHDgZt0CR2fx0lR+HBPaDTjfB80s828ReQ5STDd4WVCsQkuPcY1iF3Tij1q293qG
                CbUtaDOAHDgCYsvf3Oc/QRFkw5AE5tDEvUGErxPQxuwnMa0PAoIBAQDa+fEgRyNN
                3HULzBFxbDZahJmLQJKWINHH/ZnTDEPNBdOkQXFO917dhHATVO/seZBV/87p3IOH
                LvTELb7x4YVwb4Sth5eKulKxLmP4GDT0VA+b47yuT2HzHjtV9OQWvMU+S6n7M8tx
                3OfK5YYwL3FQMjaQ927+tPU94HLiOCaKDvMz2Q93gpZKPeLoXVBeFuIlxvK/Kbw5
                N8K9aluMdVoZOCgeEGSnWGK8O5505+5MvjMmqoQ2oYtPdVdvl/+BSitxqZqZAPQs
                IuV1COw0GTsSqMI03J4Z3fweI+cAeRET/o8ceoGpcNFEog4BdC9spf7uDc4g2oP9
                AHn4E9VvTyixAoIBAGiVIhUDD7Lx7AjFVWEod26noOhwmGSuTp2CQevEK+cN/gZF
                8A+F4AUFrNJ37GjrJKp9yNGjiKEfbsSBRYeoirQJSZbEa29jWDS3eKRdUsX5opph
                pYfSFARNqqDM+CJCeSP2+zZ3UA0ql0/7NbeCaBQ8tncjvoaE+u68MtoovWyoaFJj
                cSRYGNSpwww9lMCuYdrLqJH8AofDOB/1XE8oOYhGFwmGectpIf1Mzg2FiG5oE+Rf
                YjgJlDbqCjWq+2t7emdkzQCUO5P/DXZ3fjkhePZ29SoqoVZQ65yLhO0JamFH6uDv
                eAJzQEnf2r9utTGl8Gp0pHyxYu2vvbfokoRD1DkCggEBAK3LuEwlCekvMxqk4adC
                +rTHD1KNcoqDC90N83uk/V8I49w3MKY7AWkRRx6gyDfIl/0ZpfDI2v99DjlyEV3K
                32zIpeZQer9ZeG414pQzKjxNR4IzszopuRULho6Hakx9kJML3KKKjksVyEap+uHd
                lbLP160hJVA08Xwl2yie5j+m8/HPsk3pMD1GdwRzo2i6As125I1Co6hKEF7jvjtx
                nIvtnTGXUzvak6rQKsigl+sC/ngO2BbACmCLQlVIrzq/UlHJCfGr5x/spm9IIKiM
                6ey6UkFAZJ8lJ4gIeLxQWnSjEpTIyoZgC0fM0w7mVwmFihsIi/RZWm6AZU22+Cx7
                CFECggEAWPT1lVo1nqsqqXoOFuQs59xPBVMzXvlrYJN3BCDqedOPdjw9Om4h6SS8
                bUKsZ6pw1ALhWK0QF5kdXeyxrIh1BBKsjGR6x1yRlKV4uJmlrO8rhYSqAxtZ/LRd
                GFNYMMzM6L/uR5+MeV1qgCx5kSN5Y4DoWPDmw+DkTIRob2mJ8cVF1/ONaK6mqs4K
                1TNOstmMSyyadonWVMZ6EscmZ6HW/iLq+NeuSyu6t1FGGEOJfEsIyBwEzi6jS/QN
                4RM0rpalsI0T9+OMbDC+t9TAhMbtAXYtdlkWQeuHx07FppoOge4aNn46v7UTKx8M
                SrAk18YVlepnqmvQSfoLIThfyG8hDA==
                -----END PRIVATE KEY-----`;


                // Функция для шифрования
                function encryptMessage(message) {
                    const publicKey = forge.pki.publicKeyFromPem(publicKeyPem);
                    const encrypted = publicKey.encrypt(message, 'RSA-OAEP');
                    return forge.util.encode64(encrypted); // Кодируем в base64
                }
                message = [];
                const secretKey =
                    "def0000016d3463e9a186200dd341255a98b0de71a31bccbd465b49c4062d89627591fc62c6ce1c31387979371a4305662c8298801bc6fe9e12eb82f8aee7441cd7e4aa3";
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
                const encrypted_key = encryptMessage(secretKey);
                console.log(encrypted_key);
                console.log(message);
                let data = {
                    message: message,
                };
                document.getElementById('message_input').value = null;
                // $.ajax({
                //     url: '/send_message', // URL вашего маршрута
                //     method: 'GET', // Метод запроса (GET или POST)
                //     data: data,
                //     success: function(response) {
                //         $('#user_info_header').empty();
                //         const all_chats_list = document.getElementById('user_info_header');
                //         const user_chats_header =
                //             `<div class="user_info_header_foto border_debug"><img src=` + response.user
                //             .avatar + `></div>
            //                 <div class="border_debug user_info_header_without_foto" id='user_info_header_without_foto'>
            //                     <div class="user_info_header_name border_debug"> <i>` + response.user.name + ' ' +
                //             response.user.lastname + ' @' + response.user.username + `<i>
            //                     </div>
            //                     <div class="user_info_header_is_online border_debug"></div>
            //                 </div>`;
                //         $('#user_info_header').append(user_chats_header);
                //         document.getElementById('user_info_header_without_foto').addEventListener(
                //             'click',
                //             function() {
                //                 window.open('/user_profile?id= ' + response.user.username)
                //             });
                //     },
                //     error: function(xhr, status, error) {
                //         console.error(xhr.responseText); // Обработка ошибки
                //     }
                // });
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

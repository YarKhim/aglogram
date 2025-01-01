<x-app-layout>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://raw.githubusercontent.com/benjaminBrownlee/RSA/master/RSA.min.js"></script>
    <script src="http://peterolson.github.com/BigInteger.js/BigInteger.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsencrypt/3.0.0/jsencrypt.min.js"></script>
    {{-- <script src="{{ asset('js/func.js') }}" defe></script> --}}

    {{-- <script src="{{ mix('js/forge.js') }}"></script> --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Друзья') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="search_friends_box border_debug">
                        <div class="search_form_div border_debug">
                            {{-- @csrf --}}
                            <div class="div_search_input">
                                <input autocomplete="off" maxlength="40"
                                    placeholder="Поиск по имени, никнейму, номеру телефона..." type="text"
                                    class="friends_search_input" id="friends_search_input">
                                <input type="submit" id="search_friends_start" value="Поиск">

                            </div>
                            <div class="friend_queries_selector border_debug">
                                <h2 class="open_list_friends border_debug" id="open_list_friends">Друзья</h2>
                                <h2 class="open_list_friends_requests border_debug" id="open_list_friends_requests">
                                    Запросы в друзья</h2>
                            </div>


                            <div class="list_friend_request" id="list_friend_request">
                                {{-- <div class="friends_requests" id="friends_requests">
                                    <div class="search_results_tab">
                                        <div class="search_user_image">
                                            <img class="result_user_search_img"
                                                src="storage/s1zRSztpcLz2cdhjChIZ46ibEMOMgmJLimDoQTgY.png">
                                        </div>
                                        <div class="user_info_and_links  ">
                                            <div class="user_name ">
                                                <p>Хаймусов Ярослав @YarKhim</p>
                                            </div>
                                            <div class="user_actions request_actions">
                                                <div class="user_links  user_actions_buttons">
                                                    <a target="blank" href="/user_profile?id=YarKhim"
                                                        class="user_link">Перейти на
                                                        страницу</a>
                                                </div>
                                                <div class="accept_request  user_actions_buttons" id="accept_request_">
                                                    <p>Принять заявку</p>
                                                </div>
                                                <div class="dismiss_request  user_actions_buttons">
                                                    <p>Отклонить заявку</p>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div> --}}
                            </div>

                            <div class="list_user_friends" id="list_user_friends">
                                {{-- {{$a = 1;
                                e}} --}}
                                <div class="friends" id="user_friends">
                                    <script>
                                        // a = 0;

                                        // alert(a);
                                    </script>
                                    {{-- #TODOнеобходимо что бы у каждого элемнта был id как у пользователся которум
                                    этот элемнт соответствует --}}
                                    {{--
                                    <div class="search_results_tab">
                                        <div class="search_user_image">
                                            <img class="result_user_search_img"
                                                src="storage/s1zRSztpcLz2cdhjChIZ46ibEMOMgmJLimDoQTgY.png">
                                        </div>
                                        <div class="user_info_and_links  ">
                                            <div class="user_name ">
                                                <p>Хаймусов Ярослав @YarKhim</p>
                                            </div>
                                            <div class="user_actions">
                                                <div class="user_links  user_actions_buttons">
                                                    <a target="blank" href="/user_profile?id=YarKhim"
                                                        class="user_link">Перейти на
                                                        страницу</a>
                                                </div>
                                                <div class="remove_friend  user_actions_buttons">
                                                    <p>Удалить из друзей</p>
                                                </div>
                                            </div>

                                        </div>
                                    </div> --}}
                                </div>

                            </div>
                            <script>
                                open_list_friends.style.pointerEvents = 'none'; // Отключает события мыши
                                open_list_friends.style.opacity = '1';
                                open_list_friends_requests.style.opacity = '0.5';
                                open_list_friends = document.getElementById('open_list_friends');
                                open_list_friends_requests = document.getElementById('open_list_friends_requests');
                                list_friends = document.getElementById('list_user_friends');
                                console.log("🚀 ~ list_friends:", list_friends)
                                list_friends_requests = document.getElementById('list_friend_request');
                                console.log("🚀 ~ list_friends_requests:", list_friends_requests)
                                open_list_friends.addEventListener('click', function() {
                                    open_list_friends_requests.style.pointerEvents = 'all';
                                    open_list_friends.style.pointerEvents = 'none'; // Отключает события мыши
                                    open_list_friends.style.opacity = '1'; // Включает события мыши
                                    open_list_friends.classList.add('select_friend_tab');
                                    open_list_friends_requests.style.opacity = '0.5';
                                    list_friends.style.display = 'block';
                                    list_friends_requests.style.display = 'none';
                                    // open_list_friends.classList.add('color-change');
                                });
                                open_list_friends_requests.addEventListener('click', function() {
                                    open_list_friends.style.pointerEvents = 'all'; // Включает события мыши
                                    open_list_friends_requests.style.pointerEvents = 'none'; // Отключает события мыши
                                    open_list_friends_requests.style.opacity = '1';
                                    open_list_friends.style.opacity = '0.5';
                                    list_friends.style.display = 'none';
                                    list_friends_requests.style.display = 'block';
                                });
                                this_user_id = null;
                                $.ajax({
                                    url: '/get_this_user',
                                    type: 'post',
                                    async: false,
                                    data: {
                                        _token: '{{ csrf_token() }}' // Добавляем CSRF-токен для защиты
                                    },
                                    success: function(response) {
                                        this_user_id = response['this_user_id'];
                                    },
                                    error: function(xhr) {
                                        console.error('Error:', xhr);
                                        alert('Произошла ошибка: ' + xhr.responseJSON.message);
                                    }
                                });
                                console.log(this_user_id)
                                const socket = new WebSocket('ws://localhost:8888');
                                socket.onopen = function(event) {
                                    console.log('Подключено к WebSocket серверу');
                                };
                                socket.onmessage = function(event) {
                                    event_data = JSON.parse(event.data)
                                    if (event_data['type'] == 'new_friend_request') {
                                        // alert('lj,fdk');
                                        sender = event_data['user_sender'];
                                        console.log(sender['avatar']);
                                        new_request_type = `<div class="friends_requests" id="friends_requests` + sender['id'] + `">
                                                                <div class="search_results_tab">
                                                                    <div class="search_user_image">
                                                                        <img class="result_user_search_img"
                                                                            src="` + sender['avatar'] + `">
                                                                    </div>
                                                                    <div class="user_info_and_links  ">
                                                                        <div class="user_name ">
                                                                            <p>` + sender['name'] + " " + sender[
                                                'lastname'] +
                                            " @" + sender['username'] +
                                            `</p>
                                                                        </div>
                                                                        <div class="user_actions request_actions">
                                                                            <div class="user_links  user_actions_buttons">
                                                                                <a target="blank" href="/user_profile?id=` +
                                            sender['username'] +
                                            `"
                                                                                    class="user_link">Перейти на
                                                                                    страницу</a>
                                                                            </div>
                                                                            <div class="accept_request  user_actions_buttons" id="accept_request_` +
                                            sender['id'] +
                                            `">
                                                                                <p>Принять заявку</p>
                                                                            </div>
                                                                            <div class="dismiss_request  user_actions_buttons" id="dismiss_request_` +
                                            sender['id'] + `" uid="`+sender['id']+`">
                                                                                <p>Отклонить заявку</p>
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>`;
                                        $('#list_friend_request').append(new_request_type);
                                        document.getElementById("dismiss_request_" + sender['id']).addEventListener('click', function(e) {
                                            user_id = sender['id']
                                            data = {
                                                'sender': this_user_id,
                                                'addresee': user_id,
                                                'type_message': 'dismiss_friend_request',
                                            }
                                            sendMessage(JSON.stringify(data));
                                            button = $("#dismiss_request_" + user_id);
                                            tab = $("#friends_requests"+user_id );
                                            tab.remove();
                                        });
                                        document.getElementById("accept_request_" + sender['id']).addEventListener('click', function(e) {
                                            user_id = sender['id']
                                            data = {
                                                'sender': this_user_id,
                                                'addresee': user_id,
                                                'type_message': 'accept_friend_request',
                                            }
                                            sendMessage(JSON.stringify(data));
                                            button = $("#accept_request_" + user_id);
                                            tab = $("#friends_requests"+user_id );
                                            tab.remove();
                                        });
                                    }
                                };
                                socket.onclose = function(event) {
                                    console.log('Соединение закрыто');
                                    // alert('Соединение закрыто');
                                };

                                function sendMessage(message) {
                                    socket.send(message);
                                }
                            </script>
                            <script>
                                $(document).ready(function() {
                                    function send_request(user_id) {
                                        data = {
                                            'addresee': user_id,
                                            'sender': this_user_id,
                                            'type_message': 'send_friend_request',
                                        }
                                        sendMessage(JSON.stringify(data));
                                        button = $("#" + 'send_friend_request' + user_id);
                                        console.log(user_id);
                                        button.text('Запрос отправлен');
                                        button.css('pointerEvents', 'none');
                                        button.css('opacity', '0.5');
                                    }
                                    function accept_request(user_id){
                                        data = {
                                            'sender': this_user_id,
                                            'addresee': user_id,
                                            'type_message': 'accept_friend_request',
                                        }
                                        sendMessage(JSON.stringify(data));
                                        tab = $("#friends_requests"+user_id );
                                        tab.remove();

                                    }
                                    function dismiss_request(user_id){
                                        data = {
                                            'sender': this_user_id,
                                            'addresee': user_id,
                                            'type_message': 'dismiss_friend_request',
                                        }
                                        sendMessage(JSON.stringify(data));
                                        tab = $("#friends_requests"+user_id );
                                        tab.remove();

                                    }
                                    all_friends_id = [];
                                    $.ajax({
                                        url: '/getfriends',
                                        type: 'post',
                                        async: false,
                                        data: {
                                            _token: '{{ csrf_token() }}' // Добавляем CSRF-токен для защиты
                                        },
                                        success: function(response) {
                                            // a = 1
                                            all_friends = response['friends'];
                                            all_friends.forEach(friend => {
                                                all_friends_id.push(friend['id']);
                                                friend_div = `<div class="search_results_tab"  id="friend_tab` + friend[
                                                        'id'] + `">
                                                                <div class="search_user_image">
                                                                    <img class="result_user_search_img" src="` +
                                                    friend[
                                                        'avatar'] + `">
                                                                </div>
                                                                <div class="user_info_and_links ">
                                                                    <div class="user_name">
                                                                        <p>` + friend['name'] + " " + friend[
                                                        'lastname'] +
                                                    " @" + friend['username'] +
                                                    `</p>
                                                                    </div>
                                                                     <div class="user_actions">
                                                                        <div class="user_links  user_actions_buttons">
                                                                            <a target="blank" href="/user_profile?id=` +
                                                    friend['username'] +
                                                    `"
                                                                                class="user_link">Перейти на
                                                                                страницу</a>
                                                                        </div>
                                                                        <div class="remove_friend  user_actions_buttons" id=remove_friend"` +
                                                    friend[
                                                        'id'] + `">
                                                                            <p>Удалить из друзей</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>`;
                                                $('#user_friends').append(friend_div);
                                                console.log(friend);
                                            });
                                            // console.log(all_friends);
                                        },
                                        error: function(xhr) {
                                            console.error('Error:', xhr);
                                            alert('Произошла ошибка: ' + xhr.responseJSON.message);
                                        }
                                    });
                                    $.ajax({
                                        url: '/getFriendRequests',
                                        type: 'post',
                                        data: {
                                            _token: '{{ csrf_token() }}' // Добавляем CSRF-токен для защиты
                                        },
                                        success: function(response) {
                                            users = response['user_sender_requests'];
                                            users.forEach(user=>{
                                                // console.log(user);
                                                new_request_type = `<div class="friends_requests" id="friends_requests` + user['id'] + `">
                                                                <div class="search_results_tab">
                                                                    <div class="search_user_image">
                                                                        <img class="result_user_search_img"
                                                                            src="` + user['avatar'] + `">
                                                                    </div>
                                                                    <div class="user_info_and_links  ">
                                                                        <div class="user_name ">
                                                                            <p>` + user['name'] + " " + user[
                                                'lastname'] +
                                            " @" + user['username'] +
                                            `</p>
                                                                        </div>
                                                                        <div class="user_actions request_actions">
                                                                            <div class="user_links  user_actions_buttons">
                                                                                <a target="blank" href="/user_profile?id=` +
                                                                                user['username'] +
                                            `"
                                                                                    class="user_link">Перейти на
                                                                                    страницу</a>
                                                                            </div>
                                                                            <div uid="`+user['id']+`" class="accept_request  user_actions_buttons" id="accept_request_` +
                                                                            user['id'] +
                                            `">
                                                                                <p>Принять заявку</p>
                                                                            </div>
                                                                            <div class="dismiss_request  user_actions_buttons" id="dismiss_request_` +
                                                                            user['id'] + `" uid="`+user['id']+`">
                                                                                <p>Отклонить заявку</p>
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>`;
                                            $('#list_friend_request').append(new_request_type);
                                            });
                                            all_buttons_dismiss_request = document.querySelectorAll(
                                                        '.dismiss_request');
                                            all_buttons_dismiss_request.forEach(button => {
                                                button.addEventListener('click', function(e) {
                                                    dismiss_request($(this).attr("uid"));
                                                });
                                            });
                                            all_buttons_accept_request = document.querySelectorAll(
                                                        '.accept_request');
                                            all_buttons_accept_request.forEach(button => {
                                                button.addEventListener('click', function(e) {
                                                    accept_request($(this).attr("uid"));
                                                });
                                            });
                                            // console.log(users);
                                        },
                                        error: function(xhr) {
                                            console.error('Error:', xhr);
                                            alert('Произошла ошибка: ' + xhr.responseJSON.message);
                                        }
                                    });
                                    var serach_friends_input = document.getElementById('friends_search_input');
                                    // serach_friends_input.value = 'Ярослав 1';
                                    serach_friends = document.getElementById('search_friends_start');
                                    serach_friends.addEventListener('click', function() {
                                        if (serach_friends_input.value.length > 0) {
                                            $('#search_results').empty();
                                            var data_1 = serach_friends_input.value.split(' ');
                                            var data_for_search = data_1.filter(item => item !== '');
                                            console.log(data_for_search);
                                            data_for_search = JSON.stringify(data_for_search);


                                            $.ajax({
                                                url: '/search', // URL вашего маршрута
                                                type: 'POST', // Метод запроса
                                                data: {
                                                    data: data_for_search,
                                                    _token: '{{ csrf_token() }}' // Добавляем CSRF-токен для защиты
                                                },
                                                success: function(response) {

                                                    var all_users = [];
                                                    var searched_id = [];
                                                    var encounteredIds = {}; // Объект для отслеживания встреченных id
                                                    response.data.forEach(users => {
                                                        users.forEach(user => {
                                                            var userId = user['id'];
                                                            if (!encounteredIds[userId]) {
                                                                // Если id еще не встречался, добавляем его в объект
                                                                encounteredIds[userId] = true;
                                                                all_users.push(user);
                                                            }
                                                        });
                                                    });
                                                    all_users.forEach(user => {
                                                        is_req_sent = null;
                                                        $.ajax({
                                                            url: '/isRequestSent',
                                                            type: 'POST',
                                                            async: false,
                                                            data: {
                                                                data: user['id'],
                                                                _token: '{{ csrf_token() }}' // Добавляем CSRF-токен для защиты
                                                            },
                                                            success: function(req_state) {
                                                                // console.log(req_state
                                                                //     .request_state);
                                                                is_req_sent = req_state
                                                                    .request_state
                                                                //
                                                            },
                                                            error: function(xhr) {
                                                                console.error('Error:',
                                                                    xhr);
                                                                alert('Произошла ошибка: ' +
                                                                    xhr.responseJSON
                                                                    .message);
                                                            }
                                                        });
                                                        console.log(is_req_sent);
                                                        if (all_friends_id.includes(user['id'])) {
                                                            search_result_div =
                                                                `<div class="search_results_tab"  id="friend_tab` +
                                                                user[
                                                                    'id'] + `">
                                                                <div class="search_user_image">
                                                                    <img class="result_user_search_img" src="` +
                                                                user[
                                                                    'avatar'] + `">
                                                                </div>
                                                                <div class="user_info_and_links ">
                                                                    <div class="user_name">
                                                                        <p>` + user['name'] + " " + user[
                                                                    'lastname'] +
                                                                " @" + user['username'] +
                                                                `</p>
                                                                    </div>
                                                                     <div class="user_actions">
                                                                        <div class="user_links  user_actions_buttons">
                                                                            <a target="blank" href="/user_profile?id=` +
                                                                user['username'] +
                                                                `"
                                                                                class="user_link">Перейти на
                                                                                страницу</a>
                                                                        </div>
                                                                        <div class="remove_friend  user_actions_buttons" id=remove_friend"` +
                                                                user[
                                                                    'id'] + `">
                                                                            <p>Удалить из друзей</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>`;
                                                        } else {


                                                            search_result_div =
                                                                `<div class="search_results_tab"  id="friend_tab` +
                                                                user[
                                                                    'id'] + `">
                                                                <div class="search_user_image">
                                                                    <img class="result_user_search_img" src="` +
                                                                user[
                                                                    'avatar'] + `">
                                                                </div>
                                                                <div class="user_info_and_links ">
                                                                    <div class="user_name">
                                                                        <p>` + user['name'] + " " + user[
                                                                    'lastname'] +
                                                                " @" + user['username'] +
                                                                `</p>
                                                                    </div>
                                                                     <div class="user_actions">
                                                                        <div class="user_links  user_actions_buttons">
                                                                            <a target="blank" href="/user_profile?id=` +
                                                                user['username'] +
                                                                `"
                                                                                class="user_link">Перейти на
                                                                                страницу</a>
                                                                        </div>
                                                                                <div class="friend_request  user_actions_buttons" id="send_friend_request` +
                                                                user['id'] + `" uid="` + user['id'] + `">
                                                                                    Добавить в друзья
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>`;
                                                        }

                                                        $('#search_results').append(search_result_div);

                                                        button_send_request = document.getElementById(
                                                            'send_friend_request' + user['id']);
                                                        console.log(button_send_request)
                                                        if (is_req_sent) {
                                                            button_send_request.style.transition =
                                                                'none';
                                                            button_send_request.innerText =
                                                                'Запрос отправлен';
                                                            button_send_request.style.pointerEvents =
                                                                'none'; // Отключает события мыши
                                                            button_send_request.style.opacity =
                                                                '0.5';
                                                        }
                                                        // console.log(search_result_div);

                                                    });
                                                    if (all_users.length == 0) {
                                                        search_result_div =
                                                            `<div class="nothing_for_your_query border_debug" >
                                                            <h2>По вашему запросу никого не найдено :( </h2>
                                                            <p>Попробуйте что-нибудь другое</p>
                                                            </div>`;
                                                        $('#search_results').append(search_result_div);
                                                        // console.log("По вашему запросу ничего не найдено(");
                                                    }
                                                    serach_friends_input.value = '';



                                                    all_buttons_friends_request = document.querySelectorAll(
                                                        '.friend_request');
                                                    all_buttons_friends_request.forEach(button => {
                                                        // id = button.id
                                                        // user_id = id.replace('send_friend_request', '');
                                                        // console.log(id);

                                                        // console.log("🚀 ~ serach_friends.addEventListener ~ user_id:", user_id)
                                                        // button.addEventListener('click', send_request.bind(
                                                        //     user_id));
                                                        button.addEventListener('click', function(e) {
                                                            //lert(button);
                                                            // console.log($(this).attr("uid"));
                                                            send_request($(this).attr("uid"));
                                                        });
                                                    });
                                                },
                                                error: function(xhr) {
                                                    console.error('Error:', xhr);
                                                    alert('Произошла ошибка: ' + xhr.responseJSON.message);
                                                }
                                            });
                                        }
                                    });
                                });
                            </script>
                        </div>
                        <div class="search_results border_debug" id="search_results">

                            {{-- <div class="search_results_tab">
                                <div class="search_user_image">
                                    <img class="result_user_search_img"
                                        src="storage/s1zRSztpcLz2cdhjChIZ46ibEMOMgmJLimDoQTgY.png">
                                </div>
                                <div class="user_info_and_links  ">
                                    <div class="user_name ">
                                        <p>Хаймусов Ярослав @YarKhim</p>
                                    </div>
                                    <div class="user_actions">
                                        <div class="user_links  user_actions_buttons">
                                            <a target="blank" href="/user_profile?id=YarKhim" class="user_link">Перейти
                                                на
                                                страницу</a>
                                        </div>
                                        <div class="friend_request  user_actions_buttons">
                                            <p>Добавить в друзья</p>
                                        </div>
                                    </div>

                                </div>
                            </div> --}}

                        </div>
                    </div>

                    {{-- {{ __('Поиск друзей по имени: ') }}
                    <div class="find_friends">
                        <div class="input_data_for_search">
                            <form action="{{ route('contact.submit') }}" method="POST">
                                @csrf
                                <input class="find_friends_input_text" type="text" name="name" placeholder="Поиск"
                                    autocomplete="nope">
                                <button type="submit" class="find_friends_submit" name="search">Поиск</button>
                            </form>
                        </div>
                        <div class="result_search" id="result_search">


                            <h2>Результаты поиска</h2>
                            @if (isset($count) && $count != 0)
                            <script>
                                const encrypt = new JSEncrypt();
                                    // import JSEncrypt from 'jsencrypt';
                                    // const forge = require('node-forge');
                                    @foreach ($users as $user)
                                        // public_key = `{{ $user->public_key }}`;
                                        // private_key = `{{ $user->private_key }}`;
                                        // encrypt.setPublicKey(public_key);
                                        // encrypt.setPrivateKey(private_key);
                                        // const dataToEncrypt = "Это секретное сообщение";
                                        // const encryptedData = encrypt.encrypt(dataToEncrypt);
                                        // console.log("Зашифрованные данные:", encryptedData);

                                        // // Расшифровываем данные
                                        // const decryptedData = encrypt.decrypt(encryptedData);
                                        // console.log("Расшифрованные данные:", decryptedData);
                                        element_result_search_tab =
                                            `<div class="result_search_tab" id="result_search_tab_link_{{ $user->username }}">
                                                                        <div class="result_search_tab_info">
                                                                            <div class="user_image">
                                                                                <img src='{{ route('profile.avatar', ['user' => $user->id]) }}'>
                                                                            </div>
                                                                            <div class="text_info_search">
                                                                                <p><b>{{ $user->name }} {{ $user->lastname }}</b> <br><i> @` + `{{ $user->username }} </i></p>
                                                                            </div>
                                                                        </div>
                                                                    </div>`;
                                        $('#result_search').append(element_result_search_tab);
                                        console.log(document.getElementById('result_search_tab_link_{{ $user->username }}'));
                                        document.getElementById('result_search_tab_link_{{ $user->username }}').addEventListener('click', function() {
                                            window.open('/user_profile?id={{ $user->username }}')
                                        })
                                    @endforeach
                            </script>
                            @endif

                            @if (session('message'))
                            <div class="alert alert-warning">
                                {{ session('message') }}
                            </div>
                            @endif
                        </div>

                    </div> --}}


                </div>
            </div>
        </div>
    </div>
</x-app-layout>

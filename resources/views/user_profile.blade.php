<x-app-layout>
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    {{-- <script src="{{ asset('js/BigInteger.js') }}"></script>

    <script src="{{ asset('js/jsencrypt.min.js') }}"></script>
    <script src="{{ asset('js/RSA.min.js') }}"></script>
    <script src="{{ asset('js/forge.min.js') }}"></script>
    <script src="{{ asset('js/crypto-js.min.js') }}"></script> --}}
    <script src="{{ asset('js/func.js') }}" defe></script>
    <div class="make_post_plane">
        <div class="make_post">
            <div class="make_new_post">
                Новый пост
            </div>
            <div class="new_post_inputs">
                <div class="left_side_new_post_inputs">
                    <div class="header_left_side_new_post_input">
                        <h1>Текст поста <h1>
                    </div>

                    <div class="input_post_text_div">
                        <textarea class="input_post_text"></textarea>
                    </div>
                    <div class="publish_post">
                        <div class="button_publish_post">
                            Опубликовать
                        </div>
                        <div class="button_cancel_publish_post">
                            Отмена
                        </div>
                    </div>
                    {{-- <input type="file" id="fileInput" multiple accept="image/*"> --}}
                    {{-- <div id="previewContainer"></div> --}}
                </div>
                <div class="right_side_new_post_inputs">
                    <form id="form_upload">
                        <input id="upload_images" type="file" multiple accept="image/*">
                    </form>

                    <div class="div_label_for_upload_images">
                        <label class="label_for_upload_images" for="upload_images">Добавить фото к посту</label>
                    </div>
                    <div class="all_uploaded_images_post" id="all_uploaded_images_post">
                    </div>
                    <div class="container">
                        <ul class="image-gallery" id="image_gallery">
                            {{-- <li>
                                <img class="photo_list" src="/storage/qoHQxweYhM5Mxap4ftcKuZVEhsJ1ErgxwiVem9rp.png">
                                <div class="overlay">
                                    <span>
                                        Удалить
                                    </span>
                                </div>
                            </li> --}}
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">

            @if (isset($user))
            {{ __('Страница пользователся @') . $user->username }}
            {{-- <script>
                user = {{$user;}}
            </script> --}}
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{-- <script>
                        console.log(`{{$user->name}}, {{$user->username}}, {{$user->lastname}}, {{$user->avatar}} `)
                        user = `{{$user}}`;
                    </script> --}}
                    <div class="border_debug user_page_plane">
                        <div class="left_side_plane border_debug" id="left_side_plane">
                            <div class="user_avatar">
                                <img src="{{$user->avatar}}" alt="{{$user->username}}" class="user_avatar_img">
                            </div>
                            <div class="user_name_lastname_username">
                                <p class="user_name_lastname">{{$user->name}} {{$user->lastname}}</p>
                                <i class="user_username" id="user_username">@ {{$user->username}}</i>
                                <div class="left_user_actions_buttons_userpage write_user">
                                    Отправить сообщение
                                </div>
                                <div class="left_user_actions_buttons_userpage make_post_button">
                                    Новый пост
                                </div>
                                <script>
                                    all_files= [];
                                    all_files_links = {};
                                    function copy(){
                                        navigator.clipboard.writeText(`@`+`{{$user->username}}`)
                                        .then(() => {
                                            alert('Имя пользователя скопировано');
                                        })
                                        .catch(err => {
                                            console.error('Произошла ошибка');
                                        });
                                    }
                                    var page = 1;
                                    var loading = false; // флаг загрузки




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
                                    const socket = new WebSocket('ws://localhost:8888');
                                    socket.onopen = function(event) {
                                        console.log('Подключено к WebSocket серверу');
                                    };
                                    socket.onmessage = function(event) {
                                        event_data = JSON.parse(event.data)
                                    };
                                    socket.onclose = function(event) {
                                        console.log('Соединение закрыто');
                                    };
                                    function sendMessage(message) {
                                        socket.send(message);
                                    }
                                    document.getElementById('user_username').addEventListener('click',copy);
                                    if('{{$user->id}}' == this_user_id){
                                        // console.log('Это ваша страница');
                                        $(document.querySelector('.make_post_button')).css('display', 'block');
                                        document.querySelector('.make_post_button').addEventListener('click', function(){
                                            $(document.querySelector('.make_post_plane')).css('visibility', 'visible');
                                        });
                                        $('#upload_images').on('change', function() {
                                            var files = this.files;
                                            function del_photo_tab(event){
                                                $('#li_'+this.guid).remove();
                                                delete all_files[this.guid];
                                                delete all_files_links[this.guid];
                                            }
                                            for (var i = 0; i < files.length; i++) {
                                                var file = files[i];
                                                var reader = new FileReader();
                                                new_guid = generateGUID();
                                                all_files[new_guid] = file;
                                                reader.onload = function(e) {
                                                    // console.log(e.target.result);
                                                    // all_files_links.push(e.target.result);
                                                    all_files_links[new_guid] = e.target.result;
                                                    var li =
                                                    `<li id="li_`+new_guid+`">
                                                        <img  class="photo_list" src="`+e.target.result+`" id="img_`+new_guid+`">
                                                        <div class="overlay" id="del_`+new_guid+`">
                                                            <span>
                                                                Удалить
                                                            </span>
                                                        </div>
                                                    </li>`;
                                                    $('#image_gallery').append(li);
                                                    document.getElementById('del_'+new_guid).addEventListener('click', {handleEvent: del_photo_tab, guid: new_guid})

                                                };

                                                reader.readAsDataURL(file);
                                            }
                                            this.value = null;
                                        });
                                        document.querySelector('.button_publish_post').addEventListener('click', function(){
                                            if(Object.keys(all_files).length > 0 || document.querySelector('.input_post_text').value.length > 0 ){
                                                // console.log(all_files_links);
                                                data = {
                                                        'type_message': 'new_post',
                                                        'post_author': this_user_id,
                                                        'post_text': document.querySelector('.input_post_text').value,
                                                        'post_photo': all_files_links,
                                                };
                                                sendMessage(JSON.stringify(data));
                                            }
                                            else{
                                                alert('Вы не ввели данные, без них не получится опубликовать ваш пост :(');
                                            }
                                        });
                                    }
                                    $.ajax({
                                        url: '/getfriends',
                                        type:'post',
                                        data: {
                                        _token: '{{ csrf_token() }}' // Добавляем CSRF-токен для защиты
                                        },
                                        success: function(response) {
                                            needs_user = `{{$user->id}}`;
                                            flag = false;
                                            friends = response['friends'];
                                            friends.forEach(user => {
                                                if(user['id'] == needs_user){
                                                    flag = true;
                                                }
                                            });
                                            if(flag){
                                                delete_friend_div  =
                                                `<div uid="{{$user->id}}" class="remove_friend_userpage  left_user_actions_buttons_userpage" id="remove_friend_{{$user->id}}">
                                                    <p>Удалить из друзей</p>
                                                </div>`;
                                                $('#left_side_plane').append(delete_friend_div);
                                                document.getElementById("remove_friend_{{$user->id}}").addEventListener('click', function(){
                                                    data = {
                                                        'sender': this_user_id,
                                                        'addresee': '{{$user->id}}',
                                                        'type_message': 'delete_friend',
                                                    }
                                                    sendMessage(JSON.stringify(data));
                                                    tab = $("#remove_friend_{{$user->id}}" );
                                                    tab.remove();
                                                });
                                            }
                                            else{
                                                add_friend_div  =
                                                `<div uid="{{$user->id}}" class="add_friend_userpage  left_user_actions_buttons_userpage" id="add_friend">
                                                    <p>Добавить в друзья</p>
                                                </div>`;
                                                $('#left_side_plane').append(add_friend_div);
                                                // console.log(document.getElementById("add_friend"))
                                                document.getElementById("add_friend").addEventListener('click', function(){
                                                    data = {
                                                        'addresee': needs_user,
                                                        'sender': this_user_id,
                                                        'type_message': 'send_friend_request',
                                                    }
                                                    // console.log(data);
                                                    sendMessage(JSON.stringify(data));
                                                    button = $("#" + 'add_friend');
                                                    // console.log(user_id);
                                                    button.text('Запрос отправлен');
                                                    button.css('pointerEvents', 'none');
                                                    button.css('opacity', '0.5');
                                                });
                                            }
                                        },
                                        error: function(xhr) {
                                            console.error('Error:', xhr);
                                            alert('Произошла ошибка: ' + xhr.responseJSON.message);
                                        }
                                    });






                                    // $.ajax({
                                    //     url: '/get_user_posts',
                                    //     type: 'post',
                                    //     data: {
                                    //         _token: '{{ csrf_token() }}' // Добавляем CSRF-токен для защиты
                                    //     },
                                    //     success: function(response) {
                                    //         this_user_id = response['this_user_id'];
                                    //     },
                                    //     error: function(xhr) {
                                    //         console.error('Error:', xhr);
                                    //         alert('Произошла ошибка: ' + xhr.responseJSON.message);
                                    //     }
                                    // });
                                    // alert("Скопированно в буфер обмена");











                                    // function swap_image(new_imgae_id, image_id){
                                    //     // console.log('Меняем '+image_id+' на '+new_imgae_id);
                                    //     $('#post_data_image_carousel_'+image_id).css('display', 'none');
                                    //     $('#post_data_image_carousel_'+new_imgae_id).css('display', 'block');
                                    // }
                                    // const elements = document.querySelectorAll('.post_data_image_carousel');
                                    // current_image = 0;
                                    // last_image = 0;
                                    // max_image = elements.length - 1;
                                    // for(let i =1; i<elements.length; i++){
                                    //     id= elements[i].id;
                                    //     $("#"+id).css('display','none');
                                    // }
                                    // function handleLeftClick(id_elem) {
                                    //     if(current_image == 0){
                                    //         last_image = current_image;
                                    //         current_image = max_image;
                                    //     }
                                    //     else{
                                    //         last_image = current_image;
                                    //         current_image --;
                                    //     }
                                    //     swap_image(current_image, last_image);
                                    // }
                                    // function handleRightClick(id_elem) {
                                    //     if(current_image == max_image){
                                    //         last_image = current_image;
                                    //         current_image = 0;
                                    //     }
                                    //     else{
                                    //         last_image = current_image;
                                    //         current_image++;
                                    //     }
                                    //     swap_image(current_image, last_image);
                                    // }
                                    // elements.forEach(element => {
                                    //     element.addEventListener('click', (event) => {
                                    //         const rect = element.getBoundingClientRect(); // Получаем размеры и позицию блока
                                    //         const clickX = event.clientX - rect.left; // Координата X клика относительно блока
                                    //         const halfWidth = rect.width / 2; // Половина ширины блока

                                    //         if (clickX < halfWidth/2) {
                                    //             handleLeftClick(element.id); // Если клик был на левой половине
                                    //         }
                                    //         if(clickX> (3*halfWidth)/2){
                                    //             handleRightClick(element.id); // Если клик был на правой половине
                                    //         }
                                    //     });
                                    // });
                                </script>
                            </div>
                        </div>
                        <div class="central_side_plane border_debug">
                            <div class="user_posts border_debug" id="user_posts">
                                <div class="head_posts">
                                    <p>
                                        Посты пользователя @
                                        {{$user->username}}
                                    </p>
                                </div>
                                {{-- <div class="user_post_tab border_debug">

                                    <div class="user_post_rect">
                                        <div class="post_header ">
                                            <img class="post_image border_debug"
                                                src="/storage/s1zRSztpcLz2cdhjChIZ46ibEMOMgmJLimDoQTgY.png">
                                            <div class="post_author border_debug">
                                                Ярослав Хаймусов
                                            </div>
                                            <div class="post_date border_debug">
                                                12.12.2024
                                            </div>
                                        </div>
                                        <div class="post_data_rect">
                                            <div class="post_data">
                                                <div class="post_data_images">
                                                    <div class="post_data_image">
                                                        <img class='post_data_image_carousel'
                                                            id='post_data_image_carousel_0'
                                                            src="/storage/3K4xEsRsU8RGtY6iMmY0GzqDXdoR7DHWHmLXUHch.jpg">
                                                        <img class='post_data_image_carousel'
                                                            id='post_data_image_carousel_1'
                                                            src="/storage/qoHQxweYhM5Mxap4ftcKuZVEhsJ1ErgxwiVem9rp.png">
                                                        <img class='post_data_image_carousel'
                                                            id='post_data_image_carousel_2'
                                                            src="/storage/photo_2025-01-03_12-05-26.jpg">
                                                        <img class='post_data_image_carousel'
                                                            id='post_data_image_carousel_3'
                                                            src="/storage/photo_2025-01-03_14-08-48.jpg">
                                                        <script>
                                                        </script>
                                                    </div>
                                                </div>
                                                <div class="post_text">
                                                    <p>
                                                        Привет, это пост на странице пользователя @2 пока что это просто
                                                        тестовый пост для того что бы понять как поведёт вёрстка если в
                                                        ней окажеться достаточно большой текст как этот, можешь не
                                                        дочитывать до конца это просто ессмысленный набор слов 3453534
                                                        532452345234
                                                        233245234
                                                        ку
                                                        куцк
                                                        йцукеумумекмуему
                                                        ке цук е

                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="post_footer">
                                            <div class="post_likes">

                                                Мне нравится


                                            </div>
                                            <div class="post_likes_counter">
                                                Понравилось 1234 раз
                                            </div>
                                            <div class="post_watchers">
                                                11234 Просмотров
                                            </div>
                                        </div>
                                        <div class="post_comments">
                                            <div class="post_comments_input">
                                                <textarea class="text_input_comment" name="text"
                                                    oninput='this.style.height = "";this.style.height = this.scrollHeight + "px";'></textarea>

                                                <div class="div_send_comment">
                                                    <div class="send_comment">
                                                        Отправить
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                    </div>

                                </div> --}}
                                {{-- <div class="user_post_tab border_debug">

                                    <div class="user_post_rect">
                                        <div class="post_header ">
                                            <img class="post_image border_debug"
                                                src="/storage/s1zRSztpcLz2cdhjChIZ46ibEMOMgmJLimDoQTgY.png">
                                            <div class="post_author border_debug">
                                                Ярослав Хаймусов
                                            </div>
                                            <div class="post_date border_debug">
                                                12.12.2024
                                            </div>
                                        </div>
                                        <div class="post_data_rect">
                                            <div class="post_data">
                                                <div class="post_data_images">
                                                    <div class="post_data_image">
                                                        <img class='post_data_image_carousel'
                                                            id='post_data_image_carousel_0'
                                                            src="/storage/3K4xEsRsU8RGtY6iMmY0GzqDXdoR7DHWHmLXUHch.jpg">
                                                        <img class='post_data_image_carousel'
                                                            id='post_data_image_carousel_1'
                                                            src="/storage/qoHQxweYhM5Mxap4ftcKuZVEhsJ1ErgxwiVem9rp.png">
                                                        <img class='post_data_image_carousel'
                                                            id='post_data_image_carousel_2'
                                                            src="/storage/photo_2025-01-03_12-05-26.jpg">
                                                        <img class='post_data_image_carousel'
                                                            id='post_data_image_carousel_3'
                                                            src="/storage/photo_2025-01-03_14-08-48.jpg">
                                                        <script>
                                                        </script>
                                                    </div>
                                                </div>
                                                <div class="post_text">
                                                    <p>
                                                        Привет, это пост на странице пользователя @2 пока что это просто
                                                        тестовый пост для того что бы понять как поведёт вёрстка если в
                                                        ней окажеться достаточно большой текст как этот, можешь не
                                                        дочитывать до конца это просто ессмысленный набор слов 3453534
                                                        532452345234
                                                        233245234
                                                        ку
                                                        куцк
                                                        йцукеумумекмуему
                                                        ке цук е

                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="post_footer">
                                            <div class="post_likes">

                                                Мне нравится


                                            </div>
                                            <div class="post_likes_counter">
                                                Понравилось 1234 раз
                                            </div>
                                            <div class="post_watchers">
                                                11234 Просмотров
                                            </div>
                                        </div>
                                        <div class="post_comments">
                                            <div class="post_comments_input">
                                                <textarea class="text_input_comment" name="text"
                                                    oninput='this.style.height = "";this.style.height = this.scrollHeight + "px";'></textarea>

                                                <div class="div_send_comment">
                                                    <div class="send_comment">
                                                        Отправить
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                    </div>

                                </div> --}}

                            </div>
                        </div>
                        <script>
                            all_photos_tab ={};
                            function loadData() {
                                if (loading) return; // если данные уже загружаются, не выполнять запрос
                                loading = true; // устанавливаем флаг загрузки
                                $.ajax({
                                    url: "/load_posts",
                                    type: "POST",
                                    data: {
                                        _token: '{{ csrf_token() }}',
                                        page: page,
                                        user: '{{$user->id}}',
                                    },
                                    success: function(data) {
                                        if (data.data.length > 0) {
                                            console.log(data['data']);
                                            data['data'].forEach(post => {
                                                photos = JSON.parse(post['photos']);
                                                const url ='/storage/'+post['text'];
                                                post_text = '';
                                                new_post_tab =
                                                `<div class="user_post_tab border_debug" id="user_post_tab_`+post['id']+`">
                                                    <div class="user_post_rect">
                                                        <div class="post_header ">
                                                            <img class="post_image border_debug"
                                                                src="{{$user->avatar}}">
                                                            <div class="post_author border_debug">
                                                                {{$user->name}} {{$user->lastname}}
                                                            </div>
                                                            <div class="post_date border_debug" id="post_date_`+post['id']+`">
                                                                `+post['created_at']+`
                                                            </div>
                                                        </div>
                                                        <div class="post_data_rect">
                                                            <div class="post_data">
                                                                <div class="post_data_images">
                                                                    <div class="post_data_image" id="post_data_image_`+post['id']+`">


                                                                    </div>
                                                                </div>
                                                                <div class="post_text" id="post_text_`+post['id']+`">

                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="post_footer">
                                                            <div class="post_likes" id="post_likes_`+post['id']+`">
                                                                Мне нравится
                                                            </div>
                                                            <div class="post_likes_counter" id="post_likes_counter_`+post['id']+`">
                                                                `+post['likes_count']+`
                                                            </div>
                                                            <div class="post_watchers" id="post_watchers_`+post['id']+`">
                                                               `+post['views']+`
                                                            </div>
                                                        </div>
                                                        <div class="post_comments">
                                                            <div class="post_comments_input">
                                                                <textarea id="input_comment_`+post['id']+`"  class="text_input_comment" name="text"
                                                                    oninput='this.style.height = "";this.style.height = this.scrollHeight + "px";'></textarea>

                                                                <div class="div_send_comment">
                                                                    <div class="send_comment" id="send_comment_`+post['id']+`">
                                                                        Отправить
                                                                    </div>
                                                                </div>
                                                            </div>


                                                        </div>
                                                    </div>

                                                </div>`;
                                                $('#user_posts').append(new_post_tab);
                                                fetch(url)
                                                    .then(response => {
                                                        if (!response.ok) {
                                                            throw new Error('Сеть ответила с ошибкой: ' + response.status);
                                                        }
                                                        return response.text(); // Получаем текст из ответа
                                                    })
                                                    .then(text => {
                                                        console.log(data);
                                                        post_text = '<p>'+ text+'</p>';
                                                        $('#post_text_'+post['id']).append(post_text);
                                                        // document.getElementById('output').textContent = data; // Выводим текст на страницу
                                                    })
                                                    .catch(error => {
                                                        console.error('Произошла ошибка:', error);
                                                    });
                                                console.log(photos);
                                                counter = 0;
                                                photos.forEach(photo => {
                                                    img = `<img class='post_data_image_carousel post_`+post['id']+`_data_image_carousel'
                                                            id='post_`+post['id']+`_data_image_carousel_`+counter+`'
                                                            src="/storage/`+photo+`">`;
                                                    $('#post_data_image_'+post['id']).append(img);
                                                    counter++;
                                                });
                                                function swap_image(new_imgae_id, image_id,post_id){
                                                    console.log('Меняем '+image_id+' на '+new_imgae_id);
                                                    console.log(post_id);
                                                    console.log(all_photos_tab)
                                                    $('#post_'+post_id+'_data_image_carousel_'+image_id).css('display', 'none');
                                                    $('#post_'+post_id+'_data_image_carousel_'+new_imgae_id).css('display', 'block');
                                                }
                                                const elements = document.querySelectorAll('.post_'+post['id']+'_data_image_carousel');
                                                console.log('214321   ',elements);
                                                all_photos_tab[post['id']] =
                                                {
                                                    'current_image': 0,
                                                    'last_image': 0,
                                                    'max_image':elements.length - 1
                                                };
                                                console.log(all_photos_tab)
                                                // current_image = 0;
                                                // last_image = 0;
                                                // max_image = elements.length - 1;
                                                for(let i =1; i<elements.length; i++){
                                                    id= elements[i].id;
                                                    $("#"+id).css('display','none');
                                                }
                                                photos_tab = all_photos_tab[post['id']];
                                                function handleLeftClick(id_elem) {
                                                    console.log(id_elem);
                                                    if(photos_tab['current_image'] == 0){
                                                        photos_tab['last_image'] = photos_tab['current_image'];
                                                        photos_tab['current_image'] = photos_tab['max_image'];
                                                    }
                                                    else{
                                                        photos_tab['last_image'] = photos_tab['current_image'];
                                                        photos_tab['current_image'] --;
                                                    }
                                                    console.log(all_photos_tab)
                                                    // console.log(photos_tab['current_image'], ' ', photos_tab['last_image'],' ' ,post['id']);
                                                    swap_image(photos_tab['current_image'], photos_tab['last_image'], post['id']);
                                                }
                                                function handleRightClick(id_elem) {
                                                    console.log(id_elem);
                                                    if(photos_tab['current_image'] == photos_tab['max_image']){
                                                        photos_tab['last_image'] = photos_tab['current_image'];
                                                        photos_tab['current_image'] = 0;
                                                    }
                                                    else{
                                                        photos_tab['last_image'] = photos_tab['current_image'];
                                                        photos_tab['current_image']++;
                                                    }
                                                    console.log(all_photos_tab)
                                                    // if( photos_tab['current_image'] > photos_tab['max_image']){
                                                    //     photos_tab['current_image'] = 0;
                                                    // }
                                                    // console.log(photos_tab['current_image'], ' ', photos_tab['last_image'],' ' ,post['id']);
                                                    swap_image( photos_tab['current_image'], photos_tab['last_image'], post['id']);
                                                }
                                                elements.forEach(element => {
                                                    element.addEventListener('click', (event) => {
                                                        const rect = element.getBoundingClientRect(); // Получаем размеры и позицию блока
                                                        const clickX = event.clientX - rect.left; // Координата X клика относительно блока
                                                        const halfWidth = rect.width / 2; // Половина ширины блока

                                                        if (clickX < halfWidth/2) {
                                                            handleLeftClick(element.id); // Если клик был на левой половине
                                                        }
                                                        if(clickX> (3*halfWidth)/2){
                                                            handleRightClick(element.id); // Если клик был на правой половине
                                                        }
                                                    });
                                                });
                                                // <img class='post_data_image_carousel'
                                                //                             id='post_data_image_carousel_0'
                                                //                             src="/storage/3K4xEsRsU8RGtY6iMmY0GzqDXdoR7DHWHmLXUHch.jpg">
                                                //                         <img class='post_data_image_carousel'
                                                //                             id='post_data_image_carousel_1'
                                                //                             src="/storage/qoHQxweYhM5Mxap4ftcKuZVEhsJ1ErgxwiVem9rp.png">
                                                //                         <img class='post_data_image_carousel'
                                                //                             id='post_data_image_carousel_2'
                                                //                             src="/storage/photo_2025-01-03_12-05-26.jpg">

                                                // console.log( post['photos']);

                                            });

                                            page++;
                                        } else {
                                            $('#user_posts').off('scroll');
                                        }
                                    },
                                    complete: function() {
                                        loading = false; // сбрасываем флаг загрузки после завершения запроса
                                    }
                                });
                            }
                            $('#user_posts').on('scroll', function() {
                                if ($(this).scrollTop() + $(this).innerHeight() >= this.scrollHeight -10) {
                                    loadData(); // загружаем данные при достижении конца контейнера
                                }
                            });
                            loadData(); // первоначальная загрузка данных
                        </script>
                        <div class="right_side_plane border_debug"></div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- .post_header_img{
    border: 1px solid black;
    /* float: left;
    */
    right: 0;
    display: flex;
    /* justify-content: center; */
    align-items: center;
    }
    .post_image{
    border-radius: 50%;
    max-width: 10%;
    /* Ограничение ширины изображения */
    height: auto;
    /* Поддержка пропорций */
    } --}}
</x-app-layout>

// const { error } = require("laravel-mix/src/Log");

const fileInput = document.getElementById('channel_avatar_upload');
const preview = document.getElementById('preview');
const previewImg = document.getElementById('preview-img');
const removeButton = document.getElementById('remove-button');
const new_fileInput = document.getElementById('new_channel_avatar_upload');
const new_preview = document.getElementById('new_preview');
const new_previewImg = document.getElementById('new_preview-img');
const new_removeButton = document.getElementById('new_remove-button');
const select_channel = document.getElementById('select_channel');
let page = 1; // Начальная страница
const limit = 3; // Количество постов на страницу
var loading = false;
channel_id_settings = null;
console.log("🚀 ~ select_channel:", select_channel)
file_avatar_channel = '';
new_file_avatar_channel = '';
all_tabs_channels_select = document.querySelectorAll('.select_tab');
current_tab = null;
isFirst = true;
current_update_channel_id = null;
selected_channal_for_post = null;
opened_channel_id = null;
$('.opened_channel_subscription').css('display', 'none');
$(document).on('keydown', function (event) {
    if (event.key === 'Escape' || event.keyCode === 27) {
        // Действия при нажатии клавиши Esc
        $('.opened_channel_subscription').css('display', 'none');
        opened_channel_id = null;
    }
});
this_user_id = null;
this_user = null;

$.ajax({
    url: '/get_this_user',
    type: 'get',
    async: false,
    data: {
        _token: '{{ csrf_token() }}' // Добавляем CSRF-токен для защиты
    },
    success: function (response) {
        this_user = response['this_user'];
        this_user_id = response['this_user_id'];
    },
    error: function (xhr) {
        console.error('Error:', xhr);
        alert('Произошла ошибка: ' + xhr.responseJSON.message);
    }
});
current_tab = 'opened_tab_0';
$('#' + current_tab).css('display', 'flex');
$('#select_tab_0').addClass('selected_channel_tab');
all_tabs_channels_select.forEach(tab => {
    $(tab).on('click', function () {
        $('#' + current_tab).css('display', 'none');
        last_select_tab_id = 'select_tab_' + current_tab.replace('opened_tab_', '')
        document.getElementById(last_select_tab_id).classList.remove('selected_channel_tab');
        document.getElementById(this.id).classList.add('selected_channel_tab');
        id_tab = 'opened_tab_' + this.id.replace('select_tab_', '');
        $('#' + id_tab).css('display', 'flex');
        current_tab = id_tab;
    });
});
fileInput.addEventListener('change', function (event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();

        reader.onload = function (e) {
            file_avatar_channel = e.target.result;
            previewImg.src = e.target.result;
            preview.style.display = 'flex';
        }

        reader.readAsDataURL(file);
    }
});

removeButton.addEventListener('click', function () {
    fileInput.value = ''; // Очистка input
    preview.style.display = 'none'; // Скрытие предпросмотра
    previewImg.src = ''; // Очистка src изображения
});

new_fileInput.addEventListener('change', function (event) {
    const new_file = event.target.files[0];
    if (new_file) {
        const new_reader = new FileReader();

        new_reader.onload = function (e) {
            new_file_avatar_channel = e.target.result;
            new_previewImg.src = e.target.result;
            new_preview.style.display = 'flex';
        }

        new_reader.readAsDataURL(new_file);
    }
});

new_removeButton.addEventListener('click', function () {
    new_fileInput.value = ''; // Очистка input
    new_preview.style.display = 'none'; // Скрытие предпросмотра
    new_previewImg.src = ''; // Очистка src изображения
});

const socket = new WebSocket('ws://localhost:8888');
socket.onopen = function (event) {
    console.log('Подключено к WebSocket серверу');
};
socket.onmessage = function (event) {
    event_data = JSON.parse(event.data);
    console.log("🚀 ~ event_data:", event_data)
};
socket.onclose = function (event) {
    console.log('Соединение закрыто');
};
function sendMessage(message) {
    socket.send(message);
}
$('#create_channel_button').on('click', function () {
    data = {
        'file_avatar_channel': [file_avatar_channel],
        'channel_unique_name': $('#channel_unique_name').val(),
        'channel_name': $('#channel_name').val(),
        'type_message': 'new_channel'
    }
    sendMessage(JSON.stringify(data));
    $('#channel_unique_name').val('');
    $('#channel_name').val('');
    fileInput.value = ''; // Очистка input
    preview.style.display = 'none'; // Скрытие предпросмотра
    previewImg.src = ''; // Очистка src изображения
});
$('#update_channel_button').on('click', function () {
    data = {
        'file_avatar_channel': [new_file_avatar_channel],
        'channel_unique_name': $('#new_channel_unique_name').val(),
        'channel_name': $('#new_channel_name').val(),
        'id_channel': current_update_channel_id,
        'type_message': 'update_channel'
    }
    sendMessage(JSON.stringify(data));
    $('#new_channel_unique_name').val('');
    $('#new_channel_name').val('');
    new_fileInput.value = ''; // Очистка input
    new_preview.style.display = 'none'; // Скрытие предпросмотра
    new_previewImg.src = ''; // Очистка src изображения
});
// '/get_all_subscriptions'
$.ajax({
    url: '/get_all_subscriptions',
    type: 'get',
    success: function (response) {
        console.log(response);
        response['channels'].forEach(element => {
            if (element != null) {
                tab =
                    `<div class="subscription_tab" id="subscription_tab_` + element['id'] + `">
                    <div class="left_side_subscription_tab">
                        <img src="`+ element['channel_avatar'] + `">
                    </div>
                    <div class="right_side_subscription_tab">
                        <div class="channel_name">
                            <p>`+ element['name'] + `</p>
                        </div>
                        <div class="last_post">
                            <div class="last_post_text">
                                <p>Это последний пост в этом шикарном канале</p>
                            </div>
                            <div class="last_post_time">
                                <p>12.12.1212</p>

                            </div>
                        </div>
                    </div>
                </div>`;
                $('#my_subscriptions_list').append(tab);
                $('#subscription_tab_' + element['id']).click(function () {
                    $('.opened_channel_subscription_header_avatar_img').attr('src', element['channel_avatar']);
                    $('.text_channel_info_name').text(element['name']);
                    // $(this).css('pointer-events', 'none');
                    $('.text_channel_count_subscribers').text(element['subscribes_count'] + ' Папищиков');
                    $('.opened_channel_subscription_posts').empty();
                    $('.opened_channel_subscription').css('display', 'flex');
                    opened_channel_id = element['id'];
                    $('.unsubscribe_channel_button').click(function () {
                        data = {
                            'type_message': 'unsubscribe_channel',
                            'channel_id': opened_channel_id,
                        };
                        sendMessage(JSON.stringify(data));
                        $('.opened_channel_subscription').css('display', 'none');
                        opened_channel_id = null;
                        $('#subscription_tab_' + element['id']).remove();

                    });

                    all_comments_is_open = {};
                    all_photos_tab = {};
                    viewed_posts_id = [];
                    last_posts_comments = {};
                    comments_loading_flags = {};
                    function loadChannelPosts() {
                        if (loading) return; // если данные уже загружаются, не выполнять запрос
                        loading = true; // устанавливаем флаг загрузки
                        $.ajax({
                            url: '/get_channels_posts',
                            type: 'GET',
                            data: {
                                page: page,
                                limit: limit,
                                current_channel: opened_channel_id,
                            },
                            success: function (data) {
                                console.log(data);
                                if (data.data.data.length > 0) {
                                    posts = data.data.data;
                                    liked_post = data.liked_posts;
                                    console.log("🚀 ~ loadChannelPosts ~ liked_post:", liked_post)
                                    posts.forEach(post => {
                                        if (post['type_media'] == 'video') {
                                            console.log('Это видео');
                                            const url = '/storage/' + post['text'];
                                            post_text = '';
                                            photos = JSON.parse(post['photos']);
                                            console.log(photos);
                                            post_tab =
                                                `<div class="user_post_tab " id="user_post_tab_` + post['id'] + `">
                                                <div class="user_post_rect">
                                                    <div class="post_header ">
                                                        <img class="post_image "
                                                            src="`+ element['channel_avatar'] + `">
                                                        <div class="post_author ">
                                                            `+ element['name'] + `
                                                        </div>
                                                        <div class="post_date " id="post_date_`+ post['id'] + `">
                                                            `+ create_at + `
                                                        </div>
                                                    </div>
                                                    <div class="post_data_rect">
                                                        <div class="post_data">
                                                            <div class="post_data_images">
                                                            <video controls src="/storage/`+ photos[0] + `" id='video_` + post['id'] + `'></video>
                                                            </div>
                                                            <div class="post_text" id="post_text_`+ post['id'] + `">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="post_footer">
                                                        <div class="post_likes" id="post_likes_`+ post['id'] + `">
                                                            Мне нравится
                                                        </div>
                                                        <div class="post_likes_counter" id="post_likes_counter_`+ post['id'] + `">
                                                            Лайки: `+ post['likes_count'] + `
                                                        </div>
                                                        <div class="post_watchers" id="post_watchers_`+ post['id'] + `">
                                                            Просмотры: `+ post['views'] + `
                                                        </div>
                                                    </div>
                                                    <div class="open_comments open_comments_`+ post['id'] + `"  >
                                                        Открыть комментарии
                                                    </div>
                                                    <div class="comments_plane comments_plane_`+ post['id'] + `">

                                                    </div>
                                                    <div class="post_comments">
                                                        <div class="post_comments_input">
                                                            <textarea id="input_comment_`+ post['id'] + `"  class="text_input_comment" name="text"
                                                                oninput='this.style.height = "";this.style.height = this.scrollHeight + "px";'></textarea>

                                                            <div class="div_send_comment">
                                                                <div class="send_comment" id="send_comment_`+ post['id'] + `">
                                                                    Отправить
                                                                </div>
                                                            </div>
                                                        </div>


                                                    </div>
                                                </div>
                                            </div>`;
                                            $('.opened_channel_subscription_posts').append(post_tab);
                                            fetch(url)
                                                .then(response => {
                                                    if (!response.ok) {
                                                        post_text = '<p>Произошла ошибка загрузки :(</p>';
                                                        $('#post_text_' + post['id']).append(post_text);
                                                        throw new Error('Сеть ответила с ошибкой: ' + response.status);
                                                    }
                                                    return response.text(); // Получаем текст из ответа
                                                })
                                                .then(text => {
                                                    post_text = '<p>' + text + '</p>';
                                                    $('#post_text_' + post['id']).append(post_text);
                                                })
                                                .catch(error => {
                                                    console.error('Произошла ошибка:', error);
                                                });
                                            const open_comments = document.querySelector('.open_comments_' + post['id']);
                                            all_comments_is_open[post['id']] = false;

                                            function load_post_comments(isFirst = false) {
                                                if (load_post_comments[post['id']]) return;
                                                load_post_comments[post['id']] = true;
                                                $.ajax({
                                                    url: '/load_post_comments',
                                                    type: 'get',
                                                    data: {
                                                        post_id: post['id'],
                                                        page: last_posts_comments[post['id']],
                                                    },
                                                    success: function (comment_load_result) {
                                                        res = comment_load_result['data']['data'];
                                                        users = comment_load_result['users'];
                                                        if (res.length > 0) {
                                                            $('#not_comments_' + post['id']).remove();
                                                            res.forEach(comment => {
                                                                current_post_author = users[comment['author_id']];
                                                                post_created_at = new Date(comment['created_at']);
                                                                comment_tab =
                                                                    `<div class="comment_tab comment_tab_` + comment['id'] + `">
                                                                            <div class="comment comment_`+ comment['id'] + `">
                                                                                <div class="comentatr_avatar">
                                                                                    <img
                                                                                        src="`+ current_post_author['avatar'] + `">
                                                                                </div>
                                                                                <div class="commentor_name_comment_text">
                                                                                    <div class='about_post'>
                                                                                        <div class="commentator_name">
                                                                                            `+ current_post_author['name'] + ` ` + current_post_author['lastname'] + `
                                                                                        </div>
                                                                                    <div class='post_time'>`+ String(post_created_at.getDate()).padStart(2, '0') + `.` + String(post_created_at.getMonth() + 1).padStart(2, '0') + `.` + post_created_at.getFullYear() + ` ` + String(post_created_at.getHours()).padStart(2, '0') + `:` + String(post_created_at.getMinutes()).padStart(2, '0') + `</div>
                                                                                    </div>

                                                                                    <div class="comment_text">
                                                                                        <p>
                                                                                            `+ comment['text'] + `
                                                                                        </p>
                                                                                    </div>
                                                                                </div>


                                                                            </div>
                                                                        </div>`;
                                                                $('.comments_plane_' + post['id']).append(comment_tab);
                                                            });
                                                            last_posts_comments[post['id']] = last_posts_comments[post['id']] + 1;
                                                        }
                                                        else {
                                                            if (isFirst) {
                                                                $('.not_comments_' + post['id']).remove();
                                                                not_comments_tab =
                                                                    ` <div class='not_comments not_comments_` + post['id'] + `' id="not_comments_` + post['id'] + `" >
                                                                            Под этим постом ещё нет комметрариев :)
                                                                        </div>`;
                                                                $('.comments_plane_' + post['id']).append(not_comments_tab);
                                                            }
                                                        }
                                                    },
                                                    error: function (xhr) {
                                                        console.error('Error:', xhr);
                                                        alert('Произошла ошибка: ' + xhr.responseJSON.message);
                                                    },
                                                    complete: function () {
                                                        load_post_comments[post['id']] = false;
                                                    }
                                                })
                                            }
                                            $(document.querySelector('.comments_plane_' + post['id'])).on('scroll', function () {
                                                if ($(this).scrollTop() + $(this).innerHeight() >= this.scrollHeight - 20) { // добавляем небольшой отступ
                                                    load_post_comments(); // загружаем данные при достижении конца контейнера
                                                }
                                            });

                                            // send_comment_
                                            $('#send_comment_' + post['id']).on('click', function () {
                                                value_text = $('#input_comment_' + post['id']).val();
                                                datenow = new Date();
                                                comment_guid = generateGUID();
                                                comment_tab =
                                                    `<div class="comment_tab comment_tab_` + comment_guid + `">
                                                                <div class="comment comment_`+ comment_guid + `">
                                                                    <div class="comentatr_avatar">
                                                                        <img
                                                                            src="`+ this_user['avatar'] + `">
                                                                    </div>
                                                                    <div class="commentor_name_comment_text">
                                                                        <div class='about_post'>
                                                                            <div class="commentator_name">
                                                                                `+ this_user['name'] + ` ` + this_user['lastname'] + `
                                                                            </div>
                                                                        <div class='post_time'>`+ String(datenow.getDate()).padStart(2, '0') + `.` + String(datenow.getMonth() + 1).padStart(2, '0') + `.` + datenow.getFullYear() + ` ` + String(datenow.getHours()).padStart(2, '0') + `:` + String(datenow.getMinutes()).padStart(2, '0') + `</div>
                                                                        </div>

                                                                        <div class="comment_text">
                                                                            <p>
                                                                                `+ value_text + `
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>`;
                                                $('.comments_plane_' + post['id']).prepend(comment_tab);
                                                // $(document.querySelector('.comments_plane_' + post['id']))
                                                // post_id = post_div.id.replace('user_post_tab_', '');
                                                data = {
                                                    'post_id': post['id'],
                                                    'comment_guid': comment_guid,
                                                    'comment_text': value_text,
                                                    'author_id': this_user_id,
                                                    'type_message': 'new_comment_post',
                                                }
                                                sendMessage(JSON.stringify(data));

                                                $('#input_comment_' + post['id']).val('');
                                            })


                                            open_comments.addEventListener('click', function () {
                                                if (all_comments_is_open[post['id']]) {
                                                    document.querySelector('.open_comments_' + post['id']).classList.remove('close_comments');
                                                    document.querySelector('.open_comments_' + post['id']).innerText = 'Показать комментарии';
                                                    $(document.querySelector('.comments_plane_' + post['id'])).css('display', 'none');
                                                    all_comments_is_open[post['id']] = false;
                                                }
                                                else {
                                                    document.querySelector('.open_comments_' + post['id']).classList.add('close_comments');
                                                    document.querySelector('.open_comments_' + post['id']).innerText = 'Скрыть комметарии';
                                                    $(document.querySelector('.comments_plane_' + post['id'])).css('display', 'flex');
                                                    all_comments_is_open[post['id']] = true;
                                                    load_post_comments(true)
                                                }
                                            });
                                            const all_posts_div = document.querySelector('.opened_channel_subscription_posts');
                                            const posts_divs = document.querySelectorAll('.user_post_tab');
                                            function checkVisibility() {
                                                posts_divs.forEach(post_div => {
                                                    const childRect = post_div.getBoundingClientRect();
                                                    const parentRect = all_posts_div.getBoundingClientRect();
                                                    const childVisibleHeight = Math.max(0, Math.min(childRect.bottom, parentRect.bottom) - Math.max(childRect.top, parentRect.top));
                                                    const halfChildHeight = post_div.offsetHeight / 2;
                                                    if (childVisibleHeight >= halfChildHeight && !viewed_posts_id.includes(post_div.id)) {
                                                        viewed_posts_id.push(post_div.id);
                                                        yourFunction(post_div);
                                                    }
                                                });
                                            }
                                            function yourFunction(post_div) {

                                                post_id = post_div.id.replace('user_post_tab_', '');
                                                data = {
                                                    'post_id': post_id,
                                                    'type_message': 'new_post_view',
                                                }
                                                sendMessage(JSON.stringify(data));
                                            }
                                            all_posts_div.addEventListener('scroll', checkVisibility);
                                            return;
                                        }
                                        photos = JSON.parse(post['photos']);
                                        const url = '/storage/' + post['text'];
                                        post_text = '';
                                        const date = new Date(post['created_at']);
                                        create_at = String(date.getDate()).padStart(2, '0') + '.' + String(date.getMonth() + 1).padStart(2, '0') + '.' + String(date.getFullYear()).padStart(2, '0') + ' ' + String(date.getHours()).padStart(2, '0') + ':' + String(date.getMinutes()).padStart(2, '0');
                                        post_tab =
                                            `<div class="user_post_tab " id="user_post_tab_` + post['id'] + `">
                                            <div class="user_post_rect">
                                                <div class="post_header ">
                                                    <img class="post_image "
                                                        src="`+ element['channel_avatar'] + `">
                                                    <div class="post_author ">
                                                        `+ element['name'] + `
                                                    </div>
                                                    <div class="post_date " id="post_date_`+ post['id'] + `">
                                                        `+ create_at + `
                                                    </div>
                                                </div>
                                                <div class="post_data_rect">
                                                    <div class="post_data">
                                                        <div class="post_data_images">
                                                            <div class="post_data_image" id="post_data_image_`+ post['id'] + `">
                                                            </div>
                                                        </div>
                                                        <div class="post_text" id="post_text_`+ post['id'] + `">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="post_footer">
                                                    <div class="post_likes" id="post_likes_`+ post['id'] + `">
                                                        Мне нравится
                                                    </div>
                                                    <div class="post_likes_counter" id="post_likes_counter_`+ post['id'] + `">
                                                        Лайки: `+ post['likes_count'] + `
                                                    </div>
                                                    <div class="post_watchers" id="post_watchers_`+ post['id'] + `">
                                                        Просмотры: `+ post['views'] + `
                                                    </div>
                                                </div>
                                                <div class="open_comments open_comments_`+ post['id'] + `"  >
                                                    Открыть комментарии
                                                </div>
                                                <div class="comments_plane comments_plane_`+ post['id'] + `">

                                                </div>
                                                <div class="post_comments">
                                                    <div class="post_comments_input">
                                                        <textarea id="input_comment_`+ post['id'] + `"  class="text_input_comment" name="text"
                                                            oninput='this.style.height = "";this.style.height = this.scrollHeight + "px";'></textarea>

                                                        <div class="div_send_comment">
                                                            <div class="send_comment" id="send_comment_`+ post['id'] + `">
                                                                Отправить
                                                            </div>
                                                        </div>
                                                    </div>


                                                </div>
                                            </div>
                                        </div>`;
                                        $('.opened_channel_subscription_posts').append(post_tab);
                                        if (liked_post[post['id']]) {
                                            document.getElementById('post_likes_' + post['id']).classList.add('liked_post');
                                        }
                                        fetch(url)
                                            .then(response => {
                                                if (!response.ok) {
                                                    post_text = '<p>Произошла ошибка загрузки :(</p>';
                                                    $('#post_text_' + post['id']).append(post_text);
                                                    throw new Error('Сеть ответила с ошибкой: ' + response.status);
                                                }
                                                return response.text(); // Получаем текст из ответа
                                            })
                                            .then(text => {
                                                post_text = '<p>' + text + '</p>';
                                                $('#post_text_' + post['id']).append(post_text);
                                            })
                                            .catch(error => {
                                                console.error('Произошла ошибка:', error);
                                            });
                                        counter = 0;
                                        photos.forEach(photo => {
                                            img = `<img class='post_data_image_carousel post_` + post['id'] + `_data_image_carousel'
                                                        id='post_`+ post['id'] + `_data_image_carousel_` + counter + `'
                                                        src="/storage/`+ photo + `">`;
                                            $('#post_data_image_' + post['id']).append(img);
                                            counter++;
                                        });
                                        all_new_photos = document.querySelectorAll('.post_' + post["id"] + '_data_image_carousel');
                                        all_photos_tab[post['id']] = { 'current_img': 0, 'last_img': 0, 'max_img': all_new_photos.length - 1 };
                                        last_posts_comments[post['id']] = 1;
                                        comments_loading_flags[post['id']] = false;
                                        const elements = document.querySelectorAll('.post_' + post['id'] + '_data_image_carousel');
                                        for (let i = 1; i < elements.length; i++) {
                                            id = elements[i].id;
                                            $("#" + id).css('display', 'none');
                                        }
                                        function swap_image(new_imgae_id, image_id, post_id) {
                                            $('#post_' + post_id + '_data_image_carousel_' + image_id).css('display', 'none');
                                            $('#post_' + post_id + '_data_image_carousel_' + new_imgae_id).css('display', 'block');
                                        }
                                        function handleLeftClick(id_elem) {
                                            // console.log(id_elem);
                                            if (all_photos_tab[post['id']]['current_img'] == 0) {
                                                all_photos_tab[post['id']]['last_img'] = all_photos_tab[post['id']]['current_img'];
                                                all_photos_tab[post['id']]['current_img'] = all_photos_tab[post['id']]['max_img'];
                                            }
                                            else {
                                                all_photos_tab[post['id']]['last_img'] = all_photos_tab[post['id']]['current_img'];
                                                all_photos_tab[post['id']]['current_img']--;
                                            }
                                            swap_image(all_photos_tab[post['id']]['current_img'], all_photos_tab[post['id']]['last_img'], post['id']);
                                        }
                                        function handleRightClick(id_elem) {
                                            // console.log(id_elem);
                                            if (all_photos_tab[post['id']]['current_img'] == all_photos_tab[post['id']]['max_img']) {
                                                all_photos_tab[post['id']]['last_img'] = all_photos_tab[post['id']]['current_img'];
                                                all_photos_tab[post['id']]['current_img'] = 0;
                                            }
                                            else {
                                                all_photos_tab[post['id']]['last_img'] = all_photos_tab[post['id']]['current_img'];
                                                all_photos_tab[post['id']]['current_img']++;
                                            }
                                            swap_image(all_photos_tab[post['id']]['current_img'], all_photos_tab[post['id']]['last_img'], post['id']);
                                        }
                                        elements.forEach(element => {
                                            element.addEventListener('click', (event) => {
                                                const rect = element.getBoundingClientRect(); // Получаем размеры и позицию блока
                                                const clickX = event.clientX - rect.left; // Координата X клика относительно блока
                                                const halfWidth = rect.width / 2; // Половина ширины блока

                                                if (clickX < halfWidth / 2) {
                                                    handleLeftClick(element.id); // Если клик был на левой половине
                                                }
                                                if (clickX > (3 * halfWidth) / 2) {
                                                    handleRightClick(element.id); // Если клик был на правой половине
                                                }
                                            });
                                        });
                                        const open_comments = document.querySelector('.open_comments_' + post['id']);
                                        all_comments_is_open[post['id']] = false;

                                        function load_post_comments(isFirst = false) {
                                            if (load_post_comments[post['id']]) return;
                                            load_post_comments[post['id']] = true;
                                            $.ajax({
                                                url: '/load_post_comments',
                                                type: 'get',
                                                data: {
                                                    post_id: post['id'],
                                                    page: last_posts_comments[post['id']],
                                                },
                                                success: function (comment_load_result) {
                                                    res = comment_load_result['data']['data'];
                                                    users = comment_load_result['users'];
                                                    if (res.length > 0) {
                                                        $('#not_comments_' + post['id']).remove();
                                                        res.forEach(comment => {
                                                            current_post_author = users[comment['author_id']];
                                                            post_created_at = new Date(comment['created_at']);
                                                            comment_tab =
                                                                `<div class="comment_tab comment_tab_` + comment['id'] + `">
                                                                    <div class="comment comment_`+ comment['id'] + `">
                                                                        <div class="comentatr_avatar">
                                                                            <img
                                                                                src="`+ current_post_author['avatar'] + `">
                                                                        </div>
                                                                        <div class="commentor_name_comment_text">
                                                                            <div class='about_post'>
                                                                                <div class="commentator_name">
                                                                                    `+ current_post_author['name'] + ` ` + current_post_author['lastname'] + `
                                                                                </div>
                                                                            <div class='post_time'>`+ String(post_created_at.getDate()).padStart(2, '0') + `.` + String(post_created_at.getMonth() + 1).padStart(2, '0') + `.` + post_created_at.getFullYear() + ` ` + String(post_created_at.getHours()).padStart(2, '0') + `:` + String(post_created_at.getMinutes()).padStart(2, '0') + `</div>
                                                                            </div>

                                                                            <div class="comment_text">
                                                                                <p>
                                                                                    `+ comment['text'] + `
                                                                                </p>
                                                                            </div>
                                                                        </div>


                                                                    </div>
                                                                </div>`;
                                                            $('.comments_plane_' + post['id']).append(comment_tab);
                                                        });
                                                        last_posts_comments[post['id']] = last_posts_comments[post['id']] + 1;
                                                    }
                                                    else {
                                                        if (isFirst) {
                                                            $('.not_comments_' + post['id']).remove();
                                                            not_comments_tab =
                                                                ` <div class='not_comments not_comments_` + post['id'] + `' id="not_comments_` + post['id'] + `" >
                                                                    Под этим постом ещё нет комметрариев :)
                                                                </div>`;
                                                            $('.comments_plane_' + post['id']).append(not_comments_tab);
                                                        }
                                                    }
                                                },
                                                error: function (xhr) {
                                                    console.error('Error:', xhr);
                                                    alert('Произошла ошибка: ' + xhr.responseJSON.message);
                                                },
                                                complete: function () {
                                                    load_post_comments[post['id']] = false;
                                                }
                                            })
                                        }
                                        $(document.querySelector('.comments_plane_' + post['id'])).on('scroll', function () {
                                            if ($(this).scrollTop() + $(this).innerHeight() >= this.scrollHeight - 20) { // добавляем небольшой отступ
                                                load_post_comments(); // загружаем данные при достижении конца контейнера
                                            }
                                        });

                                        // send_comment_
                                        $('#send_comment_' + post['id']).on('click', function () {
                                            value_text = $('#input_comment_' + post['id']).val();
                                            datenow = new Date();
                                            comment_guid = generateGUID();
                                            comment_tab =
                                                `<div class="comment_tab comment_tab_` + comment_guid + `">
                                                        <div class="comment comment_`+ comment_guid + `">
                                                            <div class="comentatr_avatar">
                                                                <img
                                                                    src="`+ this_user['avatar'] + `">
                                                            </div>
                                                            <div class="commentor_name_comment_text">
                                                                <div class='about_post'>
                                                                    <div class="commentator_name">
                                                                        `+ this_user['name'] + ` ` + this_user['lastname'] + `
                                                                    </div>
                                                                <div class='post_time'>`+ String(datenow.getDate()).padStart(2, '0') + `.` + String(datenow.getMonth() + 1).padStart(2, '0') + `.` + datenow.getFullYear() + ` ` + String(datenow.getHours()).padStart(2, '0') + `:` + String(datenow.getMinutes()).padStart(2, '0') + `</div>
                                                                </div>

                                                                <div class="comment_text">
                                                                    <p>
                                                                        `+ value_text + `
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>`;
                                            $('.comments_plane_' + post['id']).prepend(comment_tab);
                                            // $(document.querySelector('.comments_plane_' + post['id']))
                                            // post_id = post_div.id.replace('user_post_tab_', '');
                                            data = {
                                                'post_id': post['id'],
                                                'comment_guid': comment_guid,
                                                'comment_text': value_text,
                                                'author_id': this_user_id,
                                                'type_message': 'new_comment_post',
                                            }
                                            sendMessage(JSON.stringify(data));

                                            $('#input_comment_' + post['id']).val('');
                                        })


                                        open_comments.addEventListener('click', function () {
                                            if (all_comments_is_open[post['id']]) {
                                                document.querySelector('.open_comments_' + post['id']).classList.remove('close_comments');
                                                document.querySelector('.open_comments_' + post['id']).innerText = 'Показать комментарии';
                                                $(document.querySelector('.comments_plane_' + post['id'])).css('display', 'none');
                                                all_comments_is_open[post['id']] = false;
                                            }
                                            else {
                                                document.querySelector('.open_comments_' + post['id']).classList.add('close_comments');
                                                document.querySelector('.open_comments_' + post['id']).innerText = 'Скрыть комметарии';
                                                $(document.querySelector('.comments_plane_' + post['id'])).css('display', 'flex');
                                                all_comments_is_open[post['id']] = true;
                                                load_post_comments(true)
                                            }
                                        });
                                        const all_posts_div = document.querySelector('.opened_channel_subscription_posts');
                                        const posts_divs = document.querySelectorAll('.user_post_tab');
                                        function checkVisibility() {
                                            posts_divs.forEach(post_div => {
                                                const childRect = post_div.getBoundingClientRect();
                                                const parentRect = all_posts_div.getBoundingClientRect();
                                                const childVisibleHeight = Math.max(0, Math.min(childRect.bottom, parentRect.bottom) - Math.max(childRect.top, parentRect.top));
                                                const halfChildHeight = post_div.offsetHeight / 2;
                                                if (childVisibleHeight >= halfChildHeight && !viewed_posts_id.includes(post_div.id)) {
                                                    viewed_posts_id.push(post_div.id);
                                                    yourFunction(post_div);
                                                }
                                            });
                                        }
                                        function yourFunction(post_div) {

                                            post_id = post_div.id.replace('user_post_tab_', '');
                                            data = {
                                                'post_id': post_id,
                                                'type_message': 'new_post_view',
                                            }
                                            sendMessage(JSON.stringify(data));
                                        }
                                        all_posts_div.addEventListener('scroll', checkVisibility);
                                    });
                                    const like_buttons = document.querySelectorAll('.post_likes');
                                    console.log("🚀 ~ loadChannelPosts ~ like_buttons:", like_buttons)
                                    like_buttons.forEach(like_button => {
                                        post_id = like_button.id.replace('post_likes_', '');
                                        console.log("🚀 ~ loadChannelPosts ~ post_id:", post_id)
                                        like_button.addEventListener('click', function () {
                                            post_id = like_button.id.replace('post_likes_', '');
                                            data = {
                                                'user_id': this_user_id,
                                                'post_id': post_id,
                                                'type_like': 'post_like',
                                                'type_message': 'new_post_like',
                                            }
                                            sendMessage(JSON.stringify(data));
                                            like_button.innerText = 'Вы лайкнули';
                                            likes_count_current = Number(document.getElementById('post_likes_counter_' + post_id).innerText.replace('Лайки: ', '')) + 1;
                                            document.getElementById('post_likes_counter_' + post_id).innerText = 'Лайки: ' + likes_count_current;
                                            like_button.classList.add('liked_post');
                                        })
                                    });
                                    console.log(posts);
                                    page++; // Увеличиваем номер страницы для следующего запроса
                                } else {
                                    console.log('Нема');
                                    $('.opened_channel_subscription_posts').off('scroll');
                                    // $('#load-more').hide(); // Скрываем кнопку, если больше нет постов
                                }
                            },
                            error: function () {
                                alert('Ошибка загрузки постов.');
                            },
                            complete: function () {
                                loading = false; // сбрасываем флаг загрузки после завершения запроса
                            }
                        });
                    }

                    // $(document).ready(function () {
                    loadChannelPosts(); // Загружаем первые посты

                    // $('#load-more').click(function () {
                    //     loadPosts(); // Загружаем еще посты при нажатии на кнопку
                    // });
                    $(document.querySelector('.opened_channel_subscription_posts')).on('scroll', function () {
                        if ($(this).scrollTop() + $(this).innerHeight() >= this.scrollHeight - 20) { // добавляем небольшой отступ
                            loadChannelPosts(); // загружаем данные при достижении конца контейнера
                        }
                    });
                    // });
                    // '/get_channels_posts'
                    // alert(element['id']);
                });
            }
            // String(date.getDate()).padStart(2, '0') + '.' + String(date.getMonth() + 1).padStart(2, '0') + '.' + String(date.getFullYear()).padStart(2, '0') + ' ' + String(date.getHours()).padStart(2, '0') + ':' + String(date.getMinutes()).padStart(2, '0')

        });
    },
    error: function (xhr) {
        console.error('Error:', xhr);
        alert('Произошла ошибка: ' + xhr.responseJSON.message);
    }
});
$.ajax({
    url: '/get_channels',
    type: 'get',
    success: function (response) {
        all_chanels = {};
        all_chanels_id = [];
        channels = response['channels'];
        console.log(channels.length);
        if (channels.length != 0) {
            $('#select_tab_3').css('display', 'block')
        }
        channels.forEach(channel => {
            console.log(all_chanels);
            all_chanels_id.push(channel['id']);
            all_chanels[channel['id']] = { 'name': channel['name'], 'unique_name': channel['channel_name'], 'avatar': channel['channel_avatar'] }
            channel_select_tab = ` <option class="select_your_channel_tab " value="` + channel['id'] + `" >` + channel['name'] + `</option>`;
            $(select_channel).append(channel_select_tab);
            channel_tab =
                `<div class="my_channel_tab" id="my_channel_tab_` + channel['id'] + `">
                <div class="left_side_my_channel_tab">
                    <img src="`+ channel['channel_avatar'] + `">
                </div>
                <div class="right_side_my_channel_tab">
                    <div class="channel_name_channel_tab">
                        <div class="channel_names">
                            <p>`+ channel['name'] + `</p>
                            <p id="`+ channel['channel_name'] + `" class="unique_channel_name">@` + channel['channel_name'] + `</p>
                        </div>
                        <div class='channel_settings_tab'>
                            <p class='channel_settings ' id="channel_settings_`+ channel['id'] + `">Управление каналом</p>
                        </div>
                    </div>
                </div>
            </div>`;
            $('.my_channels_list').prepend(channel_tab);
            // document.getElementById
            $('#' + channel['channel_name']).on('click', function () {
                navigator.clipboard.writeText(this.innerText)
                    .then(() => {
                        alert('Имя канала скопировано');
                    })
                    .catch(err => {
                        console.error('Произошла ошибка');
                    });
            });
            $('#channel_settings_' + channel['id']).on('click', function () {
                channel_id_settings = channel['id'];
                $('#third_plane').text('Настройки канала @' + channel['channel_name']);
                $('.channel_settings_inputs_tab').css('display', 'block');
                current_update_channel_id = channel['id'];
                console.log("🚀 ~ current_update_channel_id:", current_update_channel_id);
            })
        });
        select_channel.value = all_chanels_id[0];
        $('.post_channel_author').text(all_chanels[select_channel.value]['name']);
        $('.create_channel_post_header_image').attr('src', all_chanels[select_channel.value]['avatar']);
        select_channel.addEventListener('change', function () {
            $('.post_channel_author').text(all_chanels[select_channel.value]['name']);
            $('.create_channel_post_header_image').attr('src', all_chanels[select_channel.value]['avatar']);
            selected_channal_for_post = select_channel.value;
        });


    },
    error: function (xhr) {
        console.error('Error:', xhr);
        alert('Произошла ошибка: ' + xhr.responseJSON.message);
    }
});

function generateGUID() {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
        const r = Math.random() * 16 | 0;
        const v = c === 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);
    });
}
all_files = {};
all_files_links = {};
const date = new Date();
create_at = String(date.getDate()).padStart(2, '0') + '.' + String(date.getMonth() + 1).padStart(2, '0') + '.' + String(date.getFullYear()).padStart(2, '0') + ' ' + String(date.getHours()).padStart(2, '0') + ':' + String(date.getMinutes()).padStart(2, '0');
$('.post_channel_date').text(create_at);
$('.post_channel_author').text()
$('#upload_images').on('change', function () {
    var files = this.files;
    function del_photo_tab(event) {
        $('#li_' + this.guid).remove();
        delete all_files[this.guid];
        delete all_files_links[this.guid];
    }
    for (var i = 0; i < files.length; i++) {
        var file = files[i];
        var reader = new FileReader();
        new_guid = generateGUID();
        all_files[new_guid] = file;
        reader.onload = function (e) {
            all_files_links[new_guid] = e.target.result;
            var li =
                `<li id="li_` + new_guid + `">
                <img  class="photo_list" src="`+ e.target.result + `" id="img_` + new_guid + `">
                <div class="overlay" id="del_`+ new_guid + `">
                    <span>
                        Удалить
                    </span>
                </div>
            </li>`;
            $('#image_gallery').append(li);
            document.getElementById('del_' + new_guid).addEventListener('click', { handleEvent: del_photo_tab, guid: new_guid });
        };
        reader.readAsDataURL(file);
    }
    this.value = null;
});
$('.publish_channel_post_button').on('click', function () {
    if (Object.keys(all_files).length > 0 || $('.input_post_text').val().trim() != '') {
        data = {
            'type_message': 'new_post',
            'post_author': document.getElementById('select_channel').value,
            'post_text': $('.textarea_input_channel_post_text').val(),
            'post_photo': all_files_links,
            'type_post': 'channel_post',
        };
        sendMessage(JSON.stringify(data));
        all_files = {};
        all_files_links = {};
        $('#image_gallery').empty();
        $('.textarea_input_channel_post_text').val('');
        alert('Пост опубликован!');
    }
    else {
        alert('Вы не ввели данные, без них не получится опубликовать ваш пост :(');
    }
});
$('.serarch_channel_button').on('click', function () {
    value = $('#searched_channel_name').val().trim();
    if (value != '') {
        function redirect_to_subscriptions() {
            $('#select_tab_0').css('background', 'rgba(0,0,0,0)');
            $('#select_tab_0').css('color', '#fff');
        }
        // console.log(value);
        $('#searched_channel_name').val('');
        $.ajax({
            url: '/search_channels',
            type: 'get',
            data: { 'data': value },
            success: function (response) {
                searched_channels = {};
                channel_id = new Set();
                console.log(response);
                is_subscribe = [];
                response['data'].forEach(element => {
                    element.forEach(channel => {
                        console.log(channel);
                        console.log(channel['id']);
                        channel_id.add(channel['id']);
                        searched_channels[channel['id']] = channel;
                    });
                });
                channel_id = [...channel_id];
                if (channel_id.length > 0) {
                    $.ajax({
                        url: '/is_subscribe',
                        type: 'get',
                        data: { 'data': channel_id },
                        async: false,
                        success: function (is_sub) {
                            // console.log(is_sub);
                            is_subscribe = is_sub['data'];
                        },
                        error: function (xhr1) {
                            console.error('Error:', xhr1);
                            alert('Произошла ошибка: ' + xhr1.responseJSON.message);
                        }
                    });
                }

                channel_id.forEach(channel => {
                    if (is_subscribe[channel] == false) {
                        tab =
                            `<div class="search_channel_result_tab border_debug" id="search_channel_result_tab_` + channel + `">
                            <div class="search_channel_result_photo">
                                <img src="`+ searched_channels[channel]['channel_avatar'] + `">
                            </div>
                            <div class="search_channel_result_right_side">
                                <div class="search_channel_result_name">`+ searched_channels[channel]['name'] + `</div>
                                <div class="search_channel_result_options">
                                    <div class="subscibe_channel" id="subscibe_channel_`+ searched_channels[channel]['id'] + `">Подписаться</div>
                                    <div class="open_channel">Открыть</div>
                                </div>
                            </div>
                        </div>`;
                        $('.search_channel_results').append(tab);
                        $('#subscibe_channel_' + channel).on('click', function () {
                            data = {
                                'type_message': 'subscribe_channel',
                                'channel_id': channel,
                            };
                            sendMessage(JSON.stringify(data));
                            $('#search_channel_result_tab_' + channel).remove();
                            var originalColor = $('#select_tab_0').css('background-color');
                            $('#select_tab_0').css('background-color', '#fff');
                            setTimeout(function () {
                                $('#select_tab_0').css('background-color', originalColor);
                            }, 500);
                            $('#select_tab_0').hover(
                                function () {
                                    // При наведении
                                    $(this).css({
                                        'background-color': '#0056b3',
                                        'border': '1px solid transparent' // Увеличиваем элемент
                                    });
                                },
                                function () {
                                    // При уходе мыши
                                    $(this).css({
                                        'background-color': 'transparent',
                                        'border': '1px solid white' // Возвращаем размер
                                    });
                                }
                            );
                            new_subscription_tab =
                                `<div class="subscription_tab" id="subscription_tab_` + channel + `">
                                <div class="left_side_subscription_tab">
                                    <!-- <div class="img_container_subscription_tab"> -->
                                    <img src="`+ searched_channels[channel]['channel_avatar'] + `">
                                    <!-- </div> -->
                                </div>
                                <div class="right_side_subscription_tab">
                                    <div class="channel_name">
                                        <p>`+ searched_channels[channel]['name'] + `</p>
                                    </div>
                                    <div class="last_post">
                                        <div class="last_post_text">

                                        </div>
                                        <div class="last_post_time">
                                            <p>12.12.1212</p>

                                        </div>
                                    </div>

                                </div>
                            </div>`;
                            $('#my_subscriptions_list').prepend(new_subscription_tab);
                            $('#subscription_tab_' + channel).click(function () {
                                $('.opened_channel_subscription_header_avatar_img').attr('src', searched_channels[channel]['channel_avatar']);
                                $('.text_channel_info_name').text(searched_channels[channel]['name']);
                                $('.text_channel_count_subscribers').text((searched_channels[channel]['subscribes_count'] + 1) + ' Папищиков');
                                $('.opened_channel_subscription').css('display', 'flex');
                                opened_channel_id = channel;
                                $('.unsubscribe_channel_button').click(function () {
                                    data = {
                                        'type_message': 'unsubscribe_channel',
                                        'channel_id': opened_channel_id,
                                    };
                                    sendMessage(JSON.stringify(data));
                                    $('.opened_channel_subscription').css('display', 'none');
                                    opened_channel_id = null;
                                    $('#subscription_tab_' + channel).remove();
                                })
                                // alert(element['id']);
                            });

                        });
                    }
                    else {
                        tab =
                            `<div class="search_channel_result_tab border_debug" id="search_channel_result_tab_` + channel + `">
                            <div class="search_channel_result_photo">
                                <img src="`+ searched_channels[channel]['channel_avatar'] + `">
                            </div>
                            <div class="search_channel_result_right_side">
                                <div class="search_channel_result_name">`+ searched_channels[channel]['name'] + `</div>
                                <div class="search_channel_result_options">
                                    <div class="unsubscibe_channel" id="unsubscibe_channel_`+ searched_channels[channel]['id'] + `">Отписаться</div>
                                    <div class="open_channel">Открыть</div>
                                </div>
                            </div>
                        </div>`;
                        $('.search_channel_results').append(tab);
                        $('#unsubscibe_channel_' + channel).on('click', function () {
                            data = {
                                'type_message': 'unsubscribe_channel',
                                'channel_id': channel,
                            };
                            sendMessage(JSON.stringify(data));
                            $('#search_channel_result_tab_' + channel).remove();
                            var originalColor = $('#select_tab_0').css('background-color');
                            $('#select_tab_0').css('background-color', '#fff');
                            setTimeout(function () {
                                $('#select_tab_0').css('background-color', originalColor);
                            }, 500);
                            $('#select_tab_0').hover(
                                function () {
                                    // При наведении
                                    $(this).css({
                                        'background-color': '#0056b3',
                                        'border': '1px solid transparent' // Увеличиваем элемент
                                    });
                                },
                                function () {
                                    // При уходе мыши
                                    $(this).css({
                                        'background-color': 'transparent',
                                        'border': '1px solid white' // Возвращаем размер
                                    });
                                }
                            );
                            if ($(`subscription_tab_` + channel)) {
                                $(`subscription_tab_` + channel).remove();
                            }

                        });
                    }

                });
            },
            error: function (xhr) {
                console.error('Error:', xhr);
                alert('Произошла ошибка: ' + xhr.responseJSON.message);
            }
        });
    }
});
$('#select_type_post').val('post');
$('.create_video_in_channel').css('display', 'none');
$('#select_type_post').change(function () {
    if ($(this).val() == 'video') {
        $('#select_type_video').css('display', 'block');
        $('#select_type_video_header').css('display', 'block');
        $('.create_post_in_channel').css('display', 'none');
        $('.create_video_in_channel').css('display', 'block');
    }
    else {
        $('#select_type_video').css('display', 'none');
        $('#select_type_video_header').css('display', 'none');
        $('.create_post_in_channel').css('display', 'block');
        $('.create_video_in_channel').css('display', 'none');
    }
})
$('#delete_channel_button').click(function () {
    // console.log(channel_id_settings);
    $.ajax({
        url: '/delete_channel',
        type: 'get',
        data: {
            channel_id: current_update_channel_id,
        },
        success: function (response) {
            console.log(response);
            $('#my_channel_tab_' + current_update_channel_id).remove();
        },
        error: function (xhr) {
            console.log(xhr);
        }
    });
});
const videoInput = document.getElementById('videoInput');
const videoPreview = document.getElementById('videoPreview');
file = null;
current_file = null;




$('#videoInput').on('change', function () {
    const file = this.files[0];
    const reader = new FileReader();
    if (file) {
        const fileURL = URL.createObjectURL(file);
        $('#videoPreview').attr('src', fileURL).show();
        current_file = file;
        // Воспроизведение видео после загрузки метаданных
        $('#videoPreview').get(0).load(); // Загружаем видео
        $('#videoPreview').get(0).play(); // Автоматически воспроизводим

        // Очистка URL после завершения воспроизведения
        $('#videoPreview').on('ended', function () {
            URL.revokeObjectURL(fileURL);
            // fileURL = null;
        });
    }
    reader.onload = function (e) {
        // console.log(e.target.result);
        current_file_video = e.target.result;
    }

    reader.readAsDataURL(file);
});
$('.publish_channel_video_button').click(function () {
    if ($('.video_name').val().trim() != '') {
        console.log($('#select_channel').val());
        data = {
            'channel_id': $('#select_channel').val(),
            'type_message': 'new_video_channel_post',
            'video_link': [current_file_video],
            'video_name': $('.video_name').val().trim(),
            'post_text': $('.input_video_description').val().trim(),
        }
        sendMessage(JSON.stringify(data));
    }
    else {
        alert('Обязательно ввести название видео!');
    }

});

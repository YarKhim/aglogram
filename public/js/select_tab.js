const fileInput = document.getElementById('channel_avatar_upload');
const preview = document.getElementById('preview');
const previewImg = document.getElementById('preview-img');
const removeButton = document.getElementById('remove-button');
const new_fileInput = document.getElementById('new_channel_avatar_upload');
const new_preview = document.getElementById('new_preview');
const new_previewImg = document.getElementById('new_preview-img');
const new_removeButton = document.getElementById('new_remove-button');
const select_channel = document.getElementById('select_channel');
console.log("🚀 ~ select_channel:", select_channel)
file_avatar_channel = '';
new_file_avatar_channel = '';
all_tabs_channels_select = document.querySelectorAll('.select_tab');
current_tab = null;
isFirst = true;
current_update_channel_id = null;
selected_channal_for_post = null;
all_tabs_channels_select.forEach(tab => {
    console.log($(tab));
    $(tab).on('click', function () {
        if (isFirst) {
            id_tab = 'opened_tab_' + this.id.replace('select_tab_', '');
            $('#' + id_tab).css('display', 'flex');
            document.getElementById(this.id).classList.add('selected_channel_tab');
            current_tab = id_tab;
            isFirst = false;
        }
        else {
            $('#' + current_tab).css('display', 'none');
            last_select_tab_id = 'select_tab_' + current_tab.replace('opened_tab_', '')
            document.getElementById(last_select_tab_id).classList.remove('selected_channel_tab');
            document.getElementById(this.id).classList.add('selected_channel_tab');
            id_tab = 'opened_tab_' + this.id.replace('select_tab_', '');
            $('#' + id_tab).css('display', 'flex');
            current_tab = id_tab;
        }
    })
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
        // console.log("🚀 ~ channels:", channels);
        channels.forEach(channel => {
            all_chanels_id.push(channel['id']);
            all_chanels[channel['id']] = { 'name': channel['name'], 'unique_name': channel['channel_name'] }
            channel_select_tab = ` <option class="select_your_channel_tab " value="` + channel['id'] + `" >` + channel['name'] + `</option>`;
            $(select_channel).append(channel_select_tab);
            channel_tab =
                `<div class="my_channel_tab">
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
            // console.log(document.getElementById('channel_settings_'+channel['id']))
            $('#channel_settings_' + channel['id']).on('click', function () {
                // console.log(('#third_plane'));
                $('#third_plane').text('Настройки канала @' + channel['channel_name']);
                $('.channel_settings_inputs_tab').css('display', 'block');
                current_update_channel_id = channel['id'];
                console.log("🚀 ~ current_update_channel_id:", current_update_channel_id);
                // document.getElementById('third_plane').innerText = 'Настройки канала @'+channel['channel_name'];
            })
        });
        select_channel.value = all_chanels_id[0];
        $('.post_channel_author').text(all_chanels[all_chanels_id[0]]['name']);
        selected_channal_for_post = all_chanels_id[0];
        select_channel.addEventListener('change', function () {
            $('.post_channel_author').text(all_chanels[select_channel.value]['name']);
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
    if (Object.keys(all_files).length > 0 || document.querySelector('.input_post_text').value.length > 0) {
        data = {
            'type_message': 'new_post',
            'post_author': selected_channal_for_post,
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

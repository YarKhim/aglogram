const fileInput = document.getElementById('channel_avatar_upload');
const preview = document.getElementById('preview');
const previewImg = document.getElementById('preview-img');
const removeButton = document.getElementById('remove-button');
file_avatar_channel = '';
all_tabs_channels_select = document.querySelectorAll('.select_tab');
current_tab = null;
isFirst = true;
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
$.ajax({
    url: '/get_channels',
    type: 'get',
    success: function (response) {
        channels = response['channels']
        // console.log("🚀 ~ channels:", channels);
        channels.forEach(channel => {
            channel_tab =
            `<div class="my_channel_tab">
                <div class="left_side_my_channel_tab">
                    <img src="`+channel['channel_avatar']+`">
                </div>
                <div class="right_side_my_channel_tab">
                    <div class="channel_name_channel_tab">
                        <div class="channel_names">
                            <p>`+channel['name']+`</p>
                            <p id="`+channel['channel_name']+`" class="unique_channel_name">@`+channel['channel_name']+`</p>
                        </div>
                        <div class='channel_settings_tab'>
                            <p class='channel_settings'>Управление каналом</p>
                        </div>

                    </div>
                </div>
            </div>`;
            $('.my_channels_list').prepend(channel_tab);
            // document.getElementById
            $('#'+channel['channel_name']).on('click', function(){
                navigator.clipboard.writeText(this.innerText)
                    .then(() => {
                        alert('Имя канала скопировано');
                    })
                    .catch(err => {
                        console.error('Произошла ошибка');
                    });
            })
        });
        // console.log(response[]);
    },
    error: function (xhr) {
        console.error('Error:', xhr);
        alert('Произошла ошибка: ' + xhr.responseJSON.message);
    }
});

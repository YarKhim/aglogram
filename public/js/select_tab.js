const fileInput = document.getElementById('channel_avatar_upload');
const preview = document.getElementById('preview');
const previewImg = document.getElementById('preview-img');
const removeButton = document.getElementById('remove-button');
all_tabs_channels_select = document.querySelectorAll('.select_tab');
current_tab = null;
isFirst = true;
all_tabs_channels_select.forEach(tab => {
    console.log($(tab));
    $(tab).on('click', function(){
        if(isFirst){
            id_tab = 'opened_tab_'+ this.id.replace('select_tab_','');
            $('#'+id_tab).css('display','flex');
            document.getElementById(this.id).classList.add('selected_channel_tab');
            current_tab = id_tab;
            isFirst = false;
        }
        else{
            $('#'+current_tab).css('display','none');
            last_select_tab_id = 'select_tab_'+current_tab.replace('opened_tab_', '')
            document.getElementById(last_select_tab_id).classList.remove('selected_channel_tab');
            document.getElementById(this.id).classList.add('selected_channel_tab');
            id_tab = 'opened_tab_'+ this.id.replace('select_tab_','');
            $('#'+id_tab).css('display','flex');
            current_tab = id_tab;
        }
    })
});
fileInput.addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();

        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        }

        reader.readAsDataURL(file);
    }
});

removeButton.addEventListener('click', function() {
    fileInput.value = ''; // Очистка input
    preview.style.display = 'none'; // Скрытие предпросмотра
    previewImg.src = ''; // Очистка src изображения
});

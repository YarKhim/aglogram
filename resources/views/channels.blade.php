<x-app-layout>
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <x-slot name="header">
        <!-- <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Каналы') }}
        </h2> -->
        <div class="select_tabs_channels">
            <div class="select_tab" id="select_tab_0">
                Подписки
            </div>
            <div class="select_tab" id="select_tab_1">
                Поиск
            </div>
            <div class="select_tab" id="select_tab_2">
                Мои каналы
            </div>
            <div class="select_tab" id="select_tab_3">
                Сделать пост
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- <div class="channels_main_plane">
            </div> -->

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="channels_main_plane">


                        <div class="opened_tab_plane">
                            <div class="opened_tab border_debug" id="opened_tab_0">
                                <div class="your_subscriptions border_debug">
                                    <h1 class="border_debug"> Мои подписки</h1>
                                    <div class="subscriptions_list" id="my_subscriptions_list">

                                        {{-- <div class="subscription_tab">

                                            <div class="left_side_subscription_tab">
                                                <!-- <div class="img_container_subscription_tab"> -->
                                                <img src="storage/channels_avatars/aglogram.png">
                                                <!-- </div> -->
                                            </div>
                                            <div class="right_side_subscription_tab">
                                                <div class="channel_name">
                                                    <p>Aglogram</p>
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
                                        </div> --}}


                                    </div>
                                </div>
                                <div class="opened_channel_subscription">
                                    <div class="opened_channel_subscription_header">
                                        <div class="opened_channel_subscription_header_avatar">
                                            <img class="opened_channel_subscription_header_avatar_img"
                                                src="storage/channels_avatars/aglogram.png">
                                        </div>
                                        <div class="text_channel_info border_debug">
                                            <div class="text_channel_info_name">
                                                aglogram
                                            </div>
                                            <div class="text_channel_count_subscribers">
                                                1234 Подписчиков
                                            </div>
                                        </div>
                                        <div class="unsubscribe_channel">
                                            <div class="unsubscribe_channel_button">
                                                Отписаться
                                            </div>

                                        </div>
                                    </div>
                                    <div class="opened_channel_subscription_posts">

                                        {{-- <div class="user_post_tab " id="user_post_tab_` + post['id'] + `">
                                            <div class="user_post_rect">
                                                <div class="post_header ">
                                                    <img class="post_image " src="/storage/3K4xEsRsU8RGtY6iMmY0GzqDXdoR7DHWHmLXUHch.jpg">
                                                    <div class="post_author ">
                                                        aglogram
                                                    </div>
                                                    <div class="post_date ">
                                                    </div>
                                                </div>
                                                <div class="post_data_rect">
                                                    <div class="post_data">
                                                        <div class="post_data_images">
                                                            <div class="post_data_image"
                                                                >


                                                            </div>
                                                        </div>
                                                        <div class="post_text">

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="post_footer">
                                                    <div class="post_likes">
                                                        Мне нравится
                                                    </div>
                                                    <div class="post_likes_counter"
                                                        >
                                                        Лайки: `+ post['likes_count'] + `
                                                    </div>
                                                    <div class="post_watchers">
                                                        Просмотры: `+ post['views'] + `
                                                    </div>
                                                </div>
                                                <div class="open_comments open_comments_`+ post['id'] + `">
                                                    Открыть комментарии
                                                </div>
                                                <div class="comments_plane comments_plane_`+ post['id'] + `">

                                                </div>
                                                <div class="post_comments">
                                                    <div class="post_comments_input">
                                                        <textarea id="input_comment_`+ post['id'] + `"
                                                            class="text_input_comment" name="text"
                                                            oninput='this.style.height = "";this.style.height = this.scrollHeight + "px";'></textarea>

                                                        <div class="div_send_comment">
                                                            <div class="send_comment"
                                                                id="send_comment_`+ post['id'] + `">
                                                                Отправить
                                                            </div>
                                                        </div>
                                                    </div>


                                                </div>
                                            </div>
                                        </div> --}}
                                    </div>
                                </div>
                            </div>
                            <div class="search_channel_tab opened_tab border_debug" id="opened_tab_1">
                                <div class="search_channel_tab_left_side border_debug">
                                    <h1 class="my_channels_options_header border_debug"> Найти канал</h1>
                                    <div class="serarch_channel_input_box">
                                        <input
                                            class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full"
                                            id="searched_channel_name" type="text" required="required"
                                            autofocus="autofocus" autocomplete="name" placeholder="Название канала">
                                        <div class="serarch_channel_button">Поиск</div>
                                    </div>
                                    <div class="search_channel_results">


                                        <!-- <div class="search_channel_result_tab border_debug">
                                            <div class="search_channel_result_photo">
                                                <img src="storage/channels_avatars/67839a292455d.png">
                                            </div>
                                            <div class="search_channel_result_right_side">
                                                <div class="search_channel_result_name">aglogram</div>
                                                <div class="search_channel_result_options">
                                                    <div class="subscibe_channel">Подписаться</div>
                                                    <div class="open_channel">Открыть</div>
                                                </div>
                                            </div>
                                        </div> -->


                                    </div>
                                </div>
                                <div class="search_channel_tab_right_side border_debug">

                                </div>
                            </div>
                            <div class="opened_tab border_debug" id="opened_tab_2">
                                <div class="create_channel border_debug">
                                    <h1 class="my_channels_options_header border_debug">Создать новый канал</h1>
                                    <div class="new_channel_form border_debug">
                                        <!-- <input id="channel_name" class="new_channel_inputs channel_name" type="text"
                                            placeholder="Название вашего канала"> -->
                                        <input
                                            class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full"
                                            id="channel_name" type="text" required="required" autofocus="autofocus"
                                            autocomplete="name" placeholder="Название вашего канала">
                                        <!-- <label for="channel_name">Имя вашего нового канала</label> -->
                                        <input
                                            class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full"
                                            id="channel_unique_name" type="text" required="required"
                                            autofocus="autofocus" autocomplete="name"
                                            placeholder="Уникальное имя канала из латинских букв">
                                        <!-- <input id="channel_unique_name" class="new_channel_inputs channel_unique_name"
                                            type="text" placeholder="Уникальное имя канала из латинских букв"
                                            maxlength="15"> -->
                                        <!-- <label for="channel_unique_name"> Уникальное имя вашего канала из латинских букв
                                        </label> -->
                                        <input type="file" accept=".png, .jpg, .jpeg" id="channel_avatar_upload">
                                        <label id="label_for_channel_avatar_upload"
                                            class="label_for_channel_avatar_upload"
                                            for="channel_avatar_upload">Загрузить аватар</label>
                                        <div id="preview">
                                            <img id="preview-img" src="" alt="Предпросмотр">
                                            <!-- <div class="">
                                                <span>
                                                    Удалить
                                                </span>
                                            </div> -->
                                            <button type="button" class="remove_channel_avatar_button"
                                                id="remove-button">Удалить</button>
                                        </div>
                                    </div>
                                    <div class="send_req">
                                        <div class="create_channel_button" id="create_channel_button">
                                            Создать
                                        </div>
                                    </div>

                                </div>
                                <div class="my_channels border_debug">
                                    <h1 class="my_channels_options_header">Мои каналы</h1>
                                    <div class="subscriptions_list my_channels_list">

                                    </div>
                                    <!-- <div class="subscriptions_list"> -->
                                    <!-- <div class="subscription_tab">

                                        <div class="left_side_subscription_tab">
                                            <img src="storage/channels_avatars/aglogram.png">
                                        </div>
                                        <div class="right_side_subscription_tab">
                                            <div class="channel_name">
                                                <p>Aglogram</p>
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
                                    </div> -->
                                    <!-- </div> -->
                                </div>
                                <div class="statistic border_debug">
                                    <h1 class="my_channels_options_header border_debug" id="third_plane"></h1>
                                    <div class="channel_settings_inputs_tab new_channel_form border_debug">
                                        <div class="new_params_channel_input">
                                            <input
                                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full"
                                                id="new_channel_name" type="text" required="required"
                                                autofocus="autofocus" autocomplete="name"
                                                placeholder="новое название вашего канала">
                                            <!-- <label for="channel_name">Имя вашего нового канала</label> -->
                                            <input
                                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full"
                                                id="new_channel_unique_name" type="text" required="required"
                                                autofocus="autofocus" autocomplete="name"
                                                placeholder="Новое уникальное имя канала из латинских букв">
                                            <input type="file" accept=".png, .jpg, .jpeg"
                                                id="new_channel_avatar_upload">
                                            <label id="new_label_for_channel_avatar_upload"
                                                for="new_channel_avatar_upload">Загрузить новый аватар</label>
                                            <div id="new_preview">
                                                <img id="new_preview-img" src="" alt="Предпросмотр">
                                                <button type="button" class="remove_channel_avatar_button"
                                                    id="new_remove-button">Удалить</button>
                                            </div>
                                        </div>
                                        <div class="send_updt_req">
                                            <div class="update_channel_button" id="update_channel_button">
                                                Сохранить изменения
                                            </div>
                                        </div>
                                        <div class="delete_channel">
                                            <div class="delete_channel_button" id="delete_channel_button">
                                                Удалить канал
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="opened_tab border_debug" id="opened_tab_3">

                                <div class="select_channel_tab border_debug">
                                    <h1 class="my_channels_options_header">Выбрать канал</h1>
                                    <select id="select_channel"
                                        class="select_your_channel border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full">
                                    </select>
                                </div>
                                <div class="create_channel_post">
                                    <div class="create_channel_post_tab border_debug">
                                        <div class="create_channel_post_rect border_debug">
                                            <div class="create_channel_post_header">
                                                <img class="create_channel_post_header_image"
                                                    src="storage/channels_avatars/67839a292455d.png">
                                                <div class="post_channel_author ">

                                                </div>
                                                <div class="post_channel_date ">
                                                    12.12.2025
                                                </div>
                                            </div>
                                            <div class="post_data_rect border_debug">
                                                <div class="channel_post_data border_debug">
                                                    <div class="all_uploaded_images_post" id="all_uploaded_images_post">
                                                    </div>
                                                    <div class="container">
                                                        <ul class="image-gallery" id="image_gallery">
                                                        </ul>
                                                    </div>

                                                    <div class="input_channel_post_text">
                                                        <textarea placeholder="Введите текст вашего поста здесь"
                                                            oninput="this.style.height = '';this.style.height = this.scrollHeight + 'px'"
                                                            ;
                                                            class="input_post_text select_your_channel border-gray-300 dark:border-gray-700 dark:bg-gray-900
                                                            dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500
                                                             dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full textarea_input_channel_post_text"></textarea>
                                                    </div>
                                                    <form id="form_upload">
                                                        <input id="upload_images" type="file" multiple accept="image/*">
                                                    </form>
                                                    <div class="div_label_for_upload_images">
                                                        <label class="label_for_upload_images"
                                                            id="label_for_upload_images_new_post_channel"
                                                            for="upload_images">Добавить фото к посту</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="publish_channel_post">
                                                <div class="publish_channel_post_button">
                                                    Опубликовать пост
                                                </div>
                                            </div>
                                            <!-- <div class="post_footer">
                                                <div class="post_likes">
                                                    Мне нравится
                                                </div>
                                                <div class="post_likes_counter">
                                                    Лайки:
                                                </div>
                                                <div class="post_watchers">
                                                    Просмотры:
                                                </div>
                                            </div> -->
                                            <!-- <div class="open_comments open_comments_`+ post['id'] + `">
                                                Открыть комментарии
                                            </div>
                                            <div class="comments_plane comments_plane_`+ post['id'] + `">

                                            </div> -->
                                            <!-- <div class="post_comments">
                                                <div class="post_comments_input">
                                                    <textarea class="text_input_comment" name="text"
                                                        oninput='this.style.height = "";this.style.height = this.scrollHeight + "px";'></textarea>

                                                    <div class="div_send_comment">
                                                        <div class="send_comment">
                                                            Отправить
                                                        </div>
                                                    </div>
                                                </div>


                                            </div> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <script src="{{ asset('js/select_tab.js') }}"></script>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>

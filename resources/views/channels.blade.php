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
                                    <div class="subscriptions_list">
                                        <div class="subscription_tab">

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
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="opened_tab border_debug" id="opened_tab_1">
                                456
                            </div>
                            <div class="opened_tab border_debug" id="opened_tab_2">
                                <div class="create_channel border_debug">
                                    <h1 class="my_channels_options_header">Создать новый канал</h1>
                                    <div class="new_channel_form">
                                        <input id="channel_name" class="new_channel_inputs channel_name" type="text"
                                            placeholder="Название вашего канала">
                                        <label for="channel_name">Имя вашего нового канала</label>
                                        <input id="channel_unique_name" class="new_channel_inputs channel_unique_name"
                                            type="text" placeholder="Уникальное имя канала из латинских букв"
                                            maxlength="15">
                                        <label for="channel_unique_name"> Уникальное имя вашего канала из латинских букв
                                        </label>
                                        <input type="file" accept=".png, .jpg, .jpeg" id="channel_avatar_upload">
                                        <label for="channel_avatar_upload">Загрузить аватар</label>
                                        <div id="preview">
                                            <img id="preview-img" src="" alt="Предпросмотр">
                                            <button type="button" class="remove-button" id="remove-button">Удалить</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="my_channels border_debug">
                                    <h1 class="my_channels_options_header">Мои каналы</h1>
                                </div>
                                <div class="statistic border_debug">
                                    <h1 class="my_channels_options_header">Статистика по моим каналам</h1>
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

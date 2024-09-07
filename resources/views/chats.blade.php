<x-app-layout>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://raw.githubusercontent.com/benjaminBrownlee/RSA/master/RSA.min.js"></script>
    <script src="http://peterolson.github.com/BigInteger.js/BigInteger.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsencrypt/3.0.0/jsencrypt.min.js"></script>
    {{-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Чаты') }}
        </h2>
    </x-slot> --}}
    <style>
        .p-6 {
            height: 82cqb;

        }
    </style>
    <div class="py-12">
        <div class="max-w-10xl mx-auto sm:px-3 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="border_debug main_plane">
                        <div class="border_debug all_chats_list">
                            <div class="border_debug chat_tab">
                                <div class="border_debug chat_foto"></div>
                                <div class="border_debug right_side_chat_tab">
                                    <div class="border_debug chat_info">
                                        <div class="border_debug chat_name">chat_name</div>
                                        <div class="border_debug read_receipts">23</div>
                                        <div class="border_debug last_message_time">41.23</div>

                                    </div>
                                    <div class="border_debug last_message">message</div>
                                </div>

                            </div>

                        </div>
                        <div class="border_debug messages_plane">
                            <div class="border_debug messages">
                                <div class="message border_debug">
                                    <div class="my_message border_debug">

                                    </div>
                                </div>
                                <div class="message border_debug">
                                    <div class="recived_message border_debug">

                                    </div>
                                </div>
                                <div class="message border_debug">
                                    <div class="my_message border_debug">

                                    </div>
                                </div>
                                <div class="message border_debug">
                                    <div class="my_message border_debug">

                                    </div>
                                </div>
                                <div class="message border_debug">
                                    <div class="recived_message border_debug">

                                    </div>
                                </div> <div class="message border_debug">
                                    <div class="recived_message border_debug">

                                    </div>
                                </div>
                            </div>

                            <div class="border_debug input">
                                <input type="text" class="message_input">
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        {{-- <div class="max-w-10xl mx-auto sm:px-3 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="opened_chat"> <!---место, где будет находиться открытый чат --->
                        <div class="message border_debug">
                            <div class="my_message border_debug">
                                <div class="message_text">
                                    <p>GHBDTN!wjklhjklhklghkljgfgkjghfgchfcjkjhcfgghfjdgge</p>
                                </div>
                            </div>
                        </div>

                        <div class="message border_debug">
                            <div class="my_message border_debug">
                                <div class="message_text">
                                    <p>GHBDTN!we</p>
                                </div>
                            </div>
                        </div>
                        <div class="message border_debug">
                            <div class="received_messege border_debug">
                                <div class="message_text">
                                    <p>GHBDTN!we</p>
                                </div>
                            </div>
                        </div>
                        <div class="message border_debug">
                            <div class="my_message border_debug">
                                <div class="message_text">
                                    <p>GHBDTN!we</p>
                                </div>
                            </div>
                        </div>
                        <div class="message border_debug">
                            <div class="received_messege border_debug">
                                <div class="message_text">
                                    <p>GHBDTN!we</p>
                                </div>
                            </div>
                        </div>
                        <div class="message border_debug">
                            <div class="my_message border_debug">
                                <div class="message_text">
                                    <p>GHBDTN!we</p>
                                </div>
                            </div>
                        </div>
                        <div class="message border_debug">
                            <div class="received_messege border_debug">
                                <div class="message_text">
                                    <p>GHBDTN!we</p>
                                </div>
                            </div>
                        </div>

                    </div>
                    <input type="text" class="input">
                    <button> Отправить</button>
                    <div class="a"> <!--- контейнер со списком чатов --->
                        <div class="chats">
                            <div class="name">
                                <p>Никола Тесла</p>
                            </div>
                            <div class="lastmessege">
                                <div class="txt">
                                    <p>сообщени hkljhjk gj jhbjhnb</p>
                                </div>
                                <div class="messegetime">
                                    <sup>17:45</sup>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div> --}}
    </div>
</x-app-layout>

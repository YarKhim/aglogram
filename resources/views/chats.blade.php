<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Чаты') }}
        </h2>
    </x-slot>
    <style>
        .p-6 {
            height: 100vh;
        }
    </style>
    <div class="py-12">
        <div class="max-w-10xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="opened_chat"> <!---место, где будет находиться открытый чат --->
                        <div class="message border_debug">
                            <div class="my_message border_debug">
                                <div class="message_text">
                                    <p>GHBDTN!we</p>
                                </div>
                            </div>
                        </div>
                        {{--  --}}
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
                    <input type="text" class="input"></input>
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
        </div>
    </div>
</x-app-layout>

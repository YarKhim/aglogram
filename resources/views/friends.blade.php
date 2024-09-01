<x-app-layout>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Друзья') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">



                    {{ __('Поиск друзей по имени: ') }}
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
                                {{-- <p>Имя пользователя: {{ $user->name }}</p>
                                <p>Email: {{ $user->email }}</p> --}}
                                {{-- {{ $user }} --}}

                                {{-- {{ route('profile.avatar', ['user' => $user->id]) }} --}}
                                <script>
                                    @foreach ($users as $user)
                                        console.log('{{ $user->username }}');
                                        console.log('{{ route('profile.avatar', ['user' => $user->id]) }}');
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

                    </div>


                </div>
            </div>
        </div>
    </div>
</x-app-layout>

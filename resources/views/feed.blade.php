<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Лента') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"> --}}
            <div class="p-6 text-gray-900 dark:text-gray-100">
                {{-- {{ __("Главная страница") }} --}}
                <div class="post border_debug">
                    <div class="avatar">
                        <img src="http://localhost:8000/avatar/7">

                    </div>
                    <div class="username">
                        <h2>Ярослав Хаймусов</h2>
                    </div>

                    <div class="timecode">
                        <sup>18:34</sup>
                    </div>
                    <div class="foto">
                        <img src="http://localhost:8000/avatar/7">

                    </div>
                    <div class="description">
                        <p>Хаймусов Ярослав сменил фото профиля</p>
                    </div>
                    <div class="likes">
                        <img src="heart.png" width="30px">
                    </div>
                    <div class="comments">
                        <img src="heart.png" width="30px">
                    </div>
                    <div class="share">
                        <img src="heart.png" width="30px">
                    </div>
                </div>
                <div class="post">
                    <div class="avatar">
                        <img src="http://localhost:8000/avatar/7">

                    </div>
                    <div class="username">
                        <h2>Ярослав Хаймусов</h2>
                    </div>
                    <div class="timecode">
                        <sup>18:34</sup>
                    </div>
                    <div class="foto">
                        <img src="http://localhost:8000/avatar/7">

                    </div>
                    <div class="description">
                        <p>а тут у НАС дебаг цуймашйй йййййгрц уарцйу шгарцйу гшрай шцураг йцуппа йп амгйу карпу гшшшшш шшшшшшш шшшшшш шшшш шшшш шш шшшшшш шшшшшш шшшшш шшш шшшшшш шшш</p>
                    </div>
                    <div class="likes">
                        <img src="heart.png" width="30px">
                    </div>
                    <div class="comments">
                        <img src="heart.png" width="30px">
                    </div>
                    <div class="share">
                        <img src="heart.png" width="30px">
                    </div>
                </div>
                <div class="post">
                    <div class="avatar">
                        <img src="http://localhost:8000/avatar/7">
                    </div>
                    <div class="username">
                        <h2>Ярослав Хаймусов</h2>
                    </div>
                    <div class="timecode">
                        <sup>18:34</sup>
                    </div>
                    <div class="foto">
                        <img src="http://localhost:8000/avatar/7">

                    </div>
                    <div class="description">
                        <p>описание</p>
                    </div>
                    <div class="likes">
                        <img src="heart.png" width="30px">
                    </div>
                    <div class="comments">
                        <img src="heart.png" width="30px">
                    </div>
                    <div class="share">
                        <img src="heart.png" width="30px">
                    </div>
                </div>
                <div class="post">
                    <div class="avatar">
                        <img src="http://localhost:8000/avatar/7">

                    </div>
                    <div class="username">
                        <h2>Ярослав Хаймусов</h2>
                    </div>

                    <div class="timecode">
                        <sup>18:34</sup>
                    </div>
                    <div class="foto">
                        <img src="http://localhost:8000/avatar/7">

                    </div>
                    <div class="description">
                        <p>описание</p>
                    </div>
                    <div class="likes">
                        <img src="heart.png" width="30px">
                    </div>
                    <div class="comments">
                        <img src="heart.png" width="30px">
                    </div>
                    <div class="share">
                        <img src="heart.png" width="30px">
                    </div>
                </div>


            </div>
            {{-- </div> --}}
        </div>
    </div>

</x-app-layout>

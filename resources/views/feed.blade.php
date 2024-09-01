<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Лента') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("Главная страница") }}
                    <div class="post">
                        <div class="avatar">
                        </div>
                        <div class="username">
                            <h2>Никола Тесла</h2>
                        </div>
                        <div class="timecode">
                            <sup>18:34</sup>
                        </div>
                        <div class="foto">
                            <p>фото<br>фото<br>фото<br>фото<br>фото<br>фото<br>фото<br>фото<br>фото<br>фото<br>фото<br>фото<br>фото<br>r>фото<br>фотофото<br>фото<br>фото<</p>
                        </div>
                        <div class="description">
                            <p>описание</p>
                        </div>
                        <div class="likes" >
                            <img src="heart.png" width="30px">
                        </div>
                        <div class="comments" >
                            <img src="heart.png" width="30px">
                        </div>
                        <div class="share" >
                            <img src="heart.png" width="30px">
                        </div>
                    </div>
                </div>                    
            </div>
        </div>
    </div>

</x-app-layout>

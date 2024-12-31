$(document).ready(function () {
    var serach_friends_input = document.getElementById('friends_search_input');
    // serach_friends_input.value = 'Ярослав 1';
    serach_friends = document.getElementById('search_friends_start');
    serach_friends.addEventListener('click', function () {
        if (serach_friends_input.value.length > 0) {
            $('#search_results').empty();
            var data_1 = serach_friends_input.value.split(' ');
            var data_for_search = data_1.filter(item => item !== '');
            console.log(data_for_search);
            data_for_search = JSON.stringify(data_for_search);

            $.ajax({
                url: '/search', // URL вашего маршрута
                type: 'POST', // Метод запроса
                data: {
                    data: data_for_search,
                    _token: '{{ csrf_token() }}' // Добавляем CSRF-токен для защиты
                },
                success: function (response) {
                    var all_users = [];
                    var searched_id = [];
                    var encounteredIds = {}; // Объект для отслеживания встреченных id
                    response.data.forEach(users => {
                        users.forEach(user => {
                            var userId = user['id'];
                            if (!encounteredIds[userId]) {
                                // Если id еще не встречался, добавляем его в объект
                                encounteredIds[userId] = true;
                                all_users.push(user);
                            }
                        });
                    });
                    // console.log(all_users);
                    all_users.forEach(user => {
                        console.log(user['id'])
                        console.log(user['name'])
                        console.log(user['username'])
                        console.log(user['avatar'])
                        search_result_div =
                            `<div class="search_results_tab"  id="` + user[
                            'id'] + `">
                                <div class="search_user_image">
                                    <img class="result_user_search_img" src="` + user[
                            'avatar'] + `">
                                </div>
                                <div class="user_info_and_links ">
                                    <div class="user_name">
                                        <p>` + user['name'] + " " + user['lastname'] +
                            " @" + user['username'] + `</p>
                                    </div>
                                    <div class="user_links ">
                                        <a target="blank" href="/user_profile?id=` +
                            user['username'] + `" class="user_link">Перейти на страницу</a>
                                    </div>
                                </div>
                            </div>`;
                        $('#search_results').append(search_result_div);
                        // console.log(search_result_div);

                    });
                    if (all_users.length == 0) {
                        search_result_div =
                            `<div class="nothing_for_your_query border_debug" >
                            <h2>По вашему запросу ничего не найдено :( </h2>
                            <p>Попробуйте что-нибудь другое</p>
                            </div>`;
                        $('#search_results').append(search_result_div);
                        // console.log("По вашему запросу ничего не найдено(");
                    }
                    serach_friends_input.value = '';
                    // console.log('Response:', response);
                },
                error: function (xhr) {
                    console.error('Error:', xhr);
                    alert('Произошла ошибка: ' + xhr.responseJSON.message);
                }
            });
        }
    });
});

# intensive-laravel-workproj

## Общая концепция

Сервис предоставляет возможность отслеживать прогресс просмотра сериалов и получения списка не просмотренных серий.
Сериалам можно выставлять оценки, на основании которых формируются рейтинги позволяющие выбрать новые сериалы для просмотра.

### Основные сценарии использования:

- Просмотр списка сезонов и серий выбранного сериала
- Регистрация пользователя
- Составление списка просматриваемых сериалов и списка не просмотренных серий
- Выставление оценки сериалу
- Просмотр рейтинга популярных сериалов, фильтрация по жанрам, поиск по названию
- Комментирование вышедших серий, возможность ответа на комментарии (получение уведомлений при получении ответа на комментарий **?**)

## Технические требования

Проект должен разрабатываться на PHP версии 7.4 или выше
(при использовании версии 8.0 соответствующее ограничение должно быть указано в файле composer.json). // **[?]** К моменту выпуска курса уже выйдет версия 8.1, думаю стоит сделать минимальной версию 8.0
Используемая база данных — MySQL 5.7 и выше.
Разработка верстки и клиентского приложения не требуется. Ожидается только разработка бекенд api приложения.

Все запросы должны сопровождаться отправкой заголовка принимающего json в качестве ответа,
и ответы, как успешные так и об ошибках, должны возвращаться в json формате.

## Описание процессов

### Регистрация пользователя

Регистрация нового пользователя для получения доступа к персональной части сайта.

**Последовательность действий:**

* Отправка post запроса с данными пользователя на endpoint регистрации.
* Валидация полученных полей. Проверка наличия обязательных полей и соответствия заданным правилам.
* Проверка, что указанный email не занят.
* Сохранение данных в БД, или возвращение списка ошибок при их наличии.
* Сохранение аватара в публичное хранилище и указание ссылки на файл в таблице пользователей.
* Авторизация пользователя под зарегистрированной учетной записи
* Отправка уведомления для подтверждения email. (**?**)
// **[?]** Кто должен отправлять письма - api или клиент? Если api - то он должен знать что-то о шаблонах пусть и типовых,
  если клиент то нам нужно на клиент передавать секретные данные (токен подтверждения)

**Правила валидации:**

* Поля email, password, password_confirmation, name - обязательные
* Поле email - должно быть валидным email адресом (валидация средствами фреймворка), не должно использоваться другими пользователями
* Поле password - минимальная длинна 8 символов, должно совпадать с отправленным password_confirmation
* По желанию: проверка уникальности только по подтвержденным email адресам

### Авторизация

* Отправка post запроса с данными пользователя на endpoint авторизации.
* Валидация полученных полей. Проверка наличия обязательных полей и соответствия заданным правилам.
* Проверка, что email подтвержден. (**?**)
* Авторизация пользователя под зарегистрированной учетной записи.

### Выход (logout)

* Отправка post запроса на endpoint выхода пользователя.
* Уничтожение пользовательской сессии

### Запрос сброса пароля

* Отправка post запроса с email пользователя на endpoint запроса сброса пароля.
* Валидация email адреса (существующий подтвержденный адрес)
* Отправка сообщения со ссылкой для сброса пароля

### Изменение пароля

* Отправка post запроса с данными пользователя на endpoint изменения пароля.
* Обновление пароля пользователя

**Проверяемые данные:**

* token - полученный из ссылки в предыдущем сценарии, обязателен.
* email - валидный, обязательный.
* password, password_confirmation - новый пароль, и подтверждение аналогично правилам регистрации

**Примечание:** для реализации методов работы с пользователем авторизация, регистрация и пр -
рекомендуется использовать пакет `laravel/fortify`

### Получение списка сериалов

* Отправка get запроса на endpoint получения списка сериалов
* Возвращение первых 20 сериалов, если не передано другое условие (параметр page)
  * Вместе со списком сериалов возвращаются параметры пагинации:
    количество элементов всего ссылка на первую страницу, на последнюю, на предыдущую и следующую

Дополнительно вместе с запросом могут быть переданы следующие параметры:
* page - номер страницы, для пагинации
* order_by - правило сортировки. Возможные значения: date, rating
* genre - фильтрация по жанру
* search - фильтрация по имени

Если запрос выполняет авторизованный пользователь -
в дополнение к другим параметрам возвращаем статус просмотра и оценку при наличии

**Примечание:** Может использоваться с формой поиска, используя фильтрацию по имени

### Получение списка жанров

* Отправка get запроса на endpoint получения списка жанров

### Получение списка эпизодов сериала

* Отправка get запроса на endpoint получения списка эпизодов сериала указав id сериала в качестве get параметра

Если запрос выполняет авторизованный пользователь -
в дополнение к другим параметрам возвращаем статус просмотра серии

### Получение информации об эпизоде

* Отправка get запроса на endpoint получения информации об эпизоде указав id эпизода в качестве get параметра

Если запрос выполняет авторизованный пользователь -
в дополнение к другим параметрам возвращаем статус просмотра серии

### Получение списка комментариев эпизода

* Отправка get запроса на endpoint получения списка комментариев эпизода указав id эпизода в качестве get параметра

### Добавление комментария к эпизоду

* Отправка post запроса на endpoint добавления комментария указав id эпизода в качестве параметра
* Не обязательным параметром может быть отправлен id коментария, если это ответ на другой комментарий

### Получение списка просматриваемых сериалов пользователя

* Отправка get запроса на endpoint получения списка сериалов просматриваемых пользователем

Метод возвращает сериалы пользователя без пагинации.
Вместе с каждым сериалом возвращается информация о количестве просмотренных эпизодов и количестве вышедших серий -
на основании этой информации можно принять решение о необходимости получения списка не просмотренных серий

### Получение списка эпизодов отслеживаемого сериала не просмотренных пользователем

* Отправка get запроса на endpoint получения списка эпизодов не просмотренных пользователем

### Запрос на добавление сериала **[?]**

// для проработки взаимодействия с внешним api и написание адаптера данных из внешнего источника
// обращение на внешний api сервис https://www.tvmaze.com/api указав сериал по id imdb http://api.tvmaze.com/lookup/shows?imdb=tt0944947
// добавление сериала в локальную БД на основании данных полученных по api tvmaze

* Отправка get запроса на endpoint добавления сериала по imdb id
* Получение информации о сериале по api tvmaze
* Проверка наличия указанного сериала в локальной БД (400 ошибка в случае наличия)
* Добавление сериала в локальную БД по полученным от tvmaze данным
* Запрос информации об эпизодах и добавление их в БД

### Периодическое обновление информации о статусе сериала и эпизодах **[?]**

// работа с расписанием

* Получение информации о сериале по api tvmaze
* Синхронизация основной информации полученными от tvmaze данным
* Запрос информации об эпизодах и добавление отсутствующих в БД

**Примечание:** подробнее о принимаемых параметрах и возвращаемых значениях смотрите в api документации проекта

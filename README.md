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
- Комментирование вышедших серий, возможность ответа на комментарии
- Добавление сериала используя id imdb получив информацию из внешнего источника
- Периодическая актуализация информации о сериалах из внешнего источника

## Технические требования

Проект должен разрабатываться на PHP версии 8.0 или выше
(при использовании версии 8.1 соответствующее ограничение должно быть указано в файле composer.json).
Используемая база данных — MySQL 5.7 и выше.  
Разработка верстки и клиентского приложения не требуется. Ожидается только разработка бекенд api приложения.

Все запросы должны сопровождаться отправкой заголовка принимающего json в качестве ответа,
и ответы, как успешные так и об ошибках, должны возвращаться в json формате.

## Описание процессов

см [specification.md](specification.md)

## Установка

### Используя composer

- Установить зависимости `composer install`
- Создать файл с env переменными из заготовки `cp .env.example .env`
- Сгенерировать ключ приложения `php artisan key:generate`
- Установить необходимые переменные в файле `.env` (подключения БД, etc)
- Выполнить миграции `php artisan migrate` (с флагом `--seed` для заполнения сидов, при необходимости)
- Запустить локальный веб сервер `php artisan serve`, или настроить свой сервер

### Используя docker

- Установить зависимости 
`docker run --rm -u "$(id -u):$(id -g)" -v $(pwd):/opt -w /opt laravelsail/php80-composer:latest composer install --ignore-platform-reqs`
- Создать файл с env переменными из заготовки `cp .env.example .env`
- Установить необходимые переменные в файле `.env` (подключения БД, настроить `FORWARD_DB_PORT`, `APP_PORT` при необходимости)
- Поднять docker окружение `vendor/bin/sail up` (или `vendor/bin/sail up -d` для запуска в фоне)
- Сгенерировать ключ приложения `vendor/bin/sail artisan key:generate`
- Выполнить миграции `vendor/bin/sail artisan migrate` (с флагом `--seed` для заполнения сидов, при необходимости)
- Настроить алиас для выполнения команд sail по желанию https://laravel.com/docs/8.x/sail#configuring-a-bash-alias

## Запуск тестов

`sail artisan test` (используя docker) или `php artisan test` (напрямую).

Или прямо используя `vendor/bin/phpunit` без дополнительных оберток. 

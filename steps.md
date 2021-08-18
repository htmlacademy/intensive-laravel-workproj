# Пошаговая инструкция по разработке с комментариями

## Установка проекта 

https://laravel.com/docs/8.x/installation

Выбираем команду в зависимости от используемой платформы и варианта запуска (через docker или напрямую на хостовой машине).

## Конфигурация 

https://laravel.com/docs/8.x/installation#initial-configuration

- Указать используемую локаль в config/app.php
- Рекомендую задать переменную debug_blacklist и перечислить значения которые не должны выводиться в отладочной информации
- Рекомендую добавить файлы локализации, например из репозитория https://github.com/Laravel-Lang/lang

## Создаем сущности (модели) и миграции

https://laravel.com/docs/8.x/eloquent#generating-model-classes  

Для создания модели с соответсвующей миграцией можно исопльзовать команду `php artisan make:model ModelName --migration`  
Но лучше сразу `php artisan make:model ModelName -cfm --api`, кроме модели и миграции, 
так же будет создана фабрика (для генерации фейковых данных), и api контроллер

Заполняем созданную миграцию: https://laravel.com/docs/8.x/migrations

- Рекомендую использовать timestampTz и timestampsTz поля (для баз данных поддерживающих хранение времени с таймзоной)
- Для создания связанного поля, используем методы `foreignIdFor(Show::class)->constrained();` 
что равнозначно последовательному вызову методов: `foreign('show_id')->references('id')->on('shows')->index();`
- Таблицы для хранения связей ManyToMany именуются с использованием имен таблиц исходных сущностей, 
в единственном числе, в алфавитном порядке. Например, для связи таблиц `shows` и `genres` создается таблица `genre_show`.

- Данные связей которые должны возвращаться всегда при получении моделей, можно указать в свойстве `with` модели.
- Для вывода в модели дополнительных атрибутов используем accessor https://laravel.com/docs/8.x/eloquent-mutators#accessors-and-mutators
- Для приведения типов возвращаемых данных casting https://laravel.com/docs/8.x/eloquent-mutators#attribute-casting
- Если вы будете использовать sqlite движок БД для запуска тестов, в моделях придется указать casts для int полей указывающих на связанную сущность
- Массивы $hidden и $visible позволяют управлять отображаемыми атрибутами, которые мы отдаем "наружу" https://laravel.com/docs/8.x/eloquent-serialization#hiding-attributes-from-json
- Описываем связи моделей https://laravel.com/docs/8.x/eloquent-relationships

## Создаем сиды для заполнения БД фейковыми данными

Рекомендую использовать фабрики в качестве источника данных: https://laravel.com/docs/8.x/seeding#using-model-factories  



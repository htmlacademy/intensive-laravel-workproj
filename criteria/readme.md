Подготовка и проверка личных проектов проводится по базовым и дополнительным критериям.

Базовые критерии охватывают наиболее важные требования к проекту и проверяют основные знания и навыки. Для успешной защиты личного проекта должны быть выполнены все базовые критерии.

Дополнительные критерии проверяют то, насколько студент внимателен к деталям, и оценивают проект с точки зрения шлифовки его качества и оптимизации. Выполнение этих критериев необходимо для защиты на 100%.

Во время финальной защиты баллы за выполнение дополнительных критериев добавляются только при выполнении всех базовых.

# Критерии

## Базовые

### Задача

#### Б1. Код соответствует техническому заданию проекта.

- Код выполняет поставленную задачу;
- При выполнении кода не возникает ошибок.

### Стиль кода и читаемость

#### Б2. Именование переменных и функций.

Название переменной должно рассказывать о сути хранимого там значения. В названии переменных не используется тип. Массивы названы существительными во множественном числе.

Запрещено:

- называть переменные и ключи массивов транслитом: `$imya` вместо `$name`;
- однобуквенные переменные (кроме счетчиков);
- аббревиатуры (кроме общеупотребительных: `JSON`, `web`, `url`, `seo` и др.);
- использовать кириллицу в ключах массивов;

#### Б3. Используются обязательные блоки кода, даже в простых случаях.

В любых конструкциях, где подразумевается использование блока кода (фигурных скобок), таких как `for`, `while`, `if`, `switch`, `function`, блок кода используется обязательно, даже если инструкция состоит из одной строчки.

**Пример:**

```
if (foo) {
  foo++;
}

while (bar) {
  baz();
}

if (foo) {
  baz();
} else {
  qux();
}
```

#### Б4. Названия констант (постоянных значений) написаны прописными (заглавными) буквами

#### Б5. Код должен проходить проверку по phpcs и psalm

Инструменты статической оптимизации и проверки стайлгайда не должны показывать никаких ошибок и предупреждений.

### Архитектура

#### Б6. Внедрение зависимостей.

Не допускается использование классов и сервисов напрямую через вызов `new`. Внутри кода приложения, следует использовать service container для разрешения зависимостей.

Все зависимости также должны быть правильно [зарегистрированы](https://laravel.com/docs/8.x/container).


#### Б7. Нет велосипедов.

Прежде, чем писать свою функцию для решения частной задачи, необходимо проверять существование подходящей функции в [документации на стандартную библиотеку](http://php.net/manual/ru/funcref.php) или компонента фреймворка в документации на Laravel.

Использование пользовательских функций, которые дублируют функциональность встроенных не допускается.

### Мусор

#### Б8. В проекте отсутствуют посторонние файлы.

В итоговом коде проекта находятся только те файлы, которые были на момент создания репозитория, которые были получены в патчах и файлы, созданные по заданию.

#### Б9. В проекте отсутствует неиспользуемый код.

В коде проекта не должно быть файлов и частей кода, которые не используются, включая, закомментированные участки кода, неиспользуемые функции и переменные.

### База данных

#### Б10. Использование миграций.

Вся работа над структурой и заполнением БД (за исключением первоначальной схемы и дампов, созданных по заданию) ведётся только через миграции.

#### Б11. Работа с БД через средства фреймворка.

В коде сценариев запрещено взаимодействовать с MySQL напрямую (через PDO или mysqli). Все запросы должны вестись только через компоненты фреймворка.

### Тесты

##### Б12. Все API-эндпойнты должны быть покрыты функциональными тестами

#### Б13. Должны быть юнит тесты на собственные классы-сервисы

#### Б14. Для заполнения БД во время тестов используются сиды

#### Б15. Тесты исполняются изолированно и не требуют внешних зависимостей

Все тесты не зависят друг от друга: не должно быть такого, что при падении одного теста не исполняются другие.

В тестах нельзя использовать бизнес-логику приложения. 
Пример: вместо создания пользователя посредством заполнения БД сидом, используется API-метод регистрации.

#### Б16. Используются моки и стабы

Для тестирования внешних сервисов, а также любого кода, который зависит от внешних условий или вызывает сайд-эффекты, необходимо подменять вызовы с помощью моков и стабов.

### Фреймворк

#### Б17. Простые шаблоны.

В шаблонах допустимо использовать только:

- показ переменных, вызов методов моделей и представления;
- показ виджетов;
- условные конструкции и циклы;
- стандартные функции PHP и фреймворка, необходимые для форматирования результата;

#### Б18. Контроллеры не содержат бизнес-логики.

В контроллерах допустимо использовать только:

- работу с внешними параметрами;
- вызов моделей и организация запросов в БД;
- подготовка данных и передача их в представление;
- вызов объектов и их методов из сторонних классов;

#### Б19. Запрос/ответ/сессии/куки через компонент.

Прямое обращение к `$_GET`, `$_POST`, `$_SESSION` и `$_COOKIE` не допускается

#### Б20. Использование ORM

Для работы с БД используются средства Eloquent и Query builder. Не допускается писать SQL запросы руками. 

### Безопасность

#### Б21. В коде отсутствуют SQL инъекции.

При работе с БД, все SQL запросы должны быть защищены от SQL инъекций. Для этого обязательно использовать подготовленные выражения для формирования запросов.

#### Б22. Аутентификация и авторизация реализована средствами фреймворка.

Использование стандартных компонент для [аутентификации](https://laravel.com/docs/8.x/authentication) и [авторизации](https://laravel.com/docs/8.x/authorization)

### Ошибки

#### Б23. Поддержка PHP 8.

Код должен запускаться и без ошибок работать в PHP 8. Использование deprecated функций также недопустимо.

#### Б24. Отсутствие ошибок и предупреждений.

С самым строгим уровнем репортинга ошибок (`error_reporting = E_ALL`) код личного проекта не должен вызывать никаких ошибок и предупреждений.

### Деплой

#### Б25. Используются переменные окружения

Для всех настроек приложения и секретов используются переменные окружения и `env`. Хардкод в конфигах не разрешается.

#### Б26. Валидный docker-compose файл

## Дополнительные критерии

### Стиль кода и читаемость

#### Д1. Используется явное приведение типов в операциях с разными типами данных.

В случае, если операнды имеют разный тип данных, их типы приводятся вручную. Лучше не полагаться на автоматическое приведение типов, а использовать операторы строгого сравнения: `===` и `!==`.

#### Д2. Небольшая вложенность условий.

Вложенность условий (количество условных конструкций внутри друг друга) не больше трех.

#### Д3. Короткие функции и методы.

Пользовательские функции и методы должны решать только одну задачу, а их длина не должна быть больше 30 — 50 строк.

#### Д5. Документирование методов.

Все пользовательские функции и методы должны быть задокументированы, используя синтаксис phpDoc.

#### Д7. Типизация.

В методах и возвращаемых значениях собственных классов проставлены типы.

#### Д8. Методы внутри классов упорядочены.

Во всех классах методы упорядочены следующим образом:

1. Публичные свойства.
2. Скрытые свойства.
3. Конструктор.
4. Геттеры/сеттеры свойств объекта.
5. Основные методы:
   - Публичные методы.
   - Защищённые методы.
   - Приватные методы.

### Производительность

#### Д9. Используется кэширование данных (кэш API)

[Кэширование информации](https://www.yiiframework.com/doc/guide/2.0/ru/caching-data) из БД необходимо для тех запросов, где это указано по заданию

### Архитектура

#### Д10. Используются интерфейсы.

По возможности, набор однотипных пользовательских классов должен быть объединён под одним интерфейсом

### Фреймворк

#### Д11. Кастомные валидаторы.

Если каким то полям запроса требуется нестандартная валидация, то она должна быть оформлена в виде нового валидатора - наследника стандартного компонента валидации

#### Д12. Настройки компонентов вынесены в конфигурацию.

По возможности, общие настройки всех используемых компонент фреймворка должны находиться в конфигурации приложения. Допустимо указание частных настроек в контроллере или представлении, если того требует задание.
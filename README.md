# Разворачивание проекта локально

1. Установить docker и утилиту ddev (если она ранее на компьютер не устанавливалась)
2. Склонировать репозиторий: `git clone https://github.com/elvirastelmakh/task-manager.git`
3. Перейти в созданную директорию: `cd task-manager`
4. Запустить проект: `ddev start`
5. Зайти в web-контейнер: `ddev ssh`
6. Установить пакеты с помощью утилиты composer: `composer install`
7. Запустить миграции: `bin/console doctrine:migrations:migrate`
8. Запустить тесты: `vendor/bin/phpunit`
9. Для работы с БД можно в DBeaver создать подключение с параметрами: 
    1. Server Host: localhost
    2. Port: 54699 (узнать с помощью `ddev describe`, можно задать в .ddev\config.yaml)
    3. Database: db
    4. Username: db
    5. Password: db

## В проекте реализовано:
1. Страница с просмотром списка задач:
   https://task-manager.ddev.site/task

   Можно отфильтровать список по статусу задачи:
   https://task-manager.ddev.site/task?status={status}
   Значение параметра {status} = 0 (завершена задача) или 1 (не завершена)

   Можно задать параметры сортировки, например:
   https://task-manager.ddev.site/task?sort=-status,-id
   Данные будут отсортированы сначала по убыванию статуса, затем по убыванию id.
   По умолчанию данные отсортированы по убыванию id.

   Можно задать параметры паджинации:
   https://task-manager.ddev.site/task?x-pagination-size=20&x-pagination-page=2
   Параметр x-pagination-size задает кол-во записей на странице, 
   x-pagination-page - номер выводимой страницы
   Также можно задать параметры паджинации не в строке запроса, а в заголовках (headers).
   
2. Добавление/редактирование/удаление задачи доступно через утилиту Postman или аналогичную:

    1. Добавление задачи:
        URL https://task-manager.ddev.site/task, метод POST.

        Пример данных для добавления:
        ```
        {
            "name": "task1",
            "description": "test",
            "endDate": null,
            "status": 0
        }
        ```
    2. Изменение задачи:
        URL https://task-manager.ddev.site/task/{id}, метод PUT.
        
        Пример данных для изменения:
        ```
        {
            "name": "new task",
            "description": "test",
            "endDate": "2024-09-01",
            "status": 1
        }
        ```
    3. Удаление задачи:
        URL https://task-manager.ddev.site/task/{id}, метод DELETE.

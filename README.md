# Разворачивание проекта локально

0. Установить docker и утилиту ddev (если она ранее на компьютер не устанавливалась)
1. Склонировать репозиторий: `git clone https://github.com/elvirastelmakh/task-manager.git`
2. Перейти в созданную директорию: `cd task-manager`
3. Запустить проект: `ddev start`
4. Зайти в web-контейнер: `ddev ssh`
5. Установить пакеты с помощью утилиты composer: `composer install`
6. Запустить миграции: `bin/console doctrine:migrations:migrate`
7. Запустить тесты: `vendor/bin/phpunit`
8. Для работы с БД можно в DBeaver создать подключение с параметрами: 
    1. Server Host: localhost
    2. Port: узнать с помощью `ddev describe`
    3. Database: db
    4. Username: db
    5. Password: db

## В проекте реализовано:
1. Страница с просмотром списка задач:
   https://task-manager.ddev.site/task

   Можно отфильтровать список по статусу задачи:
   https://task-manager.ddev.site/task?status={status}
   Значение параметра {status} = 0 (завершена задача) или 1 (не завершена)
   
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
            "endDate": null,
            "status": 0
        }
        ```
    3. Удаление задачи:
        URL https://task-manager.ddev.site/task/{id}, метод DELETE.

# **Проект "Slotegrator"**

## **Предварительные требования**

1. Установленный Docker, Docker-compose на вашем компьютере.

## **Начало работы**

### Шаг 1: Docker build

Выполните следующую команду для создания и запуска контейнеров

`docker-compose up --build -d`

Это будет поднимать 4 контейнера (PHP-FPM (with composer), Nginx, PostgresSQL, Selenium).

Создан `Dockerfile` в `infra/php/` и конфигурация для `nginx` в `infra/nginx/nginx.conf`

### Шаг 2: Настройка базы данных

После запуска контейнера PostgreSQL можно подключиться к базе данных (используя localhost:5432).

Username: **postgres**
password: **postgres12**

### Шаг 3: Установка PHP-зависимостей

Подключитесь к контейнеру PHP-FPM:

`docker exec -it slotegrator-fpm bash`

Внутри контейнера перейдите в директорию проекта и установите PHP-зависимости: `composer install`

### Шаг 4: Настройка схемы базы данных

Находясь в контейнере PHP-FPM, выполните следующую команду для создания схемы базы данных:

`bin/console doctrine:schema:update --force`

### Шаг 5: Доступ к приложению

Теперь приложение должно работать. Вы можете получить к нему доступ по следующему адресу:

http://localhost
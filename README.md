# Laravel Project Setup

Этот проект написан на фреймворке Laravel и использует SQLite в качестве базы данных.

## Требования

- PHP >= 8.1
- Composer
- SQLite (установлен и доступен в системе)

## Установка

### 1. Клонируйте репозиторий

```bash
git clone https://github.com/alieveldar/meat_facture.git
cd your-project
```

### 2. Установите зависимости

```bash
composer install
```

### 3. Скопируйте `.env` и сгенерируйте ключ приложения

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Настройте базу данных

В файле `.env` измените настройки подключения к базе данных на SQLite:

```env
DB_CONNECTION=sqlite
```

Затем создайте сам файл базы данных:

```bash
touch database/database.sqlite
```

### 5. Выполните миграции и заполните базу данных начальными данными

```bash
php artisan migrate --seed
```

### 6. Запустите локальный сервер

```bash
php artisan serve
```

Проект будет доступен по адресу: [http://127.0.0.1:8000](http://127.0.0.1:8000)

## Тестирование

Для запуска тестов используйте команду:

```bash
php artisan test
```

## Дополнительно

- Swagger доступен на: `/api/documentation`


# TaskFlow API

## Описание проекта

TaskFlow API — это сервис для управления задачами. Пользователи могут регистрироваться, создавать задачи, редактировать их, отмечать выполненными и удалять. API поддерживает аутентификацию через токены и мягкое удаление (soft delete) задач.

## Функциональные требования

### Роли пользователей

- **Пользователь (User)** — может управлять только своими задачами (создавать, читать, обновлять, удалять)
- **Администратор (Admin)** — в планах, будет иметь полный доступ ко всем задачам

### Функционал

- Регистрация нового пользователя
- Аутентификация (логин) с получением API-токена
- Создание задачи (POST /api/tasks)
- Просмотр списка всех своих задач с пагинацией (GET /api/tasks)
- Просмотр одной задачи (GET /api/tasks/{id})
- Обновление задачи (PUT /api/tasks/{id})
- Удаление задачи (DELETE /api/tasks/{id}) — мягкое удаление (soft delete)

### Поля задачи

| Поле | Тип | Описание |
|------|-----|----------|
| id | integer | Уникальный идентификатор |
| user_id | integer | ID пользователя, создавшего задачу |
| title | string | Название задачи (обязательное) |
| description | text | Описание задачи (необязательное) |
| priority | enum (low, medium, high) | Приоритет задачи |
| due_date | date | Срок выполнения |
| is_completed | boolean | Статус выполнения |
| deleted_at | timestamp | Время мягкого удаления |
| created_at | timestamp | Дата создания |
| updated_at | timestamp | Дата обновления |

## Технические требования

- **PHP** — 7.1 и выше
- **Laravel** — 5.8
- **База данных** — MySQL / MariaDB
- **Аутентификация** — API-токены

## Структура базы данных

### Таблица `users`

| Поле | Тип | Описание |
|------|-----|----------|
| id | bigint | Первичный ключ |
| name | string | Имя пользователя |
| email | string | Email (уникальный) |
| password | string | Хеш пароля |
| api_token | string | Токен для API-аутентификации |
| created_at | timestamp | Дата регистрации |
| updated_at | timestamp | Дата обновления |

### Таблица `tasks`

| Поле | Тип | Описание |
|------|-----|----------|
| id | bigint | Первичный ключ |
| user_id | bigint | Внешний ключ → users.id |
| title | string | Название задачи |
| description | text | Описание |
| priority | enum | Приоритет (low/medium/high) |
| due_date | date | Срок выполнения |
| is_completed | boolean | Выполнена/не выполнена |
| deleted_at | timestamp | Мягкое удаление |
| created_at | timestamp | Дата создания |
| updated_at | timestamp | Дата обновления |

## API Endpoints

| Метод | Endpoint | Описание | Требует токен |
|-------|----------|----------|---------------|
| POST | /api/register | Регистрация | ❌ |
| POST | /api/login | Логин | ❌ |
| POST | /api/logout | Выход | ✅ |
| GET | /api/tasks | Список задач (пагинация) | ✅ |
| POST | /api/tasks | Создать задачу | ✅ |
| GET | /api/tasks/{id} | Просмотр задачи | ✅ |
| PUT | /api/tasks/{id} | Обновить задачу | ✅ |
| DELETE | /api/tasks/{id} | Удалить задачу | ✅ |

## Примеры запросов

### Регистрация

```json
POST /api/register
{
    "name": "Иван",
    "email": "ivan@example.com",
    "password": "123456"
}
```

### Создание задачи

POST /api/tasks
Authorization: Bearer токен_пользователя
{
    "title": "Купить хлеб",
    "due_date": "2026-06-15",
    "priority": "high"
}


### Список задач

GET /api/tasks
Authorization: Bearer токен_пользователя

## Установка и запуск
1. Клонировать репозиторий
git clone https://github.com/TheJudgeFromHell/taskflow-api.git
cd taskflow-api

2. Установить зависимости
composer install

3. Настроить окружение
cp .env.example .env
Отредактируйте .env — укажите данные для подключения к базе данных.

4. Сгенерировать ключ приложения
php artisan key:generate

5. Выполнить миграции
php artisan migrate

6. Запустить сервер
php artisan serve

7. Открыть фронтенд
http://127.0.0.1:8000/frontend/index.html

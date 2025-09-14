# REST API (JWT) — аутентификация

Ниже описаны эндпоинты аутентификации. Все ответы — JSON. Для защищённых маршрутов требуется заголовок `Authorization: Bearer <token>`.

## Базовый URL
- Локально: `http://localhost:8080/api`

## Логин
- Метод: `POST /auth/login`
- Тело запроса (JSON):
  - `email` (string, required)
  - `password` (string, required)
- Ответ `200`:
```json
{
  "access_token": "<JWT>",
  "token_type": "bearer",
  "expires_in": 3600
}
```

Примечание: регистрация отключена. Пользователи создаются только администратором.

## Сброс пароля (временный пароль на email)
- Метод: `POST /auth/forgot-password`
- Тело запроса (JSON):
  - `email` (string, required) — email существующего пользователя
- Логика: генерируется временный пароль, сохраняется и отправляется письмом на `email`.
- Ответ `200`:
```json
{ "message": "Временный пароль отправлен на email" }
```

Требования: настроенная почта в `.env` (MAIL_*) и доступность SMTP.

## Смена пароля (по JWT)
- Метод: `POST /auth/change-password`
- Требуется: `Authorization: Bearer <token>`
- Тело запроса (JSON):
  - `current_password` (string, required)
  - `new_password` (string, required, min 8)
- Ответ `200`:
```json
{ "message": "Пароль успешно изменён" }
```

## Профиль текущего пользователя
- Метод: `GET /auth/me`
- Требуется: `Authorization` заголовок с JWT
- Ответ `200`: объект пользователя

## Обновление токена
- Метод: `POST /auth/refresh`
- Требуется: JWT
- Ответ `200`:
```json
{
  "access_token": "<JWT>",
  "token_type": "bearer",
  "expires_in": 3600
}
```

## Выход
- Метод: `POST /auth/logout`
- Требуется: JWT
- Ответ `200`:
```json
{ "message": "Вы вышли из системы" }
```

Примечания:
- По умолчанию guard `api` использует JWT. Ротация токена — через `/auth/refresh`.
- Используйте HTTPS в продакшене.

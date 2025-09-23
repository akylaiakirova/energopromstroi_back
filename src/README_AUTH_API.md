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

---------------------------------------------------------------------------

# REST API — Мощности котлов (boilers_capacity)
Все эндпоинты требуют заголовок `Authorization: Bearer <token>`.

## Получить список мощностей
- Метод: `GET /boilers-capacity`
- Сортировка: по полю `name` по возрастанию
- Ответ `200`: массив объектов
```json
[
  { "id": 1, "name": "10 кВт", "createAt": "2025-09-14T10:00:00Z", "updatedAt": null },
  { "id": 2, "name": "20 кВт", "createAt": "2025-09-14T10:05:00Z", "updatedAt": null }
]
```

## Создать мощность котла
- Метод: `POST /boilers-capacity`
- Тело запроса (JSON):
  - `name` (string, required)
- Успешный ответ `201`: созданный объект
```json
{ "id": 3, "name": "30 кВт", "createAt": "2025-09-14T10:10:00Z", "updatedAt": null }
```

## Обновить мощность котла
- Метод: `PUT /boilers-capacity/{id}`
- Тело запроса (JSON):
  - `name` (string, required)
- Ответ `200`: обновлённый объект
```json
{ "id": 3, "name": "35 кВт", "createAt": "2025-09-14T10:10:00Z", "updatedAt": "2025-09-14T10:20:00Z" }
```

## Удалить мощность котла
- Метод: `DELETE /boilers-capacity/{id}`
- Ответ `200`:
```json
{ "message": "Удалено" }
```

---------------------------------------------------------------------------

# REST API — Материалы (materials)
Все эндпоинты требуют заголовок `Authorization: Bearer <token>`.

## Получить список материалов
- Метод: `GET /materials`
- Сортировка: по полю `name` по возрастанию
- Ответ `200`: массив объектов
```json
[
  { "id": 1, "name": "Сталь A", "unit": "кг", "createAt": "2025-09-14T10:00:00Z", "updatedAt": null },
  { "id": 2, "name": "Сталь B", "unit": "кг", "createAt": "2025-09-14T10:05:00Z", "updatedAt": null }
]
```

## Создать материал
- Метод: `POST /materials`
- Тело запроса (JSON):
  - `name` (string, required)
  - `unit` (string, required)
- Успешный ответ `201`: созданный объект
```json
{ "id": 3, "name": "Сталь C", "unit": "кг", "createAt": "2025-09-14T10:10:00Z", "updatedAt": null }
```

## Обновить материал
- Метод: `PUT /materials/{id}`
- Тело запроса (JSON):
  - `name` (string, required)
  - `unit` (string, required)
- Ответ `200`: обновлённый объект
```json
{ "id": 3, "name": "Сталь C-1", "unit": "кг", "createAt": "2025-09-14T10:10:00Z", "updatedAt": "2025-09-14T10:20:00Z" }
```

## Удалить материал
- Метод: `DELETE /materials/{id}`
- Ответ `200`:
```json
{ "message": "Удалено" }
```

Примечания:
- `materials_arrival`, `stocks_balance`, `write_off` используют `material_id`.
- Для массовых операций с материалами в рамках конвертации используется `conversion_materials`.

---------------------------------------------------------------------------

# REST API — Клиенты (clients)
Все эндпоинты требуют заголовок `Authorization: Bearer <token>`.

## Получить список клиентов
- Метод: `GET /clients`
- Сортировка: по полю `name` по возрастанию
- Ответ `200`: массив объектов
```json
[
  { "id": 1, "name": "ООО Ромашка", "email": "info@romashka.kz", "phone": "77001234567", "whatsapp": null, "telegram": null, "note": null, "createAt": "2025-09-14T10:00:00Z", "updatedAt": null }
]
```

## Создать клиента
- Метод: `POST /clients`
- Тело запроса (JSON):
  - `name` (string, required)
  - `email` (string, optional, email)
  - `phone` (string, required, только цифры)
  - `whatsapp` (string, optional)
  - `telegram` (string, optional)
  - `note` (string, optional)
- Успешный ответ `201`: созданный объект
```json
{ "id": 2, "name": "ТОО Василёк", "email": "contact@vasilek.kz", "phone": "77007654321", "whatsapp": "77007654321", "telegram": "@vasilek", "note": "VIP", "createAt": "2025-09-14T10:10:00Z", "updatedAt": null }
```

## Обновить клиента
- Метод: `PUT /clients/{id}`
- Тело запроса (JSON):
  - `name` (string, required)
  - `email` (string, optional, email)
  - `phone` (string, required, только цифры)
  - `whatsapp` (string, optional)
  - `telegram` (string, optional)
  - `note` (string, optional)
- Ответ `200`: обновлённый объект
```json
{ "id": 2, "name": "ТОО Василёк+", "email": "office@vasilek.kz", "phone": "77007654321", "whatsapp": "77007654321", "telegram": "@vasilek", "note": "Стратегический клиент", "createAt": "2025-09-14T10:10:00Z", "updatedAt": "2025-09-14T10:20:00Z" }
```

## Удалить клиента
- Метод: `DELETE /clients/{id}`
- Ответ `200`:
```json
{ "message": "Удалено" }
```
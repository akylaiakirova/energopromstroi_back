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

# 👉 REST API — Мощности котлов (boilers_capacity)
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

# 👉 REST API — Шаблоны документов (templates_document)
Все эндпоинты требуют заголовок `Authorization: Bearer <token>`.

Поле `files` — массив строк с именами файлов. Имена на стороне сервера нормализуются в формате `td_YYYYmmdd_HHMMSS_xxxxxx`.

## Получить список шаблонов
- Метод: `GET /templates-document`
- Сортировка: по полю `name` по возрастанию
- Ответ `200`: массив объектов
```json
[
  {
    "id": 1,
    "name": "Договор-подряд",
    "files": ["td_20250914_101000_ab12cd", "td_20250914_101000_ef34gh"],
    "note": null,
    "createAt": "2025-09-14T10:10:00Z",
    "updatedAt": null
  }
]
```

## Создать шаблон
- Метод: `POST /templates-document`
- Тело запроса (JSON):
  - `name` (string, required)
  - `files` (array<string>, optional) — список исходных имён; сервер преобразует их в `td_YYYYmmdd_HHMMSS_xxxxxx`
  - `note` (string, optional)
- Успешный ответ `201`: созданный объект
```json
{
  "id": 2,
  "name": "Акт-приемки",
  "files": ["td_20250914_111500_k9LmNq"],
  "note": "Версия для печати",
  "createAt": "2025-09-14T11:15:00Z",
  "updatedAt": null
}
```

## Обновить шаблон
- Метод: `PUT /templates-document/{id}`
- Тело запроса (JSON):
  - `name` (string, required)
  - `files` (array<string>, optional) — если передано, массив будет перезаписан и нормализован
  - `note` (string, optional)
- Ответ `200`: обновлённый объект
```json
{
  "id": 2,
  "name": "Акт-приемки (v2)",
  "files": ["td_20250914_121000_Zx12Yv", "td_20250914_121000_Pq34Rt"],
  "note": "Добавлены новые поля",
  "createAt": "2025-09-14T11:15:00Z",
  "updatedAt": "2025-09-14T12:10:00Z"
}
```

## Удалить шаблон
- Метод: `DELETE /templates-document/{id}`
- Ответ `200`:
```json
{ "message": "Удалено" }
```

---------------------------------------------------------------------------

# 👉 REST API — Материалы (materials)
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

# 👉 REST API — Клиенты (clients)
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

---------------------------------------------------------------------------

# 👉 REST API — Сотрудники (users)
Все эндпоинты требуют заголовок `Authorization: Bearer <token>`.

Важные правила:
- Поле `has_access` определяет право входа в систему.
- Пароль (`password`) НЕ обязателен, если `has_access` пусто/false.
- Если `has_access=true`, пароль обязателен при создании. При обновлении: если включаете доступ и у пользователя ещё нет пароля — передайте новый пароль.
- Телефон `phone` должен содержать только цифры. Email валидируется как email и уникален.

## Получить список сотрудников
- Метод: `GET /users`
- Сортировка: по полю `name` по возрастанию
- Ответ `200`: массив объектов
```json
[
  {
    "id": 1,
    "role_id": 1,
    "position": "Администратор",
    "name": "Акылай",
    "surname": "Акирова",
    "email": "akylaiakirova@gmail.com",
    "phone": "77000000000",
    "has_access": true,
    "createAt": "2025-09-14T10:00:00Z",
    "updatedAt": "2025-09-14T10:05:00Z"
  }
]
```

## Создать сотрудника
- Метод: `POST /users`
- Тело запроса (JSON):
  - `role_id` (int, required, exists: roles.id)
  - `position` (string, required)
  - `name` (string, required)
  - `surname` (string, optional)
  - `email` (string, required, email, unique)
  - `phone` (string, required, только цифры)
  - `whatsapp` (string, optional)
  - `telegram` (string, optional)
  - `passport_number` (string, optional)
  - `passport_pin` (string, optional)
  - `salary` (number, optional)
  - `comment` (string, optional)
  - `date_start` (date, optional)
  - `date_end` (date, optional)
  - `has_access` (boolean, optional; если true — пароль обязателен)
  - `password` (string, min 8; обязателен, если `has_access=true`)
- Пример `has_access=true`:
```json
{
  "role_id": 2,
  "position": "Бухгалтер",
  "name": "Анна",
  "surname": "Иванова",
  "email": "anna@example.com",
  "phone": "77001234567",
  "has_access": true,
  "password": "secretPa55"
}
```
- Успешный ответ `201`: созданный объект (без пароля)

## Обновить сотрудника
- Метод: `PUT /users/{id}`
- Тело запроса (JSON) — те же поля, что при создании; `email` — уникален с исключением текущего пользователя.
- Правило для доступа: если включаете `has_access=true` и у пользователя ещё нет пароля — передайте поле `password`.
- Ответ `200`: обновлённый объект
```json
{
  "id": 5,
  "role_id": 2,
  "position": "Бухгалтер",
  "name": "Анна",
  "surname": "Иванова",
  "email": "anna.office@example.com",
  "phone": "77001234567",
  "has_access": true,
  "createAt": "2025-09-14T10:10:00Z",
  "updatedAt": "2025-09-14T12:20:00Z"
}
```

## Удалить сотрудника
- Метод: `DELETE /users/{id}`
- Ответ `200`:
```json
{ "message": "Удалено" }
```

Примечания по авторизации сотрудников:
- Пользователь с `has_access=false/null` не сможет войти: `POST /auth/login` вернёт `403`.
- Сессии по устройствам учитываются в таблице `devices` (хэшированные refresh‑токены, метаданные `last_ip`, `user_agent`).

---------------------------------------------------------------------------

# 👉 REST API — Реквизиты сотрудников (user_banks)
Все эндпоинты требуют заголовок `Authorization: Bearer <token>`.

## Получить список реквизитов
- Метод: `GET /user-banks`
- Параметры запроса (query):
  - `user_id` (int, optional) — фильтр по сотруднику
- Ответ `200`: массив объектов
```json
[
  {
    "id": 1,
    "user_id": 5,
    "bank_name": "Каспи Банк",
    "bank_account_number": "KZ1234567890",
    "bank_bik": "CASPKZKA",
    "address_registered": "Алматы, ул. Абая 1",
    "address_actual": "Алматы, ул. Сатпаева 2",
    "createAt": "2025-09-14T10:00:00Z",
    "updatedAt": null
  }
]
```

## Создать реквизиты
- Метод: `POST /user-banks`
- Тело запроса (JSON):
  - `user_id` (int, required, exists: users.id)
  - `bank_name` (string, required)
  - `bank_account_number` (string, required)
  - `bank_bik` (string, optional)
  - `address_registered` (string, optional)
  - `address_actual` (string, optional)
- Успешный ответ `201`: созданный объект
```json
{
  "id": 2,
  "user_id": 5,
  "bank_name": "Halyk Bank",
  "bank_account_number": "KZ0987654321",
  "bank_bik": null,
  "address_registered": null,
  "address_actual": null,
  "createAt": "2025-09-14T10:10:00Z",
  "updatedAt": null
}
```

## Обновить реквизиты
- Метод: `PUT /user-banks/{id}`
- Тело запроса (JSON):
  - `user_id` (int, required, exists: users.id)
  - `bank_name` (string, required)
  - `bank_account_number` (string, required)
  - `bank_bik` (string, optional)
  - `address_registered` (string, optional)
  - `address_actual` (string, optional)
- Ответ `200`: обновлённый объект
```json
{
  "id": 2,
  "user_id": 5,
  "bank_name": "Halyk Bank (обновл.)",
  "bank_account_number": "KZ0987654321",
  "bank_bik": "HalykKZKX",
  "address_registered": "Астана, пр. Абылай хана 10",
  "address_actual": "Астана, пр. Абылай хана 10",
  "createAt": "2025-09-14T10:10:00Z",
  "updatedAt": "2025-09-14T12:20:00Z"
}
```

## Удалить реквизиты
- Метод: `DELETE /user-banks/{id}`
- Ответ `200`:
```json
{ "message": "Удалено" }
```
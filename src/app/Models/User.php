<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Атрибуты, доступные для массового присвоения.
     *
     * @var list<string>
     */
    protected $fillable = [
        'role_id',
        'position',
        'name',
        'surname',
        'email',
        'phone',
        'password',
    ];

    /**
     * Атрибуты, скрываемые при сериализации.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Приведения типов для атрибутов модели.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Получить идентификатор, который будет храниться внутри JWT.
     * Возвращает первичный ключ пользователя.
     */
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * Возвращает набор дополнительных клеймов (claims) для включения в JWT.
     * Здесь используются пустые клеймы — при необходимости добавьте свои поля.
     *
     * @return array<string, mixed>
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramChat extends Model
{
    protected $fillable = [
        'chat_id',
        'telegram_username',
        'first_name',
        'last_name',
        'is_bot',
        'language_code',
        'last_message_at',
        'update_id',
    ];

    protected $casts = [
        'chat_id' => 'integer',
        'is_bot' => 'boolean',
        'last_message_at' => 'datetime',
    ];

    /**
     * Get user by telegram username
     */
    public static function findByUsername($username)
    {
        $cleanUsername = ltrim($username, '@');
        return static::where('telegram_username', $cleanUsername)
                    ->orWhere('telegram_username', $username)
                    ->first();
    }

    /**
     * Get user by chat_id
     */
    public static function findByChatId($chatId)
    {
        return static::where('chat_id', $chatId)->first();
    }
}

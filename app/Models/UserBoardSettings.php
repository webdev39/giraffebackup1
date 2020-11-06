<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBoardSettings extends Model
{
    protected $fillable = ['user_id', 'board_id', 'quick_nav'];

    public static function findByUserIdAndBoardIdOrNew(int $userId, int $boardId): UserBoardSettings
    {
        if(!$model = static::query()->where('user_id', $userId)->where('board_id', $boardId)->first()) {
            return new static();
        }

        return $model;
    }
}

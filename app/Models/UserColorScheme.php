<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserColorScheme extends Model
{
    protected $fillable = ['user_id', 'default', 'sidebar', 'navbar', 'task_detail', 'manage', 'subscribers', 'management', 'modal', 'buttons', 'font', 'notification'];

    protected $casts = [
        'sidebar' => 'json',
        'navbar' => 'json',
        'task_detail' => 'json',
        'manage' => 'json',
        'subscribers' => 'json',
        'management' => 'json',
        'modal' => 'json',
        'buttons' => 'json',
        'font' => 'json',
        'notification' => 'json',
    ];

    protected $hidden = ['user_id', 'created_at', 'updated_at'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isOwnedBy(User $user): bool
    {
        return $user->id === $this->user_id;
    }

    public static function defaultScheme() {
        return [
            [
                'default' => true,
                'sidebar' => [
                    'bg' => '#376aa7',
                    'color' => '#fff',
                    'task_count_bg' => '#6291c8',
                ],
                'navbar' => [
                    'bg' => '#fff',
                    'color' => '#333',
                    'timerPlaceholder' => '#f4f4f4',
                ],
                'task_detail' => [
                    'header' => [
                        'bg' => '#376aa7',
                        'color' => '#fff',
                    ],
                ],
                'manage' => [
                    'bg' => '#dfe3e6',
                ],
                'subscribers' => [
                    'bg' => '#376aa7',
                    'color' => '#fff',
                ],
                'management' => [
                    'collapse_bg' => '#4f77f2',
                    'collapse_color' => '#fff',
                ],
                'modal' => [
                    'header_bg' => '#376aa7',
                    'header_color' => '#fff',
                ],
                'buttons' => [
                    'success' => [
                        'bg' => '#5d98de',
                        'color' => '#fff',
                        'border' => '#5d98de',
                        'bg_hover' => '#3a75bb',
                        'color_hover' => '#fff',
                        'border_hover' => '#3a75bb',
                    ],
                    'warning' => [
                        'bg' => '#f15d5d',
                        'color' => '#fff',
                        'border' => '#f15d5d',
                        'bg_hover' => '#cc4c4c',
                        'color_hover' => '#fff',
                        'border_hover' => '#cc4c4c',
                    ],
                    'dangerous' => [
                        'bg' => '#f15d5d',
                        'color' => '#fff',
                        'border' => '#f15d5d',
                        'bg_hover' => '#cc4c4c',
                        'color_hover' => '#fff',
                        'border_hover' => '#cc4c4c',
                    ],
                    'primary' => [
                        'bg' => '#a0bdd2',
                        'color' => '#fff',
                        'border' => '#a0bdd2',
                        'bg_hover' => '#7495ad',
                        'color_hover' => '#fff',
                        'border_hover' => '#7495ad',
                    ],
                    'close' => [
                        'bg' => '#e2e6e9',
                        'color' => '#7b7b7b',
                        'border' => '#e2e6e9',
                        'bg_hover' => '#d6dbdf',
                        'color_hover' => '#7b7b7b',
                        'border_hover' => '#d6dbdf',
                    ],
                ],
                'notification' => [
                    'bg' => '#fff',
                    'color' => '#333',
                ],
                'font' => [
                    'color' => '#333',
                ],
            ], [
                'default' => true,
                'sidebar' => [
                    'bg' => '#495665',
                    'color' => '#fff',
                    'task_count_bg' => '#6291c8',
                ],
                'navbar' => [
                    'bg' => '#495665',
                    'color' => '#C6C6C6',
                    'timerPlaceholder' => '#788594',
                ],
                'task_detail' => [
                    'header' => [
                        'bg' => '#495665',
                        'color' => '#C6C6C6',
                    ],
                ],
                'manage' => [
                    'bg' => '#dfe3e6',
                ],
                'subscribers' => [
                    'bg' => '#495665',
                    'color' => '#C6C6C6',
                ],
                'management' => [
                    'collapse_bg' => '#495665',
                    'collapse_color' => '#C6C6C6',
                ],
                'modal' => [
                    'header_bg' => '#495665',
                    'header_color' => '#C6C6C6',
                ],
                'buttons' => [
                    'success' => [
                        'bg' => '#5d98de',
                        'color' => '#fff',
                        'border' => '#5d98de',
                        'bg_hover' => '#3a75bb',
                        'color_hover' => '#fff',
                        'border_hover' => '#3a75bb',
                    ],
                    'warning' => [
                        'bg' => '#f15d5d',
                        'color' => '#fff',
                        'border' => '#f15d5d',
                        'bg_hover' => '#cc4c4c',
                        'color_hover' => '#fff',
                        'border_hover' => '#cc4c4c',
                    ],
                    'dangerous' => [
                        'bg' => '#f15d5d',
                        'color' => '#fff',
                        'border' => '#f15d5d',
                        'bg_hover' => '#cc4c4c',
                        'color_hover' => '#fff',
                        'border_hover' => '#cc4c4c',
                    ],
                    'primary' => [
                        'bg' => '#a0bdd2',
                        'color' => '#fff',
                        'border' => '#a0bdd2',
                        'bg_hover' => '#7495ad',
                        'color_hover' => '#fff',
                        'border_hover' => '#7495ad',
                    ],
                    'close' => [
                        'bg' => '#e2e6e9',
                        'color' => '#7b7b7b',
                        'border' => '#e2e6e9',
                        'bg_hover' => '#d6dbdf',
                        'color_hover' => '#7b7b7b',
                        'border_hover' => '#d6dbdf',
                    ],
                ],
                'notification' => [
                    'bg' => '#fff',
                    'color' => '#333',
                ],
                'font' => [
                    'color' => '#333',
                ],
            ]
        ];
    }

    public static function getDefaultColorScheme(): \Illuminate\Support\Collection
    {
        return self::where('default', true)->get();
    }
}
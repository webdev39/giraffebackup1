<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillLog extends Model
{
    public const ACTION_CREATE = 'create';
    public const ACTION_UPDATE = 'update';
    public const ACTION_DELETE = 'delete';
    
    protected $fillable = ['user_id', 'bill_id', 'comment', 'action'];
    
    protected $with = ['createdBy'];
    
    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }
    
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

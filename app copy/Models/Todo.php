<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class Todo extends Model
{
    protected $fillable = [
        'name', 'email', 'description', 'assigned_to', 'department_id'
    ];
 
    public function user()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
 
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}

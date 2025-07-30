<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'email', 'designation', 'department', 'assigned_to', 'description', 'status', 'user_id'];

    // Relationship with User (assuming a task is assigned to a user)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


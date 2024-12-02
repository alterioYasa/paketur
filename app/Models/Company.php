<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'email', 'phone_number'];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($company) {
            User::create([
                'name' => "Manager",
                'email' => 'manager_' . $company->id . '@example.org',
                'password' => bcrypt('password123'),
                'role' => 'Manager',
                'company_id' => $company->id,
            ]);
        });

        static::deleting(function ($model) {
            $model->users()->delete();
            $model->employees()->delete();
        });
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'company_id', 'id');
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'company_id', 'id');
    }
}

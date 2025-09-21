<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{


    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'color',
        'description'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the expenses for the category.
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * Get the total amount of expenses for this category.
     */
    public function getTotalExpensesAttribute(): float
    {
        return $this->expenses->sum('amount');
    }

    /**
     * Scope to get categories with expense counts.
     */
    public function scopeWithExpenseCount($query)
    {
        return $query->withCount('expenses');
    }
}

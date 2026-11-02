<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryScale extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'min_salary' => 'decimal:2', 'max_salary' => 'decimal:2'];
    }

    public function schemes()
    {
        return $this->hasMany(SchemeOfService::class);
    }
}

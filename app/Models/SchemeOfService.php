<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Scheme of Service: inabeba taarifa za nafasi ya kazi (sifa, majukumu, ngazi ya mshahara) zinazotumika kutengeneza tangazo */
class SchemeOfService extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function salaryScale()
    {
        return $this->belongsTo(SalaryScale::class);
    }

    public function vacancies()
    {
        return $this->hasMany(Vacancy::class, 'scheme_of_service_id');
    }

    public function getCategoriesListAttribute(): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R/', (string) $this->required_categories))));
    }
}

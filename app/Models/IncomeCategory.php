<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomeCategory extends Model
{
    use HasFactory;

    protected $fillable = ['tenant_id', 'name', 'code', 'is_active'];
    protected $casts    = ['is_active' => 'boolean'];

    public function tenant()  { return $this->belongsTo(Tenant::class); }
    public function incomes() { return $this->hasMany(Income::class); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Income extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'income_category_id', 'recorded_by', 'title',
        'description', 'amount', 'income_date', 'reference_number', 'payment_mode',
    ];

    protected $casts = ['income_date' => 'date', 'amount' => 'decimal:2'];

    public function tenant()         { return $this->belongsTo(Tenant::class); }
    public function incomeCategory() { return $this->belongsTo(IncomeCategory::class); }
    public function recordedBy()     { return $this->belongsTo(User::class, 'recorded_by'); }
}

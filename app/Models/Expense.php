<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'expense_category_id', 'recorded_by', 'title',
        'description', 'amount', 'expense_date', 'bill_number',
        'bill_attachment', 'payment_mode', 'vendor_name',
    ];

    protected $casts = ['expense_date' => 'date', 'amount' => 'decimal:2'];

    public function tenant()          { return $this->belongsTo(Tenant::class); }
    public function expenseCategory() { return $this->belongsTo(ExpenseCategory::class); }
    public function recordedBy()      { return $this->belongsTo(User::class, 'recorded_by'); }
}

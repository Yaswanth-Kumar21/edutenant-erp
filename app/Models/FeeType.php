<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeType extends Model
{
    use HasFactory;

    // Fee type code constants
    const UNIFORM    = 'UNIFORM';
    const EXAM       = 'EXAM';
    const UDF        = 'UDF';
    const RECORD     = 'RECORD';
    const VEHICLE    = 'VEHICLE';
    const TUITION    = 'TUITION';
    const OTHER      = 'OTHER';
    const INTERNSHIP = 'INTERNSHIP';

    protected $fillable = [
        'tenant_id', 'name', 'code', 'description', 'frequency',
        'applicable_all_streams', 'applicable_all_branches',
        'can_be_exempted', 'amount', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'applicable_all_streams'  => 'boolean',
        'applicable_all_branches' => 'boolean',
        'can_be_exempted'         => 'boolean',
        'is_active'               => 'boolean',
        'amount'                  => 'decimal:2',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function feeStructures()
    {
        return $this->hasMany(FeeStructure::class);
    }

    public function payments()
    {
        return $this->hasMany(FeePayment::class);
    }
}

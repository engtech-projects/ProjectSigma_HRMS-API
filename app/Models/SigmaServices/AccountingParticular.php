<?php

namespace App\Models\SigmaServices;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingParticular extends Model
{
    use HasFactory;

    protected $table = 'accounting_particulars';

    protected $fillable = [
        'type',
        'local_particular_name',
        'accounting_particular_name',
        'description',
    ];


}

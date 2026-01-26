<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeUploads extends Model
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    public const DOCS_DIR = "employee/docs/";
    public const MEMO_DIR = "employee/memo/";
    protected $fillable = [
        'id',
        'employee_uploads',
        'employee_id',
        'upload_type',
        'file_location',
    ];
}

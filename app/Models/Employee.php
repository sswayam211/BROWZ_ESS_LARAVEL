<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'PM_EMP_PERS'; // Change this to your actual table name
    protected $primaryKey = 'EMPS_CODE'; // Change if your primary key is different
    public $timestamps = false;

    // protected $fillable = [
    //     'EMPS_CODE',
    //     'first_name',
    //     'last_name',
    //     'email',
    //     'phone',
    //     'department',
    //     'position',
    //     'salary',
    // ];

    protected $guarded = [];

    /**
     * Relationship: Employee belongs to Login/User
     */
    public function login()
    {
        return $this->belongsTo(Login::class, 'EMPS_CODE', 'USER_ID');
    }
}

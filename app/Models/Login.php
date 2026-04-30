<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Login extends Model
{
    protected $table = 'PAY_MENU_USER';
    protected $fillable = [
        'USER_ID',
        'USER_PASSWD',
        'USER_DISABLE_FLAG',
        
    ];

    /**
     * Relationship: Login has one Employee
     */
    public function employee()
    {
        return $this->hasOne(Employee::class, 'employee_id', 'USER_ID');
    }
}

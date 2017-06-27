<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Check extends Model
{
    protected $connection='mysql_paper';
    protected $table = 'T_P_CHECK';
    protected $primaryKey = 'checkId';
}

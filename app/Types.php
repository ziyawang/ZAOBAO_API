<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Types extends Model
{
    protected $connection='mysql_paper';
    protected $table = 'T_U_TYPES';
    protected $primaryKey = 'id';
}

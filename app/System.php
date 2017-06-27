<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class System extends Model
{
    protected $connection='mysql_paper';
    protected $table = 'T_P_SYSTEM';
    protected $primaryKey = 'systemId';
}

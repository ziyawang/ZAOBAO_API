<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Collect extends Model
{
    protected $connection='mysql_paper';
    protected $table = 'T_P_COLLECT';
    protected $primaryKey = 'collectId';
}

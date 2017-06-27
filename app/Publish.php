<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Publish extends Model
{
    protected $connection='mysql_paper';
    protected $table = 'T_P_PUBLISH';
    protected $primaryKey = 'publishId';
}

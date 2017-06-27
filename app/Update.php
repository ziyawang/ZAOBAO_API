<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Update extends Model
{
    protected $connection='mysql_paper';
    protected $table = 'T_APP_UPDATE';
    protected $primaryKey = 'VersionID';
}

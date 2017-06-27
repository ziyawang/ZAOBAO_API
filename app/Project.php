<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $connection='mysql_paper';
    protected $table = 'T_P_PROJECT';
    protected $primaryKey = 'projectId';
}

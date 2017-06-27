<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $connection='mysql_paper';
    protected $table = 'T_P_NOTE';
    protected $primaryKey = 'noteId';
}

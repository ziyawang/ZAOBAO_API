<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Messages extends Model
{
    protected $connection='mysql_paper';
    protected $table = 'T_P_MESSAGE';
    protected $primaryKey = 'messageId';
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BreakDown extends Model
{
    protected $table = 'breakdowns';

    protected $fillable = ['start_timestamp', 'end_timestamp', 'time_expressions', 'breakdown_result'];

}

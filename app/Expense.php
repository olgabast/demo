<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'datetime', 'description', 'amount', 'comment'
    ];

    protected $dateFormat = 'Y-m-d H:i';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'datetime'
    ];

    /** RELATIONS **/

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserScore extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','eventScore','followers','stars',
    ];

	public function totalScore()
    {
        return $this->eventScore*0.4 + $this->stars*0.4 + $this->followers*0.2;
    }
}

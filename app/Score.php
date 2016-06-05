<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username','eventScore','followers','stars','score','updated_at',
    ];

    public function getTotalScore()
    {
        return $this->eventScore*0.4 + $this->stars*0.4 + $this->followers*0.2;
    }
}

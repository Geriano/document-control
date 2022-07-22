<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'procedur_id',
        'value',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function procedur()
    {
        return $this->hasOne(Procedur::class, 'id', 'procedur_id');
    }
}

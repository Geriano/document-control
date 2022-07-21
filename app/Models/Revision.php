<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Revision extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'document_id',
        'code',
        'expired_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function approvers()
    {
        return $this->hasMany(Approver::class, 'approverable')->orderBy('position');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function approvals()
    {
        return $this->morphMany(Approval::class, 'approvable')->orderBy('position');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function document()
    {
        return $this->hasOne(Document::class, 'id', 'document_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function procedurs()
    {
        return $this->hasMany(Procedur::class, 'revision_id', 'id')
                    ->orderBy('position')
                    ->whereNull('parent_id')
                    ->with(['childs'])
                    ->withCount('childs');
    }

    /**
     * @inheritdoc
     */
    public static function boot()
    {
        parent::boot();

        static::created(function (Revision $revision) {
            Revision::where('id', '!=', $revision->id)
                    ->where('created_at', '<=', $revision->created_at)
                    ->orderBy('created_at', 'desc')
                    ->update([
                        'expired_at' => now(),
                    ]);
        });
    }
}

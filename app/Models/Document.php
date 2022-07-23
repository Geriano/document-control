<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'code',
        'max_revision_interval',
    ];

    /**
     * @var string[]
     */
    protected $with = [
        'revision',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function approvers()
    {
        return $this->morphMany(Approver::class, 'approverable')->orderBy('position');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function approvals()
    {
        return $this->morphMany(Approval::class, 'approvalable')->orderBy('position');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function revisions()
    {
        return $this->hasMany(Revision::class, 'document_id', 'id')->orderBy('created_at', 'desc');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function revision()
    {
        return $this->hasOne(Revision::class, 'document_id', 'id')->orderBy('created_at', 'desc');
    }
}

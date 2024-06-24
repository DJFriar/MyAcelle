<?php

namespace Acelle\Model;

use Illuminate\Database\Eloquent\Model;
use Acelle\Library\Traits\HasUid;

class Transaction extends Model
{
    use HasUid;

    // wait status
    public const STATUS_PENDING = 'pending';
    public const STATUS_FAILED = 'failed';
    public const STATUS_SUCCESS = 'success';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'error', 'status'
    ];

    /**
     * Invoice.
     */
    public function invoice()
    {
        return $this->belongsTo('Acelle\Model\Invoice');
    }

    /**
     * Is failed.
     */
    public function isFailed()
    {
        return $this->status == self::STATUS_FAILED;
    }

    /**
     * Set failed.
     */
    public function setFailed($error = null)
    {
        $this->status = self::STATUS_FAILED;
        $this->error = $error;
        $this->save();
    }

    /**
     * Set as success.
     */
    public function setSuccess()
    {
        $this->status = self::STATUS_SUCCESS;
        $this->save();
    }

    // Transaction that needs admin review
    public function allowManualReview()
    {
        return $this->allow_manual_review;
    }

    public static function scopePending($query)
    {
        $query = $query->where('status', Transaction::STATUS_PENDING);
    }
}

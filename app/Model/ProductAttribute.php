<?php

namespace Acelle\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Acelle\Library\Traits\HasUid;

class ProductAttribute extends Model
{
    use HasFactory;
    use HasUid;

    protected $fillable = ['attribute_id', 'product_id', 'value'];
}

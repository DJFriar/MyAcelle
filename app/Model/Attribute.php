<?php

namespace Acelle\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Acelle\Library\Traits\HasUid;

class Attribute extends Model
{
    use HasFactory;
    use HasUid;

    public static function newDefault()
    {
        $attribute = new self();
        $attribute->uid =  uniqid();
        return $attribute;
    }

    public static function scopeSearch($query, $keyword)
    {
        if ($keyword) {
            $query =  $query->where('name', 'like', '%'.$keyword.'%');
        }
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function fillParams($params)
    {
        $this->name = $params['name'] ?? '';
        $this->description = $params['description'] ?? null;
        $this->category_id = $params['category_id'] ?? null;
    }

    public function saveFromParams($params)
    {
        // fill
        $this->fillParams($params);

        $rules = [
            'name'   => ['required'],
        ];

        // validation
        $validator = \Validator::make($params, $rules);

        // check if has errors
        if ($validator->fails()) {
            return $validator;
        }

        // save to db
        $this->save();

        // return false
        return $validator;
    }
}

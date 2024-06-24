<?php

namespace Acelle\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Acelle\Library\Traits\HasUid;
use Acelle\Model\Attribute;

class Category extends Model
{
    use HasFactory;
    use HasUid;

    public function attributes()
    {
        return $this->hasMany(Attribute::class);
    }

    public static function newDefault($type)
    {
        $categories = new self();
        return $categories;
    }

    public static function scopeSearch($query, $keyword)
    {
        if ($keyword) {
            $query =  $query->where('name', 'like', '%'.$keyword.'%');
        }
    }

    public function products()
    {
        return $this->hasMany(Products::class, 'category_id', 'id');
    }

    public function fillParams($params)
    {
        $this->name = $params['name'] ?? '';
        $this->description = $params['description'] ?? null;
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

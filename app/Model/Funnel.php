<?php

namespace Acelle\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Acelle\Library\Traits\HasUid;

class Funnel extends Model
{
    use HasFactory;
    use HasUid;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    public static function newDefault()
    {
        $template = new self();
        $template->status = self::STATUS_ACTIVE;
        return $template;
    }

    public static function scopeSearch($query, $keyword)
    {
        if ($keyword) {
            $query =  $query->where('name', 'like', '%'.$keyword.'%');
        }
    }

    public function fillParams($params)
    {
        $this->name = $params['name'];
        $this->message = $params['message'];
        //$this->status = $params['status'];
    }

    public static function getTags()
    {
        return [
            'first_name',
            'phone',
            'last_name',
            'email',
            'username',
            'company',
            'address',
            'birth_date',
            'anniversary_date',
            'state',
            'event_date',
            'website'
        ];
    }

    public function saveFromParams($params)
    {
        // fill
        $this->fillParams($params);

        // validation
        $validator = \Validator::make($params, [
            'name'   => ['required'],
            //'message'   => 'required',
            //'status' => 'required',
        ]);

        // check if has errors
        if ($validator->fails()) {
            return $validator;
        }

        if (isset($params['picture'])) {
            // save image
            $this->uploadImage($params['picture']);
        }


        // save to db
        $this->save();

        // return false
        return $validator;
    }

    /**
     * Update sending server
     */
    public function updateFromParams($params)
    {
        // fill
        $this->fillParams($params);

        // validation
        $validator = \Validator::make($params, [
            'name'    => ['required', 'max:255', Rule::unique('funnels')->ignore($this) ],
            //'message'   => 'required',
            //'status'  => 'required',
        ]);

        // check if has errors
        if ($validator->fails()) {
            return $validator;
        }

        if ($params['picture']) {
            // save image
            $this->uploadImage($params['picture']);
        }
        // save to db
        $this->save();

        // return false
        return $validator;
    }

    public function getImageUrl()
    {
        return asset('storage/products/'.$this->file);
    }
    public function uploadImage($picture)
    {
        $path = storage_path('app/public/funnels/');
        /**
         * make folder if not exxist
         */
        !is_dir($path) &&
            mkdir($path, 0777, true);

        $this->file = 'funnels-'.$this->uid;

        $old_name = $picture->getClientOriginalName();
        $old_extension = $picture->getClientOriginalExtension();

        $this->file .= '.'.$old_extension;
        $picture->move($path, $this->file);
    }
}

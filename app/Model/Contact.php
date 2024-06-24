<?php

/**
 * Contact class.
 *
 * Model class for contacts
 *
 * LICENSE: This product includes software developed at
 * the Acelle Co., Ltd. (http://acellemail.com/).
 *
 * @category   MVC Model
 *
 * @author     N. Pham <n.pham@acellemail.com>
 * @author     L. Pham <l.pham@acellemail.com>
 * @copyright  Acelle Co., Ltd
 * @license    Acelle Co., Ltd
 *
 * @version    1.0
 *
 * @link       http://acellemail.com
 */

namespace Acelle\Model;

use Illuminate\Database\Eloquent\Model;
use Acelle\Library\Traits\HasUid;

class Contact extends Model
{
    use HasUid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'first_name', 'last_name', 'address_1', 'address_2', 'city', 'zip', 'url', 'company', 'phone', 'state', 'country_id',
        'tax_number', 'billing_address',
    ];

    /**
     * The rules for validation.
     *
     * @var array
     */
    public static $rules = array(
        'email' => 'required|email',
        'first_name' => 'required',
        'last_name' => 'required',
        'address_1' => 'required',
        'state' => 'required',
        'city' => 'required',
        'zip' => 'required',
        'url' => 'nullable|regex:/^https{0,1}:\/\//',
        //'url' => 'url', # do not use the default 'url' validator of Laravel, otherwise, error: preg_match(): Compilation failed: invalid range in character class at offset 20
        'company' => 'required',
        'country_id' => 'required',
    );

    /**
     * Associations.
     *
     * @var object | collect
     */
    public function country()
    {
        return $this->belongsTo('Acelle\Model\Country');
    }

    /**
     * Display contact name.
     *
     * @var string
     */
    public function name($locale)
    {
        $lastNameFirst = get_localization_config('show_last_name_first', $locale);

        if ($lastNameFirst) {
            return htmlspecialchars(trim($this->last_name.' '.$this->first_name));
        } else {
            return htmlspecialchars(trim($this->first_name.' '.$this->last_name));
        }

        return $this->first_name.' '.$this->last_name;
    }

    /**
     * Display contact country name.
     *
     * @var string
     */
    public function countryName()
    {
        return $this->country ? $this->country->name : '';
    }
}

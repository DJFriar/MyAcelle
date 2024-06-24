<?php

namespace Acelle\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Acelle\Library\Traits\HasUid;

class Order extends Model
{
    use HasFactory;
    use HasUid;
    public const STATUS_ACTIVE = 'active'; // chờ xử lý
    public const STATUS_INPROGRESS = 'inprogress'; // chờ xử lý
    public const STATUS_RETURN_CASH = 'return'; // hoàn tiền
    public const STATUS_RETURN_GOOD = 'return'; // hoàn hàng
    public const STATUS_REMOVE = 'remove'; // hủy đơn hàng
    public const PACKAGE_ACTIVE = 'active'; // đã đóng gói
    public const PACKAGE_INACTIVE = 'inactive'; // đang đóng gói
    public const PAYMENT_CODE = 'code'; // thanh toán tiền mặt
    public const PAYMENT_CASH = 'cash'; // thanh toán chuyển khoản
    public const PAYMENT_ACTIVE = 'inactive'; // Đã thanh toán
    public const PAYMENT_INACTIVE = 'inactive'; // chuua thanh toan
    public const TRANFER_INPROGRESS = 'inprogress'; // dang van chuyen
    public const TRANFER_HANDED = 'handed'; // đã giao hàng
    public const TRANFER_FAILD = 'faild'; // giao hàng thất bại

    public const BASE_DIR = 'app/public/';
    public const PRODUCT_DIR = 'products';
    public const PRODUCT_THUMB_DIR = 'thumbs';
    public const PRODUCT_IMAGE_PATH = 'product';
    public const PRODUCT_THUMB_PATH = 'product-thumb';

    public static function newDefault()
    {
        $order = new self();
        $order->status = self::STATUS_INPROGRESS;
        $order->uid =  uniqid();
        return $order;
    }
    public static function scopeSearch($query, $keyword)
    {
        if ($keyword) {
            $query =  $query->where('name', 'like', '%'.$keyword.'%');
        }
    }
    public static function findByUid($uid)
    {
        $first = self::where('uid', '=', $uid)->first();
        return $first ? $first : null;
    }
    public static function getListActive()
    {
        return self::where('status', '=', self::STATUS_ACTIVE)->get();
    }
}

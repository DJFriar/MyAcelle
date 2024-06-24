<?php

namespace Acelle\Model;

use Illuminate\Database\Eloquent\Model;
use Acelle\Library\Traits\HasUid;

class Product extends Model
{
    use HasUid;

    public const STATUS_ACTIVE = 'active';  // đang hoạt động
    public const STATUS_INACTIVE = 'inactive'; //  Không hoạt động
    public const STATUS_INPROGRESS = 'inprogress'; // Chờ duyệt
    public const STATUS_DRAPP = 'drap'; // bản nháp
    public const STATUS_WARNING = 'warning'; // Vi phạm
    public const STATUS_REMOVE = 'remove'; // Đã xóa

    public static $itemsPerPage = 16;

    // paths
    public const PATH_BASE = 'products';
    public const PATH_IMAGES = 'images';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['category_id', 'title', 'content'];

    // belongs to customer
    public function customer()
    {
        return $this->belongsTo('Acelle\Model\Customer');
    }

    // belongs to source

    public function source()
    {
        return $this->belongsTo('Acelle\Model\Source');
    }

    public function productAttributes()
    {
        return $this->hasMany('Acelle\Model\ProductAttribute');
    }

    public function scopeFilter($query, $attribute, $value)
    {
        $query->where($attribute, '=', $value);
    }

    public function getBasePath($path = null)
    {
        $path = join_paths($this->customer->getBasePath(), self::PATH_BASE, $this->uid); // storage/app/products/000000/

        if (!\Illuminate\Support\Facades\File::exists($path)) {
            \Illuminate\Support\Facades\File::makeDirectory($path, 0777, true, true);
        }

        return $path;
    }

    public function getImagesPath($path = null)
    {
        $path = join_paths($this->getBasePath(), self::PATH_IMAGES, $this->uid); // storage/app/products/000000/

        if (!\Illuminate\Support\Facades\File::exists($path)) {
            \Illuminate\Support\Facades\File::makeDirectory($path, 0777, true, true);
        }

        return $path;
    }

    public function getImagePaths()
    {
        $path = $this->getImagesPath();
        $files = array_diff(scandir($path), array('.', '..'));
        $paths = [];

        foreach ($files as $filename) {
            $paths[] = join_paths($this->getImagesPath(), $filename);
        }

        return $paths;
    }

    public function removeImageByUrl($url)
    {
        $paths = $this->getImagePaths();

        foreach ($paths as $path) {
            if (\Acelle\Helpers\generatePublicPath($path) == $url) {
                \Illuminate\Support\Facades\File::delete($path);
            }
        }
    }

    public function getImageUrls()
    {
        $paths = $this->getImagePaths();
        $urls = [];

        foreach ($paths as $path) {
            $urls[] = \Acelle\Helpers\generatePublicPath($path);
        }

        return $urls;
    }

    public function getImageUrl()
    {
        $urls = $this->getImageUrls();

        return empty($urls) ? null : $urls[0];
    }

    public static function generateWidgetProductListHtmlContent($params)
    {
        $products = Product::limit($params['count']);
        $sort = explode('-', $params['sort']);

        if (!isset($sort[1]) || !isset($params['count']) || !isset($params['cols'])) {
            return "";
        }

        $products = $products->orderBy(explode('-', $params['sort'])[0], explode('-', $params['sort'])[1]);
        $products = $products->get();

        return view('products.widgetProductListHtmlContent', [
            'products' => $products,
            'options' => $params,
        ]);
    }

    public static function generateWidgetProductHtmlContent($params)
    {
        $product = self::findByUid($params['id']);

        // replace tags
        $html = $params['content'];
        $html = str_replace('*|PRODUCT_NAME|*', $product->title, $html);
        $html = str_replace('*|PRODUCT_DESCRIPTION|*', substr(strip_tags($product->description), 0, 200), $html);
        $html = str_replace('*|PRODUCT_PRICE|*', format_price($product->price), $html);
        $html = str_replace('*|PRODUCT_QUANTITY|*', $product->title, $html);
        $html = str_replace('*|PRODUCT_URL|*', action('ProductController@index'), $html);
        $html = str_replace('*%7CPRODUCT_URL%7C*', action('ProductController@index'), $html);

        // try to replace product image
        $dom = new \DOMDocument();
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'), LIBXML_NOWARNING | LIBXML_NOERROR | LIBXML_HTML_NODEFDTD);

        $imgs = $dom->getElementsByTagName("img");
        foreach ($imgs as $img) {
            $att = $img->getAttribute('builder-element');
            if ($att == 'ProductImgElement') {
                $img->setAttribute('src', $product->getImageUrl());
            }
        }

        return $dom->saveHTML();
    }

    public static function newDefault()
    {
        $product = new self();
        $product->status = self::STATUS_DRAPP;
        $product->uid =  uniqid();
        return $product;
    }

    public static function scopeSearch($query, $keyword)
    {
        if ($keyword) {
            $query =  $query->where('name', 'like', '%'.$keyword.'%');
        }
    }

    public function smsCategory()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function fillParams($params)
    {
        $this->title = $params['title'] ?? '';
        $this->content = $params['content'] ?? '';

        // fill category
        if (isset($params['category_uid'])) {
            $this->category_id = Category::findByUid($params['category_uid'])->id;
        }
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
            'title'   => ['required'],
            'category_uid'   => ['required'],
        ]);

        // check if has errors
        if ($validator->fails()) {
            return $validator;
        }

        // save to db
        $this->save();

        // save attributes values
        $this->productAttributes()->delete();
        if ($params['product_attributes']) {
            foreach ($params['product_attributes'] as $attributeUid => $value) {
                if ($value) {
                    $this->setValueByAttribute(Attribute::findByUid($attributeUid), $value);
                }
            }
        }

        // upload images
        if (isset($params['images'])) {
            foreach ($params['images'] as $file) {
                $this->uploadImage($file);
            }
        }

        // remove images
        if (isset($params['delete_images'])) {
            foreach ($params['delete_images'] as $url) {
                $this->removeImageByUrl($url);
            }
        }

        // return false
        return $validator;
    }

    public function uploadImage($file)
    {
        $imageId = uniqid();
        $file->move($this->getImagesPath(), $imageId);
    }

    public function setValueByAttribute($attribute, $value)
    {
        $exist = $this->productAttributes()
            ->where('attribute_id', $attribute->id)
            ->first();

        if ($exist) {
            $exist->value = $value;
            $exist->save();
        } else {
            ProductAttribute::create([
                'attribute_id' => $attribute->id,
                'product_id' => $this->id,
                'value' => $value,
            ]);
        }
    }

    public function getValueByAttribute($attribute)
    {
        $av = $this->productAttributes()
            ->where('attribute_id', $attribute->id)
            ->first();

        return $av ? $av->value : null;
    }
}

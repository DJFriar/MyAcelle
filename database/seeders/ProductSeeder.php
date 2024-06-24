<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
use Illuminate\Support\Facades\Storage;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // delete all records
        DB::table('products')->delete();
        $user  = \Acelle\Model\User::find(1)->first();
        $uid = $user->uid;
        $customer_id =  $user->customer_id;
        $price  = [ 5000, 4600, 50000, 137000, 500, 67000];

        $file = [
            'product-1.png', 'product-2.png', 'product-3.png', 'product-4.png', 'product-5.jpg', 'product-6.jpg'
        ];

        $stock = [10, 20, 5, 20, 100, 4, 16];

        $status = [
            \Acelle\Model\Product::STATUS_ACTIVE,
            \Acelle\Model\Product::STATUS_INACTIVE,
            \Acelle\Model\Product::STATUS_INPROGRESS,
            \Acelle\Model\Product::STATUS_DRAPP,
            \Acelle\Model\Product::STATUS_WARNING,
            \Acelle\Model\Product::STATUS_REMOVE,
        ];

        $curency = ['đ','$'];

        $unit_pack = [
            ['cái', '10 cái / thùng'],
            ['cuốn', '20 cuốn/ thùng'],
            ['cuộn', '30 cuộn / thùng'],
            ['Gói', '30 gói / thùng'],
        ];

        $name = [
            'Mật ong rừng Phú Quốc',
            'Bạch tuộc phan thiết',
            'Phòng tậm Gym Fitnes Happy',
            'Bệnh viện quốc tế Sangri-la',
            'Lẩu dựng bò tơ củ chi',
            'Sashimi korea one',
            'Bột sắn dẫy',
            'giầy thể thao thời trang',
            'dụng cụ thể thao'
        ];
        $description = [
            'Những nơi mà bạn thích nhất là những nơi đã được kiểm chứng là làm ăn thành công',
            'Những nơi mà bạn muốn đến là những nơi đã có thương hiêỵ cực chuẩn, và sẽ thành công',
            'chúng tôi là những người chiến thằng trong cuộc thi Marathon về độ lỳ lợm và chịu chơi nhất trong năm',
            'Bạn có tin không? đứa nào cũng nói am hiểu thị trường, nhưng chả đứa nào biết bạn sẽ mua gì trong hôm nay, ngoại trừ chính bạn',
            'Xu hướng là những thứ bạn thấy thích và muốn cải tiến nó, nhưng trong một khoảnh khắc bạn chợt nhận ra, idea của mình không bằng cái gì đó mà mình vừa chợt nhìn thấy, quay lại coi xem sao, Oh không, nó vẫn không bằng mình',
            'Thật tuyệt vời, chúc bạn có sản phẩm tốt nhất trong cuộc đời của bạn, hãy giữ gìn chúng nghen'
        ];
        $content = [
            'làm content là một công việc cực kỳ vất vả và mất công sức, hãy thiết kế cho tôi một công cụ để giảm tải nghen bạn.',
            'Chat GPT và Bing Chat đều là 2 AI hiện đang làm tốt nhất những công việc tạo nội dung cho bạn, hãy tận dụng tiệt để những gì mà công nghệ mang lại cho bạn nghen, chúng thật tuyệt vời đóa bạn',
        ];

        $meta = [
            'Chúng tôi là meta của sản phẩm này',
            'sản phẩm này có meta chính là tôi đó bạn, chúc mừng bạn đã thành công'
        ];

        for($i = 0; $i < 10;$i++) {
            $unitpack = $unit_pack[array_rand($unit_pack)];
            $unit = $unitpack[0];
            $pack = $unitpack[1];
            //$filename = $file[array_rand($file)];
            DB::table('products')->insert([
                [
                    'uid' => $uid,
                    'customer_id' => $customer_id,
                    'name' =>  $name[array_rand($name)],
                    'description' => $description[array_rand($description)],
                    'content' => $content[array_rand($content)],
                    'price' => $price[array_rand($price)],
                    'meta' => $meta[array_rand($meta)],
                    'stock' => $stock[array_rand($stock)],
                    'unit' => $unit,
                    'pack' => $pack,
                    'curency' => $curency[array_rand($curency)],
                    //'file'=> $filename,
                    'status' => $status[array_rand($status)],
                ]
            ]);
            //$url = "https://brandviet.vn/wp-content/uploads/2023/07/".$filename;
            //$contents = file_get_contents($url);
            //$name = substr($url, strrpos($url, '/') + 1);
            //Storage::put('public/products/'.$filename, $contents);
        }

    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
use Illuminate\Support\Facades\Storage;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // delete all records
        DB::table('orders')->delete();

        $products = \Acelle\Model\Order::all();
        foreach($products as $product) {

            $status = [
                \Acelle\Model\Product::STATUS_ACTIVE,
                \Acelle\Model\Product::STATUS_INACTIVE,
                \Acelle\Model\Product::STATUS_INPROGRESS,
                \Acelle\Model\Product::STATUS_DRAPP,
                \Acelle\Model\Product::STATUS_WARNING,
                \Acelle\Model\Product::STATUS_REMOVE,
            ];
            DB::table('products')->insert([
                [
                    'uid' => $product->uid,
                    'customer_id' => $product->customer_id,
                    'name' =>  $product->name,
                    'description' => $product->description,
                    'content' => $product->content,
                    'price' => $product->price,
                    'meta' => $product->meta,
                    'stock' => $product->stock,
                    'unit' => $product->unit,
                    'pack' => $product->pack,
                    'curency' => $product->curency,
                    'file' => $product->file,
                    'status' => $status[array_rand($status)],
                    'amount' => $amount[array_rand($amount)],
                    'tax' => $tax[array_rand($tax)],
                    'move' => $move[array_rand($move)],
                    'total' => $total[array_rand($total)],
                    'transport' => $transport[array_rand($transport)],
                    'delivery' => $delivery[array_rand($delivery)],
                    'receive_name' => $receive_name[array_rand($receive_name)],
                    'receive_address' => $receive_address[array_rand($receive_address)],
                    'receive_phone' => $receive_phone[array_rand($receive_phone)],

                ]
            ]);
            $tax = [1000,2300,5600,7000,8000,9000];
            $move = '';
            $transport = '';
            $delivery = '';
            $amount = [10000, 20000, 50000, 20000, 100000, 400000, 1600000];
            $receive_phone = [
                '0905.3412.435',
                '0989.345.556',
                '0877.267.577',
                '0909.154.556',
                '0909.234.535',
                '0907.163.587',
                '0989.656.787',
                '0908.657.579',
                '0878.556.676'
            ];
            $receive_name = [
                'Nguyên Nhật Long',
                'Đàm thế Phan',
                'Chu hậu bảo',
                'la văn giáp',
                'Chường trinh',
                'Tô hiệu',
                'Nguyễn Khuyến',
                'Mạc văn can',
                'Mạc thị bưởi'
            ];
            $receive_address = [
                '453/86 Lê Văn Sỹ, 12, Quận 3, Hồ Chí Minh',
                ' 532 Lý Thái Tổ, 10, Quận 10, Hồ Chí Minh',
                '175 - 786 Nguyễn Kiệm, 13, Gò Vấp, Hồ Chí Minh',
                '201A Nguyễn Chí Thanh, 12, Quận 5, Hồ Chí Minh',
                '201b Nguyễn Chí Thanh, 12, Quận 5, Hồ Chí Minh',
                '5 Nguyễn Thộng 2, 12, Quận 3, Hồ Chí Minh',
                '288 Cống Quỳnh, Phạm Ngũ Lão, Quận 1, Hồ Chí Minh',
                'Vinmec 208 Nguyễn Hữu Cảnh, Bến Nghé, Quận 1, Hồ Chí Minh',
                '281a Lê Văn Lương, Phước Kiển, Nhà Bè, Hồ Chí Minh'
            ];

        }

    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
use Illuminate\Support\Facades\Storage;

class FunnelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // delete all records
        DB::table('funnels')->delete();
        $file = [
            'funnel-1.jpg', 'funnel-2.jpg', 'funnel-3.jpg', 'funnel-4.jpg', 'funnel-5.jpg'
        ];
        $name = [
            'Thời trang',
            'Shop Vest nam',
            'Template Fashion',
            'Trang dịch vụ',
            'Trang bản đồ',
            'Shopping',
            'create',
            'Event',
        ];
        $message = [
            'Chién dịch số 1 đã thành công rực rỡ, kính chúc quý khách hàng thêm an tâm vào chúng tồi, chân thành cảm ơn',
            'chiến dịch mùa hè xanh đã hoàn thành suất sắc bài viết của chúng toi',
            'chiến dịch tây nguyên cuối cùng cũng dành chiến thắng bằng một cách không thể ngờ',
            'chiến dịch từ thiện đã dành được nhiều ưu ái trong, đoi khi trong cuộc sống cần nhiều điều hay ho',
            'chiến dịch việt bắc là chiến dịch đã đem lại thành công lớn cho việt nam cộng hòa',
            'lá cải campaign là một phạm trì vui tính trong công việc của 3 anh em nhà tôi',
            'flat Sale tháng 5 chỉ có những người đáng được hưởng saleoff thì hãy cung cấp cho họ những ưu ái cao nhát có thể',
            'Sale off trung thu dành cho các bé nghen, không phải dành cho người lớn đâu',
            'Black friday giảm tất cả các mặt hằng thưa đại chúng, hay nhanh chân nhanh tay nghen, cấm chen lấn sô đẩy',
            'Happy new year chúc mừng năm mới với nhiều thành công mới hon cho công cuộc phát triêrn của bạn',
        ];
        $status = ['active','inactive'];
        for($i = 0; $i < 10;$i++) {
            $filename = $file[array_rand($file)];
            DB::table('funnels')->insert([
                [
                    'uid'       => uniqid(),
                    'name'      => $name[array_rand($name)],
                    'message'   => $message[array_rand($message)],
                    'file'      => $filename,
                    'status'    => $status[array_rand($status)],
                ]
            ]);
            $url = "https://brandviet.vn/wp-content/uploads/2023/07/".$filename;
            $contents = file_get_contents($url);
            //$name = substr($url, strrpos($url, '/') + 1);
            Storage::put('public/funnels/'.$filename, $contents);

        }

    }
}

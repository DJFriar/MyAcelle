<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CategoryAttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Remove all categories
        foreach(\Acelle\Model\Category::all() as $category) {
            $category->delete();
        }

        // data
        $datas = [
            [
                'name' => 'Laptop',
                'attributes' => ['Memory', 'CPU', 'Ram', 'Monitor'],
            ],
            [
                'name' => 'Watch',
                'attributes' => ['Case size', 'Weight', 'Glass', 'Power supply', 'Water resistance'],
            ],
        ];

        foreach ($datas as $cat) {
            $category = \Acelle\Model\Category::create([
                'name' => $cat['name'],
            ]);

            // attributes
            foreach ($cat['attributes'] as $att) {
                $attribute = \Acelle\Model\Attribute::create([
                    'name' => $att,
                    'category_id' => $category->id,
                ]);
            }
        }
    }
}

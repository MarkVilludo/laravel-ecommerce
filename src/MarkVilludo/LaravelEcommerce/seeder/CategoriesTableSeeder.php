<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\ChildSubCategory;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Product categories initial
        DB::table('categories')->truncate();
        Category::insert([
            [
                'title' => 'Electronic Device',
                'description'  => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy',
                'status'    => 1, //Avai
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Electronic Accessories',
                'description'  => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy',
                'status'    => 1,
                'created_at'    => date('Y-m-d H:i:s'),
            ],
                [
                'title' => 'Consumer Appliances',
                'description'  => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy',
                'status'    => 1,
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Health and Beauty',
                'description'  => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy',
                'status'    => 1,
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Babies and Toys',
                'description'  => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy',
                'status'    => 1,
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Groceries and Pets',
                'description'  => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy',
                'status'    => 1,
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Home and lifestyle',
                'description'  => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy',
                'status'    => 1,
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Homen Fashions',
                'description'  => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy',
                'status'    => 1,
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Sports and Travel',
                'description'  => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy',
                'status'    => 1,
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Motors',
                'description'  => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy',
                'status'    => 1,
                'created_at'    => date('Y-m-d H:i:s'),
            ]
        ]);
       
        //Health and Beauty (4)
        //Future table seeder for subcategories
        SubCategory::insert([
            [
                'category_id' => 4,
                'title' => 'Bath and Body',
                'description'  => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy',
                'status'    => 1,
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'category_id' => 4,
                'title' => 'Beauty tools',
                'description'  => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy',
                'status'    => 1,
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'category_id' => 4,
                'title' => 'Fragrance',
                'description'  => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy',
                'status'    => 1,
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'category_id' => 4,
                'title' => 'Hair Care',
                'description'  => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy',
                'status'    => 1,
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'category_id' => 4,
                'title' => 'Makeup',
                'description'  => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy',
                'status'    => 1,
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'category_id' => 4,
                'title' => "Men's Care",
                'description'  => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy',
                'status'    => 1,
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'category_id' => 4,
                'title' => "Personal Care",
                'description'  => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy',
                'status'    => 1,
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'category_id' => 4,
                'title' => "Skin Care",
                'description'  => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy',
                'status'    => 1,
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'category_id' => 4,
                'title' => "Food Supplements",
                'description'  => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy',
                'status'    => 1,
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'category_id' => 4,
                'title' => "Medical Supplies",
                'description'  => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy',
                'status'    => 1,
                'created_at'    => date('Y-m-d H:i:s'),
            ],

        ]);
       
        //Makeup (5)
        //Future table seeder for child categories
        ChildSubCategory::insert([
            [
                'category_id' => 4,
                'sub_category_id' => 5,
                'title' => "Body",
                'description'  => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy',
                'status'    => 1,
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'category_id' => 4,
                'sub_category_id' => 5,
                'title' => "Eyes",
                'description'  => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy',
                'status'    => 1,
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'category_id' => 4,
                'sub_category_id' => 5,
                'title' => "Face",
                'description'  => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy',
                'status'    => 1,
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'category_id' => 4,
                'sub_category_id' => 5,
                'title' => "Lips",
                'description'  => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy',
                'status'    => 1,
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'category_id' => 4,
                'sub_category_id' => 5,
                'title' => "Cheeks",
                'description'  => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy',
                'status'    => 1,
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'category_id' => 4,
                'sub_category_id' => 5,
                'title' => "Brushes and Sets",
                'description'  => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy',
                'status'    => 1,
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'category_id' => 4,
                'sub_category_id' => 5,
                'title' => "Make Up Tools",
                'description'  => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy',
                'status'    => 1,
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'category_id' => 4,
                'sub_category_id' => 5,
                'title' => "Skin",
                'description'  => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy',
                'status'    => 1,
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'category_id' => 4,
                'sub_category_id' => 5,
                'title' => "Accessories",
                'description'  => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy',
                'status'    => 1,
                'created_at'    => date('Y-m-d H:i:s'),
            ]
        ]);
    }
}

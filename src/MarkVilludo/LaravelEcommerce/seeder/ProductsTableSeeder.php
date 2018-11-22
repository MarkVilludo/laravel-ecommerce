<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use App\Models\ProductVariant;
use App\Models\ProductInfo;
use App\Models\Product;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('products')->truncate();
        //future additional fields , availability (kung kaninong user type lang ipapakita yung product), Tax
        //Eyes sub categories
        Product::create(array(
            'name' => 'Auto Eyebrow Pen',
            'sub_category_id'    => 5,
            'child_sub_category_id'    => 2,
            'description'    => 'This eyebrow pen comes in twist form, so no more messing around with sharpeners. Ideal for enhancing your brows to achieve that perfect arch effect. It comes in various brown shades for natural blending.',
            'regular_price'    => 300,
            'selling_price'    => 200,
            'created_at'    => date('Y-m-d H:i:s'),
        ));

        ProductVariant::create(array(
            'size' => null,
            'product_id' => 1,
            'inventory'    => 100, //inventory by default 
            'created_at'    => date('Y-m-d H:i:s'),
        ));

        ProductVariant::create(array(
            'size' => null,
            'product_id' => 1,
            'inventory'    => 100, //inventory by default 
            'created_at'    => date('Y-m-d H:i:s'),
        ));

        //2
        Product::create(array(
            'name' => 'Defining Eyeliner',
            'sub_category_id'    => 5,
            'child_sub_category_id'    => 2,
            'description'    => 'This twist pen liner is intensely colored and is perfect for giving definition to your eyes. It glides smoothly and gently onto your skin, and is not heavy on the waterline. You can even use this product to touch up your eyebrows!',
            'regular_price'    => 300,
            'selling_price'    => 200,
            'status'    => 1, //Publish or un published 
            'created_at'    => date('Y-m-d H:i:s'),
        ));

        ProductVariant::create(array(
            'size' => null,
            'product_id'    => 2,
            'inventory'    => 100, //inventory by default 
            'created_at'    => date('Y-m-d H:i:s'),
        ));

        ProductVariant::create(array(
            'size' => null,
            'product_id'    => 2,
            'inventory'    => 100, //inventory by default 
            'created_at'    => date('Y-m-d H:i:s'),
        ));

        //3
        Product::create(array(
            'name' => 'Double-Up Mascara',
            'sub_category_id'    => 5,
            'child_sub_category_id'    => 2,
            'description'    => 'This mascara highlights your eyes like no other. Its special formula is designed to make your lashes two times longer and stronger the moment you apply it without weighing your eyes down. This mascara also lasts for a long time without running or smudging, and the unique, fine-bristled wand lets you glide this product on easily without having to worry about clumping. No wonder it’s considered one of the best, if not the best, mascaras around by local makeup gurus. For best results, curl your lashes with an eyelash curler first. Then, starting at the base of your upper lashes, gently draw the wand upward until you reach the tips. Hold the wand vertically and run it across your lower lashes to color them.',
            'regular_price'    => 300,
            'selling_price'    => 200,
            'status'    => 1, //Publish or un published 
            'featured'    => 1, //top picks
            'created_at'    => date('Y-m-d H:i:s'),
        ));

        ProductVariant::create(array(
            'size' => null,
            'product_id'    => 3,
            'inventory'    => 100, //inventory by default 
            'created_at'    => date('Y-m-d H:i:s'),
        ));

        //4
        Product::create(array(
            'name' => 'Duo Eye Enhancer',
            'sub_category_id'    => 5,
            'child_sub_category_id'    => 2,
            'description'    => 'This mascara highlights your eyes like no other. Its special formula is designed to make your lashes two times longer and stronger the moment you apply it without weighing your eyes down. This mascara also lasts for a long time without running or smudging, and the unique, fine-bristled wand lets you glide this product on easily without having to worry about clumping. No wonder it’s considered one of the best, if not the best, mascaras around by local makeup gurus. For best results, curl your lashes with an eyelash curler first. Then, starting at the base of your upper lashes, gently draw the wand upward until you reach the tips. Hold the wand vertically and run it across your lower lashes to color them.',
            'regular_price'    => 300,
            'selling_price'    => 200,
            'status'    => 1, //Publish or un published 
            'featured'    => 1, //top picks
            'created_at'    => date('Y-m-d H:i:s'),
        ));

        ProductVariant::create(array(
            'size' => null,
            'product_id'    => 4,
            'inventory'    => 100, //inventory by default 
            'created_at'    => date('Y-m-d H:i:s'),
        ));
        ProductVariant::create(array(
            'size' => null,
            'product_id'    => 4,
            'inventory'    => 100, //inventory by default 
            'created_at'    => date('Y-m-d H:i:s'),
        ));
        ProductVariant::create(array(
            'size' => null,
            'product_id'    => 4,
            'inventory'    => 100, //inventory by default 
            'created_at'    => date('Y-m-d H:i:s'),
        ));
        ProductVariant::create(array(
            'size' => null,
            'product_id'    => 4,
            'inventory'    => 100, //inventory by default 
            'created_at'    => date('Y-m-d H:i:s'),
        ));
        ProductVariant::create(array(
            'size' => null,
            'product_id'    => 4,
            'inventory'    => 100, //inventory by default 
            'created_at'    => date('Y-m-d H:i:s'),
        ));
        ProductVariant::create(array(
            'size' => null,
            'product_id'    => 4,
            'inventory'    => 100, //inventory by default 
            'created_at'    => date('Y-m-d H:i:s'),
        ));
        //Lips sub categories
        //5
        Product::create(array(
            'name' => 'Defining Lip Liner',
            'sub_category_id'    => 5,
            'child_sub_category_id'    => 2,
            'description'    => 'This lipliner may come in only one color, but it’s a stunning one that definitely fits all. It gives lips a pinkish, natural contour that perfectly complements your skin and other Fashion 21 lipstick shades.',
            'regular_price'    => 300,
            'selling_price'    => 200,
            'status'    => 1, //Publish or un published 
            'featured'    => 0, //top picks
            'created_at'    => date('Y-m-d H:i:s'),
        ));
        ProductVariant::create(array(
            'size' => null,
            'product_id'    => 5,
            'inventory'    => 100, //inventory by default 
            'created_at'    => date('Y-m-d H:i:s'),
        ));
        ProductVariant::create(array(
            'size' => null,
            'product_id'    => 5,
            'inventory'    => 100, //inventory by default 
            'created_at'    => date('Y-m-d H:i:s'),
        ));
        //6
        Product::create(array(
            'name' => 'Aqualicious Lipstick',
            'sub_category_id'    => 5,
            'child_sub_category_id'    => 2,
            'description'    => 'Aqualicious feels more like a balm than a lipstick—it’s amazingly moisturizing and contains Vitamin E—but it still provides the rich color of a normal lipstick. This product is perfect for lips that go dry during the day, and it’s available in a wide range of 10 different colors—from pink to nude—to fit any social event.',
            'regular_price'    => 300,
            'selling_price'    => 200,
            'status'    => 1, //Publish or un published 
            'featured'    => 1, //top picks
            'created_at'    => date('Y-m-d H:i:s'),
        ));
        ProductVariant::create(array(
            'size' => null,
            'product_id'    => 6,
            'inventory'    => 100, //inventory by default 
            'created_at'    => date('Y-m-d H:i:s'),
        ));
        ProductVariant::create(array(
            'size' => null,
            'product_id'    => 6,
            'inventory'    => 100, //inventory by default 
            'created_at'    => date('Y-m-d H:i:s'),
        ));
        ProductVariant::create(array(
            'size' => null,
            'product_id'    => 6,
            'inventory'    => 100, //inventory by default 
            'created_at'    => date('Y-m-d H:i:s'),
        ));
        ProductVariant::create(array(
            'size' => null,
            'product_id'    => 6,
            'inventory'    => 100, //inventory by default 
            'created_at'    => date('Y-m-d H:i:s'),
        ));
        ProductVariant::create(array(
            'size' => null,
            'product_id'    => 6,
            'inventory'    => 100, //inventory by default 
            'created_at'    => date('Y-m-d H:i:s'),
        ));
        ProductVariant::create(array(
            'size' => null,
            'product_id'    => 6,
            'inventory'    => 100, //inventory by default 
            'created_at'    => date('Y-m-d H:i:s'),
        ));
        ProductVariant::create(array(
            'size' => null,
            'product_id'    => 6,
            'inventory'    => 100, //inventory by default 
            'created_at'    => date('Y-m-d H:i:s'),
        ));
        ProductVariant::create(array(
            'size' => null,
            'product_id'    => 6,
            'inventory'    => 100, //inventory by default 
            'created_at'    => date('Y-m-d H:i:s'),
        ));
        ProductVariant::create(array(
            'size' => null,
            'product_id'    => 6,
            'inventory'    => 100, //inventory by default 
            'created_at'    => date('Y-m-d H:i:s'),
        ));
        ProductVariant::create(array(
            'size' => null,
            'product_id'    => 6,
            'inventory'    => 100, //inventory by default 
            'created_at'    => date('Y-m-d H:i:s'),
        ));

        //Product info
        $i = [1, 2, 3, 4, 5, 6];
        foreach ($i as $key => $value) {
            ProductInfo::insert(array(
                [
                    'product_id' => $value,
                    'title'    => "Details", //inventory by default 
                    'description'  => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
                    'created_at'    => date('Y-m-d H:i:s')
                ],
                [
                        'product_id' => $value,
                        'title'    => "Content + Care", //inventory by default 
                        'description'  => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
                        'created_at'    => date('Y-m-d H:i:s')
                ]
            ));
        }
    }
}

<?php

use Illuminate\Database\Seeder;
use Faker\Factory;
use App\Product;
use App\ProductExplain;
use App\ProductDetail;

class RecipesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        Product::truncate();
        ProductDetail::truncate();
        ProductExplain::truncate();

        foreach(range(1, 10) as $i)
        {
           $product = Product::create([
                'user_id' => $i,
                'name' => $faker->sentence,
                'description' => $faker->paragraph(mt_rand(10, 20)),
                'image' => 'test.png'
           ]) ;

           foreach(range(1, mt_rand(3, 12)) as $j)
           {
               ProductDetail::create([
        			'product_id' => $product->id,
        			'name' => $faker->word,
        			'qty' => mt_rand(1, 12).' Kg'
        		]);
           }

           foreach(range(1, mt_rand(5, 12)) as $k)
           {
               ProductExplain::create([
        			'product_id' => $product->id,
        			'description' => $faker->sentence,
        		]);
           }
        }
    }
}

<?php

use Illuminate\Database\Migrations\Migration;

class ChangeProductTypesToPostalDelivery extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE product MODIFY COLUMN type ENUM('Internet','TV','Voip','Device','Credit','Other','Postal') NOT NULL");

        $file = '/config/billingbase/post-invoice-product-ids';
        if (\Storage::exists($file) && \Module::collections()->has('BillingBase')) {
            $content = \Storage::get($file);
            preg_match_all('/\d+/', $content, $results);

            if (! empty($results)) {
                foreach ($results[0] as $value) {
                    \DB::table('product')->where('id', $value)
                        ->update(['type' => 'Postal']);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $products = \DB::table('product')->where('type', 'Postal')->get();
        $ids = null;

        foreach ($products as $product) {
            $ids[] = $product->id;
        }

        $productIds = implode(';\n', $ids);

        \Storage::put('/config/billingbase/post-invoice-product-ids', $productIds);
        \DB::table('product')->where('type', 'Postal')->update(['type' => 'Other']);

        DB::statement("ALTER TABLE product MODIFY COLUMN type ENUM('Internet','TV','Voip','Device','Credit','Other') NOT NULL");
    }
}

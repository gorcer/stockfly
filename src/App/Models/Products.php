<?php
/**
 * Created by PhpStorm.
 * User: gorcer
 * Date: 17.07.22
 * Time: 15:30
 */

namespace App\Models;


class Products extends \RedBeanPHP\SimpleModel {

    public function update() {

        if ($this->bean->avails) {
            $avails = json_decode($this->bean->avails, true);

            $this->bean->max_qty = max( $avails);
            $this->bean->total_qty = array_sum( $avails);
        }

        $price=0;
        if($this->bean->price) {
            $price = $this->bean->price;
            $this->bean->max_price = $price;
        }

        if ($this->bean->prices) {
            $prices = json_decode($this->bean->prices, true);

            $prices[]=$price;
            $this->bean->max_price = max( $prices);
        }

        $this->bean->updated_at = date('Y-m-d H:i:s');
    }

} 
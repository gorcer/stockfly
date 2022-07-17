<?php
/**
 * Created by PhpStorm.
 * User: gorcer
 * Date: 11.07.22
 * Time: 19:54
 */

namespace App\Controllers;


use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use RedBeanPHP\R;
use App\Models\Product;

class ProductController {

    public function searchBySlices($clusterId, RequestInterface $request) {
        $sliceIds = $request->input('slices', []);
        $operand = $request->input('operand', '&');

        // @refact to validator
        if (!in_array($operand, ['&', '|']))
            return 'Unknow operand';

        if (sizeof($sliceIds) == 0)
            return [];

        foreach($sliceIds as &$sliceId) {
            if (!is_numeric($sliceId)) {
                return 'Invalid slice';
            }
        }

        $result = R::findAll( 'products', "clusterId = :clusterId and slices @>  '[".join(',', $sliceIds)."]'", [':clusterId' => $clusterId]);

        return $result;
    }


    public function create($clusterId, RequestInterface $request) {
        $product = R::dispense('products');
        $product->title = $request->input('title', '');
        $product->cluster_id = $clusterId;
        $product->external_id = $request->input('external_id');
        $product->avails = $request->input('avails');
        $product->prices = $request->input('prices');
        $product->price = $request->input('price');
        $product->slices = $request->input('slices');
        $product->data = $request->input('data');

        R::store( $product );
        return $product;
    }

    public function update($clusterId, RequestInterface $request, $product=false) {

        if (!$product)
            $product = R::findOne( 'products', 'cluster_id = ? and external_id=?', [$clusterId, $request->input('external_id', $product->external_id)] );

        $product->title = $request->input('title', $product->title);
        $product->cluster_id = $request->input('cluster_id', $product->cluster_id);
        $product->avails = $request->input('avails', $product->avails);
        $product->prices = $request->input('prices', $product->prices);
        $product->price = $request->input('price', $product->price);
        $product->slices = $request->input('slices', $product->slices);
        $product->data = $request->input('data', $product->data);
        R::store( $product );
        return $product;
    }


    public function set($clusterId, RequestInterface $request) {
        $product = R::findOne( 'products', 'cluster_id = ? and external_id=?', [$clusterId, $request->input('external_id')] );

        if ($product == null) {
            $product= $this->create($clusterId, $request);
        } else {
            $product= $this->update($clusterId, $request, $product);
        }

        if ($product->total_qty == 0 || $product->max_price == 0) {
            R::trash($product);
            return $product;
        } else {
            return $product;
        }


    }


        public function assignSlice($clusterId, $externalId, RequestInterface $request, ResponseInterface $response) {

        $product = R::findOne( 'products', 'cluster_id = ? and external_id=?', [$clusterId, $externalId] );
        $slices =  json_decode($product->slices, true);
        $slices[]= (int)$request->input('sliceId');
        $slices = array_unique($slices);
        $product->slices =$slices;
        R::store( $product );
        return $product;
    }

    public function delete($clusterId, $externalId, RequestInterface $request, ResponseInterface $response) {

        $product = R::findOne( 'products', 'cluster_id = ? and external_id=?', [$clusterId, $externalId] );
        if (!$product['id'])
            return 'Not found';

        R::trash( $product );
        return 'Ok';
    }

}

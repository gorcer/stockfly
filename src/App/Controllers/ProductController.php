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

    public function create($clusterId, RequestInterface $request, ResponseInterface $response) {

        $product = R::dispense('products');
        $product->title = $request->input('title', '');
        $product->cluster_id = $clusterId;
        $product->external_id = $request->input('external_id');
        $product->total_qty = $request->input('total_qty');
        $product->max_qty = $request->input('max_qty');
        $product->avails = $request->input('avails');
        $product->price = $request->input('price');
        $product->slices = $request->input('slices');
        $product->data = $request->input('data');

        R::store( $product );
        return $product;
    }

    public function update($clusterId, $externalId, RequestInterface $request, ResponseInterface $response) {

        $product = R::findOne( 'products', 'cluster_id = ? and external_id=?', [$clusterId, $externalId] );
        $product->title = $request->input('title', $product->title);
        $product->cluster_id = $request->input('cluster_id', $product->cluster_id);
        $product->external_id = $request->input('external_id', $product->external_id);
        $product->total_qty = $request->input('total_qty', $product->total_qty);
        $product->max_qty = $request->input('max_qty', $product->max_qty);
        $product->avails = $request->input('avails', $product->avails);
        $product->price = $request->input('price', $product->price);
        $product->slices = $request->input('slices', $product->slices);
        $product->data = $request->input('data', $product->data);
        R::store( $product );
        return $product;
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
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

class SliceController {

    public function index($clusterId) {
        $result = R::find( 'slices',  'cluster_id = ?', [$clusterId]);
        return $result;
    }

    public function create($clusterId, RequestInterface $request, ResponseInterface $response) {

        $slice = R::dispense('slices');
        $slice->title = $request->input('title', '');
        $slice->external_id = $request->input('external_id', '');
        $slice->cluster_id = $clusterId;
        R::store( $slice );
        return $slice;
    }

    public function update($id, RequestInterface $request, ResponseInterface $response) {

        $slice = R::load( 'slices', $id );
        $slice->title = $request->input('title', $slice->title);
        $slice->external_id = $request->input('external_id', $slice->external_id);
        R::store( $slice );
        return $slice;
    }

    public function delete($id, RequestInterface $request, ResponseInterface $response) {

        $slice = R::load( 'slices', $id );
        if (!$slice['id'])
            return 'Not found';

        R::trash( $slice );
        return 'Ok';
    }

} 
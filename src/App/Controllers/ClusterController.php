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

class ClusterController {

    public function index() {

        $result = R::findAll( 'clusters' );
        return $result;
    }

    public function create(RequestInterface $request) {

        $cluster = R::dispense('clusters');
        $cluster->title = $request->input('title', '');
        $id = R::store( $cluster );
        return $cluster;
    }

    public function update($id, RequestInterface $request) {

        $cluster = R::load( 'clusters', $id );
        $cluster->title = $request->input('title', '');
        R::store( $cluster );
        return $cluster;
    }

    public function get($title, RequestInterface $request) {
        $cluster = R::findOne( 'clusters', 'title=?', [$title] );

        if ($cluster == null) {
            $cluster = R::dispense('clusters');
            $cluster->title = $title;
            R::store( $cluster );
        }

        return $cluster->id;
    }

    public function delete($id, RequestInterface $request, ResponseInterface $response) {

        $cluster = R::load( 'clusters', $id );
        if (!$cluster['id'])
            return 'Not found';

        R::trash( $cluster );
        return 'Ok';
    }

} 
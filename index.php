<?php

use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Nano\Factory\AppFactory;
use RedBeanPHP\R;

require_once __DIR__ . '/vendor/autoload.php';


define( 'REDBEAN_MODEL_PREFIX', '\\App\\Models\\' );
R::setup( 'pgsql:host=localhost;dbname=cargofly',
    'admin', 'admin' );
R::useJSONFeatures(true);

$app = AppFactory::create('0.0.0.0', 9052);

$app->get('/', function () {

    $user = $this->request->input('user', 'nano');
    $method = $this->request->getMethod();


    return $user;

});

$app->get('/clusters', 'App\Controllers\ClusterController::index');
$app->post('/clusters', 'App\Controllers\ClusterController::create');
$app->patch('/cluster/{id}', 'App\Controllers\ClusterController::update');
$app->delete('/cluster/{id}', 'App\Controllers\ClusterController::delete');
$app->get('/cluster/{title}', 'App\Controllers\ClusterController::get');


$app->get('/cluster/{clusterId}/slices', 'App\Controllers\SliceController::index');
$app->post('/cluster/{clusterId}/slices', 'App\Controllers\SliceController::create');
$app->patch('/slice/{id}', 'App\Controllers\SliceController::update');
$app->delete('/slice/{id}', 'App\Controllers\SliceController::delete');


$app->get('/cluster/{clusterId}/products/searchBySlices', 'App\Controllers\ProductController::searchBySlices');
$app->post('/cluster/{clusterId}/products', 'App\Controllers\ProductController::create');
$app->patch('/cluster/{clusterId}/products', 'App\Controllers\ProductController::set');
$app->delete('/cluster/{clusterId}/products/{externalId}', 'App\Controllers\ProductController::delete');
$app->post('/cluster/{clusterId}/products/{externalId}/assignSlice', 'App\Controllers\ProductController::assignSlice');

$app->addExceptionHandler(function ($throwable, $response) {
    print_r($throwable);
    return $response->withStatus('418')
        ->withBody(new SwooleStream($throwable));
});

$app->run();
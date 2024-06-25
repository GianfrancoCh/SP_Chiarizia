<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;


require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';
require_once './utils/AutentificadorJWT.php';

require_once './controllers/ProductoController.php';
require_once './controllers/VentaController.php';

require_once './middlewares/ProductoMiddleware.php';
require_once './middlewares/VentaMiddleware.php';


// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes
$app->group('/tienda', function (RouteCollectorProxy $group) {
  $group->get('[/]', \ProductoController::class . ':TraerTodos');
  $group->get('/{producto}', \ProductoController::class . ':TraerUno');
  $group->post('/consultar', \ProductoController::class . ':ConsultarProducto');
  $group->post('/alta', \ProductoController::class . ':CargarUno')->add(new ProductoMiddleware());
});

$app->group('/ventas', function (RouteCollectorProxy $group) {
    $group->get('[/]', \VentaController::class . ':TraerTodos');
    $group->post('/alta', \VentaController::class . ':CargarUno')->add(new VentaAltaMiddleware());
    $group->put('/modificar', \VentaController::class . ':Modificar')->add(new VentaModificarMiddleware());
});

$app->group('/ventas/consultar', function (RouteCollectorProxy $group) {
  $group->get('/productos/vendidos', \VentaController::class . ':ProductosVendidosFecha');
  $group->get('/ventas/porUsuario', \VentaController::class . ':VentasUsuario');
  $group->get('/ventas/porProducto', \VentaController::class . ':VentasTipoProducto');
  $group->get('/productos/entreValores', \ProductoController::class . ':ProductosEntreValores');
  $group->get('/ventas/ingresos', \VentaController::class . ':VentasIngresosFecha');
  $group->get('/productos/masVendido', \ProductoController::class . ':ProductosMasVendido');
});


$app->get('[/]', function (Request $request, Response $response) {    
    $payload = json_encode(array("mensaje" => "Bienvenido!"));
    
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();

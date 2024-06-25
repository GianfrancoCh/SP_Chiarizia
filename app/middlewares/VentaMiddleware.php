<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class VentaAltaMiddleware
{	public function __invoke(Request $request, RequestHandler $handler): Response
	{
		$response = new Response();
		$parametros = $request->getParsedBody();

		$camposNecesarios = ['usuario', 'marca', 'tipo', 'modelo', 'stock'];
		foreach ($camposNecesarios as $campo) {
            if (!isset($parametros[$campo])) {
                $response = new Response();
                $response->getBody()->write(json_encode(['mensaje' => "El campo $campo es requerido"]));
                return $response->withHeader('Content-Type', 'application/json');
            }
        }

		$producto = Producto::buscarMarcaTipoModelo($parametros['marca'], $parametros['tipo'],$parametros['modelo']);

        if ($producto) {

            if($producto->stock >= $parametros['stock']){
                $response = $handler->handle($request);
            }else{
                $response->getBody()->write(json_encode(array("mensaje" => "No hay stock suficiente de ese producto")));

            }

        } else {

            $response->getBody()->write(json_encode(['mensaje' => "No existe producto con esas especificaciones"]));
        }


		return $response->withHeader('Content-Type', 'application/json');
	}

}


class VentaModificarMiddleware
{	public function __invoke(Request $request, RequestHandler $handler): Response
	{
		$response = new Response();
		$parametros = $request->getParsedBody();

		$camposNecesarios = ['id','usuario','marca', 'tipo', 'modelo', 'stock'];
		foreach ($camposNecesarios as $campo) {
            if (!isset($parametros[$campo])) {
                $response = new Response();
                $response->getBody()->write(json_encode(['mensaje' => "El campo $campo es requerido"]));
                return $response->withHeader('Content-Type', 'application/json');
            }
        }

		$venta = Venta::obtenerVenta($parametros['id']);

        if ($venta) {
                     
            $response = $handler->handle($request);    
            
        } else {

            $response->getBody()->write(json_encode(['mensaje' => "No existe venta con ese ID"]));
        }


		return $response->withHeader('Content-Type', 'application/json');
	}

}

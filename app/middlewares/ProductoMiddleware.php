<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class ProductoMiddleware
{	public function __invoke(Request $request, RequestHandler $handler): Response
	{
		$response = new Response();
		$parametros = $request->getParsedBody();

		$camposNecesarios = ['marca', 'tipo', 'precio', 'modelo', 'color', 'stock'];
		foreach ($camposNecesarios as $campo) {
            if (!isset($parametros[$campo])) {
                $response = new Response();
                $response->getBody()->write(json_encode(['mensaje' => "El campo $campo es requerido"]));
                return $response->withHeader('Content-Type', 'application/json');
            }
        }

		$producto = Producto::buscarMarcaTipo($parametros['marca'], $parametros['tipo']);

        if ($producto) {
            
            $nuevoStock = $producto->stock + $parametros['stock'];
            Producto::actualizarProductoStockPrecio($producto->id, $parametros['precio'], $nuevoStock);
			$response->getBody()->write(json_encode(array("mensaje" => "Producto actualizado")));

        } else {
            $response = $handler->handle($request);
        }


		return $response->withHeader('Content-Type', 'application/json');
	}

}

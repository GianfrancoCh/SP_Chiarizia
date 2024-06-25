<?php
require_once './models/Producto.php';
require_once './interfaces/IApiUsable.php';
include_once(__DIR__ . '/../utils/Archivos.php');

class ProductoController extends Producto
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $marca = $parametros['marca'];
        $precio = $parametros['precio'];
        $tipo = $parametros['tipo'];
        $modelo = $parametros['modelo'];
        $color = $parametros['color'];
        $stock = $parametros['stock'];
        Archivo::GuardarArchivo("db/ImagenesDeProductos/2024/", "{$parametros['marca']}+{$parametros['tipo']}", 'foto', '.jpg');

        $producto = new Producto();
        $producto->marca = $marca;
        $producto->precio = $precio;
        $producto->tipo = $tipo;
        $producto->modelo = $modelo;
        $producto->color = $color;
        $producto->stock = $stock;

        $id = $producto->crearProducto();

        $payload = json_encode(array("mensaje" => "Producto creado con exito con ID: " . $id));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function consultarProducto($request,$response,$args)
    {
        $parametros = $request->getParsedBody();
        $marca = $parametros['marca'];
        $tipo = $parametros['tipo'];
        $color = $parametros['color'];

        // Verificar existencia exacta
        $producto = Producto::buscarMarcaTipoColor($marca, $tipo, $color);
        if ($producto) {
            $response->getBody()->write(json_encode(['mensaje' => 'Existe']));
            return $response->withHeader('Content-Type', 'application/json');
        }

        $productoMarca = Producto::buscarMarca($marca);
        $productoTipo = Producto::buscarTipo($tipo);

        if (!$productoMarca && !$productoTipo) {
          $response->getBody()->write(json_encode(['mensaje' => "No hay productos de la marca $marca ni del tipo $tipo"]));
        } else if (!$productoMarca) {
            $response->getBody()->write(json_encode(['mensaje' => "No hay productos de la marca $marca"]));
        } else if (!$productoTipo) {
            $response->getBody()->write(json_encode(['mensaje' => "No hay productos del tipo $tipo"]));
        } else {
            $response->getBody()->write(json_encode(['mensaje' => "Existen productos de la marca $marca y/o del tipo $tipo, pero no con el color especificado"]));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ProductosEntreValores($request, $response)
    {
        $parametros = $request->getQueryParams();

        if(isset($parametros['valor1']) && isset($parametros['valor2'])){
            $productos = Producto::consultarProductoEntreValores($parametros['valor1'],$parametros['valor2']);
            $response->getBody()->write(json_encode($productos));
        }else{
            $mensaje = "Faltan campos";
            $payload = json_encode(array("mensaje" => $mensaje));
            $response->getBody()->write($payload);
        }

       
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ProductosMasVendido($request, $response)
    {
       
        $producto = Producto::obtenerProductoMasVendido();
        $response->getBody()->write(json_encode($producto));
        return $response->withHeader('Content-Type', 'application/json');
        
    }
    

}

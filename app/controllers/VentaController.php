<?php
require_once './models/Venta.php';
require_once './interfaces/IApiUsable.php';


class VentaController extends Venta
{
  

    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $usuario = $parametros['usuario'];
        $marca = $parametros['marca'];
        $tipo = $parametros['tipo'];
        $modelo = $parametros['modelo'];
        $stock = $parametros['stock'];
        $fecha = date("Y-m-d");

        $usuarioNombre = explode("@", $usuario);

        $producto = Producto::buscarMarcaTipoModelo($parametros['marca'], $parametros['tipo'],$parametros['modelo']);

        

        $nuevoStock = $producto->stock - $stock;
        Producto::actualizarProductoStock($producto->id, $nuevoStock);

        Archivo::GuardarArchivo("db/ImagenesDeVenta/2024/", "{$parametros['marca']}+{$parametros['tipo']}+{$parametros['modelo']}+{$usuarioNombre[0]}", 'foto', '.jpg');

        $venta = new Venta();
        $venta->usuario = $usuario;
        $venta->id_producto = $producto->id;
        $venta->marca = $marca;
        $venta->tipo = $tipo;
        $venta->modelo = $modelo;
        $venta->stock = $stock;
        $venta->fecha = $fecha;

        $id = $venta->crearVenta();
        $mensaje = "Venta creada con éxito con ID: " . $id;
        $payload = json_encode(array("mensaje" => $mensaje));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
              
    }

    public function ProductosVendidosFecha($request, $response, $args)
    {
        $parametros = $request->getQueryParams();

        if(isset($parametros['fecha'])){
            $fecha = $parametros['fecha'];
            $productos = Venta::consultarProductosVentaFecha($fecha);
        }else{
            $fecha = date('Y-m-d', strtotime('-1 day'));
            $productos = Venta::consultarProductosVentaFecha($fecha);
        }

        $response->getBody()->write(json_encode($productos));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function VentasUsuario($request, $response)
    {
        $parametros = $request->getQueryParams();

        if(isset($parametros['usuario'])){
            $ventas = Venta::consultarVentasUsuario($parametros['usuario']);
            $response->getBody()->write(json_encode($ventas));
        }else{
            $mensaje = "Falta campo usuario";
            $payload = json_encode(array("mensaje" => $mensaje));
            $response->getBody()->write($payload);
        }

       
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function VentasTipoProducto($request, $response)
    {
        $parametros = $request->getQueryParams();

        if(isset($parametros['tipo'])){
            $ventas = Venta::consultarVentasTipoProducto($parametros['tipo']);
            $response->getBody()->write(json_encode($ventas));
        }else{
            $mensaje = "Falta campo tipo";
            $payload = json_encode(array("mensaje" => $mensaje));
            $response->getBody()->write($payload);
        }

       
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    public function VentasIngresosFecha($request, $response)
    {
        $parametros = $request->getQueryParams();

        if(isset($parametros['fecha'])){
            $fecha = $parametros['fecha'];
            $ingresos = Venta::consultarIngresosFecha($fecha);
        }else{
            
            $ingresos = Venta::consultarIngresosTodasFechas();
        }

        $response->getBody()->write(json_encode($ingresos));
        return $response->withHeader('Content-Type', 'application/json');
    }
 

    public function Modificar($request, $response)
    {
        $parametros = $request->getParsedBody();
        $id = $parametros['id'];
        $usuario = $parametros['usuario'];
        $marca = $parametros['marca'];
        $tipo = $parametros['tipo'];
        $modelo = $parametros['modelo'];
        $stock = $parametros['stock'];

        Venta::modificarVenta($id, $usuario, $marca,$tipo,$modelo,$stock);
        
        $payload = json_encode(array("mensaje" => "Venta con ID $id modificada"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }


}

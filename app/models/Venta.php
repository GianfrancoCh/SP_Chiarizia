<?php

include_once(__DIR__ . '/../db/AccesoDatos.php');
class Venta
{
    
	public $id;
    public $usuario;

    public $id_producto;
    public $marca;
    public $tipo;
    public $modelo;
    public $stock;
    public $fecha;

    public function crearVenta()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO ventas (usuario,id_producto, marca, tipo, modelo,stock,fecha) VALUES (:usuario,:id_producto, :marca, :tipo,:modelo, :stock,:fecha)");
    
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':id_producto', $this->id_producto, PDO::PARAM_INT);
        $consulta->bindValue(':marca', $this->marca, PDO::PARAM_STR);
        $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
        $consulta->bindValue(':modelo', $this->modelo, PDO::PARAM_STR);
        $consulta->bindValue(':stock', $this->stock, PDO::PARAM_INT);
        $consulta->bindValue(':fecha', $this->fecha, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM ventas");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'venta');
    }

    public static function obtenerTodosId()
	{
		$objAccesoDatos = AccesoDatos::ObtenerInstancia();
		$consulta = $objAccesoDatos->PrepararConsulta("SELECT id FROM ventas");
		$consulta->execute();
		return $consulta->fetchAll(PDO::FETCH_COLUMN);
	}
    public static function obtenerVenta($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM ventas WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('venta');
    }

    public static function consultarProductosVentaFecha($fecha)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("SELECT ventas.usuario, ventas.marca, ventas.tipo, ventas.modelo, ventas.stock, ventas.fecha 
        FROM ventas
        JOIN productos ON productos.id = ventas.id_producto
        WHERE ventas.fecha = :fecha");
        $consulta->bindValue(':fecha', $fecha, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_OBJ);
    }

    public static function consultarVentasUsuario($usuario)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("SELECT * FROM ventas WHERE usuario = :usuario");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_OBJ);
    }

    public static function consultarVentasTipoProducto($tipo)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("SELECT * FROM ventas WHERE tipo = :tipo");
        $consulta->bindValue(':tipo', $tipo, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_OBJ);
    }


    public static function consultarIngresosFecha($fecha)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();

        $consulta = $objAccesoDato->prepararConsulta("SELECT v.fecha, SUM(p.precio) AS ingresos
            FROM ventas v
            JOIN productos p ON p.id = v.id_producto
            WHERE v.fecha = :fecha
            GROUP BY v.fecha");
        $consulta->bindValue(':fecha', $fecha, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_OBJ);
    }

    public static function consultarIngresosTodasFechas()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("SELECT v.fecha, SUM(p.precio) AS ingresos
            FROM ventas v
            JOIN productos p ON p.id = v.id_producto
            GROUP BY v.fecha
            ORDER BY v.fecha DESC"); 
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_OBJ);

    }

    public static function modificarVenta($id, $usuario, $marca,$tipo,$modelo,$stock)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE ventas SET usuario = :usuario, marca = :marca, tipo = :tipo, modelo= :modelo, stock=:stock WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->bindValue(':marca', $marca, PDO::PARAM_STR);
        $consulta->bindValue(':tipo', $tipo, PDO::PARAM_STR);
        $consulta->bindValue(':modelo', $modelo, PDO::PARAM_STR);
        $consulta->bindValue(':stock', $stock, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetchObject('venta');
    }  

    

}
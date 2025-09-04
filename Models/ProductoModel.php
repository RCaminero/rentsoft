<?php
class ProductoModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }

    /* === LISTADOS ===================================================== */

    public function getCategorias()
    {
        return $this->selectAll("SELECT * FROM categorias_productos WHERE estado = 1");
    }

    public function getProductos()
    {
        $sql = "SELECT p.*, c.categoria
                  FROM productos p
             LEFT JOIN categorias_productos c ON c.id = p.id_categoria
                 WHERE p.estado = 1
              ORDER BY p.id DESC";
        return $this->selectAll($sql);
    }

    public function getProducto($id)
    {
        return $this->select("SELECT * FROM productos WHERE id = ?", [$id]);
    }

    /* === REGISTRO / EDICIÓN ========================================== */

  public function registrarProducto($cod,$nom,$des,$pc,$pv,$stock,$cat)
    {
        // 1. Evitar duplicado de código
        if ($this->select("SELECT id FROM productos WHERE codigo = ?", [$cod])) {
            return 'existe';
        }

        // 2. Insert
        $sql   = "INSERT INTO productos
                    (codigo, nombre, descripcion, precio_compra,
                    precio_venta, stock_actual, id_categoria)
                VALUES (?,?,?,?,?,?,?)";
        $datos = [$cod,$nom,$des,$pc,$pv,$stock,$cat];

        // 3. Ejecuta y devuelve resultado
        $ok = $this->save($sql, $datos);     // true / false
        return $ok ? 'ok' : 'error';         // ternario correcto
    }

    public function modificarProducto($cod,$nom,$des,$pc,$pv,$stock,$cat,$id)
    {
        $sql = "UPDATE productos SET
                   codigo         = ?, nombre = ?, descripcion    = ?,
                   precio_compra  = ?, precio_venta = ?, stock_actual = ?,
                   id_categoria   = ?
                WHERE id = ?";
        return $this->save($sql,
              [$cod,$nom,$des,$pc,$pv,$stock,$cat,$id]);
    }

    /* === BAJA LÓGICA ================================================== */

    public function setInactivo($id)
    {
        return $this->save("UPDATE productos SET estado = 0 WHERE id = ?", [$id]);
    }
}

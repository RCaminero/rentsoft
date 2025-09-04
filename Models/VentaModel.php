<?php
class VentaModel extends Query
{
    public function __construct(){ parent::__construct(); }

    /* ---------- CLIENTES / PRODUCTOS ------------------------------ */

   /* ===========================================================
   Productos disponibles para autocompletar en ventas
   =========================================================== */
public function buscarProducto($valor)
{
    $sql = "SELECT id, nombre, precio_venta, stock_actual
              FROM productos 
             WHERE stock_actual > 0
             AND nombre LIKE ? 
             LIMIT 10";
    return $this->selectAll($sql, ['%'.$valor.'%']);
}


    /* ---------- VENTAS (cabecera + detalle) ----------------------- */

    // listado para DataTable
    public function getVentas()
    {
        $sql = "SELECT v.id, v.fecha, v.total_neto, v.metodo_pago,
                       c.nombre AS cliente,
                       v.id_caja   AS caja
                FROM   ventas  v
                LEFT  JOIN clientes c ON c.id = v.id_cliente
                WHERE  v.estado = 1
                ORDER  BY v.id DESC";
        return $this->selectAll($sql);
    }

    public function getVenta($id)
    {
        $sql = "SELECT v.*, c.nombre AS cliente
                FROM ventas v
                LEFT JOIN clientes c ON c.id = v.id_cliente
                WHERE v.id = ?";
        return $this->select($sql, [$id]);
    }

    public function getDetalleVenta($id)
    {
        $sql="SELECT dv.*, p.nombre
              FROM detalle_ventas dv
              INNER JOIN productos p ON p.id = dv.id_producto
              WHERE dv.id_venta = ?";
        return $this->selectAll($sql,[$id]);
    }

    /* ---------- registrar venta completa -------------------------- */
    public function registrarVenta($cab,$detalle)
    {
        /* 1)‑ insert cabecera */
        $sqlCab = "INSERT INTO ventas
                   (fecha,total_bruto,descuento,impuesto,total_neto,
                    metodo_pago,id_cliente,id_usuario,id_caja)
                   VALUES (?,?,?,?,?,?,?,?,?)";
        if(!$this->save($sqlCab,$cab)){ return ['ok'=>false,'msg'=>'Error cabecera']; }

        $idVenta = $this->lastId();   // helper de Query → lastInsertId()

        /* 2)‑ recorrer detalle */
        $sqlDet   = "INSERT INTO detalle_ventas
                     (cantidad,precio_unit,subtotal,id_producto,id_venta)
                     VALUES (?,?,?,?,?)";
        $sqlStock = "UPDATE productos SET stock_actual = stock_actual - ? WHERE id = ?";

        foreach($detalle as $it){
            // comprobar stock actual
            $disp = $this->select("SELECT stock_actual FROM productos WHERE id = ?",[$it['id_producto']])['stock_actual'];
            if($disp < $it['cantidad']){
                return ['ok'=>false,'msg'=>'Stock insuficiente de '.$it['nombre']];
            }
            // insertar línea
            $ok1 = $this->save($sqlDet,[
                      $it['cantidad'],
                      $it['precio_unit'],
                      $it['subtotal'],
                      $it['id_producto'],
                      $idVenta
                  ]);
            $ok2 = $this->save($sqlStock,[$it['cantidad'],$it['id_producto']]);
            if(!$ok1 || !$ok2){
                return ['ok'=>false,'msg'=>'Error detalle/stock'];
            }
        }
        return ['ok'=>true,'id'=>$idVenta];
    }

    /* ---------- anulación / baja lógica --------------------------- */
    public function setInactivo($id)
    { return $this->save("UPDATE ventas SET estado = 0 WHERE id = ?",[$id]); }

        public function getEmpresa()
    {
        $sql = "SELECT * FROM configuracion";
        $data = $this->select($sql);
        return $data;
    }
}

<?php
class AlquilerModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }
    public function getAlquiler()
    {
        $sql = "SELECT a.*, c.nombre, v.placa, v.modelo, d.documento, t.tipo FROM alquiler a INNER JOIN clientes c ON c.id = a.id_cliente INNER JOIN vehiculos v ON v.id = a.id_vehiculo INNER JOIN documentos d ON d.id = a.id_doc INNER JOIN tipos t ON t.id = v.id_tipo";
        $data = $this->selectAll($sql);
        return $data;
    }
    public function getDoc()
    {
        $sql = "SELECT * FROM documentos WHERE estado = 1";
        $existe = $this->selectAll($sql);
        return $existe;
    }
    public function getVehiculos()
    {
        $sql = 
        "SELECT v.id, v.placa, v.id_tipo, v.id_marca, v.estado, t.id, t.tipo, m.id, m.marca 
        FROM vehiculos v 
        INNER JOIN tipos t ON t.id = v.id_tipo 
        INNER JOIN marcas m ON m.id = v.id_marca 
        WHERE v.estado = 1";
        $existe = $this->selectAll($sql);
        return $existe;
    }
    public function getVehiculo($id)
    {
        $sql = "SELECT * FROM vehiculos WHERE id = $id";
        $existe = $this->select($sql);
        return $existe;
    }
    public function getEmpresa()
    {
        $sql = "SELECT * FROM configuracion";
        $data = $this->select($sql);
        return $data;
    }

    public function verify($desde, $hasta, $id_veh) {
        $sql = "SELECT * FROM reservas
        WHERE id_vehiculo = $id_veh
        AND f_recogida <= '$desde'
        AND f_entrega >= '$hasta'";

        return $this->select($sql);
    }

    /*  Registrar un alquiler y ligarlo a la caja activa  */
public function registrarAlquiler(
    $cantidad,
    $precios,
    $monto,
    $abono,
    $fecha,
    $fecha_devolucion,
    $observacion,
    $id_cli,
    $id_veh,
    $documento,
    $id_caja          //  ← NUEVO
){
    /*  1. Verificar que exista una caja activa           */
    if (!$id_caja) {
        return 'sin_caja';            // El controlador decide qué hacer
    }

    /*  2. Evitar duplicados para la misma caja           */
    $verificar = "SELECT 1
                    FROM alquiler
                   WHERE id_cliente = ?
                     AND id_vehiculo = ?
                     AND id_doc     = ?
                     AND id_caja    = ?
                     AND estado     = 1
                   LIMIT 1";
    $existe = $this->select($verificar, [$id_cli, $id_veh, $documento, $id_caja]);

    if (!empty($existe)) {
        return 'existe';
    }

    /*  3. Insertar con id_caja                           */
    $sql = "INSERT INTO alquiler
              (cantidad, tipo_precio, monto, abono,
               fecha_prestamo, fecha_devolucion, observacion,
               id_cliente, id_vehiculo, id_doc, id_caja)
            VALUES (?,?,?,?,?,?,?,?,?,?,?)";

    $datos = [
        $cantidad, $precios, $monto, $abono,
        $fecha, $fecha_devolucion, $observacion,
        $id_cli, $id_veh, $documento, $id_caja
    ];

    $data = $this->insertar($sql, $datos);

    return ($data > 0) ? $data : 'error';
}

    public function actualizarVehiculo(int $estado, int $id)
    {
        $sql = "UPDATE vehiculos SET estado = ? WHERE id = ?";
        $datos = array($estado, $id);
        $data = $this->save($sql, $datos);
        if ($data == 1) {
            $res = 'ok';
        }else{
            $res = 'error';
        }
        return $res;
    }
    public function procesarEntrega(int $estado, int $id)
    {
        $sql = "UPDATE alquiler SET estado = ? WHERE id = ?";
        $datos = array($estado, $id);
        $data = $this->save($sql, $datos);
        if ($data == 1) {
            $res = 'ok';
        } else {
            $res = 'error';
        }
        return $res;
    }
    public function verPrestamo(int $id)
    {
        $sql = "SELECT a.*, c.dni, c.nombre, c.telefono, c.direccion, v.placa, v.modelo, d.documento, t.tipo FROM alquiler a INNER JOIN clientes c ON c.id = a.id_cliente INNER JOIN vehiculos v ON v.id = a.id_vehiculo INNER JOIN documentos d ON d.id = a.id_doc INNER JOIN tipos t ON t.id = v.id_tipo WHERE a.id = $id";
        $existe = $this->select($sql);
        return $existe;
    }

    /* ==========================================================
 *  Comprueba solapamiento de fechas para un vehículo
 *  Devuelve true si existe cruce; false si está libre
 * ========================================================== */
public function haySolape(string $fIni, string $fFin, int $idVeh): bool
{
    /* --- 1. Alquileres activos -------------------------------- */
    $sqlAlq = "SELECT 1
                 FROM alquiler
                WHERE id_vehiculo = ?
                  AND estado       = 1          -- todavía alquilado
                  AND (
                        (? BETWEEN fecha_prestamo AND fecha_devolucion)
                     OR (? BETWEEN fecha_prestamo AND fecha_devolucion)
                     OR (fecha_prestamo BETWEEN ? AND ?)
                     )
                LIMIT 1";

    /* --- 2. Reservas activas ---------------------------------- */
    $sqlRes = "SELECT 1
                 FROM reservas
                WHERE id_vehiculo = ?
                  AND estado       = 1          -- reserva confirmada/activa
                  AND (
                        (? BETWEEN f_recogida AND f_entrega)
                     OR (? BETWEEN f_recogida AND f_entrega)
                     OR (f_recogida BETWEEN ? AND ?)
                     )
                LIMIT 1";

    /* parámetros */
    $p = [$idVeh,$fIni,$fFin,$fIni,$fFin];

    /* Si alguna consulta devuelve fila => hay solape */
    return $this->select($sqlAlq,$p) || $this->select($sqlRes,$p);
}

}

<?php
class CajaModel extends Query
{
    public function __construct(){ parent::__construct(); }

    public function idCajaActiva($user){
        $c=$this->select("SELECT id FROM caja WHERE id_usuario=? AND estado=1 LIMIT 1",[$user]);
        return $c?$c['id']:0;
    }

    public function abrir($monto,$usr){
        return $this->insertar(
            "INSERT INTO caja (fecha_apertura,hora_apertura,efectivo_inicio,id_usuario)
             VALUES (CURDATE(),CURTIME(),?,?)",[$monto,$usr]);
    }

    public function datos($id){
        return $this->select("SELECT * FROM caja WHERE id=?",[$id]);
    }

    public function cerrar($idCaja,$efFinal,$obs){
        /* totales del día */
        $ventas = $this->select(
          "SELECT IFNULL(SUM(total_neto),0) AS t FROM ventas WHERE id_caja=? AND estado=1",[$idCaja])['t'];

        $gastos = $this->select(
          "SELECT IFNULL(SUM(monto),0) AS t FROM gastos WHERE id_caja=? AND estado=1",[$idCaja])['t'];

        $alq = $this->select(
        "SELECT IFNULL(SUM(cantidad*monto),0) AS t
            FROM alquiler
            WHERE id_caja = ? AND estado = 0",    // ← id_caja
        [$idCaja])['t'];

        $sql="UPDATE caja SET fecha_cierre=CURDATE(),hora_cierre=CURTIME(),
             efectivo_cierre=?,total_ventas=?,total_gastos=?,total_alquiler=?,observacion=?,estado=0
             WHERE id=?";
        return $this->save($sql,[$efFinal,$ventas,$gastos,$alq,$obs,$idCaja]);
    }

    public function listado(){ return $this->selectAll("SELECT * FROM caja WHERE estado=0 ORDER BY id DESC"); }

    /* -----------------------------------------------------------------
   Cambia el estado de una caja cerrada
   estado = 2  → Cancelada
   ----------------------------------------------------------------- */
    public function cancelar($id)
    {
        /*  Solo puede cancelarse si ya está cerrada (estado = 0) */
        $caja = $this->select("SELECT estado FROM caja WHERE id=?",[$id]);
        if(!$caja || $caja['estado']!=0){ return 0; }

        /*  Actualiza estado y deja una marca en observación            */
        return $this->save(
            "UPDATE caja SET estado = 2,
                            observacion = CONCAT(IFNULL(observacion,''),'  [CANCELADA]')
            WHERE id = ?", [$id]);
    }

    public function getCaja($id)
    {
        $sql = "SELECT * FROM caja WHERE id = ?";
        $data = $this->select($sql, [$id]);
        return $data;
    }

    public function getEmpresa()
    {
        $sql = "SELECT * FROM configuracion";
        $data = $this->select($sql);
        return $data;
    }

        /*  Totales acumulados mientras la caja está abierta  */
    public function totalesActuales($idCaja)
    {
        $ventas = $this->select(
            "SELECT IFNULL(SUM(total_neto),0) AS t 
            FROM ventas 
            WHERE id_caja = ? AND estado = 1",
            [$idCaja]
        )['t'];

        $gastos = $this->select(
            "SELECT IFNULL(SUM(monto),0) AS t 
            FROM gastos 
            WHERE id_caja = ?",
            [$idCaja]
        )['t'];

        $alq = $this->select(
            "SELECT IFNULL(SUM(cantidad*monto),0) AS t 
            FROM alquiler 
            WHERE id_caja = ? AND estado = 0",
            [$idCaja]
        )['t'];

        return [
            'total_ventas'   => $ventas,
            'total_gastos'   => $gastos,
            'total_alquiler' => $alq
        ];
    }



}

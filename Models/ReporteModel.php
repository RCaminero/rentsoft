<?php
/**
 *  ReporteModel
 *  – consultas genéricas de informes (por mes, por caja, etc.)
 */
class ReporteModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }

    /* ==========================================================
     *  Datos fijos de la empresa  (tabla configuracion)
     * ========================================================*/
    public function getEmpresa()
    {
        return $this->select("SELECT * FROM configuracion");
    }

    /* ==========================================================
     *  Reporte mensual  →  array con 3 secciones:
     *      alquileres , ventas , gastos
     *  $mes  : 1‑12   |  $anio : YYYY
     * ========================================================*/
    public function getReporteMensual(int $mes, int $anio): array
    {
        /* ---------- Alquileres ---------------------------------- */
        $alqSql = "SELECT a.id,
                          c.nombre                         AS cliente,
                          CONCAT(t.tipo,' / ',v.placa)     AS vehiculo,
                          a.cantidad,
                          a.monto,
                          a.abono,
                          (a.cantidad*a.monto)            AS total,
                          DATE(a.fecha_prestamo) AS fecha
                     FROM alquiler      a
               INNER JOIN clientes      c ON c.id = a.id_cliente
               INNER JOIN vehiculos     v ON v.id = a.id_vehiculo
               INNER JOIN tipos         t ON t.id = v.id_tipo
                    WHERE MONTH(a.fecha_prestamo) = ? 
                      AND YEAR(a.fecha_prestamo)  = ?";
        $alquileres = $this->selectAll($alqSql, [$mes, $anio]);

        /* ---------- Ventas  (cabecera) --------------------------- */
        $venSql = "SELECT v.id,
                          v.fecha,
                          IFNULL(c.nombre,'—')            AS cliente,
                          v.total_neto
                     FROM ventas v
                LEFT JOIN clientes c ON c.id = v.id_cliente
                    WHERE v.estado = 1
                      AND MONTH(v.fecha) = ? 
                      AND YEAR(v.fecha)  = ?";
        $ventas = $this->selectAll($venSql, [$mes, $anio]);

        /* ---------- Gastos -------------------------------------- */
        $gasSql = "SELECT id,
                          descripcion,
                          monto,
                          fecha
                     FROM gastos
                    WHERE MONTH(fecha) = ? 
                      AND YEAR(fecha)  = ?";
        $gastos = $this->selectAll($gasSql, [$mes, $anio]);

        return [
            'alquileres' => $alquileres,
            'ventas'     => $ventas,
            'gastos'     => $gastos
        ];
    }
    public function getReporteSemanal($f1, $f2): array
{
    $alquileres = $this->selectAll("
        SELECT a.id, DATE(a.fecha_prestamo) AS fecha,
               c.nombre AS cliente,
               CONCAT(t.tipo,' / ',v.placa) AS vehiculo,
               a.cantidad, a.monto, a.abono,
               (a.cantidad * a.monto) AS total
          FROM alquiler a
    INNER JOIN clientes  c ON c.id = a.id_cliente
    INNER JOIN vehiculos v ON v.id = a.id_vehiculo
    INNER JOIN tipos     t ON t.id = v.id_tipo
         WHERE DATE(a.fecha_prestamo) BETWEEN ? AND ?", [$f1, $f2]);

    $ventas = $this->selectAll("
        SELECT v.id, v.fecha,
               IFNULL(c.nombre,'—') AS cliente,
               v.total_neto
          FROM ventas v
     LEFT JOIN clientes c ON c.id = v.id_cliente
         WHERE v.estado = 1
           AND DATE(v.fecha) BETWEEN ? AND ?", [$f1, $f2]);

    $gastos = $this->selectAll("
        SELECT id, descripcion, monto, fecha
          FROM gastos
         WHERE DATE(fecha) BETWEEN ? AND ?", [$f1, $f2]);

    return [
        'alquileres' => $alquileres,
        'ventas'     => $ventas,
        'gastos'     => $gastos
    ];
}

}

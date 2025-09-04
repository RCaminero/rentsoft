<?php
class DashboardModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getDatos(string $table)
    {
        $sql = "SELECT COUNT(*) AS total FROM $table WHERE estado = 1";
        $data = $this->select($sql);
        return $data;
    }

    public function rentas($desde, $hasta)
    {
        $sql = "SELECT SUM(IF(MONTH(fecha_prestamo) = 1, (cantidad * monto), 0)) AS ene,
        SUM(IF(MONTH(fecha_prestamo) = 2, (cantidad * monto), 0)) AS feb,
        SUM(IF(MONTH(fecha_prestamo) = 3, (cantidad * monto), 0)) AS mar,
        SUM(IF(MONTH(fecha_prestamo) = 4, (cantidad * monto), 0)) AS abr,
        SUM(IF(MONTH(fecha_prestamo) = 5, (cantidad * monto), 0)) AS may,
        SUM(IF(MONTH(fecha_prestamo) = 6, (cantidad * monto), 0)) AS jun,
        SUM(IF(MONTH(fecha_prestamo) = 7, (cantidad * monto), 0)) AS jul,
        SUM(IF(MONTH(fecha_prestamo) = 8, (cantidad * monto), 0)) AS ago,
        SUM(IF(MONTH(fecha_prestamo) = 9, (cantidad * monto), 0)) AS sep,
        SUM(IF(MONTH(fecha_prestamo) = 10, (cantidad * monto), 0)) AS oct,
        SUM(IF(MONTH(fecha_prestamo) = 11, (cantidad * monto), 0)) AS nov,
        SUM(IF(MONTH(fecha_prestamo) = 12, (cantidad * monto), 0)) AS dic 
        FROM alquiler WHERE fecha_prestamo BETWEEN '$desde' AND '$hasta'";
        return $this->select($sql);
    }

   /* ===========================================================
 *  Nº de alquileres por día de la semana (semana en curso)
 * =========================================================== */
public function rentasSemana()
{
    $sql = "SELECT
                DAYOFWEEK(fecha_prestamo)              AS nro_dia,        -- 1=Dom, 2=Lun …
                DATE_FORMAT(fecha_prestamo,'%W')       AS dia_semana,     -- Lunes, Martes…
                COUNT(*)                               AS total_rentas
            FROM  alquiler
            WHERE YEARWEEK(fecha_prestamo, 1) = YEARWEEK(CURDATE(), 1)   -- misma semana ISO 8601
            GROUP BY nro_dia, dia_semana
            ORDER BY nro_dia";
            return $this->selectAll($sql);
}
}
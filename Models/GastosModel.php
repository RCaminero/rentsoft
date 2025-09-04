<?php
class GastosModel extends Query
{
    public function __construct(){ parent::__construct(); }

    public function getGastos()
    {
        $sql = "SELECT g.*, u.nombre AS usuario, ca.id AS caja
                  FROM gastos g
             LEFT JOIN usuarios u ON u.id = g.id_usuario
             LEFT JOIN caja     ca ON ca.id = g.id_caja
             WHERE g.estado = 1
             ORDER BY g.id DESC";
        return $this->selectAll($sql);
    }

    public function registrar($d,$m,$f,$usr,$caja)
    {
        $sql = "INSERT INTO gastos
                  (descripcion,monto,fecha,id_usuario,id_caja)
                VALUES (?,?,?,?,?)";
        return $this->save($sql,[$d,$m,$f,$usr,$caja]);
    }

    public function actualizar($d,$m,$f,$caja,$id)
    {
        $sql="UPDATE gastos SET descripcion=?,monto=?,fecha=?,id_caja=? WHERE id=?";
        return $this->save($sql,[$d,$m,$f,$caja,$id]);
    }

    public function getGasto($id)
    { return $this->select("SELECT * FROM gastos WHERE id=?",[$id]); }

    public function eliminar($id)
    { return $this->save("DELETE FROM gastos WHERE id=?",[$id]); }

    public function desactivarGasto($id) {
    // Cambia el estado a 0 (inactivo) o usa un campo booleano como 'activo = false'
    return $this->save("UPDATE gastos SET estado = 0 WHERE id = ?", [$id]);
}
}

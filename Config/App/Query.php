<?php
/**
 * Clase: Query
 * Función: Encapsula las operaciones básicas con PDO usando consultas preparadas.
 */
class Query extends Conexion
{
    private $con;

    public function __construct()
    {
        parent::__construct();        // crea la conexión en la clase padre
        $this->con = $this->conect(); // devuelve el objeto PDO
    }

    /* =========================================================
       SELECT — una sola fila
    ========================================================= */
    public function select(string $sql, array $params = [])
    {
        $stmt = $this->con->prepare($sql);  // ✅ consulta preparada
        $stmt->execute($params);            // ✅ pasa los parámetros
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* =========================================================
       SELECT — varias filas
    ========================================================= */
    public function selectAll(string $sql, array $params = [])
    {
        $stmt = $this->con->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================================================
       INSERT que devuelve el ID
    ========================================================= */
    public function insertar(string $sql, array $params)
    {
        $stmt = $this->con->prepare($sql);
        if ($stmt->execute($params)) {
            return $this->con->lastInsertId();  // id del nuevo registro
        }
        return 0;                               // error
    }

    /* =========================================================
       UPDATE / DELETE / INSERT genérico
       Devuelve 1 si la operación fue exitosa, 0 en caso contrario
    ========================================================= */
    public function save(string $sql, array $params)
    {
        $stmt = $this->con->prepare($sql);
        return $stmt->execute($params) ? 1 : 0;
    }

    public function lastId(){ return $this->con->lastInsertId(); }

}

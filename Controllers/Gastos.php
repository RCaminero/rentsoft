<?php
require_once __DIR__.'/../models/GastosModel.php';
require_once __DIR__.'/../models/CajaModel.php';  // para caja abierta

class Gastos extends Controller
{
    public function __construct(){
        session_start();
        if(empty($_SESSION['id_usuario'])||$_SESSION['tipo']!=1){
            header('location: '.base_url.'login'); exit;
        }
        parent::__construct();
        $this->model = new GastosModel();
    }

    public function index(){ $this->views->getView('gastos/index'); }

    public function listar(){
        header('Content-Type: application/json');
        $gastos = $this->model->getGastos();
        echo json_encode($gastos !== false ? $gastos : []); // Si falla, envía array vacío
        exit;
    }

    public function registrar()
    {
        $id   = strClean($_POST['id']??'');
        $desc = strClean($_POST['descripcion']);
        $mon  = strClean($_POST['monto']);
        $fec  = strClean($_POST['fecha']);
        $usr  = $_SESSION['id_usuario'];
        $caja = (new CajaModel())->idCajaActiva($usr);

        if($caja==0){ $this->resp('Abra caja primero','error'); }

        if($id==''){
            $ok = $this->model->registrar($desc,$mon,$fec,$usr,$caja);
            $ok? $this->resp('Gasto registrado') : $this->resp('Error','error');
        }else{
            $ok = $this->model->actualizar($desc,$mon,$fec,$caja,$id);
            $ok? $this->resp('Gasto actualizado') : $this->resp('Error','error');
        }
    }

    public function editar($id){
        header('Content-Type: application/json');
        echo json_encode($this->model->getGasto($id)); exit;
    }

    public function eliminar($id){
        $ok = $this->model->desactivarGasto($id);
        $ok? $this->resp('Eliminado'):$this->resp('Error','error');
    }

    private function resp($m,$ico='success'){
        header('Content-Type: application/json');
        echo json_encode(['msg'=>$m,'icono'=>$ico]); exit;
    }
}

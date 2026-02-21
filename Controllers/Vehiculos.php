<?php

class Vehiculos extends Controller
{
    public function __construct()
    {
        session_start();
        if (empty($_SESSION['id_usuario'])) {
            header("location: " . base_url . 'login');
        }
        if ($_SESSION['tipo'] != 1) {
            header("location: " . base_url . 'login');
        }
        parent::__construct();
    }
    public function index()
    {
        $data['marcas'] = $this->model->getDatos('marcas');
        $data['tipos'] = $this->model->getDatos('tipos');
        $this->views->getView("vehiculos/index", $data);
    }
    public function listar()
    {
        $data = $this->model->vehiculos();
        $date = date('Y-m-d');
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['date'] = $date;
            $data[$i]['editar'] = '<button class="btn btn-outline-primary" type="button" onclick="btnEditarVeh(' . $data[$i]['id'] . ');"><i class="fas fa-edit"></i></button>';
            $data[$i]['eliminar'] = '<button class="btn btn-outline-danger" type="button" onclick="btnEliminarVeh(' . $data[$i]['id'] . ');"><i class="fas fa-trash-alt"></i></button>';
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function registrar()
    {
        $marca = intval(strClean($_POST['marca']));
        $tipo = intval(strClean($_POST['tipo']));
        $modelo = strClean($_POST['modelo']);
        $precio_hora = strClean($_POST['precio_hora']);
        $precio_dia = strClean($_POST['precio_dia']);
        $precio_mes = strClean($_POST['precio_mes']);
        $id = strClean($_POST['id']);

        if (
            empty($marca) || empty($tipo) || empty($modelo)
            || empty($precio_dia) || empty($precio_hora) || empty($precio_mes)
        ) {
            $msg = array('msg' => 'Todo los campos son obligatorios', 'icono' => 'warning');
        } else {
            if ($id == "") {
                $data = $this->model->registrarVehiculo($precio_hora, $precio_dia, $precio_mes, $modelo, $tipo, $marca);
                if ($data == "ok") {
                    $msg = array('msg' => 'Vehículo registrado con éxito', 'icono' => 'success');
                } else {
                    $msg = array('msg' => 'Error al registrar', 'icono' => 'error');
                }
            } else {
                $data = $this->model->modificarVehiculo($precio_hora, $precio_dia, $precio_mes, $modelo, $tipo, $marca, $id);
                if ($data == "modificado") {
                    $msg = array('msg' => 'Vehículo modificado', 'icono' => 'success');
                } else {
                    $msg = array('msg' => 'Error al modificar', 'icono' => 'error');
                }
            }
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function editar(int $id)
    {
        $data = $this->model->editarVeh($id);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function eliminar(int $id)
    {
        $data = $this->model->accionVeh(0, $id);
        if ($data == 1) {
            $msg = array('msg' => 'Vehículo dado de baja', 'icono' => 'success');
        } else {
            $msg = array('msg' => 'Error al eliminar el Vehiculo', 'icono' => 'error');
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function buscarVehiculo()
    {
        if (isset($_GET['veh'])) {
            $data = $this->model->buscarVehiculo($_GET['veh']);
            $datos = array();
            foreach ($data as $row) {
                $data['id'] = $row['id'];
                $data['label'] = $row['placa'] . ' - ' . $row['marca'] . ' - ' . $row['tipo'];
                $data['value'] = $row['placa'] . ' - ' . $row['marca'] . ' - ' . $row['tipo'];
                $data['hora'] = $row['precio_hora'];
                $data['dia'] = $row['precio_dia'];
                $data['mes'] = $row['precio_mes'];
                array_push($datos, $data);
            }
            echo json_encode($datos, JSON_UNESCAPED_UNICODE);
            die();
        }
    }
}

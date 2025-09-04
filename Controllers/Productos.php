<?php
require_once __DIR__ . '/../models/ProductoModel.php';

class Productos extends Controller
{
    public function __construct()
    {
        session_start();
        if (empty($_SESSION['id_usuario']) || $_SESSION['tipo'] != 1) {
            header('location: '.base_url.'login');
        }
        parent::__construct();
        $this->model = new ProductoModel();
    }

    /* ---------- vista principal ---------- */
    public function index()
    {
        $data['categorias'] = $this->model->getCategorias();
        $this->views->getView('productos/index', $data);
    }

    /* ---------- listar (DataTable) ---------- */
   public function listar()
    {
        $data = $this->model->getProductos();
        for ($i=0; $i<count($data); $i++) {
            $data[$i]['acciones'] = '
                <div class="text-center">
                    <button class="btn btn-outline-primary btn-sm" onclick="editProd('.$data[$i]['id'].')">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-outline-danger btn-sm" onclick="delProd('.$data[$i]['id'].')">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>';
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE); die();
    }

    /* ---------- registrar / modificar ---------- */
    public function registrar()
    {
        $id  = strClean($_POST['id']??'');
        $cod = strClean($_POST['codigo']);
        $nom = strClean($_POST['nombre']);
        $des = strClean($_POST['descripcion']);
        $pc  = strClean($_POST['precio_compra']);
        $pv  = strClean($_POST['precio_venta']);
        $st  = strClean($_POST['stock_actual']);
        $cat = strClean($_POST['id_categoria']);

        if ($cod==''||$nom==''||$pv==''||$st==''||$cat==''){
            $msg=['msg'=>'Todos los campos con * son requeridos','icono'=>'warning'];
        }else{
            if ($id==''){
                $res=$this->model->registrarProducto($cod,$nom,$des,$pc,$pv,$st,$cat);
                $msg=($res>0)?['msg'=>'Producto registrado','icono'=>'success']:
                     (($res=='existe')?['msg'=>'Código duplicado','icono'=>'warning']:
                                        ['msg'=>'Error al registrar','icono'=>'error']);
            }else{
                $res=$this->model->modificarProducto($cod,$nom,$des,$pc,$pv,$st,$cat,$id);
                $msg=($res==1)?['msg'=>'Producto actualizado','icono'=>'success']:
                     ['msg'=>'Error al actualizar','icono'=>'error'];
            }
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE); die();
    }

    /* ---------- obtener un registro ---------- */
    public function editar(int $id)
    {
        echo json_encode($this->model->getProducto($id), JSON_UNESCAPED_UNICODE); die();
    }

    /* ---------- baja lógica ---------- */
    public function eliminar(int $id)
    {
        $res=$this->model->setInactivo($id);
        $msg=($res==1)?['msg'=>'Producto dado de baja','icono'=>'success']:
                       ['msg'=>'Error','icono'=>'error'];
        echo json_encode($msg, JSON_UNESCAPED_UNICODE); die();
    }
}

<?php
require_once __DIR__.'/../models/VentaModel.php';
require_once __DIR__.'/../models/CajaModel.php';


class Ventas extends Controller
{
    public function __construct()
    {
        session_start();
        if(empty($_SESSION['id_usuario'])||$_SESSION['tipo']!=1){
            header('location: '.base_url.'login'); exit;
        }
        parent::__construct();
        $this->model = new VentaModel();
    }

    public function index(){ $this->views->getView('ventas/index'); }

    public function listar()
    {
        $data=$this->model->getVentas();
        foreach ($data as &$v){
            $v['acciones'] = '
            <a  href="'.base_url.'ventas/pdfFactura/'.$v['id'].'"
                class="btn btn-outline-danger btn-sm" target="_blank"
                title="Ver factura PDF">
                <i class="fas fa-file-pdf"></i>
            </a>
            <button class="btn btn-outline-primary btn-sm"
                    onclick="editVenta('.$v['id'].')" title="Editar">
                <i class="fas fa-edit"></i>
            </button>
            <button class="btn btn-outline-danger btn-sm"
                    onclick="delVenta('.$v['id'].')"  title="Anular">
                <i class="fas fa-trash"></i>
            </button>';
            $v['total_neto'] = $v['total_neto'];
        }

        header('Content-Type: application/json'); echo json_encode($data); exit;
    }

   public function registrar()
{
    /* ─────────────── 1.  Datos de cabecera ─────────────── */
    $fecha     = strClean($_POST['fecha']);
    $bruto     = strClean($_POST['total_bruto']);
    $descuento = strClean($_POST['descuento']);
    $impuesto  = strClean($_POST['impuesto']);
    $neto      = strClean($_POST['total_neto']);
    $metodo    = strClean($_POST['metodo_pago']);
    $cliente   = strClean($_POST['id_cli']);

    /* ─────────────── 2.  Caja activa ─────────────── */
    $usuario  = $_SESSION['id_usuario'];
    $cajaM    = new CajaModel();
    $idCaja   = $cajaM->idCajaActiva($usuario);
    if ($idCaja == 0) { $this->resp('Debe abrir una caja primero', 'error'); }

    /* ─────────────── 3.  Carrito (detalle) ────────── */
    $detalle = json_decode($_POST['carrito'] ?? '[]', true);
    if (empty($detalle)) { $this->resp('Agregue productos', 'warning'); }

    /* ─────────────── 4.  Insertar venta ───────────── */
    $cab = [$fecha,$bruto,$descuento,$impuesto,$neto,
            $metodo,$cliente,$usuario,$idCaja];

    $resultado = $this->model->registrarVenta($cab, $detalle);

    if ($resultado['ok']) {
        $this->resp('Venta registrada','success',['id'=>$resultado['id']]);
    } else {
        $this->resp($resultado['msg'],'error');
    }
}


    public function editar($id){
        header('Content-Type: application/json');
        echo json_encode($this->model->getVenta($id)); exit;
    }

    public function eliminar($id){
        $ok=$this->model->setInactivo($id);
        $ok==1? $this->resp('Venta anulada') : $this->resp('Error','error');
    }
    /* ---------------------------------------------------------------
   PDF – Factura simplificada de la venta
   URL: /ventas/pdfFactura/{id}
----------------------------------------------------------------*/
public function pdffac($id)
{
    ob_clean(); // ← limpia salida previa para evitar error de FPDF

    /* ---- 1.  Traemos cabecera + detalle -------------------- */
    $venta    = $this->model->getVenta($id);
    $detalle  = $this->model->getDetalleVenta($id);
    $empresa  = $this->model->getEmpresa();          // si ya tienes ese método
    if (empty($venta)) { exit('Venta inexistente'); }

    /* ---- 2.  Generamos PDF con FPDF ------------------------ */
    require 'Libraries/fpdf/fpdf.php';
    $pdf = new FPDF('P','mm','A4');
    $pdf->AddPage();
    $pdf->SetMargins(10,10,10);

    /*  Logo (arriba‑derecha)  */
    $pdf->Image('Assets/img/logo.png', 160, 10, 35);

    /*  Encabezado empresa  */
    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(140,6,utf8_decode($empresa['nombre']),0,1);
    $pdf->SetFont('Arial','',9);
    $pdf->Cell(140,5,'RUC: '.$empresa['ruc'],0,1);
    $pdf->Cell(140,5,utf8_decode('Tel.: '.$empresa['telefono']),0,1);
    $pdf->Cell(140,5,utf8_decode($empresa['direccion']),0,1);
    $pdf->Ln(4);

    /*  Datos venta  */
    $pdf->SetFont('Arial','B',11);
    $pdf->Cell(0,6,'FACTURA #'.$venta['id'],0,1,'C');
    $pdf->Ln(2);
    $pdf->SetFont('Arial','',9);
    $pdf->Cell(30,5,'Fecha:');   $pdf->Cell(60,5,$venta['fecha'],0,0);
    $pdf->Cell(30,5,'Cliente:'); $pdf->Cell(60,5,utf8_decode($venta['cliente']),0,1);
    $pdf->Cell(30,5,'Met. Pago:');$pdf->Cell(60,5,$venta['metodo_pago'],0,1);
    $pdf->Ln(3);

    /*  Tabla detalle  */
    $pdf->SetFillColor(200,200,200);
    $pdf->SetFont('Arial','B',9);
    $pdf->Cell(80,6,'Producto',1,0,'L',true);
    $pdf->Cell(25,6,'Cant',1,0,'C',true);
    $pdf->Cell(30,6,'P.Unit',1,0,'C',true);
    $pdf->Cell(35,6,'Subtotal',1,1,'C',true);

    $pdf->SetFont('Arial','',9);
    foreach($detalle as $d){
        $pdf->Cell(80,6,utf8_decode($d['nombre']),1);
        $pdf->Cell(25,6,$d['cantidad'],1,0,'C');
        $pdf->Cell(30,6,number_format($d['precio_unit'],2),1,0,'R');
        $pdf->Cell(35,6,number_format($d['subtotal'],2),1,1,'R');
    }

    /*  Totales  */
    $pdf->Ln(2);
    $pdf->Cell(105);                 // sangría
    $pdf->Cell(40,6,'Total bruto',0,0,'R');
    $pdf->Cell(35,6,number_format($venta['total_bruto'],2),0,1,'R');

    $pdf->Cell(105);
    $pdf->Cell(40,6,'Descuento',0,0,'R');
    $pdf->Cell(35,6,number_format($venta['descuento'],2),0,1,'R');

    $pdf->Cell(105);
    $pdf->Cell(40,6,'Impuesto',0,0,'R');
    $pdf->Cell(35,6,number_format($venta['impuesto'],2),0,1,'R');

    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(105);
    $pdf->Cell(40,7,'TOTAL NETO',1,0,'R');
    $pdf->Cell(35,7,number_format($venta['total_neto'],2),1,1,'R');

    /*  Mensaje final centrado  */
    $pdf->Ln(6);
    $pdf->Cell(0,5,utf8_decode('GRACIAS POR SU PREFERENCIA'),0,1,'C');

    $pdf->Output();
}


   /* en Controllers/Ventas.php (o en tu Controller base si lo heredas) */
    private function resp($m, $ico='success', array $extra = [])
    {
        // $extra se fusiona con los campos por defecto
        $out = array_merge(['msg'=>$m,'icono'=>$ico], $extra);

        header('Content-Type: application/json; charset=utf-8');
        if (ob_get_length()) ob_clean();              // evita texto previo
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
        exit;
    }


   public function buscarProducto()
{
    $valor = strClean($_GET['prod'] ?? '');

    $res = $this->model->buscarProducto($valor);

    $data = [];
    foreach ($res as $row) {
        $data[] = [
            'id'    => $row['id'],
            'value' => $row['nombre'] . ' – $' . number_format($row['precio_venta'], 2),
            'precio'=> $row['precio_venta'],
            'stock' => $row['stock_actual']
        ];
    }

    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    die();
}

/* -----------------------------------------------------------------
   Ticket / Factura térmica 80 mm
   URL: /ventas/pdfTicket/{id}
   ----------------------------------------------------------------- */
/* ------------------------------------------------------------- */
/*  Ventas::pdfFactura($id) – Ticket 80 mm con FPDF              */
/* ------------------------------------------------------------- */
public function pdfFactura($id)
{
    $empresa = $this->model->getEmpresa();
    $venta   = $this->model->getVenta($id);
    $detalle = $this->model->getDetalleVenta($id);

    if (!$venta) { exit('Venta no encontrada'); }

    require 'Libraries/fpdf/fpdf.php';

    $alto = 140 + (count($detalle) * 8);
    $pdf  = new FPDF('P', 'mm', [80, $alto]);
    $pdf->SetMargins(3, 3, 3);
    $pdf->AddPage();

    // --- LOGO (opcional)
    if (is_file('Assets/img/'.$empresa['logo'])) {
        $pdf->Image('Assets/img/'.$empresa['logo'], 5, 3, 18);
        $pdf->Ln(22);
    }

    // --- DATOS EMPRESA
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(0, 4, utf8_decode($empresa['nombre']), 0, 1, 'C');
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(0, 4, 'RNC: '.$empresa['ruc'], 0, 1, 'C');
    $pdf->Cell(0, 4, 'Tel: '.$empresa['telefono'], 0, 1, 'C');
    $pdf->Cell(0, 4, utf8_decode($empresa['direccion']), 0, 1, 'C');
    $pdf->Ln(2);

    // --- DATOS VENTA
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(0, 4, 'Ticket: '.$venta['id'], 0, 1);
    $pdf->Cell(0, 4, 'Fecha : '.$venta['fecha'], 0, 1);
    $pdf->Cell(0, 4, 'Cliente: '.utf8_decode($venta['cliente']), 0, 1);
    $pdf->Ln(2);

    // --- CABECERA TABLA
    $pdf->SetFont('Arial', 'B', 7.5);
    $pdf->Cell(30, 5, 'Producto', 1, 0, 'L');
    $pdf->Cell(8,  5, 'Cant',     1, 0, 'C');
    $pdf->Cell(17, 5, 'P.Unit',   1, 0, 'R');
    $pdf->Cell(20, 5, 'Importe',  1, 1, 'R');

    // --- DETALLE PRODUCTOS
    $pdf->SetFont('Arial', '', 7);
    foreach ($detalle as $it) {
        $pdf->Cell(30, 5, utf8_decode(mb_strimwidth($it['nombre'],0,22,'…')), 1, 0, 'L');
        $pdf->Cell(8,  5, $it['cantidad'], 1, 0, 'C');
        $pdf->Cell(17, 5, number_format($it['precio_unit'], 2), 1, 0, 'R');
        $pdf->Cell(20, 5, number_format($it['subtotal'], 2),    1, 1, 'R');
    }

    $pdf->Ln(1);

    // --- TOTALES
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(40, 4, 'Subtotal', 0, 0, 'R');
    $pdf->Cell(0,  4, number_format($venta['total_bruto'], 2), 0, 1, 'R');
    if ($venta['descuento'] > 0) {
        $pdf->Cell(40, 4, 'Descuento', 0, 0, 'R');
        $pdf->Cell(0,  4, number_format($venta['descuento'], 2), 0, 1, 'R');
    }
    if ($venta['impuesto'] > 0) {
        $pdf->Cell(40, 4, 'Impuesto', 0, 0, 'R');
        $pdf->Cell(0,  4, number_format($venta['impuesto'], 2), 0, 1, 'R');
    }

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell(40, 5, 'TOTAL', 0, 0, 'R');
    $pdf->Cell(0,  5, number_format($venta['total_neto'], 2), 0, 1, 'R');
    $pdf->Ln(2);

    // --- MÉTODO DE PAGO
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(0, 4, 'Pago: '.utf8_decode($venta['metodo_pago']), 0, 1);
    $pdf->Ln(2);

    // --- MENSAJE FINAL
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell(0, 5, utf8_decode('GRACIAS POR SU PREFERENCIA'), 0, 1, 'C');
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(0, 4, utf8_decode('¡Gracias por su preferencia!'), 0, 1, 'C');

    // --- SALIDA
    $pdf->Output("I", "ticket_$id.pdf");
}



    
}

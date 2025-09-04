<?php
require_once __DIR__.'/../models/CajaModel.php';

class Caja extends Controller
{
    public function __construct(){
        session_start();
        if(empty($_SESSION['id_usuario'])||$_SESSION['tipo']!=1){
            header('location: '.base_url.'login'); exit;
        }
        parent::__construct();
        $this->model = new CajaModel();
    }

    public function index(){ $this->views->getView('caja/index'); }

    public function estado()
    {
        $id = $this->model->idCajaActiva($_SESSION['id_usuario']);

        if (!$id) {
            $data = [];                      //  sin caja abierta
        } else {
            $data      = $this->model->datos($id);
            $totales   = $this->model->totalesActuales($id);
            $data      = array_merge($data, $totales);

            /* cálculo listo para el frontend */
            $data['efectivo_cierre_calc'] =
                $data['efectivo_inicio']
                + $totales['total_alquiler']
                + $totales['total_ventas']
                - $totales['total_gastos'];
        }

        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }


    public function abrir(){
        $mnt=$_POST['efectivo_inicio']; $id=$this->model->abrir($mnt,$_SESSION['id_usuario']);
        $_SESSION['id_caja_activa']=$id;
        $this->resp($id?'Caja abierta':'Error','success');
    }

    public function cerrar(){
        $id=$this->model->idCajaActiva($_SESSION['id_usuario']);
        if(!$id){ $this->resp('No hay caja abierta','error'); }
        $ok=$this->model->cerrar($id,$_POST['efectivo_cierre'],$_POST['obs']);
        unset($_SESSION['id_caja_activa']);
        $ok?$this->resp('Caja cerrada'):$this->resp('Error','error');
    }

    public function listado()
        {
            $data = $this->model->listado();   // ← método del modelo que ya existe
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($data);           // convierte a JSON
            exit;
        }


    private function resp($m,$ico='success'){
        header('Content-Type: application/json');
        echo json_encode(['msg'=>$m,'icono'=>$ico]); exit;
    }

    /* -----------------------------------------------------------------
   Anular una caja cerrada (estado pasa a 2)
   URL: /caja/cancelar/{id}
   ----------------------------------------------------------------- */
    public function cancelar($id)
    {
        $ok = $this->model->cancelar($id);
        $ok ? $this->resp('Caja cancelada') :
            $this->resp('No se puede cancelar (debe estar cerrada)','error');
    }
    

    public function pdf($id)
{
    // ───── Datos generales ─────
    $empresa = $this->model->getEmpresa();
    $caja    = $this->model->getCaja($id);

    if (empty($caja)) {
        echo 'Caja no encontrada'; return;
    }

    // ───── Consultas Detalladas ─────
    $alquileres = $this->model->selectAll(
        "SELECT a.id,
                c.nombre           AS cliente,
                CONCAT(t.tipo,' / ',v.placa) AS vehiculo,
                a.cantidad,
                a.monto,
                a.abono,
                (a.cantidad*a.monto) AS total
           FROM alquiler a
           JOIN clientes  c ON c.id = a.id_cliente
           JOIN vehiculos v ON v.id = a.id_vehiculo
           JOIN tipos     t ON t.id = v.id_tipo
          WHERE a.id_caja = ?", [$id]);

    $ventas = $this->model->selectAll(
        "SELECT v.id,
                v.fecha,
                IFNULL(c.nombre,'—') AS cliente,
                v.total_neto
           FROM ventas v
      LEFT JOIN clientes c ON c.id = v.id_cliente
          WHERE v.id_caja = ? AND v.estado = 1",
        [$id]);

    $detalles = $this->model->selectAll(
        "SELECT dv.id_venta,
                p.nombre AS prod,
                dv.cantidad,
                dv.precio_unit,
                dv.subtotal
           FROM detalle_ventas dv
           JOIN productos p ON p.id = dv.id_producto
          WHERE dv.id_venta IN (
                SELECT id FROM ventas WHERE id_caja = ? AND estado = 1
          )", [$id]);

    $gastos = $this->model->selectAll(
        "SELECT id, descripcion, monto
           FROM gastos
          WHERE id_caja = ?", [$id]);

    $itemsVenta = [];
    foreach ($detalles as $d) {
        $itemsVenta[$d['id_venta']][] = $d;
    }

    // ───── PDF ─────
    require('Libraries/fpdf/fpdf.php');
    $pdf = new FPDF('P', 'mm', 'A4');
    $pdf->AliasNbPages();
    $pdf->AddPage();

    // --- Encabezado ---
    if (!empty($empresa['logo'])) {
        $logoPath = 'Assets/img/'.$empresa['logo'];
        if (file_exists($logoPath)) $pdf->Image($logoPath,10,8,30);
    }

    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(0,10,utf8_decode($empresa['nombre_empresa'] ?? 'TEAM BLANQUITO'),0,1,'R');
    $pdf->SetFont('Arial','',12);
    $pdf->Ln(3);
    $pdf->Cell(0,8,utf8_decode("Detalle Caja #{$id}  ({$caja['fecha_apertura']} {$caja['hora_apertura']})"),0,1,'C');
    $pdf->Ln(2);

    // Función auxiliar para título de sección
    $secTitle = function($text) use ($pdf){
        $pdf->SetFont('Arial','B',11);
        $pdf->SetFillColor(230,230,230);
        $pdf->Cell(0,8,utf8_decode($text),0,1,'L',true);
        $pdf->Ln(1);
        $pdf->SetFont('Arial','',10);
    };

    // --- Tabla Alquileres ---
    $secTitle('Alquileres');
    if ($alquileres) {
        $pdf->SetFont('Arial','B',9);
        $pdf->Cell(12,6,'ID',1);
        $pdf->Cell(45,6,'Cliente',1);
        $pdf->Cell(40,6,utf8_decode('Vehículo'),1);
        $pdf->Cell(12,6,'Cant',1,0,'R');
        $pdf->Cell(20,6,'Monto',1,0,'R');
        $pdf->Cell(20,6,'Pagado',1,0,'R');
        $pdf->Cell(20,6,'Total',1,1,'R');

        $pdf->SetFont('Arial','',8);
        foreach($alquileres as $a){
            $pdf->Cell(12,6,$a['id'],1);
            $pdf->Cell(45,6,utf8_decode(mb_strimwidth($a['cliente'],0,26,'…')),1);
            $pdf->Cell(40,6,utf8_decode(mb_strimwidth($a['vehiculo'],0,22,'…')),1);
            $pdf->Cell(12,6,$a['cantidad'],1,0,'R');
            $pdf->Cell(20,6,number_format($a['monto'],2),1,0,'R');
            $pdf->Cell(20,6,number_format($a['abono'],2),1,0,'R');
            $pdf->Cell(20,6,number_format($a['total'],2),1,1,'R');
        }
    } else {
        $pdf->Cell(0,6,'Sin registros',1,1);
    }
    $pdf->Ln(2);

    // --- Tabla Ventas ---
    $secTitle('Ventas');
    if ($ventas) {
        $pdf->SetFont('Arial','B',9);
        $pdf->Cell(10,6,'ID',1);
        $pdf->Cell(25,6,'Fecha',1);
        $pdf->Cell(45,6,'Cliente',1);
        $pdf->Cell(25,6,'Total',1,1,'R');

        $pdf->SetFont('Arial','',8);
        foreach($ventas as $v){
            $pdf->Cell(10,6,$v['id'],1);
            $pdf->Cell(25,6,substr($v['fecha'],0,16),1);
            $pdf->Cell(45,6,utf8_decode(mb_strimwidth($v['cliente'],0,24,'…')),1);
            $pdf->Cell(25,6,number_format($v['total_neto'],2),1,1,'R');

            // Productos vendidos (sangrado)
            if (!empty($itemsVenta[$v['id']])) {
                $pdf->SetFont('Arial','I',7);
                foreach($itemsVenta[$v['id']] as $it){
                    $txt = "   ▸ {$it['cantidad']} x {$it['prod']}";
                    $pdf->Cell(70,5,utf8_decode(mb_strimwidth($txt,0,50,'…')),0,0,'L');
                    $pdf->Cell(25,5,number_format($it['subtotal'],2),0,1,'R');
                }
                $pdf->SetFont('Arial','',8);
            }
        }
    } else {
        $pdf->Cell(0,6,'Sin registros',1,1);
    }
    $pdf->Ln(2);

    // --- Tabla Gastos ---
    $secTitle('Gastos');
    if ($gastos) {
        $pdf->SetFont('Arial','B',9);
        $pdf->Cell(15,6,'ID',1);
        $pdf->Cell(80,6,utf8_decode('Descripción'),1);
        $pdf->Cell(30,6,'Monto',1,1,'R');
        $pdf->SetFont('Arial','',9);
        foreach($gastos as $g){
            $pdf->Cell(15,6,$g['id'],1);
            $pdf->Cell(80,6,utf8_decode($g['descripcion']),1);
            $pdf->Cell(30,6,number_format($g['monto'],2),1,1,'R');
        }
    } else {
        $pdf->Cell(0,6,'Sin registros',1,1);
    }
    $pdf->Ln(3);

    // --- Resumen final ---
    $pdf->SetFillColor(230,230,230);
    $pdf->SetFont('Arial','B',11);
    $pdf->Cell(0,8,'Resumen de Caja',0,1,'L',true);
    $pdf->SetFont('Arial','',10);

    $label = function($txt, $val) use ($pdf){
        $pdf->Cell(50,6,utf8_decode($txt),0,0);
        $pdf->Cell(30,6,'$'.number_format($val,2),0,1,'R');
    };

    $label('Efectivo Inicial:',   $caja['efectivo_inicio']);
    $label('Total Ventas:',       $caja['total_ventas']);
    $label('Total Alquileres:',   $caja['total_alquiler']);
    $label('Total Gastos:',       $caja['total_gastos']);

   /* ---------- EFECTIVO CIERRE (recuadro) ---------- */
    $pdf->Ln(2);                          // pequeño espacio
    $pdf->SetFont('Arial','B',11);
    $pdf->SetFillColor(235,235,235);      // gris suave (puedes ajustar)

    $pdf->SetLineWidth(0.2);              // grosor de borde estándar
    $efec = number_format($caja['efectivo_cierre'],2);

    /* ancho total 80 mm (50 + 30) */
    $pdf->Cell(50, 9, 'Efectivo Cierre:', 1, 0, 'L', true);   // 1 = bordes
    $pdf->Cell(30, 9, '$ '.$efec,        1, 1, 'R', true);


    // --- Pie de página ---
    $pdf->SetY(-15);
    $pdf->SetFont('Arial','I',8);
    $pdf->Cell(0,10,'Página '.$pdf->PageNo().' de {nb}',0,0,'C');

    $pdf->Output();
}


}

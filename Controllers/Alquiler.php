<?php

class Alquiler extends Controller
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
        $data['vehiculos'] = $this->model->getVehiculos();
        $data['documentos'] = $this->model->getDoc();
        $this->views->getView("alquiler/index", $data);
    }

    public function registrar()
    {
        /* ───────── 1. Variables recibidas ───────── */
        $id_cli         = strClean($_POST['id_cli']);
        $id_veh         = strClean($_POST['id_veh']);
        $sel_cliente    = strClean($_POST['select_cliente']);
        $sel_vehiculo   = strClean($_POST['select_vehiculo']);
        $cantidad       = (int)strClean($_POST['cantidad']);
        $tipo_precio    = (int)strClean($_POST['precios']);   // 1‑hora | 2‑día | 3‑mes
        $abono          = (float)strClean($_POST['abono']);
        $fecha_ini      = strClean($_POST['fecha']);          // yyyy‑mm‑dd hh:ii
        $id_doc         = (int)strClean($_POST['documento']);
        $obs            = strClean($_POST['observacion']);

        /* ───────── 2. Validación básica ─────────── */
        if (
            !$id_cli || !$id_veh || !$sel_cliente || !$sel_vehiculo ||
            !$cantidad || !$tipo_precio || $abono==='' || !$fecha_ini || !$id_doc
        ){
            $this->json(['msg'=>'Todos los campos * son obligatorios','icono'=>'warning']);
        }

        /* ───────── 3. Precio y fecha fin ────────── */
        $veh   = $this->model->getVehiculo($id_veh);
        if(!$veh){ $this->json(['msg'=>'Vehículo inexistente','icono'=>'error']); }

        switch ($tipo_precio) {
            case 1:  $monto = $veh['precio_hora']; $delta = "+{$cantidad} hours"; break;
            case 2:  $monto = $veh['precio_dia'];  $delta = "+{$cantidad} days";  break;
            default: $monto = $veh['precio_mes'];  $delta = "+{$cantidad} month"; break;
        }
        $fecha_fin = date('Y-m-d H:i:s', strtotime($fecha_ini.$delta));

        /* ───────── 4. Disponibilidad (anti‑solape) ───────── */
        if ($this->model->haySolape($fecha_ini,$fecha_fin,$id_veh)){
            $this->json([
                'msg'=>"El vehículo ya está asignado entre {$fecha_ini} y {$fecha_fin}",
                'icono'=>'error'
            ]);
        }

        /* ───────── 5. Caja abierta obligatoria ─────────── */
        $idCaja = $_SESSION['id_caja_activa'] ?? 0;
        if ($idCaja == 0){
            $this->json(['msg'=>'Debe abrir una caja antes de registrar','icono'=>'error']);
        }

        /* ───────── 6. Insertar registro ─────────── */
        $idAlq = $this->model->registrarAlquiler(
                    $cantidad,$tipo_precio,$monto,$abono,
                    $fecha_ini,$fecha_fin,$obs,
                    $id_cli,$id_veh,$id_doc,$idCaja);

        if ($idAlq > 0){
            $this->json(['msg'=>'Alquiler registrado','icono'=>'success',
                        'id_alquiler'=>$idAlq]);
        }
        $this->json(['msg'=>'Error al registrar','icono'=>'error']);
    }

    /* helper interno para responder JSON */
    private function json(array $a){ echo json_encode($a,JSON_UNESCAPED_UNICODE); exit; }

    public function listar()
    {
        $data = $this->model->getAlquiler();
        for ($i = 0; $i < count($data); $i++) {
                $btnPdf   = '<a class="btn btn-outline-danger btn-sm" target="_blank"
                href="'.base_url.'alquiler/pdfPrestamo/'.$data[$i]['id'].'">
              <i class="fas fa-file-pdf"></i></a>';

                $btnTicket= '<a class="btn btn-outline-success btn-sm" target="_blank"
                            href="'.base_url.'alquiler/pdfTicket/'.$data[$i]['id'].'">
                            <i class="fas fa-receipt"></i></a>';
            $data[$i]['f_prestamo'] = '<span class="badge bg-primary">' . $data[$i]['fecha_prestamo'] . '</span>';
            $data[$i]['f_devolucion'] = '<span class="badge bg-info">' . $data[$i]['fecha_devolucion'] . '</span>';
            if ($data[$i]['estado'] == 1) {
                $data[$i]['recibir'] = '<button class="btn btn-outline-primary" type="button" onclick="entrega(' . $data[$i]['id'] . ');"><i class="fas fa-sync-alt"></i></button>';
                $data[$i]['accion'] = $btnPdf.' '.$btnTicket;
                $data[$i]['estatus'] = '<span class="badge bg-warning">Alquilado</span>';
            } else {
                $data[$i]['recibir'] = '';
                $data[$i]['accion'] = $btnPdf.' '.$btnTicket;
                $data[$i]['estatus'] = '<span class="badge bg-success">Devuelto</span>';
            }
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function ver(int $id)
    {
        $data = $this->model->verPrestamo($id);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function procesar(int $id)
    {
        if (is_numeric($id)) {
            $data = $this->model->procesarEntrega(0, $id);
            if ($data == 'ok') {
                $msg = array('msg' => 'Procesado con éxito', 'icono' => 'success');
            }else{
                $msg = array('msg' => 'Error al recibir el prestamo', 'icono' => 'error');
            }
            echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
    public function pdfPrestamo($id)
    {
        $empresa = $this->model->getEmpresa();
        $data = $this->model->verPrestamo($id);
        require('Libraries/fpdf/html2pdf.php');

        $pdf = new PDF_HTML('P', 'mm', array(210, 148));
        $pdf->AddPage();
        $pdf->SetMargins(10, 0, 0);
        $pdf->SetTitle('Reporte Pago');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(135, 8, utf8_decode($empresa['nombre']), 0, 1, 'C');
        //$pdf->Image('Assets/img/logo.png', 50, 16, 20, 20);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(20, 5, 'Ruc: ', 0, 0, 'L');
        $pdf->Cell(50, 5, $empresa['ruc'], 0, 0, 'L');
        $pdf->Cell(20, 5, utf8_decode('Teléfono: '), 0, 0, 'L');
        $pdf->Cell(50, 5, $empresa['telefono'], 0, 1, 'L');
        $pdf->Cell(20, 5, utf8_decode('Correo: '), 0, 0, 'L');
        $pdf->Cell(50, 5, utf8_decode($empresa['correo']), 0, 0, 'L');
        $pdf->Cell(20, 5, utf8_decode('Dirección: '), 0, 0, 'L');
        $pdf->Cell(50, 5, utf8_decode($empresa['direccion']), 0, 1, 'L');
        $pdf->Cell(20, 5, 'Fecha: ', 0, 0, 'L');
        $pdf->Cell(50, 5, $data['fecha_prestamo'], 0, 0, 'L');
        if ($data['estado'] == 1) {
            $pdf->SetTextColor(255, 0, 0);
            $estado = 'Alquilado';
        } else {
            $pdf->SetTextColor(0, 0, 255);
            $estado = 'Devuelto';
        }
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(20, 5, 'Estado: ', 0, 0, 'L');
        $pdf->Cell(50, 5, $estado, 0, 1, 'L');
        //Encabezado
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(135, 10, 'Datos del Cliente', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(40, 5, 'DOC: ' . $data['dni'], 1, 0, 'L');
        $pdf->Cell(95, 5, 'NOMBRE: ' . utf8_decode($data['nombre']), 1, 1, 'L');
        $pdf->Cell(65, 5, utf8_decode('DIRECCIÓN: ' . $data['direccion']), 1, 0, 'L');
        $pdf->Cell(70, 5, utf8_decode('TELÉFONO: ' . $data['telefono']), 1, 1, 'L');
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(135, 10, utf8_decode('Datos del Vehículo'), 0, 1, 'C');
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(65, 5, utf8_decode('PLACA: ' . $data['placa']), 1, 0, 'L');
        $pdf->Cell(70, 5, utf8_decode('VEHÍCULO: ' . $data['tipo']), 1, 1, 'L');
        $pdf->Cell(65, 5, utf8_decode('MÓDELO: ' . $data['modelo']), 1, 0, 'L');
        //COMPROBAR TIPO
        if ($data['tipo_precio'] == 1) {
            $tipo = 'HORAS: ';
        } else if ($data['tipo_precio'] == 2) {
            $tipo = 'DIAS: ';
        } else {
            $tipo = 'MESES: ';
        }

        $pdf->Cell(70, 5, utf8_decode($tipo . $data['cantidad']), 1, 1, 'L');
        $pdf->Cell(65, 5, utf8_decode('MONTO x ' . $tipo . $data['monto']), 1, 0, 'L');
        $pdf->Cell(70, 5, utf8_decode('PAGADO: ' . $data['abono']), 1, 1, 'L');
        $pdf->Cell(65, 5, utf8_decode('F. PRESTAMO: ' . $data['fecha_prestamo']), 1, 0, 'L');
        $pdf->Cell(70, 5, utf8_decode('F. DEVOLUCIÓN: ' . $data['fecha_devolucion']), 1, 1, 'L');
        $pdf->Ln();
        if ($data['estado'] == 0) {
            $total = 0;
        } else {
            $total = ($data['cantidad'] * $data['monto']) - $data['abono'];
        }
        $pdf->Cell(135, 5, utf8_decode('PENDIENTE: ' . number_format($total, 2)), 0, 1, 'C');
        $pdf->Ln();
        $pdf->Cell(65, 5, utf8_decode('_____________________________'), 0, 0, 'C');
        $pdf->Cell(65, 5, utf8_decode('_____________________________'), 0, 1, 'C');
        $pdf->Cell(65, 5, utf8_decode('Firma'), 0, 0, 'C');
        $pdf->Cell(65, 5, utf8_decode('Huella'), 0, 1, 'C');
        $pdf->Ln(2);
        $pdf->Ln(5); // Espacio antes del mensaje
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(135, 5, 'GRACIAS POR PREFERIRNOS', 0, 1, 'C');
$pdf->Ln(2); // Espacio después del mensaje

        $pdf->Output();
    }
    /* ------------------------------------------------------------------
 *  Reporte global de alquileres  –  PDF A4 horizontal
 * -----------------------------------------------------------------*/
public function pdfAlquiler()
{
    $empresa  = $this->model->getEmpresa();
    $alquiler = $this->model->getAlquiler();

    if (empty($alquiler)) { echo 'No hay registro'; return; }

    require 'Libraries/fpdf/fpdf.php';

    // ─── Configuración básica ──────────────────────────────────────
    $pdf = new FPDF('L', 'mm', 'A4');
    $pdf->SetMargins(5,5,5);
    $pdf->SetTitle('Reporte Alquiler');
    $pdf->AddPage();

    /* --------------------------------------------------------------
     *  Encabezado: logo + datos de empresa
     * --------------------------------------------------------------*/
    // Logo arriba‑derecha
    $pdf->Image('Assets/img/logo.png', 260, 6, 30);

    // Nombre de empresa centrado
    $pdf->SetFont('Arial','B',16);
    $pdf->Cell(0,8,utf8_decode($empresa['nombre']),0,1,'C');

    $pdf->SetFont('Arial','',10);
    $yInicio = $pdf->GetY();
    $label   = function($texto){ return utf8_decode($texto).': '; };

    $pdf->Cell(25,5,$label('RUC'),0,0);
    $pdf->Cell(60,5,$empresa['ruc'],0,0);
    $pdf->Cell(25,5,$label('Teléfono'),0,0);
    $pdf->Cell(60,5,$empresa['telefono'],0,1);

    $pdf->Cell(25,5,$label('Correo'),0,0);
    $pdf->Cell(60,5,utf8_decode($empresa['correo']),0,0);
    $pdf->Cell(25,5,$label('Dirección'),0,0);
    $pdf->Cell(60,5,utf8_decode($empresa['direccion']),0,1);

    $pdf->Ln(8);

    /* --------------------------------------------------------------
     *  Tabla de detalles
     * --------------------------------------------------------------*/
    // Cabecera
    $pdf->SetFillColor(0);           // negro
    $pdf->SetTextColor(255);         // blanco
    $pdf->SetFont('Arial','',11);

    $header = [
      ['Doc. Garantía',30],
      ['Cliente',50],
      ['Placa',25],
      ['Vehículo',35],
      ['F. Préstamo',32],
      ['F. Entrega',32],
      ['Cant',12],
      ['Monto',20],
      ['Pagado',20],
      ['Estado',20]
    ];
    foreach ($header as [$h,$w]) { $pdf->Cell($w,6,utf8_decode($h),1,0,'C',true); }
    $pdf->Ln();

    // Detalle
    $pdf->SetTextColor(0);           // negro
    $pdf->SetFont('Arial','',9);

    foreach ($alquiler as $row) {
        $estadoTxt = $row['estado']==1 ? 'Alquilado' : 'Devuelto';
        $montoPago = number_format($row['abono'],2);          // ahora lo llamas “pagado”
        $montoTot  = number_format($row['monto'],2);

        $pdf->Cell(30,6,utf8_decode($row['documento']),1);
        $pdf->Cell(50,6,utf8_decode($row['nombre']),1);
        $pdf->Cell(25,6,$row['placa'],1);
        $pdf->Cell(35,6,utf8_decode($row['tipo']),1);
        $pdf->Cell(32,6,$row['fecha_prestamo'],1);
        $pdf->Cell(32,6,$row['fecha_devolucion'],1);
        $pdf->Cell(12,6,$row['cantidad'],1,0,'C');
        $pdf->Cell(20,6,$montoTot,1,0,'R');
        $pdf->Cell(20,6,$montoPago,1,0,'R');

        // color verde / rojo para estado
        ($row['estado']==1) ? $pdf->SetTextColor(220,0,0) : $pdf->SetTextColor(0,150,0);
        $pdf->Cell(20,6,$estadoTxt,1,1,'C');
        $pdf->SetTextColor(0);      // restaura negro
    }

    /* --------------------------------------------------------------
     *  Mensaje final
     * --------------------------------------------------------------*/
    $pdf->Ln(3);
    $pdf->SetFont('Arial','B',11);
    $pdf->Cell(0,6,utf8_decode('GRACIAS POR SU PREFERENCIA'),0,1,'C');

    $pdf->Output();
}
public function pdfTicket($id)
{
    $empresa = $this->model->getEmpresa();
    $d       = $this->model->verPrestamo($id);

    if (!$d) { echo 'Alquiler no encontrado'; return; }

    require 'Libraries/fpdf/fpdf.php';

    $pdf = new FPDF('P','mm',[80,200]);
    $pdf->SetMargins(2,2,2);
    $pdf->AddPage();
    $pdf->SetAutoPageBreak(false);

    /* ---- LOGO (opcional) ---- */
    if (is_file('Assets/img/logo.png')) {
        $pdf->Image('Assets/img/logo.png', 25, 2, 30);
        $pdf->Ln(26);
    }

    /* ---- Encabezado empresa ---- */
    $pdf->SetFont('Arial','B',9);
    $pdf->MultiCell(0,4,utf8_decode($empresa['nombre']), '', 'C');
    $pdf->SetFont('Arial','',7);
    $pdf->MultiCell(0,4,"RNC: {$empresa['ruc']}", '', 'C');
    $pdf->MultiCell(0,4,utf8_decode($empresa['telefono']), '', 'C');
    $pdf->MultiCell(0,4,utf8_decode($empresa['direccion']), '', 'C');
    $pdf->Ln(2);

    /* ---- Datos del alquiler ---- */
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(0,4,'DATOS DEL ALQUILER', 0, 1, 'C');
    $pdf->SetFont('Arial','',7);
    $pdf->Cell(22,4,'Alquiler #:',0,0);  $pdf->Cell(0,4,$d['id'],0,1);
    $pdf->Cell(22,4,'Fecha:',0,0);       $pdf->Cell(0,4,$d['fecha_prestamo'],0,1);
    $pdf->Cell(22,4,'Cliente:',0,0);     $pdf->Cell(0,4,utf8_decode($d['nombre']),0,1);
    $pdf->Ln(2);

    /* ---- Detalle del vehículo ---- */
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(0,4,'DETALLE VEHICULO',0,1,'C');
    $pdf->SetFont('Arial','',7);
    $pdf->Cell(20,5,'Tipo:',1,0);   $pdf->Cell(0,5,utf8_decode($d['tipo']),1,1);
    $pdf->Cell(20,5,'Placa:',1,0);  $pdf->Cell(0,5,$d['placa'],1,1);
    $pdf->Cell(20,5,'Modelo:',1,0); $pdf->Cell(0,5,$d['modelo'],1,1);

    $tipoTxt = ['1'=>'Hora','2'=>'Día','3'=>'Mes'][$d['tipo_precio']];
    $pdf->Cell(20,5,'Cantidad:',1,0); $pdf->Cell(0,5,"{$d['cantidad']} {$tipoTxt}(s)",1,1);
    $pdf->Cell(20,5,'Precio x ' . $tipoTxt . ':',1,0); $pdf->Cell(0,5,'$'.number_format($d['monto'], 2),1,1);
    $pdf->Ln(2);

    /* ---- Resumen de pago ---- */
    $total = $d['cantidad'] * $d['monto'];
    $pend  = $total - $d['abono'];

    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(0,4,'RESUMEN DE PAGO',0,1,'C');
    $pdf->SetFont('Arial','',7);

    // Alineamos los montos a la derecha
    $pdf->Cell(40,4,'Total:',0,0);
    $pdf->Cell(0,4,'$'.number_format($total, 2),0,1,'R');

    $pdf->Cell(40,4,'Pagado:',0,0);
    $pdf->Cell(0,4,'$'.number_format($d['abono'], 2),0,1,'R');

    $pdf->Cell(40,4,'Pendiente:',0,0);
    $pdf->Cell(0,4,'$'.number_format($pend, 2),0,1,'R');

    $pdf->Ln(2);


    /* ---- Estado actual ---- */
    $estado = $d['estado'] == 1 ? 'ALQUILADO' : 'DEVUELTO';
    $pdf->SetFont('Arial','B',9);
    $pdf->Cell(0,5,$estado,0,1,'C');
    $pdf->Ln(3);

    /* ---- Mensaje final ---- */
    $pdf->SetFont('Arial','B',8);
    $pdf->MultiCell(0,4,utf8_decode('¡GRACIAS POR SU PREFERENCIA!'),'','C');
    $pdf->Ln(5);

    $pdf->Output();
}

}

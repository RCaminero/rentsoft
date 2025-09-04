<?php
/* ------------------------------------------------------------------
 *  ReporteMes – resumen mensual de Alquileres, Ventas y Gastos
 *  Rutas sugeridas
 *    /reportemes                 -> selector + tablas
 *    /reportemes/listar/MM,YYYY  -> JSON para las tablas
 *    /reportemes/pdf/MM,YYYY     -> PDF A4 vertical
 * -----------------------------------------------------------------*/
require_once __DIR__ . '/../models/ReporteModel.php';

/* ---------- clase PDF con pie de página automático ------------- */
require_once 'Libraries/fpdf/fpdf.php';

class PDF extends FPDF
{
    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') .
                          $this->PageNo() . ' de {nb}', 0, 0, 'C');
    }
}

class ReporteMes extends Controller
{
    /** @var ReporteModel */
    protected $model;

    public function __construct()
    {
        session_start();
        if (empty($_SESSION['id_usuario'])) {
            header('location: ' . base_url . 'login');
            exit;
        }
        parent::__construct();
        $this->model = new ReporteModel();
    }

    /* --------------------- Vista con filtros -------------------- */
    public function index()
    {
        $this->views->getView('reportemes/index');
    }

    /* --------------------- JSON para tablas -------------------- */
    public function listar($param = '')
    {
        if (!$param || strpos($param, ',') === false) {
            echo json_encode(['error' => 'Parámetro “mes,año” faltante']);
            return;
        }
        [$mes, $anio] = array_map('intval', explode(',', $param, 2));
        $data = $this->model->getReporteMensual($mes, $anio);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
    }

    /* --------------------------- PDF --------------------------- */
    public function pdf($param = '')
    {
        if (!$param || strpos($param, ',') === false) {
            exit('Parámetro “mes,año” faltante');
        }
        [$mes, $anio] = array_map('intval', explode(',', $param, 2));

        $empresa = $this->model->getEmpresa();
        $datos   = $this->model->getReporteMensual($mes, $anio);

        $pdf = new PDF('P', 'mm', 'A4');
        $pdf->AliasNbPages();
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->SetMargins(10, 10, 10);
        $pdf->AddPage();

        /* ----- Encabezado empresa / logo ----- */
        if (!empty($empresa['logo']) && file_exists('Assets/img/' . $empresa['logo'])) {
            $pdf->Image('Assets/img/' . $empresa['logo'], 10, 8, 30);
        }
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, utf8_decode($empresa['nombre_empresa'] ?? ''), 0, 1, 'R');

        $periodo = $this->mesNombre($mes) . " $anio";
        $pdf->Cell(0, 8, utf8_decode("Reporte mensual - $periodo"), 0, 1, 'C');
        $pdf->Ln(4);

        /* utilitario para secciones --------------- */
        $sec = function ($titulo) use ($pdf) {
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->SetFillColor(230, 230, 230);
            $pdf->Cell(0, 7, utf8_decode($titulo), 0, 1, 'L', true);
            $pdf->Ln(1);
            $pdf->SetFont('Arial', '', 9);
        };

        /* ================= ALQUILERES ================ */
        $sec('Alquileres');
        if ($datos['alquileres']) {
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(12, 6, 'ID', 1);
            $pdf->Cell(22, 6, 'Fecha', 1);

            $pdf->Cell(45, 6, 'Cliente', 1);

            $pdf->Cell(35, 6, utf8_decode('Vehículo'), 1);
            $pdf->Cell(12, 6, 'Cant', 1, 0, 'R');
            $pdf->Cell(18, 6, 'Monto', 1, 0, 'R');
            $pdf->Cell(18, 6, 'Pagado', 1, 0, 'R');
            $pdf->Cell(18, 6, 'Total', 1, 1, 'R');
            $pdf->SetFont('Arial', '', 8);

            foreach ($datos['alquileres'] as $a) {
                $pdf->Cell(12, 6, $a['id'], 1);
                $pdf->Cell(22, 6, substr($a['fecha'], 0, 10), 1);

                $pdf->Cell(45, 6, utf8_decode(mb_strimwidth($a['cliente'], 0, 24, '…')), 1);
                $pdf->Cell(35, 6, utf8_decode(mb_strimwidth($a['vehiculo'], 0, 20, '…')), 1);
                $pdf->Cell(12, 6, $a['cantidad'], 1, 0, 'R');
                $pdf->Cell(18, 6, number_format($a['monto'], 2), 1, 0, 'R');
                $pdf->Cell(18, 6, number_format($a['abono'], 2), 1, 0, 'R');
                $pdf->Cell(18, 6, number_format($a['total'], 2), 1, 1, 'R');
            }
        } else {
            $pdf->Cell(0, 6, 'Sin registros', 1, 1);
        }
        $pdf->Ln(2);

        /* ================= VENTAS ==================== */
        $sec('Ventas');
        if ($datos['ventas']) {
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(10, 6, 'ID', 1);
            $pdf->Cell(22, 6, 'Fecha', 1);
            $pdf->Cell(50, 6, 'Cliente', 1);
            $pdf->Cell(25, 6, 'Total', 1, 1, 'R');
            $pdf->SetFont('Arial', '', 8);

            foreach ($datos['ventas'] as $v) {
                $pdf->Cell(10, 6, $v['id'], 1);
                $pdf->Cell(22, 6, substr($v['fecha'], 0, 10), 1);
                $pdf->Cell(50, 6, utf8_decode(mb_strimwidth($v['cliente'], 0, 28, '…')), 1);
                $pdf->Cell(25, 6, number_format($v['total_neto'], 2), 1, 1, 'R');
            }
        } else {
            $pdf->Cell(0, 6, 'Sin registros', 1, 1);
        }
        $pdf->Ln(2);

        /* ================= GASTOS ==================== */
        $sec('Gastos');
        if ($datos['gastos']) {
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(12, 6, 'ID', 1);
            $pdf->Cell(22, 6, 'Fecha', 1);
            $pdf->Cell(70, 6, utf8_decode('Descripción'), 1);
            $pdf->Cell(25, 6, 'Monto', 1, 1, 'R');
            $pdf->SetFont('Arial', '', 8);

            foreach ($datos['gastos'] as $g) {
                $pdf->Cell(12, 6, $g['id'], 1);
                $pdf->Cell(22, 6, substr($g['fecha'], 0, 10), 1);
                $pdf->Cell(70, 6, utf8_decode(mb_strimwidth($g['descripcion'], 0, 38, '…')), 1);
                $pdf->Cell(25, 6, number_format($g['monto'], 2), 1, 1, 'R');
            }
        } else {
            $pdf->Cell(0, 6, 'Sin registros', 1, 1);
        }
        $pdf->Ln(4);

        /* =============== RESUMEN NUMÉRICO ============== */
        $totalAlq = array_sum(array_column($datos['alquileres'], 'total'));
        $totalVen = array_sum(array_column($datos['ventas'],     'total_neto'));
        $totalGas = array_sum(array_column($datos['gastos'],     'monto'));

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetFillColor(245, 245, 245);
        $pdf->Cell(0, 7, 'RESUMEN DEL MES', 0, 1, 'L', true);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(50, 6, 'Total Alquileres:', 0, 0);
        $pdf->Cell(30, 6, '$ ' . number_format($totalAlq, 2), 0, 1, 'R');
        $pdf->Cell(50, 6, 'Total Ventas:', 0, 0);
        $pdf->Cell(30, 6, '$ ' . number_format($totalVen, 2), 0, 1, 'R');
        $pdf->Cell(50, 6, 'Total Gastos:', 0, 0);
        $pdf->Cell(30, 6, '$ ' . number_format($totalGas, 2), 0, 1, 'R');

        /* ---------- TOTAL NETO (recuadro) ---------- */
        $pdf->Ln(2);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetFillColor(235, 235, 235);   // gris suave

        $neto = number_format($totalAlq + $totalVen - $totalGas, 2);

        /*  ancho total deseado (80 mm) = 50 + 30  */
        $pdf->Cell(50, 9, 'Total neto:',    1, 0, 'L', true);   // 1 = bordes, true = relleno
        $pdf->Cell(30, 9, '$ ' . $neto,     1, 1, 'R', true);


        $pdf->Output("I", "reporte_$periodo.pdf");
    }

    /* ---------- helper nombre mes ------------------- */
    private function mesNombre(int $m): string
    {
        $n = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo',  6 => 'Junio',   7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        return $n[$m] ?? (string)$m;
    }
}

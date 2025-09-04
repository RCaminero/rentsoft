<?php cargarHeader($_SESSION['tipo']); ?>

<h4 class="mb-3">Reporte mensual</h4>

<div class="row g-2 mb-3">
  <div class="col-auto">
      <input type="month" id="mesSel" class="form-control" value="<?=date('Y-m');?>">
  </div>
  <div class="col-auto">
      <button class="btn btn-primary" id="btnFiltrar">
        <i class="fas fa-search"></i> Buscar
      </button>
      <a class="btn btn-danger" id="btnPdf" target="_blank">
        <i class="fas fa-file-pdf"></i> PDF
      </a>
  </div>
</div>

<ul class="nav nav-tabs" id="tabsRep" role="tablist">
  <li class="nav-item">
    <a class="nav-link active" data-bs-toggle="tab" href="#tabAlq">Alquileres</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" data-bs-toggle="tab" href="#tabVen">Ventas</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" data-bs-toggle="tab" href="#tabGas">Gastos</a>
  </li>
</ul>

<div class="tab-content border border-top-0 p-3">
  <div class="tab-pane fade show active" id="tabAlq">
    <table id="tblAlq" class="table table-sm table-bordered w-100" style="background-color: #fff;"></table>
  </div>
  <div class="tab-pane fade" id="tabVen">
    <table id="tblVen" class="table table-sm table-bordered w-100" style="background-color: #fff;"></table>
  </div>
  <div class="tab-pane fade" id="tabGas">
    <table id="tblGas" class="table table-sm table-bordered w-100" style="background-color: #fff;"></table>
  </div>
</div>

<?php cargarFooter($_SESSION['tipo']); ?>
<script src="<?=base_url?>Assets/js/pages/reporte_mes.js"></script>

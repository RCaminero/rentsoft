<?php cargarHeader($_SESSION['tipo']); ?>

<button class="btn btn-outline-primary mb-2" onclick="frmGasto()">
 <i class="fas fa-plus"></i> Nuevo Gasto</button>

<div class="card"><div class="card-body">
<table id="tblGastos" class="table table-bordered nowrap" style="width:100%">
 <thead><tr><th>ID</th><th>Descripción</th><th>Monto</th><th>Fecha</th><th>Caja</th><th></th></tr></thead>
 <tbody></tbody>
</table>
</div></div>

<!-- modal -->
<div class="modal fade" id="modalGasto" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
 <div class="modal-header bg-info"><h5 id="tGasto">Nuevo Gasto</h5>
  <button class="btn-close" data-bs-dismiss="modal"></button></div>
 <form id="fGasto" onsubmit="guardarGasto(event)">
  <div class="modal-body">
    <input type="hidden" name="id" id="id">
    <label>Descripción *</label><input class="form-control" name="descripcion" id="descripcion" required>
    <label class="mt-2">Monto *</label><input type="number" step="0.01" class="form-control" name="monto" id="monto" required>
    <label class="mt-2">Fecha *</label><input type="datetime-local" class="form-control" name="fecha" id="fecha" required>
    <!-- <label class="mt-2">Categoría *</label><input class="form-control" name="categoria" id="categoria" required> -->
  </div>
  <div class="modal-footer">
    <button class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
    <button class="btn btn-primary" id="bAccion">Guardar</button>
  </div>
 </form>
</div></div></div>

<?php cargarFooter($_SESSION['tipo']); ?>
<script src="<?= base_url ?>Assets/js/pages/gastos.js"></script>

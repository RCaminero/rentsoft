<?php cargarHeader($_SESSION['tipo']); ?>

<!--  Panel de estado / botones  -->
<div id="panel-caja"></div>

<!-- Historial de cajas (opcional) -->
<div class="card mt-3">
  <div class="card-header">Historial de Cajas</div>
  <div class="card-body p-0">
    <table class="table table-bordered mb-0" id="tblHistCaja" style="width:100%">
      <thead class="table-light">
        <tr>
          <th>ID</th><th>F. Apertura</th><th>F. Cierre</th>
          <th>Inicio</th><th>Total Ventas</th><th>Total Gastos</th>
          <th>Total Alquiler</th><th>Efectivo Cierre</th><th>Acción</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>

<!-- ----------------------------------------------------------------- -->
<!-- Modal – Abrir caja -->
<div class="modal fade" id="mAbrir" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header bg-info"><h5 class="modal-title">Abrir Caja</h5></div>
    <form id="fAbrir" onsubmit="abrirCaja(event)">
      <div class="modal-body">
        <label class="form-label">Efectivo inicial *</label>
        <input type="number" step="0.01" class="form-control" name="efectivo_inicio" required>
      </div>
      <div class="modal-footer">
        <button class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-primary">Abrir</button>
      </div>
    </form>
  </div></div>
</div>

<!-- Modal – Cerrar caja -->
<div class="modal fade" id="mCerrar" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header bg-info"><h5 class="modal-title">Cerrar Caja</h5></div>
    <form id="fCerrar" onsubmit="cerrarCaja(event)">
      <div class="modal-body">
        <label class="form-label">Efectivo final *</label>
        <input type="number" step="0.01" class="form-control" name="efectivo_cierre" id="efectivo_cierre" required readonly>
        <label class="form-label mt-2">Observación</label>
        <textarea class="form-control" name="obs" rows="2"></textarea>
      </div>
      <div class="modal-footer">
        <button class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-primary">Cerrar</button>
      </div>
    </form>
  </div></div>
</div>

<?php cargarFooter($_SESSION['tipo']); ?>
<script src="<?= base_url ?>Assets/js/pages/caja.js"></script>

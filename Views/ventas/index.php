<?php cargarHeader($_SESSION['tipo']); ?>

<button class="btn btn-outline-primary mb-2" onclick="frmVenta()">
  <i class="fas fa-plus"></i> Nueva Venta
</button>

<div class="card"><div class="card-body">
<table id="tblVentas" class="table table-bordered nowrap" style="width:100%">
 <thead><tr>
  <th>ID</th><th>Cliente</th><th>Método</th><th>Total</th><th>Fecha</th><th>Caja</th><th></th>
 </tr></thead><tbody></tbody>
</table>
</div></div>

<!-- ───────────────────────────────────────────── Modal Venta ───── -->
<div class="modal fade" id="modalVenta" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content">
 <div class="modal-header bg-info"><h5 id="modalTitle">Nueva Venta</h5>
  <button class="btn-close" data-bs-dismiss="modal"></button></div>

 <form id="formVenta" onsubmit="registrarVenta(event)" autocomplete="off">
  <div class="modal-body">
   <input type="hidden" name="id" id="id">
   <!-- Datos cliente -------------------------------------------------->
   <label class="form-label">Cliente *</label>
   <input type="hidden" id="id_cli" name="id_cli">
   <input id="select_cliente" class="form-control" type="text" name="select_cliente" placeholder="Buscar Cliente" required>   <!-- Productos ----------------------------------------------------->
   <div class="row g-2 align-items-end">
     <div class="col-md-6">
       <label>Producto *</label>
       <input id="inputProd" class="form-control">
       <input type="hidden" id="id_prod">
        <!-- <small id="stockDisp"></small>    -->
     </div>
     <div class="col-md-2">
       <label>Cant *</label>
       <input id="cant" type="number" class="form-control" min="1" value="1">
     </div>
     <div class="col-md-2">
       <label>P.Unit</label>
       <input id="precio" class="form-control" readonly>
     </div>
     <div class="col-md-2 d-grid">
       <button type="button" class="btn btn-success" onclick="agregarItem()">Agregar</button>
     </div>
   </div>
   <small id="stockDisp" class="text-muted"></small>

   <!-- Tabla carrito ------------------------------------------------->
   <div class="table-responsive mt-3">
     <table class="table table-sm table-bordered" id="tblDet">
       <thead class="table-light"><tr>
         <th>Producto</th><th>Cant</th><th>P.Unit</th><th>Subtotal</th><th></th>
       </tr></thead><tbody></tbody>
     </table>
   </div>

   <!-- Totales ------------------------------------------------------->
   <div class="row mt-3 g-2">
     <div class="col-md-3">
       <label>Total bruto *</label>
       <input id="total_bruto" name="total_bruto" class="form-control" readonly>
     </div>
     <div class="col-md-3">
       <label>Descuento</label>
       <input id="descuento" name="descuento" class="form-control" value="0" oninput="recalcularTotales()">
     </div>
     <div class="col-md-3">
       <label>Impuesto</label>
       <input id="impuesto"  name="impuesto"  class="form-control" value="0" oninput="recalcularTotales()">
     </div>
     <div class="col-md-3">
       <label>Total neto *</label>
       <input id="total_neto" name="total_neto" class="form-control" readonly>
     </div>
   </div>

   <!-- Otros datos --------------------------------------------------->
   <div class="row mt-3 g-2">
     <div class="col-md-4">
       <label>Método de pago *</label>
       <select class="form-select" name="metodo_pago" id="metodo_pago" required>
         <option>Efectivo</option><option>Tarjeta</option><option>Transferencia</option>
       </select>
     </div>
     <div class="col-md-4">
       <label>Fecha *</label>
       <input type="datetime-local" name="fecha" id="fecha"
              value="<?= date('Y-m-d\TH:i'); ?>" class="form-control" required>
     </div>
   </div>

  </div>
  <div class="modal-footer">
   <button class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
   <button class="btn btn-primary" id="btnAccion">Guardar</button>
  </div>
 </form>
</div></div></div>

<?php cargarFooter($_SESSION['tipo']); ?>
<script src="<?= base_url ?>Assets/js/pages/ventas.js"></script>

<?php cargarHeader($_SESSION['tipo']); ?>

<button class="btn btn-outline-primary mb-2" onclick="frmProducto();">
  <i class="fas fa-plus"></i>
</button>

<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table id="tblProductos" class="table table-bordered table-hover display nowrap" style="width:100%;">
        <thead>
          <tr>
            <th>ID</th>
            <th>Código</th>
            <th>Nombre</th>
            <th>Categoría</th>
            <th>P. Compra</th>
            <th>P. Venta</th>
            <th>Stock</th>
            <th></th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Alta/Edición -->
<div class="modal fade" id="modalProducto" tabindex="-1">
 <div class="modal-dialog modal-lg"><div class="modal-content">
  <div class="modal-header bg-info">
   <h5 class="modal-title" id="modalTitle">Nuevo Producto</h5>
   <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
  </div>

  <form id="formProducto" onsubmit="registrarProducto(event)" autocomplete="off">
   <div class="modal-body">
    <div class="alert alert-info">Todos los campos con <span class="text-danger fw-bold">*</span> son obligatorios.</div>

    <input type="hidden" id="id" name="id">

    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Código *</label>
        <input class="form-control" id="codigo" name="codigo" required>
      </div>
      <div class="col-md-8">
        <label class="form-label">Nombre *</label>
        <input class="form-control" id="nombre" name="nombre" style="text-transform: capitalize;" required>
      </div>
      <div class="col-12">
        <label class="form-label">Descripción</label>
        <textarea class="form-control" id="descripcion" name="descripcion" rows="2"></textarea>
      </div>
      <div class="col-md-4">
        <label class="form-label">Precio compra *</label>
        <input type="number" step="0.01" class="form-control" id="precio_compra" name="precio_compra" required>
      </div>
      <div class="col-md-4">
        <label class="form-label">Precio venta *</label>
        <input type="number" step="0.01" class="form-control" id="precio_venta" name="precio_venta" required>
      </div>
      <div class="col-md-4">
        <label class="form-label">Stock *</label>
        <input type="number" class="form-control" id="stock_actual" name="stock_actual" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Categoría *</label>
        <select class="form-select" id="id_categoria" name="id_categoria" required>
          <?php foreach ($data['categorias'] as $cat) { ?>
            <option value="<?= $cat['id']; ?>"><?= $cat['categoria']; ?></option>
          <?php } ?>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Estado</label>
        <select class="form-select" id="estado" name="estado">
          <option value="1">Activo</option>
          <option value="0">Inactivo</option>
        </select>
      </div>
    </div>
   </div>

   <div class="modal-footer">
    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Cancelar</button>
    <button type="submit" class="btn btn-outline-primary" id="btnAccion">Guardar</button>
   </div>
  </form>
 </div></div>
</div>

<?php cargarFooter($_SESSION['tipo']); ?>
<script src="<?php echo base_url.'Assets/js/pages/productos.js'; ?>"></script>

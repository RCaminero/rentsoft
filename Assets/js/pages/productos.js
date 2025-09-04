let modalProducto = new bootstrap.Modal(document.getElementById('modalProducto'));
let tblProductos;

document.addEventListener('DOMContentLoaded', () => {
  /* === DataTable ==================================================== */
  tblProductos = $('#tblProductos').DataTable({
    responsive: true,
    serverSide: false,
    ajax: {
      url: base_url + 'productos/listar',
      dataSrc: ''
    },
    columns: [
      { data: 'id' },
      { data: 'codigo' },
      { data: 'nombre' },
      { data: 'categoria' },
      { 
        data: 'precio_compra',
        render: function(data) {
          return '$' + parseFloat(data).toFixed(2);
        }
      },
      { 
        data: 'precio_venta',
        render: function(data) {
          return '$' + parseFloat(data).toFixed(2);
        }
      },
      { data: 'stock_actual' },
      { 
        data: 'acciones',
        orderable: false,
        searchable: false
      }
    ],
    language: { 
      url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json" 
    },
    dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
         "<'row'<'col-sm-12'tr>>" +
         "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    buttons: [],
    bDestroy: true,
    pageLength: 10,
    order: [[0, "desc"]]
  });
});

/* === Abrir modal de alta === */
function frmProducto() {
  document.getElementById('modalTitle').textContent = 'Nuevo Producto';
  document.getElementById('btnAccion').textContent = 'Registrar';
  document.getElementById('formProducto').reset();
  document.getElementById('id').value = '';
  modalProducto.show();
}

/* === Registrar / actualizar === */
function registrarProducto(e) {
  e.preventDefault();
  const frm = document.getElementById('formProducto');
  const url = base_url + 'productos/registrar';
  
  // Validación básica
  if (!frm.nombre.value || !frm.codigo.value) {
    alertify.error('Nombre y código son campos requeridos');
    return;
  }

  const http = new XMLHttpRequest();
  http.open('POST', url, true);
  http.send(new FormData(frm));

  http.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);
      if (res.icono == 'success') {
        alertify.success(res.msg);
        modalProducto.hide();
        tblProductos.ajax.reload();
      } else {
        alertify.error(res.msg);
      }
    }
  }
}

/* === Editar === */
function editProd(id) {
  document.getElementById('modalTitle').textContent = 'Editar Producto';
  document.getElementById('btnAccion').textContent = 'Actualizar';
  const url = base_url + 'productos/editar/' + id;
  const http = new XMLHttpRequest();
  http.open('GET', url, true);
  http.send();
  http.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);
      for (const key in res) {
        if (document.getElementById(key)) {
          document.getElementById(key).value = res[key];
        }
      }
      modalProducto.show();
    }
  }
}

/* === Eliminar === */
function delProd(id) {
  Swal.fire({
    title: '¿Está seguro de eliminar?',
    text: "El producto no se eliminará permanentemente, solo cambiará a estado inactivo",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      const url = base_url + 'productos/eliminar/' + id;
      const http = new XMLHttpRequest();
      http.open('GET', url, true);
      http.send();
      http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          const res = JSON.parse(this.responseText);
          if (res.icono == 'success') {
            alertify.success(res.msg);
            tblProductos.ajax.reload();
          } else {
            alertify.error(res.msg);
          }
        }
      }
    }
  })
}
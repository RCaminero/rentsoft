/* ------------------------------------------------------------ */
/*  ventas.js – lista, carrito y envío al backend                */
/* ------------------------------------------------------------ */

let modalVenta = new bootstrap.Modal(document.getElementById('modalVenta'));
let tblVentas, tblDet;

document.addEventListener('DOMContentLoaded',()=>{

  /* --- DataTable principal ---------------------------------- */
  tblVentas = $('#tblVentas').DataTable({
    ajax:{ url: base_url+'ventas/listar', dataSrc:'' },
    columns:[
      {data:'id'},
      {data:'cliente'},
      {data:'metodo_pago'},
      {data:'total_neto',render:d=>'$'+parseFloat(d).toFixed(2)},
      {data:'fecha'},
      {data:'caja'},
      {data:'acciones',orderable:false}
    ],
    language:{ url:'//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json' },
    order:[[0,'desc']]
  });

  /* --- Autocomplete CLIENTE --------------------------------- */
  $("#select_cliente").autocomplete({
        minLength: 2,
        source: function (request, response) {
            $.ajax({
                url: base_url + 'clientes/buscarCliente/',
                dataType: "json",
                data: {
                    cli: request.term
                },
                success: function (data) {
                    response(data);
                }
            });
        },
        select: function (event, ui) {
            document.getElementById('id_cli').value = ui.item.id;
            document.getElementById('select_cliente').value = ui.item.nombre;
        }
    })

  /* --- Autocomplete PRODUCTO -------------------------------- */

  $('#inputProd').autocomplete({
  minLength: 2,
  source: function (request, response) {
    $.ajax({
      url: base_url + 'ventas/buscarProducto',
      dataType: 'json',
      data: { prod: request.term },
      success: function (data) {
        response(data);
      }
    });
  },
  select: function (event, ui) {
    $('#id_prod').val(ui.item.id);
    $('#inputProd').val(ui.item.value);
    $('#precio').val(ui.item.precio);
    $('#stockDisp').text(ui.item.stock + ' ud. disp.');
    $('#cant').attr('max', ui.item.stock);
    return false; // ← previene que jQuery UI sobrescriba el campo
  }
});


});

/* ---------- Formulario modal -------------------------------- */
function frmVenta(){
  document.getElementById('modalTitle').textContent='Nueva Venta';
  document.getElementById('btnAccion').textContent='Registrar';
  document.getElementById('formVenta').reset();
  document.querySelector('#tblDet tbody').innerHTML='';
  recalcularTotales();
  modalVenta.show();
}

/* ---------- Agregar producto -------------------------------- */
function agregarItem(){
  const id   = $('#id_prod').val();
  const nom  = $('#inputProd').val();
  const cant = parseInt($('#cant').val());
  const pre  = parseFloat($('#precio').val());

  if(!id||!nom||cant<=0||!pre){ alertify.error('Datos incompletos'); return; }

  const sub = cant*pre;

  $('#tblDet tbody').append(`
    <tr data-id="${id}">
      <td>${nom}</td>
      <td>${cant}</td>
      <td>${pre.toFixed(2)}</td>
      <td>${sub.toFixed(2)}</td>
      <td><button class="btn btn-sm btn-danger" onclick="quitar(this)">x</button></td>
    </tr>`);
  limpiarProd();
  recalcularTotales();
}

function quitar(btn){
  $(btn).closest('tr').remove();
  recalcularTotales();
}

function limpiarProd(){
  $('#inputProd,#id_prod,#precio').val('');
  $('#cant').val(1);
  $('#stockDisp').text('');
}

/* ---------- Totales ----------------------------------------- */
function recalcularTotales(){
  let bruto=0;
  $('#tblDet tbody tr').each(function(){
    bruto += parseFloat($(this).find('td').eq(3).text());
  });
  $('#total_bruto').val(bruto.toFixed(2));

  const desc = parseFloat($('#descuento').val()||0);
  const imp  = parseFloat($('#impuesto').val()||0);
  const neto = bruto - desc + imp;
  $('#total_neto').val(neto.toFixed(2));
}

/* ---------- Enviar venta ------------------------------------ */
function registrarVenta(e){
  e.preventDefault();

  const items=[];
  $('#tblDet tbody tr').each(function(){
     items.push({
       id_producto: $(this).data('id'),
       cantidad   : parseInt($(this).find('td').eq(1).text()),
       precio_unit: parseFloat($(this).find('td').eq(2).text()),
       subtotal   : parseFloat($(this).find('td').eq(3).text())
     });
  });
  if(items.length===0){ alertify.error('Agregue productos'); return; }

  const form = new FormData(e.target);
  form.append('carrito',JSON.stringify(items));

  fetch(base_url+'ventas/registrar',{method:'POST',body:form})
   .then(r=>r.json()).then(res=>{
      res.icono==='success'
        ? alertify.success(res.msg)
        : alertify.error(res.msg);
      if(res.icono==='success'){
        modalVenta.hide(); tblVentas.ajax.reload();
        window.open(base_url+'ventas/pdfFactura/'+res.id,'_blank');
      }
   });
}

/* ---------- Editar (solo cabecera) / Eliminar ----------------
   Si necesitas editar también detalle, deberás traerlo y
   reconstruir la tabla 'tblDet'. Se deja como tarea opcional. */
function editVenta(id){ /* ... igual que antes ... */ }
function delVenta(id){  /* ... igual que antes ... */ }

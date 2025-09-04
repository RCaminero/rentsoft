/* ------------------------------------------------------------
   reporte_mes.js – filtro mes + 3 DataTables y botón PDF
-------------------------------------------------------------*/
let tblAlq, tblVen, tblGas;

document.addEventListener('DOMContentLoaded', () => {

  /* --- filtrar / recargar --------------------------------- */
  document.getElementById('btnFiltrar').addEventListener('click', loadData);

  /* --- Botón PDF ------------------------------------------ */
  document.getElementById('btnPdf').addEventListener('click', function () {
    const [anio, mes] = document.getElementById('mesSel').value.split('-');
    this.href = `${base_url}reportemes/pdf/${mes}/${anio}`;   // ← enlace correcto
  });

  /* 1ª carga (mes actual) */
  loadData();
});

/* ------------  trae datos del backend y rellena tablas  ------------ */
function loadData() {
  const [anio, mes] = document.getElementById('mesSel').value.split('-');

  fetch(`${base_url}reportemes/listar/${mes}/${anio}`)
    .then(r => r.json())
    .then(d => {
      /* OJO:  d.alquileres ‑‑> plural */
      renderTable('#tblAlq', d.alquileres, [
        { title: 'ID',        data: 'id' },
        { title: 'Fecha',     data: 'fecha' },
        { title: 'Cliente',   data: 'cliente' },
        { title: 'Vehículo',  data: 'vehiculo' },
        { title: 'Cant',      data: 'cantidad', className: 'text-end' },
        { title: 'Pagado',    data: 'abono',    className: 'text-end',
                              render: x => '$' + parseFloat(x).toFixed(2) },
        { title: 'Total',     data: 'total',    className: 'text-end',
                              render: x => '$' + parseFloat(x).toFixed(2) }
      ]);

      renderTable('#tblVen', d.ventas, [
        { title: 'ID',       data: 'id' },
        { title: 'Fecha',    data: 'fecha' },
        { title: 'Cliente',  data: 'cliente' },
        { title: 'Total',    data: 'total_neto', className: 'text-end',
                             render: x => '$' + parseFloat(x).toFixed(2) }
      ]);

      renderTable('#tblGas', d.gastos, [
        { title: 'ID',           data: 'id' },
        { title: 'Fecha',        data: 'fecha' },
        { title: 'Descripción',  data: 'descripcion' },
        { title: 'Monto',        data: 'monto', className: 'text-end',
                                  render: x => '$' + parseFloat(x).toFixed(2) },
      ]);
    });
}

/* ------------  helper: crea DataTable o lo recarga  ---------------- */
function renderTable(selector, data, columns) {
  if ($.fn.DataTable.isDataTable(selector)) {
    $(selector).DataTable().clear().rows.add(data).draw();
  } else {
    $(selector).DataTable({
      data,
      columns,
      paging: false,
      searching: false,
      info: false,
      language: {
        url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
      }
    });
  }
}

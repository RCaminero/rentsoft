let tblAlq, tblVen, tblGas;

document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('btnFiltrar').addEventListener('click', loadData);

  document.getElementById('btnPdf').addEventListener('click', function () {
    const ini = document.getElementById('fechaIni').value;
    const fin = document.getElementById('fechaFin').value;
    this.href = `${base_url}reportesemana/pdf/${ini},${fin}`;
  });

  // Primera carga
  loadData();
});

function loadData() {
  const ini = document.getElementById('fechaIni').value;
  const fin = document.getElementById('fechaFin').value;

  fetch(`${base_url}reportesemana/listar/${ini},${fin}`)
    .then(r => r.json())
    .then(d => {
      renderTable('#tblAlq', d.alquileres, [
        { title: 'ID',        data: 'id' },
        { title: 'Fecha',     data: 'fecha' },
        { title: 'Cliente',   data: 'cliente' },
        { title: 'Vehículo',  data: 'vehiculo' },
        { title: 'Cant',      data: 'cantidad', className: 'text-end' },
        { title: 'Pagado',    data: 'abono', className: 'text-end',
                              render: x => '$' + parseFloat(x).toFixed(2) },
        { title: 'Total',     data: 'total', className: 'text-end',
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
        { title: 'ID',          data: 'id' },
        { title: 'Fecha',       data: 'fecha' },
        { title: 'Descripción', data: 'descripcion' },
        { title: 'Monto',       data: 'monto', className: 'text-end',
                                render: x => '$' + parseFloat(x).toFixed(2) }
      ]);
    });
}

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

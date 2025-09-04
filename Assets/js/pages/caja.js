/* -------------------------------------------------------------- */
/*  caja.js – estado, apertura y cierre de caja con cálculos      */
/* -------------------------------------------------------------- */

const panel  = document.getElementById('panel-caja');
const mAbrir = new bootstrap.Modal(document.getElementById('mAbrir'));
const mCerrar= new bootstrap.Modal(document.getElementById('mCerrar'));

let tblHist;

document.addEventListener('DOMContentLoaded', () => {
  estadoCaja();         // pinta el panel principal
  cargarHistorial();    // tabla con historial
});

  function prepararCerrarCaja() {
  fetch(base_url + 'caja/estado')
    .then(r => r.json())
    .then(caja => {
      if (!caja || Object.keys(caja).length === 0) {
        alertify.error('No hay caja abierta para cerrar');
        mCerrar.hide();
        return;
      }

      /*  Si el backend ya mandó 'efectivo_cierre_calc', úsalo;
          si no, recalcúlalo por si acaso                                       */
      const efectivoCierre = caja.efectivo_cierre_calc !== undefined
        ? parseFloat(caja.efectivo_cierre_calc)
        : (parseFloat(caja.efectivo_inicio || 0)
          + parseFloat(caja.total_alquiler || 0)
          + parseFloat(caja.total_ventas   || 0)
          - parseFloat(caja.total_gastos   || 0));

      document.getElementById('efectivo_cierre').value =
        efectivoCierre.toFixed(2);
    })
    .catch(err => {
      console.error('Error al calcular efectivo cierre:', err);
      alertify.error('No se pudo calcular el efectivo de cierre');
      mCerrar.hide();
    });
}

/* ---------- Estado de caja ------------------------------------ */
function estadoCaja(){
  fetch(base_url + 'caja/estado')
  .then(r => r.json())
  .then(caja => {
    if (Object.keys(caja).length === 0) {       // sin caja abierta
      panel.innerHTML = `
        <div class="alert alert-warning d-flex justify-content-between align-items-center">
          <div><i class="fas fa-cash-register me-2"></i>No hay caja abierta</div>
          <button class="btn btn-primary btn-sm" onclick="mAbrir.show()">Abrir Caja</button>
        </div>`;
    } else {
      panel.innerHTML = `
        <div class="alert alert-success">
          <h5>Caja #${caja.id} abierta el ${caja.fecha_apertura} ${caja.hora_apertura}</h5>
          <p class="mb-1">Efectivo inicial: <b>$${parseFloat(caja.efectivo_inicio).toFixed(2)}</b></p>
          <button class="btn btn-danger btn-sm" onclick="prepararCerrarCaja(); mCerrar.show();">Cerrar Caja</button>
        </div>`;
    }
  });
}

/* ---------- Abrir caja ---------------------------------------- */
function abrirCaja(e){
  e.preventDefault();
  fetch(base_url + 'caja/abrir',{
    method:'POST',
    body:new FormData(e.target)
  })
  .then(r=>r.json())
  .then(res=>{
    (res.icono==='success') ? alertify.success(res.msg) : alertify.error(res.msg);
    if(res.icono==='success'){ mAbrir.hide(); estadoCaja(); cargarHistorial(); }
  });
}

/* ---------- Cerrar caja --------------------------------------- */
function cerrarCaja(e){
  e.preventDefault();
  fetch(base_url + 'caja/cerrar',{
    method:'POST',
    body:new FormData(e.target)
  })
  .then(r=>r.json())
  .then(res=>{
    (res.icono==='success') ? alertify.success(res.msg) : alertify.error(res.msg);
    if(res.icono==='success'){ mCerrar.hide(); estadoCaja(); cargarHistorial(); }
  });
}

 /* Cancelar una caja cerrada ------------------------------------- */
  function cancelarCaja(id){
    Swal.fire({title:'¿Cancelar caja?',icon:'warning',showCancelButton:true})
      .then(r=>{
        if(r.isConfirmed){
          fetch(base_url+'caja/cancelar/'+id)
          .then(r=>r.json()).then(res=>{
            (res.icono==='success')?alertify.success(res.msg):alertify.error(res.msg);
            cargarHistorial();    // recarga tabla
            estadoCaja();         // refresca panel principal por si acaso
          });
        }
      });
  }
/* ---------- Historial ----------------------------------------- */
function cargarHistorial(){
  if (tblHist) { tblHist.ajax.reload(); return; }

  tblHist = $('#tblHistCaja').DataTable({
    responsive: true,
    processing: true,
    serverSide: false,
    ajax : { url: base_url + 'caja/listado', dataSrc:'' },
    columns:[
      {data:'id'},
      {data: null, render:d=>`${d.fecha_apertura} ${d.hora_apertura}`},
      {data: null, render:d=> d.fecha_cierre ? `${d.fecha_cierre} ${d.hora_cierre}` : '—'},
      {data:'efectivo_inicio',  render:d=>'$'+parseFloat(d).toFixed(2)},
      {data:'total_ventas',     render:d=>'$'+parseFloat(d).toFixed(2)},
      {data:'total_gastos',     render:d=>'$'+parseFloat(d).toFixed(2)},
      {data:'total_alquiler',   render:d=>'$'+parseFloat(d).toFixed(2)},
      {data:'efectivo_cierre',  render:d=> d? '$'+parseFloat(d).toFixed(2):'—'},
      {
      data:null, orderable:false, render:d=>{
        let cancelarBtn = (d.estado == 0)
          ? `<button class="btn btn-sm btn-danger me-1" title="Cancelar"
              onclick="cancelarCaja(${d.id})">
              <i class="fas fa-times"></i></button>`
          : '';
        let pdfBtn = `<a href="${base_url}caja/pdf/${d.id}" target="_blank" class="btn btn-sm btn-outline-danger" title="PDF">
                        <i class="fas fa-file-pdf"></i>
                      </a>`;
        return cancelarBtn + pdfBtn;
      }
    },
    //   { data: 'id', render: id => `
    //   <button class="btn btn-sm btn-outline-secondary" onclick="ticketCaja(${id})">
    //     <i class="fas fa-print"></i>
    //   </button>
    // `, orderable: false }
    ],
    language:{ url:'https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json' },
    dom,
    buttons,
    order:[[0,'asc']],
    resonsieve: true,
        bDestroy: true,
        iDisplayLength: 10,
        order: [
            [0, "desc"]
        ]
  });




// Modificar el botón que abre el modal para llamar esta función
// En caja.js, cambia el evento onclick en estadoCaja()

// Donde dice:
// <button class="btn btn-danger btn-sm" onclick="mCerrar.show()">Cerrar Caja</button>
// Cambiar a:
// <button class="btn btn-danger btn-sm" onclick="prepararCerrarCaja(); mCerrar.show();">Cerrar Caja</button>


}

function ticketCaja(id) {
  const url = base_url + 'caja/pdf/' + id;
  window.open(url, '_blank');
}


let mg = new bootstrap.Modal(document.getElementById('modalGasto'));
let tblGastos;

document.addEventListener('DOMContentLoaded',()=>{
  tblGastos=$('#tblGastos').DataTable({
    ajax:{url:base_url+'gastos/listar',dataSrc:''},
    columns:[
      {data:'id'},{data:'descripcion'},
      {data:'monto',render:d=>'$'+parseFloat(d).toFixed(2)},
      {data:'fecha'},{data:'caja'},
      {data:null,orderable:false,render:d=>`
         <button class="btn btn-sm btn-primary" onclick="editGasto(${d.id})"><i class="fas fa-edit"></i></button>
         <button class="btn btn-sm btn-danger" onclick="delGasto(${d.id})"><i class="fas fa-trash"></i></button>`}],
    language:{url:'https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'}
  });
});

function frmGasto(){
  document.getElementById('tGasto').textContent='Nuevo Gasto';
  document.getElementById('bAccion').textContent='Registrar';
  document.getElementById('fGasto').reset(); document.getElementById('id').value='';
  mg.show();
}
function guardarGasto(e){
  e.preventDefault();
  fetch(base_url+'gastos/registrar',{method:'POST',body:new FormData(e.target)})
  .then(r=>r.json()).then(res=>{
    (res.icono==='success')?alertify.success(res.msg):alertify.error(res.msg);
    if(res.icono==='success'){ mg.hide(); tblGastos.ajax.reload(); }
  });
}
function editGasto(id){
  fetch(base_url+'gastos/editar/'+id).then(r=>r.json()).then(d=>{
    for(const k in d){ if(document.getElementById(k)){ document.getElementById(k).value=d[k]; }}
    document.getElementById('tGasto').textContent='Editar Gasto';
    document.getElementById('bAccion').textContent='Actualizar';
    mg.show();
  });
}
function delGasto(id){
  Swal.fire({title:'¿Eliminar?',showCancelButton:true}).then(res=>{
    if(res.isConfirmed){
      fetch(base_url+'gastos/eliminar/'+id).then(r=>r.json()).then(d=>{
        (d.icono==='success')?alertify.success(d.msg):alertify.error(d.msg);
        tblGastos.ajax.reload();
      });
    }
  });
}

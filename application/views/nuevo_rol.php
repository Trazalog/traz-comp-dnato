 
<br>
<a href="#nuevo-rol" onclick="$('#nuevo-rol').show()"><i class="fa fa-plus"></i> Nuevo Rol</a> 

<hr>
<div id="nuevo-rol" class="col-lg-4 col-lg-offset-4" style="display:none;">
    <h2>Nuevo Rol de Sistema</h2>
    <div class="form-group">
        <input id="nombre" class="form-control" placeholder="Nombre del Rol">
    </div>

    <div class="form-group">
        <textarea id="descripcion" class="form-control" placeholder="Descripción de Rol"></textarea>
    </div>

    <button class="btn btn-lg btn-primary btn-block" onclick="guardarRol()"><i class="fa fa-plus"></i>  Guardar</button>
</div>

<script>


function guardarRol(){
  var nombre = $('#nombre').val();
  var descripcion = $('#descripcion').val();
  $.ajax({
          type:'POST',
          dataType:'JSON',
          url:'<?php echo base_url() ?>/Rol/guardar',
          data:{nombre, descripcion},
          success:function(rsp){
              alert('Hecho');
              obtenerRoles();
              $('#nombre').val(null);
              $('#descripcion').val(null);
              $('#nuevo-rol').hide();
          },
          error: function(rsp){  
              alert('Error');
          }
      });
}
</script>
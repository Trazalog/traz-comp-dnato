<div class="container">
    <h2><?php echo $title; ?></h2>
    <table class="table table-hover table-bordered table-striped">
        <tr>
            <th>id</th>
            <th>Nombre</th>
            <th>CUIT</th>
            <th>Descripción</th>
            <th>Teléfono</th>
            <th>Email</th>
            <th>País</th>
            <th>Estado</th>
            <th>Localidad</th>
            <th colspan="2">Acciones</th>
        </tr>
            <?php
                foreach($lista_empresas as $row){                                               
                    echo '<tr>';
                    echo '<td>'.$row->empr_id.'</td>';
                    echo '<td>'.$row->nombre.'</td>';
                    echo '<td>'.$row->cuit.'</td>';
                    echo '<td>'.$row->descripcion.'</td>';
                    echo '<td>'.$row->telefono.'</td>';
                    echo '<td>'.$row->email.'</td>';
                    echo '<td>'.$row->pais_id.'</td>';
                    echo '<td>'.$row->estado.'</td>';
                    echo '<td>'.$row->localidad.'</td>';
                    echo '<td>
                        <a href="'.site_url().'empresa/borrarEmpresa/'.$row->empr_id.'"><button type="button" class="btn btn-danger">Borrar</button></a>';
                    echo '</td>';
                }
            ?>
    </table>
</div>
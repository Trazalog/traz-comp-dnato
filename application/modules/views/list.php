<div class="box">
    <div class="box-header">
        <h3 class="box-title">Lista Formularios</h3>
    </div>
    <div class="box-body">
        <!-- <button class="btn btn-primary frm-new-modal" data-form="1">Nuevo Form</button><br> -->
        <table class="table-striped table-hover table">
            <tbody>
                <?php 
                foreach ($list as $key => $o) {
                        echo "<tr>";
                        echo "<td>$o->nombre</td>";
                        echo "<td>$o->fecha</td>";
                        echo "<td><button class='btn btn-primary frm-open' data-info='$o->info_id'><i class='fa fa-paperclip'></i></button></td>";
                        echo "</tr>";
                    }
                ?>
            </tbody>
        </table>
    </div>
</div>

<div class="box">
    <div class="box-header">
        <h3 class="box-title">Nuevo Formulario</h3>
    </div>
    <div class="box-body frm-new" data-form="1">

    </div>
</div>

<script>
detectarForm()
</script>
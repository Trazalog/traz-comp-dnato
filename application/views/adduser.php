<div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
    <div class="form-card">
        <h2 class="form-title">Nuevo Usuario</h2>
        <p class="form-subtitle">Por favor ingrese la información requerida a continuación.</p>
        <p class="form-required-legend"><span class="required">*</span> Campos obligatorios</p>

        <?php
        $fattr = array('class' => 'form-signin', 'enctype' => 'multipart/form-data');
        echo form_open('/main/adduser', $fattr);
        ?>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="firstname" class="control-label">Nombre <span class="required" aria-hidden="true">*</span></label>
                    <?php echo form_input(array('name' => 'firstname', 'id' => 'firstname', 'placeholder' => 'Nombre', 'class' => 'form-control', 'value' => set_value('firstname'))); ?>
                    <?php echo form_error('firstname'); ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="lastname" class="control-label">Apellido <span class="required" aria-hidden="true">*</span></label>
                    <?php echo form_input(array('name' => 'lastname', 'id' => 'lastname', 'placeholder' => 'Apellido', 'class' => 'form-control', 'value' => set_value('lastname'))); ?>
                    <?php echo form_error('lastname'); ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="email" class="control-label">Email <span class="required" aria-hidden="true">*</span></label>
                    <?php echo form_input(array('name' => 'email', 'id' => 'email', 'placeholder' => 'Email', 'class' => 'form-control', 'value' => set_value('email'), 'type' => 'email')); ?>
                    <?php echo form_error('email'); ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="image" class="control-label">Foto de perfil</label>
                    <div class="file-input-wrap">
                        <span class="file-input-hidden-wrap">
                            <input type="file" name="image" accept="image/*" id="image" class="file-input-hidden" style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0;font-size:0;" tabindex="-1" />
                        </span>
                        <label for="image" class="file-input-btn" title="Seleccionar archivo" aria-label="Seleccionar imagen de perfil"><i class="fa fa-folder-open-o" aria-hidden="true"></i></label>
                        <span class="file-input-name" id="image-filename">Ningún archivo</span>
                    </div>
                    <div id="image-preview-wrap" class="profile-photo-preview-wrap" aria-hidden="true"></div>
                    <?php echo form_error('image'); ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="telefono" class="control-label">Teléfono</label>
                    <?php echo form_input(array('name' => 'telefono', 'id' => 'telefono', 'placeholder' => 'Teléfono', 'class' => 'form-control', 'value' => set_value('telefono'))); ?>
                    <?php echo form_error('telefono'); ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="dni" class="control-label">D.N.I</label>
                    <?php echo form_input(array('name' => 'dni', 'id' => 'dni', 'placeholder' => 'D.N.I', 'class' => 'form-control', 'value' => set_value('dni'))); ?>
                    <?php echo form_error('dni'); ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="business" class="control-label">Empresa <span class="required" aria-hidden="true">*</span></label>
                    <select class="form-control" name="business" id="business">
                        <option value="">Seleccione empresa...</option>
                        <?php
                        foreach ($emp_connect as $emp_con) {
                            foreach ($groups as $group) {
                                list($id_group, $group_name) = explode("-", $group->name);
                                if ($id_group && $group_name) {
                                    if ($emp_con->group === $group_name) {
                                        echo '<option value="' . htmlspecialchars($emp_con->group) . '"' . (set_value('business') === $emp_con->group ? ' selected' : '') . '>' . htmlspecialchars($group->displayName) . '</option>';
                                        break;
                                    }
                                }
                            }
                        }
                        ?>
                    </select>
                    <?php echo form_error('business'); ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="role" class="control-label">Rol <span class="required" aria-hidden="true">*</span></label>
                    <?php echo form_dropdown('role', $dd_list, set_value('role'), 'class="form-control" id="role"'); ?>
                    <?php echo form_error('role'); ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="password" class="control-label">Password <span class="required" aria-hidden="true">*</span></label>
                    <?php echo form_password(array('name' => 'password', 'id' => 'password', 'placeholder' => 'Password', 'class' => 'form-control')); ?>
                    <?php echo form_error('password'); ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="passconf" class="control-label">Confirme Password <span class="required" aria-hidden="true">*</span></label>
                    <?php echo form_password(array('name' => 'passconf', 'id' => 'passconf', 'placeholder' => 'Confirme Password', 'class' => 'form-control')); ?>
                    <?php echo form_error('passconf'); ?>
                </div>
            </div>
        </div>

        <?php echo form_submit(array('value' => 'Guardar', 'class' => 'btn btn-primary btn-submit btn-block')); ?>
        <?php echo form_close(); ?>
    </div>
</div>

<script>
(function() {
    var input = document.getElementById('image');
    var wrap = document.getElementById('image-preview-wrap');
    var filenameEl = document.getElementById('image-filename');
    if (!input || !wrap) return;

    input.addEventListener('change', function() {
        var file = this.files && this.files[0];
        if (filenameEl) {
            filenameEl.textContent = file ? file.name : 'Ningún archivo';
        }
        if (!file || !file.type.match(/^image\//)) {
            while (wrap.firstChild) wrap.removeChild(wrap.firstChild);
            wrap.setAttribute('aria-hidden', 'true');
            wrap.classList.remove('profile-photo-preview-wrap--visible');
            return;
        }
        var reader = new FileReader();
        reader.onload = function(e) {
            while (wrap.firstChild) wrap.removeChild(wrap.firstChild);
            var img = document.createElement('img');
            img.className = 'profile-photo-preview';
            img.alt = 'Vista previa de la foto de perfil';
            img.src = e.target.result;
            wrap.appendChild(img);
            wrap.removeAttribute('aria-hidden');
            wrap.classList.add('profile-photo-preview-wrap--visible');
        };
        reader.readAsDataURL(file);
    });
})();
</script>

# 📋 Documentación: Formularios Dinámicos con traz-comp-formularios

## 📖 Índice
1. [Introducción](#introducción)
2. [Configuración Base](#configuración-base)
3. [Patrones de Uso](#patrones-de-uso)
4. [Casos de Uso Específicos](#casos-de-uso-específicos)
5. [Flujos Completos](#flujos-completos)
6. [Funciones Helper](#funciones-helper)
7. [Ejemplos Prácticos](#ejemplos-prácticos)
8. [Troubleshooting](#troubleshooting)

---

## 🎯 Introducción

El módulo `traz-comp-formularios` es un sistema de formularios dinámicos que permite:
- Crear formularios desde la base de datos
- Generar instancias de formularios
- Recopilar y almacenar respuestas
- Validar datos dinámicamente
- Renderizar formularios HTML automáticamente

### Estructura de Base de Datos
- `frm.formularios` - Plantillas de formularios
- `frm.items` - Campos de cada formulario
- `frm.instancias_formularios` - Respuestas/instancias de formularios
- `core.tablas` - Valores de lookup para select/radio/checkboxes

---

## ⚙️ Configuración Base

### 1. Constants.php
```php
define('FRM', 'traz-comp-formularios/');
define('FORMULARIO_REGISTRO_ID', 72); // ID del formulario específico
```

### 2. Autoload.php
```php
$autoload['helper'] = array('url', 'form', 'security', 'sesion', 'image','gitv', FRM.'form');
```

### 3. Submodule Git
```bash
git submodule add https://github.com/Trazalog/traz-comp-formularios.git application/modules/traz-comp-formularios
```

---

## 🔄 Patrones de Uso

### Patrón A: Crear Nueva Instancia + Renderizar
**Cuándo usar:** Formularios nuevos que el usuario debe completar

```php
// En el controlador
$this->load->model(FRM . 'Forms');
$instancia = $this->Forms->generarInstancia($form_id);
$info_id = $instancia['info_id'];

// En la vista
<?php echo nuevoForm($form_id); ?>
```

### Patrón B: Obtener Formulario Existente
**Cuándo usar:** Mostrar formularios ya completados o para edición

```php
// En el controlador
$this->load->model(FRM . 'Forms');
$res = $this->Forms->obtener($info_id);

// En la vista
$formulario = getForm($info_id);
echo $formulario;
```

### Patrón C: Actualizar Formulario Existente
**Cuándo usar:** Guardar cambios en formularios ya creados

```php
$this->load->model(FRM . 'Forms');
$this->Forms->actualizar($info_id, $form_data);
```

### Patrón D: Crear y Guardar en un Paso
**Cuándo usar:** Formularios simples con datos iniciales

```php
$this->load->model(FRM . 'Forms');
$info_id = intval($this->Forms->guardar($form_id, $data));
```

---

## 🎯 Casos de Uso Específicos

### 1. Registro de Usuario (Nuevo Usuario)
**Flujo:** Usuario se registra → Establece password → Completa formulario adicional

```php
// En complete() - después de establecer password
$this->load->model(FRM . 'Forms');
$instancia = $this->Forms->generarInstancia(FORMULARIO_REGISTRO_ID);
$info_id = $instancia['info_id'];

// Guardar en sesión
$this->session->set_userdata('temp_user_id', $userInfo->id);
$this->session->set_userdata('temp_info_id', $info_id);

// En la vista
<?php echo nuevoForm(FORMULARIO_REGISTRO_ID); ?>
```

### 2. Formularios de Tareas BPM
**Flujo:** Tarea BPM → Formulario asociado → Guardar respuestas

```php
// En el controlador de tareas
$this->load->model(FRM . 'Forms');
$res = $this->Forms->obtener($info_id);

// En la vista
$formulario = getForm($info_id);
echo $formulario;
```

### 3. Formularios de Inspección
**Flujo:** Inspección → Formulario de datos → Imágenes y archivos

```php
// Obtener formulario con archivos
$this->load->model(FRM . 'Forms');
$res = $this->Forms->obtener($info_id);

foreach ($res->items as $dato) {
    if (isset($dato->valor4_base64)) {
        // Procesar archivos/imágenes
    }
}
```

---

## 🔄 Flujos Completos

### Flujo 1: Formulario Nuevo (Registro)
```
1. generarInstancia($form_id) → retorna info_id
2. nuevoForm($form_id) → crea instancia + renderiza HTML
3. Usuario completa formulario
4. actualizar($info_id, $data) → guarda respuestas
5. Asociar info_id con usuario/entidad
```

### Flujo 2: Formulario Existente (Edición)
```
1. obtener($info_id) → obtiene datos existentes
2. getForm($info_id) → renderiza HTML con datos
3. Usuario modifica formulario
4. actualizar($info_id, $data) → guarda cambios
```

### Flujo 3: Formulario Dinámico (AJAX)
```
1. nuevoForm($form_id) → crea instancia + renderiza
2. JavaScript extrae info_id del HTML
3. AJAX envía datos + info_id
4. actualizar($info_id, $data) → guarda respuestas
```

---

## 🛠️ Funciones Helper

### nuevoForm($form_id)
**Propósito:** Crear nueva instancia y renderizar formulario
**Retorna:** HTML del formulario con data-info="info_id"
**Uso:** `<?php echo nuevoForm($form_id); ?>`

### getForm($info_id)
**Propósito:** Obtener formulario existente y renderizarlo
**Retorna:** HTML del formulario con datos cargados
**Uso:** `echo getForm($info_id);`

### form($data, $modal)
**Propósito:** Renderizar HTML del formulario
**Parámetros:** 
- `$data` - Datos del formulario
- `$modal` - Si es modal (true/false)
**Uso:** Interno de las funciones anteriores

---

## 💡 Ejemplos Prácticos

### Ejemplo 1: Formulario de Registro
```php
// Main.php - complete()
public function complete() {
    // ... validación de password ...
    
    // Crear instancia del formulario
    $this->load->model(FRM . 'Forms');
    $instancia = $this->Forms->generarInstancia(FORMULARIO_REGISTRO_ID);
    $info_id = $instancia['info_id'];
    
    // Guardar en sesión
    $this->session->set_userdata('temp_user_id', $userInfo->id);
    $this->session->set_userdata('temp_info_id', $info_id);
    
    // Mostrar formulario
    $data['form_id'] = FORMULARIO_REGISTRO_ID;
    $this->load->view('formulario_registro', $data);
}

// formulario_registro.php
<?php echo nuevoForm($form_id); ?>

<script>
$('form').on('submit', function(e) {
    e.preventDefault();
    var info_id = $('.frm').attr('data-info');
    var formData = new FormData(this);
    formData.append('info_id', info_id);
    
    $.ajax({
        type: 'POST',
        url: '<?php echo base_url(); ?>main/guardarFormularioRegistro',
        data: formData,
        success: function(response) {
            if (response.success) {
                window.location.href = '<?php echo base_url(); ?>register/register_success';
            }
        }
    });
});
</script>
```

### Ejemplo 2: Formulario de Tarea BPM
```php
// Pedidotrabajo.php
public function cargar_formulario_asociado() {
    $info_id = $_GET['info_id'];
    $formulario = getForm($info_id);
    echo $formulario;
}

// tbl_formularios_pedido.php
function verForm(e) {
    info_id = $(e).closest('tr').attr('id');
    var url = "<?php echo base_url(BPM); ?>Pedidotrabajo/cargar_formulario_asociado?info_id="+info_id;
    $("#form-dinamico").load(url);
}
```

### Ejemplo 3: Formulario con Archivos
```php
// Reportes.php
function getFormEscaneoDocu($info_id) {
    $this->load->model(FRM . 'Forms');
    $res = $this->Forms->obtener($info_id);
    
    foreach ($res->items as $dato) {
        if (isset($dato->valor4_base64)) {
            $rec = stream_get_contents($dato->valor4_base64);
            $ext = obtenerExtension($dato->valor);
            
            if ($dato->tipo_dato == 'image') {
                $formEscaneo['imagenes'][$key]['imagen'] = $ext . $rec;
            }
        }
    }
    return $formEscaneo;
}
```

---

## 🔧 Troubleshooting

### Error: "Unable to load the requested file: helpers/traz-comp-formularios/form_helper.php"
**Solución:** Agregar `FRM.'form'` al autoload.php
```php
$autoload['helper'] = array('url', 'form', 'security', 'sesion', 'image','gitv', FRM.'form');
```

### Error: "500 Internal Server Error"
**Causas posibles:**
1. Helper no cargado en autoload
2. Constante FRM no definida
3. Modelo Forms no encontrado

**Solución:**
```php
// Verificar constants.php
define('FRM', 'traz-comp-formularios/');

// Verificar autoload.php
$autoload['helper'] = array(FRM.'form');

// Cargar modelo correctamente
$this->load->model(FRM . 'Forms');
```

### Error: "info_id no encontrado"
**Causa:** El formulario no se creó correctamente
**Solución:** Usar `generarInstancia()` antes de renderizar

### Error: "Loop de redirección"
**Causa:** No se está guardando correctamente el info_id
**Solución:** 
1. Crear instancia en `complete()`
2. Guardar info_id en sesión
3. Usar `actualizar()` en lugar de `guardar()`

---

## 📚 Referencias

### Archivos Clave Analizados
- `/mnt/win/dev/git/traz-tools/application/modules/traz-comp-formularios/helpers/form_helper.php`
- `/mnt/win/dev/git/traz-tools/application/modules/traz-comp-formularios/models/Forms.php`
- `/mnt/win/dev/git/traz-tools/application/modules/traz-prod-trazasoft/views/etapa/abm.php`
- `/mnt/win/dev/git/traz-tools/application/modules/traz-comp-bpm/controllers/Pedidotrabajo.php`
- `/mnt/win/dev/git/traz-tools/application/modules/ddpe-tools-pro/controllers/Reportes.php`

### Patrones Identificados
1. **Carga del modelo:** `$this->load->model(FRM . 'Forms');`
2. **Crear instancia:** `$this->Forms->generarInstancia($form_id)`
3. **Obtener formulario:** `$this->Forms->obtener($info_id)`
4. **Actualizar datos:** `$this->Forms->actualizar($info_id, $data)`
5. **Renderizar en vista:** `<?php echo nuevoForm($form_id); ?>`
6. **Obtener existente:** `echo getForm($info_id);`

---

## ✅ Checklist de Implementación

- [ ] Constante FRM definida en constants.php
- [ ] Helper FRM.'form' agregado a autoload.php
- [ ] Submodule traz-comp-formularios agregado
- [ ] Modelo Forms cargado correctamente
- [ ] Instancia creada con generarInstancia()
- [ ] info_id guardado en sesión
- [ ] Formulario renderizado con nuevoForm()
- [ ] Datos actualizados con actualizar()
- [ ] info_id asociado con usuario/entidad
- [ ] Redirección correcta después del guardado

---

*Documentación generada basada en análisis exhaustivo de traz-tools y implementación en traz-comp-dnato*



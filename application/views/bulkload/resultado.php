<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Resultado de Carga Masiva</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="apple-touch-icon" href="apple-touch-icon.png">
    
    <!--CSS-->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/cosmo/bootstrap.min.css">
    <link rel="stylesheet" href="<?= base_url('public/css/main.css') ?>">
    
    <!-- JQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h2>Resultado de Carga Masiva</h2>
                <hr>
                
                <!-- Resultado del procesamiento -->
                <div class="panel <?= $resultado['success'] ? 'panel-success' : 'panel-danger' ?>">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <i class="fa <?= $resultado['success'] ? 'fa-check-circle' : 'fa-exclamation-triangle' ?>"></i>
                            <?= $resultado['success'] ? 'Procesamiento Exitoso' : 'Error en el Procesamiento' ?>
                        </h3>
                    </div>
                    <div class="panel-body">
                        <?php if ($resultado['success']): ?>
                            <div class="alert alert-success">
                                <strong>¡Éxito!</strong> El archivo se procesó correctamente.
                            </div>
                        <?php else: ?>
                            <div class="alert alert-danger">
                                <strong>Error:</strong> Hubo problemas durante el procesamiento del archivo.
                            </div>
                        <?php endif; ?>
                        
                        <!-- Detalles del resultado -->
                        <div class="well">
                            <h4>Detalles del Procesamiento:</h4>
                            <pre style="white-space: pre-wrap; word-wrap: break-word; max-height: 400px; overflow-y: auto;"><?= htmlspecialchars($resultado['output']) ?></pre>
                        </div>
                        
                        <!-- Información adicional -->
                        <?php if (isset($resultado['total_messages']) && $resultado['total_messages'] > 0): ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Total de mensajes:</strong> <?= $resultado['total_messages'] ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Último mensaje:</strong> <?= isset($resultado['last_message_time']) ? $resultado['last_message_time'] : 'N/A' ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Acciones -->
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <a href="<?= base_url('bulkload') ?>" class="btn btn-primary">
                                    <i class="fa fa-upload"></i> Nueva Carga Masiva
                                </a>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="<?= base_url('main') ?>" class="btn btn-default">
                                    <i class="fa fa-home"></i> Volver al Inicio
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Información adicional -->
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title">Información Importante</h3>
                    </div>
                    <div class="panel-body">
                        <ul>
                            <li><strong>Procesamiento:</strong> El archivo se procesó en el sistema central</li>
                            <li><strong>Logs:</strong> Se muestran todos los mensajes de log del procesamiento</li>
                            <li><strong>Resultado:</strong> 
                                <?php if ($resultado['success']): ?>
                                    El procesamiento se completó exitosamente
                                <?php else: ?>
                                    Hubo errores durante el procesamiento que requieren atención
                                <?php endif; ?>
                            </li>
                            <li><strong>Próximos pasos:</strong> 
                                <?php if ($resultado['success']): ?>
                                    Los datos ya están disponibles en el sistema
                                <?php else: ?>
                                    Revise los errores mostrados y corrija el archivo antes de volver a cargar
                                <?php endif; ?>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="<?= base_url('public/js/main.js') ?>"></script>
    
    <script>
    $(document).ready(function() {
        console.log('Resultado de carga masiva cargado');
        console.log('Resultado:', <?= json_encode($resultado) ?>);
        
        // Auto-scroll al resultado si hay muchos mensajes
        $('.well pre').scrollTop(0);
    });
    </script>
</body>
</html>

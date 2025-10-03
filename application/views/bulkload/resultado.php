<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!-- Estilos adicionales para la página de resultado -->
<style>
    .log-message {
        margin-bottom: 5px;
        padding: 8px 12px;
        border-radius: 4px;
        border-left: 4px solid;
    }
    .log-info {
        background-color: #d9edf7;
        border-left-color: #31708f;
        color: #31708f;
    }
    .log-success {
        background-color: #dff0d8;
        border-left-color: #3c763d;
        color: #3c763d;
    }
    .log-warning {
        background-color: #fcf8e3;
        border-left-color: #8a6d3b;
        color: #8a6d3b;
    }
    .log-error {
        background-color: #f2dede;
        border-left-color: #a94442;
        color: #a94442;
        font-weight: bold;
    }
    .log-timestamp {
        font-size: 0.85em;
        color: #666;
        margin-right: 10px;
    }
    .log-level {
        font-weight: bold;
        margin-right: 8px;
    }
    .log-content {
        margin-left: 5px;
    }
    .table-responsive {
        border-radius: 6px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
    }
    .summary-card {
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    .summary-number {
        font-size: 2em;
        font-weight: bold;
        margin-bottom: 5px;
    }
    .summary-label {
        color: #666;
        font-size: 0.9em;
    }
</style>

<!-- DataTables CSS adicional -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap.min.css">

<!-- Contenido principal -->
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h2><i class="fa fa-file-text-o"></i> Resultado de Carga Masiva</h2>
                <hr>
                
                <?php
                // Parsear mensajes del stored procedure
                $parsed_messages = array();
                $summary = array(
                    'total' => 0,
                    'success' => 0,
                    'info' => 0,
                    'warning' => 0,
                    'error' => 0
                );
                
                if (isset($resultado['output']) && !empty($resultado['output'])) {
                    // Dividir por "|" y procesar cada mensaje
                    $raw_messages = explode('|', $resultado['output']);
                    
                    foreach ($raw_messages as $index => $raw_message) {
                        $raw_message = trim($raw_message);
                        if (empty($raw_message)) continue;
                        
                        $message = array(
                            'id' => $index + 1,
                            'raw' => $raw_message,
                            'level' => 'INFO',
                            'content' => $raw_message,
                            'timestamp' => '',
                            'icon' => 'fa-info-circle',
                            'class' => 'log-info'
                        );
                        
                        // Detectar nivel del mensaje
                        if (stripos($raw_message, 'SUCCESS:') === 0) {
                            $message['level'] = 'SUCCESS';
                            $message['content'] = str_replace('SUCCESS:', '', $raw_message);
                            $message['icon'] = 'fa-check-circle';
                            $message['class'] = 'log-success';
                            $summary['success']++;
                        } elseif (stripos($raw_message, 'ERROR:') === 0) {
                            $message['level'] = 'ERROR';
                            $message['content'] = str_replace('ERROR:', '', $raw_message);
                            $message['icon'] = 'fa-exclamation-triangle';
                            $message['class'] = 'log-error';
                            $summary['error']++;
                        } elseif (stripos($raw_message, 'WARNING:') === 0) {
                            $message['level'] = 'WARNING';
                            $message['content'] = str_replace('WARNING:', '', $raw_message);
                            $message['icon'] = 'fa-warning';
                            $message['class'] = 'log-warning';
                            $summary['warning']++;
                        } elseif (stripos($raw_message, 'INFO:') === 0) {
                            $message['level'] = 'INFO';
                            $message['content'] = str_replace('INFO:', '', $raw_message);
                            $message['icon'] = 'fa-info-circle';
                            $message['class'] = 'log-info';
                            $summary['info']++;
                        } else {
                            $summary['info']++;
                        }
                        
                        // Limpiar contenido
                        $message['content'] = trim($message['content']);
                        
                        // Agregar emojis según el contenido
                        $content_lower = strtolower($message['content']);
                        if (strpos($content_lower, 'iniciando') !== false || strpos($content_lower, 'inicio') !== false) {
                            $message['content'] = '🚀 ' . $message['content'];
                        } elseif (strpos($content_lower, 'completado') !== false || strpos($content_lower, 'exitoso') !== false) {
                            $message['content'] = '✅ ' . $message['content'];
                        } elseif (strpos($content_lower, 'error') !== false || strpos($content_lower, 'falló') !== false) {
                            $message['content'] = '❌ ' . $message['content'];
                        } elseif (strpos($content_lower, 'cargando') !== false || strpos($content_lower, 'procesando') !== false) {
                            $message['content'] = '⏳ ' . $message['content'];
                        } elseif (strpos($content_lower, 'validando') !== false || strpos($content_lower, 'verificando') !== false) {
                            $message['content'] = '🔍 ' . $message['content'];
                        } elseif (strpos($content_lower, 'insertando') !== false || strpos($content_lower, 'guardando') !== false) {
                            $message['content'] = '💾 ' . $message['content'];
                        } elseif (strpos($content_lower, 'eliminando') !== false || strpos($content_lower, 'limpiando') !== false) {
                            $message['content'] = '🗑️ ' . $message['content'];
                        } elseif (strpos($content_lower, 'archivo') !== false) {
                            $message['content'] = '📁 ' . $message['content'];
                        } elseif (strpos($content_lower, 'base de datos') !== false || strpos($content_lower, 'database') !== false) {
                            $message['content'] = '🗄️ ' . $message['content'];
                        } elseif (strpos($content_lower, 'registro') !== false) {
                            $message['content'] = '📝 ' . $message['content'];
                        }
                        
                        $parsed_messages[] = $message;
                        $summary['total']++;
                    }
                }
                ?>
                
                <!-- Resumen del procesamiento -->
                <div class="row">
                    <div class="col-md-2">
                        <div class="panel panel-default summary-card text-center">
                            <div class="panel-body">
                                <div class="summary-number text-primary"><?= $summary['total'] ?></div>
                                <div class="summary-label">Total Mensajes</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="panel panel-success summary-card text-center">
                            <div class="panel-body">
                                <div class="summary-number text-success"><?= $summary['success'] ?></div>
                                <div class="summary-label">Exitosos</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="panel panel-info summary-card text-center">
                            <div class="panel-body">
                                <div class="summary-number text-info"><?= $summary['info'] ?></div>
                                <div class="summary-label">Informativos</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="panel panel-warning summary-card text-center">
                            <div class="panel-body">
                                <div class="summary-number text-warning"><?= $summary['warning'] ?></div>
                                <div class="summary-label">Advertencias</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="panel panel-danger summary-card text-center">
                            <div class="panel-body">
                                <div class="summary-number text-danger"><?= $summary['error'] ?></div>
                                <div class="summary-label">Errores</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="panel <?= $resultado['success'] ? 'panel-success' : 'panel-danger' ?> summary-card text-center">
                            <div class="panel-body">
                                <div class="summary-number">
                                    <i class="fa <?= $resultado['success'] ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' ?>"></i>
                                </div>
                                <div class="summary-label"><?= $resultado['success'] ? 'Exitoso' : 'Falló' ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tabla de mensajes con DataTables -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <i class="fa fa-list"></i> Detalle de Mensajes del Procesamiento
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table id="messagesTable" class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Tipo</th>
                                        <th>Mensaje</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($parsed_messages as $message): ?>
                                    <tr>
                                        <td><?= $message['id'] ?></td>
                                        <td>
                                            <span class="label label-<?= strtolower($message['level']) == 'error' ? 'danger' : (strtolower($message['level']) == 'warning' ? 'warning' : (strtolower($message['level']) == 'success' ? 'success' : 'info')) ?>">
                                                <i class="fa <?= $message['icon'] ?>"></i> <?= $message['level'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="<?= $message['class'] ?> log-message">
                                                <?= htmlspecialchars($message['content']) ?>
                            </div>
                                        </td>
                                        <td>
                                            <button class="btn btn-xs btn-default copy-message" data-message="<?= htmlspecialchars($message['content']) ?>">
                                                <i class="fa fa-copy"></i> Copiar
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
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
    
<!-- DataTables JS adicional -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap.min.js"></script>

<!-- JSZip para exportación Excel -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<!-- PDFMake para exportación PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    
    <script>
    $(document).ready(function() {
        console.log('Resultado de carga masiva cargado');
        console.log('Resultado:', <?= json_encode($resultado) ?>);
        
    // Inicializar DataTable con funcionalidades de exportación
    var table = $('#messagesTable').DataTable({
        "responsive": true,
        "pageLength": 25,
        "order": [[ 0, "asc" ]],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json"
        },
        "dom": 'Bfrtip',
        "buttons": [
            {
                extend: 'excel',
                text: '<i class="fa fa-file-excel-o"></i> Excel',
                className: 'btn btn-success btn-sm',
                title: 'Mensajes de Carga Masiva - <?= date('Y-m-d H:i:s') ?>',
                exportOptions: {
                    columns: [0, 1, 2]
                }
            },
            {
                extend: 'pdf',
                text: '<i class="fa fa-file-pdf-o"></i> PDF',
                className: 'btn btn-danger btn-sm',
                title: 'Mensajes de Carga Masiva - <?= date('Y-m-d H:i:s') ?>',
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: {
                    columns: [0, 1, 2]
                },
                customize: function(doc) {
                    doc.content[1].table.widths = ['10%', '15%', '75%'];
                    doc.styles.tableHeader.fontSize = 10;
                    doc.defaultStyle.fontSize = 9;
                }
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print"></i> Imprimir',
                className: 'btn btn-info btn-sm',
                title: 'Mensajes de Carga Masiva - <?= date('Y-m-d H:i:s') ?>',
                exportOptions: {
                    columns: [0, 1, 2]
                }
            },
            {
                text: '<i class="fa fa-copy"></i> Copiar Todo',
                className: 'btn btn-warning btn-sm',
                action: function(e, dt, button, config) {
                    copyAllMessages();
                }
            }
        ],
        "columnDefs": [
            {
                "targets": [3],
                "orderable": false,
                "searchable": false
            }
        ]
    });
    
    // Función para copiar mensaje individual
    $('.copy-message').on('click', function() {
        var message = $(this).data('message');
        copyToClipboard(message);
        
        // Mostrar feedback visual
        var originalText = $(this).html();
        $(this).html('<i class="fa fa-check"></i> Copiado!');
        $(this).removeClass('btn-default').addClass('btn-success');
        
        setTimeout(function() {
            $(this).html(originalText);
            $(this).removeClass('btn-success').addClass('btn-default');
        }.bind(this), 2000);
    });
    
    // Función para copiar todos los mensajes
    function copyAllMessages() {
        var allMessages = '';
        table.rows().every(function() {
            var data = this.data();
            allMessages += '[' + data[1] + '] ' + data[2] + '\n';
        });
        
        copyToClipboard(allMessages);
        
        // Mostrar notificación
        showNotification('Todos los mensajes copiados al portapapeles', 'success');
    }
    
    // Función para copiar al portapapeles
    function copyToClipboard(text) {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(function() {
                console.log('Texto copiado al portapapeles');
            });
        } else {
            // Fallback para navegadores más antiguos
            var textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            textArea.style.top = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            try {
                document.execCommand('copy');
                console.log('Texto copiado al portapapeles (fallback)');
            } catch (err) {
                console.error('Error al copiar: ', err);
            }
            
            document.body.removeChild(textArea);
        }
    }
    
    // Función para mostrar notificaciones
    function showNotification(message, type) {
        var alertClass = type === 'success' ? 'alert-success' : 'alert-info';
        var notification = $('<div class="alert ' + alertClass + ' alert-dismissible" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">' +
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
            '<span aria-hidden="true">&times;</span></button>' +
            '<i class="fa fa-info-circle"></i> ' + message +
            '</div>');
        
        $('body').append(notification);
        
        setTimeout(function() {
            notification.fadeOut(function() {
                notification.remove();
            });
        }, 3000);
    }
    
    // Destacar filas con errores
    table.rows().every(function() {
        var data = this.data();
        if (data[1].includes('ERROR')) {
            $(this.node()).addClass('danger');
        } else if (data[1].includes('WARNING')) {
            $(this.node()).addClass('warning');
        } else if (data[1].includes('SUCCESS')) {
            $(this.node()).addClass('success');
        }
    });
    
    // Auto-scroll a la tabla
    $('html, body').animate({
        scrollTop: $('#messagesTable').offset().top - 100
    }, 500);
    });
    </script>

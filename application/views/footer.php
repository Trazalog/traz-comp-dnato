    </div> 
    <!--row-->   
    
    <footer class="main-footer">
        <hr>
        <?php if ($copyright == 'true') {
            echo 'Copyright - 2020 | <a href="http://trazalog.com/">TRAZALOG</a>';
        } ?>
        <div  class="pull-right hidden-xs" style="text-align:center;">    
        <i style="cursor: pointer;" id="btnVersion"><strong>Versión </strong> <?php echo  ApplicationVersion::getVerision(); ?></i>
        </div>
    </footer>
    </div>
    <!-- /container -->  
        
    <!--_______ MODAL ______-->
    <div class="modal" id="modalGitVersion">
        <div class="modal-dialog">
            <!-- modal-content -->
            <div class="modal-content">
                
                <!-- /.modal-body -->
                <div class="modal-body ">
                

                    <hr>
                    <div class="text-center text-muted" style="font-size: 12px; margin-top: 15px;">
                        © 2026 <strong>Trazalog SAS</strong>. Todos los derechos reservados.<br><br>

                        <strong>Propiedad Intelectual y Derechos de Autor</strong><br>
                        Todo el contenido de este software, incluyendo pero no limitado a: código fuente,
                        algoritmos, interfaces de usuario, diseños gráficos, logotipos, textos y archivos de audio,
                        es propiedad exclusiva de <strong>Trazalog SAS</strong> y está protegido por las leyes
                        internacionales de derechos de autor y propiedad intelectual.
                        Queda prohibida la reproducción, distribución, modificación o ingeniería inversa de
                        cualquier parte de este producto sin la autorización previa y por escrito del titular
                        de los derechos.
                    </div>
                </div> 
                <!-- /.modal-body -->

                <!-- modal-footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Aceptar</button>
                </div>
                <!-- modal-footer -->

            </div>
            <!-- /.modal-content -->
        </div>
    </div>
    <!-- /.modal -->

    <!-- /Load Js -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js" type="text/javascript"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="<?php echo base_url().'public/js/main.js' ?>"></script>

    <script>

        $(function () {
            $('#btnVersion').on('click', function () {
                $('#modalGitVersion').modal('show');
            });
        });

        function cargarCalendar(){

            var tagsLastCommits = <?php 
                try {
                    echo ApplicationVersion::getLastVersions();
                } catch (Exception $e) {
                    echo '[""]';
                }
            ?>;
            /*console.log(tagsLastCommits);*/

            let lastCommits = tagsLastCommits[0].split("\n");  
            /*console.log(lastCommits);*/

            var dataCalendar = [];

            lastCommits.forEach(function callback(elemento, indice, array) {  
                /*console.log("Elemento: "+elemento, indice);*/
                tagElemento = elemento.split(" ");
                
                if (typeof(tagElemento[0]) != "undefined" && typeof(tagElemento[1]) != "undefined" && typeof(tagElemento[2]) != "undefined" && typeof(tagElemento[3]) != "undefined" && typeof(tagElemento[4]) != "undefined" && typeof(tagElemento[5]) != "undefined"){
                    /*console.log("0: "+tagElemento[0]+" 1: "+tagElemento[1]+" 2: "+tagElemento[2]+" 3. "+tagElemento[3]+" 4. "+tagElemento[4]+" 5. "+tagElemento[5]);*/
                    dataCalendar[indice] = {
                        title : tagElemento[3] +" "+tagElemento[4] +" "+tagElemento[5],
                        start : tagElemento[0],
                        end : tagElemento[0]  

                    }
                }

            });
            /*console.log("DataCalendar: "+dataCalendar);*/
            var data = dataCalendar.filter(Boolean);
            var events = JSON.stringify(data);
            /*console.log("Events: "+events);*/


            var initialLocaleCode = 'es';
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
            headerToolbar: {
                left: 'prev,next',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listMonth'
            },
            initialDate: new Date(),
            locale: initialLocaleCode,
            navLinks: true, 
            businessHours: true, 
            selectable: true,
            events: $.parseJSON(events)  
            });

            calendar.render();
        }
    </script>

    <script src='<?php  echo base_url();?>assets/fullcalendar/lib/main.js'></script>
    <script src='<?php  echo base_url();?>assets/fullcalendar/lib/locales-all.js'></script>

    <!-- DataTables 1.10.7 -->
    <script src="<?php echo base_url();?>assets/plugin/datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo base_url();?>assets/plugin/datatables/dataTables.bootstrap.min.js"></script>
    <script src="<?php echo base_url();?>assets/plugin/datatables/extensions/Buttons/js/dataTables.buttons.min.js"></script>
    <script src="<?php echo base_url();?>assets/plugin/datatables/extensions/Buttons/js/jszip.min.js"></script>
    <script src="<?php echo base_url();?>assets/plugin/datatables/extensions/Buttons/js/buttons.html5.min.js"></script>
    <script src="<?php echo base_url();?>assets/plugin/datatables/extensions/Buttons/js/buttons.print.min.js"></script>
    <script src="<?php echo base_url();?>assets/plugin/datatables/extensions/Buttons/js/buttons.bootstrap.js"></script>

    </body>
</html>

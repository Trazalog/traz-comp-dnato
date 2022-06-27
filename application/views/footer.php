    </div> 
    <!--row-->   
    
    <footer class="main-footer">
        <hr>
        Copyright - 2020 | <a href="http://trazalog.com/">TRAZALOG</a>
        <div  class="pull-right hidden-xs" style="text-align:center;">    
        <i style="cursor: pointer;" onclick="modalDetailVersion();"><strong>Versión </strong> <?php echo  ApplicationVersion::getVerision(); ?></i>
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
                    <?php
                        //echo  ApplicationVersion::getLastVersions();
                    ?>
                    <div id='calendar'></div>
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
        function modalDetailVersion(){

            $("#modalGitVersion").modal('show');
            cargarCalendar();
        }

        function cargarCalendar(){

            var tagsLastCommits = <?php echo ApplicationVersion::getLastVersions(); ?>;
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

    </body>
</html>

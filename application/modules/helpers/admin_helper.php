<?php defined('BASEPATH') OR exit('No direct script access allowed');

if(!function_exists('modal'))
{ 
    function modal($e)
    {
       return 
       "<div class='modal fade' id='$e->id'>
       <div class='modal-dialog'>
         <div class='modal-content'>
           <div class='modal-header'>
             <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
               <span aria-hidden='true'>&times;</span></button>
             <h4 class='modal-title'>$e->titulo</h4>
           </div>
           <div class='modal-body'>
             $e->body
           </div>
           <div class='modal-footer'>
             <button type='button' class='btn btn-default pull-left' data-dismiss='modal'>Cerrar</button>
             ".(isset($e->accion)?"<button type='button' class='btn btn-primary btn-accion'>$e->accion</button>":null)."
           </div>
         </div>
       </div>  
     </div>";
    }
}
?>
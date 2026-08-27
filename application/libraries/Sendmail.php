<?php
/**
 * Send Mail
 * Create by Abed Putra
 * http://abedputra.com
 * Github: https://github.com/abedputra
 * 2017
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class SendMail{

    public function secureMail($fn,$ln,$em,$dt,$t,$tLe,$bro,$os,$ip,$url){
        $message = '';
        $message .= 'Hi ' .$fn.' '.$ln.',';
        $message .= '<br>';
        $message .= '<br>';
        $message .= 'Your account ' .$em.' was just used to sign in from '.$bro.' on '.$os.'.';
        $message .= '<br>';
        $message .= '<br>';
        $message .= '<table>';
        $message .= '<tr>';
        $message .= '<td>Your Username</td><td> : <b>' .$em.'</b></td>';
        $message .= '</tr>';
        $message .= '<tr>';
        $message .= '<td>From Browser</td><td> : <b>'.$bro.'</b></td>';
        $message .= '</tr>';
        $message .= '<tr>';
        $message .= '<td>From OS</td><td> : <b>'.$os.'</b><td>';
        $message .= '</tr>';
        $message .= '<tr>';
        $message .= '<td>From IP</td><td> : <b>'.$ip.'</b></td>';
        $message .= '</tr>';
        $message .= '<tr>';
        $message .= '<td>Date</td><td> : <b>'.$dt.'</b></td>';
        $message .= '</tr>';
        $message .= '<tr>';
        $message .= '<td>Time</td><td> : <b>'.$t.'</b></td>';
        $message .= '</tr>';
        $message .= '</table>';
        $message .= '<br>';
        $message .= '<br>';
        $message .= 'Don\'t recognise this activity?';
        $message .= '<br>';
        $message .= 'Secure your account, from this link.';
        $message .= '<br>';
        $message .= '<a href='.$url.'><b>Login.</b></a>';
        $message .= '<br>';
        $message .= '<br>';
        $message .= 'Why are we sending this?<br>We take security very seriously and we want to keep you in the loop on important actions in your account.';
        $message .= '<br>';
        $message .= '<br>';
        $message .= 'Sincerely yours,<br>';
        $message .= $tLe;
        return $message;
    }
    
    public function sendRegister($ls,$em,$link,$tLe){
        
        $message = '';
        $message .= 'Hi, ' .$ls.'<br>';
        $message .= '<br>';
        $message .= 'Welcome! you have signed up with our website with the following information:<br>';
        $message .= '<br>';
        $message .= '<strong>Username : '.$em.'</strong><br>';
        $message .= '<strong>Password : (Not Set) </strong><br>';
        $message .= '<br>';
        $message .= 'Before you can login, you need to activate and set your Password';
        $message .= '<br>';
        $message .= 'account by clicking on this link:';
        $message .= '<br><br>';
        $message .= $link . '<br>';
        $message .= '<br>';
        $message .= 'Sincerely yours,<br>';
        $message .= $tLe;
        return $message;
    }
    
    public function sendForgot($ls,$em,$link,$tLe){
        
        $message = '';
        $message .= 'Hello, ' .$ls.'<br>';
        $message .= '<br>';
        $message .= 'We\'ve generated a new password for you at your<br>';
        $message .= 'request, you can use this new password with your username:<br>';
        $message .= '<br>';
        $message .= '<strong>Username : '.$em.'</strong><br>';
        $message .= '<strong>Password : (Forgot Password) </strong><br>';
        $message .= '<br>';
        $message .= 'To reset your Password please, clicking on this link:';
        $message .= '<br><br>';
        $message .= $link . '<br>';
        $message .= '<br>';
        $message .= 'Sincerely yours,<br>';
        $message .= $tLe;
        return $message;
    }


    /**
     * Correo de bienvenida que se envía al terminar el alta de una empresa.
     *
     * Va al mismo correo que se verificó durante el registro, y resume lo que
     * el sistema creó: los usuarios de la empresa con su rol y la contraseña
     * temporal con la que nacen.
     *
     * HTML pensado para clientes de correo: maquetado con tablas y estilos
     * en línea, sin hojas de estilo externas ni flexbox, que Outlook ignora.
     * Ancho fijo de 600 px, el estándar que entra bien en cualquier cliente.
     *
     * @param  array  $usuarios     lista de array('email' => ..., 'roles_label' => ...)
     * @param  string $password     contraseña temporal de esos usuarios
     * @param  string $empresa      razón social
     * @param  string $urlLogin     URL de la pantalla de ingreso
     * @param  string $logoUrl      URL absoluta del logo
     * @param  string $sitio        nombre del sistema, para la firma
     * @return string HTML del correo
     */
    public function sendBienvenidaEmpresa($usuarios, $password, $empresa, $urlLogin, $logoUrl, $sitio)
    {
        $empresa  = htmlspecialchars((string) $empresa, ENT_QUOTES, 'UTF-8');
        $password = htmlspecialchars((string) $password, ENT_QUOTES, 'UTF-8');
        $sitio    = htmlspecialchars((string) $sitio, ENT_QUOTES, 'UTF-8');

        // Filas de la tabla de usuarios
        $filas = '';
        $i = 0;
        foreach ((array) $usuarios as $u) {
            $mail  = htmlspecialchars(isset($u['email']) ? $u['email'] : '', ENT_QUOTES, 'UTF-8');
            $roles = htmlspecialchars(isset($u['roles_label']) ? $u['roles_label'] : '', ENT_QUOTES, 'UTF-8');
            $fondo = ($i % 2 === 0) ? '#ffffff' : '#f7f9fb';
            $filas .= '<tr>'
                   .  '<td style="padding:11px 14px;background:' . $fondo . ';border-bottom:1px solid #e8edf2;'
                   .  'font-family:Arial,Helvetica,sans-serif;font-size:14px;color:#1b2733;">' . $mail . '</td>'
                   .  '<td style="padding:11px 14px;background:' . $fondo . ';border-bottom:1px solid #e8edf2;'
                   .  'font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#6b7a8c;">' . $roles . '</td>'
                   .  '</tr>';
            $i++;
        }
        if ($filas === '') {
            $filas = '<tr><td colspan="2" style="padding:14px;font-family:Arial,Helvetica,sans-serif;'
                   . 'font-size:14px;color:#6b7a8c;">No se registraron usuarios adicionales.</td></tr>';
        }

        $m  = '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8">';
        $m .= '<meta name="viewport" content="width=device-width,initial-scale=1">';
        $m .= '<title>Bienvenido a ' . $sitio . '</title></head>';
        $m .= '<body style="margin:0;padding:0;background:#eef2f6;">';

        $m .= '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#eef2f6;padding:28px 12px;">';
        $m .= '<tr><td align="center">';

        $m .= '<table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="width:600px;max-width:100%;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(27,39,51,0.08);">';

        // Encabezado
        $m .= '<tr><td align="center" style="background:#1b2a38;padding:28px 24px;">';
        $m .= '<img src="' . $logoUrl . '" alt="' . $sitio . '" width="170" style="display:block;border:0;max-width:170px;height:auto;">';
        $m .= '</td></tr>';

        // Título
        $m .= '<tr><td style="padding:34px 36px 0 36px;font-family:Arial,Helvetica,sans-serif;">';
        $m .= '<h1 style="margin:0 0 10px 0;font-size:23px;line-height:1.3;color:#1b2733;font-weight:bold;">';
        $m .= 'La cuenta de ' . $empresa . ' ya está activa</h1>';
        $m .= '<p style="margin:0 0 22px 0;font-size:15px;line-height:1.6;color:#55677c;">';
        $m .= 'Terminamos de preparar tu empresa. Junto con tu cuenta creamos los usuarios de trabajo ';
        $m .= 'que están abajo, cada uno con su rol ya asignado, para que puedas empezar a operar hoy mismo.</p>';
        $m .= '</td></tr>';

        // Tabla de usuarios
        $m .= '<tr><td style="padding:0 36px;">';
        $m .= '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #e8edf2;border-radius:8px;overflow:hidden;">';
        $m .= '<tr>';
        $m .= '<th align="left" style="padding:11px 14px;background:#f0f4f8;border-bottom:1px solid #e8edf2;'
           .  'font-family:Arial,Helvetica,sans-serif;font-size:12px;letter-spacing:0.4px;text-transform:uppercase;color:#44546a;">Usuario</th>';
        $m .= '<th align="left" style="padding:11px 14px;background:#f0f4f8;border-bottom:1px solid #e8edf2;'
           .  'font-family:Arial,Helvetica,sans-serif;font-size:12px;letter-spacing:0.4px;text-transform:uppercase;color:#44546a;">Rol</th>';
        $m .= '</tr>' . $filas . '</table>';
        $m .= '</td></tr>';

        // Contraseña temporal
        $m .= '<tr><td style="padding:20px 36px 0 36px;font-family:Arial,Helvetica,sans-serif;">';
        $m .= '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#fff8e6;border-left:4px solid #f0ad4e;border-radius:6px;">';
        $m .= '<tr><td style="padding:16px 18px;font-family:Arial,Helvetica,sans-serif;">';
        $m .= '<p style="margin:0 0 8px 0;font-size:14px;color:#6b5312;font-weight:bold;">Contraseña temporal</p>';
        $m .= '<p style="margin:0 0 10px 0;font-size:14px;line-height:1.6;color:#6b5312;">';
        $m .= 'Todos esos usuarios ingresan por primera vez con la contraseña ';
        $m .= '<span style="display:inline-block;background:#ffffff;border:1px solid #e6d9b0;border-radius:4px;';
        $m .= 'padding:2px 9px;font-family:Consolas,Monaco,monospace;font-size:15px;color:#1b2733;">' . $password . '</span></p>';
        $m .= '<p style="margin:0;font-size:13px;line-height:1.6;color:#8a6116;">';
        $m .= '<strong>Cambiala en el primer ingreso.</strong> Es la misma para todos y viaja en este correo, ';
        $m .= 'así que cualquiera que lo lea puede usarla. Entrá a Mi perfil y definí una contraseña propia para cada usuario.</p>';
        $m .= '</td></tr></table></td></tr>';

        // Botón
        $m .= '<tr><td align="center" style="padding:28px 36px 6px 36px;">';
        $m .= '<table role="presentation" cellpadding="0" cellspacing="0" border="0"><tr>';
        $m .= '<td align="center" style="background:#27ae60;border-radius:8px;">';
        $m .= '<a href="' . $urlLogin . '" style="display:inline-block;padding:14px 34px;font-family:Arial,Helvetica,sans-serif;';
        $m .= 'font-size:16px;font-weight:bold;color:#ffffff;text-decoration:none;">Ingresar a ' . $sitio . '</a>';
        $m .= '</td></tr></table></td></tr>';

        $m .= '<tr><td align="center" style="padding:12px 36px 30px 36px;font-family:Arial,Helvetica,sans-serif;">';
        $m .= '<p style="margin:0;font-size:12.5px;line-height:1.6;color:#8595a7;">';
        $m .= 'Si el botón no funciona, copiá esta dirección en tu navegador:<br>';
        $m .= '<span style="color:#3498db;word-break:break-all;">' . $urlLogin . '</span></p>';
        $m .= '</td></tr>';

        // Pie
        $m .= '<tr><td style="padding:22px 36px;background:#f7f9fb;border-top:1px solid #e8edf2;font-family:Arial,Helvetica,sans-serif;">';
        $m .= '<p style="margin:0 0 6px 0;font-size:13px;line-height:1.6;color:#6b7a8c;">';
        $m .= '¿Necesitás una mano para arrancar? Respondé este correo y te ayudamos.</p>';
        $m .= '<p style="margin:0;font-size:12px;color:#9aa8b8;">' . $sitio . ' · Recibiste este correo porque diste de alta una empresa en nuestro sistema.</p>';
        $m .= '</td></tr>';

        $m .= '</table></td></tr></table></body></html>';

        return $m;
    }
}

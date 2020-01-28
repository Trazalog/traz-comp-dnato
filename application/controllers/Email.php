<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Email extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function send()
    {
        // Load PHPMailer library
        $this->load->library('Phpmailer_lib');

        // PHPMailer object
        $mail = $this->phpmailer_lib->load();

        // SMTP configuration
        $mail->isSMTP();
        $mail->SMTPDebug = 4;
        $mail->Host = 'ssl://smtp.gmail.com:465';
        $mail->SMTPAuth = true;
        $mail->Username = 'soportetrazalog24@gmail.com';
        $mail->Password = '123trazalog24';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('info@example.com', 'Programacion.net');
        $mail->addReplyTo('info@example.com', 'Programacion.net');

        // Add a recipient
        $mail->addAddress('fleiva@trazalog.com');

        // Add cc or bcc
        $mail->addCC('cc@example.com');
        $mail->addBCC('bcc@example.com');

        // Email subject
        $mail->Subject = 'Send Email via SMTP using PHPMailer in CodeIgniter';

        // Set email format to HTML
        $mail->isHTML(true);

        // Email body content
        $mailContent = "<h1>Send HTML Email using SMTP in CodeIgniter</h1>
            <p>This is a test email sending using SMTP mail server with PHPMailer.</p>";
        $mail->Body = $mailContent;

        // Send email
        if (!$mail->send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            echo 'Message has been sent';
        }
    }

}

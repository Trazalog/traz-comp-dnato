    <!--[if lt IE 8]>
        <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

    <?php
        //for warning -> flash_message
        //for info -> success_message
        
        $arr = $this->session->flashdata();
        if(!empty($arr['flash_message'])){
            $html = '<div class="container" style="margin-top: 10px;">';
            $html .= '<div class="alert alert-warning alert-dismissible" role="alert">';
            $html .= '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
            $html .= '<span class="alert-main-text">' . $arr['flash_message'] . '</span>';
            if (!empty($arr['flash_message_hint'])) {
                $html .= '<div class="flash-message-hint" style="margin-top:12px; padding:10px 12px; background:#fff; border:1px solid rgba(0,0,0,0.12); border-radius:4px; color:#212529; font-size:0.88em; line-height:1.45;">';
                $html .= $arr['flash_message_hint'];
                $html .= '</div>';
            }
            $html .= '</div>';
            $html .= '</div>';
            echo $html;
        }else if (!empty($arr['success_message'])){
            $html = '<div class="container" style="margin-top: 10px;">';
            $html .= '<div class="alert alert-info alert-dismissible" role="alert">';
            $html .= '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
            $html .= '<span class="alert-main-text">' . $arr['success_message'] . '</span>';
            if (!empty($arr['flash_message_hint'])) {
                $html .= '<div class="flash-message-hint" style="margin-top:12px; padding:10px 12px; background:#fff; border:1px solid rgba(0,0,0,0.12); border-radius:4px; color:#212529; font-size:0.88em; line-height:1.45;">';
                $html .= $arr['flash_message_hint'];
                $html .= '</div>';
            }
            $html .= '</div>';
            $html .= '</div>';
            echo $html;
        }else if (!empty($arr['danger_message'])){
            $html = '<div class="container" style="margin-top: 10px;">';
            $html .= '<div class="alert alert-danger alert-dismissible" role="alert">';
            $html .= '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
            $html .= $arr['danger_message'];
            $html .= '</div>';
            $html .= '</div>';
            echo $html;
        }
    ?>
    <div class="container">
        <div class="row">
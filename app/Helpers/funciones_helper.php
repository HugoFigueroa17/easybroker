<?php

//prints a safe value applying htmlspecialchars
function output($str){
    return htmlspecialchars($str);
}//output

//HUGO->Returns a cleaned post value
function post($key = NULL){
    $output = null;
    if ($key !== NULL){
        if (isset($_POST[$key])){
            if(is_array($_POST[$key])){
                $valores = array();
                foreach ($_POST[$key] as $valor){
                    if(trim($valor) != "")
                        array_push($valores,$valor);
                }
                $output = $valores;
            }
            else
            {
                $output = trim($_POST[$key]);
                $output = strip_tags($output);
            }
        }//if isset
    } else {
        $output = array();
        foreach($_POST as $key => $value){
            if(is_array($value)){
                $valores = array();
                foreach ($value as $valor){
                    if(trim($valor) != "")
                        array_push($valores,$valor);

                }
                $output[$key] = $valores;
            }
            else {
                $output[$key] = strip_tags(trim($value));
            }
        }//foreach
    }//if-else
    return $output;
}//post

//Retursn an active string if v1 and v2 are identical
function activeMenu($v1, $v2){
    $output = '';
    if (is_array($v1)){
        if (in_array($v2, $v1)){
            $output = 'active show';
        }//if
    } else if ($v1 === $v2){
        $output = 'active show';
    }
    return $output;
}//activeMenu

//HUGO->Get foto perfil
function fotoPerfil($empleadoID){
    $empleadoID = (int)$empleadoID;
    helper('filesystem');
    $url = dirname(WRITEPATH);
    $url2 = dirname(WRITEPATH);
    $url .= "/assets/uploads/fotoEmpleado/empleado-".$empleadoID.".png";
    $url2 .= "/assets/uploads/fotoEmpleado/empleado-".$empleadoID.".jpg";
    $imagen = base_url("assets/img/avatar.jpg");

    if(file_exists($url)){
        $imagen = base_url("assets/uploads/fotoEmpleado/empleado-".$empleadoID.".png");
    }elseif (file_exists($url2)){
        $imagen = base_url("assets/uploads/fotoEmpleado/empleado-".$empleadoID.".jpg");
    }//if

    return $imagen;
}//fotoPerfil


//Verifica que la secion sea correcta
function validarSesion($sesion){
    $ok = false;
    if (is_array($sesion)){
        foreach($sesion as $k){
            if (isset($_SESSION['loginType']) && $_SESSION['loginType'] === $k){
                $ok = true;
            }//if
        }//foreach
    } else {
        if (!isset($_SESSION['loginType']) || $_SESSION['loginType'] !== $sesion){
            $ok = false;
        }//if
        else {
            $ok = true;
        }
    }//if-else

    if(!$ok){
        $url = "Location: " .base_url('Access/logOut');
        //$url = "Location: " .base_url('Access/sesionInvalida');
        header($url);
        exit();
    }
}//validateSession

function getPermisos($usuarioID,$obj){
    $sql = "SELECT R.rol_Permisos, E.emp_Permisos, E.emp_RolID 
            FROM empleado E 
            LEFT JOIN rol R ON R.rol_RolID = E.emp_RolID
            WHERE E.emp_EmpleadoID = ?";
    $permisos = $obj->db->query($sql,array((int)$usuarioID))->getRowArray();
    if(isset($permisos['emp_RolID']) && $permisos['emp_RolID'] == 2){
        $permisos = $permisos['emp_Permisos'];
    }else{
        $permisos = $permisos['rol_Permisos'];
    }
    if(!empty($permisos)){
        $permisos = json_decode($permisos,1);
    }else{
        $permisos = array(
            'index' => array('Ver')
        );
    }
    return $permisos;
}

function getCumpleanios($obj){
    return $obj->db->query(
        "SELECT COUNT(E.emp_EmpleadoID) as 'total' FROM empleado E
            WHERE DAY(E.emp_FechaNacimiento)=DAY(NOW()) AND MONTH(E.emp_FechaNacimiento)=MONTH(NOW())
        AND E.emp_Estatus = 1")->getRowArray();
}

function revisarPermisos($accion,$funcion = null){
    $permisos = session('permisos');
    $response = false;

    if(is_null($funcion)) {
        $url = uri_string(true);
        $url = explode('/', $url);
        $funcion = $url[1];
    }
    if(isset($permisos[$funcion])){
        if(in_array($accion,$permisos[$funcion])){
            $response = true;
        }
    }
    return $response;
}

function validarPermisos($permisos,$funcion){
    if(!isset($permisos[$funcion])){
        $url = "Location: " .base_url('Access/sesionInvalida');
        header($url);
        exit();
    }
}

function showMenu($funcion){
    $permisos = session('permisos');
    $mostrar = false;

    if(is_array($funcion)) {
        foreach ($funcion as $f) {
            if (isset($permisos[$f])) {
                $mostrar = true;
            }
        }
    }else{
        if (isset($permisos[$funcion])) {
            $mostrar = true;
        }
    }
    return $mostrar;
}

function addMenuOption($controlador,$funcion,$txt,$icon='',$show = 0){
    if(showMenu($funcion) || $show) {
        $uri = uri_string();
        $segments = explode("/", $uri);
        $page = output($segments[1]);
        $class = activeMenu($funcion,$page);

        echo '<li class="'.$class.'">';
        echo '<a href="' . base_url($controlador . "/" . $funcion) . '">';
        echo ($icon != '') ? $icon : '';
        echo $txt;
        echo '</a></li>';
    }
}

//Generates a secures hash key
function encryptKey($key){
    return password_hash($key, PASSWORD_DEFAULT);
}//encryptKey


function getController(){
    $controlador = session('type');
    $controlador = ucfirst($controlador);
    return $controlador;
}//getController

//returns a dd-month-yyyy mysql date
function longDate($date, $delimiter = '-'){
    $meses = array('', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');
    $output = '';
    //Format date
    if (!empty($date)){
        $exploded = explode('-', $date);
        $output = $exploded[2].$delimiter.$meses[(int)$exploded[1]].$delimiter.$exploded[0];
    }//if
    return $output;
}//longDate

//Sends an email using PHPMailer
function sendMail($targets,$subject ,$datos ,$tipo , $files = array()){

    require_once (APPPATH.'Libraries/phpmailer/class.phpmailer.php');
    require_once APPPATH.'Libraries/phpmailer/class.smtp.php';

    try {
        $mail = new PHPMailer(true);
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "TLS";
        $mail->Host = "216.70.80.40";                   //< - - - EDIT
        $mail->Port = 587;
        $mail->Username = "ccsistemas@rekhum.com";             //< - - - EDIT
        $mail->Password = "@h6Xq48x";                   //< - - - EDIT
        $mail->From = "ccsistemas@rekhum.com";                 //< - - - EDIT
        $mail->FromName = "Agrizar";               //< - - - EDIT
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $subject;


        if($tipo === 'ProyAut') {
            $content = writeMessageProyAut($datos);
        }

        $mail->MsgHTML($content);

        //Clear addresses and attatchments
        $mail->clearAllRecipients();
        $mail->clearAttachments();

        //Set dest email
        if (is_array($targets)){

            foreach ($targets as $email){
                var_dump($email);
                if (filter_var($email, FILTER_VALIDATE_EMAIL)){
                    $mail->AddAddress(trim($email));
                }//if
            }//foreach
        } else {
            $mail->AddAddress(trim($targets));
        }//if-else

        //Attatch files
        if (!empty($files)){
            foreach($files as $f){
                $mail->addAttachment($f['src'], $f['name']);
            }//foreach
        }//files

        //Return success code
        return $mail->Send();
    } catch(Exception $e){
        var_dump($e->getMessage());
        return false;
    }//try-catch

}//sendMail

//Germán -> Construye el mensaje del correo para los contactos
function writeMessageProyAut($datos){
    return '<table class="body-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6">
    <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
        <td style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top"></td>
        <td class="container" width="600"
            style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; display: block !important; max-width: 600px !important; clear: both !important; margin: 0 auto;"
            valign="top">
            <div class="content"
                 style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; max-width: 600px; display: block; margin: 0 auto; padding: 20px;">
                <table class="main" width="100%" cellpadding="0" cellspacing="0"
                       style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; background-color: #fff; display: inline-block; margin: 0; border: 1px solid #e9e9e9;"
                       bgcolor="#fff">
                    <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                        <td class=""
                            style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; color: #fff; font-weight: 500; text-align: center; border-radius: 3px 3px 0 0; background-color: #e3eaef; margin: 0; padding: 20px;"
                            align="center" bgcolor="#71b6f9" valign="top">
                            <a href="#"> <img src="http://http://agrizar.scmcomonfort.com/assets/img/logo-login.png" height="70" alt="logo"/></a> <br/>
                            <span style="margin-top: 10px;display: block; color:#22af47;">Sistema Agrizar</span>
                        </td>
                    </tr>
                    <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                        <td class="content-wrap"
                            style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 20px;"
                            valign="top">
                            <table width="100%" cellpadding="0" cellspacing="0"
                                   style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                    <td class="content-block"
                                        style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;"
                                        valign="top">
                                        Estimado (a) '.$datos['usu_Nombre'].':
                                    </td>
                                </tr>
                                <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                    <td class="content-block"
                                        style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;"
                                        valign="top">
                                       Se ha Autorizado una nueva Proyeccion puedes revisarla en el sistema.
                                    </td>
                                </tr>
                                <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                    <td class="content-block"
                                        style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;"
                                        valign="top">
                                        ¡Buen día! <br> El equipo de <b>Agrizar</b>.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <div class=""
                     style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; clear: both; color: #999; margin: 0; padding: 20px;">
                    <table width="100%"
                           style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                        <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                            <td class="aligncenter content-block"
                                style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; color: #999; text-align: center; margin: 0; padding: 0 0 20px;"
                                align="center" valign="top"> Derechos Reservados &#169; <a href="#"
                                                               style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; color: #999; text-decoration: underline; margin: 0;">Comwor</a> '.date("Y").'
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </td>
        <td style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;"
            valign="top"></td>
    </tr>
</table>';
}// end writeMessageContacto

// Fernando Carrillo -> Enviar una notificacion push a un dispositivo Android o iOS
function sendPush($device_id,$message)
{
    if (empty(API_KEY_PUSH)){
        return array(
            'success'=>0,
            'results'=>array(
                array(
                    'error'=>'No se encontro la api key'
                )
            )
        );
    }

    if (empty($device_id)){
        return array(
            'success'=>0,
            'results'=>array(
                array(
                    'error'=>'No se encontro el device id'
                )
            )
        );
    }

    $url = 'https://fcm.googleapis.com/fcm/send';

    $fields = array(
        'to' => $device_id,
        'notification'=> $message,
    );

    $headers = array(
        'Authorization: key='.API_KEY_PUSH,
        'Content-Type: application/json'
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    $result = curl_exec($ch);
    if ($result === FALSE) {
        return array(
            'success'=>0,
            'results'=>array(
                array(
                    'error'=>'Curl failed: '. curl_error($ch)
                )
            )
        );
    }

    curl_close($ch);

    $result=!empty($result)?json_decode($result,true):false;

    if ($result==false){
        return array(
            'success'=>0,
            'results'=>array(
                array(

                    'error'=>'invalid response from push service'
                )
            )
        );
    }

    return $result;
}

// Fernando Carrillo -> Funcion para enviar una Notificacion Push  
function enviarNotificacionPush($tokens,$body,$title){
    $message = array('body'=>$body,
                     'title'=>$title,
                     'sound'=>"default",
                     'vibrate'=>"true");

    // Variable $tokens puede ser un String o un ResultArray
    if(!empty($tokens)){
        if (is_array($tokens) && count($tokens)>=1){
            foreach ($tokens as $item) {
                foreach ($item as $key => $value) {
                    sendPush($value,$message);
                } 
            }
        }else{
            if(!empty($tokens)){
                sendPush($tokens,$message);
            }
        }
    }
}

function diaFecha($fecha){
    $fechats  = strtotime($fecha); //pasamos a timestamp

//el parametro w en la funcion date indica que queremos el dia de la semana
//lo devuelve en numero 0 domingo, 1 lunes,....
    switch (date('w', $fechats)){
        case 0: return "Domingo"; break;
        case 1: return "Lunes"; break;
        case 2: return "Martes"; break;
        case 3: return "Miercoles"; break;
        case 4: return "Jueves"; break;
        case 5: return "Viernes"; break;
        case 6: return "Sabado"; break;
    }
}

//Verifies a key-value pair and redirects to logout page
function validateSession($key, $value = NULL){
    if (is_array($key)){
        $ok = false;
        foreach($key as $k => $value){
            if (isset($_SESSION[$k]) && $_SESSION[$k] === $value){
                $ok = true;
            }//if
        }//foreach
        if ($ok === false){
            return redirect()->to(base_url("Access/logOut"));
        }//if
    } else {
        if (!isset($_SESSION[$key]) || $_SESSION[$key] !== $value){
            return redirect()->to(base_url("Access/logOut"));
        }//if
    }//if-else
}//validateSession

//Diego -> Get file expediente unidad
function fileExpedienteUnidad($UnidadID,$idArchivo){
    helper('filesystem');
    $url = dirname(WRITEPATH);

    $url .="/assets/uploads/expedienteUnidad/".$UnidadID."/";

    if(!file_exists($url)) mkdir($url, 0777, true);

    $directory = $url;
    $archivo = "Archivo".$idArchivo;
    $data =array();
    if (file_exists($url .$archivo.".docx")) {
        $data["id"]=$idArchivo;
        $data["archivo"]=$idArchivo;
        $data["tipo"] = "word";
        $data["icon"] = base_url("assets/img/iconExpediente/word.png");
        $data["url"] = base_url("/assets/uploads/expedienteUnidad/".$UnidadID."/".$archivo.".docx");
    }elseif(file_exists($url . $archivo.".pdf")){
        $data["id"]=$idArchivo;
        $data["archivo"]=$idArchivo;
        $data["tipo"] = "pdf";
        $data["icon"] = base_url("assets/img/iconExpediente/pdf.png");
        $data["url"] = base_url("/assets/uploads/expedienteUnidad/".$UnidadID."/".$archivo.".pdf");
    }elseif (file_exists($url . $archivo.".png")){
        $data["id"]=$idArchivo;
        $data["archivo"]=$archivo;
        $data["tipo"] = "img";
        $data["icon"] = base_url("assets/img/iconExpediente/img.png");
        $data["url"] = base_url("/assets/uploads/expedienteUnidad/".$UnidadID."/".$archivo.".png");
    }elseif(file_exists($url . $archivo.".jpg")){
        $data["id"]=$idArchivo;
        $data["archivo"]=$idArchivo;
        $data["tipo"] = "img";
        $data["icon"] = base_url("assets/img/iconExpediente/img.png");
        $data["url"] = base_url("/assets/uploads/expedienteUnidad/".$UnidadID."/".$archivo.".jpg");
    }elseif (file_exists($url . $archivo.".jpeg")){
        $data["id"]=$idArchivo;
        $data["archivo"]=$idArchivo;
        $data["tipo"] = "img";
        $data["icon"] = base_url("assets/img/iconExpediente/img.png");
        $data["url"] = base_url("/assets/uploads/expedienteUnidad/".$UnidadID."/".$archivo.".jpeg");
    }elseif (file_exists($url .$archivo.".xml")) {
        $data["id"]=$idArchivo;
        $data["archivo"]=$idArchivo;
        $data["tipo"] = "xml";
        $data["icon"] = base_url("assets/img/iconExpediente/xml.png");
        $data["url"] = base_url("/assets/uploads/expedienteUnidad/".$UnidadID."/".$archivo.".xml");
    }else{
        $data=null;
    }

    return $data;
}//fileExpedienteUnidad

//Diego -> Get file expediente unidad
function fileEvidenciaAccidente($AccidenteID,$idArchivo){
    helper('filesystem');
    $url = dirname(WRITEPATH);

    $url .="/assets/uploads/accidentes/".$AccidenteID."/";

    if(!file_exists($url)) mkdir($url, 0777, true);

    $directory = $url;
    $archivo = "Evidencia".$idArchivo;
    $data =array();
    if (file_exists($url .$archivo.".docx")) {
        $data["id"]=$idArchivo;
        $data["archivo"]=$idArchivo;
        $data["tipo"] = "word";
        $data["icon"] = base_url("assets/img/iconExpediente/word.png");
        $data["url"] = base_url("/assets/uploads/accidentes/".$AccidenteID."/".$archivo.".docx");
    }elseif(file_exists($url . $archivo.".pdf")){
        $data["id"]=$idArchivo;
        $data["archivo"]=$idArchivo;
        $data["tipo"] = "pdf";
        $data["icon"] = base_url("assets/img/iconExpediente/pdf.png");
        $data["url"] = base_url("/assets/uploads/accidentes/".$AccidenteID."/".$archivo.".pdf");
    }elseif (file_exists($url . $archivo.".png")){
        $data["id"]=$idArchivo;
        $data["archivo"]=$archivo;
        $data["tipo"] = "img";
        $data["icon"] = base_url("assets/img/iconExpediente/img.png");
        $data["url"] = base_url("/assets/uploads/accidentes/".$AccidenteID."/".$archivo.".png");
    }elseif(file_exists($url . $archivo.".jpg")){
        $data["id"]=$idArchivo;
        $data["archivo"]=$idArchivo;
        $data["tipo"] = "img";
        $data["icon"] = base_url("assets/img/iconExpediente/img.png");
        $data["url"] = base_url("/assets/uploads/accidentes/".$AccidenteID."/".$archivo.".jpg");
    }elseif (file_exists($url . $archivo.".jpeg")){
        $data["id"]=$idArchivo;
        $data["archivo"]=$idArchivo;
        $data["tipo"] = "img";
        $data["icon"] = base_url("assets/img/iconExpediente/img.png");
        $data["url"] = base_url("/assets/uploads/accidentes/".$AccidenteID."/".$archivo.".jpeg");
    }elseif (file_exists($url .$archivo.".xml")) {
        $data["id"]=$idArchivo;
        $data["archivo"]=$idArchivo;
        $data["tipo"] = "xml";
        $data["icon"] = base_url("assets/img/iconExpediente/xml.png");
        $data["url"] = base_url("/assets/uploads/accidentes/".$AccidenteID."/".$archivo.".xml");
    }else{
        $data=null;
    }

    return $data;
}//fileExpedienteUnidad

//Nat -> Comprimir imagenes, recibe archivo temporal, lugar de destino y que calidad debe tener (porcentaje)
function comprimirImagen($source, $destination, $quality) {
    // Obtenemos la información de la imagen
    $imgInfo = getimagesize($source);
    $mime = $imgInfo['mime'];

    // Creamos una imagen
    switch($mime){
        case 'image/jpeg':
            $image = imagecreatefromjpeg($source);
            break;
        case 'image/png':
            $image = imagecreatefrompng($source);
            break;
        case 'image/gif':
            $image = imagecreatefromgif($source);
            break;
        default:
            $image = imagecreatefromjpeg($source);
    }

    // Guardamos la imagen
    $response = imagejpeg($image, $destination, $quality);
    //Destruir imagen para liberar memoria
    imagedestroy($image);
    // Devolvemos si la imagen se guardo
    return $response;
}//comprimirImagen

//Tipo de moneda a prefijo
function num_letras($numero, $moneda){

    $flag="N";
    $entero="";
    $deci="";
    $moneda_prefijo = "";

    if($moneda=="MNX")
    {
        $moneda_prefijo=" PESOS ";
        $moneda_sufijo_2=" MN ";
    }

    if($moneda=="MN")
    {
        $moneda_prefijo=" PESOS ";
        $moneda_sufijo_2=" MN ";
    }

    if($moneda=="PESOS")
    {
        $moneda_prefijo=" PESOS ";
        $moneda_sufijo_2="MN ";
    }

    if($moneda=="USD")
    {
        $moneda_prefijo=" DOLARES ";
        $moneda_sufijo_2=" USD ";
    }

    if($moneda=="DOLARES")
    {
        $moneda_prefijo=" DOLARES ";
        $moneda_sufijo_2=" USD ";
    }

    for($i=0; $i<=strlen($numero);$i++)
    {
        $caracter=substr($numero,$i,1);
        if($caracter==".")
        {
            $flag="S";
        }
        else
        {
            if($flag=="N")
            {
                $entero.=$caracter;
            }
            else
            {
                if(strlen($deci)<=1)
                {
                    $deci.=$caracter;
                }
            }
        }
    }
    if($deci=="")
    {
        $deci="00";
    }
    else
    {
        if(strlen($deci)==1)
        {
            $deci.="0";
        }
    }
    //echo "Entero: ".$entero."<br>";
    $Letras = num_texto($entero);
    if($entero==1)
        $Letras = "(".$Letras." ".$moneda_prefijo.substr($deci,strlen($deci)-2,2)."/100 MN)";
    else
        $Letras = "(".$Letras." ".$moneda_prefijo.substr($deci,strlen($deci)-2,2)."/100 MN)";

    return $Letras;
}//num_letras

//Una cantidad da texto
function num_texto($numero){
    $texto="";
    $millones="";
    $miles="";
    $cientos="";
    $decimales="";
    $cadena="";
    $cadMillones="";
    $cadMiles="";
    $cadCientos="";

    $texto="                 ".$numero;
    $millones=substr($texto,strlen($texto)-9,3);
    $miles=substr($texto,strlen($texto)-6,3);
    $cientos=substr($texto,strlen($texto)-3,3);

    //return "Millones: ".$millones." Miles:".$miles." Cientos:".$cientos;

    $cadMillones = ConvierteCifra($millones, "1");
    $cadMiles = ConvierteCifra($miles, "1");
    $cadCientos = ConvierteCifra($cientos, "1");

    if(trim($cadMillones)!="")
    {
        if(trim($cadMillones)=="UN")
        {
            $cadena=$cadMillones." MILLON";
        }
        else
        {
            $cadena=$cadMillones." MILLONES";
        }
    }

    if(trim($cadMiles)!="")
    {
        $cadena.=$cadMiles." MIL";
    }

    if((trim($cadMiles.$cadCientos))=="UN")
    {
        $cadena.=" UNO";
    }
    else
    {
        if(trim($cadMiles).trim($cadCientos)=="000000")
        {
            $cadena.=" ".$cadCientos;
        }
        else
        {
            $cadena.=" ".$cadCientos;
        }
    }

    return $cadena;
}//num_texto

//ConvierteCifra
function ConvierteCifra($Texto,$SW){

    $Centena="";
    $Decena="";
    $Unidad="";
    $txtCentena="";
    $txtDecena="";
    $txtUnidad="";

    $Centena = substr($Texto, 0, 1);
    $Decena = substr($Texto, 1, 1);
    $Unidad = substr($Texto, 2, 1);

    //echo "Texto: ".$Texto."<br>";
    //echo "Longitud: ".strlen($Texto)."<br>";
    //echo $Centena."<br>";
    //echo $Decena."<br>";
    //echo $Unidad."<br>";

    switch ($Centena) {
        case "1":
            $txtCentena = "CIEN";
            if($Decena.$Unidad!="00")
            {
                $txtCentena = "CIENTO";
            }
            break;
        case "2":
            $txtCentena = "DOSCIENTOS";
            break;
        case "3":
            $txtCentena = "TRESCIENTOS";
            break;
        case "4":
            $txtCentena = "CUATROCIENTOS";
            break;
        case "5":
            $txtCentena = "QUINIENTOS";
            break;
        case "6":
            $txtCentena = "SEISCIENTOS";
            break;
        case "7":
            $txtCentena = "SETECIENTOS";
            break;
        case "8":
            $txtCentena = "OCHOCIENTOS";
            break;
        case "9":
            $txtCentena = "NOVECIENTOS";
            break;
    }

    switch ($Decena) {
        case "1":
            $txtDecena = "DIEZ";
            switch ($Unidad) {
                case "1":
                    $txtDecena = "ONCE";
                    break;
                case "2":
                    $txtDecena = "DOCE";
                    break;
                case "3":
                    $txtDecena = "TRECE";
                    break;
                case "4":
                    $txtDecena = "CATORCE";
                    break;
                case "5":
                    $txtDecena = "QUINCE";
                    break;
                case "6":
                    $txtDecena = "DIECISEIS";
                    break;
                case "7":
                    $txtDecena = "DIECISIETE";
                    break;
                case "8":
                    $txtDecena = "DIECIOCHO";
                    break;
                case "9":
                    $txtDecena = "DIECINUEVE";
                    break;
            }
            break;
        case "2":
            $txtDecena = "VEINTE";
            if($Unidad!="0")
            {
                $txtDecena = "VEINTI";
            }
            break;
        case "3":
            $txtDecena = "TREINTA";
            if($Unidad!="0")
            {
                $txtDecena = "TREINTA Y ";
            }
            break;
        case "4":
            $txtDecena = "CUARENTA";
            if($Unidad!="0")
            {
                $txtDecena = "CUARENTA Y ";
            }
            break;
        case "5":
            $txtDecena = "CINCUENTA";
            if($Unidad!="0")
            {
                $txtDecena = "CINCUENTA Y ";
            }
            break;
        case "6":
            $txtDecena = "SESENTA";
            if($Unidad!="0")
            {
                $txtDecena = "SESENTA Y ";
            }
            break;
        case "7":
            $txtDecena = "SETENTA";
            if($Unidad!="0")
            {
                $txtDecena = "SETENTA Y ";
            }
            break;
        case "8":
            $txtDecena = "OCHENTA";
            if($Unidad!="0")
            {
                $txtDecena = "OCHENTA Y ";
            }
            break;
        case "9":
            $txtDecena = "NOVENTA";
            if($Unidad!="0")
            {
                $txtDecena = "NOVENTA Y ";
            }
            break;
    }

    if($Decena!="1")
    {
        switch ($Unidad) {
            case "1":
                if($SW=="1")
                {
                    $txtUnidad = "UN";
                }
                else
                {
                    $txtUnidad = "UNO";
                }
                break;
            case "2":
                $txtUnidad = "DOS";
                break;
            case "3":
                $txtUnidad = "TRES";
                break;
            case "4":
                $txtUnidad = "CUATRO";
                break;
            case "5":
                $txtUnidad = "CINCO";
                break;
            case "6":
                $txtUnidad = "SEIS";
                break;
            case "7":
                $txtUnidad = "SIETE";
                break;
            case "8":
                $txtUnidad = "OCHO";
                break;
            case "9":
                $txtUnidad = "NUEVE";
                break;
        }
    }
    return $txtCentena." ".$txtDecena.$txtUnidad;
}//ConvierteCifra
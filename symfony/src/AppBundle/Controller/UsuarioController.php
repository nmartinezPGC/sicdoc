<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\JsonResponse;
//Importamos las Tablas a Relacionar
use BackendBundle\Entity\TblUsuarios;
use BackendBundle\Entity\TblEstados;
use BackendBundle\Entity\TblTipoUsuario;
use BackendBundle\Entity\TblTiposFuncionarios;
use BackendBundle\Entity\TblFuncionarios;
use BackendBundle\Entity\TblDepartamentosFuncionales;

/**
 * Description of UsuarioController
 *
 * @author Nahum Martinez
 */
class UsuarioController extends Controller{
    //put your code here
    
    
    /**
     * @Route("/new", name="new")
     * Creacion del Controlador: Usuarios
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00001
     */
    public function newAction(Request $request) {
        date_default_timezone_set('America/Tegucigalpa');
        //Instanciamos el Servicio Helpers
        $helpers = $this->get("app.helpers");
        
        $json = $request->get("json", null);
        $params = json_decode($json);
        
        //Array de Mensajes
        $data = $data = array(
                "status" => "error",                
                "code" => "400",                
                "msg" => "Usuario no creado, hay problemas en los datos, faltan campos por llenar !!"                
            ); 
        
        //Evaluamos el Json
        if ($json != null) {
            //Variables que vienen del Json ***********************************************
            //Seccion de Identificacion ***************************************************
            //El ID no se incluye; ya que es un campo Serial            
            $cod_usuario      = (isset($params->codUsuario)) ? $params->codUsuario : null;
            $iniciales        = (isset($params->inicialesUsuario)) ? $params->inicialesUsuario : null;
            $nombre1          = (isset($params->primerNombre) && ctype_alpha($params->primerNombre) ) ? $params->primerNombre : null;
            $nombre2          = (isset($params->segundoNombre) && ctype_alpha($params->segundoNombre) ) ? $params->segundoNombre : null;
            $apellido1        = (isset($params->primerApellido) && ctype_alpha($params->primerApellido) ) ? $params->primerApellido : null;
            $apellido2        = (isset($params->segundoApellido) && ctype_alpha($params->segundoApellido) ) ? $params->segundoApellido : null;
            $email            = (isset($params->emailUsuario)) ? $params->emailUsuario  : null;            
            //Seccion de Relaciones entre Tablas ********************************************************************
            $cod_estado           = (isset($params->idEstado)) ? $params->idEstado : null;
            $cod_tipo_funcionario = (isset($params->idTipoFuncionario)) ? $params->idTipoFuncionario : null;
            $cod_depto_funcional  = (isset($params->idDeptoFuncional)) ? $params->idDeptoFuncional : null;
            $cod_tipo_usuario     = (isset($params->idTipoUsuario)) ? $params->idTipoUsuario : null;                        
            //Datos de Bitacora *************************************************************************************
            $createdAt        = new \DateTime("now");
            $image            = "";
            $password         = (isset($params->passwordUsuairo)) ? $params->passwordUsuairo : null;
            $celular          = (isset($params->celularFuncionario)) ? $params->celularFuncionario : null;
            $telefono         = (isset($params->telefonoFuncionario)) ? $params->telefonoFuncionario : null;
            
            // Fechas Nulas
            $fecha_null = new \DateTime('2999-12-31');
            
            //Validamos el Email ************************************************************************************
            $emailConstraint = new Assert\Email();
            $emailConstraint->message = "El Email no es valido!!";
            
            $valid_email = $this->get("validator")->validate($email, $emailConstraint);
            //Entitie Manager Definition ****************************************************************************
            $em = $this->getDoctrine()->getManager();
            
            if ($email != null && count($valid_email) == 0 && $cod_usuario != null &&
                $password != null && $nombre1 != null && $apellido1 != null && $celular != 0 ){
                //Instanciamos la Entidad TblUsuario *****************************************                
                $usuario = new TblUsuarios();                
                //Seteamos los valores de Identificacion ***********************
                $usuario->setcodUsuario($cod_usuario);                
                $usuario->setNombre1Usuario($nombre1);
                $usuario->setNombre2Usuario($nombre2);
                $usuario->setApellido1Usuario($apellido1);
                $usuario->setApellido2Usuario($apellido2);
                $usuario->setEmailUsuario($email);
                $usuario->setFechaModificacion($fecha_null);
                
                //Seteamos los valores de Relaciones de Tablas *******************************
                //Instancia a la Tabla: TblEstados *****************************                
                $estados = $em->getRepository("BackendBundle:TblEstados")->findOneBy(
                            array(
                                "idEstado" => $cod_estado
                            )) ;
                
                $usuario->setIdEstado($estados);
                
                //Instancia a la Tabla: TblTipoFuncionarios ********************                
                $tipoFuncionario = $em->getRepository("BackendBundle:TblTiposFuncionarios")->findOneBy(
                            array(
                                "idTipoFuncionario" => $cod_tipo_funcionario
                            )) ;
                $usuario->setIdTipoFuncionario($tipoFuncionario); 
                
                //Instancia a la Tabla: TblDepartamentosFuncionales ************                
                $deptoFuncional = $em->getRepository("BackendBundle:TblDepartamentosFuncionales")->findOneBy(
                            array(
                                "idDeptoFuncional" => $cod_depto_funcional
                            )) ;
                $usuario->setIdDeptoFuncional($deptoFuncional);
                
                //Instancia a la Tabla: TblTipoUsuario *************************                
                $tipoUsuario = $em->getRepository("BackendBundle:TblTipoUsuario")->findOneBy(
                            array(
                                "idTipoUsuario" => $cod_tipo_usuario
                            )) ;
                $usuario->setIdTipoUsuario($tipoUsuario);
                
                //Seteamos el Resto de campos de la Tabla: TblUsuarios *********
                $usuario->setInicialesUsuario($iniciales);
                
                //Cifrar la Contraseña *****************************************
                $pwd = hash('sha256', $password);                
                $usuario->setPasswordUsuario($pwd); 
                
                // Imagen del usuario
                $usuario->setImagenUsuario("sreci.png");
                
                //Seteamos los valores de la Bitacora **************************
                $usuario->setFechaCreacion($createdAt);                
                //Verificacion del Codigo y Email en la Tabla: TblUsuarios *****                
                $isset_user_mail = $em->getRepository("BackendBundle:TblUsuarios")->findOneBy(
                        array(
                          "emailUsuario" => $email
                        ));
        
                //Verificacion del Codigo del Usuario **************************
                $isset_user_cod = $em->getRepository("BackendBundle:TblUsuarios")->findOneBy(
                        array(
                          "codUsuario" => $cod_usuario
                        ));
                
                //Verificamos que el retorno de la Funcion sea = 0 *************                
                if(count($isset_user_cod) == 0 && count($isset_user_mail) == 0){                    
                    $em->persist($usuario);                    
                    $em->flush();
                    
                    // Termina Tblusuarios *************************************
                    
                    
                    //Instanciamos la Entidad TblUsuario ***********************
                    // Objetivo: Igualar los usuarios a los Funcionarios *******
                    $funcionario = new TblFuncionarios();
                    
                    //Seteamos los valores de Identificacion *******************
                    $funcionario->setcodFuncionario($cod_usuario);                    
                    $funcionario->setNombre1Funcionario($nombre1);
                    $funcionario->setNombre2Funcionario($nombre2);
                    $funcionario->setApellido1Funcionario($apellido1);
                    $funcionario->setApellido2Funcionario($apellido2);
                    $funcionario->setCelularFuncionario($celular);
                    $funcionario->setTelefonoFuncionario($telefono);
                    $funcionario->setEmailFuncionario($email);  
                    
                    // Tablas relacionales *************************************
                    $funcionario->setIdTipoFuncionario($tipoFuncionario);
                    $funcionario->setIdDeptoFuncional($deptoFuncional);
                    
                    //Instancia a la Tabla: TblUsuarios ************************                
                    $id_user = $em->getRepository("BackendBundle:TblUsuarios")->findOneBy(
                        array(
                            "codUsuario" => $cod_usuario
                        )) ;
                    $funcionario->setIdUsuario($id_user);
                    
                    $em->persist($funcionario);                                        
                    $em->flush();
                    
                    // Termina TblFuncionarios *********************************                    
                    
                    
                    // Envio de Correo despues de la Grabacion de Datos
                    // *************************************************

                    // los Datos de envio de Mail **********************
                        //Creamos la instancia con la configuración 
                        $transport = \Swift_SmtpTransport::newInstance()
                           ->setHost('smtp.gmail.com')
                           ->setPort(587)
                           ->setEncryption('tls')                               
                           ->setStreamOptions(array(
                                        'ssl' => array(
                                            'allow_self_signed' => true, 
                                            'verify_peer' => false, 
                                            'verify_peer_name' => false
                                            )
                                        )
                                     )                                                                                                
                           ->setUsername( "correspondenciascpi@sreci.gob.hn" )
                           ->setPassword('Despachomcns');
                       //echo "Paso 1";
                       //Creamos la instancia del envío
                       $mailer = \Swift_Mailer::newInstance($transport);

                       //Creamos el mensaje
                       $mail = \Swift_Message::newInstance()
                           ->setSubject('Creacion de Usuario | SICDOC')                           
                           ->setFrom(array("correspondenciascpi@sreci.gob.hn" => "Administrador SICDOC" )) 
                           ->addCc('correspondenciascpi@sreci.gob.hn') 
                           ->setTo($email)                               
                           ->setBody(
                                $this->renderView(                               
                                    'Emails/newUser.html.twig',
                                    array( 'name' => $nombre1, 'apellidoOficio' => $apellido1,
                                        'fechaCreated' => date_format($createdAt, "Y-m-d"), 'userActual' => $email, 
                                        'passActual' => $password )
                                ), 'text/html' );                                

                        // Envia el Correo con todos los Parametros
                        $resuly = $mailer->send($mail);

                    // ***** Fin de Envio de Correo ********************
                    
                    //Seteamos el array de Mensajes a enviar *******************
                    $data = array(
                        "status" => "success",                
                        "code" => "200",                
                        "msg" => "El Usuario, " . " " . $nombre1 . " " . $apellido1 . " se ha creado satisfactoriamente."                 
                    );
                } else {
                    $data = array(
                        "status" => "error",                
                        "code" => "400",                
                        "msg" => "Error al registrar, el Usuario ya existe revise el "
                                . "EMail o el No. de Identidad !!"                
                    );
                }
            }
        }
        //Retorno de la Funcion ************************************************
        return $helpers->parserJson($data);
    } // FIN FND00001
    
    
    /**
     * @Route("/edit", name="edit")
     * Creacion del Controlador: Usuarios
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00002
     */
    public function editAction(Request $request) {
        date_default_timezone_set('America/Tegucigalpa');
        //Instanciamos el Servicio Helpers
        $helpers = $this->get("app.helpers");
        //Recoger el Hash
        //Recogemos el Hash y la Autrizacion del Mismo
        $hash = $request->get("authorization", null);
        //Se Chekea el Token
        $checkToken = $helpers->authCheck($hash);
        //Evalua que el Token sea True
        if($checkToken == true){
        //Ejecutamos todo el Codigo restante
        $identity = $helpers->authCheck($hash, true);    
        $em = $this->getDoctrine()->getManager();
        $usuario = $em->getRepository("BackendBundle:TblUsuarios")->findOneBy(
                array(
                    "idUsuario" => $identity->sub
            ));        
        $json = $request->get("json", null);
        $params = json_decode($json);
        
        //Array de Mensajes
        $data = $data = array(
                "status" => "error",                
                "code" => "400",                
                "msg" => "Usuario no actualizado, hay problemas en los Datos !!"                
            ); 
        
        //Evaluamos el Json
        if ($json != null) {
            //Variables que vienen del Json ***********************************************
            //Seccion de Identificacion ***************************************************
            //El ID no se incluye; ya que es un campo Serial            
            $cod_usuario = (isset($params->cod_usuario)) ? $params->cod_usuario : null;
            $iniciales = (isset($params->iniciales)) ? $params->iniciales : null;
            $nombre1 = (isset($params->nombre1) && ctype_alpha($params->nombre1) ) ? $params->nombre1 : null;
            $nombre2 = (isset($params->nombre2) && ctype_alpha($params->nombre2) ) ? $params->nombre2 : null;
            $apellido1 = (isset($params->apellido1) && ctype_alpha($params->apellido1) ) ? $params->apellido1 : null;
            $apellido2 = (isset($params->apellido2) && ctype_alpha($params->apellido2) ) ? $params->apellido2 : null;
            $email = (isset($params->email)) ? $params->email : null;            
            //Seccion de Relaciones entre Tablas ********************************************************************
            $cod_estado = (isset($params->cod_estado)) ? $params->cod_estado : null;
            $cod_tipo_funcionario = (isset($params->cod_tipo_funcionario)) ? $params->cod_tipo_funcionario : null;
            $cod_depto_funcional = (isset($params->cod_depto_funcional)) ? $params->cod_depto_funcional : null;
            $cod_tipo_usuario = (isset($params->cod_tipo_usuario)) ? $params->cod_tipo_usuario : null;                        
            //Datos de Bitacora *************************************************************************************
            $createdAt = new \DateTime("now");
            $image = null;
            $password = (isset($params->password)) ? $params->password : null;
            
            //Validamos el Email ************************************************************************************
            $emailConstraint = new Assert\Email();
            $emailConstraint->message = "El Email no es valido!!";
            
            $valid_email = $this->get("validator")->validate($email, $emailConstraint);
            //Entitie Manager Definition ****************************************************************************
            $em = $this->getDoctrine()->getManager();
            
            if ($email != null && count($valid_email) == 0 &&
                $nombre1 != null && $apellido1 != null ){
                //Instanciamos la Entidad TblUsuario *****************************************                
                //$usuario = new TblUsuarios();                
                //Seteamos los valores de Identificacion ***********************
                $usuario->setcodUsuario($cod_usuario);                
                $usuario->setNombre1Usuario($nombre1);
                $usuario->setNombre2Usuario($nombre2);
                $usuario->setApellido1Usuario($apellido1);
                $usuario->setApellido2Usuario($apellido2);
                $usuario->setEmailUsuario($email);
                //Seteamos los valores de Relaciones de Tablas *******************************
                //Instancia a la Tabla: TblEstados *****************************                
                $estados = $em->getRepository("BackendBundle:TblEstados")->findOneBy(
                            array(
                                "codEstado" => $cod_estado
                            )) ;
                $usuario->setCodEstado($estados);
                //Instancia a la Tabla: TblTipoFuncionarios ********************                
                $tipoFuncionario = $em->getRepository("BackendBundle:TblTiposFuncionarios")->findOneBy(
                            array(
                                "codTipoFuncionario" => $cod_tipo_funcionario
                            )) ;
                $usuario->setCodTipoFuncionario($tipoFuncionario);  
                //Instancia a la Tabla: TblDepartamentosFuncionales ************                
                $deptoFuncional = $em->getRepository("BackendBundle:TblDepartamentosFuncionales")->findOneBy(
                            array(
                                "codDeptoFuncional" => $cod_depto_funcional
                            )) ;
                $usuario->setCodDeptoFuncional($deptoFuncional);
                //Instancia a la Tabla: TblTipoUsuario *************************                
                $tipoUsuario = $em->getRepository("BackendBundle:TblTipoUsuario")->findOneBy(
                            array(
                                "codTipoUsuario" => $cod_tipo_usuario
                            )) ;
                $usuario->setCodTipoUsuario($tipoUsuario);
                //Seteamos el Resto de campos de la Tabla: TblUsuarios *********
                $usuario->setInicialesUsuario($iniciales);
                //Cifrar la Contraseña *****************************************
                if ($password != null) {                            
                    $pwd = hash('sha256', $password);                
                    $usuario->setPasswordUsuario($pwd);                
                }
                $usuario->setImagenUsuario($image);
                //Seteamos los valores de la Bitacora **************************
                $usuario->setFechaCreacion($createdAt);                
                //Verificacion del Codigo y Email en la Tabla: TblUsuarios *****                
                $isset_user_mail = $em->getRepository("BackendBundle:TblUsuarios")->findBy(
                        array(
                          "emailUsuario" => $email
                        ));
        
                //Verificacion del Codigo del Usuario **************************
                $isset_user_cod = $em->getRepository("BackendBundle:TblUsuarios")->findBy(
                        array(
                          "codUsuario" => $cod_usuario
                        ));
                //Verificamos que el retorno de la Funcion sea = 0 *************                
                if(count($isset_user_cod) == 0 || $identity->email == $email ){
                    $em->persist($usuario);
                    //$em->persist($estados);
                    //$em->persist($tipoUsuario);
                    //$em->persist($tiposFuncionarios);
                    //$em->persist($deptosFuncionales);                    
                    $em->flush();
                    //Seteamos el array de Mensajes a enviar *******************
                    $data = array(
                        "status" => "success",                
                        "code" => "200",                
                        "msg" => "Usuario actualizado !!"                
                    );
                } else {
                    $data = array(
                        "status" => "error",                
                        "code" => "400",                
                        "msg" => "Error al registrar, el Usuario ya existe !!"                
                    );
                }
            }
        } else {
            $data = array(
                "status" => "error",                
                "code" => "400",                
                "msg" => "Autorizacion de Token no valida !!"                
            );
        }
        }
        //Retorno de la Funcion ************************************************
        return $helpers->parserJson($data);
    } // FIN FND00002
    
    
    /**
     * @Route("/upload", name="upload")
     * Creacion del Controlador: Usuarios
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00003
     */
    public function uploadImageAction(Request $request) {
        date_default_timezone_set('America/Tegucigalpa');
        //Instanciamos el Servicio Helpers
        $helpers = $this->get("app.helpers");        
        //Recoger el Hash
        //Recogemos el Hash y la Autrizacion del Mismo
        $hash = $request->get("authorization", null);
        //Se Chekea el Token
        $checkToken = $helpers->authCheck($hash);
        //Evaluamos la Autoriuzacion del Token
        if($checkToken == true){
        //Ejecutamos todo el Codigo restante
        $identity = $helpers->authCheck($hash, true);    
        $em = $this->getDoctrine()->getManager();
        //Buscamos el registro por el Id de Usaurio
        $usuario = $em->getRepository("BackendBundle:TblUsuarios")->findOneBy(
            array(
               "idUsuario" => $identity->sub
            ));
            //Recoger el Fichero que viene por el POST y lo guardamos el HD
            $file = $request->files->get("image");
            //Se verifica que el fichero no venga Null
            if (!empty($file) && $file != null) {
                //Obtenemos la extencion del Fichero
                $ext = $file->guessExtension();
                //Comprobamos que la Extencion sea Aceptada
                if ($ext == "jpeg" || $ext == "jpg" || $ext == "png" || $ext == "gif") {                   
                    // Concatenmos al Nombre del Fichero la Fecha y la Extencion
                    $file_name = time().".".$ext;
                    //Movemos el Fichero
                    $file->move("uploads/users", $file_name);

                    //Seteamos el valor de la Imagen dentro de la Tabla:Tblusuarios+
                    $usuario->setImagenUsuario($file_name);
                    $em->persist($usuario);
                    $em->flush();
                
                    // Devolvemos el Mensaje de Array
                    $data = array(
                        "status" => "success",
                        "code" => 200,
                        "msg" => "Imagen for user uploaded success !!"
                    );
                } else {
                    // Devolvemos el Mensaje de Array, cuando la Imagen no sea valida
                    $data = array(
                        "status" => "error",
                        "code" => 400,
                        "msg" => "File not valid !!"
                    );
                }
            }else{
                $data = array(
                    "status" => "error",
                    "code" => 400,
                    "msg" => "Imagen not upload !!"
                );                
            }            
        }else{
            $data = array(
               "status" => "error",
               "code" => 400,
               "msg" => "Autorización no valida !!"
            );            
        }
        //Retorno de la Funcion ************************************************
        return $helpers->parserJson($data);
    } // FIN FND00003
    
    
    /**
     * @Route("/change-pass-user", name="change-pass-user")
     * Creacion del Controlador: Usuarios
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00004
     */
    public function changePassUserAction(Request $request) {
        date_default_timezone_set('America/Tegucigalpa');
        //Instanciamos el Servicio Helpers
        $helpers = $this->get("app.helpers");
        //Recoger el Hash
        //Recogemos el Hash y la Autrizacion del Mismo
        $hash = $request->get("authorization", null);
        //Se Chekea el Token
        $checkToken = $helpers->authCheck($hash);
        //Evalua que el Token sea True
        if($checkToken == true){
            //Ejecutamos todo el Codigo restante
            $identity = $helpers->authCheck($hash, true);    
            $em = $this->getDoctrine()->getManager();

            $json = $request->get("json", null);
            $params = json_decode($json);
        
        
            //Evaluamos el Json
            if ($json != null) {
                //Variables que vienen del Json ********************************
                //Seccion de Identificacion ************************************
                //El ID no se incluye; ya que es un campo Serial            
                $id_usuario = (isset($params->idUserChange)) ? $params->idUserChange : 0;

                // Password a Cambiar
                $password_actual   = (isset($params->passWordUserAct)) ? $params->passWordUserAct : null;
                $password_actual_sha   = (isset($params->passWordUserAct)) ? $params->passWordUserActSha : null;
                $password_new      = (isset($params->passWordUserNew)) ? $params->passWordUserNew : null;
                $password_confirm  = (isset($params->passWordUserConfirm)) ? $params->passWordUserConfirm : null;
                
                $modifyAt = new \DateTime("now");

                //Entitie Manager Definition ***********************************
                $em = $this->getDoctrine()->getManager();

                if ( $password_actual != null && $password_new != null && $id_usuario != 0 ){
                    //Instanciamos la Entidad TblUsuario *****************************************                
                    //$usuario = new TblUsuarios();                
                    //Seteamos los valores de Identificacion ***********************
                     $usuario = $em->getRepository("BackendBundle:TblUsuarios")->findOneBy(
                        array(
                            "idUsuario" => $id_usuario
                        ));   
                    
                    //Cifrar la Contraseña *************************************
                    if ( $password_actual != null ) {                            
                        $pwdAct = hash('sha256', $password_actual);
                        $pwdNew = hash('sha256', $password_new);
                        
                        $compa = strcmp($pwdAct, $password_actual_sha);
                        
                        //if( $password_actual_sha != $pwdAct ){
                        if( $compa != 0 ){
                            // Evaluamos que los Password sean Distintos
                            if( $password_actual_sha != $pwdNew  ){
                                //Actualizmos el password
                                $usuario->setPasswordUsuario($pwdNew);
                                $usuario->setFechaModificacion($modifyAt);
                                //$usuario->setImagenUsuario($image);
                                //Seteamos los valores de la Bitacora **************                            

                                // Relizamos la persistencia de Datos de las Comunicaciones Detalle
                                $em->persist($usuario); 

                                //Realizar la actualizacion en el storage de la BD
                                $em->flush();
                                // Envio de Correo despues de la Grabacion de Datos
                                // *************************************************
                                //Instanciamos de la Clase TblFuncionarios, para Obtener
                                // los Datos de envio de Mail **********************

                                // Parametros de Salida
                                // los Datos de envio de Mail **************************
                                $usuario_asignado_send = $em->getRepository("BackendBundle:TblFuncionarios")->findOneBy(
                                    array(
                                        "idFuncionario" => $id_usuario                
                                    ));
                                // Parametros de Salida
                                $mailSend = $usuario_asignado_send->getEmailFuncionario() ; // Get de mail de Funcionario Asignado
                                $nombreSend = $usuario_asignado_send->getNombre1Funcionario() ; // Get de Nombre de Funcionario Asignado
                                $apellidoSend = $usuario_asignado_send->getApellido1Funcionario() ; // Get de Apellido de Funcionario Asignado

                                    //Creamos la instancia con la configuración 
                                    $transport = \Swift_SmtpTransport::newInstance()
                                       ->setHost('smtp.gmail.com')
                                       ->setPort(587)
                                       ->setEncryption('tls')                               
                                       ->setStreamOptions(array(
                                                    'ssl' => array(
                                                        'allow_self_signed' => true, 
                                                        'verify_peer' => false, 
                                                        'verify_peer_name' => false
                                                        )
                                                    )
                                                 )                                   
                                       //->setUsername("nahum.sreci@gmail.com")                                   
                                       //->setPassword('1897Juve');                                   
                                       ->setUsername( "correspondenciascpi@sreci.gob.hn" )
                                       ->setPassword('Despachomcns');
                                   //echo "Paso 1";
                                   //Creamos la instancia del envío
                                   $mailer = \Swift_Mailer::newInstance($transport);

                                   //Creamos el mensaje
                                   $mail = \Swift_Message::newInstance()
                                       ->setSubject('Cambio de Contraseña | SICDOC')
                                       //->setFrom(array($identity->email => $identity->nombre . " " .  $identity->apellido ))
                                       //->setFrom(array("nahum.sreci@gmail.com" => "Administrador SICDOC" )) 
                                       ->setFrom(array("correspondenciascpi@sreci.gob.hn" => "Administrador SICDOC" )) 
                                       ->setTo($mailSend)                               
                                       ->setBody(
                                            $this->renderView(
                                            // app/Resources/views/Emails/registration.html.twig
                                                'Emails/changePassWord.html.twig',
                                                array( 'name' => $nombreSend, 'apellidoOficio' => $apellidoSend,
                                                       'passActual' => $password_new ,
                                                       'fechaChange' => date_format($modifyAt, "Y/m/d") )
                                            ), 'text/html' );                                

                                    // Envia el Correo con todos los Parametros
                                    $resuly = $mailer->send($mail);

                                // ***** Fin de Envio de Correo ********************

                                //Seteamos el array de Mensajes a enviar ***********
                                $data = array(
                                    "status" => "success",                
                                    "code" => "200",                
                                    "msg" => "Usuario actualizado, la contraseña se Actualizo pronto recibiras un correo de confirmación !! "
                                );
                            } else {
                                //Seteamos el array de Mensajes a enviar ***********
                                $data = array(
                                    "status" => "success",                
                                    "code" => "400",                
                                    "msg" => "El Usuario no se ha actualizado, la contraseña Actual es igual a la Anterior !!"                
                                );
                            } // FIN | Condicion    
                        } else { 
                            $data = array(
                                "status" => "error",                
                                "code" => "400",                
                                "msg" => "La Contraseña Actual Ingresada no es igual que la registrada en la BD !!"                
                            );
                        } // FIN | Cifrado
                        
                    } // FIN | Passwor Null                                                                                         
                } else {
                    $data = array(
                        "status" => "error",                
                        "code" => "400",                
                        "msg" => "Error al cambiar la contraseña, faltan campos por ingresar !!"                
                    );            
                }
            } else {
                    $data = array(
                        "status" => "error",                
                        "code" => "400",                
                        "msg" => "Los parametros enviados son Nulos, por favor verificar la información, para continuar !!"                
                    );
            }
        } else {
            $data = array(
                "status" => "error",                
                "code" => "400",                
                "msg" => "Autorizacion de Token no valida !!"                
            );
        }
        //Retorno de la Funcion ************************************************
        return $helpers->parserJson($data);
    } // FIN FND00004
    
    
    /**
     * Creacion del Controlador: Transforma Fechas Time Stamp
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00005
     */
    public function convertirFechasTimeStampAction( $fecha_time_stamp )
    {
        // Recibe los Parametros de la Funcion, en un Formato TimeStamp ********
        $fecha_time_stamp_In = $fecha_time_stamp;
        
        // Decodificamos el Json con su Campo de Fechas | es nesesario que se **
        // haga la Consulta a la BD por medio de Doctrine( getFechaConsulta())**
        //$fecha_transformar = json_encode($correspondenciaAsigna->getFechaMaxEntrega()->getTimestamp(), true );
        
        // Itaciamosla fecha y le Seteamos el valor TimeStamp del campo de la **
        // Consulta del Doctrine (getFechaConsulta()) **************************
        $fecha_set = new \DateTime();
        $fecha_set->setTimestamp( $fecha_time_stamp_In );
        
        //Salida del Formato a la fecha Convertida *****************************
        $fecha_salida =  $fecha_set->format('Y-m-d');
        
        return $fecha_salida;
    } // FIN | FND00005
    
}

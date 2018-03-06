<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\JsonResponse;

//Importamos las Tablas a Relacionar
use BackendBundle\Entity\TblCorrespondenciaEnc;
use BackendBundle\Entity\TblUsuarios;
use BackendBundle\Entity\TblCambioFechas;

use BackendBundle\Entity\TblEstados;


use Swift_MessageAcceptanceTest;

/**
 * Description of MantCambioFechasController
 *
 * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
 * @since 1.0
 * Fecha: 2017-11-20
 * Objetivo: Controlador de las Solicitudes de Cambio de
 * Fechas de Entrega
 */
class MantCambioFechasController extends Controller {
    
    
    /**
     * @Route("/mantenimientos/solicitud-cambio-fecha", name="solicitud-cambio-fecha")
     * Creacion del Controlador: Solicitud de Cambio de Fecha de Entrega
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00001
     */
    public function solicitudCambioFechaAction(Request $request, $search = null )
    {
        //Instanciamos el Servicio Helpers
        date_default_timezone_set('America/Tegucigalpa');
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");
        
        //Recogemos el Hash y la Autorizacion del Mismo        
        $hash = $request->get("authorization", null);
        //Se Chekea el Token
        $checkToken = $helpers->authCheck($hash);        
        
        // Valida el Token de la Peticion
        if($checkToken == true){
           // Seteo  de la Identidad del Ususario, con las variables del
           // Localsotrage
           $identity = $helpers->authCheck($hash, true); 
        
           // Parametros enviados por el Json
           $json = $request->get("json", null);
                      
            //Comprobamos que Json no es Null
            if ($json != null) {
               // Decodificamos el Json
               $params = json_decode($json);
               
               //Parametros a Convertir                           
               //Datos generales de la Tabla ***********************************                
               //Variables que vienen del Json *********************************
                $cod_comunicacion     = ($params->codCorrespondencia != null) ? $params->codCorrespondencia : null ;
                $cod_referenciaSreci  = ($params->codCorrespondenciaExt != null) ? $params->codCorrespondenciaExt : null ;
                
                $tema_correspondencia  = ($params->temaComunicacion != null) ? $params->temaComunicacion : null ;
                $desc_correspondencia  = ($params->descComunicacion != null) ? $params->descComunicacion : null ;
                
                // Observacion de Solicitu de Cambio
                $justificacion_comunicacion = ($params->justifiacionCom != null) ? $params->justifiacionCom : null ;
                
                // Fecha de Solicitud de Cambio
                $fecha_ingreso        = new \DateTime('now');
                
                $fecha_max_solicitada = ($params->fechaMaxEntrega != null) ? $params->fechaMaxEntrega : null ;
                $fecha_max_solicitada_new = ($params->fechaMaxEntregaNew != null) ? $params->fechaMaxEntregaNew : null ;
                               
                
                // Llamamo a la Funcion Interna para que nos convierta *
                // La Fecha a Calendario Gregoriano ********************

                //Incrementando 2 dias
                //$fecha_max_solicitada = strtotime($fecha_max_solicitada."+ 2 days");
                
                // Ejecucion de la Funcion *****************************
                $fecha_maxima_entrega_convert = $this->convertirFechasTimeStampAction( $fecha_max_solicitada );
                
                
                // Envio por Json el Codigo de Usuario | Buscar en la Tabla: TblUsuarios
                $cod_usuario          = $identity->sub;
                
                // Envio por Json el Codigo de Usuario | Buscar en la Tabla: TblUsuarios
                $cod_usuario_creador  = ($params->idUserCreador != null) ? $params->idUserCreador : null ;
                $email_usuario_creador  = ($params->emailUserCreador != null) ? $params->emailUserCreador : null ;
                
                // Fecha de Entrega Actual
                //$fecha_entrega_act    = ($params->fechaEntregaActual != null) ? $params->fechaEntregaActual : null ;
                //$fecha_creacion       = ($params->fechaCreacion != null) ? $params->fechaCreacion : null ;
                
                
                //Evalua si la Informacion de los Parametros es Null
                if( $cod_comunicacion != null ){
                    // Evalua si se Ingreso la jsutificacion de la Solicitud ******
                    if( $justificacion_comunicacion != null ){
                        // Creacion del Metodo Create Query Builder | hace mas Efectiva la *****
                        // Busqueda a la BD  ***************************************************        
                        $em = $this
                                ->getDoctrine()
                                ->getManager();
                        
                        
                  
                        //Verificacion del Codigo de la Correspondencia *******************
                        $isset_corresp_cod = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findOneBy(
                            array(
                                "codCorrespondenciaEnc" => $cod_comunicacion
                        ));
                        
                        //Verificamos que el retorno de la Funcion sea > 0 *****
                        // Encontro los Datos de la Comunicacion Solicitada ****
                        if( count($isset_corresp_cod) > 0 ){
                           
                            
                            $isset_cambio_fehas = $em->getRepository("BackendBundle:TblCambioFechas")->findOneBy(
                                array(
                                    "codCorrespondencia" => $cod_comunicacion
                            ));
                            
                            
                            // Validamos las veces que se ha Solictado el Cambio
                            if( count( $isset_cambio_fehas ) > 5 ) {
                                // Mensaje de Rechazo de Soliciutd de Cambio ***
                                
                                $data = array(
                                    "status" => "error",                                
                                    "code"   => 400, 
                                    "msg"    => "Ya son: " . count($isset_cambio_fehas) . " las veces que has solicitado el cambio.",
                                    "data"   => $isset_cambio_fehas
                                ); 
                            }else{     
                                // Ingresa en la BD la Solicitud ***************
                                //Seteo de Datos Generales de la tabla: TblCambioFechas
                                $soliciutdCambioFechaNew = new TblCambioFechas();
                                
                                $soliciutdCambioFechaNew->setCodCorrespondencia( $cod_comunicacion );                    
                    
                                $soliciutdCambioFechaNew->setJustificacionSolicitud( $justificacion_comunicacion );                    
                                //$soliciutdCambioFechaNew->setObservaciones($observacion_correspondencia);                    
                                
                                // Seteo de las Fechas de la Solicitud
                                $soliciutdCambioFechaNew->setFechaCreacionSolicitud( $fecha_ingreso );
                                //$soliciutdCambioFechaNew->setFechaIngreso( $fecha_ingreso );
                                
                                //Instanciamos de la Clase TblFuncionarios******
                                $usuarioCreador = $em->getRepository("BackendBundle:TblFuncionarios")->findOneBy(
                                    array(
                                       "idUsuario" => $cod_usuario_creador                        
                                    ));                    
                                $soliciutdCambioFechaNew->setIdUsuarioCreacion( $usuarioCreador ); //Set de Id de Usuario Creador
                                
                                
                                //Instanciamos de la Clase TblFuncionarios *****
                                $usuarioSolicitud = $em->getRepository("BackendBundle:TblFuncionarios")->findOneBy(
                                    array(
                                       "idUsuario" => $cod_usuario                         
                                    ));                 
                                $soliciutdCambioFechaNew->setIdUsuarioSolicitud( $usuarioSolicitud ); //Set de Id de Usuario Solicitud
                                
                                // Datos de las Relacones de la tabla
                                //Instanciamos de la Clase TblEstados **********                              
                                $estado = $em->getRepository("BackendBundle:TblEstados")->findOneBy(                            
                                    array(
                                       "idEstado" => 9
                                    ));                    
                                $soliciutdCambioFechaNew->setIdEstadoSolicitud( $estado ); //Set de Codigo de Estados 
                                
                                //Realizar la Persistencia de los Datos y enviar a la BD
                                $em->persist( $soliciutdCambioFechaNew );
                                
                                //Realizar la actualizacion en el storage de la BD
                                $em->flush();
                                
                                // Envio de Correo despues de la Grabacion de Datos
                                // *****************************************************
                                //Instanciamos de la Clase TblFuncionarios, para Obtener
                                // los Datos de envio de Mail **************************
                                $usuario_asignado_send = $em->getRepository("BackendBundle:TblFuncionarios")->findOneBy(
                                    array(
                                        "idUsuario" => $cod_usuario                
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
                                       ->setUsername( "correspondenciascpi@sreci.gob.hn" )
                                       ->setPassword('Despachomcns')
                                       ->setTimeout(180);
                                   //echo "Paso 1";
                                   //Creamos la instancia del envío
                                   $mailer = \Swift_Mailer::newInstance($transport);

                                   //Creamos el mensaje
                                   $mail = \Swift_Message::newInstance()
                                       ->setSubject('Solicitud de Cambio de Fecha | SICDOC')                                       
                                       ->setFrom(array("correspondenciascpi@sreci.gob.hn" => "Administrador SICDOC " ))                                       
                                       //->setTo($mailSend)                                                        
                                       ->setTo("correspondenciascpi@sreci.gob.hn")
                                       ->addCc($email_usuario_creador) 
                                       ->setBody(
                                            $this->renderView(                                            
                                                'Emails/sendMailSolicitudCambFechas.html.twig',
                                                array( 'name' => $nombreSend, 'apellidoOficio' => $apellidoSend,
                                                       'oficioExtNo' => $cod_referenciaSreci, 'oficioInNo' => $cod_comunicacion,
                                                       'temaOficio' => $tema_correspondencia, 'descOficio' => $desc_correspondencia,
                                                       'obsComunicacion' => $justificacion_comunicacion,
                                                       'fechaMaxEntrega' => $fecha_maxima_entrega_convert, 
                                                       'fechaMaxEntregaNew' => $fecha_max_solicitada_new,  )
                                            ), 'text/html' );                                                   

                                    // Envia el Correo con todos los Parametros
                                    $resuly = $mailer->send( $mail );

                                // ***** Fin de Envio de Correo ************************
                                                               
                                // Mensaje de Respuesta
                                $data = array(
                                    "status" => "success",                                
                                    "code"   => 200, 
                                    "msg"    => "La Solicitud fue creada de manera exitosa",
                                    "data"   => count( $isset_cambio_fehas )
                                );
                            }                                                                                                                
                        }else{
                            $data = array(
                                "status" => "error",                                
                                "code"   => 400, 
                                "msg"   => "Error al buscar, No existe una comunicación con este código, ". $cod_comunicacion . 
                                           " por favor, valide que los Datos sean correctos !!"
                            ); 
                        }
                    }else{
                      //Array de Mensajes
                        $data = array(
                           "status" => "error",                       
                           "code"   => 400, 
                           "msg"   => "Solicitud no creada, falta ingresar el Justificacion del cambio de Fecha de Entrega !!"
                        );  
                    }                    
                }else{
                   //Array de Mensajes
                    $data = array(
                       "status" => "error",                       
                       "code"   => 400, 
                       "msg"   => "Solicitud no creada, falta ingresar el Código de la Comunicación !!"
                    );
                }                
            }else{
             //Array de Mensajes
             $data = array(
                "status" => "error",
                "desc"   => "Eror al Enviar el Json, el Json no ha sido enviado",
                "code"   => 400, 
                "msg"   => "Solicitud no creada, falta ingresar los parametros !!"
             );
            }         
        }else{
            $data = array(
                "status" => "error",
                "desc"   => "El Token, es invalido",    
                "code" => "400",                
                "msg" => "Autorizacion de Token no valida !!"                
            );
        }
        // Retornamos la Data
        return $helpers->parserJson($data);
    }//FIN | FND00001
    
    
    /**
     * @Route("/mantenimientos/cambio-fecha", name="cambio-fecha")
     * Creacion del Controlador: Solicitud de Cambio de Fecha de Entrega
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00002
     */
    public function cambioFechaAction(Request $request)
    {
        //Instanciamos el Servicio Helpers
        date_default_timezone_set('America/Tegucigalpa');
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");
        
        //Recogemos el Hash y la Autorizacion del Mismo        
        $hash = $request->get("authorization", null);
        //Se Chekea el Token
        $checkToken = $helpers->authCheck($hash);        
        
        // Valida el Token de la Peticion
        if($checkToken == true){
           // Seteo  de la Identidad del Ususario, con las variables del
           // Localsotrage
           $identity = $helpers->authCheck($hash, true); 
        
           // Parametros enviados por el Json
           $json = $request->get("json", null);
                      
            //Comprobamos que Json no es Null
            if ($json != null) {
               // Decodificamos el Json
               $params = json_decode($json);
               
               //Parametros a Convertir                           
               //Datos generales de la Tabla ***********************************                
               //Variables que vienen del Json *********************************
                $cod_comunicacion     = ($params->codCorrespondencia != null) ? $params->codCorrespondencia : null ;
                $cod_referenciaSreci  = ($params->codCorrespondenciaExt != null) ? $params->codCorrespondenciaExt : null ;
                
                $tema_correspondencia  = ($params->temaComunicacion != null) ? $params->temaComunicacion : null ;
                $desc_correspondencia  = ($params->descComunicacion != null) ? $params->descComunicacion : null ;
                
                // Observacion de Solicitu de Cambio
                $justificacion_comunicacion = ($params->justifiacionCom != null) ? $params->justifiacionCom : null ;
                
                // Fecha de Solicitud de Cambio
                $fecha_ingreso        = new \DateTime('now');
                
                $fecha_max_solicitada = ($params->fechaMaxEntrega != null) ? $params->fechaMaxEntrega : null ;
                
                // Envio por Json el Codigo de Usuario | Buscar en la Tabla: TblUsuarios
                $cod_usuario          = $identity->sub;
                
                // Envio por Json el Codigo de Usuario | Buscar en la Tabla: TblUsuarios
                $cod_usuario_creador  = ($params->idUserCreador != null) ? $params->idUserCreador : null ;
                
                // Fecha de Entrega Actual
                //$fecha_entrega_act    = ($params->fechaEntregaActual != null) ? $params->fechaEntregaActual : null ;
                //$fecha_creacion       = ($params->fechaCreacion != null) ? $params->fechaCreacion : null ;
                
                
                //Evalua si la Informacion de los Parametros es Null
                if( $cod_comunicacion != null ){
                    // Evalua si se Ingreso la jsutificacion de la Solicitud ******
                    if( $justificacion_comunicacion != null ){                                                
                        //Verificacion del Codigo de la Correspondencia *******************
                        $isset_corresp_cod = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findOneBy(
                            array(
                                "codCorrespondenciaEnc" => $cod_comunicacion
                        ));
                                               
                        //$cod_usuario_creador = $isset_corresp_cod->getIdUsuario();
                        
                        //Verificamos que el retorno de la Funcion sea > 0 *****
                        // Encontro los Datos de la Comunicacion Solicitada ****
                        if( count($isset_corresp_cod) > 0 ){                           
                            // Comprobacion de las Veces de una misma Solciitud
                            $isset_cambio_fehas = $em->getRepository("BackendBundle:TblCambioFechas")->findOneBy(
                                array(
                                    "codCorrespondencia" => $cod_comunicacion
                            ));
                                                        
                            // Validamos las veces que se ha Solictado el Cambio
                            if( count( $isset_cambio_fehas ) > 5 ) {
                                // Mensaje de Rechazo de Soliciutd de Cambio ***
                                
                                $data = array(
                                    "status" => "error",                                
                                    "code"   => 400, 
                                    "msg"    => "Ya son: " . count($isset_cambio_fehas) . " las veces que has solicitado el cambio.",
                                    "data"   => $isset_cambio_fehas
                                ); 
                            }else{     
                                // Ingresa en la BD la Solicitud ***************
                                //Seteo de Datos Generales de la tabla: TblCambioFechas
                                $soliciutdCambioFechaNew = new TblCambioFechas();
                                
                                $soliciutdCambioFechaNew->setCodCorrespondencia( $cod_comunicacion );                    
                    
                                $soliciutdCambioFechaNew->setJustificacionSolicitud( $justificacion_comunicacion );                    
                                //$soliciutdCambioFechaNew->setObservaciones($observacion_correspondencia);                    
                                
                                // Seteo de las Fechas de la Solicitud
                                $soliciutdCambioFechaNew->setFechaCreacionSolicitud( $fecha_ingreso );
                                //$soliciutdCambioFechaNew->setFechaIngreso( $fecha_ingreso );
                                
                                //Instanciamos de la Clase TblFuncionarios******
                                $usuarioCreador = $em->getRepository("BackendBundle:TblFuncionarios")->findOneBy(
                                    array(
                                       "idUsuario" => $cod_usuario_creador                        
                                    ));                    
                                $soliciutdCambioFechaNew->setIdUsuarioCreacion( $usuarioCreador ); //Set de Id de Usuario Creador
                                
                                
                                //Instanciamos de la Clase TblFuncionarios *****
                                $usuarioSolicitud = $em->getRepository("BackendBundle:TblFuncionarios")->findOneBy(
                                    array(
                                       "idUsuario" => $cod_usuario                         
                                    ));                 
                                $soliciutdCambioFechaNew->setIdUsuarioSolicitud( $usuarioSolicitud ); //Set de Id de Usuario Solicitud
                                
                                // Datos de las Relacones de la tabla
                                //Instanciamos de la Clase TblEstados **********                              
                                $estado = $em->getRepository("BackendBundle:TblEstados")->findOneBy(                            
                                    array(
                                       "idEstado" => 9
                                    ));                    
                                $soliciutdCambioFechaNew->setIdEstadoSolicitud( $estado ); //Set de Codigo de Estados 
                                
                                //Realizar la Persistencia de los Datos y enviar a la BD
                                $em->persist( $soliciutdCambioFechaNew );
                                
                                //Realizar la actualizacion en el storage de la BD
                                $em->flush();
                                
                                
                                // Envio de Correo despues de la Grabacion de Datos
                                // *****************************************************
                                //Instanciamos de la Clase TblFuncionarios, para Obtener
                                // los Datos de envio de Mail **************************
                                $usuario_asignado_send = $em->getRepository("BackendBundle:TblFuncionarios")->findOneBy(
                                    array(
                                        "idUsuario" => $cod_usuario                
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
                                       ->setUsername( "correspondenciascpi@sreci.gob.hn" )
                                       ->setPassword('Despachomcns')
                                       ->setTimeout(180);
                                   //echo "Paso 1";
                                   //Creamos la instancia del envío
                                   $mailer = \Swift_Mailer::newInstance($transport);

                                   //Creamos el mensaje
                                   $mail = \Swift_Message::newInstance()
                                       ->setSubject('Solicitud de Cambio de Fecha | SICDOC')                                       
                                       ->setFrom(array("correspondenciascpi@sreci.gob.hn" => "Administrador SICDOC " ))                                       
                                       ->setTo($mailSend) 
                                       //->addCc($address)
                                       ->setBody(
                                            $this->renderView(                                            
                                                'Emails/sendMailSolicitudCambFechas.html.twig',
                                                array( 'name' => $nombreSend, 'apellidoOficio' => $apellidoSend,
                                                       'oficioExtNo' => $cod_referenciaSreci, 'oficioInNo' => $cod_comunicacion,
                                                       'temaOficio' => $tema_correspondencia, 'descOficio' => $desc_correspondencia,
                                                       'obsComunicacion' => $justificacion_comunicacion,
                                                       'fechaMaxEntrega' => $fecha_max_solicitada  )
                                            ), 'text/html' );                                                   

                                    // Envia el Correo con todos los Parametros
                                    $resuly = $mailer->send( $mail );

                                // ***** Fin de Envio de Correo ************************
                                                               
                                // Mensaje de Respuesta
                                $data = array(
                                    "status" => "success",                                
                                    "code"   => 200, 
                                    "msg"    => "La Solicitud fue creada de manera exitosa.",
                                    "data"   => count( $isset_cambio_fehas )
                                );
                            }                                                                                                                
                        }else{
                            $data = array(
                                "status" => "error",                                
                                "code"   => 400, 
                                "msg"   => "Error al buscar, No existe una comunicación con este código, ". $cod_comunicacion . 
                                           " por favor, valide que los Datos sean correctos !!"
                            ); 
                        }
                    }else{
                      //Array de Mensajes
                        $data = array(
                           "status" => "error",                       
                           "code"   => 400, 
                           "msg"   => "Solicitud no creada, falta ingresar el Justificacion del cambio de Fecha de Entrega !!"
                        );  
                    }                    
                }else{
                   //Array de Mensajes
                    $data = array(
                       "status" => "error",                       
                       "code"   => 400, 
                       "msg"   => "Solicitud no creada, falta ingresar el Código de la Comunicación !!"
                    );
                }                
            }else{
             //Array de Mensajes
             $data = array(
                "status" => "error",
                "desc"   => "Eror al Enviar el Json, el Json no ha sido enviado",
                "code"   => 400, 
                "msg"   => "Solicitud no creada, falta ingresar los parametros !!"
             );
            }         
        }else{
            $data = array(
                "status" => "error",
                "desc"   => "El Token, es invalido",    
                "code" => "400",                
                "msg" => "Autorizacion de Token no valida !!"                
            );
        }
        // Retornamos la Data
        return $helpers->parserJson($data);
    }//FIN | FND00002
    
    
    /**
     * @Route("/mantenimientos/busca-comunicacion", name="busca-comunicacion")
     * Creacion del Controlador: Busqueda de la Comunicacion
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00003
     */
    public function buscaComunicacionAction(Request $request)
    {
        //Instanciamos el Servicio Helpers
        date_default_timezone_set('America/Tegucigalpa');
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");
        
        //Recogemos el Hash y la Autorizacion del Mismo        
        $hash = $request->get("authorization", null);
        //Se Chekea el Token
        $checkToken = $helpers->authCheck($hash);        
        
        // Valida el Token de la Peticion
        if($checkToken == true){
           // Seteo  de la Identidad del Ususario, con las variables del
           // Localsotrage
           $identity = $helpers->authCheck($hash, true); 
        
           // Parametros enviados por el Json
           $json = $request->get("json", null);
                      
            //Comprobamos que Json no es Null
            if ($json != null) {
               // Decodificamos el Json
               $params = json_decode($json);
               
               $em = $this
                ->getDoctrine()
                ->getManager();
               
               //Parametros a Convertir                           
               //Datos generales de la Tabla ***********************************                
               //Variables que vienen del Json *********************************
               //$cod_comunicacion = $request->query->get("codCorrespondencia ", null);
               $cod_comunicacion     = ($params->codCorrespondencia != null) ? $params->codCorrespondencia : null ;                             
                
                //Evalua si la Informacion de los Parametros es Null
                if( $cod_comunicacion != null ){
                    //Verificacion del Codigo de la Correspondencia *******************
                    /*$isset_corresp_cod = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findOneBy(
                        array(
                             "codCorrespondenciaEnc" => $cod_comunicacion
                    ));*/
                    
                    
                    $query = $em->createQuery('SELECT enc.idCorrespondenciaEnc, enc.codCorrespondenciaEnc, enc.codReferenciaSreci, '
                                    . 'enc.descCorrespondenciaEnc, enc.temaComunicacion, tcom.idTipoComunicacion, tdoc.idTipoDocumento,  '
                                    ."DATE_SUB(enc.fechaIngreso, 0, 'DAY') AS fechaIngreso, DATE_SUB(enc.fechaMaxEntrega, 0, 'DAY') AS fechaMaxEntrega, "
                                    . 'p.idUsuario, p.emailUsuario '
                                    . 'FROM BackendBundle:TblCorrespondenciaEnc enc '
                                    . 'INNER JOIN BackendBundle:TblUsuarios p WITH  p.idUsuario = enc.idUsuario '
                                    . 'INNER JOIN BackendBundle:TblCorrespondenciaDet d WITH d.idCorrespondenciaEnc = enc.idCorrespondenciaEnc '
                                    . 'INNER JOIN BackendBundle:TblTipoComunicacion tcom WITH tcom.idTipoComunicacion = enc.idTipoComunicacion '
                                    . 'INNER JOIN BackendBundle:TblTipoDocumento tdoc WITH tdoc.idTipoDocumento = enc.idTipoDocumento '
                                    //. 'INNER JOIN BackendBundle:TblCorrespondenciaEnc c WITH c.idCorrespondenciaEnc = doc.idCorrespondenciaEnc '
                                    . 'WHERE enc.codCorrespondenciaEnc = :codCorrespondenciaEnc '                                    
                                    . 'ORDER BY enc.codCorrespondenciaEnc, enc.idCorrespondenciaEnc ASC') 
                    ->setParameter('codCorrespondenciaEnc', $cod_comunicacion ) ;
                    
                    $isset_corresp_cod = $query->getResult();
                    
                    //Verificamos que el retorno de la Funcion sea > 0 *****
                    // Encontro los Datos de la Comunicacion Solicitada ****
                    if( count($isset_corresp_cod) > 0 ){ 
                        // Asignacion de los valores de la Consulta
                        //$cod_usuario_creador = $isset_corresp_cod->getIdUsuario();
                        //Array de Mensajes
                        $data = array(
                           "status" => "success",                       
                           "code"   => 200, 
                           "msg"    => "Datos encontrados !!",
                           "data"   => $isset_corresp_cod
                        );  
                    } else {
                        //Array de Mensajes
                        $data = array(
                           "status" => "error",                       
                           "code"   => 400, 
                           "msg"    => "Datos no encontrados, no existe información con este código de Comunicación !!"                           
                        );
                    }                 
                }else{
                    //Array de Mensajes
                    $data = array(
                       "status" => "error",                      
                       "code"   => 400, 
                       "msg"   => "Falta ingresar el código de la comunicación!!"
                    );
                }                                   
            }else{
             //Array de Mensajes
             $data = array(
                "status" => "error",                
                "code"   => 400, 
                "msg"   => "Datos no encontrados, falta ingresar los parametros !!"
             );
            }         
        }else{
            $data = array(
                "status" => "error",
                "desc"   => "El Token, es invalido",    
                "code" => "400",                
                "msg" => "Autorizacion de Token no valida !!"                
            );
        }
        // Retornamos la Data
        return $helpers->parserJson($data);
    }//FIN | FND00003
    
    
    /**
     * Creacion del Controlador: Transforma Fechas Time Stamp
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00004
     */
    public function convertirFechasTimeStampAction( $fecha_time_stamp )
    {
        //Instanciamos el Servicio Helpers
        date_default_timezone_set('America/Tegucigalpa');
        // Recibe los Parametros de la Funcion, en un Formato TimeStamp ********
        // Incidencia de Menos un Dia | Se suma 86400 que es un Dia en
        // TimeStamp
        $fecha_time_stamp_In = $fecha_time_stamp + 86400;               
        
        // Itaciamosla fecha y le Seteamos el valor TimeStamp del campo de la **
        // Consulta del Doctrine (getFechaConsulta()) **************************
        $fecha_set = new \DateTime();
        $fecha_set->setTimestamp( $fecha_time_stamp_In );
        
        //Salida del Formato a la fecha Convertida *****************************
        $fecha_salida =  $fecha_set->format('Y-m-d');
        
        return $fecha_salida;
    } // FIN | FND00004
    
}

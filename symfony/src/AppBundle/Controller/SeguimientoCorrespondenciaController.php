<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\JsonResponse;
//Importamos las Tablas a Relacionar
use BackendBundle\Entity\TblCorrespondenciaEnc;
use BackendBundle\Entity\TblCorrespondenciaDet;
use BackendBundle\Entity\TblUsuarios;
use BackendBundle\Entity\TblDocumentos;
use BackendBundle\Entity\TblInstituciones;
use BackendBundle\Entity\TblEstados;
use BackendBundle\Entity\TblDireccionesSreci;
use BackendBundle\Entity\TblSecuenciales;
use Swift_MessageAcceptanceTest;


/********************************************************************
 * Description of SeguimientoCorrespondenciaController              *
 * Seguimiento de Correspondencia en la Tabla: TblCorrespondenciaEnc*
 * @author Nahum Martinez <nahum.sreci@gmail.com>                   *
 * @category Correspondencia/Seguimiento                            *
 * @version 1.0                                                     *
 * Fecha: 18-09-2017                                                *
 ********************************************************************/
class SeguimientoCorrespondenciaController extends Controller {
    // Seccion de variables Privadas    
    
    /**
     * @Route("/asignar-oficio", name="asignar-oficio")
     * Creacion del Controlador: Asignar Oficio
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00001
     */
    public function asignarOficioAction(Request $request)
    {
        //Instanciamos el Servicio Helpers
        $helpers = $this->get("app.helpers");
        //Recoger el Hash
        //Recogemos el Hash y la Autorizacion del Mismo        
        $hash = $request->get("authorization", null);
        //Se Chekea el Token
        $checkToken = $helpers->authCheck($hash);
        
        //Evalua que el Token sea True
        if($checkToken == true){
            // Variable del LocalStorage
            $identity = $helpers->authCheck($hash, true);
            
            //Convertimos los Parametros POSt a Json
            $json = $request->get("json", null);
           
            //Comprobamos que Json no es Null
            if ($json != null) {
                //Decodificamos el Json
                $params = json_decode($json);

                //Parametros a Convertir                           
                //Datos generales de la Tabla
                $id_usuario_modifca        = $identity->sub;
                $codgio_oficio_interno     = ($params->codOficioInterno != null) ? $params->codOficioInterno : null ;
                $codgio_oficio_externo     = ($params->codOficioExterno != null) ? $params->codOficioExterno : null ;
                $id_funcionario_asignado   = ($params->idFuncionarioAsigmado != null) ? $params->idFuncionarioAsigmado : null ;
                $nombre1_funcionario_asignado   = ($params->nombre1FuncionarioAsigmado != null) ? $params->nombre1FuncionarioAsigmado : null ;
                $nombre2_funcionario_asignado   = ($params->nombre2FuncionarioAsigmado != null) ? $params->nombre2FuncionarioAsigmado : null ;
                $apellido1_funcionario_asignado = ($params->apellido1FuncionarioAsigmado != null) ? $params->apellido1FuncionarioAsigmado : null ;
                $apellido2_funcionario_asignado = ($params->apellido2FuncionarioAsigmado != null) ? $params->apellido2FuncionarioAsigmado : null ;
                
                $estado_asignado = ($params->idEstadoAsigna != null) ? $params->idEstadoAsigna : null ;
                                
                $fecha_modifcacion        = new \DateTime('now');
                
                //Evaluamos que los Campos del Json  no sean Null ni 0. ********
                if($codgio_oficio_interno != null && $codgio_oficio_externo != null && $id_funcionario_asignado != 0 )
                {
                    // Actualizamos el Funcionario que estara a Cargo de dar ***
                    // Seguimiento y Respuesta al Oficio Ingresado *************
                    //La condicion fue Exitosa
                    //Instancia del Doctrine
                    $em = $this->getDoctrine()->getManager();
                    
                    //Seteo de Datos Generales de la tabla: TblCorrespondenciaEnc
                    // Localizamos el Oficio que se va a Asignar
                    // Verificacion del Codigo de la Correspondenia ************
                    $correspondenciaAsigna = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findOneBy(
                        array(
                          "codCorrespondenciaEnc" => $codgio_oficio_interno
                        ));
                    
                    $correspondenciaAsigna->setFechaModificacion( $fecha_modifcacion );
                    
                    
                    // Verificacion del Estado de la Correspondenia ************
                    $estadoAsigna = $em->getRepository("BackendBundle:TblEstados")->findOneBy(
                        array(
                          "idEstado" => $estado_asignado
                        ));
                    
                    $correspondenciaAsigna->setIdestado( $estadoAsigna );
                    
                    //$correspondenciaAsigna->setIdEstado( 3 );
                    
                    // ---------------------------------------------------------
                    //Instanciamos de la Clase TblFuncionarios
                    $funcionario_asignado = $em->getRepository("BackendBundle:TblFuncionarios")->findOneBy(
                        array(
                           "idFuncionario" => $id_funcionario_asignado                
                        ));                    
                    $correspondenciaAsigna->setIdFuncionarioAsignado( $funcionario_asignado ); //Set de Codigo de Funcionario Asignado
                    
                    
                    //Verificacion del Codigo de la Correspondenia *******************
                    $isset_corresp_cod = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findBy(
                        array(
                          "codCorrespondenciaEnc" => $codgio_oficio_interno
                        ));
                    
                    //Verificacion del Codigo de Referencia de la Correspondenia *******************
                    $isset_referencia_cod = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findBy(
                        array(
                          "codReferenciaSreci" => $codgio_oficio_externo
                        ));
    
                    
                    //Verificamos que el retorno de la Funcion sea = 0 ********* 
                    if(count($isset_corresp_cod) > 0 && count($isset_referencia_cod) > 0 ){
                        
                        //Realizar la Persistencia de los Datos y enviar a la BD
                        $em->persist($correspondenciaAsigna);
                        
                        //Realizar la actualizacion en el storage de la BD
                        $em->flush();
                        
                        // FIN de Actualizacion a Tabla Encabezados de Comunicacion
                        
                        // Verificacion del Codigo de la Correspondenia ********
                        // Detalle TblCorrespondenciaDet  **********************
                        $correspondenciaDetEncAsigna = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findOneBy(
                            array(
                                "codCorrespondenciaEnc" => $codgio_oficio_interno
                            ));
                        
                        
                        // Busca el Id del Oficio Detalle **********************
                        // Primero Buscamos el idCorrespondenciaEnc de la Tabla*
                        // Padre para Ubicarlo en la Tabla Hijo ****************
                        $correspondenciaDetAsigna = $em->getRepository("BackendBundle:TblCorrespondenciaDet")->findOneBy(
                            array(
                                "idCorrespondenciaEnc" => $correspondenciaDetEncAsigna
                            ));
                        
                        // Empieza la Actualizacion
                        $correspondenciaDetAsigna->setIdFuncionarioAsignado( $funcionario_asignado ); //Set de Codigo de Funcionario Asignado                        
                        
                        //Realizar la Persistencia de los Datos y enviar a la BD
                        $em->persist($correspondenciaDetAsigna);
                        
                        //Realizar la actualizacion en el storage de la BD
                        $em->flush();
                        
                        // FIN de Actualizacion a Tabla Detalle de Comunicacion
                        
                        
                        // Llamamo a la Funcion Interna para que nos convierta *
                        // La Fecha a Calendario Gregoriano ********************
                        $fecha_maxima_entrega_time_stamp = json_encode($correspondenciaAsigna->getFechaMaxEntrega()->getTimestamp(), true ); 
                        // Ejecucion de la Funcion *****************************
                        $fecha_maxima_entrega_convert = $this->convertirFechasTimeStampAction( $fecha_maxima_entrega_time_stamp );
                                                
                        $fecha_creacion_time_stamp  = json_encode( $correspondenciaAsigna->getFechaIngreso()->getTimestamp(), true );
                        $fecha_creacion_convert  = $this->convertirFechasTimeStampAction( $fecha_creacion_time_stamp );
                                                                       
                        // Fin de la Funcion ( convertirFechasTimeStampAction )* 
                        // *****************************************************
                        
                        // Parametros del Oficio nesearios para enviar por *****
                        // Mail ************************************************
                        $tema_correspondencia = $correspondenciaAsigna->getTemaComunicacion();
                        $desc_correspondencia = $correspondenciaAsigna->getDescCorrespondenciaEnc();
                        
                        // Parametros de Salida que Utiliza el Correo
                        //Instanciamos de la Clase TblFuncionarios, para Obtener
                        // los Datos de envio de Mail **************************
                            $funcionario_asignado = $em->getRepository("BackendBundle:TblFuncionarios")->findOneBy(
                            array(
                                "idFuncionario" => $identity->sub                
                            ));
                        $mailSend = $funcionario_asignado->getEmailFuncionario() ; // Get de mail de Funcionario Asignado
                        $nombreSend = $funcionario_asignado->getNombre1Funcionario() ; // Get de Nombre de Funcionario Asignado
                        $apellidoSend = $funcionario_asignado->getApellido1Funcionario() ; // Get de Apellido de Funcionario Asignado
                        
                        // Fin de Envio de Parametros para el Mail *************
                                                
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
                               //->setUsername( $identity->email )
                               ->setUsername( "nahum.sreci@gmail.com" )
                               //->setUsername( 'gcallejas.sreci@gmail.com')
                               ->setPassword('1897Juve');
                               //->setPassword('gec2017*');
                           //echo "Paso 1";
                           //Creamos la instancia del envío
                           $mailer = \Swift_Mailer::newInstance($transport);
                           
                           //Creamos el mensaje
                           $mail = \Swift_Message::newInstance()
                               ->setSubject('Asignación de Oficio | SICDOC')
                               ->setFrom(array($identity->email => $nombreSend . " " .  $apellidoSend ))
                               ->setTo($mailSend)                               
                               ->setBody(
                                    $this->renderView(
                                    // Plantilla HTML que sirve para el envio de los Correos
                                        'Emails/sendMailFuncionario.html.twig',
                                        array( 'name' => $nombreSend, 'apellidoOficio' => $apellidoSend,
                                               'oficioExtNo' => $codgio_oficio_externo, 'oficioInNo' => $codgio_oficio_interno,
                                               'temaOficio' => $tema_correspondencia, 'descOficio' => $desc_correspondencia,
                                               'fechaIngresoOfi' => strval($fecha_creacion_convert), 
                                               'fechaMaxOfi' => strval($fecha_maxima_entrega_convert) )
                                    ), 'text/html' );                           
                           
                            /*// validamos que se adjunta pdf
                            if( $pdf_send != null ){
                              $target_path1 = "uploads/correspondencia/correspondencia_" . date('Y-m-d') . "/" . $pdf_send . "-" .date('Y-m-d'). ".pdf";                            
                              //$target_path1 = "COM-IN-OFI-11-2017-09-12.pdf";                            
                              $mail->attach(\Swift_Attachment::fromPath($target_path1));                                
                            }*/
                                 
                            // Envia el Correo con todos los Parametros
                            $resuly = $mailer->send($mail);
                                                  
                        // ***** Fin de Envio de Correo ************************                        
                        
                        
                        //Array de Mensajes
                        $data = array(
                            "status" => "success", 
                            "code"   => 200, 
                            "msg"    => "El Oficio: " . $codgio_oficio_interno . " se ha Asignado al Funcinario: " . 
                                        $nombre1_funcionario_asignado . ", " .  $apellido1_funcionario_asignado . ""
                                      . " con fecha de entrega el : " . $fecha_maxima_entrega_convert ,
                            "data"   => $correspondenciaAsigna
                        );                        
                    } else{
                        $data = array(
                            "status" => "error",
                            "desc"   => "No existe un codigo",
                            "code"   => 400, 
                            "msg"   => "Error al asignar, no existe un  el Oficio con este código, ". $codgio_oficio_interno . 
                                       " por favor ingrese otro !!"
                        );                       
                    } // Fin de Busqueda del Oficio que se esta Asignado
                } else{
                        $data = array(
                            "status" => "error",
                            "desc"   => "Falta Informacion",
                            "code"   => 400, 
                            "msg"   => "Falta Ingresar información para poder continuar !!"
                        );                       
                }//Finaliza el Bloque de la validadacion de la Data en la Tabla
            } else {
                    //Array de Mensajes | Campos que Faltan del Json
                    $data = array(
                       "status" => "error",
                       "desc"   => "Eror al Enviar el Json, el Json no ha sido enviado",
                       "code"   => 400, 
                       "msg"   => "Oficio no asignado, falta ingresar parametros, revisar la información ingresada !!"
                    );
            } // Evalua el Json que no sea Null            
        } else {
            $data = array(
                "status" => "error",
                "desc"   => "El Token, es invalido",    
                "code" => "400",                
                "msg" => "Autorizacion de Token no valida !!"                
            );
        } // FIN | Token
        //Retorno de la Funcion ************************************************
        return $helpers->parserJson($data);
    } // FIN | FND00001
    
    
    
    /**
     * Creacion del Controlador: Transforma Fechas Time Stamp
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00002
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
    } // FIN | FND00002
    
    
    /**
     * @Route("/finalizar-oficio-asignado", name="finalizar-oficio-asignado")
     * Creacion del Controlador: Asignar Oficio
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00003
     */
    public function finalizarOficioAction(Request $request)
    {
        //Instanciamos el Servicio Helpers
        $helpers = $this->get("app.helpers");
        //Recoger el Hash
        //Recogemos el Hash y la Autorizacion del Mismo        
        $hash = $request->get("authorization", null);
        //Se Chekea el Token
        $checkToken = $helpers->authCheck($hash);
        
        //Evalua que el Token sea True
        if($checkToken == true){
            // Variable del LocalStorage
            $identity = $helpers->authCheck($hash, true);
            
            //Convertimos los Parametros POSt a Json
            $json = $request->get("json", null);
           
            //Comprobamos que Json no es Null
            if ($json != null) {
                //Decodificamos el Json
                $params = json_decode($json);

                //Parametros a Convertir                           
                //Datos generales de la Tabla
                $id_usuario_modifica       = $identity->sub;
                $new_secuencia             = ($params->secuenciaComunicacionDet != null) ? $params->secuenciaComunicacionDet : null ;
                $new_cod_correspondencia_det   = ($params->codCorrespondenciaDet != null) ? $params->codCorrespondenciaDet : null ;  
                
                $codgio_oficio_interno     = ($params->codOficioInterno != null) ? $params->codOficioInterno : null ;
                $codgio_oficio_externo     = ($params->codOficioExterno != null) ? $params->codOficioExterno : null ;
                $codgio_oficio_respuesta   = ($params->codOficioRespuesta != null) ? $params->codOficioRespuesta : null ;
                $descripcion_oficio        = ($params->descripcionOficio != null) ? $params->descripcionOficio : null ;
                $actividad_oficio          = ($params->actividadOficio != null) ? $params->actividadOficio : null ;
                
                $nombre1_funcionario_asignado   = ($params->nombre1FuncionarioAsigmado != null) ? $params->nombre1FuncionarioAsigmado : null ;
                $nombre2_funcionario_asignado   = ($params->nombre2FuncionarioAsigmado != null) ? $params->nombre2FuncionarioAsigmado : null ;
                $apellido1_funcionario_asignado = ($params->apellido1FuncionarioAsigmado != null) ? $params->apellido1FuncionarioAsigmado : null ;
                $apellido2_funcionario_asignado = ($params->apellido2FuncionarioAsigmado != null) ? $params->apellido2FuncionarioAsigmado : null ;
                
                $estado_asignado                = ($params->idEstadoAsigna != null) ? $params->idEstadoAsigna : null ;
                                
                $fecha_finalizacion             = new \DateTime('now');
                
                //Evaluamos que los Campos del Json  no sean Null ni 0. ********
                if($codgio_oficio_interno != null && $codgio_oficio_externo != null && $id_usuario_modifica != 0 && 
                   $descripcion_oficio != null && $actividad_oficio != null)
                {
                    // Actualizamos el Funcionario que estara a Cargo de dar ***
                    // Seguimiento y Respuesta al Oficio Ingresado *************
                    //La condicion fue Exitosa
                    //Instancia del Doctrine
                    $em = $this->getDoctrine()->getManager();
                    
                    // ---------------------------------------------------------
                    //Seteo de Datos Generales de la tabla: TblCorrespondenciaEnc
                    // Localizamos el Oficio que se va a Finalizar
                    
                    // 1 ) Verificacion del Codigo de la Correspondenia ********                    
                    $correspondenciaAsigna = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findOneBy(
                        array(
                          "codCorrespondenciaEnc" => $codgio_oficio_interno
                        ));
                    
                    $correspondenciaAsigna->setFechaFinalizacion( $fecha_finalizacion );                    
                    
                    // 2 ) Verificacion del Estado de la Correspondenia ********
                    $estadoAsigna = $em->getRepository("BackendBundle:TblEstados")->findOneBy(
                        array(
                          "idEstado" => $estado_asignado
                        ));                    
                    $correspondenciaAsigna->setIdestado( $estadoAsigna ); // Id = 5 ( Finalizado )
                                        
                    // 3 ) Verificacion del Codigo de la Correspondenia ********
                    $isset_corresp_cod = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findBy(
                        array(
                          "codCorrespondenciaEnc" => $codgio_oficio_interno
                        ));
                    
                    // 4 ) Verificacion del Codigo de Referencia de la *********
                    //     Correspondenia  *************************************
                    $isset_referencia_cod = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findBy(
                        array(
                          "codReferenciaSreci" => $codgio_oficio_externo
                        ));
    
                    
                    //Verificamos que el retorno de la Funcion sea = 0 ********* 
                    if(count($isset_corresp_cod) > 0 && count($isset_referencia_cod) > 0 ){
                        // Verificacion del Codigo de la Correspondenia ********
                        // Detalle TblCorrespondenciaDet  **********************
                        $correspondenciaDetEncAsigna = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findOneBy(
                            array(
                                "codCorrespondenciaEnc" => $codgio_oficio_interno
                            ));                        
                        
                        // Busca el Id del Oficio Detalle **********************
                        // Primero Buscamos el idCorrespondenciaEnc de la Tabla*
                        // Padre para Ubicarlo en la Tabla Hijo ****************
                        //Seteo de Datos Generales de la tabla: TblCorrespondenciaEnc
                        $correspondenciaDetAsigna = new TblCorrespondenciaDet();
                        
                        //Instanciamos de la Clase TblSecuenciales
                        //Seteo del nuevo secuencial de la tabla: TblSecuenciales
                        $secuenciaNew = new TblSecuenciales();
                        
                        // Busqueda del Codigo de la Secuencia a Actualizar | Correspondencia Enc
                        $secuenciaNew = $em->getRepository("BackendBundle:TblSecuenciales")->findOneBy(                            
                            array(
                                "codSecuencial"  => "COM-IN-DET-OFI"
                            ));                    
                        $secuenciaNew->setValor2($new_secuencia); //Set de valor2 de Secuencia de Oficios
                        
                        //Realizar la Persistencia de los Datos y enviar a la BD
                                                
                        $em->persist($secuenciaNew);
                        //Realizar la actualizacion en el storage de la BD
                        $em->flush();
                        
                        // Consulta a los Datos Anteriores del Detalle *********
                        /*$correspondenciaDetConsulta = $em->getRepository("BackendBundle:TblCorrespondenciaDet")->findOneBy(
                            array(
                                "idCorrespondenciaEnc" => $correspondenciaDetEncAsigna
                            ));
                        */
                        // Empieza la Actualizacion
                        $correspondenciaDetAsigna->setCodCorrespondenciaDet( $new_cod_correspondencia_det ); //Set de Descripcion de Correspondencia
                        $correspondenciaDetAsigna->setDescCorrespondenciaDet( $descripcion_oficio ); //Set de Descripcion de Correspondencia
                        $correspondenciaDetAsigna->setActividadRealizar( $actividad_oficio ); //Set de Actividad de Correspondencia
                        $correspondenciaDetAsigna->setFechaIngreso( $correspondenciaAsigna->getFechaIngreso() ); //Set de Actividad de Correspondencia
                        $correspondenciaDetAsigna->setFechaSalida( $fecha_finalizacion ); //Set de Actividad de Correspondencia
                        $correspondenciaDetAsigna->setIdCorrespondenciaEnc( $correspondenciaAsigna ); //Set de Id de Correspondenica Enc
                        $correspondenciaDetAsigna->setCodOficioRespuesta( $codgio_oficio_respuesta ); //Set de Cod Oficio Respuesta
                        
                        // Obtenemos ls Datos del Usuario
                        // Detalle TblUsuarios  **********************
                        $id_usuario_creador = $em->getRepository("BackendBundle:TblUsuarios")->findOneBy(
                            array(
                                "idUsuario" => $identity->sub
                            ));
                        $correspondenciaDetAsigna->setIdUsuario( $id_usuario_creador ); //Set de Id de Usuario Creador
                        $correspondenciaDetAsigna->setCodReferenciaSreci( $correspondenciaAsigna->getCodReferenciaSreci() ); //Set de Id de Usuario Creador
                        $correspondenciaDetAsigna->setIdFuncionarioAsignado( $correspondenciaAsigna->getIdFuncionarioAsignado() ); //Set de Id de Funcionario Asignado
                        
                        $correspondenciaDetAsigna->setIdEstado( $estadoAsigna ); //Set de Estado de Correspondencia | Id = 5 ( Finalizado )
                        $correspondenciaDetAsigna->setCodOficioRespuesta( $codgio_oficio_respuesta ); //Set de Oficio Respuesta
                        
                        //Realizar la Persistencia de los Datos y enviar a la BD
                        // Detalle de la Comunicacion Principal
                        $em->persist($correspondenciaDetAsigna);
                        
                        // Secuencia Actualizada + 1 
                        //$em->persist($secuenciaNew);
                        
                        // Encabezado de la Comunicacion Principal
                        $em->persist($correspondenciaAsigna);
                        
                        //Realizar la actualizacion en el storage de la BD
                        $em->flush();
                        
                        // FIN de Actualizacion a Tabla Detalle de Comunicacion,
                        // Secuenciales y Encabezados Comunicacion *************
                        
                        
                        // Llamamo a la Funcion Interna para que nos convierta *
                        // La Fecha a Calendario Gregoriano ********************
                        
                            $fecha_maxima_entrega_time_stamp = json_encode($correspondenciaAsigna->getFechaMaxEntrega()->getTimestamp(), true ); 
                        // Ejecucion de la Funcion *****************************
                            $fecha_maxima_entrega_convert = $this->convertirFechasTimeStampAction( $fecha_maxima_entrega_time_stamp );
                                                
                            $fecha_creacion_time_stamp  = json_encode( $correspondenciaAsigna->getFechaIngreso()->getTimestamp(), true );
                            $fecha_creacion_convert  = $this->convertirFechasTimeStampAction( $fecha_creacion_time_stamp );
                                                                       
                        // Fin de la Funcion ( convertirFechasTimeStampAction )* 
                        // *****************************************************
                        
                        // Parametros del Oficio nesearios para enviar por *****
                        // Mail ************************************************
                            $tema_correspondencia = $correspondenciaAsigna->getTemaComunicacion();
                            $desc_correspondencia = $correspondenciaAsigna->getDescCorrespondenciaEnc();
                        
                        // Parametros de Salida que Utiliza el Correo
                            //Instanciamos de la Clase TblFuncionarios, para Obtener
                        // los Datos de envio de Mail **************************
                            $funcionario_asignado = $em->getRepository("BackendBundle:TblFuncionarios")->findOneBy(
                            array(
                                "idFuncionario" => $identity->sub                
                            ));
                            $mailSend = $funcionario_asignado->getEmailFuncionario() ; // Get de mail de Funcionario Asignado
                            $nombreSend = $funcionario_asignado->getNombre1Funcionario() ; // Get de Nombre de Funcionario Asignado
                            $apellidoSend = $funcionario_asignado->getApellido1Funcionario() ; // Get de Apellido de Funcionario Asignado
                         
                        
                        // Fin de Envio de Parametros para el Mail *************
                                                
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
                               //->setUsername( $identity->email )
                               ->setUsername( "nahum.sreci@gmail.com" )                               
                               ->setPassword('1897Juve');                               
                           //echo "Paso 1";
                           //Creamos la instancia del envío
                           $mailer = \Swift_Mailer::newInstance($transport);
                           
                           //Creamos el mensaje
                           $mail = \Swift_Message::newInstance()
                               ->setSubject('Asignación de Oficio | SICDOC')
                               ->setFrom(array($identity->email => $nombreSend . " " .  $apellidoSend ))
                               ->setTo($mailSend)                               
                               ->setBody(
                                    $this->renderView(
                                    // Plantilla HTML que sirve para el envio de los Correos
                                        'Emails/sendMailFuncionario.html.twig',
                                        array( 'name' => $nombreSend, 'apellidoOficio' => $apellidoSend,
                                               'oficioExtNo' => $codgio_oficio_externo, 'oficioInNo' => $codgio_oficio_interno,
                                               'temaOficio' => $tema_correspondencia, 'descOficio' => $desc_correspondencia,
                                               'fechaIngresoOfi' => strval($fecha_creacion_convert), 
                                               'fechaMaxOfi' => strval($fecha_maxima_entrega_convert) )
                                    ), 'text/html' );   
                                                
                           
                            // validamos que se adjunta pdf
                            if( $pdf_send != null ){
                              $target_path1 = "uploads/correspondencia/correspondencia_" . date('Y-m-d') . "/" . $pdf_send . "-" .date('Y-m-d'). ".pdf";                            
                              //$target_path1 = "COM-IN-OFI-11-2017-09-12.pdf";                            
                              $mail->attach(\Swift_Attachment::fromPath($target_path1));                                
                            }
                                 
                            // Envia el Correo con todos los Parametros
                        //  $resuly = $mailer->send($mail);
                                                  
                        // ***** Fin de Envio de Correo ************************                        
                        
                        
                        //Array de Mensajes
                        $data = array(
                            "status" => "success", 
                            "code"   => 200, 
                            "msg"    => "El Oficio: " . $codgio_oficio_interno . " se ha Finalizado por el Funcionario: " . 
                                        $nombre1_funcionario_asignado . ", " .  $apellido1_funcionario_asignado . ""
                                      . " con fecha de finzalizacion el : " . $fecha_maxima_entrega_convert ,
                            "data"   => $correspondenciaAsigna
                        );                        
                    } else{
                        $data = array(
                            "status" => "error",
                            "desc"   => "No existe un codigo",
                            "code"   => 400, 
                            "msg"   => "Error al asignar, no existe un  el Oficio con este código, ". $codgio_oficio_interno . 
                                       " por favor ingrese otro !!"
                        );                       
                    } // Fin de Busqueda del Oficio que se esta Asignado
                } else{
                        $data = array(
                            "status" => "error",
                            "desc"   => "Falta Informacion",
                            "code"   => 400, 
                            "msg"   => "Falta Ingresar información para poder continuar !!"
                        );                       
                }//Finaliza el Bloque de la validadacion de la Data en la Tabla
            } else {
                    //Array de Mensajes | Campos que Faltan del Json
                    $data = array(
                       "status" => "error",
                       "desc"   => "Eror al Enviar el Json, el Json no ha sido enviado",
                       "code"   => 400, 
                       "msg"   => "Oficio no asignado, falta ingresar parametros, revisar la información ingresada !!"
                    );
            } // Evalua el Json que no sea Null            
        } else {
            $data = array(
                "status" => "error",
                "desc"   => "El Token, es invalido",    
                "code" => "400",                
                "msg" => "Autorizacion de Token no valida !!"                
            );
        } // FIN | Token
        //Retorno de la Funcion ************************************************
        return $helpers->parserJson($data);
    } // FIN | FND00003
    
    
    /**
     * @Route("/creacion-oficio-asignado", name="creacion-oficio-asignado")
     * Creacion del Controlador: Crear Oficio Respuesta
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00004
     */
    public function creacionOficioRespAction(Request $request)
    {
        //Instanciamos el Servicio Helpers
        $helpers = $this->get("app.helpers");
        //Recoger el Hash
        //Recogemos el Hash y la Autorizacion del Mismo        
        $hash = $request->get("authorization", null);
        //Se Chekea el Token
        $checkToken = $helpers->authCheck($hash);
        
        //Evalua que el Token sea True
        if($checkToken == true){
            // Variable del LocalStorage
            $identity = $helpers->authCheck($hash, true);
            
            //Convertimos los Parametros POSt a Json
            $json = $request->get("json", null);
           
            //Comprobamos que Json no es Null
            if ($json != null) {
                //Decodificamos el Json
                $params = json_decode($json);

                //Parametros a Convertir                           
                //Datos generales de la Tabla
                $id_usuario_modifica       = $identity->sub;
                $new_secuencia             = ($params->secuenciaComunicacionNewOfi != null) ? $params->secuenciaComunicacionNewOfi : null ;
                $act_secuencia             = ($params->secuenciaComunicacionNewOfiAct != null) ? $params->secuenciaComunicacionNewOfiAct : null ;
                $new_cod_correspondencia_det   = ($params->codCorrespondenciaNewOfi != null) ? $params->codCorrespondenciaNewOfi : null ;  
                
                $codgio_oficio_interno     = ($params->codOficioInterno != null) ? $params->codOficioInterno : null ;
                $codgio_oficio_externo     = ($params->codOficioExterno != null) ? $params->codOficioExterno : null ;
                $codgio_oficio_respuesta   = ($params->codOficioRespuesta != null) ? $params->codOficioRespuesta : null ;
                $descripcion_oficio        = ($params->descripcionOficio != null) ? $params->descripcionOficio : null ;
                $actividad_oficio          = ($params->actividadOficio != null) ? $params->actividadOficio : null ;
                
                $nombre1_funcionario_asignado   = ($params->nombre1FuncionarioAsigmado != null) ? $params->nombre1FuncionarioAsigmado : null ;
                $nombre2_funcionario_asignado   = ($params->nombre2FuncionarioAsigmado != null) ? $params->nombre2FuncionarioAsigmado : null ;
                $apellido1_funcionario_asignado = ($params->apellido1FuncionarioAsigmado != null) ? $params->apellido1FuncionarioAsigmado : null ;
                $apellido2_funcionario_asignado = ($params->apellido2FuncionarioAsigmado != null) ? $params->apellido2FuncionarioAsigmado : null ;
                
                $estado_asignado                = ($params->idEstadoAsigna != null) ? $params->idEstadoAsigna : null ;
                                
                $fecha_finalizacion             = new \DateTime('now');
                
                //Evaluamos que los Campos del Json  no sean Null ni 0. ********
                if($codgio_oficio_interno != null && $codgio_oficio_externo != null && $id_usuario_modifica != 0 && 
                   $descripcion_oficio != null)
                {
                    // Actualizamos el Funcionario que estara a Cargo de dar ***
                    // Seguimiento y Respuesta al Oficio Ingresado *************
                    //La condicion fue Exitosa
                    //Instancia del Doctrine
                    $em = $this->getDoctrine()->getManager();
                    
                    // ---------------------------------------------------------
                    //Seteo de Datos Generales de la tabla: TblCorrespondenciaEnc
                    // Localizamos el Oficio que se va a Finalizar
                    
                    // 1 ) Verificacion del Codigo de la Correspondenia ********                    
                    $correspondenciaAsigna = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findOneBy(
                        array(
                          "codCorrespondenciaEnc" => $codgio_oficio_interno
                        ));
                    
                    $correspondenciaAsigna->setFechaFinalizacion( $fecha_finalizacion );                    
                    
                    // 2 ) Verificacion del Estado de la Correspondenia ********
                    $estadoAsigna = $em->getRepository("BackendBundle:TblEstados")->findOneBy(
                        array(
                          "idEstado" => $estado_asignado
                        ));                    
                    $correspondenciaAsigna->setIdestado( $estadoAsigna ); // Id = 8 ( Respuesta )
                                        
                    // 3 ) Verificacion del Codigo de la Correspondenia ********
                    $isset_corresp_cod = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findBy(
                        array(
                          "codCorrespondenciaEnc" => $codgio_oficio_interno
                        ));
                    
                    // 4 ) Verificacion del Codigo de Referencia de la *********
                    //     Correspondenia  *************************************
                    $isset_referencia_cod = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findBy(
                        array(
                          "codReferenciaSreci" => $codgio_oficio_externo
                        ));
    
                    
                    //Verificamos que el retorno de la Funcion sea = 0 ********* 
                    if(count($isset_corresp_cod) > 0 && count($isset_referencia_cod) > 0 ){
                        // Verificacion del Codigo de la Correspondenia ********
                        // Detalle TblCorrespondenciaDet  **********************
                        $correspondenciaDetEncAsigna = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findOneBy(
                            array(
                                "codCorrespondenciaEnc" => $codgio_oficio_interno
                            ));                        
                        
                        // Busca el Id del Oficio Detalle **********************
                        // Primero Buscamos el idCorrespondenciaEnc de la Tabla*
                        // Padre para Ubicarlo en la Tabla Hijo ****************
                        //Seteo de Datos Generales de la tabla: TblCorrespondenciaEnc
                        $correspondenciaDetAsigna = new TblCorrespondenciaDet();
                        
                        //Instanciamos de la Clase TblSecuenciales
                        //Seteo del nuevo secuencial de la tabla: TblSecuenciales
                        $secuenciaNew = new TblSecuenciales();
                        
                        // Busqueda del Codigo de la Secuencia a Actualizar | Correspondencia Enc
                        $secuenciaNew = $em->getRepository("BackendBundle:TblSecuenciales")->findOneBy(                            
                            array(
                                "codSecuencial"  => "SCPI"
                            ));                    
                        $secuenciaNew->setValor2($new_secuencia); //Set de valor2 de Secuencia de Oficios
                        
                        //Realizar la Persistencia de los Datos y enviar a la BD
                                                
                        $em->persist($secuenciaNew);
                        //Realizar la actualizacion en el storage de la BD
                        $em->flush();
                        
                        // Consulta a los Datos del Depto Funcinal *************
                        $deptoFuncConsulta = $em->getRepository("BackendBundle:TblDepartamentosFuncionales")->findOneBy(
                            array(
                                "idDeptoFuncional" => $identity->idDeptoFuncional
                            ));
                        $inicialesDeptoFunc = $deptoFuncConsulta->getInicialesDeptoFuncional();
                        $cod_oficio_resp = $new_cod_correspondencia_det . "-" . $inicialesDeptoFunc . "-" . $act_secuencia  ;
                        $actividad_oficio = "Pendiente de Enviar Oficio de respuesta";
                        
                        // Empieza la Actualizacion
                        $correspondenciaDetAsigna->setCodCorrespondenciaDet( $cod_oficio_resp ); //Set de Descripcion de Correspondencia
                        $correspondenciaDetAsigna->setDescCorrespondenciaDet( $descripcion_oficio ); //Set de Descripcion de Correspondencia
                        $correspondenciaDetAsigna->setActividadRealizar( $actividad_oficio ); //Set de Actividad de Correspondencia
                        $correspondenciaDetAsigna->setFechaIngreso( $correspondenciaAsigna->getFechaIngreso() ); //Set de Actividad de Correspondencia
                        $correspondenciaDetAsigna->setFechaSalida( $fecha_finalizacion ); //Set de Actividad de Correspondencia
                        $correspondenciaDetAsigna->setIdCorrespondenciaEnc( $correspondenciaAsigna ); //Set de Id de Correspondenica Enc
                        
                        // Obtenemos ls Datos del Usuario
                        // Detalle TblUsuarios  **********************
                        $id_usuario_creador = $em->getRepository("BackendBundle:TblUsuarios")->findOneBy(
                            array(
                                "idUsuario" => $identity->sub
                            ));
                        $correspondenciaDetAsigna->setIdUsuario( $id_usuario_creador ); //Set de Id de Usuario Creador
                        $correspondenciaDetAsigna->setCodReferenciaSreci( $correspondenciaAsigna->getCodReferenciaSreci() ); //Set de Id de Usuario Creador
                        $correspondenciaDetAsigna->setIdFuncionarioAsignado( $correspondenciaAsigna->getIdFuncionarioAsignado() ); //Set de Id de Funcionario Asignado
                        
                        $correspondenciaDetAsigna->setIdEstado( $estadoAsigna ); //Set de Estado de Correspondencia | Id = 5 ( Finalizado )
                        $correspondenciaDetAsigna->setCodOficioRespuesta( $codgio_oficio_respuesta ); //Set de Oficio Respuesta
                        
                        //Realizar la Persistencia de los Datos y enviar a la BD
                        // Detalle de la Comunicacion Principal
                        $em->persist($correspondenciaDetAsigna);
                        
                        // Secuencia Actualizada + 1 
                        //$em->persist($secuenciaNew);
                        
                        // Encabezado de la Comunicacion Principal
                        $em->persist($correspondenciaAsigna);
                        
                        //Realizar la actualizacion en el storage de la BD
                        $em->flush();
                        
                        // FIN de Actualizacion a Tabla Detalle de Comunicacion,
                        // Secuenciales y Encabezados Comunicacion *************
                        
                        
                        // Llamamo a la Funcion Interna para que nos convierta *
                        // La Fecha a Calendario Gregoriano ********************                        
                            $fecha_maxima_entrega_time_stamp = json_encode($correspondenciaAsigna->getFechaMaxEntrega()->getTimestamp(), true ); 
                        // Ejecucion de la Funcion *****************************
                            $fecha_maxima_entrega_convert = $this->convertirFechasTimeStampAction( $fecha_maxima_entrega_time_stamp );
                                                
                            $fecha_creacion_time_stamp  = json_encode( $correspondenciaAsigna->getFechaIngreso()->getTimestamp(), true );
                            $fecha_creacion_convert  = $this->convertirFechasTimeStampAction( $fecha_creacion_time_stamp );
                                                                       
                        // Fin de la Funcion ( convertirFechasTimeStampAction )* 
                        // *****************************************************
                        
                        // Parametros del Oficio nesearios para enviar por *****
                        // Mail ************************************************
                        
                            $tema_correspondencia = $correspondenciaAsigna->getTemaComunicacion();
                            $desc_correspondencia = $correspondenciaAsigna->getDescCorrespondenciaEnc();
                        
                        // Parametros de Salida que Utiliza el Correo
                        //Instanciamos de la Clase TblFuncionarios, para Obtener
                        // los Datos de envio de Mail **************************
                            $funcionario_asignado = $em->getRepository("BackendBundle:TblFuncionarios")->findOneBy(
                            array(
                                "idFuncionario" => $identity->sub                
                            ));
                            $mailSend = $funcionario_asignado->getEmailFuncionario() ; // Get de mail de Funcionario Asignado
                            $nombreSend = $funcionario_asignado->getNombre1Funcionario() ; // Get de Nombre de Funcionario Asignado
                            $apellidoSend = $funcionario_asignado->getApellido1Funcionario() ; // Get de Apellido de Funcionario Asignado
                         
                        
                        // Fin de Envio de Parametros para el Mail *************
                                                
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
                               ->setUsername( "nahum.sreci@gmail.com" )                               
                               ->setPassword('1897Juve');                               
                           //echo "Paso 1";
                           //Creamos la instancia del envío
                           $mailer = \Swift_Mailer::newInstance($transport);
                           
                           //Creamos el mensaje
                           $mail = \Swift_Message::newInstance()
                               ->setSubject('Asignación de Oficio | SICDOC')
                               ->setFrom(array($identity->email => $nombreSend . " " .  $apellidoSend ))
                               ->setTo($mailSend)                               
                               ->setBody(
                                    $this->renderView(
                                    // Plantilla HTML que sirve para el envio de los Correos
                                        'Emails/sendMailFuncionario.html.twig',
                                        array( 'name' => $nombreSend, 'apellidoOficio' => $apellidoSend,
                                               'oficioExtNo' => $codgio_oficio_externo, 'oficioInNo' => $codgio_oficio_interno,
                                               'temaOficio' => $tema_correspondencia, 'descOficio' => $desc_correspondencia,
                                               'fechaIngresoOfi' => strval($fecha_creacion_convert), 
                                               'fechaMaxOfi' => strval($fecha_maxima_entrega_convert) )
                                    ), 'text/html' );   
                                                
                           
                            // validamos que se adjunta pdf
                            if( $pdf_send != null ){
                              $target_path1 = "uploads/correspondencia/correspondencia_" . date('Y-m-d') . "/" . $pdf_send . "-" .date('Y-m-d'). ".pdf";                            
                              //$target_path1 = "COM-IN-OFI-11-2017-09-12.pdf";                            
                              $mail->attach(\Swift_Attachment::fromPath($target_path1));                                
                            }
                                 
                            // Envia el Correo con todos los Parametros
                        //  $resuly = $mailer->send($mail);
                                                  
                        // ***** Fin de Envio de Correo ************************                        
                        
                        
                        //Array de Mensajes
                        $data = array(
                            "status" => "success", 
                            "code"   => 200, 
                            "msg"    => "El Oficio: " . $codgio_oficio_interno . " se ha Finalizado por el Funcionario: " . 
                                        $nombre1_funcionario_asignado . ", " .  $apellido1_funcionario_asignado . ""
                                      . " con fecha de finzalizacion el : " . $fecha_maxima_entrega_convert ,
                            "data"   => $correspondenciaAsigna
                        );                        
                    } else{
                        $data = array(
                            "status" => "error",
                            "desc"   => "No existe un codigo",
                            "code"   => 400, 
                            "msg"   => "Error al asignar, no existe un  el Oficio con este código, ". $codgio_oficio_interno . 
                                       " por favor ingrese otro !!"
                        );                       
                    } // Fin de Busqueda del Oficio que se esta Asignado
                } else{
                        $data = array(
                            "status" => "error",
                            "desc"   => "Falta Informacion",
                            "code"   => 400, 
                            "msg"   => "Falta Ingresar información para poder continuar !!"
                        );                       
                }//Finaliza el Bloque de la validadacion de la Data en la Tabla
            } else {
                    //Array de Mensajes | Campos que Faltan del Json
                    $data = array(
                       "status" => "error",
                       "desc"   => "Eror al Enviar el Json, el Json no ha sido enviado",
                       "code"   => 400, 
                       "msg"   => "Oficio no asignado, falta ingresar parametros, revisar la información ingresada !!"
                    );
            } // Evalua el Json que no sea Null            
        } else {
            $data = array(
                "status" => "error",
                "desc"   => "El Token, es invalido",    
                "code" => "400",                
                "msg" => "Autorizacion de Token no valida !!"                
            );
        } // FIN | Token
        //Retorno de la Funcion ************************************************
        return $helpers->parserJson($data);
    } // FIN | FND00004
    
    
    /**
     * @Route("/creacion-actividad-resp", name="creacion-actividad-resp")
     * Creacion del Controlador: Crear Actividad de Respuesta
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00005
     */
    public function creacionActividadRespAction(Request $request)
    {
        //Instanciamos el Servicio Helpers
        $helpers = $this->get("app.helpers");
        //Recoger el Hash
        //Recogemos el Hash y la Autorizacion del Mismo        
        $hash = $request->get("authorization", null);
        //Se Chekea el Token
        $checkToken = $helpers->authCheck($hash);
        
        //Evalua que el Token sea True
        if($checkToken == true){
            // Variable del LocalStorage
            $identity = $helpers->authCheck($hash, true);
            
            //Convertimos los Parametros POSt a Json
            $json = $request->get("json", null);
           
            //Comprobamos que Json no es Null
            if ($json != null) {
                //Decodificamos el Json
                $params = json_decode($json);

                //Parametros a Convertir                           
                //Datos generales de la Tabla
                $id_usuario_modifica       = $identity->sub;
                $new_secuencia             = ($params->secuenciaComunicacionNewRespActividad != null) ? $params->secuenciaComunicacionNewRespActividad : null ;
                                
                $codgio_oficio_interno     = ($params->codOficioInterno != null) ? $params->codOficioInterno : null ;
                $codgio_oficio_externo     = ($params->codOficioExterno != null) ? $params->codOficioExterno : null ;
                $codgio_oficio_respuesta   = ($params->codOficioRespuesta != null) ? $params->codOficioRespuesta : null ;
                $descripcion_oficio        = ($params->descripcionOficio != null) ? $params->descripcionOficio : null ;
                $actividad_oficio          = ($params->actividadOficio != null) ? $params->actividadOficio : null ;
                
                $nombre1_funcionario_asignado   = ($params->nombre1FuncionarioAsigmado != null) ? $params->nombre1FuncionarioAsigmado : null ;
                $nombre2_funcionario_asignado   = ($params->nombre2FuncionarioAsigmado != null) ? $params->nombre2FuncionarioAsigmado : null ;
                $apellido1_funcionario_asignado = ($params->apellido1FuncionarioAsigmado != null) ? $params->apellido1FuncionarioAsigmado : null ;
                $apellido2_funcionario_asignado = ($params->apellido2FuncionarioAsigmado != null) ? $params->apellido2FuncionarioAsigmado : null ;
                
                $cod_correspondencia_det        = ($params->codCorrespondenciaRespAct != null) ? $params->codCorrespondenciaRespAct : null ;
                
                $estado_asignado                = ($params->idEstadoAsigna != null) ? $params->idEstadoAsigna : null ;
                                
                $fecha_finalizacion             = new \DateTime('now');
                
                //Evaluamos que los Campos del Json  no sean Null ni 0. ********
                if($codgio_oficio_interno != null && $codgio_oficio_externo != null && $id_usuario_modifica != 0 && 
                   $descripcion_oficio != null)
                {
                    // Actualizamos el Funcionario que estara a Cargo de dar ***
                    // Seguimiento y Respuesta al Oficio Ingresado *************
                    //La condicion fue Exitosa
                    //Instancia del Doctrine
                    $em = $this->getDoctrine()->getManager();
                    
                    // ---------------------------------------------------------
                    //Seteo de Datos Generales de la tabla: TblCorrespondenciaEnc
                    // Localizamos el Oficio que se va a Finalizar
                    
                    // 1 ) Verificacion del Codigo de la Correspondenia ********                    
                    $correspondenciaAsigna = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findOneBy(
                        array(
                          "codCorrespondenciaEnc" => $codgio_oficio_interno
                        ));
                    
                    $correspondenciaAsigna->setFechaFinalizacion( $fecha_finalizacion );                    
                    
                    // 2 ) Verificacion del Estado de la Correspondenia ********
                    $estadoAsigna = $em->getRepository("BackendBundle:TblEstados")->findOneBy(
                        array(
                          "idEstado" => $estado_asignado
                        ));                    
                    //$correspondenciaAsigna->setIdestado( $estadoAsigna ); // Id = 8 ( Respuesta )
                                        
                    // 3 ) Verificacion del Codigo de la Correspondenia ********
                    $isset_corresp_cod = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findOneBy(
                        array(
                          "codCorrespondenciaEnc" => $codgio_oficio_interno
                        ));
                    
                    // 4 ) Verificacion del Codigo de Referencia de la *********
                    //     Correspondenia  *************************************
                    $isset_referencia_cod = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findOneBy(
                        array(
                          "codReferenciaSreci" => $codgio_oficio_externo
                        ));
    
                    
                    //Verificamos que el retorno de la Funcion sea = 0 ********* 
                    if(count($isset_corresp_cod) > 0 && count($isset_referencia_cod) > 0 ){
                        // Verificacion del Codigo de la Correspondenia ********
                        // Detalle TblCorrespondenciaDet  **********************
                        $correspondenciaDetEncAsigna = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findOneBy(
                            array(
                                "codCorrespondenciaEnc" => $codgio_oficio_interno
                            ));                        
                        
                        // Busca el Id del Oficio Detalle **********************
                        // Primero Buscamos el idCorrespondenciaEnc de la Tabla*
                        // Padre para Ubicarlo en la Tabla Hijo ****************
                        //Seteo de Datos Generales de la tabla: TblCorrespondenciaEnc
                        $correspondenciaDetAsigna = new TblCorrespondenciaDet();
                        
                        //Instanciamos de la Clase TblSecuenciales
                        //Seteo del nuevo secuencial de la tabla: TblSecuenciales
                        $secuenciaNew = new TblSecuenciales();
                        
                        
                        // Busqueda del Codigo de la Secuencia a Actualizar | Correspondencia Det
                        $secuenciaNew = $em->getRepository("BackendBundle:TblSecuenciales")->findOneBy(                            
                            array(
                                "codSecuencial"  => $cod_correspondencia_det
                            ));                    
                        $secuenciaNew->setValor2($new_secuencia); //Set de valor2 de Secuencia de Documentos
                        
                        //Realizar la Persistencia de los Datos y enviar a la BD
                                                
                        $em->persist($secuenciaNew);
                        //Realizar la actualizacion en el storage de la BD
                        $em->flush();
                        
                        
                        $actividad_comunicacion = "Pendiente de Enviar respuesta";
                        
                        // Empieza la Actualizacion
                        $correspondenciaDetAsigna->setCodCorrespondenciaDet( $cod_correspondencia_det . "-" . $new_secuencia ); //Set de Descripcion de Correspondencia
                        $correspondenciaDetAsigna->setDescCorrespondenciaDet( $descripcion_oficio ); //Set de Descripcion de Correspondencia
                        $correspondenciaDetAsigna->setActividadRealizar( $actividad_comunicacion ); //Set de Actividad de Correspondencia
                        $correspondenciaDetAsigna->setFechaIngreso( $correspondenciaAsigna->getFechaIngreso() ); //Set de Actividad de Correspondencia
                        $correspondenciaDetAsigna->setFechaSalida( $fecha_finalizacion ); //Set de Actividad de Correspondencia
                        $correspondenciaDetAsigna->setIdCorrespondenciaEnc( $correspondenciaAsigna ); //Set de Id de Correspondenica Enc
                        
                        // Obtenemos ls Datos del Usuario
                        // Detalle TblUsuarios  **********************
                        $id_usuario_creador = $em->getRepository("BackendBundle:TblUsuarios")->findOneBy(
                            array(
                                "idUsuario" => $identity->sub
                            ));
                        $correspondenciaDetAsigna->setIdUsuario( $id_usuario_creador ); //Set de Id de Usuario Creador
                        $correspondenciaDetAsigna->setCodReferenciaSreci( $correspondenciaAsigna->getCodReferenciaSreci() ); //Set de Id de Usuario Creador
                        $correspondenciaDetAsigna->setIdFuncionarioAsignado( $correspondenciaAsigna->getIdFuncionarioAsignado() ); //Set de Id de Funcionario Asignado
                        
                        $correspondenciaDetAsigna->setIdEstado( $estadoAsigna ); //Set de Estado de Correspondencia | Id = 5 ( Finalizado )
                        $correspondenciaDetAsigna->setCodOficioRespuesta( $codgio_oficio_respuesta ); //Set de Oficio Respuesta
                        
                        //Realizar la Persistencia de los Datos y enviar a la BD
                        // Detalle de la Comunicacion Principal
                        $em->persist($correspondenciaDetAsigna);
                        
                        // Secuencia Actualizada + 1 
                        //$em->persist($secuenciaNew);
                        
                        // Encabezado de la Comunicacion Principal
                        $em->persist($correspondenciaAsigna);
                        
                        //Realizar la actualizacion en el storage de la BD
                        $em->flush();
                        
                        // FIN de Actualizacion a Tabla Detalle de Comunicacion,
                        // Secuenciales y Encabezados Comunicacion *************
                        
                        
                        // Llamamo a la Funcion Interna para que nos convierta *
                        // La Fecha a Calendario Gregoriano ********************
                        
                            $fecha_maxima_entrega_time_stamp = json_encode($correspondenciaAsigna->getFechaMaxEntrega()->getTimestamp(), true ); 
                        // Ejecucion de la Funcion *****************************
                            $fecha_maxima_entrega_convert = $this->convertirFechasTimeStampAction( $fecha_maxima_entrega_time_stamp );
                                                
                            $fecha_creacion_time_stamp  = json_encode( $correspondenciaAsigna->getFechaIngreso()->getTimestamp(), true );
                            $fecha_creacion_convert  = $this->convertirFechasTimeStampAction( $fecha_creacion_time_stamp );
                                                                       
                        // Fin de la Funcion ( convertirFechasTimeStampAction )* 
                        // *****************************************************
                        
                        // Parametros del Oficio nesearios para enviar por *****
                        // Mail ************************************************
                            $tema_correspondencia = $correspondenciaAsigna->getTemaComunicacion();
                            $desc_correspondencia = $correspondenciaAsigna->getDescCorrespondenciaEnc();
                        
                        // Parametros de Salida que Utiliza el Correo
                            //Instanciamos de la Clase TblFuncionarios, para Obtener
                        // los Datos de envio de Mail **************************
                            $funcionario_asignado = $em->getRepository("BackendBundle:TblFuncionarios")->findOneBy(
                            array(
                                "idFuncionario" => $identity->sub                
                            ));
                            $mailSend = $funcionario_asignado->getEmailFuncionario() ; // Get de mail de Funcionario Asignado
                            $nombreSend = $funcionario_asignado->getNombre1Funcionario() ; // Get de Nombre de Funcionario Asignado
                            $apellidoSend = $funcionario_asignado->getApellido1Funcionario() ; // Get de Apellido de Funcionario Asignado
                         
                        
                        // Fin de Envio de Parametros para el Mail *************
                                                
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
                               //->setUsername( $identity->email )
                               ->setUsername( "nahum.sreci@gmail.com" )                               
                               ->setPassword('1897Juve');                               
                           //echo "Paso 1";
                           //Creamos la instancia del envío
                           $mailer = \Swift_Mailer::newInstance($transport);
                           
                           //Creamos el mensaje
                           $mail = \Swift_Message::newInstance()
                               ->setSubject('Asignación de Oficio | SICDOC')
                               ->setFrom(array($identity->email => $nombreSend . " " .  $apellidoSend ))
                               ->setTo($mailSend)                               
                               ->setBody(
                                    $this->renderView(
                                    // Plantilla HTML que sirve para el envio de los Correos
                                        'Emails/sendMailFuncionario.html.twig',
                                        array( 'name' => $nombreSend, 'apellidoOficio' => $apellidoSend,
                                               'oficioExtNo' => $codgio_oficio_externo, 'oficioInNo' => $codgio_oficio_interno,
                                               'temaOficio' => $tema_correspondencia, 'descOficio' => $desc_correspondencia,
                                               'fechaIngresoOfi' => strval($fecha_creacion_convert), 
                                               'fechaMaxOfi' => strval($fecha_maxima_entrega_convert) )
                                    ), 'text/html' );   
                                                
                           
                            // validamos que se adjunta pdf
                            /*if( $pdf_send != null ){
                              $target_path1 = "uploads/correspondencia/correspondencia_" . date('Y-m-d') . "/" . $pdf_send . "-" .date('Y-m-d'). ".pdf";                            
                              //$target_path1 = "COM-IN-OFI-11-2017-09-12.pdf";                            
                              $mail->attach(\Swift_Attachment::fromPath($target_path1));                                
                            }*/
                                 
                            // Envia el Correo con todos los Parametros
                        //  $resuly = $mailer->send($mail);
                                                  
                        // ***** Fin de Envio de Correo ************************                        
                        
                        
                        //Array de Mensajes
                        $data = array(
                            "status" => "success", 
                            "code"   => 200, 
                            "msg"    => "Actividad No. : " . $codgio_oficio_interno . " se ha creado por el Funcionario: " . 
                                        $nombre1_funcionario_asignado . ", " .  $apellido1_funcionario_asignado . ""
                                      . " con fecha de finzalizacion el : " . $fecha_maxima_entrega_convert ,
                            "data"   => $correspondenciaAsigna
                        );                        
                    } else{
                        $data = array(
                            "status" => "error",
                            "desc"   => "No existe un codigo",
                            "code"   => 400, 
                            "msg"   => "Error al asignar, no existe la Comunicacion con este código, ". $codgio_oficio_interno . 
                                       " por favor ingrese otro !!"
                        );                       
                    } // Fin de Busqueda del Oficio que se esta Asignado
                } else{
                        $data = array(
                            "status" => "error",
                            "desc"   => "Falta Informacion",
                            "code"   => 400, 
                            "msg"   => "Falta Ingresar información para poder continuar !!"
                        );                       
                }//Finaliza el Bloque de la validadacion de la Data en la Tabla
            } else {
                    //Array de Mensajes | Campos que Faltan del Json
                    $data = array(
                       "status" => "error",
                       "desc"   => "Eror al Enviar el Json, el Json no ha sido enviado",
                       "code"   => 400, 
                       "msg"   => "Oficio no asignado, falta ingresar parametros, revisar la información ingresada !!"
                    );
            } // Evalua el Json que no sea Null            
        } else {
            $data = array(
                "status" => "error",
                "desc"   => "El Token, es invalido",    
                "code" => "400",                
                "msg" => "Autorizacion de Token no valida !!"                
            );
        } // FIN | Token
        //Retorno de la Funcion ************************************************
        return $helpers->parserJson($data);
    } // FIN | FND00005
    
    
    /**
     * @Route("/seguimiento-search-comunicacion-enc", name="seguimiento-search-comunicacion-enc")
     * Creacion del Controlador: Busqueda de la Actividad de la Comunicacion
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00006
     * Descripcion: Consulta que muestra la informacion general de la Comunicacion
     *              atraves de Parametros de Search
     */
    public function seguimientoEncListAction(Request $request )
    {
        //Instanciamos el Servicio Helpers
        $helpers = $this->get("app.helpers");
        //Recoger el Hash
        //Recogemos el Hash y la Autorizacion del Mismo        
        $hash = $request->get("authorization", null);
        //Se Chekea el Token
        $checkToken = $helpers->authCheck($hash);
        
        //Evalua que el Token sea True
        if($checkToken == true){
            // Variable del LocalStorage
            $identity = $helpers->authCheck($hash, true);
            
            //Convertimos los Parametros POSt a Json
            $json = $request->get("json", null);
           
            //Comprobamos que Json no es Null
            if ($json != null) {
                //Decodificamos el Json
                $params = json_decode($json);

                //Parametros a Convertir                           
                //Datos generales de la Tabla
                $id_funcionario_asignado   = $identity->sub;
                $codigo_oficio_interno     = ($params->codOficioInterno != null) ? $params->codOficioInterno : null ;
                $codigo_oficio_externo     = ($params->codOficioExterno != null) ? $params->codOficioExterno : null ;
                $send_search               = ($params->searchValueSend != null) ? $params->searchValueSend : null ;
                
                $opcion_busqueda           = ($params->optUserFindId != null) ? $params->optUserFindId : null ;
                
                $fecha_modifcacion         = new \DateTime('now');
                
                //Evaluamos que los Campos del Json  no sean Null ni 0. ********
                if( $codigo_oficio_interno != null || $codigo_oficio_externo != null 
                   && $id_funcionario_asignado != 0 )
                {
                    //Instancia del Doctrine
                    $em = $this->getDoctrine()->getManager();
                    
                    // Seteo de Datos Generales de la tabla: TblCorrespondenciaEnc
                    // Localizamos el Oficio que se va a Buscar
                    // Verificacion del Codigo de la Correspondenia ************
                    // Realizamos Condicion con swith ( $opcion_busqueda )
                    switch ( $opcion_busqueda )
                    {
                        case "1": // Case por codCorrespondenciaEnc
                            $correspondenciaFind = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")
                                ->findOneBy(
                                    array(
                                        "codCorrespondenciaEnc" => $codigo_oficio_interno,
                                        "idFuncionarioAsignado"     => $id_funcionario_asignado
                                    ));
                            $opcion_salida = $codigo_oficio_interno;
                            break;
                        case "2":
                            $correspondenciaFind = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")
                                ->findOneBy(
                                    array(
                                        "codReferenciaSreci" => $codigo_oficio_externo,
                                        "idFuncionarioAsignado"  => $id_funcionario_asignado
                                    ));
                            $opcion_salida = $codigo_oficio_externo;
                            break;
                    } // FIN | Case                    
                                        
                    
                    //Verificamos que el retorno de la Funcion sea = 0 ********* 
                    if(count($correspondenciaFind) > 0 ){
                        //Array de Mensajes
                        $data = array(
                            "status" => "success", 
                            "code"   => 200, 
                            "msg"    => "Se ha encontrado la Informacion solicitada",
                            "data"   => $correspondenciaFind
                        );                        
                    } else{
                        $data = array(
                            "status" => "error",                            
                            "code"   => 404, 
                            "msg"   => "Error al buscar, no existe una Comunicacion con este código, ". $opcion_salida . 
                                       " por favor ingrese otro !!"
                        );                       
                    } // Fin de Busqueda de la Comunicacion
                } else{
                        $data = array(
                            "status" => "error",
                            "desc"   => "Falta Informacion",
                            "code"   => 400, 
                            "msg"   => "Falta Ingresar información para poder continuar !!"
                        );                       
                }//Finaliza el Bloque de la validadacion de la Data en la Tabla
            } else {
                    //Array de Mensajes | Campos que Faltan del Json
                    $data = array(
                       "status" => "error",
                       "desc"   => "Eror al Enviar el Json, el Json no ha sido enviado",
                       "code"   => 400, 
                       "msg"   => "Informacion no encontrada, falta ingresar parametros, revisar la información ingresada !!"
                    );
            } // Evalua el Json que no sea Null            
        } else {
            $data = array(
                "status" => "error",
                "desc"   => "El Token, es invalido",    
                "code" => 404,                
                "msg" => "Autorizacion de Token no valida !!"                
            );
        } // FIN | Token
        //Retorno de la Funcion ************************************************
        return $helpers->parserJson($data);
    }//FIN | FND00006
    
    
    /**
     * @Route("/seguimiento-search-comunicacion-det", name="seguimiento-search-comunicacion-det")
     * Creacion del Controlador: Busqueda de la Actividad de la Comunicacion, Detalle
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND0007
     * Descripcion: Consulta que muestra la informacion general de la Comunicacion
     *              atraves de Parametros de Search, para obtener las distintas Actividades
     */
    public function seguimientoDetListAction(Request $request )
    {
        //Instanciamos el Servicio Helpers
        $helpers = $this->get("app.helpers");
        //Recoger el Hash
        //Recogemos el Hash y la Autorizacion del Mismo        
        $hash = $request->get("authorization", null);
        //Se Chekea el Token
        $checkToken = $helpers->authCheck($hash);
        
        //Evalua que el Token sea True
        if($checkToken == true){
            // Variable del LocalStorage
            $identity = $helpers->authCheck($hash, true);
            
            //Convertimos los Parametros POSt a Json
            $json = $request->get("json", null);
           
            //Comprobamos que Json no es Null
            if ($json != null) {
                //Decodificamos el Json
                $params = json_decode($json);

                //Parametros a Convertir                           
                //Datos generales de la Tabla
                $id_funcionario_asignado   = $identity->sub;
                $codigo_oficio_interno     = ($params->codOficioInterno != null) ? $params->codOficioInterno : null ;
                $codigo_oficio_externo     = ($params->codOficioExterno != null) ? $params->codOficioExterno : null ;
                $send_search               = ($params->searchValueSend != null) ? $params->searchValueSend : null ;
                
                $opcion_busqueda           = ($params->optUserFindId != null) ? $params->optUserFindId : null ;
                
                $fecha_modifcacion         = new \DateTime('now');
                
                               
                //Evaluamos que los Campos del Json  no sean Null ni 0. ********
                if( $codigo_oficio_interno != null || $codigo_oficio_externo != null 
                   && $id_funcionario_asignado != 0 || $opcion_busqueda != null )
                {
                    //Instancia del Doctrine
                    $em = $this->getDoctrine()->getManager();
                    
                    // Seteo de Datos Generales de la tabla: TblCorrespondenciaEnc
                    // Localizamos el Oficio que se va a Buscar
                    // Verificacion del Codigo de la Correspondenia ************
                    // Realizamos Condicion con swith ( $opcion_busqueda )
                    switch ( $opcion_busqueda )
                    {
                        case "1": // Case por codCorrespondenciaEnc
                            $correspondenciaFind = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")
                                ->findBy(
                                    array(
                                        "codCorrespondenciaEnc" => $codigo_oficio_interno,
                                        "idFuncionarioAsignado"     => $id_funcionario_asignado
                                    ));
                            $opcion_salida = $codigo_oficio_interno;
                            break;
                        case "2":
                            $correspondenciaFind = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")
                                ->findBy(
                                    array(
                                        "codReferenciaSreci" => $codigo_oficio_externo,
                                        "idFuncionarioAsignado"  => $id_funcionario_asignado
                                    ));
                            $opcion_salida = $codigo_oficio_externo;
                            break;
                    } // FIN | Case
                    
                    // Obtenemos los Datos de Detalle de la Actividad **********
                    $correspondenciaDetFind = $em->getRepository("BackendBundle:TblCorrespondenciaDet")
                        ->findBy(
                            array(
                                "idCorrespondenciaEnc"  => $correspondenciaFind
                            ), array("idCorrespondenciaDet" => "ASC", "idEstado" => "ASC")  );
                                        
                    
                    //Verificamos que el retorno de la Funcion sea = 0 ********* 
                    if(count($correspondenciaDetFind) > 0 ){
                        //Array de Mensajes
                        $data = array(
                            "status" => "success", 
                            "code"   => 200, 
                            "msg"    => "Se ha encontrado la Informacion solicitada",
                            "data"   => $correspondenciaDetFind
                        );                        
                    } else{
                        $data = array(
                            "status" => "error",                            
                            "code"   => 404, 
                            "msg"   => "Error al buscar, no existe una Comunicacion con este código, ". $opcion_salida . 
                                       " por favor ingrese otro !!"
                        );                       
                    } // Fin de Busqueda de la Comunicacion
                } else{
                        $data = array(
                            "status" => "error",
                            "desc"   => "Falta Informacion",
                            "code"   => 400, 
                            "msg"   => "Falta Ingresar información para poder continuar !!"
                        );                       
                }//Finaliza el Bloque de la validadacion de la Data en la Tabla
            } else {
                    //Array de Mensajes | Campos que Faltan del Json
                    $data = array(
                       "status" => "error",
                       "desc"   => "Eror al Enviar el Json, el Json no ha sido enviado",
                       "code"   => 400, 
                       "msg"   => "Informacion no encontrada, falta ingresar parametros, revisar la información ingresada !!"
                    );
            } // Evalua el Json que no sea Null            
        } else {
            $data = array(
                "status" => "error",
                "desc"   => "El Token, es invalido",    
                "code" => 404,                
                "msg" => "Autorizacion de Token no valida !!"                
            );
        } // FIN | Token
        //Retorno de la Funcion ************************************************
        return $helpers->parserJson($data);
    }//FIN | FND00007
    
    
} // FIN Clase

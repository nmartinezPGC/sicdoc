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
                        $mailSend = $funcionario_asignado->getEmailFuncionario() ; // Get de mail de Funcionario Asignado
                        $nombreSend = $funcionario_asignado->getNombre1Funcionario() ; // Get de Nombre de Funcionario Asignado
                        $apellidoSend = $funcionario_asignado->getApellido1Funcionario() ; // Get de Apellido de Funcionario Asignado
                        
                        // Fin de Envio de Parametros para el Mail *************
                                                
                            //Creamos la instancia con la configuración 
                            $transport = \Swift_SmtpTransport::newInstance()
                               ->setHost('smtp.gmail.com')
                               ->setPort(587)
                               ->setEncryption('tls')                               
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
                                      . " con fecha de entrga el : " . $fecha_maxima_entrega_convert ,
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
    
    
} // FIN Clase
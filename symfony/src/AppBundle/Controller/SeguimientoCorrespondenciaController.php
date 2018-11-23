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
        date_default_timezone_set('America/Tegucigalpa');
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
                
                //Instruccion de la Asignacion
                $instruccion_asignado = ($params->buscadorOficio != null) ? $params->buscadorOficio : null ;
                                
                $fecha_modifcacion        = new \DateTime('now');
                
                //Depto Funcional | Datos del Token
                $id_depto_funcional   = $identity->idDeptoFuncional;
                
                $id_tipo_usuario   = $identity->idTipoUser;
                
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
                    // Valida el Perfil del Usuario | Si es Director General, 
                    // No cambia el Estado 
                    if( $id_tipo_usuario != 5 ){
                        //Actualiza el Estado
                        $estadoAsigna = $em->getRepository("BackendBundle:TblEstados")->findOneBy(
                        array(
                          "idEstado" => $estado_asignado
                        ));                    
                        $correspondenciaAsigna->setIdestado( $estadoAsigna );
                                                
                    }else if ( $id_tipo_usuario == 5 ) { 
                        //Actualiza el Estado
                        $estadoAsigna = $em->getRepository("BackendBundle:TblEstados")->findOneBy(
                            array(
                              "idEstado" => 7
                            ));                    
                        $correspondenciaAsigna->setIdestado( $estadoAsigna );
                        
                        //Obtenemos el idDeptoFuncional, del Director Asignado
                        //Actualiza el Estado | TblUsuarios
                        $deptoFuncAsigna = $em->getRepository("BackendBundle:TblUsuarios")->findOneBy(
                            array(
                              "idUsuario" => $id_funcionario_asignado
                            ));                    
                        
                        
                        //Instanciamos de la Clase TblDepartamentosFuncionales
                        $depto_funcional = $em->getRepository("BackendBundle:TblDepartamentosFuncionales")->findOneBy(
                            array(
                               "idDeptoFuncional" => $deptoFuncAsigna->getIdDeptoFuncional()                
                            ));                    
                        $correspondenciaAsigna->setIdDeptoFuncional( $depto_funcional ); //Set de Codigo de Depto Funcional
                    }// FIN | Condicion de Director General
                    
                    // ---------------------------------------------------------
                    //Instanciamos de la Clase TblFuncionarios
                    $funcionario_asignado = $em->getRepository("BackendBundle:TblFuncionarios")->findOneBy(
                        array(
                           "idFuncionario" => $id_funcionario_asignado                
                        ));                    
                    $correspondenciaAsigna->setIdFuncionarioAsignado( $funcionario_asignado ); //Set de Codigo de Funcionario Asignado
                                                            
                    
                    //----------------------------------------------------------
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
                        $correspondenciaDetAsigna->setInstrucciones( $instruccion_asignado ); //Set de Instruccion de Asignacion
                        
                        /* Incidencia: INC.00001 | Generacion de Secuencia SCPI | Automatica
                        * Fecha : 2017-12-24 | 02:00 pm
                        * Reportada : Nahum Martinez | Admon. SICDOC
                        * INI | NMA | INC.00001
                        * Se incluyo el Seteo del idEstado, para que pueda
                         * Trabajar el usuario Asignado */                        
                        $correspondenciaDetAsigna->setIdestado( $estadoAsigna ); //Set de Estado del Detalle
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
                                //"idFuncionario" => $identity->sub                
                                "idFuncionario" => $id_funcionario_asignado                
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
                               //->setUsername( "nahum.sreci@gmail.com" )                               
                               //->setPassword('1897Juve');                               
                               ->setUsername( "correspondenciascpi@sreci.gob.hn" )
                               ->setPassword('Despachomcns')
                               ->setTimeout(180);
                           //echo "Paso 1";
                           //Creamos la instancia del envío
                           $mailer = \Swift_Mailer::newInstance($transport);
                           
                           //Creamos el mensaje
                           $mail = \Swift_Message::newInstance()
                               ->setSubject('Asignación de Comunicacion | SICDOC')
                               //->setFrom(array($mailSend => $nombreSend . " " .  $apellidoSend ))
                               //->setFrom(array("nahum.sreci@gmail.com" => "Administrador SICDOC" ))
                               ->setFrom(array("correspondenciascpi@sreci.gob.hn" => "Administrador SICDOC " ))
                               ->setTo($mailSend)                               
                               ->setBody(
                                    $this->renderView(
                                    // Plantilla HTML que sirve para el envio de los Correos
                                        'Emails/sendMailAsignacion.html.twig',
                                        array( 'name' => $nombreSend, 'apellidoOficio' => $apellidoSend,
                                               'oficioExtNo' => $codgio_oficio_externo, 'oficioInNo' => $codgio_oficio_interno,
                                               'temaOficio' => $tema_correspondencia, 'descOficio' => $desc_correspondencia,
                                               'instruccion' => $instruccion_asignado, 'fechaIngresoOfi' => strval($fecha_creacion_convert), 
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
                            "msg"    => "La Comunicaion: " . $codgio_oficio_interno . " se ha Asignado al Funcinario: " . 
                                        $nombre1_funcionario_asignado . ", " .  $apellido1_funcionario_asignado . ""
                                      . " con fecha de entrega el : " . $fecha_maxima_entrega_convert ,
                            "data"   => $correspondenciaAsigna
                        );                        
                    } else{
                        $data = array(
                            "status" => "error",
                            "desc"   => "No existe un codigo",
                            "code"   => 400, 
                            "msg"   => "Error al asignar, no existe una comunicacion con este código, ". $codgio_oficio_interno . 
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
                       "msg"   => "Comunicacion no asignada, falta ingresar parametros, revisar la información ingresada !!"
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
        date_default_timezone_set('America/Tegucigalpa');
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
     * Creacion del Controlador: Finzalizacion Oficio
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00003
     */
    public function finalizarOficioAction(Request $request)
    {
        date_default_timezone_set('America/Tegucigalpa');
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
                
                // Secuencia de la Tabla Secuencias, para Obtener el Valor2
                //$secuenca_generate = ($params->secuenciaComunicacionFind != null) ? $params->secuenciaComunicacionFind : null ;
                
                $secuenca_generate    = ($params->codCorrespondenciaRespAct != null) ? $params->codCorrespondenciaRespAct : null ;
                
                $estado_asignado      = ($params->idEstadoAsigna != null) ? $params->idEstadoAsigna : null ;
                                
                $fecha_finalizacion   = new \DateTime('now');
                
                // Hora de Finalizacion de la Comuniacion
                $hora_finalizacion    = new \DateTime('now');            
                $hora_finalizacion->format('H:i');
                
                // Ruta del Pdf a Subir
                $pdf_send  = ($params->pdfDocumento != null) ? $params->pdfDocumento : null ;
                
                // 2018-02-19 | Comunicaciones Vinculantes al Tema
                $comVinculante_send = ($params->comunicacionesVinculantes != null) ? $params->comunicacionesVinculantes : null ;
                
                // Envio por Json el Codigo de Usuario | Buscar en la Tabla: TblUsuarios
                $cod_usuario          = $identity->sub;
                
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
                    
                    $correspondenciaAsigna->setHoraFinalizacion( $hora_finalizacion ); // Hora de Finalizacion
                    
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
                        
                        
                        // Buscamos el Tipo de Documento para Generar la Secuencia                        
                        $tipoComunicacion = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findOneBy(
                        array(
                          "codCorrespondenciaEnc" => $codgio_oficio_interno
                        ));
                                                
                        
                        // Busqueda del Codigo de la Secuencia a Actualizar | Correspondencia Enc
                        $secuenciaNew = $em->getRepository("BackendBundle:TblSecuenciales")->findOneBy(                            
                            array(
                                //"codSecuencial"  => "COM-IN-DET-OFI"
                                "codSecuencial"  => $secuenca_generate
                            ));
                        
                        // Evalua que el valor2 de la Consulta no sea Mayor al Enviado
                        $secuenciaAct = $secuenciaNew->getValor2();
                        if( $secuenciaAct > $new_secuencia ){
                            $secuenciaNew->setValor2($secuenciaAct); //Set de valor2 de Secuencia de Comunicacion                            
                            $secuenciaNew->setReservada("N"); //Set de Reservada de Secuencia de Comunicacion                        
                        } else if ( $secuenciaAct < $new_secuencia ){
                            $secuenciaNew->setValor2($new_secuencia ); //Set de valor2 de Secuencia de Comunicacion
                            $secuenciaNew->setReservada("N"); //Set de Reservada de Secuencia de Comunicacion
                        }
                        
                        //$secuenciaNew->setValor2($new_secuencia); //Set de valor2 de Secuencia de Oficios
                        
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
                        //$correspondenciaDetAsigna->setCodOficioRespuesta( $codgio_oficio_respuesta ); //Set de Oficio Respuesta
                        
                         /* 2018-02-19
                        * Campo de las Sub Comunicaciones Vinculantes
                        * MEJ-000002
                        */
                        // *****************************************************
                        if( $comVinculante_send != null ){
                            // Se convierte el Array en String
                            $ComVinc_array_convert   = json_encode($comVinculante_send);
                            $ComVinc_array_convert2  = json_decode($ComVinc_array_convert);

                            // Recorreros los Items del Array
                            $codigoComunicacionAcum = "";
                            
                            foreach ( $ComVinc_array_convert2 as $arr ){                                
                                $idComunicacionVinculante     = $arr->id;
                                $codigoComunicacionVinculante = $arr->itemName;
                                
                                if( $codigoComunicacionAcum != "" ){
                                    $codigoComunicacionAcum = $codigoComunicacionAcum . ', ' . $codigoComunicacionVinculante;
                                }else {
                                    $codigoComunicacionAcum = $codigoComunicacionVinculante;
                                }
                                // Asignamos las Sub Direcciones al Listado
                                $correspondenciaAsigna->setcomunicacionVinculante( $codigoComunicacionAcum );
                            }
                        }
                        // FIN | MEJ-000002
                        
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
                            
                        
                        // Ingresamos los Datos a la Tabla TblDocumentos *******
                        //Seteo del nuevo documentos de la tabla: TblDocumentos
                        // *****************************************************
                        if( $pdf_send != null ){
                            // Se convierte el Array en String
                            $documentos_array_convert      = json_encode($pdf_send);
                            $documentos_array_convert2      = json_decode($documentos_array_convert);

                            // Recorreros los Items del Array
                            foreach ( $documentos_array_convert2 as $arr ){                                
                                $nameDoc = $arr->nameDoc;
                                $extDoc = $arr->extDoc;
                                $pesoDoc = $arr->pesoDoc;
                                
                                // Cambiamos el Tipo de extencion jpg => jpeg
                                if( $extDoc == "jpg" || $extDoc == "JPG" ){
                                    $extDoc = "jpeg";
                                }
                                
                                /* INC00001 | 2018-01-04
                                * Corregir la Extencion del PDF a pdf
                                */
                                if( $extDoc == "PDF" ){
                                    $extDoc = "pdf";
                                }
                                //FIN | INC00001
                                
                                /* INC00002 | 2018-01-09
                                * Corregir la Extencion del PNG a png
                                */
                                if( $extDoc == "PNG" ){
                                    $extDoc = "png";
                                }
                                //FIN | INC00002
                                //var_dump($nameDoc);
                                
                                $documentosIn = new TblDocumentos();

                                //$documentosIn->setCodDocumento($cod_correspondencia . "-" . $new_secuencia); //Set de Codigo Documento
                                $documentosIn->setCodDocumento($nameDoc); //Set de Codigo Documento
                                $documentosIn->setFechaIngreso($fecha_finalizacion); //Set Fecha Ingreso

                                $documentosIn->setDescDocumento("Documento de Respaldo"); //Set Documento Desc
                                $documentosIn->setStatus("LOAD"); //Set Documento Desc

                                //Instanciamos de la Clase TblUsuario
                                $usuarioDocumento = $em->getRepository("BackendBundle:TblUsuarios")->findOneBy(
                                    array(
                                       "idUsuario" => $cod_usuario                           
                                    ));                    
                                $documentosIn->setIdUsuario($usuarioDocumento); //Set de Codigo de Usuario 

                                // Verificacion del Codigo de la Correspondenia  *******
                                // Detalle  ********************************************
                                $id_correspondencia_det_docu = $em->getRepository("BackendBundle:TblCorrespondenciaDet")->findOneBy(
                                    array(
                                        "codCorrespondenciaDet" => $new_cod_correspondencia_det
                                    ));
                                $documentosIn->setIdCorrespondenciaDet($id_correspondencia_det_docu); //Set de Id Correspondencia Det
                                
                                // Verificacion del Codigo de la Correspondenia*
                                // Encabezado  *********************************
                                $id_correspondencia_enc_docu = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findOneBy(
                                    array(
                                        "codCorrespondenciaEnc" => $codgio_oficio_interno
                                    ));
                                $documentosIn->setIdCorrespondenciaEnc($id_correspondencia_enc_docu); //Set de Id Correspondencia Enc

                                // Pdf que se Agrega
                                // validamos que se adjunta pdf
                                $documentosIn->setUrlDocumento($nameDoc . "." . $extDoc ); //Set Url de Documento

                                // Relizamos la persistencia de Datos de las Comunicaciones Detalle
                                $em->persist($documentosIn); 

                                //Realizar la actualizacion en el storage de la BD
                                $em->flush();
                            } // Fin de foreach                            
                        }
                        // Fin de Grabacion de Documentos **********************                        
                                                        
                        
                        // Parametros del Oficio nesearios para enviar por *****
                        // Mail ************************************************
                            $tema_correspondencia = $correspondenciaAsigna->getTemaComunicacion();
                            $desc_correspondencia = $correspondenciaAsigna->getDescCorrespondenciaEnc();
                        
                        // Parametros de Salida que Utiliza el Correo
                            //Instanciamos de la Clase TblFuncionarios, para Obtener
                        // los Datos de envio de Mail **************************
                            $funcionario_asignado = $em->getRepository("BackendBundle:TblFuncionarios")->findOneBy(
                            array(
                                "idUsuario" => $identity->sub                
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
                               //->setUsername( "nahum.sreci@gmail.com" )                                                                  
                               //->setPassword('1897Juve');
                               ->setUsername( "correspondenciascpi@sreci.gob.hn" )                               
                               ->setPassword('Despachomcns')
                               ->setTimeout(180);                               
                           //echo "Paso 1";
                           //Creamos la instancia del envío
                           $mailer = \Swift_Mailer::newInstance($transport);
                           
                           //Creamos el mensaje
                           $mail = \Swift_Message::newInstance()
                               ->setSubject('Finalización de Comunicacion | SICDOC')
                               ->setFrom(array("correspondenciascpi@sreci.gob.hn"  => "Administrador SICDOC " ))
                               //->setFrom(array("nahum.sreci@gmail.com" => "Administrador SICDOC" ))
                               ->setTo($mailSend)                               
                               ->setBody(
                                    $this->renderView(
                                    // Plantilla HTML que sirve para el envio de los Correos
                                        'Emails/sendMailFuncionario.html.twig',
                                        array( 'name' => $nombreSend, 'apellidoOficio' => $apellidoSend,
                                               'oficioExtNo' => $codgio_oficio_externo, 'oficioInNo' => $codgio_oficio_interno,
                                               'temaOficio' => $tema_correspondencia, 'descOficio' => $desc_correspondencia,
                                               'fechaIngresoOfi' => strval($fecha_creacion_convert), 
                                               'fechaMaxOfi' => strval($fecha_maxima_entrega_convert), 'actividadCom' => $actividad_oficio )
                                    ), 'text/html' );   
                                                
                           
                            // validamos que se adjunta pdf
                            // Array | Attach
                            if( $pdf_send != null ){
                              // Realizamos el foreach de los Documentos enviados
                              // Se convierte el Array en String
                              $documentos_array_convert      = json_encode($pdf_send);
                              $documentos_array_convert2      = json_decode($documentos_array_convert);
                            
                              foreach ( $documentos_array_convert2 as $attachMail  ) {
                                // varibles
                                $nameDoc = $attachMail->nameDoc;
                                $extDoc = $attachMail->extDoc;
                                $pesoDoc = $attachMail->pesoDoc;
                                
                                // Cambiamos el Tipo de extencion jpg => jpeg
                                if( $extDoc == "jpg" || $extDoc == "JPG" ){
                                    $extDoc = "jpeg";
                                }
                                
                                // INC00001 | Cambiamos el Tipo de extencion PDF => pdf
                                //Fecha: 2017-01-03 | Incidencia con PDF
                                if( $extDoc == "PDF" ){
                                    $extDoc = "pdf";
                                }
                                //FIN | INC00001
                                
                                /* INC00002 | 2018-01-09
                                * Corregir la Extencion del PNG a png
                                */
                                if( $extDoc == "PNG" ){
                                    $extDoc = "png";
                                }
                                //FIN | INC00002
                                
                                $target_path1 = "uploads/correspondencia" . "/" . $nameDoc . "." . $extDoc;                            
                                  
                                /*********************************************** 
                                 * Se Comenta la Opcion de Adjuntar los Documentos
                                 * Sobrecargan el Correo de los Funcionarios
                                 * Fecha: 2018-03-12
                                 ***********************************************/
                                //$mail->attach(\Swift_Attachment::fromPath($target_path1));
                              }
                            } // FIN Array | Attach
                                 
                            // Envia el Correo con todos los Parametros
                            $resuly = $mailer->send($mail);
                                                  
                        // ***** Fin de Envio de Correo ************************                        
                        
                        
                        //Array de Mensajes
                        $data = array(
                            "status" => "success", 
                            "code"   => 200, 
                            "msg"    => "La Comunicacion: " . $codgio_oficio_interno . " se ha Finalizado por el Funcionario: " . 
                                        $nombre1_funcionario_asignado . ", " .  $apellido1_funcionario_asignado . ""
                                      . " con fecha de finzalizacion el : " . $fecha_maxima_entrega_convert ,
                            "data"   => $correspondenciaAsigna
                        );                        
                    } else{
                        $data = array(
                            "status" => "error",
                            "desc"   => "No existe un codigo",
                            "code"   => 400, 
                            "msg"   => "Error al asignar, no existe una comunicacion con este código, ". $codgio_oficio_interno . 
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
                       "msg"   => "Comunicacion no asignada, falta ingresar parametros, revisar la información ingresada !!"
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
        date_default_timezone_set('America/Tegucigalpa');
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
                $tipo_documento                = ($params->idTipoDocumento != null) ? $params->idTipoDocumento : null ;
                                
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
                        
                        /*
                        //Instanciamos de la Clase TblSecuenciales
                        //Seteo del nuevo secuencial de la tabla: TblSecuenciales
                        $secuenciaNew = new TblSecuenciales();
                        
                        // Busqueda del Codigo de la Secuencia a Actualizar | Correspondencia Enc
                        $secuenciaNew = $em->getRepository("BackendBundle:TblSecuenciales")->findOneBy(                            
                            array(
                                "codSecuencial"  => "SCPI",
                                "idTipoDocumento" => $tipo_documento
                            )); 
                        
                        // Evalua que el valor2 de la Consulta no sea Mayor al Enviado
                        $secuenciaAct = $secuenciaNew->getValor2();
                        if( $secuenciaAct > $new_secuencia ){
                            $secuenciaNew->setValor2($secuenciaAct); //Set de valor2 de Secuencia de Comunicacion                            
                            $secuenciaNew->setReservada("N"); //Set de Reservada de Secuencia de Comunicacion                        
                        } else if ( $secuenciaAct < $new_secuencia ){
                            $secuenciaNew->setValor2($new_secuencia ); //Set de valor2 de Secuencia de Comunicacion
                            $secuenciaNew->setReservada("N"); //Set de Reservada de Secuencia de Comunicacion
                        }
                        
                        //$secuenciaNew->setValor2($new_secuencia); //Set de valor2 de Secuencia de Oficios
                        
                        //Realizar la Persistencia de los Datos y enviar a la BD
                                                
                        $em->persist($secuenciaNew);
                        //Realizar la actualizacion en el storage de la BD
                        $em->flush();
                        
                        */
                        
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
                               //->setUsername( "nahum.sreci@gmail.com" )                               
                               //->setPassword('1897Juve');                               
                               ->setUsername( "correspondenciascpi@sreci.gob.hn" )
                               ->setPassword('Despachomcns')
                               ->setTimeout(180);
                           //echo "Paso 1";
                           //Creamos la instancia del envío
                           $mailer = \Swift_Mailer::newInstance($transport);
                           
                           //Creamos el mensaje
                           $mail = \Swift_Message::newInstance()
                               ->setSubject('Creacion de Actividad | SICDOC')
                               //->setFrom(array($mailSend => $nombreSend . " " .  $apellidoSend ))
                               //->setFrom(array("nahum.sreci@gmail.com" => "Administrador" ))
                               ->setFrom(array("correspondenciascpi@sreci.gob.hn" => "Administrador SICDOC " ))
                               ->setTo($mailSend)                               
                               ->setBody(
                                    $this->renderView(
                                    // Plantilla HTML que sirve para el envio de los Correos
                                        'Emails/sendMailFuncionarioCrearActividad.html.twig',
                                        array( 'name' => $nombreSend, 'apellidoOficio' => $apellidoSend,
                                               'oficioExtNo' => $cod_oficio_resp, 'oficioInNo' => $codgio_oficio_interno,
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
                            $resuly = $mailer->send($mail);
                                                  
                        // ***** Fin de Envio de Correo ************************                        
                        
                        
                        //Array de Mensajes
                        $data = array(
                            "status" => "success", 
                            "code"   => 200, 
                            "msg"    => "La Comunicacion: " . $codgio_oficio_interno . " se ha Finalizado por el Funcionario: " . 
                                        $nombre1_funcionario_asignado . ", " .  $apellido1_funcionario_asignado . ""
                                      . " con fecha de finzalizacion el : " . $fecha_maxima_entrega_convert ,
                            "data"   => $correspondenciaAsigna
                        );                        
                    } else{
                        $data = array(
                            "status" => "error",
                            "desc"   => "No existe un codigo",
                            "code"   => 400, 
                            "msg"   => "Error al asignar, no existe una comunicacion con este código, ". $codgio_oficio_interno . 
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
                       "msg"   => "Comunicacion no asignada, falta ingresar parametros, revisar la información ingresada !!"
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
        date_default_timezone_set('America/Tegucigalpa');
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
                        
                        // Evalua que el valor2 de la Consulta no sea Mayor al Enviado
                        $secuenciaAct = $secuenciaNew->getValor2();
                        if( $secuenciaAct > $new_secuencia ){
                            $secuenciaNew->setValor2($secuenciaAct); //Set de valor2 de Secuencia de Comunicacion                            
                            $secuenciaNew->setReservada("N"); //Set de Reservada de Secuencia de Comunicacion                        
                        } else if ( $secuenciaAct < $new_secuencia ){
                            $secuenciaNew->setValor2($new_secuencia ); //Set de valor2 de Secuencia de Comunicacion
                            $secuenciaNew->setReservada("N"); //Set de Reservada de Secuencia de Comunicacion
                        }
                        
                        //$secuenciaNew->setValor2($new_secuencia); //Set de valor2 de Secuencia de Documentos
                        
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
                               //->setUsername( "nahum.sreci@gmail.com" )                               
                               //->setPassword('1897Juve');                               
                               ->setUsername( "correspondenciascpi@sreci.gob.hn" )
                               ->setPassword('Despachomcns')
                               ->setTimeout(180);
                           //echo "Paso 1";
                           //Creamos la instancia del envío
                           $mailer = \Swift_Mailer::newInstance($transport);
                           
                           //Creamos el mensaje
                           $mail = \Swift_Message::newInstance()
                               ->setSubject('Creacion de Actividad | SICDOC')
                               //->setFrom(array($mailSend => $nombreSend . " " .  $apellidoSend ))
                               //->setFrom(array("nahum.sreci@gmail.com" => "Administrador SICDOC" ))
                               ->setFrom(array("correspondenciascpi@sreci.gob.hn" => "Administrador SICDOC " ))
                               ->setTo($mailSend)                               
                               ->setBody(
                                    $this->renderView(
                                    // Plantilla HTML que sirve para el envio de los Correos
                                        'Emails/sendMailFuncionarioCrearActividad.html.twig',
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
                            $resuly = $mailer->send($mail);
                                                  
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
                       "msg"   => "Comunicacion no asignada, falta ingresar parametros, revisar la información ingresada !!"
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
        date_default_timezone_set('America/Tegucigalpa');
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
                    
                    $opt = 0;
                    // Seteo de Datos Generales de la tabla: TblCorrespondenciaEnc
                    // Localizamos el Oficio que se va a Buscar
                    // Verificacion del Codigo de la Correspondenia ************
                    // Realizamos Condicion con swith ( $opcion_busqueda )
                    switch ( $opcion_busqueda )
                    {
                        case "1": // Case por codCorrespondenciaEnc
                            /*$correspondenciaFind = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")
                                ->findOneBy(
                                    array(
                                        "codCorrespondenciaEnc" => $codigo_oficio_interno,
                                        "idFuncionarioAsignado"     => $id_funcionario_asignado
                                    ));
                            */
                            $opt = 1;
                            
                            $query = $em->createQuery('SELECT DISTINCT c.idCorrespondenciaEnc, c.codCorrespondenciaEnc, c.codReferenciaSreci, '
                                    ."DATE_SUB(c.fechaIngreso, 0, 'DAY') AS fechaIngreso, DATE_SUB(c.fechaMaxEntrega, 0, 'DAY') AS fechaMaxEntrega, "
                                    ."DATE_SUB(c.fechaModificacion, 0, 'DAY') AS fechaModificacion, DATE_SUB(c.fechaFinalizacion, 0, 'DAY') AS fechaFinalizacion, "
                                    . 'tdoc.codTipoDocumento, tdoc.descTipoDocumento, '
                                    . 'dfunc.idDeptoFuncional, dfunc.descDeptoFuncional, dfunc.inicialesDeptoFuncional, '
                                    . 'dsreci.idDireccionSreci, dsreci.descDireccionSreci, dsreci.inicialesDireccionSreci, '
                                    . 'c.descCorrespondenciaEnc, c.temaComunicacion, est.idEstado, est.descripcionEstado, fasig.idFuncionario, '
                                    . 'fasig.nombre1Funcionario, fasig.nombre2Funcionario, fasig.apellido1Funcionario, fasig.apellido2Funcionario, '
                                    . 'inst.descInstitucion, inst.perfilInstitucion, c.observaciones, '
                                    . 'p.idUsuario, p.nombre1Usuario, p.nombre2Usuario, p.apellido1Usuario, p.apellido2Usuario  '
                                    . 'FROM BackendBundle:TblCorrespondenciaEnc c '
                                    . 'INNER JOIN BackendBundle:TblTipoDocumento tdoc WITH  tdoc.idTipoDocumento = c.idTipoDocumento '
                                    . 'INNER JOIN BackendBundle:TblDepartamentosFuncionales dfunc WITH  dfunc.idDeptoFuncional = c.idDeptoFuncional '
                                    . 'INNER JOIN BackendBundle:TblDireccionesSreci dsreci WITH  dsreci.idDireccionSreci = c.idDireccionSreci '
                                    . 'INNER JOIN BackendBundle:TblEstados est WITH  est.idEstado = c.idEstado '
                                    . 'INNER JOIN BackendBundle:TblUsuarios p WITH  p.idUsuario = c.idUsuario '
                                    . 'INNER JOIN BackendBundle:TblFuncionarios fasig WITH  fasig.idFuncionario = c.idFuncionarioAsignado '
                                    . 'INNER JOIN BackendBundle:TblInstituciones inst WITH  inst.idInstitucion = c.idInstitucion '
                                    . 'INNER JOIN BackendBundle:TblCorrespondenciaDet d WITH d.idCorrespondenciaEnc = c.idCorrespondenciaEnc '
                                    . "WHERE c.codCorrespondenciaEnc = '". $codigo_oficio_interno ."' AND "
                                    . 'c.idFuncionarioAsignado = '. $id_funcionario_asignado .' '
                                    . 'ORDER BY c.idCorrespondenciaEnc, c.codCorrespondenciaEnc DESC' ) ;
                                   
                            $correspondenciaFind = $query->getResult();
                            
                            $opcion_salida = $codigo_oficio_interno;
                            break;
                        case "2":
                            $opt = 2;
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
                            "opt"    => $opt,
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
        date_default_timezone_set('America/Tegucigalpa');
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
                    $opt = 0;
                    switch ( $opcion_busqueda )
                    {
                        case "1": // Case por codCorrespondenciaEnc
                            $opt = 1;   
                            
                            $queryEnc = $em->createQuery( "SELECT A.idCorrespondenciaEnc "
                                    . " FROM BackendBundle:TblCorrespondenciaEnc A "
                                    . " WHERE A.codCorrespondenciaEnc = '". $codigo_oficio_interno ."' " );
                            
                            $correspondenciaFind = $queryEnc->getResult();
                            
                            /*$correspondenciaFind = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")
                                ->findOneBy(
                                    array(
                                        "codCorrespondenciaEnc" => $codigo_oficio_interno,
                                        "idFuncionarioAsignado"     => $id_funcionario_asignado
                                    ));*/
                            $opcion_salida = $codigo_oficio_interno;
                            break;
                        case "2":
                            $opt = 2;
                            
                            $queryEnc = $em->createQuery( "SELECT A.idCorrespondenciaEnc "
                                    . " FROM BackendBundle:TblCorrespondenciaEnc A "
                                    . " WHERE A.codReferenciaSreci = '". $codigo_oficio_externo ."' " );
                            
                            $correspondenciaFind = $queryEnc->getResult();
                            
                            /*$correspondenciaFind = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")
                                ->findOneBy(
                                    array(
                                        "codReferenciaSreci" => $codigo_oficio_externo,
                                        "idFuncionarioAsignado"  => $id_funcionario_asignado
                                    ));*/
                            $opcion_salida = $codigo_oficio_externo;
                            break;
                    } // FIN | Case                    
                    // *********************************************************                   
                    $var2 = implode($correspondenciaFind[0]) ;
                    
                    // Obtenemos los Datos de Detalle de la Actividad **********
                    $query = $em->createQuery('SELECT DISTINCT c.idCorrespondenciaDet, c.codCorrespondenciaDet, c.codReferenciaSreci, '
                                    ."DATE_SUB(c.fechaSalida, 0, 'DAY') AS fechaSalida,  "                                                                        
                                    . 'c.descCorrespondenciaDet, c.actividadRealizar, '
                                    . 'est.idEstado, est.descripcionEstado, fasig.idFuncionario, '
                                    . 'fasig.nombre1Funcionario, fasig.nombre2Funcionario, fasig.apellido1Funcionario, fasig.apellido2Funcionario, '                                    
                                    . 'p.idUsuario, p.nombre1Usuario, p.nombre2Usuario, p.apellido1Usuario, p.apellido2Usuario  '
                                    . 'FROM BackendBundle:TblCorrespondenciaDet c '                                    
                                    . 'INNER JOIN BackendBundle:TblEstados est WITH  est.idEstado = c.idEstado '
                                    . 'INNER JOIN BackendBundle:TblUsuarios p WITH  p.idUsuario = c.idUsuario '
                                    . 'INNER JOIN BackendBundle:TblFuncionarios fasig WITH  fasig.idFuncionario = c.idFuncionarioAsignado '                                    
                                    . 'INNER JOIN BackendBundle:TblCorrespondenciaEnc d WITH d.idCorrespondenciaEnc = c.idCorrespondenciaEnc '
                                    . "WHERE c.idCorrespondenciaEnc =  " . $var2 . "   AND "
                                    . 'c.idFuncionarioAsignado = '. $id_funcionario_asignado .' '
                                    . 'ORDER BY c.idCorrespondenciaDet, c.codCorrespondenciaDet DESC' ) ;
                                   
                    $correspondenciaDetFind = $query->getResult();                  
                    
                    
                    /*$correspondenciaDetFind = $em->getRepository("BackendBundle:TblCorrespondenciaDet")
                        ->findBy(
                            array(
                                "idCorrespondenciaEnc"  => $correspondenciaFind
                            ), array("idCorrespondenciaDet" => "ASC", "idEstado" => "ASC")  );*/
                                        
                    
                    //Verificamos que el retorno de la Funcion sea = 0 ********* 
                    if(count($correspondenciaDetFind) > 0 ){
                        //Array de Mensajes
                        $data = array(
                            "status" => "success", 
                            "code"   => 200, 
                            "opt"    => $opt,
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
    
    
    /**
     * @Route("/anula-comunicacion", name="anula-comunicacion")
     * Creacion del Controlador: Anula la Coumicación
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00006
     */
    public function anulaComunicacionAction(Request $request)
    {
        date_default_timezone_set('America/Tegucigalpa');
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
                
                // Hora de Finalizacion de la Comuniacion
                $hora_finalizacion    = new \DateTime('now');            
                $hora_finalizacion->format('H:i');
                
                //Evaluamos que los Campos del Json  no sean Null ni 0. ********
                if($codgio_oficio_interno != null && $codgio_oficio_externo != null && $id_usuario_modifica != 0 && 
                   $descripcion_oficio != null)
                {
                    // Actualizamos el Funcionario que estara a Cargo de dar ***
                    // Anulacion a la Comunicacion Ingresada *******************
                    //La condicion fue Exitosa
                    //Instancia del Doctrine
                    $em = $this->getDoctrine()->getManager();
                    
                    // ---------------------------------------------------------
                    //Seteo de Datos Generales de la tabla: TblCorrespondenciaEnc
                    // Localizamos el Oficio que se va a Anular
                    
                    // 1 ) Verificacion del Codigo de la Correspondenia ********                    
                    $correspondenciaAsigna = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findOneBy(
                        array(
                          "codCorrespondenciaEnc" => $codgio_oficio_interno
                        ));
                    
                    // 2 ) Verificacion del Estado de la Correspondenia ********
                    $estadoAnula = $em->getRepository("BackendBundle:TblEstados")->findOneBy(
                        array(
                          "idEstado" => $estado_asignado
                        ));                                                           
                    
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
                        $correspondenciaEncAsigna = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findOneBy(
                            array(
                                "codCorrespondenciaEnc" => $codgio_oficio_interno
                            ));                        
                        
                        
                        // Actualiza los Datos del Encabezado  *****************
                        $correspondenciaAsigna->setFechaFinalizacion( $fecha_finalizacion );
                        
                        $correspondenciaAsigna->setHoraFinalizacion( $hora_finalizacion ); // Hora de Finalizacion

                        // Set de Observaciones de Tabla Encabezado
                        $observacion_comunicacion = "Comunicación Anulada por Usuario: " . 
                                    $nombre1_funcionario_asignado . ", " . $apellido1_funcionario_asignado;
                        $correspondenciaAsigna->setObservaciones( $observacion_comunicacion );
                                                
                        $correspondenciaAsigna->setIdEstado( $estadoAnula ); //Set de Estado de Correspondencia | Id = 8 ( Anulado )
                    
                        
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
                        
                        // Evalua que el valor2 de la Consulta no sea Mayor al Enviado
                        $secuenciaAct = $secuenciaNew->getValor2();
                        if( $secuenciaAct > $new_secuencia ){
                            $secuenciaNew->setValor2($secuenciaAct); //Set de valor2 de Secuencia de Comunicacion                            
                            $secuenciaNew->setReservada("N"); //Set de Reservada de Secuencia de Comunicacion                        
                        } else if ( $secuenciaAct < $new_secuencia ){
                            $secuenciaNew->setValor2($new_secuencia ); //Set de valor2 de Secuencia de Comunicacion
                            $secuenciaNew->setReservada("N"); //Set de Reservada de Secuencia de Comunicacion
                        }
                                                
                        //Realizar la Persistencia de los Datos y enviar a la BD                                                
                        $em->persist($secuenciaNew);                        
                        //Realizar la actualizacion en el storage de la BD
                        $em->flush();
                        
                        
                        $actividad_comunicacion = "Comunicación Anulada por Usuario: " . 
                                $nombre1_funcionario_asignado . ", " . $apellido1_funcionario_asignado;
                        
                        // Empieza la Actualizacion
                        $correspondenciaDetAsigna->setCodCorrespondenciaDet( $cod_correspondencia_det . "-" . $new_secuencia ); //Set de Descripcion de Correspondencia
                        $correspondenciaDetAsigna->setDescCorrespondenciaDet( $descripcion_oficio ); //Set de Descripcion de Correspondencia
                        $correspondenciaDetAsigna->setInstrucciones( $observacion_comunicacion ); //Set de Instruccion de Correspondencia
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
                        
                        // 2 ) Verificacion del Estado de la Correspondenia ********
                        $estadoAsigna = $em->getRepository("BackendBundle:TblEstados")->findOneBy(
                            array(
                            "idEstado" => 5
                        ));
                        $correspondenciaDetAsigna->setIdEstado( $estadoAsigna ); //Set de Estado de Correspondencia | Id = 5 ( Finalizado )
                        $correspondenciaDetAsigna->setCodOficioRespuesta( $codgio_oficio_respuesta ); //Set de Oficio Respuesta
                        
                        //Realizar la Persistencia de los Datos y enviar a la BD
                        // Detalle de la Comunicacion Principal
                        $em->persist($correspondenciaDetAsigna);
                                                
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
                               ->setUsername( "correspondenciascpi@sreci.gob.hn" )
                               ->setPassword('Despachomcns')
                               ->setTimeout(180);
                           //echo "Paso 1";
                           //Creamos la instancia del envío
                           $mailer = \Swift_Mailer::newInstance($transport);
                           
                           //Creamos el mensaje
                           $mail = \Swift_Message::newInstance()
                               ->setSubject('Anulación de Comunicación | SICDOC')                               
                               ->setFrom(array("correspondenciascpi@sreci.gob.hn" => "Administrador SICDOC " ))
                               ->setTo($mailSend)                               
                               ->setBody(
                                    $this->renderView(
                                    // Plantilla HTML que sirve para el envio de los Correos
                                        'Emails/sendMailFuncionarioAnularComunicacion.html.twig',
                                        array( 'name' => $nombreSend, 'apellidoOficio' => $apellidoSend,
                                               'oficioExtNo' => $codgio_oficio_externo, 'oficioInNo' => $codgio_oficio_interno,
                                               'descOficio' => $desc_correspondencia,
                                               'fechaIngresoOfi' => strval($fecha_creacion_convert), 
                                               'fechaMaxOfi' => date_format($fecha_finalizacion, "Y-m-d"))
                                    ), 'text/html' );   
                                                                                                       
                            // Envia el Correo con todos los Parametros
                            $resuly = $mailer->send($mail);
                                                  
                        // ***** Fin de Envio de Correo ************************                        
                        
                        
                        //Array de Mensajes
                        $data = array(
                            "status" => "success", 
                            "code"   => 200, 
                            "msg"    => "Comunicación No. : " . $codgio_oficio_interno . " se ha Anulado por el Funcionario: " . 
                                        $nombre1_funcionario_asignado . ", " .  $apellido1_funcionario_asignado . ""
                                      . " con fecha de finazalizacion el : " . $fecha_maxima_entrega_convert ,
                            "data"   => $correspondenciaAsigna
                        );                        
                    } else{
                        $data = array(
                            "status" => "error",
                            "desc"   => "No existe un codigo",
                            "code"   => 400, 
                            "msg"   => "Error al Anular, no existe la Comunicacion con este código, ". $codgio_oficio_interno . 
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
                       "msg"   => "Comunicacion no asignada, falta ingresar parametros, revisar la información ingresada !!"
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
    } // FIN | FND00006
    
} // FIN Clase

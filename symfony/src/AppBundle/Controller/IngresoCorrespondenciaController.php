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
 * Description of IngresoCorrespondenciaController                  *
 * Ingreso de Correspondencia en la Tabla: TblCorrespondenciaEnc    *
 * @author Nahum Martinez <nahum.sreci@gmail.com>                   *
 * @category Correspondencia/Ingreso                                *
 * @version 1.0                                                     *
 * Fecha: 22-08-2017                                                *
 ********************************************************************/
class IngresoCorrespondenciaController extends Controller{
    
    
    public function indexAction()
    {
    $mensaje = \Swift_Message::newInstance()
        ->setSubject('Hola')
        ->setFrom('nahum.sreci@gmail.com')
        ->setTo('nahum.sreci@gmail.com')
        ->setBody('Hola Mundo');
        $this->get('mailer')->send($mensaje);
        //return $this->render(...);
        return 0;
    }
    
    /* Funcion de Nuevo Correspondencia ****************************************
     * Parametros:                                                             * 
     * 1 ) Recibe un Objeto Request con el Metodo POST, el Json de la          *  
     *     Informacion.                                                        * 
     ***************************************************************************/
    public function newCorrespondenciaAction(Request $request) 
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
            $identity = $helpers->authCheck($hash, true);
            
            //Convertimos los Parametros POSt a Json
            $json = $request->get("json", null);
           
            //Comprobamos que Json no es Null
            if ($json != null) {
                //Decodificamos el Json
                $params = json_decode($json);

                //Parametros a Convertir                           
                //Datos generales de la Tabla                
                $new_secuencia        = ($params->secuenciaComunicacionIn != null) ? $params->secuenciaComunicacionIn : null ;
                $cod_correspondencia  = ($params->codCorrespondencia != null) ? $params->codCorrespondencia : null ;
                $desc_correspondencia = ($params->descCorrespondencia != null) ? $params->descCorrespondencia : null ;                
                $tema_correspondencia = ($params->temaCorrespondencia != null) ? $params->temaCorrespondencia : null ;                
                $cod_referenciaSreci  = ($params->codReferenciaSreci != null) ? $params->codReferenciaSreci : null ;   
                
                $fecha_ingreso        = new \DateTime('now');
                $fecha_maxima_entrega = ($params->fechaMaxEntrega != null) ? $params->fechaMaxEntrega : null ;
                // Seteo de la Fecha, viene en Json (String) se tiene que convertir a su dato Nativo (Date)
                $fecha_maxima_entrega_date = new \DateTime($fecha_maxima_entrega);
                
                // Fechas Nulas
                $fecha_null = new \DateTime('2999-12-31');
                
                //Relaciones de la Tabla con Otras.
                // Envio por Json el Codigo de Institucion | Buscar en la Tabla: TblInstituciones
                $cod_institucion      = ($params->idInstitucion != null) ? $params->idInstitucion : null ;
                
                // Envio por Json el Codigo de Usuario | Buscar en la Tabla: TblUsuarios
                $cod_usuario          = $identity->sub;
                
                // Envio por Json el Codigo de Estados | Buscar en la Tabla: TblEstados
                $cod_estado           = ($params->idEstado != null) ? $params->idEstado : null ;                
                
                // Envio por Json el Codigo de Direccion Sreci | Buscar en la Tabla: TblDireccionesSreci
                $cod_direccion_sreci  = ($params->idDireccionSreci != null) ? $params->idDireccionSreci : null ;
                
                // Envio por Json el Codigo de Depto Funcional | Buscar en la Tabla: TblDepartamentosSreci
                $cod_depto_funcional   = ($params->idDeptoFuncional != null) ? $params->idDeptoFuncional : null ;
                
                // Envio por Json el Codigo de Depto Acompañante | Buscar en la Tabla: TblDepartamentosSreci
                $cod_depto_acomp   = ($params->idDeptoFuncionalAcom != null) ? $params->idDeptoFuncionalAcom : null ;
                
                // Envio por Json el Codigo de Depto Funcional | Buscar en la Tabla: TblDepartamentosFuncionales
                $cod_tipo_documento  = ($params->idTipoDocumento != null) ? $params->idTipoDocumento : null ;
                
                // Relacion con la Tabla Correspondencia Detalle | Proceso de Respuesta
                $new_secuencia_det        = ($params->secuenciaComunicacionDet != null) ? $params->secuenciaComunicacionDet : null ;
                $cod_correspondencia_det  = ($params->codCorrespondenciaDet != null) ? $params->codCorrespondenciaDet : null ;
               
                
                // Ruta del Pdf a Subir
                $pdf_send  = ($params->pdfDocumento != null) ? $params->pdfDocumento : null ;
                
                // idUsario que tendra asignado el Oficio
                $id_usuario_asignado = ($params->idUsuarioAsaignado != null) ? $params->idUsuarioAsaignado : null ;
                
                
                //Evaluamos que el Codigo de Correspondencia no sea Null y la Descripcion tambien
                if($cod_correspondencia != null && $desc_correspondencia != null && $cod_referenciaSreci != null &&
                   $tema_correspondencia != null && $cod_depto_funcional != 0 && $cod_institucion != 0 &&
                   $id_usuario_asignado != 0 ){
                    //La condicion fue Exitosa
                    //Instancia del Doctrine
                    $em = $this->getDoctrine()->getManager();
                    
                    //Seteo de Datos Generales de la tabla: TblCorrespondenciaEnc
                    $correspondenciaNew = new TblCorrespondenciaEnc();                   
                    
                    
                    // Buscamos el Id de la Secuencia y Generamos el Codigo
                    $correspondenciaNew->setCodCorrespondenciaEnc($cod_correspondencia);                    
                    
                    $correspondenciaNew->setDescCorrespondenciaEnc($desc_correspondencia);                    
                    $correspondenciaNew->setFechaIngreso($fecha_ingreso);
                    $correspondenciaNew->setFechaModificacion($fecha_null);
                    $correspondenciaNew->setFechaFinalizacion($fecha_null);
                    
                    
                    $correspondenciaNew->setFechaMaxEntrega($fecha_maxima_entrega_date);
                    
                    // Nuevo Campo de Codigo de Refrencia SRECI ----------------
                    $correspondenciaNew->setCodReferenciaSreci($cod_referenciaSreci);
                    $correspondenciaNew->setTemaComunicacion($tema_correspondencia);
                    
                    $correspondenciaNew->setIdDeptoAcomp($cod_depto_acomp);
                    
                    //variables de Otras Tablas, las Buscamos para saber si hay Integridad                
                    //Instanciamos de la Clase TblInstituciones
                    $institucion = $em->getRepository("BackendBundle:TblInstituciones")->findOneBy(
                        array(
                           "idInstitucion" => $cod_institucion                        
                        ));                    
                    $correspondenciaNew->setIdInstitucion($institucion); //Set de Codigo de Institucion
                    
                    //Instanciamos de la Clase TblUsuario
                    $usuario = $em->getRepository("BackendBundle:TblUsuarios")->findOneBy(
                        array(
                           "idUsuario" => $cod_usuario                         
                        ));                    
                    $correspondenciaNew->setIdUsuario($usuario); //Set de Codigo de Usuario
                    
                    //Instanciamos de la Clase TblEstados                        
                    $estado = $em->getRepository("BackendBundle:TblEstados")->findOneBy(                            
                        array(
                           "idEstado" => $cod_estado
                        ));                    
                    $correspondenciaNew->setIdEstado($estado); //Set de Codigo de Estados   
                    
                    //Instanciamos de la Clase TblDireccionesSreci                        
                    $direccion = $em->getRepository("BackendBundle:TblDireccionesSreci")->findOneBy(                            
                        array(
                           "idDireccionSreci" => $cod_direccion_sreci
                        ));                    
                    $correspondenciaNew->setIdDireccionSreci($direccion); //Set de Codigo de Dreicciones Sreci 
                    
                    //Instanciamos de la Clase TblDireccionesSreci                        
                    $depto_funcional = $em->getRepository("BackendBundle:TblDepartamentosFuncionales")->findOneBy(                            
                        array(
                           "idDeptoFuncional" => $cod_depto_funcional
                        ));                    
                    $correspondenciaNew->setIdDeptoFuncional($depto_funcional); //Set de Codigo de Dreicciones Sreci 
                    
                    //Instanciamos de la Clase TblTipoDocumentos
                    $tipo_documento_in = $em->getRepository("BackendBundle:TblTipoDocumento")->findOneBy(                            
                        array(
                           "idTipoDocumento" => $cod_tipo_documento
                        ));                    
                    $correspondenciaNew->setIdTipoDocumento($tipo_documento_in); //Set de Codigo de Tipo de Documentos 
                    
                    
                    //Instanciamos de la Clase TblFuncionarios
                    $usuario_asignado = $em->getRepository("BackendBundle:TblFuncionarios")->findOneBy(
                        array(
                           "idFuncionario" => $id_usuario_asignado                
                        ));                    
                    $correspondenciaNew->setIdFuncionarioAsignado($usuario_asignado); //Set de Codigo de Funcionario Asignado
                                                         
                    //Finaliza Busqueda de Integridad entre Tablas
                    
                    
                    //Verificacion del Codigo de la Correspondenia *******************
                    $isset_corresp_cod = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findBy(
                        array(
                          "codCorrespondenciaEnc" => $cod_correspondencia
                        ));
                    
                    //Verificacion del Codigo de Referencia de la Correspondenia *******************
                    $isset_referencia_cod = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findBy(
                        array(
                          "codReferenciaSreci" => $cod_referenciaSreci
                        ));
                    
                    
                    //Verificamos que el retorno de la Funcion sea = 0 ********* 
                    if(count($isset_corresp_cod) == 0 && count($isset_referencia_cod) == 0 ){
                        //Instanciamos de la Clase TblSecuenciales
                        //Seteo del nuevo secuencial de la tabla: TblSecuenciales
                        $secuenciaNew = new TblSecuenciales();
                        
                        
                        // Busqueda del Codigo de la Secuencia a Actualizar | Correspondencia Enc
                        $secuenciaNew = $em->getRepository("BackendBundle:TblSecuenciales")->findOneBy(                            
                            array(
                                "codSecuencial"  => "COM-IN-OFI"
                            ));                    
                        $secuenciaNew->setValor2($new_secuencia); //Set de valor2 de Secuencia de Oficios
                        
                        
                        //Realizar la Persistencia de los Datos y enviar a la BD
                        $em->persist($correspondenciaNew);
                        
                        $em->persist($secuenciaNew);
                        //Realizar la actualizacion en el storage de la BD
                        $em->flush();
                        
                        
                        // Ingresamos los Datos a la Tabla TblEncabezadosDet **********
                        //Seteo del nuevo secuencial de la tabla: TblCorrespondenciaDet
                        // ************************************************************
                        $correspondenciaDet = new TblCorrespondenciaDet();
                        
                        //Ingresamos un valor en la Tabla **********************
                        //Correspondencia Enc **********************************                        
                        $correspondenciaDet->setCodCorrespondenciaDet($cod_correspondencia_det); //Set de Codigo Correspondencia
                        $correspondenciaDet->setFechaIngreso($fecha_ingreso); //Set de Fecha Ingreso
                        $correspondenciaDet->setFechaSalida($fecha_null); //Set de Fecha Salida
                        
                        $correspondenciaDet->setCodReferenciaSreci($cod_referenciaSreci); //Set de Codigo Ref SRECI
                        
                        $correspondenciaDet->setDescCorrespondenciaDet("Creacion de Oficio: " . $cod_correspondencia); //Set de Descripcion Inicial
                        $correspondenciaDet->setActividadRealizar("Pendiente de Crear Oficio de respuesta"); //Set de Actividad Inicial
                        
                       
                        //Verificacion del Codigo de la Correspondenia *********
                        $id_correspondencia_enc = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findOneBy(
                            array(
                                "codCorrespondenciaEnc" => $cod_correspondencia
                            ));
                        $correspondenciaDet->setIdCorrespondenciaEnc($id_correspondencia_enc); //Set de Fecha Id Correspondencia Enc
                        
                        //Instanciamos de la Clase TblEstados                        
                        $estadoDet = $em->getRepository("BackendBundle:TblEstados")->findOneBy(                            
                            array(
                                "idEstado" => $estado
                            ));                    
                        $correspondenciaDet->setIdEstado($estadoDet); //Set de Codigo de Estados                        
                        
                        //Instanciamos de la Clase TblUsuario
                        $usuarioDetalle = $em->getRepository("BackendBundle:TblUsuarios")->findOneBy(
                            array(
                               "idUsuario" => $cod_usuario                           
                            ));                    
                        $correspondenciaDet->setIdUsuario($usuarioDetalle); //Set de Codigo de Usuario
                        
                        //Instanciamos de la Clase TblFuncionarios
                        $usuario_asignado = $em->getRepository("BackendBundle:TblFuncionarios")->findOneBy(
                        array(
                           "idFuncionario" => $id_usuario_asignado                
                        ));                    
                        $correspondenciaDet->setIdFuncionarioAsignado($usuario_asignado); 
                        
                        
                        // Busqueda del Codigo de la Secuencia a Actualizar | Correspondencia Det
                        $secuenciaNew = $em->getRepository("BackendBundle:TblSecuenciales")->findOneBy(                            
                            array(
                                "codSecuencial"  => "COM-IN-DET-OFI"
                            ));                    
                        $secuenciaNew->setValor2($new_secuencia_det); //Set de valor2 de Secuencia de Oficios
                        
                        // Relizamos la persistencia de Datos de las Comunicaciones Detalle
                        $em->persist($correspondenciaDet);
                        
                        // Realizamos la persistencia de la Secuencia
                        $em->persist($secuenciaNew);
                        
                        //Realizar la actualizacion en el storage de la BD
                        $em->flush();                        
                        
                        
                        // Ingresamos los Datos a la Tabla TblDocumentos *******
                        //Seteo del nuevo documentos de la tabla: TblDocumentos
                        // *****************************************************
                        if( $pdf_send != null ){
                            $documentosIn = new TblDocumentos();

                            $documentosIn->setCodDocumento($cod_correspondencia); //Set de Codigo Documento
                            $documentosIn->setFechaIngreso($fecha_ingreso); //Set Fecha Ingreso

                            $documentosIn->setDescDocumento("Oficio de Respaldo"); //Set Documento Desc
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
                                    "codCorrespondenciaDet" => $cod_correspondencia_det
                                ));
                            $documentosIn->setIdCorrespondenciaDet($id_correspondencia_det_docu); //Set de Fecha Id Correspondencia Det


                            // Pdf que se Agrega
                            // validamos que se adjunta pdf

                                $documentosIn->setUrlDocumento($pdf_send . "-" . date('Y-m-d') . ".pdf"); //Set Url de Documento


                            // Relizamos la persistencia de Datos de las Comunicaciones Detalle
                            $em->persist($documentosIn); 

                            //Realizar la actualizacion en el storage de la BD
                            $em->flush();
                        }
                        // Fin de Comunicacion Detalle *************************
                        
                        // Envio de Correo despues de la Granacion de Datos
                        // *****************************************************
                        //Instanciamos de la Clase TblFuncionarios, para Obtener
                        // los Datos de envio de Mail **************************
                        $usuario_asignado_send = $em->getRepository("BackendBundle:TblFuncionarios")->findOneBy(
                            array(
                                "idFuncionario" => $id_usuario_asignado                
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
                               //->setUsername( $identity->email )
                               ->setUsername("nahum.sreci@gmail.com")
                               //->setUsername( 'gcallejas.sreci@gmail.com')
                               ->setPassword('1897Juve');
                               //->setPassword('gec2017*');
                           //echo "Paso 1";
                           //Creamos la instancia del envío
                           $mailer = \Swift_Mailer::newInstance($transport);
                           
                           //Creamos el mensaje
                           $mail = \Swift_Message::newInstance()
                               ->setSubject('Notificación de Ingreso de Oficio | SICDOC')
                               ->setFrom(array($identity->email => $identity->nombre . " " .  $identity->apellido ))
                               ->setTo($mailSend)                               
                               ->setBody(
                                    $this->renderView(
                                    // app/Resources/views/Emails/registration.html.twig
                                        'Emails/sendMail.html.twig',
                                        array( 'name' => $nombreSend, 'apellidoOficio' => $apellidoSend,
                                               'oficioExtNo' => $cod_referenciaSreci, 'oficioInNo' => $cod_correspondencia,
                                               'temaOficio' => $tema_correspondencia, 'descOficio' => $desc_correspondencia,
                                               'fechaIngresoOfi' => strval($fecha_maxima_entrega), 
                                               'fechaIngresoCom' => date_format($fecha_ingreso, "Y/m/d") )
                                    ), 'text/html' );                           
                           
                            // validamos que se adjunta pdf
                            if( $pdf_send != null ){
                              $target_path1 = "uploads/correspondencia/correspondencia_" . date('Y-m-d') . "/" . $pdf_send . "-" .date('Y-m-d'). ".pdf";                            
                              //$target_path1 = "COM-IN-OFI-11-2017-09-12.pdf";                            
                              $mail->attach(\Swift_Attachment::fromPath($target_path1));                                
                            }
                                 
                            // Envia el Correo con todos los Parametros
                            $resuly = $mailer->send($mail);
                                                  
                        // ***** Fin de Envio de Correo ************************
                        // 
                        // 
                        //Consulta de esa Correspondencia recien Ingresada *****
                        $correspondenciaConsulta = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findOneBy(
                            array(                                
                                "codCorrespondenciaEnc" => $cod_correspondencia 
                            ));
                    
                            //Array de Mensajes
                            $data = array(
                                "status" => "success", 
                                "code"   => 200, 
                                "msg"    => "Se ha ingresado el Oficio No. " . $cod_correspondencia,
                                "data"   => $correspondenciaConsulta
                            );
                    }else{
                        $data = array(
                            "status" => "error",
                            "desc"   => "Ya existe un codigo",
                            "code"   => 400, 
                            "msg"   => "Error al registrar, ya existe una correspondencia con este código, ". $cod_referenciaSreci . 
                                       " por favor ingrese otro !!"
                        );                       
                    }//Finaliza el Bloque de la validadacion de la Data en la Tabla
                    // TblCorrespondenciaEnc
                } else {
                    //Array de Mensajes
                    $data = array(
                       "status" => "error",
                       "desc"   => "Eror al Enviar el Json, faltan parametros",
                       "code"   => 400, 
                       "msg"   => "No se ha podido crear la correspondencia, falta ingresar información  !!"
                    );
                }                
            } else {
                    //Array de Mensajes
                    $data = array(
                       "status" => "error",
                       "desc"   => "Eror al Enviar el Json, el Json no ha sido enviado",
                       "code"   => 400, 
                       "msg"   => "Correspondencia no creada, falta ingresar los parametros !!"
                    );
                }
            } else {
            $data = array(
                "status" => "error",
                "desc"   => "El Token, es invalido",    
                "code" => "400",                
                "msg" => "Autorizacion de Token no valida !!"                
            );
        }        
        //Retorno de la Funcion ************************************************
        return $helpers->parserJson($data);
    } //Fin de la Funcion New Correspondencia ********************
    
    
    /* Funcion de Editar Correspondencia****************************************
     * Parametros:                                                             * 
     * 1 ) Recibe un Objeto Request con el Metodo POST, el Json de la          *  
     *     Informacion.                                                        * 
     * 2 ) Recibe el Codigo del Documento por medio de la Url.                 * 
     ***************************************************************************/
    public function editCorrespondenciaAction(Request $request, $id = null) 
    {
        //Instanciamos el Servicio Helpers
        $helpers = $this->get("app.helpers");
        //Recoger el Hash
        //Recogemos el Hash y la Autrizacion del Mismo
        $hash = $request->get("authorization", null);
        //Se Chekea el Token
        $checkToken = $helpers->authCheck($hash);
        //Evalua que el Token sea True
        if($checkToken == true){
            $identity = $helpers->authCheck($hash, true);
            
            //Convertimos los Parametros POSt a Json
            $json = $request->get("json", null);
            
            //Comprobamos que Json no es Null
            if ($json != null) {
                $params = json_decode($json);

                //Parametros a Convertir                                
                //Parametro de la Url
                $correspondenciaId       = $id;
                      
                //Datos generales de la Tabla, que viene del Json
                $desc_correspondencia     = ($params->desc_correspondencia != null) ? $params->desc_correspondencia : null ;                
                $fecha_modificacion       = new \DateTime('now');
                $fecha_maxima_entrega     = ($params->fecha_maxima_entrega != null) ? $params->fecha_maxima_entrega : null ;                 
                
                
                //Relaciones de la Tabla con Otras.
                // Envio por Json el Codigo de Institucion | Buscar en la Tabla: TblInstituciones
                $cod_institucion      = ($params->cod_institucion != null) ? $params->cod_institucion : null ;
                
                // Envio por Json el Codigo de Usuario | Buscar en la Tabla: TblUsuarios
                $cod_usuario          = $identity->codUser;
                
                // Envio por Json el Codigo de Estados | Buscar en la Tabla: TblEstados
                $cod_estado           = ($params->cod_estado != null) ? $params->cod_estado : null ;
                
                // Envio por Json el Codigo de Direccion Sreci | Buscar en la Tabla: TblDireccionesSreci
                $cod_direccion_sreci  = ($params->cod_direccion_sreci != null) ? $params->cod_direccion_sreci : null ;                
                
                
                //Evaluamos que el Codigo de Usuario no sea Null y la Descripcion tambien
                if($correspondenciaId != null && $desc_correspondencia != null){
                    //La condicion fue Exitosa
                    //Instancia del Doctrine
                    $em = $this->getDoctrine()->getManager();
                    
                    //Repositorio de la Tabla: TblCorrespondenciaEnc, se Busca si el Codigo
                    // enviado por Parametro Existe
                    $correspondenciaEdit = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findOneBy(
                        array(
                            "codCorrespondenciaEnc" => $correspondenciaId
                        ));                   
                        
                        //Evaluo el Resultado del Query, con el Paramtro del Codigo del
                        //Documento, para verificar si existe en la BD
                        if(count($correspondenciaEdit) > 0){
                            //Asignamos el usuario de la Consulta Anterior
                            $idUsarioCorrespondencia = $correspondenciaEdit->getIdUsuario()->getIdUsuario();
                            
                            //Evaluamos que el Usuario de la Consulta del Token, sea el dueño
                            // de la Correspondencia
                            if (isset($identity->sub) && $identity->sub === $idUsarioCorrespondencia) {
                                //Seteamos los valores de los campos modificados
                                $correspondenciaEdit->setDescCorrespondenciaEnc($desc_correspondencia);
                                $correspondenciaEdit->setFechaMaxEntrega($fecha_maxima_entrega);
                                
                                //variables de Otras Tablas, las Buscamos para saber si hay Integridad                
                                //Instanciamos de la Clase TblInstituciones
                                $institucion = $em->getRepository("BackendBundle:TblInstituciones")->findOneBy(
                                    array(
                                       "codInstitucion" => $cod_institucion                        
                                    ));                    
                                $correspondenciaEdit->setIdInstitucion($institucion); //Set de Codigo de Institucion

                                //Instanciamos de la Clase TblUsuario
                                $usuario = $em->getRepository("BackendBundle:TblUsuarios")->findOneBy(
                                    array(
                                       "codUsuario" => $identity->codUser                           
                                    ));                    
                                $correspondenciaEdit->setIdUsuario($usuario); //Set de Codigo de Usuario

                                //Instanciamos de la Clase TblEstados                        
                                $estado = $em->getRepository("BackendBundle:TblEstados")->findOneBy(                            
                                    array(
                                       "codEstado" => $cod_estado
                                    ));                    
                                $correspondenciaEdit->setIdEstado($estado); //Set de Codigo de Estados   

                                //Instanciamos de la Clase TblDireccionesSreci                        
                                $direccion = $em->getRepository("BackendBundle:TblDireccionesSreci")->findOneBy(                            
                                    array(
                                       "codDireccionSreci" => $cod_direccion_sreci
                                    ));                    
                                $correspondenciaEdit->setIdDireccionSreci($direccion); //Set de Codigo de Dreicciones Sreci 
                                //Finaliza Busqueda de Integridad entre Tablas
                                
                                //Realizar la Persistencia de los Datos y enviar a la BD
                                $em->persist($correspondenciaEdit);
                                $em->flush();

                                //Array de Mensajes
                                $data = array(
                                    "status" => "success",
                                    "code" => 200,
                                    "msg" => "La Correspondencia ha sido actualizada, exitosamente !!"
                                );
                            } else {
                                //Array de Mensajes
                                $data = array(
                                    "status" => "error",
                                    "code" => 400,
                                    "msg" => "La Correspondencia no ha sido actualizada, no eres el creador del Documento !!"
                                );
                            }
                        }else{                            
                            //Array de Mensajes
                            $data = array(
                                "status" => "error", 
                                "code"   => 400,
                                "msg"   => "No existe una Correspondencia con ese Codigo !!"
                            );                           
                        }
                    }else{
                        $data = array(
                            "status" => "error", 
                            "code"   => 400, 
                            "data"   => "Error al editar, ya existe una Correspondencia con ese Codigo !!"
                        );                       
                    } //Finaliza el Bloque de la validadacion de la Data en la Tabla
            } else {
                    //Array de Mensajes
                    $data = array(
                       "status" => "error", 
                       "code"   => 400, 
                       "msg"   => "Correspondencia no actualizada, parametros invalidos !!"
                    );
                }
            } else {
            $data = array(
                "status" => "error",                
                "code"   => 400,                
                "msg"    => "Autorizacion de Token no valida !!"                
            );
        }        
        //Retorno de la Funcion ************************************************
        return $helpers->parserJson($data);
    } //Fin de la Funcion Editar Correspondencia ****************

    
    /**
     * @Route("/valid-form", name="valid-form")
     * Creacion del Controlador: Ingreso Comunicacion
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00001
     */
    public function validCampoAction($campoValid)
    {
        //Instanciamos el Servicio Helpers
        if($campoValid != null){
          $data = array(
                "status" => "success",
                "desc"   => "Camo valid",    
                "code"   => "200",                
                "msg"    => "has ingresado bien el campo"
            );
        }else{
            $data = array(
                "status" => "error",
                "desc"   => "Faltaingresar el campo",    
                "code"   => "400",                
                "msg"    => "Falta ingresar el campo " .$campoValid                
            );
        }        
        
        //Retorno de la Funcion ************************************************
        return $helpers->parserJson($data);
    }//FIN | FND00001
    
    
    /* Funcion de Nuevo Correspondencia ****************************************
     * Parametros:                                                             * 
     * 1 ) Recibe un Objeto Request con el Metodo POST, el Json de la          *  
     *     Informacion.                                                        * 
     ***************************************************************************/
    public function newCorrespondenciaTipoAction(Request $request) 
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
            $identity = $helpers->authCheck($hash, true);
            
            //Convertimos los Parametros POSt a Json
            $json = $request->get("json", null);
           
            //Comprobamos que Json no es Null
            if ($json != null) {
                //Decodificamos el Json
                $params = json_decode($json);

                //Parametros a Convertir                           
                //Datos generales de la Tabla                
                $new_secuencia        = ($params->secuenciaComunicacionIn != null) ? $params->secuenciaComunicacionIn : null ;
                $act_secuencia_enc    = ($params->secuenciaComunicacionInAct != null) ? $params->secuenciaComunicacionInAct : null ;
                $act_secuencia_det    = ($params->secuenciaComunicacionDetAct != null) ? $params->secuenciaComunicacionDetAct : null ;
                $act_secuencia_scpi   = ($params->secuenciaComunicacionSCPI != null) ? $params->secuenciaComunicacionSCPI : null ;
                
                $cod_correspondencia  = ($params->codCorrespondencia != null) ? $params->codCorrespondencia : null ;
                $desc_correspondencia = ($params->descCorrespondencia != null) ? $params->descCorrespondencia : null ;                
                $tema_correspondencia = ($params->temaCorrespondencia != null) ? $params->temaCorrespondencia : null ;                
                $cod_referenciaSreci  = ($params->codReferenciaSreci != null) ? $params->codReferenciaSreci : null ;   
                
                $fecha_ingreso        = new \DateTime('now');
                $fecha_maxima_entrega = ($params->fechaMaxEntrega != null) ? $params->fechaMaxEntrega : null ;
                // Seteo de la Fecha, viene en Json (String) se tiene que convertir a su dato Nativo (Date)
                $fecha_maxima_entrega_date = new \DateTime($fecha_maxima_entrega);
                
                // Fechas Nulas
                $fecha_null = new \DateTime('2999-12-31');
                
                //Relaciones de la Tabla con Otras.
                // Envio por Json el Codigo de Institucion | Buscar en la Tabla: TblInstituciones
                $cod_institucion      = ($params->idInstitucion != null) ? $params->idInstitucion : null ;
                
                // Envio por Json el Codigo de Usuario | Buscar en la Tabla: TblUsuarios
                $cod_usuario          = $identity->sub;
                
                // Envio por Json el Codigo de Estados | Buscar en la Tabla: TblEstados
                $cod_estado           = ($params->idEstado != null) ? $params->idEstado : null ;                
                
                // Envio por Json el Codigo de Direccion Sreci | Buscar en la Tabla: TblDireccionesSreci
                $cod_direccion_sreci  = ($params->idDireccionSreci != null) ? $params->idDireccionSreci : null ;
                
                // Envio por Json el Codigo de Depto Funcional | Buscar en la Tabla: TblDepartamentosSreci
                $cod_depto_funcional   = ($params->idDeptoFuncional != null) ? $params->idDeptoFuncional : null ;
                
                // Envio por Json el Codigo de Depto Acompañante | Buscar en la Tabla: TblDepartamentosSreci
                $cod_depto_acomp   = ($params->idDeptoFuncionalAcom != null) ? $params->idDeptoFuncionalAcom : null ;
                
                // Envio por Json el Codigo de Depto Funcional | Buscar en la Tabla: TblDepartamentosFuncionales
                $cod_tipo_documento  = ($params->idTipoDocumento != null) ? $params->idTipoDocumento : null ;
                
                // Relacion con la Tabla Correspondencia Detalle | Proceso de Respuesta
                $new_secuencia_det        = ($params->secuenciaComunicacionDet != null) ? $params->secuenciaComunicacionDet : null ;
                $cod_correspondencia_det  = ($params->codCorrespondenciaDet != null) ? $params->codCorrespondenciaDet : null ;
               
                
                // Ruta del Pdf a Subir
                $pdf_send  = ($params->pdfDocumento != null) ? $params->pdfDocumento : null ;
                
                // idUsario que tendra asignado el Oficio
                $id_usuario_asignado = ($params->idUsuarioAsaignado != null) ? $params->idUsuarioAsaignado : null ;
                
                
                //Evaluamos que el Codigo de Correspondencia no sea Null y la Descripcion tambien
                if($cod_correspondencia != null && $desc_correspondencia != null && $cod_referenciaSreci != null &&
                   $tema_correspondencia != null && $cod_depto_funcional != 0 && $cod_institucion != 0 &&
                   $id_usuario_asignado != 0 ){
                    //La condicion fue Exitosa
                    //Instancia del Doctrine
                    $em = $this->getDoctrine()->getManager();
                    
                    //Seteo de Datos Generales de la tabla: TblCorrespondenciaEnc
                    $correspondenciaNew = new TblCorrespondenciaEnc();                   
                    
                    
                    // Buscamos el Id de la Secuencia y Generamos el Codigo
                    $correspondenciaNew->setCodCorrespondenciaEnc($cod_correspondencia. "-" . $new_secuencia);                    
                    
                    $correspondenciaNew->setDescCorrespondenciaEnc($desc_correspondencia);                    
                    $correspondenciaNew->setFechaIngreso($fecha_ingreso);
                    $correspondenciaNew->setFechaIngreso($fecha_ingreso);
                    $correspondenciaNew->setFechaModificacion($fecha_null);
                    $correspondenciaNew->setFechaFinalizacion($fecha_null);
                    
                    $correspondenciaNew->setFechaMaxEntrega($fecha_maxima_entrega_date);
                    
                    // Nuevo Campo de Codigo de Refrencia SRECI ----------------
                                        
                    //Preguntamos si Tipo Doc Es 1 | Oficio ********************
                    if( $cod_tipo_documento === "1" ){
                        // Busqueda del Codigo de la Secuencia a Actualizar | Correspondencia Enc
                        $secuenciaSCPI = $em->getRepository("BackendBundle:TblSecuenciales")->findOneBy(                            
                            array(
                               "codSecuencial"  => "SCPI"
                            ));
                        $cod_referenciaSCPI = $secuenciaSCPI->getCodSecuencial();
                        $valor2_secuenciaSCPI = $secuenciaSCPI->getValor2() + 1;
                        $secuenciaSCPI->setValor2($valor2_secuenciaSCPI);

                        $em->persist($secuenciaSCPI);
                        //Realizar la actualizacion en el storage de la BD
                        $em->flush();


                        // Consulta a los Datos del Depto Funcional *************
                        $deptoFuncConsulta = $em->getRepository("BackendBundle:TblDepartamentosFuncionales")->findOneBy(
                            array(
                               "idDeptoFuncional" => $identity->idDeptoFuncional
                            ));
                        $inicialesDeptoFunc = $deptoFuncConsulta->getInicialesDeptoFuncional();
                        
                    $cod_referenciaSreci = $cod_referenciaSCPI . "-". $inicialesDeptoFunc . "-". $valor2_secuenciaSCPI;
                    } // Fin Codicion de Oficio Secuencial SCPI ****************
                    
                    // Seteamos el Valor de Codigo de Referencia | SCPI-DEPTO-CORRELATIVO
                    $correspondenciaNew->setCodReferenciaSreci( $cod_referenciaSreci );
                    $correspondenciaNew->setTemaComunicacion($tema_correspondencia);
                    
                    $correspondenciaNew->setIdDeptoAcomp($cod_depto_acomp);
                    
                    //variables de Otras Tablas, las Buscamos para saber si hay Integridad                
                    //Instanciamos de la Clase TblInstituciones
                    $institucion = $em->getRepository("BackendBundle:TblInstituciones")->findOneBy(
                        array(
                           "idInstitucion" => $cod_institucion                        
                        ));                    
                    $correspondenciaNew->setIdInstitucion($institucion); //Set de Codigo de Institucion
                    
                    //Instanciamos de la Clase TblUsuario
                    $usuario = $em->getRepository("BackendBundle:TblUsuarios")->findOneBy(
                        array(
                           "idUsuario" => $cod_usuario                         
                        ));                    
                    $correspondenciaNew->setIdUsuario($usuario); //Set de Codigo de Usuario
                    
                    //Instanciamos de la Clase TblEstados                        
                    $estado = $em->getRepository("BackendBundle:TblEstados")->findOneBy(                            
                        array(
                           "idEstado" => $cod_estado
                        ));                    
                    $correspondenciaNew->setIdEstado($estado); //Set de Codigo de Estados   
                    
                    //Instanciamos de la Clase TblDireccionesSreci                        
                    $direccion = $em->getRepository("BackendBundle:TblDireccionesSreci")->findOneBy(                            
                        array(
                           "idDireccionSreci" => $cod_direccion_sreci
                        ));                    
                    $correspondenciaNew->setIdDireccionSreci($direccion); //Set de Codigo de Dreicciones Sreci 
                    
                    //Instanciamos de la Clase TblDireccionesSreci                        
                    $depto_funcional = $em->getRepository("BackendBundle:TblDepartamentosFuncionales")->findOneBy(                            
                        array(
                           "idDeptoFuncional" => $cod_depto_funcional
                        ));                    
                    $correspondenciaNew->setIdDeptoFuncional($depto_funcional); //Set de Codigo de Dreicciones Sreci 
                    
                    //Instanciamos de la Clase TblTipoDocumentos
                    $tipo_documento_in = $em->getRepository("BackendBundle:TblTipoDocumento")->findOneBy(                            
                        array(
                           "idTipoDocumento" => $cod_tipo_documento
                        ));                    
                    $correspondenciaNew->setIdTipoDocumento($tipo_documento_in); //Set de Codigo de Tipo de Documentos 
                    
                    
                    //Instanciamos de la Clase TblFuncionarios
                    $usuario_asignado = $em->getRepository("BackendBundle:TblFuncionarios")->findOneBy(
                        array(
                           "idFuncionario" => $id_usuario_asignado                
                        ));                    
                    $correspondenciaNew->setIdFuncionarioAsignado($usuario_asignado); //Set de Codigo de Funcionario Asignado
                                                         
                    //Finaliza Busqueda de Integridad entre Tablas
                    
                    
                    //Verificacion del Codigo de la Correspondenia *******************
                    $isset_corresp_cod = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findBy(
                        array(
                          "codCorrespondenciaEnc" => $cod_correspondencia . "-" . $new_secuencia
                        ));
                    
                    //Verificacion del Codigo de Referencia de la Correspondenia *******************
                    //if( $cod_tipo_documento === "1" ){
                        $isset_referencia_cod = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findBy(
                            array(
                                "codReferenciaSreci" => $cod_referenciaSreci,
                                "idTipoDocumento"    => 1
                            ));                    
                    //}else { $isset_referencia_cod = 0; }
                    
                    
                    //Verificamos que el retorno de la Funcion sea = 0 ********* 
                    if(count($isset_corresp_cod) == 0 && count($isset_referencia_cod) == 0){
                        //Instanciamos de la Clase TblSecuenciales
                        //Seteo del nuevo secuencial de la tabla: TblSecuenciales
                        $secuenciaNew = new TblSecuenciales();
                        
                        
                        // Busqueda del Codigo de la Secuencia a Actualizar | Correspondencia Enc
                        $secuenciaNew = $em->getRepository("BackendBundle:TblSecuenciales")->findOneBy(                            
                            array(
                                "codSecuencial"  => $cod_correspondencia
                            ));                    
                        $secuenciaNew->setValor2($new_secuencia); //Set de valor2 de Secuencia de Oficios
                        
                        
                        //Realizar la Persistencia de los Datos y enviar a la BD
                        $em->persist($correspondenciaNew);
                        
                        $em->persist($secuenciaNew);
                        //Realizar la actualizacion en el storage de la BD
                        $em->flush();
                        
                        
                        // Ingresamos los Datos a la Tabla TblEncabezadosDet **********
                        //Seteo del nuevo secuencial de la tabla: TblCorrespondenciaDet
                        // ************************************************************
                        $correspondenciaDet = new TblCorrespondenciaDet();
                        
                        //Ingresamos un valor en la Tabla **********************
                        //Correspondencia Enc **********************************                        
                        $correspondenciaDet->setCodCorrespondenciaDet($cod_correspondencia_det . "-" . $new_secuencia_det); //Set de Codigo Correspondencia
                        $correspondenciaDet->setFechaIngreso($fecha_ingreso); //Set de Fecha Ingreso
                        
                        $correspondenciaDet->setFechasalida($fecha_null); //Set de Fecha Ingreso
                        
                        $correspondenciaDet->setCodReferenciaSreci($cod_referenciaSreci); //Set de Codigo Ref SRECI
                        
                        $correspondenciaDet->setDescCorrespondenciaDet("Creacion de Comunicacion: " . $cod_correspondencia . "-" . $new_secuencia_det); //Set de Descripcion Inicial
                        $correspondenciaDet->setActividadRealizar("Pendiente de Crear respuesta a comunicación: " . $cod_correspondencia . "-" . $new_secuencia_det); //Set de Actividad Inicial
                        
                       
                        //Verificacion del Codigo de la Correspondenia *********
                        $id_correspondencia_enc = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findOneBy(
                            array(
                                "codCorrespondenciaEnc" => $cod_correspondencia . "-" . $new_secuencia
                            ));
                        $correspondenciaDet->setIdCorrespondenciaEnc($id_correspondencia_enc); //Set de Fecha Id Correspondencia Enc
                        
                        //Instanciamos de la Clase TblEstados                        
                        $estadoDet = $em->getRepository("BackendBundle:TblEstados")->findOneBy(                            
                            array(
                                "idEstado" => $estado
                            ));                    
                        $correspondenciaDet->setIdEstado($estadoDet); //Set de Codigo de Estados                        
                        
                        //Instanciamos de la Clase TblUsuario
                        $usuarioDetalle = $em->getRepository("BackendBundle:TblUsuarios")->findOneBy(
                            array(
                               "idUsuario" => $cod_usuario                           
                            ));                    
                        $correspondenciaDet->setIdUsuario($usuarioDetalle); //Set de Codigo de Usuario
                        
                        //Instanciamos de la Clase TblFuncionarios
                        $usuario_asignado = $em->getRepository("BackendBundle:TblFuncionarios")->findOneBy(
                        array(
                           "idFuncionario" => $id_usuario_asignado                
                        ));                    
                        $correspondenciaDet->setIdFuncionarioAsignado($usuario_asignado); 
                        
                        
                        // Busqueda del Codigo de la Secuencia a Actualizar | Correspondencia Det
                        $secuenciaNew = $em->getRepository("BackendBundle:TblSecuenciales")->findOneBy(                            
                            array(
                                "codSecuencial"  => $cod_correspondencia_det
                            ));                    
                        $secuenciaNew->setValor2($new_secuencia_det); //Set de valor2 de Secuencia de Oficios
                        
                        // Llamamo a la Funcion Interna para que nos convierta *
                        // La Fecha a Calendario Gregoriano ********************
                        
                        $fecha_maxima_entrega_time_stamp = json_encode($correspondenciaDet->getFechaIngreso()->getTimestamp(), true ); 
                        // Ejecucion de la Funcion *****************************
                        $fecha_maxima_entrega_convert = $this->convertirFechasTimeStampAction( $fecha_maxima_entrega_time_stamp );
                        
                        // Relizamos la persistencia de Datos de las Comunicaciones Detalle
                        $em->persist($correspondenciaDet);
                        
                        // Realizamos la persistencia de la Secuencia
                        $em->persist($secuenciaNew);
                        
                        //Realizar la actualizacion en el storage de la BD
                        $em->flush();                        
                        
                        
                        // Ingresamos los Datos a la Tabla TblDocumentos *******
                        //Seteo del nuevo documentos de la tabla: TblDocumentos
                        // *****************************************************
                        if( $pdf_send != null ){
                            $documentosIn = new TblDocumentos();

                            $documentosIn->setCodDocumento($cod_correspondencia . "-" . $new_secuencia); //Set de Codigo Documento
                            $documentosIn->setFechaIngreso($fecha_ingreso); //Set Fecha Ingreso

                            $documentosIn->setDescDocumento("Oficio de Respaldo"); //Set Documento Desc
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
                                    "codCorrespondenciaDet" => $cod_correspondencia_det . "-" . $new_secuencia_det
                                ));
                            $documentosIn->setIdCorrespondenciaDet($id_correspondencia_det_docu); //Set de Fecha Id Correspondencia Det


                            // Pdf que se Agrega
                            // validamos que se adjunta pdf
                            $documentosIn->setUrlDocumento($pdf_send . "-" . date('Y-m-d') . ".pdf"); //Set Url de Documento


                            // Relizamos la persistencia de Datos de las Comunicaciones Detalle
                            $em->persist($documentosIn); 

                            //Realizar la actualizacion en el storage de la BD
                            $em->flush();
                        }
                        // Fin de Comunicacion Detalle *************************
                        
                        // Envio de Correo despues de la Granacion de Datos
                        // *****************************************************
                        //Instanciamos de la Clase TblFuncionarios, para Obtener
                        // los Datos de envio de Mail **************************
                        $usuario_asignado_send = $em->getRepository("BackendBundle:TblFuncionarios")->findOneBy(
                            array(
                                "idFuncionario" => $id_usuario_asignado                
                            ));
                        // Parametros de Salida
                        $mailSend = $usuario_asignado_send->getEmailFuncionario() ; // Get de mail de Funcionario Asignado
                        $nombreSend = $usuario_asignado_send->getNombre1Funcionario() ; // Get de Nombre de Funcionario Asignado
                        $apellidoSend = $usuario_asignado_send->getApellido1Funcionario() ; // Get de Apellido de Funcionario Asignado
                           
                        
                            //Creamos la instancia con la configuración 
                            $transport = \Swift_SmtpTransport::newInstance()
                               ->setHost('smtp.gmail.com')
                               //->setHost('smtp.mail.yahoo.com')
                               ->setPort(587)
                               //->setPort(465)
                               ->setEncryption('tls')
                               ->setStreamOptions(array(
                                                    'ssl' => array(
                                                        'allow_self_signed' => true, 
                                                        'verify_peer' => false, 
                                                        'verify_peer_name' => false
                                                        )
                                                    )
                                                 )
                               //->setEncryption('ssl')                               
                               ->setUsername( $identity->email )
                               //->setUsername( 'nahum.sreci@gmail.com')
                               //->setUsername( 'gcallejas.sreci@gmail.com')
                               ->setPassword('1897Juve');
                               //->setPassword('gec2017*');
                           
                           //Creamos la instancia del envío
                           $mailer = \Swift_Mailer::newInstance($transport);
                           
                           //Creamos el mensaje
                           $mail = \Swift_Message::newInstance()
                               ->setSubject('Notificación de Ingreso de Comunicacion | SICDOC')
                               ->setFrom(array($identity->email => $identity->nombre . " " .  $identity->apellido ))
                               ->setTo($mailSend)                               
                               ->setBody(
                                    $this->renderView(
                                    // app/Resources/views/Emails/registration.html.twig
                                        'Emails/sendMailComunicacion.html.twig',
                                        array( 'name' => $nombreSend, 'apellidoOficio' => $apellidoSend,
                                               'oficioExtNo' => $cod_referenciaSreci, 'oficioInNo' => $cod_correspondencia . "-" . $new_secuencia,
                                               'temaOficio' => $tema_correspondencia, 'descOficio' => $desc_correspondencia,
                                               'fechaIngresoOfi' => strval($fecha_maxima_entrega), 'fechaMax' => $fecha_maxima_entrega_convert )
                                    ), 'text/html' );                           
                           
                            // validamos que se adjunta pdf
                            if( $pdf_send != null ){
                              $target_path1 = "uploads/correspondencia/correspondencia_" . date('Y-m-d') . "/" . $pdf_send . "-" .date('Y-m-d'). ".pdf";                            
                              //$target_path1 = "COM-IN-OFI-11-2017-09-12.pdf";                            
                              $mail->attach(\Swift_Attachment::fromPath($target_path1));                                
                            }
                                 
                            // Envia el Correo con todos los Parametros
                            $resuly = $mailer->send($mail);
                                                
                        // ***** Fin de Envio de Correo ************************
                        // 
                        // 
                        //Consulta de esa Correspondencia recien Ingresada *****
                        $correspondenciaConsulta = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findOneBy(
                            array(                                
                                "codCorrespondenciaEnc" => $cod_correspondencia 
                            ));
                    
                            //Array de Mensajes
                            $data = array(
                                "status" => "success", 
                                "code"   => 200, 
                                "msg"    => "Se ha ingresado la comunicacion No. " . $cod_correspondencia . "-" . $new_secuencia,
                                "data"   => $correspondenciaConsulta
                            );
                    }else{
                        $data = array(
                            "status" => "error",
                            "desc"   => "Ya existe un codigo",
                            "code"   => 400, 
                            "msg"   => "Error al registrar, ya existe una correspondencia con este código, ". $cod_referenciaSreci . 
                                       " por favor ingrese otro !! "
                        );                       
                    }//Finaliza el Bloque de la validadacion de la Data en la Tabla
                    // TblCorrespondenciaEnc
                } else {
                    //Array de Mensajes
                    $data = array(
                       "status" => "error",
                       "desc"   => "Eror al Enviar el Json, faltan parametros",
                       "code"   => 400, 
                       "msg"   => "No se ha podido crear la correspondencia, falta ingresar información  !!"
                    );
                }                
            } else {
                    //Array de Mensajes
                    $data = array(
                       "status" => "error",
                       "desc"   => "Eror al Enviar el Json, el Json no ha sido enviado",
                       "code"   => 400, 
                       "msg"   => "Correspondencia no creada, falta ingresar los parametros !!"
                    );
                }
            } else {
            $data = array(
                "status" => "error",
                "desc"   => "El Token, es invalido",    
                "code" => "400",                
                "msg" => "Autorizacion de Token no valida !!"                
            );
        }        
        //Retorno de la Funcion ************************************************
        return $helpers->parserJson($data);
    } //Fin de la Funcion New Correspondencia **********************************
    
    
    /**
     * Creacion del Controlador: Transforma Fechas Time Stamp
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00001
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
    } // FIN | FND00001
    
}

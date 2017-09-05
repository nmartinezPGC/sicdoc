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
    public function newCorrespondenciaAction(Request $request) {
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
                
                //Relaciones de la Tabla con Otras.
                // Envio por Json el Codigo de Institucion | Buscar en la Tabla: TblInstituciones
                $cod_institucion      = ($params->idInstitucion != null) ? $params->idInstitucion : null ;
                
                // Envio por Json el Codigo de Usuario | Buscar en la Tabla: TblUsuarios
                $cod_usuario          = $identity->sub;
                
                // Envio por Json el Codigo de Estados | Buscar en la Tabla: TblEstados
                $cod_estado           = ($params->idEstado != null) ? $params->idEstado : null ;                
                
                // Envio por Json el Codigo de Direccion Sreci | Buscar en la Tabla: TblDireccionesSreci
                $cod_direccion_sreci  = ($params->idDireccionSreci != null) ? $params->idDireccionSreci : null ;
                
                // Envio por Json el Codigo de Tipo de Documento | Buscar en la Tabla: TblTipoDocumento
                $cod_depto_funcional   = ($params->idDeptoFuncional != null) ? $params->idDeptoFuncional : null ;
                
                // Envio por Json el Codigo de Depto Funcional | Buscar en la Tabla: TblDepartamentosFuncionales
                $cod_tipo_documento  = ($params->idTipoDocumento != null) ? $params->idTipoDocumento : null ;
                
                // Relacion con la Tabla Correspondencia Detalle | Proceso de Respuesta
                $new_secuencia_det        = ($params->secuenciaComunicacionDet != null) ? $params->secuenciaComunicacionDet : null ;
                $cod_correspondencia_det  = ($params->codCorrespondenciaDet != null) ? $params->codCorrespondenciaDet : null ;
                
                
                // Informacion para el envio de los correos a las Direcciones
                $email_direccion  = ($params->emailDireccion != null) ? $params->emailDireccion : null ;
                
                
                //Evaluamos que el Codigo de Correspondencia no sea Null y la Descripcion tambien
                if($cod_correspondencia != null && $desc_correspondencia != null){
                    //La condicion fue Exitosa
                    //Instancia del Doctrine
                    $em = $this->getDoctrine()->getManager();
                    
                    //Seteo de Datos Generales de la tabla: TblCorrespondenciaEnc
                    $correspondenciaNew = new TblCorrespondenciaEnc();                   
                    
                    
                    // Buscamos el Id de la Secuencia y Generamos el Codigo
                    $correspondenciaNew->setCodCorrespondenciaEnc($cod_correspondencia);                    
                    
                    $correspondenciaNew->setDescCorrespondenciaEnc($desc_correspondencia);                    
                    $correspondenciaNew->setFechaIngreso($fecha_ingreso);
                    $correspondenciaNew->setFechaMaxEntrega($fecha_maxima_entrega_date);
                    
                    // Nuevo Campo de Codigo de Refrencia SRECI ----------------
                    $correspondenciaNew->setCodReferenciaSreci($cod_referenciaSreci);
                    $correspondenciaNew->setTemaComunicacion($tema_correspondencia);
                    
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
                                                         
                    //Finaliza Busqueda de Integridad entre Tablas
                    
                    
                    //Verificacion del Codigo de la Correspondenia *******************
                    $isset_corresp_cod = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findBy(
                        array(
                          "codCorrespondenciaEnc" => $cod_correspondencia
                        ));
                    
                    //Verificamos que el retorno de la Funcion sea = 0 ********* 
                    if(count($isset_corresp_cod) == 0){
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
                        
                        //$correspondenciaDet->setDescCorrespondenciaDet($cod_correspondencia); //Set de Fecha Ingreso
                        
                       
                        //Verificacion del Codigo de la Correspondenia *********
                        $id_correspondencia_enc = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findOneBy(
                            array(
                                "codCorrespondenciaEnc" => $cod_correspondencia
                            ));
                        $correspondenciaDet->setIdCorrespondenciaEnc($id_correspondencia_enc); //Set de Fecha Id Correspondencia Enc
                        
                        //Instanciamos de la Clase TblEstados                        
                        $estadoDet = $em->getRepository("BackendBundle:TblEstados")->findOneBy(                            
                            array(
                                "idEstado" => 7
                            ));                    
                        $correspondenciaDet->setIdEstado($estadoDet); //Set de Codigo de Estados
                        //$correspondenciaDet->setIdCorrespondenciaEnc($isset_corresp_cod);
                        
                        
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
                        $documentosIn = new TblDocumentos();
                        
                        $documentosIn->setCodDocumento($cod_correspondencia); //Set de Codigo Documento
                        $documentosIn->setFechaIngreso($fecha_ingreso); //Set Fecha Ingreso
                        
                        //Instanciamos de la Clase TblUsuario
                        $usuarioDocumento = $em->getRepository("BackendBundle:TblUsuarios")->findOneBy(
                            array(
                               "idUsuario" => $cod_usuario                           
                            ));                    
                        $documentosIn->setIdUsuario($usuarioDocumento); //Set de Codigo de Usuario                        
                        //$documentosIn->setIdUsuario($identity->sub); //Set Fecha Ingreso
                        
                        // Relizamos la persistencia de Datos de las Comunicaciones Detalle
                        $em->persist($documentosIn); 
                        
                        //Realizar la actualizacion en el storage de la BD
                        $em->flush();
                        
                        
                        
                        // Envio de Correo despues de la Granacion de Datos
                        // *****************************************************                        
                        //try{
                        // echo "Paso 1.0";
                       //require_once './swiftmailer/lib/swift_required.php';
                      // echo "Paso 1.02";
                       //require __DIR__.'/classes/Swift.php';
                       //require_once '/PATH/library/SwiftMailer/swift_required.php'; 
                            //    C:\wamp64\www\sicdoc\symfony\vendor\swiftmailer\swiftmailer\lib
                            //$mailer = $this->container->get('mailer');
                            //Creamos la instancia con la configuración 
                            $transport = \Swift_SmtpTransport::newInstance()
                               ->setHost('smtp.gmail.com')
                               ->setPort(587)
                               ->setEncryption('tls')
                               ->setUsername('nahum.sreci@gmail.com')
                               ->setPassword('1897Juve');
                           //echo "Paso 1";
                           //Creamos la instancia del envío
                           $mailer = \Swift_Mailer::newInstance($transport);
                           //echo "Paso 2";
                           //Creamos el mensaje
                           $mail = \Swift_Message::newInstance()
                               ->setSubject('Notificación de Ingreso de Oficio | SICDOC')
                               ->setFrom(array($identity->email => $identity->nombre . " " .  $identity->apellido ))
                               ->setTo($email_direccion)
                               ->setBody('Estimado(a) Sr. Direcctor(a) <br> '
                                       . '<b> Se notifica el ingreso del oficio: </b>' . $cod_correspondencia . 
                                         ' <br> <b>Tema</b>: ' . $tema_correspondencia . 
                                         ' <br> <b>Descripción</b>: ' . $desc_correspondencia , 'text/html');
                           
                            
                                         
                            $target_path1 = "uploads/users/1503335120.png";
                            $target_path2 = "uploads/users/1503335511.jpeg";
                            $mail->attach(\Swift_Attachment::fromPath($target_path1));
                            $mail->attach(\Swift_Attachment::fromPath($target_path2));
                            
                                         
                               //->attach(Swift_Attachment::fromPath('../../../web/uploads/users/1503335120.png'));
                           //echo "Paso 3";
                           //Enviamos el correo
                           //$this->get('mailer')->send($mail);
                           $resuly = $mailer->send($mail);
                           //echo "Paso 4  " ;
                       /*} catch(Swift_TransportException  $e) {
                           echo ("Error al enviar mensaje: " + $e->getMessage());
                       }*/
                        
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
                            "msg"   => "Error al registrar, ya existe una correspondencia con este código, ". $cod_correspondencia . 
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
                       "msg"   => "No se ha podido crear la correspondencia, error en los parametros !!"
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
    
    
    /* Funcion de Editar Correspondencia****************************************
     * Parametros:                                                             * 
     * 1 ) Recibe un Objeto Request con el Metodo POST, el Json de la          *  
     *     Informacion.                                                        * 
     * 2 ) Recibe el Codigo del Documento por medio de la Url.                 * 
     ***************************************************************************/
    public function editCorrespondenciaAction(Request $request, $id = null) {
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
    } //Fin de la Funcion Editar Correspondencia *******************************

    
}

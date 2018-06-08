<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\JsonResponse;
//Importamos las Tablas a Relacionar
use BackendBundle\Entity\TblCorrespondenciaEntrante;
use BackendBundle\Entity\TblCorrespondenciaDet;
use BackendBundle\Entity\TblUsuarios;
use BackendBundle\Entity\TblDocumentosUnidadCorrespondencia;
use BackendBundle\Entity\TblInstituciones;
use BackendBundle\Entity\TblEstados;
use BackendBundle\Entity\TblDireccionesSreci;
use BackendBundle\Entity\TblSecuenciales;
use BackendBundle\Entity\TblTipoComunicacion;

use Swift_MessageAcceptanceTest;

// Tabla de Secuencias Comprometidas
use BackendBundle\Entity\TblSecuenciasComprometidas;

//Traslado de Comunicacion
use BackendBundle\Entity\TblTrasladoComunicacion;
use BackendBundle\Entity\TblBitacoraCorrespondenciaEdit;


/********************************************************************
 * Description of EntradaCorrespondenciaController                  *
 * Entrada de Correspondencia en la Tabla: TblCorrespondenciaEnc    *
 * @author Nahum Martinez <nahum.sreci@gmail.com>                   *
 * @category Correspondencia/Ingreso                                *
 * @version 1.0                                                     *
 * Fecha: 07-06-2018                                                *
 ********************************************************************/
class UnidadCorrespondenciaController extends Controller{
    
    
    /* Funcion de Nuevo Correspondencia ****************************************
     * Parametros:                                                             * 
     * 1 ) Recibe un Objeto Request con el Metodo POST, el Json de la          *  
     *     Informacion.                                                        *
     * @Route("/unidad-correspondencia/entrada-correspondencia", name="/unidad-correspondencia/entrada-correspondencia") 
     ***************************************************************************/
    public function entradaCorrespondenciaAction(Request $request) 
    {
        //Instanciamos el Servicio Helpers
        date_default_timezone_set('America/Tegucigalpa');
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
                $cod_correspondencia  = ($params->codCorrespondencia != null) ? $params->codCorrespondencia : null ;
                
                $desc_correspondencia = ($params->descCorrespondencia != null) ? $params->descCorrespondencia : null ;                
                $tema_correspondencia = ($params->temaCorrespondencia != null) ? $params->temaCorrespondencia : null ;                
                $observacion_correspondencia = ($params->observaciones != null) ? $params->observaciones : null ;
                
                //$cod_referenciaSreci  = ($params->codReferenciaSreci != null) ? $params->codReferenciaSreci : null ;   
                
                $fecha_ingreso        = new \DateTime('now');
                
                $hora_ingreso = new \DateTime('now');            
                $hora_ingreso->format('H:i');                
                                       
                // Fechas Nulas
                $fecha_null = new \DateTime('2999-12-31');
                
                //Relaciones de la Tabla con Otras.
                //Envio por Json el Codigo de Institucion | Buscar en la Tabla: TblInstituciones
                $cod_institucion      = ($params->idInstitucion != null) ? $params->idInstitucion : null ;
                
                // Envio por Json el Codigo de Usuario | Buscar en la Tabla: TblUsuarios
                $cod_usuario          = $identity->sub;
                
                // Envio por Json el Codigo de Estados | Buscar en la Tabla: TblEstados
                $cod_estado           = ($params->idEstado != null) ? $params->idEstado : null ;                
                
                // Envio por Json el Codigo de Direccion Sreci | Buscar en la Tabla: TblDireccionesSreci
                $cod_direccion_sreci  = ($params->idDireccionSreci != null) ? $params->idDireccionSreci : null ;                                
                
                // Envio por Json el Codigo de Depto Funcional | Buscar en la Tabla: TblDepartamentosFuncionales
                $cod_tipo_documento  = ($params->idTipoDocumento != null) ? $params->idTipoDocumento : null ;                                
                
                // Ruta del Pdf a Subir
                $pdf_send  = ($params->pdfDocumento != null) ? $params->pdfDocumento : null ;
                                                                                            
                
                //Evaluamos que el Codigo de Correspondencia no sea Null y la Descripcion tambien
                if($cod_correspondencia != null && $desc_correspondencia != null &&
                   $tema_correspondencia != null && $cod_institucion != 0 ){
                    //La condicion fue Exitosa
                    //Instancia del Doctrine
                    $em = $this->getDoctrine()->getManager();
                    
                    //Seteo de Datos Generales de la tabla: TblCorrespondenciaEntrante
                    $correspondenciaNew = new TblCorrespondenciaEntrante();                   
                    
                    
                    // Buscamos el Id de la Secuencia y Generamos el Codigo
                    $correspondenciaNew->setCodCorrespondenciaEntrante($cod_correspondencia);                    
                    
                    $correspondenciaNew->setDescripcionCorrespondencia($desc_correspondencia);                    
                    $correspondenciaNew->setObservacionesCorrespondencia($observacion_correspondencia);                    
                    $correspondenciaNew->setFechaIngreso($fecha_ingreso);                    
                    
                    $correspondenciaNew->setHoraIngreso($hora_ingreso); // Hora de Creacion                                        
                                                            
                    $correspondenciaNew->setAsuntoCorrespondencia($tema_correspondencia);                                      
                    
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
                    
                    // 2018-02-20
                    // Si la Comuniacion es Sin Seguimiento la Hora de Finalizacion es la Misma que la de la Ingreso
                    /*if( $estado->getIdEstado() == 5 ){
                        $correspondenciaNew->setHoraFinalizacion( $hora_ingreso ); // Hora de Finalizacion
                        $correspondenciaNew->setFechaFinalizacion( $fecha_ingreso ); // Fecha de Finalizacion
                    }*/
                    
                    //Instanciamos de la Clase TblDireccionesSreci                        
                    $direccion = $em->getRepository("BackendBundle:TblDireccionesSreci")->findOneBy(                            
                        array(
                           "idDireccionSreci" => $cod_direccion_sreci
                        ));                    
                    $correspondenciaNew->setIdDireccionSreci($direccion); //Set de Codigo de Dreicciones Sreci                                        
                    
                    //Instanciamos de la Clase TblTipoDocumentos
                    $tipo_documento_in = $em->getRepository("BackendBundle:TblTipoDocumento")->findOneBy(                            
                        array(
                           "idTipoDocumento" => $cod_tipo_documento
                        ));                    
                    $correspondenciaNew->setIdTipoDocumento($tipo_documento_in); //Set de Codigo de Tipo de Documentos                                                             
                                        
                    //Finaliza Busqueda de Integridad entre Tablas *************
                    
                    
                    //Verificacion del Codigo de la Correspondencia *******************
                    $isset_corresp_cod = $em->getRepository("BackendBundle:TblCorrespondenciaEntrante")->findOneBy(
                        array(
                          "codCorrespondenciaEntrante" => $cod_correspondencia
                        ));
                                        
                    
                    //Verificamos que el retorno de la Funcion sea = 0 ********* 
                    if(count($isset_corresp_cod) == 0 ){
                        //echo "Error 1";
                        //Realizar la Persistencia de los Datos y enviar a la BD
                        $em->persist($correspondenciaNew);
                                                
                        //Realizar la actualizacion en el storage de la BD
                        $em->flush();
                        
                        
                        // Ingresamos los Datos a la Tabla TblDocumentosUnidadCorrespondencia *******
                        //Seteo del nuevo documentos de la tabla: TblDocumentosUnidadCorrespondencia
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
                                $nombreDoc = $arr->nombreDoc;
                                
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
                                
                                /* INC00002 | 2018-01-09
                                * Corregir la Extencion del PNG a png
                                */
                                if( $extDoc == "PNG" ){
                                    $extDoc = "png";
                                }
                                //var_dump($nameDoc);
                                
                                $documentosIn = new TblDocumentosUnidadCorrespondencia();

                                //Seteamos los valores de los Documentos Cargados                                
                                $documentosIn->setFechaIngreso($fecha_ingreso); //Set Fecha Ingreso

                                //$documentosIn->setDescDocumento("Documento de Respaldo"); //Set Documento Desc
                                $documentosIn->setDescDocumento($nombreDoc); //Set Documento Desc / 2018-02-28                                

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
                                        "codCorrespondenciaEntrante" => $cod_correspondencia_det . "-" . $new_secuencia_det
                                    ));
                                $documentosIn->setIdCorrespondenciaDet($id_correspondencia_det_docu); //Set de Id Correspondencia Det
                                
                                // Verificacion del Codigo de la Unidad Correspondenia*
                                // Encabezado  *********************************
                                $id_correspondencia_enc_docu = $em->getRepository("BackendBundle:TblCorrespondenciaEntrante")->findOneBy(
                                    array(
                                        "codCorrespondenciaEntrante" => $cod_correspondencia
                                    ));
                                $documentosIn->setIdCorrespondenciaEntrante($id_correspondencia_enc_docu); //Set de Id Correspondencia Entrante

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
                        
                        // Fin de Comunicacion Detalle *************************
                        
                        // Envio de Correo despues de la Grabacion de Datos
                        // *****************************************************
                        //Instanciamos de la Clase TblFuncionarios, para Obtener
                        // los Datos de envio de Mail **************************
                        /*$usuario_asignado_send = $em->getRepository("BackendBundle:TblFuncionarios")->findOneBy(
                            array(
                                "idUsuario" => $cod_usuario
                            ));*/
                        // Parametros de Salida
                        /*$mailSend = $usuario_asignado_send->getEmailFuncionario() ; // Get de mail de Funcionario Asignado
                        $nombreSend = $usuario_asignado_send->getNombre1Funcionario() ; // Get de Nombre de Funcionario Asignado
                        $apellidoSend = $usuario_asignado_send->getApellido1Funcionario() ; // Get de Apellido de Funcionario Asignado*/
                                                
                            //Creamos la instancia con la configuración 
                            /*$transport = \Swift_SmtpTransport::newInstance()
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
                               ->setSubject('Notificación de Ingreso de Correspondencia | SICDOC')                                                            
                               ->setFrom(array("correspondenciascpi@sreci.gob.hn" => "Administrador SICDOC" ))                                       
                               ->setTo($mailSend)                                
                               //->addCc([ $setTo_array_convertIn ])                              
                               ->setBody(
                                    $this->renderView(                                    
                                        'Emails/sendMail.html.twig',
                                        array( 'name' => $nombreSend, 'apellidoOficio' => $apellidoSend,
                                               'oficioExtNo' => $cod_referenciaSreci, 'oficioInNo' => $cod_correspondencia . "-" . $new_secuencia ,
                                               'temaOficio' => $tema_correspondencia, 'descOficio' => $desc_correspondencia,
                                               'fechaIngresoOfi' => strval($fecha_maxima_entrega), 
                                               'fechaIngresoCom' => date_format($fecha_ingreso, "Y-m-d"), 'obsComunicacion' => $observacion_correspondencia,
                                               'institucionCom' => $institucion->getPerfilInstitucion())
                                    ), 'text/html' );                                 
                            // Envia el Correo con todos los Parametros
                            //$resuly = $mailer->send($mail);*/
                                                  
                        // ***** Fin de Envio de Correo ************************
                        // 
                        // 
                        //Consulta de esa Correspondencia recien Ingresada *****
                        $correspondenciaConsulta = $em->getRepository("BackendBundle:TblCorrespondenciaEntrante")->findOneBy(
                            array(                                
                                "codCorrespondenciaEntrante" => $cod_correspondencia 
                            ));
                    
                            //Array de Mensajes
                            $data = array(
                                "status" => "success", 
                                "code"   => 200, 
                                "msg"    => "Se ha ingresado la Correspondencia No. " . $cod_correspondencia .
                                            " pronto recibira una notificación vía correo. Gracias",
                                "data"   => $correspondenciaConsulta
                            );
                    }else{
                        $data = array(
                            "status" => "error",
                            "desc"   => "Ya existe un codigo",
                            "code"   => 400, 
                            "data"   => $isset_corresp_cod,
                            "msg"   => "Error al registrar, ya existe una Correspondencia con este código, ". $cod_correspondencia . 
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
                       "msg"   => "No se ha podido crear la Comunicación, falta ingresar información  !!"
                    );
                }                
            } else {
                    //Array de Mensajes
                    $data = array(
                       "status" => "error",
                       "desc"   => "Eror al Enviar el Json, el Json no ha sido enviado",
                       "code"   => 400, 
                       "msg"   => "Comunicación no creada, falta ingresar los parametros !!"
                    );
                }
            } else {
            $data = array(
                "status" => "error",
                "desc"   => "El Token, es invalido",    
                "code" => 500,                
                "msg" => "Autorizacion de Token no valida, tu sesion ha expirado, cierra y vuelve a iniciar. !!"                
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
     * @Route("/correspondencia/edit-correspondencia", name="/correspondencia/edit-correspondencia")
     ***************************************************************************/
    public function editCorrespondenciaAction(Request $request) 
    {
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
            $identity = $helpers->authCheck($hash, true);
            
            //Convertimos los Parametros POSt a Json
            $json = $request->get("json", null);
            
            //Comprobamos que Json no es Null
            if ($json != null) {
                $params = json_decode($json);

                //Parametros a Convertir                                
                //Parametro de la Url
                $correspondenciaId       = ($params->codCorrespondencia != null) ? $params->codCorrespondencia : null ;  ;
                      
                //Datos generales de la Tabla, que viene del Json
                $desc_correspondencia     = ($params->descCorrespondencia != null) ? $params->descCorrespondencia : null ;                
                $desc_correspondencia_anterior  = ($params->descComunicacionAnterior != null) ? $params->descComunicacionAnterior : null ;                
                $tema_correspondencia     = ($params->temaCorrespondencia != null) ? $params->temaCorrespondencia : null ;                
                $tema_correspondencia_anterior   = ($params->temaComunicacionAnterior != null) ? $params->temaComunicacionAnterior : null ;                
                $id_estado     = ($params->idEstado != null) ? $params->idEstado : null ;
                
                $fecha_modificacion       = new \DateTime('now');
                //$fecha_maxima_entrega     = ($params->fecha_maxima_entrega != null) ? $params->fecha_maxima_entrega : null ;                 
                
                $hora_modificacion = new \DateTime('now');            
                $hora_modificacion->format('H:i');                
                
                //Relaciones de la Tabla con Otras.
                // Envio por Json el Codigo de Institucion | Buscar en la Tabla: TblInstituciones
                $cod_institucion      = ($params->idInstitucion != null) ? $params->idInstitucion : null ;
                $cod_institucion_anterior  = ($params->idInstitucionAnterior != null) ? $params->idInstitucionAnterior : null ;
                
                // Envio por Json el Codigo de Usuario | Buscar en la Tabla: TblUsuarios
                $cod_usuario          = $identity->sub;
                
                // Envio por Json el Codigo de Estados | Buscar en la Tabla: TblEstados
                //$cod_estado           = ($params->cod_estado != null) ? $params->cod_estado : null ;
                
                // Envio por Json el Codigo de Direccion Sreci | Buscar en la Tabla: TblDireccionesSreci
                //$cod_direccion_sreci  = ($params->cod_direccion_sreci != null) ? $params->cod_direccion_sreci : null ;                
                
                
                //Evaluamos que el Codigo de Usuario no sea Null y la Descripcion tambien
                if($correspondenciaId != null && $desc_correspondencia != null){
                    //La condicion fue Exitosa
                    //Instancia del Doctrine
                    $em = $this->getDoctrine()->getManager();
                    
                    //Repositorio de la Tabla: TblCorrespondenciaEnc, se Busca si el Codigo
                    // enviado por Parametro Existe
                    $correspondenciaEdit = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findOneBy(
                        array(
                            "codCorrespondenciaEnc" => $correspondenciaId,
                            "idFuncionarioAsignado" => $cod_usuario
                        ));                   
                        
                        //Evaluo el Resultado del Query, con el Paramtro del Codigo del
                        //Documento, para verificar si existe en la BD
                        if(count($correspondenciaEdit) > 0){
                            //Asignamos el usuario de la Consulta Anterior
                            //$idUsarioCorrespondencia = $correspondenciaEdit->getIdUsuario()->getIdUsuario();
                            
                            //Evaluamos que el Usuario de la Consulta del Token, sea el dueño
                            // de la Correspondencia
                            if (isset($identity->sub)) {
                                //Seteamos los valores de los campos modificados
                                $correspondenciaEdit->setDescCorrespondenciaEnc($desc_correspondencia);
                                $correspondenciaEdit->settemaComunicacion($tema_correspondencia);
                                $correspondenciaEdit->setFechaModificacion($fecha_modificacion);
                                
                                //variables de Otras Tablas, las Buscamos para saber si hay Integridad                
                                //Instanciamos de la Clase TblInstituciones
                                $institucion = $em->getRepository("BackendBundle:TblInstituciones")->findOneBy(
                                    array(
                                       "idInstitucion" => $cod_institucion
                                    ));                    
                                $correspondenciaEdit->setIdInstitucion($institucion); //Set de Codigo de Institucion*/

                                //Instanciamos de la Clase TblUsuario
                                /*$usuario = $em->getRepository("BackendBundle:TblUsuarios")->findOneBy(
                                    array(
                                       "idUsuario" => $cod_usuario
                                    ));                    
                                //$correspondenciaEdit->setIdUsuario($usuario); //Set de Codigo de Usuario*/

                                //Instanciamos de la Clase TblEstados                        
                                /*$estado = $em->getRepository("BackendBundle:TblEstados")->findOneBy(                            
                                    array(
                                       "codEstado" => $cod_estado
                                    ));                    
                                $correspondenciaEdit->setIdEstado($estado); //Set de Codigo de Estados*/   

                                //Instanciamos de la Clase TblDireccionesSreci                        
                                /*$direccion = $em->getRepository("BackendBundle:TblDireccionesSreci")->findOneBy(                            
                                    array(
                                       "codDireccionSreci" => $cod_direccion_sreci
                                    ));                    
                                $correspondenciaEdit->setIdDireccionSreci($direccion); //Set de Codigo de Dreicciones Sreci */
                                //Finaliza Busqueda de Integridad entre Tablas
                                
                                // ************************************************************
                                //Seteo de Datos Generales de la tabla: TblTrasladoComunicacion
                                $bitacoraEditCorrespondencia = new TblBitacoraCorrespondenciaEdit();
                                
                                //Instanciamos de la Clase TblUsuario
                                $usuario_asignado = $em->getRepository("BackendBundle:TblFuncionarios")->findOneBy(
                                    array(
                                       "idUsuario" => $cod_usuario
                                    ));
                                //$bitacoraEditCorrespondencia->setIdFuncionarioAsignado($usuario_asignado); //Set de Codigo de Usuario*/
                                
                                $estado_edit = $em->getRepository("BackendBundle:TblEstados")->findOneBy(
                                    array(
                                     "idEstado" => $id_estado   
                                    ));
                
                                //Instanciamos de la Clase TblInstituciones
                                $institucion_anterior = $em->getRepository("BackendBundle:TblInstituciones")->findOneBy(
                                    array(
                                        "idInstitucion" => $cod_institucion_anterior
                                ));
                                
                                $bitacoraEditCorrespondencia->setIdCorrespondenciaEnc( $correspondenciaEdit ); //Set de Correspondencia Enc
                                //$bitacoraEditCorrespondencia->setIdUsuario( $usuario ); //Set de Usuario Creador
                                $bitacoraEditCorrespondencia->setIdFuncionarioAsignado( $usuario_asignado ); //Set de Funcionario Asignado
                                $bitacoraEditCorrespondencia->setFechaEdicion( $fecha_modificacion ); //Set de Fecha de Modificacion
                                $bitacoraEditCorrespondencia->setHoraEdicion( $hora_modificacion ); //Set Hora de Modificacion
                                $bitacoraEditCorrespondencia->setDescCorrespondenciaAnterior( $desc_correspondencia_anterior ); //Set Descripcion Anterior
                                $bitacoraEditCorrespondencia->setDescCorrespondenciaActual( $desc_correspondencia ); //Set Descripcion Actual
                                $bitacoraEditCorrespondencia->setTemaComunicacionActual( $tema_correspondencia ); //Set Tema Anterior
                                $bitacoraEditCorrespondencia->setTemaComunicacionAnterior( $tema_correspondencia_anterior ); //Set Tema Actual
                                $bitacoraEditCorrespondencia->setIdInstitucionAnterior( $institucion_anterior ); //Set Institucion Anterior
                                $bitacoraEditCorrespondencia->setIdInstitucionActual( $institucion ); //Set Institucion Actual
                                $bitacoraEditCorrespondencia->setIdEstado( $estado_edit ); //Set Institucion Actual
                                
                                //Realizar la Persistencia de los Datos y enviar a la BD
                                $em->persist($correspondenciaEdit);
                                $em->persist($bitacoraEditCorrespondencia);
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
                                "msg"   => "No puedes Editar esta Correspondencia; no eres el creador de la misma !!"
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
                "msg" => "Autorizacion de Token no valida, tu sesion ha expirado, cierra y vuelve a iniciar. !!"
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
     * @Route("/correspondencia/new-correspondencia-tipo", name="/correspondencia/new-correspondencia-tipo") 
     ***************************************************************************/
    public function newCorrespondenciaTipoAction(Request $request) 
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
                $observacion_correspondencia = ($params->observaciones != null) ? $params->observaciones : null ;
                
                $cod_referenciaSreci  = ($params->codReferenciaSreci != null) ? $params->codReferenciaSreci : null ;   
                
                $fecha_ingreso        = new \DateTime('now');
                
                $hora_ingreso = new \DateTime('now');            
                $hora_ingreso->format('H:i');
                
                $hora_actualizacion = new \DateTime('now');            
                $hora_actualizacion->format('H:i');
                
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
                
                // setTomail, copias de contactos a enviar
                $setTomail = ($params->setTomail != null) ? $params->setTomail : null ;
                
                // 2018-02-13
                $subDir_send = ($params->subDireccionesSreciAcom != null) ? $params->subDireccionesSreciAcom : null ;
                
                // 2018-02-19 | Comunicaciones Vinculantes al Tema
                $comVinculante_send = ($params->comunicacionesVinculantes != null) ? $params->comunicacionesVinculantes : null ;
                
                // Se convierte el Array en String
                $setTo_array_convert = explode(",", $setTomail);
                $setTo_array_convertIn = implode(",", $setTo_array_convert);                                             
                
                
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
                    $correspondenciaNew->setObservaciones($observacion_correspondencia); 
                    $correspondenciaNew->setFechaIngreso($fecha_ingreso);                    
                    $correspondenciaNew->setFechaModificacion($fecha_null);
                    $correspondenciaNew->setFechaFinalizacion($fecha_null);
                    
                    $correspondenciaNew->setHoraIngreso( $hora_ingreso ); // Hora de Creacion
                    
                    $correspondenciaNew->setFechaMaxEntrega($fecha_maxima_entrega_date);
                    
                    // Nuevo Campo de Codigo de Refrencia SRECI ----------------
                                        
                    //Preguntamos si Tipo Doc Es 1 | Oficio ********************
                    if( $cod_tipo_documento === "1" || $cod_tipo_documento === "2" || 
                         $cod_tipo_documento === "3"  || $cod_tipo_documento === "4"){
                        // Busqueda del Codigo de la Secuencia a Actualizar | Correspondencia Enc
                        $secuenciaSCPI = $em->getRepository("BackendBundle:TblSecuenciales")->findOneBy(                            
                            array(
                               //"codSecuencial"  => "SCPI",
                               "codSecuencial"  => $identity->inicialesDireccion,
                               "idTipoDocumento" => $cod_tipo_documento,
                               "idDeptoFuncional" => $identity->idDeptoFuncional                                
                            ));
                        
                        $cod_referenciaSCPI = $secuenciaSCPI->getCodSecuencial();
                        $valor2_secuenciaSCPI = $secuenciaSCPI->getValor2() + 1;
                        $secuenciaSCPI->setValor2($valor2_secuenciaSCPI);
                        $secuenciaSCPI->setReservada("N"); //Set de Reservada de Secuencia de Comunicacion
                        
                        $em->persist($secuenciaSCPI);
                        //Realizar la actualizacion en el storage de la BD
                        $em->flush();
                        
                        // Consulta a los Datos del Depto Funcional *************
                        $deptoFuncConsulta = $em->getRepository("BackendBundle:TblDepartamentosFuncionales")->findOneBy(
                            array(
                               "idDeptoFuncional" => $identity->idDeptoFuncional
                            ));
                        $inicialesDeptoFunc = $deptoFuncConsulta->getInicialesDeptoFuncional();
                        
                    //$cod_referenciaSreci = $cod_referenciaSCPI . "-". $inicialesDeptoFunc . "-". $valor2_secuenciaSCPI;
                        /* Incidencia: INC.00001 | Generacion de Secuencia SCPI | Automatica
                        * Fecha : 2017-11-15 | 05:25 pm
                        * Reportada : Nahum Martinez | Admon. SICDOC
                        * INI | NMA | INC.00001
                        * Cambiamos Cambiamos El Proceso de Generar la Secuencia en Back End
                         * y Hacerlo desde el Front End */
                        $cod_referenciaSreci = $act_secuencia_scpi;
                        // FIN | NMA | INC.00001
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
                    
                    // 2018-02-20
                    // Si la Comuniacion es Sin Seguimiento la Hora de Finalizacion es la Misma que la de la Ingreso
                    if( $estado->getIdEstado() == 5 ){
                        $correspondenciaNew->setHoraFinalizacion( $hora_ingreso ); // Hora de Finalizacion
                        $correspondenciaNew->setFechaFinalizacion( $fecha_ingreso ); // Fecha de Finalizacion
                    }
                    
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
                    
                    // Setea el Tipo de Comunicacion
                    //Instanciamos de la Clase TblTipoComunicacion
                    $tipo_comunicacion = $em->getRepository("BackendBundle:TblTipoComunicacion")->findOneBy(
                        array(
                           "idTipoComunicacion" => 2
                        ));                    
                    $correspondenciaNew->setIdTipoComunicacion($tipo_comunicacion); //Set de Tipo de Comunicacion
                    
                    
                    
                    /* 2018-02-13
                    * Campo de las Sub Direcciones Acompanantes
                    * MEJ-000001
                    */
                        // *****************************************************
                        if( $subDir_send != null ){
                            // Se convierte el Array en String
                            $subDir_array_convert   = json_encode($subDir_send);
                            $subDir_array_convert2  = json_decode($subDir_array_convert);

                            // Recorreros los Items del Array
                            $descDeptoFuncionalAcum = "";
                            
                            foreach ( $subDir_array_convert2 as $arr ){                                
                                $idDeptoFuncional  = $arr->id;
                                $descDeptoFuncional = $arr->itemName;
                                //$inicialesDeptoFuncional = $arr->name2;
                                if( $descDeptoFuncionalAcum != "" ){
                                    $descDeptoFuncionalAcum = $descDeptoFuncionalAcum . ', ' . $descDeptoFuncional;
                                }else {
                                    $descDeptoFuncionalAcum = $descDeptoFuncional;
                                }
                                // Asignamos las Sub Direcciones al Listado
                                $correspondenciaNew->setdireccionesAcompanantes( $descDeptoFuncionalAcum );
                            }
                        }
                        // FIN | MEJ-000001
                        
                        
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
                                $correspondenciaNew->setcomunicacionVinculante( $codigoComunicacionAcum );
                            }
                        }
                        // FIN | MEJ-000002
                    
                                                                             
                    //Finaliza Busqueda de Integridad entre Tablas *************                    
                    
                    //Verificacion del Codigo de la Correspondenia *************
                    $isset_corresp_cod = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findOneBy(
                        array(
                          "codCorrespondenciaEnc" => $cod_correspondencia . "-" . $new_secuencia
                        ));
                     
                    //Verificacion del Codigo de Referencia de la Correspondenia *******************
                    // Verifica el Tipo de Documento valido para las Repeticiones
                    if( $cod_tipo_documento == 1 || $cod_tipo_documento == 2 ||
                        $cod_tipo_documento == 3 || $cod_tipo_documento == 4 ){
                        $cod_tipo_documento_send = $cod_tipo_documento;
                    }else {
                        $cod_tipo_documento_send = 0;
                    }
                    
                    // Ejecutamos la Consulta
                    $isset_referencia_cod = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findOneBy(
                        array(
                          "codReferenciaSreci" => $cod_referenciaSreci,
                          "idTipoDocumento"    => $cod_tipo_documento_send,
                          "idTipoComunicacion" => $tipo_comunicacion
                          //"idTipoDocumento"    => [1,2,3,4]
                        ));
                    
                    
                    //Verificamos que el retorno de la Funcion sea = 0 ********* 
                    if(count($isset_corresp_cod) == 0 && count($isset_referencia_cod) == 0){
                        //Instanciamos de la Clase TblSecuenciales
                        //Seteo del nuevo secuencial de la tabla: TblSecuenciales
                        $secuenciaNew = new TblSecuenciales();
                        
                        /* Incidencia: INC.00002 | Actualizacion de la Secuencia Comprometida
                        * Fecha : 2018-02-07 | 11:00 am
                        * Reportada : Nahum Martinez | Admon. SICDOC
                        * INI | NMA | INC.00002
                        * Buscamos si el Usuario tiene una Secuencia pendiente y
                        * comprometida, luego Actualizamos el Estado (12) y la
                        * fecha que se actualizo 
                        *******************************************************/                         
                        // Query para Obtener todos las Sec. de la Tabla: TblSecuenciasComprometidas
                        $comprometidasSecuencias = $em->getRepository("BackendBundle:TblSecuenciasComprometidas")->findOneBy(
                            array(
                                //"codSecuencial"    => "SCPI", // Codigo de la Secuencia,
                                "codSecuencial"    => $identity->inicialesDireccion, // Codigo de la Secuencia,
                                "idTipoDocumento"  => $cod_tipo_documento, // Tipo de Documentos de la Secuencia,
                                "idDeptoFuncional" => $cod_depto_funcional, // Depto Funcional de la Secuencia,
                                "idUsuario"        => $cod_usuario, // Usuario de la Secuencia,
                                "idEstadoSecuencia" => 11 // Estado de la Secuencia,                            
                            ));
                        
                        $countComprometidas = count($comprometidasSecuencias);
                        if( $countComprometidas > 0 ){                            
                            // Query para Obtener el Estado de la Tabla: TblEstados
                            $estadoSecuencia = $em->getRepository("BackendBundle:TblEstados")->findOneBy(
                                array(
                                    "idEstado" => 12, // Id de Estado (Secuencia Grabada)
                                ));
                            
                            $comprometidasSecuencias->setIdEstadoSecuencia( $estadoSecuencia ); // Actualiza El Estado de la Secuencia Comprometida
                            $comprometidasSecuencias->setFechaActualizacion( $fecha_ingreso ); // Fecha de Actualizacion 
                            $comprometidasSecuencias->setHoraActualizacion( $hora_actualizacion ); // Hora de Creacion
                            //$comprometidasSecuencias->setIdCorrespondenciaEnc( $isset_corresp_cod->getIdCorrespondenciaEnc() ); // Id_Correspindencia_Enc                                                    
                            // Persistencia de los datos                            
                            $em->persist( $comprometidasSecuencias );
                        }                        
                        // FIN | INC.00002                        
                        // *****************************************************
                        
                        // Busqueda del Codigo de la Secuencia a Actualizar | Correspondencia Enc
                        $secuenciaNew = $em->getRepository("BackendBundle:TblSecuenciales")->findOneBy(                            
                            array(
                                "codSecuencial"  => $cod_correspondencia
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
                        
                        //$secuenciaNew->setValor2($new_secuencia); //Set de valor2 de Secuencia de Comunicacion
                        //$secuenciaNew->setReservada("N"); //Set de Reservada de Secuencia de Comunicacion
                        
                        
                        //Realizar la Persistencia de los Datos y enviar a la BD
                        $em->persist($correspondenciaNew);
                        
                        $em->persist($secuenciaNew);
                        
                        //$em->persist( $comprometidasSecuencias );
                        
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
                        
                        $correspondenciaDet->setDescCorrespondenciaDet("Creacion de Comunicacion: " . $cod_correspondencia_det . "-" . $new_secuencia_det); //Set de Descripcion Inicial
                        
                        // 2018-02-12
                        // Validacion del estado; asi sera la Descripcion de la Actividad
                        
                        //Instanciamos de la Clase TblEstados                        
                        $estadoDet = $em->getRepository("BackendBundle:TblEstados")->findOneBy(                            
                            array(
                                "idEstado" => $estado
                            ));                    
                        $correspondenciaDet->setIdEstado($estadoDet); //Set de Codigo de Estados
                        
                        // Estado de la Consulta
                        if( $estadoDet->getIdEstado() == 5 ){
                            $correspondenciaDet->setActividadRealizar("La Comunicación: " . $cod_correspondencia . "-" . $new_secuencia . " No tiene seguimiento, se ha Finalizado."  ); //Set de Actividad Inicial
                        }else {
                            $correspondenciaDet->setActividadRealizar("Pendiente de Crear respuesta a comunicación: " . $cod_correspondencia . "-" . $new_secuencia); //Set de Actividad Inicial
                        }
                                               
                        $correspondenciaDet->setInstrucciones($observacion_correspondencia); 
                       
                        //Verificacion del Codigo de la Correspondenia *********
                        $id_correspondencia_enc = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findOneBy(
                            array(
                                "codCorrespondenciaEnc" => $cod_correspondencia . "-" . $new_secuencia
                            ));
                        $correspondenciaDet->setIdCorrespondenciaEnc($id_correspondencia_enc); //Set de Fecha Id Correspondencia Enc
                                                
                        
                        //Instanciamos de la Clase TblUsuario
                        $usuarioDetalle = $em->getRepository("BackendBundle:TblUsuarios")->findOneBy(
                            array(
                               "idUsuario" => $cod_usuario                           
                            ));                    
                        $correspondenciaDet->setIdUsuario($usuarioDetalle); //Set de Codigo de Usuario
                        
                        //Instanciamos de la Clase TblFuncionarios
                        $usuario_asignado = $em->getRepository("BackendBundle:TblFuncionarios")->findOneBy(
                        array(
                           "idUsuario" => $id_usuario_asignado
                        ));                    
                        $correspondenciaDet->setIdFuncionarioAsignado($usuario_asignado); 
                        
                        
                        // Busqueda del Codigo de la Secuencia a Actualizar | Correspondencia Det
                        $secuenciaNew = $em->getRepository("BackendBundle:TblSecuenciales")->findOneBy(                            
                            array(
                                "codSecuencial"  => $cod_correspondencia_det
                            ));
                        
                        // Evalua que el valor2 de la Consulta no sea Mayor al Enviado
                        $secuenciaActDet = $secuenciaNew->getValor2();
                        if( $secuenciaActDet > $new_secuencia_det ){
                            $secuenciaNew->setValor2($secuenciaActDet); //Set de valor2 de Secuencia de Comunicacion                            
                            $secuenciaNew->setReservada("N"); //Set de Reservada de Secuencia de Comunicacion                        
                        } else if ( $secuenciaActDet < $new_secuencia_det ){
                            $secuenciaNew->setValor2( $new_secuencia_det ); //Set de valor2 de Secuencia de Comunicacion
                            $secuenciaNew->setReservada("N"); //Set de Reservada de Secuencia de Comunicacion
                        }
                        
                        //$secuenciaNew->setValor2($new_secuencia_det); //Set de valor2 de Secuencia de Comunicacion
                        //$secuenciaNew->setReservada("N"); //Set de Reservada de Secuencia de Comunicacion
                        
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
                            // Se convierte el Array en String
                            $documentos_array_convert      = json_encode($pdf_send);
                            $documentos_array_convert2      = json_decode($documentos_array_convert);

                            // Recorreros los Items del Array
                            foreach ( $documentos_array_convert2 as $arr ){                                
                                $nameDoc = $arr->nameDoc;
                                $extDoc = $arr->extDoc;
                                $pesoDoc = $arr->pesoDoc;
                                $nombreDoc = $arr->nombreDoc;
                                
                                // Cambiamos el Tipo de extencion jpg => jpeg
                                if( $extDoc == "jpg" || $extDoc == "JPG"  ){
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
                                
                                // Ingresa el Documento ************************
                                $documentosIn = new TblDocumentos();

                                //$documentosIn->setCodDocumento($cod_correspondencia . "-" . $new_secuencia); //Set de Codigo Documento
                                $documentosIn->setCodDocumento($nameDoc); //Set de Codigo Documento
                                $documentosIn->setFechaIngreso($fecha_ingreso); //Set Fecha Ingreso

                                //$documentosIn->setDescDocumento("Documento de Respaldo / Salida de Comunicaión"); //Set Documento Desc
                                $documentosIn->setDescDocumento($nombreDoc); //Set Documento Desc / 2018-02-28
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
                                $documentosIn->setIdCorrespondenciaDet($id_correspondencia_det_docu); //Set de Id Correspondencia Det
                                
                                // Verificacion del Codigo de la Correspondenia*
                                // Encabezado  *********************************
                                $id_correspondencia_enc_docu = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findOneBy(
                                    array(
                                        "codCorrespondenciaEnc" => $cod_correspondencia . "-" . $new_secuencia
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
                        // Fin de Comunicacion Detalle *************************
                        
                        // Envio de Correo despues de la Granacion de Datos
                        // *****************************************************
                        //Instanciamos de la Clase TblFuncionarios, para Obtener
                        // los Datos de envio de Mail **************************
                        $usuario_asignado_send = $em->getRepository("BackendBundle:TblFuncionarios")->findOneBy(
                            array(
                                //"idFuncionario" => $id_usuario_asignado                
                                "idUsuario" => $id_usuario_asignado                
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
                               //->setUsername( 'nahum.sreci@gmail.com')                               
                               //->setPassword('1897Juve');                               
                               ->setUsername( "correspondenciascpi@sreci.gob.hn" )
                               ->setPassword('Despachomcns')
                               ->setTimeout(180);     
                           //Creamos la instancia del envío
                           $mailer = \Swift_Mailer::newInstance($transport);
                           
                           //Creamos el mensaje
                           $mail = \Swift_Message::newInstance()
                               ->setSubject('Notificación de Ingreso de Comunicacion | SICDOC')                               
                               //->setFrom(array($mailSend => $identity->nombre . " " .  $identity->apellido ))
                               //->setFrom(array("nahum.sreci@gmail.com" => "Administrador SICDOC" ))
                               ->setFrom(array("correspondenciascpi@sreci.gob.hn" => "Administrador SICDOC" ))
                               ->setTo($mailSend)                               
                               ->setBody(
                                    $this->renderView(
                                    // app/Resources/views/Emails/registration.html.twig
                                        'Emails/sendMailComunicacion.html.twig',
                                        array( 'name' => $nombreSend, 'apellidoOficio' => $apellidoSend,
                                               'oficioExtNo' => $cod_referenciaSreci, 'oficioInNo' => $cod_correspondencia . "-" . $new_secuencia,
                                               'temaOficio' => $tema_correspondencia, 'descOficio' => $desc_correspondencia,
                                               'fechaIngresoOfi' => strval($fecha_maxima_entrega), 'fechaMax' => date_format($fecha_ingreso, "Y-m-d"),
                                                'obsComunicacion' => $observacion_correspondencia, 'institucionCom' => $institucion->getPerfilInstitucion())
                                    ), 'text/html' );  
                           
                            // Insercion de los Contactos en Copia
                            // Array | addCC
                            if ( $setTomail != null && $setTomail != ''  ) {
                              foreach ($setTo_array_convert as $address) {
                                $mail->addCc($address);
                              }
                            } //FIN Array | addCC
                           
                            // validamos que se adjunta pdf
                            if( $pdf_send != null ){
                              // Realizamos el foreach de los Documentos enviados
                              // Se convierte el Array en String
                              $documentos_array_convert      = json_encode($pdf_send);
                              $documentos_array_convert2      = json_decode($documentos_array_convert);
                            
                              foreach ( $documentos_array_convert2 as $attachMail  ) {
                                // varibles
                                $nameDoc = $arr->nameDoc;
                                $extDoc = $arr->extDoc;
                                $pesoDoc = $arr->pesoDoc;
                                
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
                                "msg"    => "Se ha ingresado la Comunicacion No. " . $cod_referenciaSreci .
                                            " pronto recibira una notificación vía correo. Gracias",
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
                "code" => "300",                
                "msg" => "Autorización no valida, la sesión ha sido vencida !!"                
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
        // Incidencia de Menos un Dia | Se suma 86400 que es un Dia en
        // TimeStamp
        //$fecha_time_stamp_In = $fecha_time_stamp + 86400;
        $fecha_time_stamp_In = $fecha_time_stamp + 86400;
        
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
    
    
    
    /* Funcion de Traslado Correspondencia****************************************
     * Parametros:                                                             * 
     * 1 ) Recibe un Objeto Request con el Metodo POST, el Json de la          *  
     *     Informacion.                                                        * 
     * 2 ) Recibe el Codigo del Documento por medio de la Url.                 * 
     * @Route("/correspondencia/traslado-correspondencia", name="/correspondencia/traslado-correspondencia")
     ***************************************************************************/
    public function trasladoCorrespondenciaAction(Request $request, $id = null) 
    {
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
            $identity = $helpers->authCheck($hash, true);
            
            //Convertimos los Parametros POSt a Json
            $json = $request->get("json", null);
            
            //Comprobamos que Json no es Null
            if ($json != null) {
                $params = json_decode($json);
                
                //Parametro de la Url
                $correspondenciaId       = ($params->codCorrespondencia != null) ? $params->codCorrespondencia : null ;
                      
                //Datos generales de la Tabla, que viene del Json
                $just_correspondencia     = ($params->justificacionTraslado != null) ? $params->justificacionTraslado : null ;                
                $fecha_modificacion       = new \DateTime('now');
                
                $hora_modificacion = new \DateTime('now');            
                $hora_modificacion->format('H:i');
                
                //Relaciones de la Tabla con Otras.
                
                // Envio por Json el Codigo de Usuario | Buscar en la Tabla: TblUsuarios
                $cod_usuario          = $identity->sub;
                $id_funcionario_traslado = ($params->idUsuarioAsaignado != null) ? $params->idUsuarioAsaignado : null ;
                
                // Envio por Json el Codigo de Estados | Buscar en la Tabla: TblEstados
                $cod_estado           = ($params->idEstado != null) ? $params->idEstado : null ;
                
                // Envio por Json el Codigo de Direccion Sreci | Buscar en la Tabla: TblDireccionesSreci
                $id_direccion_sreci  = ($params->idDireccionSreci != null) ? $params->idDireccionSreci : null ;                
                $id_depto_funcional      = ($params->idDeptoFuncional != null) ? $params->idDeptoFuncional : null ;                
                
                
                //Evaluamos que el Codigo de Usuario no sea Null y la Descripcion tambien
                if($correspondenciaId != null && $just_correspondencia != null){
                    //La condicion fue Exitosa
                    //Instancia del Doctrine
                    $em = $this->getDoctrine()->getManager();
                    
                    //Repositorio de la Tabla: TblCorrespondenciaEnc, se Busca si el Codigo
                    // enviado por Parametro Existe
                    $correspondenciaEdit = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findOneBy(
                        array(
                            "codCorrespondenciaEnc" => $correspondenciaId
                        ));
                    
                    // Departamento Funcional Actual 
                    //Instanciamos de la Clase TblDepartementosFuncionales
                    $direccion_actual = $em->getRepository("BackendBundle:TblDepartamentosFuncionales")->findOneBy(                            
                        array(
                           "idDeptoFuncional" => $correspondenciaEdit->getIdDeptoFuncional()
                        )); 
                    
                        
                        //Evaluo el Resultado del Query, con el Paramtro del Codigo del
                        //Documento, para verificar si existe en la BD
                        if(count($correspondenciaEdit) > 0){
                            //Asignamos el usuario de la Consulta Anterior
                            $idUsarioCorrespondencia = $correspondenciaEdit->getIdUsuario()->getIdUsuario();
                            
                            //Evaluamos que el Usuario de la Consulta del Token, sea el dueño
                            // de la Correspondencia
                            //Se comenta por falta de funcionalidad | 2018-04-23
                            //if (isset($identity->sub) && $identity->sub === $idUsarioCorrespondencia) {
                            if ( isset($identity->sub) ) {
                                //Seteamos los valores de los campos modificados                                
                                $correspondenciaEdit->setFechaModificacion( $fecha_modificacion );                                
                                
                                //Instanciamos de la Clase TblUsuarios
                                $usuario = $em->getRepository("BackendBundle:TblUsuarios")->findOneBy(
                                    array(
                                       "idUsuario" => $idUsarioCorrespondencia
                                    ));                    
                                                                
                                //Instanciamos de la Clase TblFuncionarios
                                $usuario_traslado = $em->getRepository("BackendBundle:TblFuncionarios")->findOneBy(
                                    array(
                                       "idFuncionario" => $id_funcionario_traslado
                                    ));                    
                                $correspondenciaEdit->setIdFuncionarioAsignado( $usuario_traslado ); //Set de Codigo de Usuario Asigando

                                //Instanciamos de la Clase TblEstados
                                //Condicionamos que el perfil sea Director
                                if( $usuario_traslado->getidTipoFuncionario()->getidTipoFuncionario() == 6 ){
                                    $estado_traslado = 7;
                                }else{
                                    $estado_traslado = 3;
                                }
                                
                                $estado_traslado = $em->getRepository("BackendBundle:TblEstados")->findOneBy(                            
                                    array(
                                       "idEstado" => $estado_traslado
                                    ));                    
                                $correspondenciaEdit->setIdEstado( $estado_traslado ); //Set de Codigo de Estados   

                                //Instanciamos de la Clase TblDireccionesSreci                        
                                $direccion_traslado = $em->getRepository("BackendBundle:TblDireccionesSreci")->findOneBy(                            
                                    array(
                                       "idDireccionSreci" => $id_direccion_sreci
                                    ));                    
                                $correspondenciaEdit->setIdDireccionSreci( $direccion_traslado ); //Set de Codigo de Dreicciones Sreci
                                //
                                //Instanciamos de la Clase TblDepartementosFuncionales
                                $depto_traslado = $em->getRepository("BackendBundle:TblDepartamentosFuncionales")->findOneBy(                            
                                    array(
                                       "idDeptoFuncional" => $id_depto_funcional
                                    ));                    
                                $correspondenciaEdit->setIdDeptoFuncional( $depto_traslado ); //Set de Codigo de DeptosFuncionales Sreci  
                                //Finaliza Busqueda de Integridad entre Tablas
                                
                                
                                // ************************************************************
                                //Seteo de Datos Generales de la tabla: TblTrasladoComunicacion
                                $correspondenciaTrasladoNew = new TblTrasladoComunicacion();
                                
                                $correspondenciaTrasladoNew->setIdCorrespondenciaEnc( $correspondenciaEdit ); //Set de Correspondencia Enc
                                $correspondenciaTrasladoNew->setIdUsuario( $usuario ); //Set de Usuario Creador
                                $correspondenciaTrasladoNew->setIdUsuarioAsignado( $usuario_traslado ); //Set de Funcionario Asignado
                                $correspondenciaTrasladoNew->setFechaTraslado( $fecha_modificacion ); //Set de Fecha de Creacion
                                $correspondenciaTrasladoNew->setHoraTraslado( $hora_modificacion ); //Set Hora de Creacion
                                $correspondenciaTrasladoNew->setJustificacionTraslado( $just_correspondencia ); //Set Justificacion de Traslado                                
                                
                                
                                // Envio de Correo despues de la Granacion de Datos
                                // *****************************************************
                                // los Datos de envio de Mail **************************
                                //Instanciamos de la Clase TblFuncionarios, para Obtener
                                $usuario_asignado_send = $em->getRepository("BackendBundle:TblFuncionarios")->findOneBy(
                                    array(
                                        "idFuncionario" => $id_funcionario_traslado
                                        //"idUsuario" => $id_usuario_asignado                
                                    ));
                                
                                //Instanciamos de la Clase TblInstituciones, para Obtener
                                $institucion = $em->getRepository("BackendBundle:TblInstituciones")->findOneBy(
                                    array(
                                        "idInstitucion" => $correspondenciaEdit->getIdInstitucion()
                                    ));
                                
                                // Parametros de Salida
                                $mailSend = $usuario_asignado_send->getEmailFuncionario() ; // Get de mail de Funcionario Asignado
                                $nombreSend = $usuario_asignado_send->getNombre1Funcionario() ; // Get de Nombre de Funcionario Asignado
                                $apellidoSend = $usuario_asignado_send->getApellido1Funcionario() ; // Get de Apellido de Funcionario Asignado

                                // Llamamo a la Funcion Interna para que nos convierta *
                                // La Fecha a Calendario Gregoriano ********************

                                $fecha_maxima_entrega_time_stamp = json_encode($correspondenciaEdit->getFechaIngreso()->getTimestamp(), true ); 
                                // Ejecucion de la Funcion *****************************
                                $fecha_maxima_entrega_convert = $this->convertirFechasTimeStampAction( $fecha_maxima_entrega_time_stamp );

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
                                       ->setUsername( "correspondenciascpi@sreci.gob.hn" )
                                       ->setPassword('Despachomcns')
                                       ->setTimeout(180);     
                                   //Creamos la instancia del envío
                                   $mailer = \Swift_Mailer::newInstance($transport);

                                   //Creamos el mensaje
                                   $mail = \Swift_Message::newInstance()
                                       ->setSubject('Notificación de Traslado de Comunicación | SICDOC')                                       
                                       ->setFrom(array("correspondenciascpi@sreci.gob.hn" => "Administrador SICDOC" ))
                                       ->setTo($mailSend)                               
                                       ->setBody(
                                            $this->renderView(                                            
                                                'Emails/sendMailComunicacionTraslado.html.twig',
                                                array( 'name' => $nombreSend, 'apellidoOficio' => $apellidoSend,
                                                       'oficioExtNo' => $correspondenciaEdit->getCodReferenciaSreci(), 
                                                       'oficioInNo' => $correspondenciaEdit->getCodCorrespondenciaEnc(),
                                                       'temaOficio' => $correspondenciaEdit->getTemaComunicacion(), 
                                                       'descOficio' => $correspondenciaEdit->getDescCorrespondenciaEnc(),
                                                       'fechaIngresoOfi' => strval( $fecha_maxima_entrega_convert ), 
                                                       'fechaMax' => date_format( $fecha_modificacion, "Y-m-d"),
                                                       'obsComunicacion' => $correspondenciaEdit->getObservaciones(), 
                                                       'obsTraslado' => $just_correspondencia, 
                                                       'institucionCom' => $institucion->getPerfilInstitucion(),
                                                       'deptoFunAct' => $direccion_actual->getDescDeptoFuncional(),
                                                       'deptoFunTraslado' => $depto_traslado->getDescDeptoFuncional() )
                                            ), 'text/html' );  
                                   
                                    // Envia el Correo con todos los Parametros
                                    $resuly = $mailer->send($mail);

                                // ***** Fin de Envio de Correo ************************
                                
                                
                                //Realizar la Persistencia de los Datos y enviar a la BD
                                $em->persist( $correspondenciaEdit );                                                                
                                $em->persist( $correspondenciaTrasladoNew );
                                
                                
                                // Opcion de Ejecucion
                                $optEjec = 0;
                                
                                //Variable de Actualizacion
                                $actDatos = $em->flush();
                                
                                // Actualizacion en la Base de Datos ***********
                                if( $actDatos == null ){
                                    //Array de Mensajes
                                    $optEjec = 1;
                                    $data = array(
                                        "status" => "success",
                                        "code"     => 200,
                                        "dataSend" => $correspondenciaEdit,
                                        "optEjec"  => $optEjec,
                                        "msg" => "La Comunicación ha sido trasladada, exitosamente !!"
                                    );
                                }else{
                                    //Array de Mensajes
                                    $data = array(
                                        "status" => "error",
                                        "code"     => 400,
                                        "optEjec"  => $optEjec,
                                        "msg" => "La Comunicación no ha sido actualizada, existe error en la informacion !!"
                                    );
                                }// Fin de Validacion de Actualizacion
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
                                "msg"   => "No existe una Comunicación con ese Codigo !!"
                            );                           
                        }
                    }else{
                        $data = array(
                            "status" => "error", 
                            "code"   => 400, 
                            "data"   => "Error al editar, ya existe una Comunicación con ese Codigo !!"
                        );                       
                    } //Finaliza el Bloque de la validadacion de la Data en la Tabla
            } else {
                    //Array de Mensajes
                    $data = array(
                       "status" => "error", 
                       "code"   => 400, 
                       "msg"   => "Comunicación no actualizada, parametros invalidos !!"
                    );
                }
            } else {
            $data = array(
                "status" => "error",                
                "code"   => 400,                
                "msg" => "Autorizacion de Token no valida, tu sesion ha expirado, cierra y vuelve a iniciar. !!"
            );
        }        
        //Retorno de la Funcion ************************************************
        return $helpers->parserJson($data);
    } //Fin de la Funcion Traslado de Correspondencia ****************
    
}

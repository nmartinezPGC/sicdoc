<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\JsonResponse;
//Importamos las Tablas a Relacionar
use BackendBundle\Entity\TblUsuarios;
use BackendBundle\Entity\TblDocumentos;
use BackendBundle\Entity\TblCorrespondenciaDet;

/*******************************************************************************
 * Description of DocumentosController                                         * 
 * Controlador para Subir Ficheros de los Documentos que se anexan a la        * 
 * Documentacion de la Comunicacion entrante.                                  * 
 * Llamado: Se debera ejecutar el Llamado a los servicios de esta Clase        * 
 * una ves que se Ingrese el Detalle de Actividad en Respuesta                 * 
 * @author Nahum Martinez <nahum.sreci@gmail.com>                              * 
 * @category Ficheros                                                          * 
 * @version 1.0                                                                * 
 *******************************************************************************/
class DocumentosController extends Controller{
    
    
    /* Funcion de Nuevo Documento **********************************************
     * Parametros:                                                             * 
     * 1 ) Recibe un Objeto Request con el Metodo POST, el Json de la          *  
     *     Informacion.                                                        * 
     ***************************************************************************/
    public function newAction(Request $request) {
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
                //Datos generales de la Tabla                
                $cod_documento      = ($params->cod_documento != null) ? $params->cod_documento : null ;
                $desc_documento     = ($params->desc_documento != null) ? $params->desc_documento : null ;
                $url_documento      = ($params->url_documento != null) ? $params->url_documento : null ;                
                $fecha_ingreso      = new \DateTime('now');
                $fecha_modificacion = new \DateTime('now');
                $image              = null;
                $status             = ($params->status != null) ? $params->status : null ;
                //Relaciones de la Tabla con Otras
                $cod_correspondencia_det  = ($params->cod_correspondencia_det != null) ? $params->cod_correspondencia_det : null ;
                //$cod_usuario          = ($params->cod_usuario != null) ? $params->cod_usuario : null ;
                $cod_usuario              = $identity->codUser;               
                
                //Evaluamos que el Codigo de Usuario no sea Null y la Descripcion tambien
                if($cod_documento != null && $desc_documento != null){
                    //La condicion fue Exitosa
                    //Instancia del Doctrine
                    $em = $this->getDoctrine()->getManager();
                    
                    //Seteo de Datos Generales de la tabla
                    $documentoNew = new TblDocumentos();
                    $documentoNew->setCodDocumento($cod_documento);
                    $documentoNew->setDescDocumento($desc_documento);
                    $documentoNew->setUrlDocumento($url_documento);
                    $documentoNew->setFechaIngreso($fecha_ingreso);
                    $documentoNew->setFechaModificacion($fecha_modificacion);
                    $documentoNew->setMiniImagen($image);
                    $documentoNew->setStatus($status);
                    
                    //variables de Otras Tablas
                    //Instanciamos de la Clase TblUsuario
                    $usuario = $em->getRepository("BackendBundle:TblUsuarios")->findOneBy(
                        array(
                           "codUsuario" => $identity->codUser
                           // "idUsuario" => $identity->sub
                        ));                    
                    $documentoNew->setIdUsuario($usuario); //Set de Codigo de Usuario
                    //Instanciamos de la Clase TblCorrespondenciaDet
                    //echo "Voy por Aka 1.3  " . $identity->codUser;      
                    $correspondencia = $em->getRepository("BackendBundle:TblCorrespondenciaDet")->findOneBy(                            
                        array(
                           "codCorrespondenciaDet" => $cod_correspondencia_det
                        ));
                    
                    $documentoNew->setIdCorrespondenciaDet($correspondencia); //Set de Codigo de Detalle de Correspondencia
                    
                    //Verificacion del Codigo del Documentos********************
                    $isset_doc_cod = $em->getRepository("BackendBundle:TblDocumentos")->findBy(
                        array(
                          "codDocumento" => $cod_documento
                        ));
                    //Verificamos que el retorno de la Funcion sea = 0 ********* 
                    if(count($isset_doc_cod) == 0){                    
                    //Realizar la Persistencia de los Datos y enviar a la BD
                    $em->persist($documentoNew);
                    $em->flush();
                    
                    //Consulta de ese Documento recien Ingresado
                    /*$documentoConsulta = $em->getRepository("BackendBundle:TblDocumentos")->findOneBy(
                            array(
                                //"codUsuario"        => $cod_usuario, 
                                "descDocumento"     => $desc_documento,
                                "status"            => $status,
                                "fechaIngreso"      => $fecha_ingreso,
                                "fechaModificacion" => $fecha_modificacion,
                                "codDocumento"      => $cod_documento 
                            ));*/
                    
                        //Array de Mensajes
                        $data = array(
                            "status" => "success", 
                            "code"   => 200, 
                            "data"   => $isset_doc_cod
                        );
                    }else{
                        $data = array(
                            "status" => "error", 
                            "code"   => 400, 
                            "data"   => "Error al registrar, ya existe un documento con ese Codigo !!"
                        );                       
                    }//Finaliza el Bloque de la validadacion de la Data en la Tabla
                    // TblDocumentos
                } else {
                    //Array de Mensajes
                    $data = array(
                       "status" => "error", 
                       "code"   => 400, 
                       "msg"   => "Documento no Creado !!"
                    );
                }                
            } else {
                    //Array de Mensajes
                    $data = array(
                       "status" => "error", 
                       "code"   => 400, 
                       "msg"   => "Documento no Creado, parametros invalidos !!"
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
    } //Fin de la Funcion New Documento ****************************************
    
    
    /* Funcion de Editar Documento *********************************************
     * Parametros:                                                             * 
     * 1 ) Recibe un Objeto Request con el Metodo POST, el Json de la          *  
     *     Informacion.                                                        * 
     * 2 ) Recibe el Codigo del Documento por medio de la Url.                 * 
     ***************************************************************************/
    public function editAction(Request $request, $id = null) {
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
                $documentoId_2      = $id;
                      
                //Datos generales de la Tabla, que viene del Json
                $desc_documento     = ($params->desc_documento != null) ? $params->desc_documento : null ;
                $url_documento      = ($params->url_documento != null) ? $params->url_documento : null ; 
                $fecha_modificacion = new \DateTime('now');
                $image              = null;
                $status             = ($params->status != null) ? $params->status : null ;
                
                //Relaciones de la Tabla con Otras                
                $cod_usuario        = $identity->sub;
                
                //Evaluamos que el Codigo de Usuario no sea Null y la Descripcion tambien
                if($cod_usuario != null && $desc_documento != null){
                    //La condicion fue Exitosa
                    //Instancia del Doctrine
                    $em = $this->getDoctrine()->getManager();
                    
                    //Repositorio de la Tabla: TblUsuarios
                    $documentoNew = $em->getRepository("BackendBundle:TblDocumentos")->findOneBy(
                        array(
                            "codDocumento" => $documentoId_2
                        ));                   
                        
                        //Evaluo el Resultado del Query, con el Paramtro del Codigo del
                        //Documento, para verificar si existe en la BD
                        if(count($documentoNew) > 0){
                            //Asignamos el usuario de la Consulta Anterior
                            $idUsarioDocumento = $documentoNew->getIdUsuario()->getIdUsuario();
                            
                            //Evaluamos que el Usuario de la Consulta del Token, sea el dueño
                            // del Documento
                            if (isset($identity->sub) && $identity->sub === $idUsarioDocumento) {
                                //$documentoNew->setCodDocumento($cod_documento);
                                $documentoNew->setDescDocumento($desc_documento);
                                $documentoNew->setUrlDocumento($url_documento);
                                $documentoNew->setFechaModificacion($fecha_modificacion);
                                $documentoNew->setMiniImagen($image);
                                $documentoNew->setStatus($status);

                                //Realizar la Persistencia de los Datos y enviar a la BD
                                $em->persist($documentoNew);
                                $em->flush();

                                //Array de Mensajes
                                $data = array(
                                    "status" => "success",
                                    "code" => 200,
                                    "msg" => "Documento actualizado, exitosamente !!"
                                );
                            } else {
                                //Array de Mensajes
                                $data = array(
                                    "status" => "error",
                                    "code" => 400,
                                    "msg" => "Documento no ha sido actualizado, no eres el creador del Documento !!"
                                );
                            }
                        }else{                            
                            //Array de Mensajes
                            $data = array(
                                "status" => "error", 
                                "code"   => 400,
                                "msg"   => "No existe un Documento con ese Codigo !!"
                            );                           
                        }
                    }else{
                        $data = array(
                            "status" => "error", 
                            "code"   => 400, 
                            "data"   => "Error al registrar, ya existe un documento con ese Codigo !!"
                        );                       
                    } //Finaliza el Bloque de la validadacion de la Data en la Tabla
            } else {
                    //Array de Mensajes
                    $data = array(
                       "status" => "error", 
                       "code"   => 400, 
                       "msg"   => "Documento no actualizado, parametros invalidos !!"
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
    } //Fin de la Funcion Editar Documento *************************************
    
    
    /* Funcion de Subir Documento **********************************************
     * Parametros:                                                             * 
     * 1 ) Recibe un Objeto Request con el Metodo POST, el Json de la          *  
     *     Informacion.                                                        * 
     * 2 ) Recibe el Codigo del Documento por medio de la Url.                 * 
     ***************************************************************************/
    public function uploadAction(Request $request, $id) {
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
            
            $documento_id = $id;
            $em = $this->getDoctrine()->getManager(); 
            
            $documentoEM = $em->getRepository("BackendBundle:TblDocumentos")->findOneby(
                array(
                    "codDocumento" => $documento_id
                ));                   
            
            //Evaluo el Resultado del Query, con el Paramtro del Codigo del
            //Documento, para verificar si existe en la BD
            if(count($documentoEM) > 0){
                //Asignamos el usuario de la Consulta Anterior
                $idUsarioDocumento = $documentoEM->getIdUsuario()->getIdUsuario();
                
                //Evaluamos que el Usuario de la Consulta del Token, sea el dueño
                // del Documento
                if(isset($identity->sub) && $identity->sub === $idUsarioDocumento) {
                    //Recogemos los Archivos
                    $file = $request->files->get('image', null);
                    $file_pdf = $request->files->get('pdf', null);

                    if ($file != null && !empty($file)) {
                        $ext = $file->guessExtension();

                        //Comprobamos la extencion sea Correcta
                        if ($ext == "jpg" || $ext == "PDF" ||  $ext == "png" || $ext == "jpeg" || $ext == "pdf" ||
                            $ext == "doc" || $ext == "docs" || $ext == "docx" || $ext == "txt") {
                            //Secuencia para Definir la Fecha y la Extencion del
                            //Archivo a Subir
                            //$file_name = time(). "." .$ext; 
                            /* INC00001 | 2018-01-04
                             * Corregir la Extencion del PDF a pdf
                             */
                            if( $ext == "PDF" ){
                                $ext = "pdf";
                            }
                            
                            if( $ext == "JPG" ){
                                $ext = "jpg";
                            }
                            
                            if( $ext == "JPEG" ){
                                $ext = "jpeg";
                            }
                            //FIN | INC00001
                            
                            $file_name = $documentoEM->getCodDocumento() . "-" . date('Y-m-d'). "." .$ext;                            
                            //$path_of_file = "uploads/correspondencia/correspondencia_".$documentoEM->getCodDocumento();
                            $path_of_file = "uploads/correspondencia/";
                            
                            $file->move($path_of_file, $file_name);
                            
                            //Steamos el valor de la Imagen a la BD
                            $documentoEM->setUrlDocumento($file_name);

                            $em->persist($documentoEM);
                            $em->flush();

                            //Array de Mensajes
                            $data = array(
                                "status" => "success",
                                "code" => 200,
                                "msg" => "Documento ha sido actualizado, la Imagen se ha cargado !!"
                            );
                        } else {
                            //Array de Mensajes
                            $data = array(
                                "status" => "error",
                                "code" => 400,
                                "msg" => "El formato de imagen no es correcto !!"
                            );
                        }
                    }
                } else {
                    //Array de Mensajes
                    $data = array(
                        "status" => "error",
                        "code" => 400,
                        "msg" => "Documento no ha sido actualizado, no eres el creador del Documento !!"
                    );
                }
            }else{                            
                //Array de Mensajes
                $data = array(
                    "status" => "error", 
                    "code"   => 400,
                    "msg"   => "No existe un Documento con ese Codigo !!"
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
    } //Fin de la Funcion Subir Arhcivo **********************************
    
    
    /* Funcion de Listar Documentos ********************************************
     * Parametros:                                                             *
     * @Route("/listar-documentos", name="listar-documentos")                  * 
     * 1 ) Recibe un Objeto Request con el Metodo POST, el Json de la          *  
     *     Informacion.                                                        * 
     * 2 ) Lista los docuemntos segun parametro ( codCorrespondenciaEnc )      *
     * 3 ) Ruta = documentos/listar-documentos                                 * 
     ***************************************************************************/
    public function listaDocumentosAction(Request $request)
    {   
        //Seteo de variables Globales        
        date_default_timezone_set('America/Tegucigalpa');
        
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");
        
        $em = $this->getDoctrine()->getManager();
        
        $json = $request->get("json", null);
        $params = json_decode($json);
        
        //Evaluamos el Json
        if ($json != null) {
            //Variables que vienen del Json ************************************
            //Recogemos el ID de Comunicacion Enc ******************************
            $cod_correspondencia = (isset($params->searchValueSend)) ? $params->searchValueSend : null; 
                        
            //Validacion de los Datos
            // Verificacion del Codigo de la Correspondenia*
            // Encabezado  *********************************
            $id_correspondencia_enc_docu = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findOneBy(
                array(
                    "codCorrespondenciaEnc" => $cod_correspondencia
                ));
            
            // Query para Obtener todos los Funcionarios de la Tabla: TblDocumentos
            
            $query = $em->createQuery('SELECT doc.idDocumento, doc.codDocumento, doc.descDocumento, doc.urlDocumento, '
                                    ."DATE_SUB(doc.fechaIngreso, 0, 'DAY') AS fechaIngreso, DATE_SUB(doc.fechaModificacion, 0, 'DAY') AS fechaModificacion, "
                                    . 'p.idUsuario, d.idCorrespondenciaDet, c.idCorrespondenciaEnc '
                                    . 'FROM BackendBundle:TblDocumentos doc '
                                    . 'INNER JOIN BackendBundle:TblUsuarios p WITH  p.idUsuario = doc.idUsuario '
                                    . 'INNER JOIN BackendBundle:TblCorrespondenciaDet d WITH d.idCorrespondenciaDet = doc.idCorrespondenciaDet '
                                    . 'INNER JOIN BackendBundle:TblCorrespondenciaEnc c WITH c.idCorrespondenciaEnc = doc.idCorrespondenciaEnc '
                                    . 'WHERE doc.idCorrespondenciaEnc = :idCorrespondenciaEnc '                                    
                                    . 'ORDER BY doc.codDocumento, doc.idDocumento ASC') 
                    ->setParameter('idCorrespondenciaEnc', $id_correspondencia_enc_docu->getIdCorrespondenciaEnc() ) ;
                    
            $lista_documentos = $query->getResult();
                       
            
            // Total de Registros de la Query
            $total_documentos = count( $lista_documentos );
            
            // Condicion de la Busqueda
            if ( $total_documentos >= 1 ) {
                $data = array(
                    "status" => "success",
                    "code"   => 200,
                    "recordsTotal"  => $total_documentos,
                    "data"   => $lista_documentos
                );
            }else {
                $data = array(
                    "status" => "error",
                    "code"   => 400,
                    "msg"    => "No existe Documentos asociados a la Coumunicacion, "
                                . "comuniquese con el Administrador !!"
                );
            }
        }else {
            $data = array(
                "status" => "error",
                "code"   => 400,
                "msg"    => "No existe Datos en la Tabla de Documentos, comuniquese con el Administrador !!"
            );
        }
               
        return $helpers->parserJson($data);
    }//FIN
 
    
    /* Funcion de Borrar Documento *********************************************
     * Parametros:                                                             *
     * @Route("/borrar-documento-server", name="borrar-documento-server")      * 
     * 1 ) Recibe un Objeto Request con el Metodo POST, el Json de la          *  
     *     Informacion.                                                        * 
     * 2 ) Lista los docuemntos segun parametro ( codDocumento )               *
     * 3 ) Ruta = /documentos/borrar-documento-server                          * 
     ***************************************************************************/
    public function borrarDocumentoServerAction(Request $request, $id = null)
    {
        date_default_timezone_set('America/Tegucigalpa');
        //Instanciamos el Servicio Helpers
        $helpers = $this->get("app.helpers");
        //Recoger el Hash
        //Recogemos el Hash y la Autrizacion del Mismo
        $hash = $request->get("authorization", null);
        //Se Chekea el Token
        $checkToken = $helpers->authCheck($hash);
        
        $json = $request->get("json", null);
        $params = json_decode($json);
        
        //Evalua que el Token sea True
        if($checkToken == true){
            $identity = $helpers->authCheck($hash, true);
            
            //Recogemos el ID de Comunicacion Enc ******************************
            $cod_correspondencia = (isset($params->codDocument)) ? $params->codDocument : null;
            $ext_documento = (isset($params->extDocument)) ? $params->extDocument : null;
            $indicador_borrado = (isset($params->indicadorExt)) ? $params->indicadorExt : null;
            
            $ruta = "uploads/correspondencia/";
            //$documento_id = $id;
            // Validamos que el Codigo no venga Null
            if( $cod_correspondencia != null || $cod_correspondencia != 0 ){
                // Evalua si el Indicador de Borrado es = 1; es con Extencion
                // Si es = 2 viene toda la Url
                if( $indicador_borrado == 1 ){
                    // Seteo del Patht y la Informacion del Documento
                    $path = pathinfo( $cod_correspondencia.".".$ext_documento );
                    $nombre_de_archivo_anterior = pathinfo( $cod_correspondencia.".".$ext_documento );

                    $path_of_file = $ruta.$cod_correspondencia.".".$ext_documento;                    
                }else if ( $indicador_borrado == 2 ){
                    // Seteo del Path del Documento
                    $path = pathinfo( $ext_documento );
                    $nombre_de_archivo_anterior = pathinfo( $ext_documento );

                    $path_of_file = $ruta.$ext_documento;                   
                }
                
                // Inicio de Opcion
                $opt = 0;
                // Verifiacion si el Documento Existe **************************               
                //if (file_exists($path_of_file)){
                if($path['filename'] == $nombre_de_archivo_anterior['filename']){
                    if ( @unlink($path_of_file) ) { 
                        $opt = 1;
                        $data = array(
                            "status" => "succes",                
                            "code" => "200",
                            "opt"  => $opt,
                            "msg" => "Documento Borrado Existosamente !!",
                            "data" => $path_of_file
                        );
                    } else {
                        $opt = 2;
                        $data = array(
                            "status" => "succes",                
                            "code" => "500",
                            "opt"  => $opt,
                            "path" => $path,
                            "nameFile" => $nombre_de_archivo_anterior,
                            "msg"  => "Documento no se Borrado, Intenta Otra ves !!",
                            "data" => $path_of_file
                        );   
                    }   
                } else {
                    $opt = 3;
                    $data = array(
                        "status" => "error",                
                        "code" => "500",   
                        "opt"  => $opt,
                        "msg" => "Documento No Exsite, vefica la extención o contacta al Administrador !!",
                        "data" => $path_of_file
                    );
                }            
            }else{
                $data = array(
                    "status" => "error",                
                    "code" => "300",                
                    "msg" => "Debes de Seleccionar el Documento para luego Borrar !!"                 
                );
            }              
        } else {
            $data = array(
                "status" => "error",                
                "code"   => "400",
                "token"  => $checkToken,
                "msg" => "Autorizacion de Token no valida, la sessión ha finalizado !!"                
            );
        }   
        //Retorno de la Funcion ************************************************
        return $helpers->parserJson($data);        
    }//FIN
    
    
    
    /* Funcion de Subir Documentos desde Ventana **************************************
     * Parametros:                                                                    *
     * @Route("/subir-documentos-comunicacion", name="subir-documentos-comunicacion") * 
     * 1 ) Recibe un Objeto Request con el Metodo POST, el Json de la                 *  
     *     Informacion.                                                               * 
     * 2 ) Lista los docuemntos segun parametro ( arrayDocumentos )                   *
     * 3 ) Ruta = /documentos/subir-documentos-comunicacion                           *  
     **********************************************************************************/
    public function subirDocumentosComunicacionAction(Request $request){
        date_default_timezone_set('America/Tegucigalpa');
        //Instanciamos el Servicio Helpers
        $helpers = $this->get("app.helpers");
        
        $em = $this->getDoctrine()->getManager();
        
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
                //Datos generales de la Tabla
                $cod_correspondencia =  ($params->codCorrespondencia != null) ? $params->codCorrespondencia : null ;                  
                
                $desc_correspondencia = ($params->descCorrespondencia != null) ? $params->descCorrespondencia : null ;                
                $tema_correspondencia = ($params->temaCorrespondencia != null) ? $params->temaCorrespondencia : null ;
                                
                $cod_referenciaSreci  = ($params->codReferenciaSreci != null) ? $params->codReferenciaSreci : null ;   
                
                $fecha_ingreso        = new \DateTime('now');                
                                
                // Fechas Nulas
                $fecha_null = new \DateTime('2999-12-31');
                
                //Relaciones de la Tabla con Otras.
                // Envio por Json el Codigo de Institucion | Buscar en la Tabla: TblInstituciones
                $cod_institucion      = ($params->idInstitucion != null) ? $params->idInstitucion : null ;
                
                // Envio por Json el Codigo de Usuario | Buscar en la Tabla: TblUsuarios
                $cod_usuario          = $identity->sub;                                               
                
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
                //$id_usuario_asignado = $identity->sub;
                
                // Justificacion de los Documentos
                $justifacion_documentos = ($params->justificacionNewDocs != null) ? $params->justificacionNewDocs : null ;
                
                
                // *******************************************************************************************************************************
                // Ingresamos los Datos a la Tabla TblEncabezadosDet **********
                //Seteo del nuevo secuencial de la tabla: TblCorrespondenciaDet
                // ************************************************************
                $correspondenciaDet = new TblCorrespondenciaDet();

                //Ingresamos un valor en la Tabla **********************
                //Correspondencia Enc **********************************                        
                $correspondenciaDet->setCodCorrespondenciaDet($cod_correspondencia_det . "-" . $new_secuencia_det); //Set de Codigo Correspondencia
                $correspondenciaDet->setFechaIngreso($fecha_ingreso); //Set de Fecha Ingreso
                $correspondenciaDet->setFechaSalida($fecha_ingreso); //Set de Fecha Salida

                $correspondenciaDet->setCodReferenciaSreci($cod_referenciaSreci); //Set de Codigo Ref SRECI

                //$correspondenciaDet->setDescCorrespondenciaDet("Anexos de Documentos a Comunicación: " . $cod_correspondencia_det . "-" . $new_secuencia_det ); //Set de Descripcion
                $correspondenciaDet->setDescCorrespondenciaDet( $justifacion_documentos ); //Set de Descripcion

                //Instanciamos de la Clase TblEstados                        
                $estadoDet = $em->getRepository("BackendBundle:TblEstados")->findOneBy(                            
                    array(
                        "idEstado" => 5 // Finalizado
                    ));                    
                $correspondenciaDet->setIdEstado($estadoDet); //Set de Codigo de Estados 
                
                $correspondenciaDet->setActividadRealizar("Anexos de Documentos a comunicación: " . $cod_correspondencia_det . "-" . $new_secuencia_det); //Set de Actividad
                
                $correspondenciaDet->setInstrucciones("Documentos anexados despues de su Ingreso");

                //Verificacion del Codigo de la Correspondenia *********
                $id_correspondencia_enc = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findOneBy(
                    array(
                        "codCorrespondenciaEnc" => $cod_correspondencia
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
                   "idFuncionario" => $id_usuario_asignado                
                ));                    
                $correspondenciaDet->setIdFuncionarioAsignado($usuario_asignado); 


                // Busqueda del Codigo de la Secuencia a Actualizar | Correspondencia Det
                $secuenciaNew = $em->getRepository("BackendBundle:TblSecuenciales")->findOneBy(                            
                    array(
                        "codSecuencial"  => $cod_correspondencia_det                        
                    ));

                    //var_dump($id_usuario_asignado);
                // Evalua que el valor2 de la Consulta no sea Mayor al Enviado
                $secuenciaActDet = $secuenciaNew->getValor2();
                if( $secuenciaActDet > $new_secuencia_det ){
                    $secuenciaNew->setValor2($secuenciaActDet); //Set de valor2 de Secuencia de Comunicacion                            
                    $secuenciaNew->setReservada("N"); //Set de Reservada de Secuencia de Comunicacion                        
                } else if ( $secuenciaActDet < $new_secuencia_det ){
                    $secuenciaNew->setValor2( $new_secuencia_det ); //Set de valor2 de Secuencia de Comunicacion
                    $secuenciaNew->setReservada("N"); //Set de Reservada de Secuencia de Comunicacion
                }
                
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
                            $nameDoc   = $arr->nameDoc;
                            $extDoc    = $arr->extDoc;
                            $pesoDoc   = $arr->pesoDoc;
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

                            // Instancia del Doctrine de la Tabla Documentos
                            $documentosIn = new TblDocumentos();

                            // Datos a Incluir de la Tabla
                            $documentosIn->setCodDocumento($nameDoc); //Set de Codigo Documento
                            $documentosIn->setFechaIngreso($fecha_ingreso); //Set Fecha Ingreso                           
                            
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
                                    "codCorrespondenciaEnc" => $cod_correspondencia
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
                    // Fin de Grabacion de Documentos **************************
                    
                    //Array de Mensajes
                    $data = array(
                        "status" => "success", 
                        "code"   => 200, 
                        "msg"    => "Se han ingresado el/los Documentos Exitosamente ",
                        //"data"   => $correspondenciaConsulta
                    );
            }else {
                //Array de Mensajes
                $data = array(
                   "status" => "error", 
                   "code"   => 400, 
                   "msg"   => "Documento no Creado, parametros invalidos !!"
                );
            }
        }else {
            $data = array(
                "status" => "error",                
                "code" => "400",                
                "msg" => "Autorizacion de Token no valida, tu sessión ha caducado !!"                
            );
        }
        //Retorno de la Funcion ************************************************
        return $helpers->parserJson($data);
    } // FIN
    
    
    
    /* Funcion de Subir Documentos desde Ventana **************************************
     * Parametros:                                                                    *
     * @Route("/borrar-documentos-comunicacion", name="borrar-documentos-comunicacion") * 
     * 1 ) Recibe un Objeto Request con el Metodo POST, el Json de la                 *  
     *     Informacion.                                                               * 
     * 2 ) Lista los docuemntos segun parametro ( codDocument )                       *
     * 3 ) Ruta = /documentos/borrar-documentos-comunicacion                          *  
     **********************************************************************************/
    public function borrarDocumentosComunicacionAction(Request $request){
        date_default_timezone_set('America/Tegucigalpa');
        //Instanciamos el Servicio Helpers
        $helpers = $this->get("app.helpers");
        
        $em = $this->getDoctrine()->getManager();
        
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
                //Datos generales de la Tabla
                $cod_documento_del =  ($params->codDocument != null) ? $params->codDocument : null ;                                                                  
                $id_correspondencia_det =  ($params->idCorrespondenciaDet != null) ? $params->idCorrespondenciaDet : null ;                                                                  
                                
                // INI de Grabacion de Documentos ******************************
                // Buscamos el Documento con el Parametro enviado
                //Instanciamos de la Clase TblDocumentos  **********************
                $documento_borrarBD = $em->getRepository("BackendBundle:TblDocumentos")->findOneBy(
                    array(
                       "codDocumento" => $cod_documento_del           
                    ));                    
                $em->remove( $documento_borrarBD ); //Borramos el Documento
                
                $flush = $em->flush();
                
                if ($flush == null) {
                    //Buscamos si tiene mas Detalle la Tabla de Documentos
                    $detalle_comunicacion = $em->getRepository("BackendBundle:TblDocumentos")->findOneBy(
                    array(
                       "idCorrespondenciaDet" => $id_correspondencia_det
                    ));   
                    
                    //Contamos Cuantas Acciones de Documentos tiene ese Detalle
                    if( count($detalle_comunicacion) == 0 ){
                        //Buscamos si tiene mas Detalle la Tabla de Documentos
                        // INI Borrar Detalle Correspondencia
                        $act_detalle_comunicacion = $em->getRepository("BackendBundle:TblCorrespondenciaDet")->findOneBy(
                        array(
                           "idCorrespondenciaDet" => $id_correspondencia_det
                        ));
                        
                        $em->remove( $act_detalle_comunicacion );
                        
                        $flush = $em->flush();
                    }
                    //Bitacora de 
                    // Fin de Borrar Detalle Correspondencia
                    
                    //Array de Mensajes
                    $data = array(
                        "status" => "success", 
                        "code"   => 200, 
                        "msg"    => "Se ha Borrado el Documento Exitosamente ",
                        //"data"   => $correspondenciaConsulta
                    );
                } else {
                    //Array de Mensajes
                    $data = array(
                        "status" => "error", 
                        "code"   => 400, 
                        "msg"    => "No se ha Borrado el Documento, intentalo de nuevo ... ",
                        //"data"   => $correspondenciaConsulta
                    );
                }                 
                // FIN de Grabacion de Documentos ******************************
            }else {
                //Array de Mensajes
                $data = array(
                   "status" => "error", 
                   "code"   => 400, 
                   "msg"   => "Documento no Creado, parametros invalidos !!"
                );
            }
        }else {
            $data = array(
                "status" => "error",                
                "code" => "400",                
                "msg" => "Autorizacion de Token no valida, tu sessión ha caducado !!"                
            );
        }
        //Retorno de la Funcion ************************************************
        return $helpers->parserJson($data);       
    }// FIN
    
}

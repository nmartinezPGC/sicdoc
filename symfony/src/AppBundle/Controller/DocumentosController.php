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
                    $documentoConsulta = $em->getRepository("BackendBundle:TblDocumentos")->findOneBy(
                            array(
                                //"codUsuario"        => $cod_usuario, 
                                "descDocumento"     => $desc_documento,
                                "status"            => $status,
                                "fechaIngreso"      => $fecha_ingreso,
                                "fechaModificacion" => $fecha_modificacion,
                                "codDocumento"      => $cod_documento 
                            ));
                    
                        //Array de Mensajes
                        $data = array(
                            "status" => "success", 
                            "code"   => 200, 
                            "data"   => $documentoConsulta
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
                        if ($ext == "jpg" || $ext == "png" || $ext == "jpeg" || $ext == "pdf" ||
                            $ext == "doc" || $ext == "docs" || $ext == "docx" || $ext == "txt") {
                            //Secuencia para Definir la Fecha y la Extencion del
                            //Archivo a Subir
                            //$file_name = time(). "." .$ext;                            
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
    
}

<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\JsonResponse;
//Importamos las Tablas a Relacionar
use BackendBundle\Entity\TblInstituciones;
use BackendBundle\Entity\TblFuncionarios;
use BackendBundle\Entity\TblContactos;
use Swift_MessageAcceptanceTest;

/**
 * Description of ContactosController
 * Objetivo: Regiestro de COntactos
 * @author Nahum Martinez
 */
class ContactosController extends Controller{    
    
    /**
     * @Route("contactos/contactos-consulta", name="contactos/contactos-consulta")
     * Creacion del Controlador: Consulta de Contactos SRECI
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00001
     * Descripcion: Consulta que muestra la informacion general de los Contactos
     *              atraves de Parametros de Search
     * @param array $request Filtros de Contactos a Buscar
     */
    public function consultaGeneralContactoListAction(Request $request )
    {
        date_default_timezone_set('America/Tegucigalpa');
        //Instanciamos el Servicio Helpers y Jwt  ******************************
        $helpers = $this->get("app.helpers");
        
        // Declaramos el Entity Manager  ***************************************
        $em = $this->getDoctrine()->getManager();
        
        // Query para Obtener todos los Contactos de la Tabla: TblContactos ****
        $contactos = $em->getRepository("BackendBundle:TblContactos")->findBy(
            array(
                "habilitado" => true
            ));
        
        // Condicion de la Busqueda
        if (count($contactos) >= 1 ) {
            $data = array(
                "status" => "success",
                "code"   => 200,
                "msg"    => "Se han encontrados los datos de Contactos",
                "data"   => $contactos
            );
        }else {
            $data = array(
                "status" => "error",
                "code"   => 400,
                "msg"    => "No existe Datos en la Tabla de Contactos !!"
            );
        }        
        return $helpers->parserJson($data);
    }//FIN | FND00001
    
    
    /**
     * @Route("contactos/contactos-new", name="contactos/contactos-new")
     * Creacion del Controlador: Consulta de Contactos SRECI
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00002
     * Descripcion: Agregar Nuevo Contacto
     * @param array $request Campos a Ingresar
     */
    public function contactoNewAction(Request $request )
    {
        date_default_timezone_set('America/Tegucigalpa');
        //Instanciamos el Servicio Helpers y Jwt  ******************************
        $helpers = $this->get("app.helpers");
        
        $json = $request->get("json", null);
        $params = json_decode($json);
        
       //echo "Antes de Falla  ******************   " . $json;
        // INI | Valid Json
        if ($json != null) {
        // Declaramos el Entity Manager  *******************************
        $em = $this->getDoctrine()->getManager();

        //Variables que vienen del Json ************************************
        //Seccion de Identificacion ****************************************
        //El ID no se incluye en el Ingreso; ya que es un campo Serial                   
            $cod_contacto     = (isset($params->codContacto)) ? $params->codContacto : null;
            
            // Datos Generales
            $nombre1_contacto = (isset($params->nombre1Contacto) && ctype_alpha($params->nombre1Contacto) ) ? $params->nombre1Contacto : null;
            $nombre2_contacto = (isset($params->nombre2Contacto) && ctype_alpha($params->nombre2Contacto) ) ? $params->nombre2Contacto : null;
            $apellido1_contacto = (isset($params->apellido1Contacto) && ctype_alpha($params->apellido1Contacto) ) ? $params->apellido1Contacto : null;
            $apellido2_contacto = (isset($params->apellido2Contacto) && ctype_alpha($params->apellido2Contacto) ) ? $params->apellido2Contacto : null;
            
            // Datos de Contacto
            $email_1          = (isset($params->email1Contacto)) ? $params->email1Contacto  : null;            
            $email_2          = (isset($params->email2Contacto)) ? $params->email2Contacto  : null;            
            $telefono_1       = (isset($params->telefono1Contacto)) ? $params->telefono1Contacto  : 0;            
            $telefono_2       = (isset($params->telefono2Contacto)) ? $params->telefono2Contacto  : 0;            
            $celular_1        = (isset($params->celular1Contacto)) ? $params->celular1Contacto  : 0;            
            $celular_2        = (isset($params->celular2Contacto)) ? $params->celular2Contacto  : 0;            
                        
            // Relaciones de Tablas
            $id_institucion   = (isset($params->idInstitucion)) ? $params->idInstitucion  : 0;
            $id_funcionario   = (isset($params->idContactoSreci)) ? $params->idContactoSreci  : 0;
            
            //Documentos enviados
            $pdf_documento   = (isset($params->pdfDocumento)) ? $params->pdfDocumento  : null;
            $img_documento   = (isset($params->imgDocumento)) ? $params->imgDocumento  : null;
            
            $cargo_funcional   = (isset($params->cargoFuncional)) ? $params->cargoFuncional  : null;
            
            $tipo_contacto   = (isset($params->tipoContacto)) ? $params->tipoContacto  : null;
            
            $trato_contacto   = (isset($params->tratoContacto)) ? $params->tratoContacto  : null;
        
            //Verificacion del Codigo y Email en la Tabla: TblContactos ********                
            $isset_contact_mail = $em->getRepository("BackendBundle:TblContactos")
                ->findOneBy(
                    array(
                      "email1Contacto" => $email_1
                    ));

            //Verificacion del Nombre y Apellido *******************************
            $isset_contact_cod = $em->getRepository("BackendBundle:TblContactos")
                ->findOneBy(
                    array(
                      "nombre1Contacto"   => $nombre1_contacto,
                      "apellido1Contacto" => $apellido1_contacto 
                    ));
            
            if( count($isset_contact_mail) == 0 && count($isset_contact_cod) == 0 ){
                
                // Query para Obtener todos los Contactos de la Tabla: TblContactos ****
                //$contactos = $em->getRepository("BackendBundle:TblContactos")->findAll();

                // Validar que los Campos no vengan vacios
                if ( $nombre1_contacto != null && $apellido1_contacto != null  && 
                     $id_funcionario != 0 && $id_institucion != 0 ){
                    //Instanciamos la Entidad TblContactos *********************
                    $contactoNew = new TblContactos();
                    //Seteamos los valores de Identificacion *******************
                    $contactoNew->setCodContacto($cod_contacto);                
                    $contactoNew->setNombre1Contacto($nombre1_contacto);
                    $contactoNew->setNombre2Contacto($nombre2_contacto);
                    $contactoNew->setApellido1Contacto($apellido1_contacto);
                    $contactoNew->setApellido2Contacto($apellido2_contacto);
                    
                    // Seteamos los valores de contacto
                    $contactoNew->setEmail1Contacto($email_1);
                    $contactoNew->setEmail2Contacto($email_2);
                    $contactoNew->setCelular1Contacto($celular_1);
                    $contactoNew->setCelular2Contacto($celular_2);
                    $contactoNew->setTelefono1Contacto($telefono_1);
                    $contactoNew->setTelefono2Contacto($telefono_2);
                    
                    $contactoNew->setCargoFuncional($cargo_funcional);
                    $contactoNew->setTipoContacto($tipo_contacto);
                    $contactoNew->setTrato($trato_contacto);

                    //Seteamos los valores de Relaciones de Tablas *************
                    //Instancia a la Tabla: TblInstituciones *******************                
                    $institucion = $em->getRepository("BackendBundle:TblInstituciones")
                        ->findOneBy(
                            array(
                                "idInstitucion" => $id_institucion
                            )) ;
                    $contactoNew->setIdInstitucion($institucion);

                    //Instancia a la Tabla: TblFuncionarios *********************                
                    $funcionarios = $em->getRepository("BackendBundle:TblFuncionarios")
                        ->findOneBy(
                            array(
                                "idFuncionario" => $id_funcionario
                            )) ;
                    $contactoNew->setIdFuncionario($funcionarios);
                                        
                    
                    // INI | Ingreso de Documento ******************************
                    //Recoger el Fichero que viene por el POST y lo guardamos el HD
                    $file_document  = $request->files->get("documento");
                                 
                    //Se verifica que el fichero no venga Null
                    if (!empty($file_document) && $file_document != null) {
                        //Obtenemos la extencion del Fichero
                        $ext = $file_document->guessExtension();
                        //Comprobamos que la Extencion sea Aceptada
                        if ($ext == "pdf" || $ext == "doc" || $ext == "docs" ) {                   
                            // Concatenmos al Nombre del Fichero la Fecha y la Extencion
                            //$file_name = time().".".$ext;
                            $file_name = $nombre1_contacto . "-". $apellido1_contacto . "-" . date('Y-m-d'). "." .$ext; 
                            //Movemos el Fichero
                            $path_of_file = "uploads/contactos/perfiles/perfiles_".date('Y-m-d');
                            $file_document->move($path_of_file, $file_name);                            
                            
                            // Setamos los Valores de Perfil de Contacto
                            $contactoNew->setPerfilContacto( $file_name );
                                                                                    
                        } else {
                            // Devolvemos el Mensaje de Array, cuando la Imagen no sea valida
                            $data = array(
                                "status" => "error",
                                "code" => 400,
                                "msg" => "El formato del archivo no es valido !!"
                            );
                        }
                    }else{
                        // Setamos los Valores de Perfil de Contacto
                        $contactoNew->setPerfilContacto($pdf_documento);                          
                    } // FIN | Ingreso de Documento ****************************
                    
                    
                    // INI | Ingreso de Imagen *********************************
                    //Recoger el Fichero que viene por el POST y lo guardamos el HD
                    $file_imagen   = $request->files->get("image");
                                 
                    //Se verifica que el fichero no venga Null
                    if (!empty($file_imagen) && $file_imagen != null) {
                        //Obtenemos la extencion del Fichero
                        $ext = $file_imagen->guessExtension();
                        //Comprobamos que la Extencion sea Aceptada
                        if ($ext == "png" || $ext == "jpg" || $ext == "jpeg" ) {                   
                            // Concatenmos al Nombre del Fichero la Fecha y la Extencion
                            //$file_name = time().".".$ext;
                            $file_name = $nombre1_contacto . "-". $apellido1_contacto . "-" . date('Y-m-d'). "." .$ext; 
                            //Movemos el Fichero
                            $path_of_file = "uploads/contactos/imagen/imagen_".date('Y-m-d');
                            $file_imagen->move($path_of_file, $file_name);                            
                            
                            // Setamos los Valores de Perfil de Contacto
                            $contactoNew->setFotoContacto( $file_name );
                                                                                    
                        } else {
                            // Devolvemos el Mensaje de Array, cuando la Imagen no sea valida
                            $data = array(
                                "status" => "error",
                                "code" => 400,
                                "msg" => "El formato del archivo no es valido !!"
                            );
                        }
                    }else{
                        // Setamos los Valores de Perfil de Contacto
                        $contactoNew->setFotoContacto($img_documento);                          
                    } // FIN | Ingreso de Documento ****************************
                   
                   
                    // *********************************************************
                    // Realizamos la Persistencia de los Datos
                    $em->persist($contactoNew);
                    
                    // Realizamos la Actualizacion a la BD
                    $em->flush();
                    
                    //Seteamos el array de Mensajes a enviar *******************
                    $data = array(
                        "status" => "success",                
                        "code" => "200",                
                        "msg" => "El Contacto, " . " " . $nombre1_contacto . " " . $apellido1_contacto  .
                                 " se ha creado satisfactoriamente."                 
                    );
                } else {
                    $data = array(
                        "status" => "error",                
                        "code"   => "400",                
                        "msg"    => "Falta Informacion por Ingresar !!"             
                    );
                } // FIN | Validacion de Datos Pendientes
            } else {
                $data = array(
                        "status" => "error",                
                        "code"   => "400",                
                        "msg"    => "Ya Existe un Contacto Ingresado con esta informacion !!"                
                    );
            } // FIN | Validacion de Existencia de Contacto
        }else {
             $data = array(
                    "status" => "error",                
                    "code"   => 400,                
                    "msg"    => "Error en los Parametros enviados, comuniquese con el Admiistrador !!"                
                );            
        }// FIN | Valid Json
         
        return $helpers->parserJson($data);
    }//FIN | FND00002
    
    
    
    /**
     * @Route("contactos/contacto-upload-perfil", name="contactos/contacto-upload-perfil")
     * Creacion del Controlador: Comunes Perfil PDF
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00003
     */
    public function uploadDocumentoAction(Request $request) {
        date_default_timezone_set('America/Tegucigalpa');
        //Instanciamos el Servicio Helpers
        $helpers = $this->get("app.helpers");

        $json = $request->get("json", null);
        $params = json_decode($json);
        
        $cod_contacto  = (isset($params->codigoSec)) ? $params->codigoSec : null;
                
        // Nombre del Documento
        $file_nameIn = $request->get("name_pdf");
                
        //Evaluamos la Autoriuzacion del Token
        
        //$em = $this->getDoctrine()->getManager();
        //Recoger el Fichero que viene por el POST y lo guardamos el HD
        $file      = $request->files->get("name_pdf");
        
        //Recoger el Fichero que viene por el POST y lo guardamos el HD ********
            //Se verifica que el fichero no venga Null
            if (!empty($file) && $file != null) {
                //Obtenemos la extencion del Fichero
                $ext = $file->guessExtension();
                //Comprobamos que la Extencion sea Aceptada
                if ($ext == "pdf" || $ext == "doc" || $ext == "docs" || $ext == "docx" ||
                    $ext == "png" || $ext == "jpg" || $ext == "jpeg" ) {                   
                    // Concatenmos al Nombre del Fichero la Fecha y la Extencion
                    //$file_name = time().".".$ext;
                    if( $ext == "PDF" ){
                        $ext = "pdf";
                    }
                    
                    if( $ext == "PNG" ){
                        $ext = "png";
                    }
                    
                    if( $ext == "jpg" || $ext == "JPG" ){
                        $ext = "jpeg";
                    }
                    
                    $file_name = $file_nameIn . "-" . date('Y-m-d'). "." .$ext; 
                    //Movemos el Fichero
                    $path_of_file = "uploads/contactos/perfiles/";
                    $file->move($path_of_file, $file_name);                    
                
                    // Devolvemos el Mensaje de Array
                    $data = array(
                        "status" => "success",
                        "code" => 200,
                        "msg" => "Document for user uploaded success !!",
                        "data" => $file_name
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
                    "msg" => "Document not upload !!" . $file_nameIn
                );                
            }            
        
        //Retorno de la Funcion ************************************************
        return $helpers->parserJson($data);
    } // FIN FND00003
    
    
}

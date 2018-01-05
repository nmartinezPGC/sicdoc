<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\JsonResponse;
//Importamos las Tablas a Relacionar
use BackendBundle\Entity\TblUsuarios;
use BackendBundle\Entity\TblEstados;
use BackendBundle\Entity\TblTipoUsuario;
use BackendBundle\Entity\TblTiposFuncionarios;
use BackendBundle\Entity\TblDepartamentosFuncionales;


/**
 * Description of ComunesController
 *
 * @author Sammy Guergachi <sguergachi at gmail.com>
 */
class ComunesController extends Controller {
    //put your code here
    
    /**
     * @Route("/upload-documento", name="upload-documento")
     * Creacion del Controlador: Comunes PDF
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00001
     */
    public function uploadDocumentoAction(Request $request) {
        //Instanciamos el Servicio Helpers
        date_default_timezone_set('America/Tegucigalpa');
        //date_default_timezone_set('Australia/Sydney');
        $helpers = $this->get("app.helpers");        
        //Recoger el Hash
        //Recogemos el Hash y la Autrizacion del Mismo
        $hash = $request->get("authorization", null);
        
        // Nombre del Documento
        $file_nameIn = $request->get("name_pdf");
        
        //Se Chekea el Token
        $checkToken = $helpers->authCheck($hash);
        //Evaluamos la Autoriuzacion del Token
        if($checkToken == true){
        //Ejecutamos todo el Codigo restante
        $identity = $helpers->authCheck($hash, true);    
        $em = $this->getDoctrine()->getManager();
        //Buscamos el registro por el Id de Usaurio
        $usuario = $em->getRepository("BackendBundle:TblUsuarios")->findOneBy(
            array(
               "idUsuario" => $identity->sub
            ));
            //Recoger el Fichero que viene por el POST y lo guardamos el HD
            $file      = $request->files->get("image");            
            
            //Se verifica que el fichero no venga Null
            if (!empty($file) && $file != null) {
                //Obtenemos la extencion del Fichero
                $ext = $file->guessExtension();
                //Comprobamos que la Extencion sea Aceptada
                if ($ext == "pdf" || $ext == "PDF" ||  $ext == "doc" || $ext == "docs" || $ext == "docx" ||
                    $ext == "xlsx" || $ext == "xls" || $ext == "ppt" || $ext == "pptx" ||
                    $ext == "png" || $ext == "jpeg" || $ext == "jpg") {                   
                    // Concatenmos al Nombre del Fichero la Fecha y la Extencion
                    //$file_name = time().".".$ext;
                    /* INC00001 | 2018-01-04
                    * Corregir la Extencion del PDF a pdf
                    */
                    if( $ext == "PDF" ){
                        $ext = "pdf";
                    }
                    //FIN | INC00001
                    
                    $file_name = $file_nameIn . "-" . date('Y-m-d'). "." .$ext; 
                    //$file_name = $file_nameIn . "." .$ext; 
                    //Movemos el Fichero
                    //$path_of_file = "uploads/correspondencia/correspondencia_".date('Y-m-d');
                    $path_of_file = "uploads/correspondencia/";
                    $file->move($path_of_file, $file_name);
                
                    // Devolvemos el Mensaje de Array
                    $data = array(
                        "status" => "success",
                        "code" => 200,
                        "msg" => "Imagen for user uploaded success !!"
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
                    "msg" => "Imagen not upload !!"
                );                
            }            
        }else{
            $data = array(
               "status" => "error",
               "code" => 400,
               "msg" => "Autorización no valida !!"
            );            
        }
        //Retorno de la Funcion ************************************************
        return $helpers->parserJson($data);
    } // FIN FND00001
    
    
    /**
     * @Route("/gen-secuencia-comunicacion-in", name="gen-secuencia-comunicacion-in")
     * Creacion del Controlador: Secuenciales
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00002
     */
    public function genSecuenciaComInAction(Request $request)
    {
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");        
        
        //Recoger el Hash
        //Recogemos el Hash y la Autrizacion del Mismo
        $hash = $request->get("authorization", null);
        //Se Chekea el Token
        $checkToken = $helpers->authCheck($hash);
        //Evaluamos la Autoriuzacion del Token
        if($checkToken == true){
            $em = $this->getDoctrine()->getManager();

            $json = $request->get("json", null);
            $params = json_decode($json);

            //Array de Mensajes
            $data = $data = array(
                    "status" => "error",                
                    "code" => "400",                
                    "msg" => "No se ha podido obtener los Datos, los parametros son erroneos !!"                
            ); 
        
        //Evaluamos el Json
        if ($json != null) {
            //Variables que vienen del Json ************************************
            // Recogemos el Pais y el Tipo de Institucion **********************
            $codigo_secuencia = (isset($params->codSecuencial)) ? $params->codSecuencial : null;
            $tabla_secuencia  = (isset($params->tablaSecuencia)) ? $params->tablaSecuencia : null;
            $tipo_documento   = (isset($params->idTipoDocumento)) ? $params->idTipoDocumento : null;
            
            // Query para Obtener todos las Instituciones segun Parametros de la Tabla: TblInstituciones
            $secuencias  = $em->getRepository("BackendBundle:TblSecuenciales")->findOneBy(
                    array(
                        "codSecuencial"   => $codigo_secuencia, // Codigo de la Secuencia
                        "tablaSecuencia"  => $tabla_secuencia,  // Tabla de la Secuencia a Obtener
                        "idTipoDocumento" => $tipo_documento, // Tipo de Documento (Oficio)
                        //"reservada"       => "N"
                    ));
            $optSec = 0;
            // Condicion para Actualizar Datos de la Secuencia
            if( $secuencias->getReservada() === "N"  ){
                //Opcion de Secuencia
                $optSec = 1;
                $secuencias->setReservada('S'); 
            
                //Realizar la Persistencia de los Datos y enviar a la BD
                $em->persist($secuencias);

                //Realizar la actualizacion en el storage de la BD
                $em->flush();
            } else if ( $secuencias->getReservada() === "S" ) {
                //$secuencias->setReservada('N'); 
                //Opcion de Secuencia
                $optSec = 2;
                $secuencias->setValor2( $secuencias->getValor2() + 1 ); //Set de valor2 de Secuencia de Comunicacion
                //$secuencias->setReservada('N'); 
            
                //Realizar la Persistencia de los Datos y enviar a la BD
                $em->persist($secuencias);

                //Realizar la actualizacion en el storage de la BD
                $em->flush();
            }
            
            
            // Validar que la Secuencia no ha sido, reservada por Otro Usuario
            $count_Sec = count($secuencias);
              
                
            // Condicion de la Busqueda
            if ( $count_Sec >= 1 ) {
                // Actualizamos la Suencia a Reservada
                //Seteo de Datos Generales de la tabla
                //$secueciasNew = new TblDocumentos();
                             
                $data = array(
                    "status" => "success",
                    "msg"    => "Seceuncia Encontrada",
                    "code"   => 200, 
                    "optSec" => $optSec,
                    "data"   => $secuencias
                );                
                                
                // Query para Obtener todos las Instituciones segun Parametros de la Tabla: TblInstituciones
                /*$secuenciasAct  = $em->getRepository("BackendBundle:TblSecuenciales")->findOneBy(
                    array(
                        "codSecuencial"   => $codigo_secuencia, // Codigo de la Secuencia
                        "tablaSecuencia"  => $tabla_secuencia,  // Tabla de la Secuencia a Obtener
                        "idTipoDocumento" => $tipo_documento, // Tipo de Documento (Oficio)
                        "reservada"       => "N"
                    ));
                
                // Acualiza que la Secuencia Esta Reservda
                $valor2_secuencia = $secuenciasAct->getValor2() + 1;
                //$reserva_secuencia = $secuencias->getReservada();
                $secuenciasAct->setValor2( $valor2_secuencia );
                $em->persist($secuenciasAct);
                // Realizar la actualizacion en el storage de la BD
                $em->flush();*/
                
            }else {
                $data = array(
                    "status" => "error",
                    "code"   => 400,
                    "optSec" => $optSec,
                    "msg"    => "No existe Datos en la Tabla de Secuencias, comuniquese con el Administrador !!"
                );
            }
        }
        }else{
            $data = array(
               "status" => "error",
               "code" => 400,
               "msg" => "Autorización no valida !!"
            );  
        }
        return $helpers->parserJson($data);
    }//FIN | FND00002
    
    
    
    /**
     * @Route("comunes/documentos-upload-options", name="comunes/documentos-upload-options")
     * Creacion del Controlador: Comunes Documentos PDF
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00003
     */
    public function uploadDocumentoOpctionAction(Request $request) {
        //Instanciamos el Servicio Helpers
        //date_default_timezone_set('Australia/Sydney');
        date_default_timezone_set('America/Tegucigalpa');
        $helpers = $this->get("app.helpers");

        $json = $request->get("json", null);
        $params = json_decode($json);
        
        $cod_contacto  = (isset($params->codigoSec)) ? $params->codigoSec : null;
        $cod_contacto2  = (isset($params->codigoSec2)) ? $params->codigoSec2 : null;
                
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
                //$nameDoc = $file->guessName();
                //Comprobamos que la Extencion sea Aceptada
                if ($ext == "pdf" || $ext == "PDF" || $ext == "doc" || $ext == "docs" || $ext == "docx" ||
                    $ext == "xlsx" || $ext == "xls" || $ext == "ppt" || $ext == "pptx" ||
                    $ext == "png" || $ext == "jpg" || $ext == "jpeg" ) {                   
                    // Concatenmos al Nombre del Fichero la Fecha y la Extencion
                    //$file_name = time().".".$ext;
                    $file_name = $file_nameIn . "-" . date('Y-m-d'). "." .$ext; 
                    //Movemos el Fichero
                    $path_of_file = "uploads/correspondencia/";
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
                        "msg" => "File not valid, please check this format !!"
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

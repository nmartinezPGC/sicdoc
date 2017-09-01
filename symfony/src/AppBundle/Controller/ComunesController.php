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
     * @Route("/uploadImage", name="uploadImage")
     * Creacion del Controlador: Comunes
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00001
     */
    public function uploadImageAction(Request $request) {
        //Instanciamos el Servicio Helpers
        $helpers = $this->get("app.helpers");        
        //Recoger el Hash
        //Recogemos el Hash y la Autrizacion del Mismo
        $hash = $request->get("authorization", null);
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
            $file = $request->files->get("image");
            //Se verifica que el fichero no venga Null
            if (!empty($file) && $file != null) {
                //Obtenemos la extencion del Fichero
                $ext = $file->guessExtension();
                //Comprobamos que la Extencion sea Aceptada
                if ($ext == "jpeg" || $ext == "jpg" || $ext == "png" || $ext == "gif") {                   
                    // Concatenmos al Nombre del Fichero la Fecha y la Extencion
                    $file_name = time().".".$ext;
                    //Movemos el Fichero
                    $file->move("uploads/users", $file_name);

                    //Seteamos el valor de la Imagen dentro de la Tabla:Tblusuarios+
                    $usuario->setImagenUsuario($file_name);
                    $em->persist($usuario);
                    $em->flush();
                
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
            //Variables que vienen del Json ***********************************************
            ////Recogemos el Pais y el Tipo de Institucion ********************************
            $codigo_secuencia = (isset($params->codSecuencial)) ? $params->codSecuencial : null;
            $tabla_secuencia  = (isset($params->tablaSecuencia)) ? $params->tablaSecuencia : null;
            
            // Query para Obtener todos las Instituciones segun Parametros de la Tabla: TblInstituciones
            $secuencias  = $em->getRepository("BackendBundle:TblSecuenciales")->findBy(
                    array(
                        "codSecuencial"   => $codigo_secuencia, // Codigo de la Secuencia
                        "tablaSecuencia"  => $tabla_secuencia // Tabla de la Secuencia a Obtener
                    ));

            // Condicion de la Busqueda
            if (count($secuencias) >= 1 ) {
                $data = array(
                    "status" => "success",
                    "code"   => 200,
                    "data"   => $secuencias
                );
            }else {
                $data = array(
                    "status" => "error",
                    "code"   => 400,
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
    
    
}

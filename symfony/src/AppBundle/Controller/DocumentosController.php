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

/**
 * Description of DocumentosController
 *
 * @author Sammy Guergachi <sguergachi at gmail.com>
 */
class DocumentosController extends Controller{
    //Accion de Prueba
    //Funcion de Nuevo Documento ***********************************************
    //**************************************************************************
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
            $identity = authCheck($hash, true);
            
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
                $cod_correspondencia  = ($params->cod_correspondencia != null) ? $params->cod_correspondencia : null ;
                $cod_usuario          = ($params->cod_usuario != null) ? $params->cod_usuario : null ;
                
                
                if($cod_usuario != null && $desc_documento != null){
                    //La condicion fue Exitosa
                    $em = $this->getDoctrine()->getManager();
                    $usuario = $em->getRepository("backendBundle:TblUsuarios")->findOneBy(
                        array(
                           "codUsuario" => $cod_usuario 
                        ));
                    
                    //Instanciamos Las Clases
                    $documentoNew = new TblDocumentos();
                    $documentoNew->setCodDocumento($cod_documento);
                    $documentoNew->setDescDocumento($desc_documento);
                    $documentoNew->setUrlDocumento($url_documento);
                    $documentoNew->setFechaIngreso($fecha_ingreso);
                    $documentoNew->setfech($fecha_modificacion);
                    $documentoNew->setUrlDocumento($url_documento);
                    $documentoNew->setCodUsuario($usuario);
                    $documentoNew = new TblDocumentos();
                    $documentoNew = new TblDocumentos();
                    
                    
                    
                }
                
            }
            
        } else {
            $data = array(
                "status" => "error",                
                "code" => "400",                
                "msg" => "Autorizacion de Token no valida !!"                
            );
        }
    } //Fin de la Funcion New Documento ****************************************
    
}

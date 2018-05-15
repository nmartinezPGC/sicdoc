<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Description of ChartsController
 *
 * @author Nahum Martinez
 */
class ChartsController extends Controller {
    
       
     /**
     * @Route("/com-ingresadas-list", name="com-ingresadas-list")
     * Creacion del Controlador: Total de Oficios Ingresados
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00001
     */
    public function comIngresadasListAction(Request $request)
    {
        date_default_timezone_set('America/Tegucigalpa');
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");
        
        // Parametros enviados por el Json
        $json = $request->get("json", null);
        $params = json_decode($json);
        
        if ($json != null) {
            //Variables que vienen del Json ************************************
            //Recogemos el ID del Depto. Funcional *****************************
            $tipo_comunicacion = (isset($params->idTipoCom)) ? $params->idTipoCom : null; 
            $user_comunicacion = (isset($params->idFuncionarioAsignado)) ? $params->idFuncionarioAsignado : null; 
            $tipo_documento    = (isset($params->idTipoDoc)) ? $params->idTipoDoc : null; 
        
            // Creacion del Metodo Create Query Builder | hace mas Efectiva la *****
            // Busqueda a la BD  ***************************************************
            $em = $this
                    ->getDoctrine()
                    ->getManager()
                    ->getRepository("BackendBundle:TblCorrespondenciaEnc");

            // Declaracion del Alias de la tabla
            $qb = $em->createQueryBuilder('a');

            // Query a la BD
            $qb->select('COUNT(a)');
            $qb->where('a.idEstado = :validEstado and a.idTipoDocumento = :validDocumento and '
                    . 'a.idTipoComunicacion = :validComunicacion and a.idFuncionarioAsignado = :validUsuario ');
            $qb->setParameter('validEstado', 7 )->setParameter('validDocumento', $tipo_documento )->setParameter('validComunicacion', $tipo_comunicacion )
                            ->setParameter('validUsuario', $user_comunicacion );

            $count = $qb->getQuery()->getSingleScalarResult();


            // Condicion de la Busqueda
            if ( $count >= 1 ) {
                $data = array(
                    "status" => "success",
                    "code"   => 200,
                    "data"   => $count
                );
            }else {
                $data = array(
                    "status" => "error",
                    "code"   => 400,
                    "msg"    => "No existe Datos en la Tabla de Correspondencia !!"
                );
            }            
        }else {
            $data = array(
                    "status" => "error",
                    "code"   => 400,
                    "msg"    => "No se ha encontrado parametros a enviar, contacte al Administrador !!"
                );
        }      
        // Retorno de los Datos
        return $helpers->parserJson($data);
    }//FIN | FND00001
    
    
    /**
     * @Route("/com-pendientes-list", name="com-pendientes-list")
     * Creacion del Controlador: Total de Oficios Pendientes
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00002
     */
    public function comPendientesListAction(Request $request)
    {
        date_default_timezone_set('America/Tegucigalpa');
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");
        
        // Parametros enviados por el Json
        $json = $request->get("json", null);
        $params = json_decode($json);
        
        if ($json != null) {
            //Variables que vienen del Json ************************************
            //Recogemos el ID del Depto. Funcional *****************************
            $tipo_comunicacion = (isset($params->idTipoCom)) ? $params->idTipoCom : null; 
            $user_comunicacion = (isset($params->idFuncionarioAsignado)) ? $params->idFuncionarioAsignado : null; 
            $tipo_documento    = (isset($params->idTipoDoc)) ? $params->idTipoDoc : null; 
        
            // Creacion del Metodo Create Query Builder | hace mas Efectiva la *****
            // Busqueda a la BD  ***************************************************
            $em = $this
                    ->getDoctrine()
                    ->getManager()
                    ->getRepository("BackendBundle:TblCorrespondenciaEnc");

            // Declaracion del Alias de la tabla
            $qb = $em->createQueryBuilder('a');

            // Query a la BD
            $qb->select('COUNT(a)');
            $qb->where('a.idEstado IN (:validEstado) and a.idTipoDocumento = :validDocumento and '
                    . 'a.idTipoComunicacion = :validComunicacion and a.idFuncionarioAsignado = :validUsuario ');
            $qb->setParameter('validEstado', [3,7,8] )->setParameter('validDocumento', $tipo_documento )->setParameter('validComunicacion', $tipo_comunicacion )
                        ->setParameter('validUsuario', $user_comunicacion );

            $count = $qb->getQuery()->getSingleScalarResult();


            // Condicion de la Busqueda
            if ( $count >= 1 ) {
                $data = array(
                    "status" => "success",
                    "code"   => 200,
                    "data"   => $count
                );
            }else {
                $data = array(
                    "status" => "error",
                    "code"   => 400,
                    "msg"    => "No existe Datos en la Tabla de Correspondencia !!"
                );
            }            
        }else {
            $data = array(
                    "status" => "error",
                    "code"   => 400,
                    "msg"    => "No se ha encontrado parametros, contacte al Administrador !!"
                );
        }               
        // Retorno de la Data
        return $helpers->parserJson($data);
    }//FIN | FND00002
    
    
    /**
     * @Route("/com-finalizados-list", name="com-finalizados-list")
     * Creacion del Controlador: Total de Oficios Finalizados
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00003
     */
    public function comFinalizadasListAction(Request $request)
    {
        date_default_timezone_set('America/Tegucigalpa');
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");
        
        // Parametros enviados por el Json
        $json = $request->get("json", null);
        $params = json_decode($json);
        
        if ($json != null) {
            //Variables que vienen del Json ************************************
            //Recogemos el ID del Depto. Funcional *****************************
            $tipo_comunicacion = (isset($params->idTipoCom)) ? $params->idTipoCom : null; 
            $user_comunicacion = (isset($params->idFuncionarioAsignado)) ? $params->idFuncionarioAsignado : null; 
            $tipo_documento    = (isset($params->idTipoDoc)) ? $params->idTipoDoc : null; 
        
            // Creacion del Metodo Create Query Builder | hace mas Efectiva la *****
            // Busqueda a la BD  ***************************************************
            $em = $this
                    ->getDoctrine()
                    ->getManager()
                    ->getRepository("BackendBundle:TblCorrespondenciaEnc");

            // Declaracion del Alias de la tabla
            $qb = $em->createQueryBuilder('a');

            // Query a la BD
            $qb->select('COUNT(a)');
            $qb->where('a.idEstado = :validEstado and a.idTipoDocumento = :validDocumento and '
                    . 'a.idTipoComunicacion = :validComunicacion and a.idFuncionarioAsignado = :validUsuario ');
            $qb->setParameter('validEstado', 5 )->setParameter('validDocumento', $tipo_documento )->setParameter('validComunicacion', $tipo_comunicacion )
                    ->setParameter('validUsuario', $user_comunicacion );

            $count = $qb->getQuery()->getSingleScalarResult();


            // Condicion de la Busqueda
            if ( $count >= 1 ) {
                $data = array(
                    "status" => "success",
                    "code"   => 200,
                    "data"   => $count
                );
            }else {
                $data = array(
                    "status" => "error",
                    "code"   => 400,
                    "msg"    => "No existe Datos en la Tabla de Correspondencia !!"
                );
            }            
        }else {
            $data = array(
                "status" => "error",
                "code"   => 400,
                "msg"    => "No se ha encontrado parametros, contacte al Administrador !!"
            );
        }
               
        
        return $helpers->parserJson($data);
    }//FIN | FND00003
    
    
    /**
     * @Route("/com-anulados-list", name="com-anulados-list")
     * Creacion del Controlador: Total de Oficios Anulados
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00003
     */
    public function comAnuladasListAction(Request $request)
    {
        date_default_timezone_set('America/Tegucigalpa');
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");
        
        // Parametros enviados por el Json
        $json = $request->get("json", null);
        $params = json_decode($json);
        
        if ($json != null) {
            //Variables que vienen del Json ************************************
            //Recogemos el ID del Depto. Funcional *****************************
            $tipo_comunicacion = (isset($params->idTipoCom)) ? $params->idTipoCom : null; 
            $user_comunicacion = (isset($params->idFuncionarioAsignado)) ? $params->idFuncionarioAsignado : null; 
            $tipo_documento    = (isset($params->idTipoDoc)) ? $params->idTipoDoc : null; 
        
            // Creacion del Metodo Create Query Builder | hace mas Efectiva la *****
            // Busqueda a la BD  ***************************************************
            $em = $this
                    ->getDoctrine()
                    ->getManager()
                    ->getRepository("BackendBundle:TblCorrespondenciaEnc");

            // Declaracion del Alias de la tabla
            $qb = $em->createQueryBuilder('a');

            // Query a la BD
            $qb->select('COUNT(a)');
            $qb->where('a.idEstado = :validEstado and a.idTipoDocumento = :validDocumento and '
                    . 'a.idTipoComunicacion = :validComunicacion and a.idFuncionarioAsignado = :validUsuario ');
            $qb->setParameter('validEstado', 4 )->setParameter('validDocumento', $tipo_documento )->setParameter('validComunicacion', $tipo_comunicacion )
                    ->setParameter('validUsuario', $user_comunicacion );

            $count = $qb->getQuery()->getSingleScalarResult();


            // Condicion de la Busqueda
            if ( $count >= 1 ) {
                $data = array(
                    "status" => "success",
                    "code"   => 200,
                    "data"   => $count
                );
            }else {
                $data = array(
                    "status" => "error",
                    "code"   => 400,
                    "msg"    => "No existe Datos en la Tabla de Correspondencia !!"
                );
            }            
        }else {
            $data = array(
                "status" => "error",
                "code"   => 400,
                "msg"    => "No se ha encontrado parametros, contacte al Administrador !!"
            );
        }
               
        // Retorna el Dato de la Consulta
        return $helpers->parserJson($data);
    }//FIN | FND00003
    
    
    /**
     * @Route("/resumen-comunicacion-list", name="resumen-comunicacion-list")
     * Creacion del Controlador: Total de Comunicaciones Ingresadas
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * @return array de Comunicaciones Ingresadas
     * Funcion: FND00004
     */
    public function resumenComunicacionListAction(Request $request){
        date_default_timezone_set('America/Tegucigalpa');
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");
        
        // Parametros enviados por el Json
        $json = $request->get("json", null);
        $params = json_decode($json);
        
        //Recoger el Hash
        //Recogemos el Hash y la Autrizacion del Mismo
        $hash = $request->get("authorization", null);
        //Se Chekea el Token
        $checkToken = $helpers->authCheck($hash);
        
        //Evalua que el Token sea True
        if($checkToken == true){
            $identity = $helpers->authCheck($hash, true);
            
            if ($json != null) {
                //Parametros a Convertir                           
                //Datos generales de la Tabla
                $id_usuario        = $identity->sub;
                $tipo_comunicacion      = (isset($params->idTipoCom)) ? $params->idTipoCom : null; 
                $id_estado_comunicacion = (isset($params->idEstado)) ? $params->idEstado : null; 
                //$tipo_documento    = (isset($params->idTipoDoc)) ? $params->idTipoDoc : null; 

                // Creacion del Metodo Create Query Builder | hace mas Efectiva la *****
                // Busqueda a la BD  ***************************************************
                $em = $this->getDoctrine()->getManager();

                // Query a la BD
                $opt = 1;
                     /*
                      * (SELECT COUNT(ce1.idTipoDocumento) FROM BackendBundle:TblCorrespondenciaEnc ce1 WHERE ce1.idTipoDocumento = :idTipoCom
                                                                               AND ce1.idEstado = 7 AND ce1.idFuncionarioAsignado = :idUsuario ) AS INGRESADO,
                      */
                $dql = $em->createQuery('SELECT td.idTipoDocumento,                                            
                                            (SELECT COUNT(ce2.idTipoDocumento) FROM BackendBundle:TblCorrespondenciaEnc ce2 WHERE ce2.idTipoDocumento = :idTipoCom
                                                                               AND ce2.idEstado IN (3,7,8) AND ce2.idFuncionarioAsignado = :idUsuario ) AS PENDIENTE,
                                            (SELECT COUNT(ce3.idTipoDocumento) FROM BackendBundle:TblCorrespondenciaEnc ce3 WHERE ce3.idTipoDocumento = :idTipoCom
                                                                               AND ce3.idEstado = 5 AND ce3.idFuncionarioAsignado = :idUsuario ) AS RESUELTO,
                                            (SELECT COUNT(ce4.idTipoDocumento) FROM BackendBundle:TblCorrespondenciaEnc ce4 WHERE ce4.idTipoDocumento = :idTipoCom
                                                                               AND ce4.idEstado = 4 AND ce4.idFuncionarioAsignado = :idUsuario ) AS ANULADO
                                    FROM BackendBundle:TblTipoDocumento td 
                                    WHERE td.idTipoDocumento IN ( :idTipoCom )  
                                    GROUP BY td.idTipoDocumento  
                                    ORDER BY td.idTipoDocumento ' )
                        ->setParameter('idUsuario', $id_usuario)->setParameter('idTipoCom', $tipo_comunicacion ) ;
             
                // Busqueda a la BD  ***************************************************
                $arrayIngresados = $dql->getResult();

                $count = count( $arrayIngresados) ;

                // Condicion de la Busqueda
                if ( $count >= 1 ) {
                    $data = array(
                        "status" => "success",
                        "code"   => 200,
                        "totalRegistros" => $count,
                        "data"   => $arrayIngresados
                    );
                }else {
                    $data = array(
                        "status" => "error",
                        "code"   => 400,
                        "msg"    => "No existe Datos en la Tabla de Correspondencia !!"
                    );
                }            
            }else {
                $data = array(
                    "status" => "error",
                    "code"   => 400,
                    "msg"    => "No se ha encontrado parametros, contacte al Administrador !!"
                );
            }  
        }else{
           $data = array(
                "status" => "error",
                "code"   => 300,
                "msg"    => "Autorizacion no Valida, porfavor inicia sesion para continuar !!"
            ); 
        }
                       
        // Retorna el Dato de la Consulta
        return $helpers->parserJson($data);
    }//FIN | FND00004
    
}

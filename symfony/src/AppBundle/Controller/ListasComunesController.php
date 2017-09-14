<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Description of ListasComunesController
 *
 * @author Nahum Martinez
 */
class ListasComunesController extends Controller {
    
    
    /**
     * @Route("/estados-user-list", name="estados-user-list")
     * Creacion del Controlador: Estados
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00001
     */
    public function estadosUsuarioListAction(Request $request)
    {
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");
        
        $em = $this->getDoctrine()->getManager();
        
        // Query para Obtener todos los Estados de la Tabla: TblEstados
        $estados = $em->getRepository("BackendBundle:TblEstados")->findBy(
                array(
                    "grupoEstado" => "USR"
                ));
        
        // Condicion de la Busqueda
        if (count($estados) >= 1 ) {
            $data = array(
                "status" => "success",
                "code"   => 200,
                "data"   => $estados
            );
        }else {
            $data = array(
                "status" => "error",
                "code"   => 400,
                "msg"    => "No existe Datos en la Tabla de Estados !!"
            );
        }
        
        return $helpers->parserJson($data);
    }//FIN | FND00001
    
    
    /**
     * @Route("/tipo-funcionario-list", name="tipo-funcionario-list")
     * Creacion del Controlador: Tipo Funcionario
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00002
     */
    public function tipoFuncionarioListAction(Request $request)
        {
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");
        
        $em = $this->getDoctrine()->getManager();
        
        // Query para Obtener todos los Estados de la Tabla: TblEstados
        $tipoFunc = $em->getRepository("BackendBundle:TblTiposFuncionarios")->findAll();
        
        // Condicion de la Busqueda
        if (count($tipoFunc) >= 1 ) {
            $data = array(
                "status" => "success",
                "code"   => 200,
                "data"   => $tipoFunc
            );
        }else {
            $data = array(
                "status" => "error",
                "code"   => 400,
                "msg"    => "No existe Datos en la Tabla de Tipo Funcionarios !!"
            );
        }
        
        return $helpers->parserJson($data);
    }//FIN | FND00002
    
    
    /**
     * @Route("/subdir-sreci-list", name="subdir-sreci-list")
     * Creacion del Controlador: Departamentos Funcionales
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00003
     */
    public function deptoFuncionalListAction(Request $request)
    {
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");
        
        $em = $this->getDoctrine()->getManager();
        
        $json = $request->get("json", null);
        $params = json_decode($json);
        
        //Evaluamos el Json
        if ($json != null) {
            //Variables que vienen del Json ***********************************************
            ////Recogemos el la Direccion ********************************
            $direccion_sreci    = (isset($params->idDireccionSreci)) ? $params->idDireccionSreci : null;
            
            // Query para Obtener todos los Estados de la Tabla: TblEstados
            $tipoFunc = $em->getRepository("BackendBundle:TblDepartamentosFuncionales")->findBy(
                    array(
                        "idDireccionSreci" => $direccion_sreci
                    ));

            // Condicion de la Busqueda
            if (count($tipoFunc) >= 1 ) {
                $data = array(
                    "status" => "success",
                    "code"   => 200,
                    "data"   => $tipoFunc
                );
            }else {
                $data = array(
                    "status" => "error",
                    "code"   => 400,
                    "msg"    => "No existe Datos en la Tabla de Sub Direcciones !!"
                );
            }
        }else {
            $data = array(
                "status" => "error",
                "code"   => 400,
                "msg"    => "No existe Datos en la Tabla de Sub Direcciones, comuniquese con el Administrador !!"
            );
        }
               
        return $helpers->parserJson($data);
    }//FIN | FND00003
    
    
    /**
     * @Route("/tipoUsuarioList", name="tipoUsuarioList")
     * Creacion del Controlador: Tipos de Usuarios
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00004
     */
    public function tipoUsuarioListAction(Request $request)
    {
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");
        
        $em = $this->getDoctrine()->getManager();
        
        // Query para Obtener todos los Estados de la Tabla: TblEstados
        $tipoFunc = $em->getRepository("BackendBundle:TblTipoUsuario")->findBy(
                array(
                    "idTipoUsuario" => [2,3,4,5] //Excluimos el Administrador
                ));
        
        // Condicion de la Busqueda
        if (count($tipoFunc) >= 1 ) {
            $data = array(
                "status" => "success",
                "code"   => 200,
                "data"   => $tipoFunc
            );
        }else {
            $data = array(
                "status" => "error",
                "code"   => 400,
                "msg"    => "No existe Datos en la Tabla de Tipo Usuarios !!"
            );
        }
        
        return $helpers->parserJson($data);
    }//FIN | FND00004
    
    
    /**
     * @Route("/instituciones-sreci", name="/instituciones-sreci")
     * Creacion del Controlador: Instituciones
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00005
     */
    public function institucionesSreciAction(Request $request)
    {
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");
        
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
            $pais_institucion    = (isset($params->idPais)) ? $params->idPais : null;
            $tipo_institucion    = (isset($params->idTipoInstitucion)) ? $params->idTipoInstitucion : null;
            
            // Query para Obtener todos las Instituciones segun Parametros de la Tabla: TblInstituciones
            $institucionesSreci = $em->getRepository("BackendBundle:TblInstituciones")->findBy(
                    array(
                        "idTipoInstitucion" => $tipo_institucion, //Tipo de Institucion
                        "idPais"            => $pais_institucion //Pais de la Institucion
                    ));

            // Condicion de la Busqueda
            if (count($institucionesSreci) >= 1 ) {
                $data = array(
                    "status" => "success",
                    "code"   => 200,
                    "data"   => $institucionesSreci
                );
            }else {
                $data = array(
                    "status" => "error",
                    "code"   => 400,
                    "msg"    => "No existe Datos en la Tabla de Instituciones, comuniquese con el Administrador !!"
                );
            }
        }
        return $helpers->parserJson($data);
    }//FIN | FND00005
    
    
    /**
     * @Route("/tipo-instituciones-sreci", name="tipo-instituciones-sreci")
     * Creacion del Controlador: Tipo de Instirucion
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00005.1
     */
    public function tipoInstitucionesAction(Request $request)
    {
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");
        
        $em = $this->getDoctrine()->getManager();
        
        
        // Query para Obtener todos los Estados de la Tabla: TblEstados
        $tipoInstitucion = $em->getRepository("BackendBundle:TblTipoInstitucion")->findAll();
        
        // Condicion de la Busqueda
        if (count( $tipoInstitucion ) >= 1 ) {
            $data = array(
                "status" => "success",
                "code"   => 200,
                "data"   => $tipoInstitucion
            );
        }else {
            $data = array(
                "status" => "error",
                "code"   => 400,
                "msg"    => "No existe Datos en la Tabla de Tipo de Instiucion !!"
            );
        }
        
        return $helpers->parserJson($data);
    }//FIN | FND00005.1
    
    
    /**
     * @Route("/paisesList", name="paisesList")
     * Creacion del Controlador: Paises
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00006
     */
    public function paisesListAction(Request $request)
    {
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");
        
        $em = $this->getDoctrine()->getManager();
        
        
        // Query para Obtener todos los Estados de la Tabla: TblEstados
        $paisList = $em->getRepository("BackendBundle:TblPais")->findAll();
        
        // Condicion de la Busqueda
        if (count( $paisList ) >= 1 ) {
            $data = array(
                "status" => "success",
                "code"   => 200,
                "data"   => $paisList
            );
        }else {
            $data = array(
                "status" => "error",
                "code"   => 400,
                "msg"    => "No existe Datos en la Tabla de Paises !!"
            );
        }
        
        return $helpers->parserJson($data);
    }//FIN | FND00006
    
    
    /**
     * @Route("/estadosComunicacionList", name="estadosComunicacionList")
     * Creacion del Controlador: Estados
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00007
     */
    public function estadosComunicacionListAction(Request $request)
    {
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");
        
        $em = $this->getDoctrine()->getManager();
        
        // Query para Obtener todos los Estados de la Tabla: TblEstados
        $estados = $em->getRepository("BackendBundle:TblEstados")->findBy(
                array(
                    "grupoEstado" => "MAIL"
                ));
        
        // Condicion de la Busqueda
        if (count($estados) >= 1 ) {
            $data = array(
                "status" => "success",
                "code"   => 200,
                "data"   => $estados
            );
        }else {
            $data = array(
                "status" => "error",
                "code"   => 400,
                "msg"    => "No existe Datos en la Tabla de Estados !!"
            );
        }
        
        return $helpers->parserJson($data);
    }//FIN | FND00007
    
    
    /**
     * @Route("/tipo-documento-list", name="tipo-documento-list")
     * Creacion del Controlador: Tipo de Documentos
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00008
     */
    public function tipoDocumentoListAction(Request $request)
    {
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");
        
        $em = $this->getDoctrine()->getManager();
        
        // Query para Obtener todos los Estados de la Tabla: TblEstados
        $tipo_documento = $em->getRepository("BackendBundle:TblTipoDocumento")->findAll();
        
        // Condicion de la Busqueda
        if (count($tipo_documento) >= 1 ) {
            $data = array(
                "status" => "success",
                "code"   => 200,
                "data"   => $tipo_documento
            );
        }else {
            $data = array(
                "status" => "error",
                "code"   => 400,
                "msg"    => "No existe Datos en la Tabla de Tipo de Documentos !!"
            );
        }
        
        return $helpers->parserJson($data);
    }//FIN | FND00008
    
    
    /**
     * @Route("/depto-funcional-user-list", name="depto-funcional-user-list")
     * Creacion del Controlador: Depto. Funcional por usuario
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00009
     */
    public function deptoFuncionalUserListAction(Request $request)
    {
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");
        //Recoger el Hash
        //Recogemos el Hash y la Autrizacion del Mismo
        $hash = $request->get("authorization", null);
        //Se Chekea el Token
        $checkToken = $helpers->authCheck($hash);
        
        $identity = $helpers->authCheck($hash, true);
        
        $em = $this->getDoctrine()->getManager();
        
        
        // Query para Obtener los Datos del usuario: TblUsusarios
        $cred_user = $em->getRepository("BackendBundle:TblUsuarios")->findBy(
                array(
                    "idUsuario" => $identity->sub
                ));
        
        // Query para Obtener el Departamento al que corresponde de la Tabla: TblDepartamentosFuncionales
        $depto_user = $em->getRepository("BackendBundle:TblDepartamentosFuncionales")->findBy(
                array(
                    "idDeptoFuncional" => $cred_user
                ));
        
        // Query para Obtener la Direccion que corresponde de la Tabla: TblDireccionesSreci
        $direccion_user = $em->getRepository("BackendBundle:TblDireccionesSreci")->findBy(
                array(
                    "idDireccionSreci" => $depto_user
                ));
        
        // Condicion de la Busqueda
        if (count($direccion_user) >= 1 ) {
            $data = array(
                "status" => "success",
                "code"   => 200,
                "data"   => $direccion_user
            );
        }else {
            $data = array(
                "status" => "error",
                "code"   => 400,
                "msg"    => "No existe Datos en la Tabla de Deparmentos Funcionales !!"
            );
        }
        
        return $helpers->parserJson($data);
    }//FIN | FND00009
    
    
    /**
     * @Route("/dir-sreci-list", name="dir-sreci-list")
     * Creacion del Controlador: Direcciones de SRECI
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00010
     */
    public function direccionSreciListAction(Request $request)
    {
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");
        
        $em = $this->getDoctrine()->getManager();
        
        // Query para Obtener todos los Estados de la Tabla: TblEstados
        $direccion_sreci = $em->getRepository("BackendBundle:TblDireccionesSreci")->findAll();
        
        // Condicion de la Busqueda
        if (count($direccion_sreci) >= 1 ) {
            $data = array(
                "status" => "success",
                "code"   => 200,
                "data"   => $direccion_sreci
            );
        }else {
            $data = array(
                "status" => "error",
                "code"   => 400,
                "msg"    => "No existe Datos en la Tabla de Departamentos Funcionales !!"
            );
        }
        
        return $helpers->parserJson($data);
    }//FIN | FND00010
    
    
     /**
     * @Route("/com-ingresadas-list", name="com-ingresadas-list")
     * Creacion del Controlador: Comunicaciones
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00011
     */
    public function comIngresadasListAction(Request $request)
    {
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");
               
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
        $qb->where('a.idEstado = :validEstado and a.idTipoDocumento = :validDocumento ');
        $qb->setParameter('validEstado', 7 )->setParameter('validDocumento', 1 )  ;

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
        
        return $helpers->parserJson($data);
    }//FIN | FND00011
    
    
    /**
     * @Route("/com-pendientes-list", name="com-pendientes-list")
     * Creacion del Controlador: Comunicaciones
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00012
     */
    public function comPendientesListAction(Request $request)
    {
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");
               
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
        $qb->where('a.idEstado = :validEstado and a.idTipoDocumento = :validDocumento ');
        $qb->setParameter('validEstado', 3 )->setParameter('validDocumento', 1 )  ;

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
        
        return $helpers->parserJson($data);
    }//FIN | FND00012
    
    
    /**
     * @Route("/com-finalizados-list", name="com-finalizados-list")
     * Creacion del Controlador: Comunicaciones
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00013
     */
    public function comFinalizadasListAction(Request $request)
    {
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");
               
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
        $qb->where('a.idEstado = :validEstado and a.idTipoDocumento = :validDocumento ');
        $qb->setParameter('validEstado', 5 )->setParameter('validDocumento', 1 )  ;

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
        
        return $helpers->parserJson($data);
    }//FIN | FND00013
    
    
    /**
     * @Route("/asignar-oficios-list", name="asignar-oficios-list")
     * Creacion del Controlador: Comunicaciones
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00014
     */
    public function asignarOficiosListAction(Request $request )
    {
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");
               
        // Creacion del Metodo Create Query Builder | hace mas Efectiva la *****
        // Busqueda a la BD  ***************************************************
        $em = $this
                ->getDoctrine()
                ->getManager();
                
        $dql = "SELECT v FROM BackendBundle:TblCorrespondenciaEnc v ORDER BY v.idCorrespondenciaEnc DESC";
        $query = $em->createQuery($dql);
        
        $page = $request->query->getInt("page", 1);
        $paginator = $this->get("knp_paginator");
        $item_per_page = 6;
        
        $pagination = $paginator->paginate($query, $page, $item_per_page);
        $total_items_count = $pagination->getTotalItemCount();
        
        $data = array(
                "status" => "success",
                "code"   => 200,
                "total_items_count"   => $total_items_count,
                "page_actual"   => $page,
                "items_per_page"   => $item_per_page,
                "total_page"   => ceil( $total_items_count / $item_per_page ),
                "data"   => $pagination
            );
                
        return $helpers->parserJson($data);
    }//FIN | FND00014
    
    
}

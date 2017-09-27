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
     * @Route("/depto-func-user", name="depto-func-user")
     * Creacion del Controlador: Depto. Funcionales de User
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00003.1
     */
    public function deptoFuncionalUserAction(Request $request)
    {
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");
        
        $em = $this->getDoctrine()->getManager();
        
        $json = $request->get("json", null);
        $params = json_decode($json);
        
        //Evaluamos el Json
        if ($json != null) {
            //Variables que vienen del Json ************************************
            ////Recogemos el ID del Usuario ************************************
            $id_usuario   = (isset($params->idUser)) ? $params->idUser : null;
            
            // Query para Obtener todos los Estados de la Tabla: TblUsuarios
            $userFunc = $em->getRepository("BackendBundle:TblUsuarios")->findOneBy(
                    array(
                        "idUsuario" => $id_usuario
                    ));
            

            // Condicion de la Busqueda
            if (count($userFunc) >= 1 ) {
                $data = array(
                    "status" => "success",
                    "code"   => 200,
                    "data"   => $userFunc
                );
            }else {
                $data = array(
                    "status" => "error",
                    "code"   => 400,
                    "msg"    => "No existe Datos de Funcionario para este Usuario !!"
                );
            }
        }else {
            $data = array(
                "status" => "error",
                "code"   => 400,
                "msg"    => "No existe Datos en la Tabla de Funcionarios, comuniquese con el Administrador !!"
            );
        }
               
        return $helpers->parserJson($data);
    }//FIN | FND00003.1
    
    
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
        $tipo_documento = $em->getRepository("BackendBundle:TblTipoDocumento")->findBy(
                array(
                    "activo" => TRUE
                )
            );
        
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
                "msg"    => "No existe Datos en la Tabla de Departamentos Funcionales !!"
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
        
        // Query para Obtener la Direccion del Funcionario de la Tabla: TblDireccionesSreci
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
     * Creacion del Controlador: Total de Oficios Ingresados
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
     * Creacion del Controlador: Total de Oficios Pendientes
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
     * Creacion del Controlador: Total de Oficios Finalizados
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
     * Creacion del Controlador: Listado de Todos Ofcios a Asignar, All
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00014
     */
    public function asignarOficiosListAction(Request $request )
    {
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");
        
        $em = $this->getDoctrine()->getManager();
        
        // Query para Obtener todos los Estados de la Tabla: TblEstados
        $com_enc = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findAll();
        
        // Condicion de la Busqueda
        if (count($com_enc) >= 1 ) {
            $data = array(
                "status" => "success",
                "code"   => 200,
                "data"   => $com_enc
            );
        }else {
            $data = array(
                "status" => "error",
                "code"   => 400,
                "msg"    => "No existe Datos en la Tabla de Correspondencias !!"
            );
        }
        
        return $helpers->parserJson($data);
    }//FIN | FND00014
    
    
    /**
     * @Route("/asignar-oficios-page-list", name="asignar-oficios-page-list")
     * Creacion del Controlador: Listado de Todos Ofcios a Asignar, por Page
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00015
     */
    public function asignarOficiosPageListAction(Request $request, $search = null )
    {
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");
        
        //Recogemos el Hash y la Autorizacion del Mismo        
        $hash = $request->get("authorization", null);
        //Se Chekea el Token
        $checkToken = $helpers->authCheck($hash);
        
        // Parametros enviados por el Json
        $json = $request->get("json", null);
        $params = json_decode($json);
        
        //Variables que vienen del Json ************************************
        //Recogemos el ID del Depto. Funcional *****************************
        //$page = (isset($params->page)) ? $params->page : 1;
               
        // Creacion del Metodo Create Query Builder | hace mas Efectiva la *****
        // Busqueda a la BD  ***************************************************
        $identity = $helpers->authCheck($hash, true);
        $em = $this
                ->getDoctrine()
                ->getManager();
        
        // Recogemos los Parametros de la URL vi GET, esto con el Fin de *******
        // Incluirlos en el Query        
        $idDeptoFuncional = $request->query->getInt("idDeptoFuncional", null);
        $idFuncionarioAsignado = $request->query->getInt("idFuncionarioAsignado", null);
        $idUsuario = $request->query->getInt("idUsuario", null);
        
        // Query para Obtener  de la Tabla: TblUsuarios ************************
        // Utilizamos esta seccion para Obtener el Perfil del Funcionario ******
        $tipo_usuario = $em->getRepository("BackendBundle:TblUsuarios")->findOneBy(
            array(
                "idUsuario" => $idUsuario
            ));
            
        // Parametro de Tipo de Funcionario | Director ID (6)
        $tipo_funcionario_entitie = $tipo_usuario->getIdTipoFuncionario(); // Obtenemos la Entidad Completa
        $tipo_funcionario = $tipo_funcionario_entitie->getIdTipoFuncionario(); // Obtenemos el Tipo de Funcionario
        // Es Director y solo el Puede Asignar Oficios
        if ( $tipo_funcionario === 6 ) {        
            // Evaluamos si si hizo una consulta desde la caja Search
            if ( $search != null ) {
               $dql = "SELECT v FROM BackendBundle:TblCorrespondenciaEnc v "
                    . "WHERE v.idDeptoFuncional = :search "                
                    . "ORDER BY v.idCorrespondenciaEnc DESC";

               $query = $em->createQuery($dql)
                        ->setParameter("search", "%$search%");
            }else{
                $dql = "SELECT v FROM BackendBundle:TblCorrespondenciaEnc v "
                    . "WHERE v.idDeptoFuncional = '". $idDeptoFuncional ."' AND v.idEstado IN (7) "
                    . "ORDER BY v.idCorrespondenciaEnc DESC";

                $query = $em->createQuery($dql);
            }        

            // Parametros de la Paginacion
            $page = $request->query->getInt("page", 1);
            $paginator = $this->get("knp_paginator");
            $item_per_page = 10;

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
        }else{
            // Lanzamos la Consulta Errornea para Llenar la Condicion
            $dql = "SELECT v FROM BackendBundle:TblCorrespondenciaEnc v "
                    . "WHERE v.idDeptoFuncional = 100000 "
                    . "ORDER BY v.idCorrespondenciaEnc DESC";

            $query = $em->createQuery($dql);
            $paginator = $this->get("knp_paginator");
            $pagination = $paginator->paginate($query, 1, 1);
            $data = array(
                    "status" => "success",
                    "code"   => 200,
                    "total_items_count"   => 0,
                    "page_actual"   => 1,
                    "items_per_page"   => 0,
                    "total_page"   => 0,
                    "data"   => $pagination
                );
        }

        return $helpers->parserJson($data);
    }//FIN | FND00015
    
    
    /**
     * @Route("/funcionarios-depto-list", name="funcionarios-depto-list")
     * Creacion del Controlador: Funcionario a Asignar Oficio
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00016
     */
    public function funcionariosAsignadosListAction(Request $request)
    {
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");
        
        $em = $this->getDoctrine()->getManager();
        
        $json = $request->get("json", null);
        $params = json_decode($json);
        
        //Evaluamos el Json
        if ($json != null) {
            //Variables que vienen del Json ************************************
            //Recogemos el ID del Depto. Funcional *****************************
            $tipo_funcionario = (isset($params->idDeptoFunc)) ? $params->idDeptoFunc : null;                       
            
            // Query para Obtener todos los Funcionarios de la Tabla: TblFuncionarios
            $usuario_asignado = $em->getRepository("BackendBundle:TblFuncionarios")->findBy(
                    array(
                        "idDeptoFuncional" => $tipo_funcionario                        
                    ));

            // Condicion de la Busqueda
            if (count( $usuario_asignado ) >= 1 ) {
                $data = array(
                    "status" => "success",
                    "code"   => 200,
                    "data"   => $usuario_asignado
                );
            }else {
                $data = array(
                    "status" => "error",
                    "code"   => 400,
                    "msg"    => "No existe Funcionarios asociados al Departamento, "
                                . "comuniquese con el Administrador !!"
                );
            }
        }else {
            $data = array(
                "status" => "error",
                "code"   => 400,
                "msg"    => "No existe Datos en la Tabla de Funcionarios, comuniquese con el Administrador !!"
            );
        }
               
        return $helpers->parserJson($data);
    }//FIN | FND00016
    
    
    /**
     * @Route("/funcionarios-list", name="funcionarios-list")
     * Creacion del Controlador: Funcionarios Listado Completo
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00017
     */
    public function funcionariosListAction(Request $request)
    {
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");
        
        $em = $this->getDoctrine()->getManager();
        
        $json = $request->get("json", null);
        $params = json_decode($json);
        
        //Evaluamos el Json
        if ($json != null) {
            //Variables que vienen del Json ************************************
            //Recogemos el ID del Tipo de Funcionario***************************
            $tipo_funcionario = (isset($params->idTipoFuncionario)) ? $params->idTipoFuncionario : null;
            
            // Query para Obtener todos los Estados de la Tabla: TblUsuarios
            $usuario_asignado = $em->getRepository("BackendBundle:TblFuncionarios")->findBy(
                    array(
                        "idTipoFuncionario" => $tipo_funcionario
                    ));

            // Condicion de la Busqueda
            if (count( $usuario_asignado ) >= 1 ) {
                $data = array(
                    "status" => "success",
                    "code"   => 200,
                    "data"   => $usuario_asignado
                );
            }else {
                $data = array(
                    "status" => "error",
                    "code"   => 400,
                    "msg"    => "No existe Datos en la Tabla de Funcionarios !!"
                );
            }
        }else {
            $data = array(
                "status" => "error",
                "code"   => 400,
                "msg"    => "No existe Datos en la Tabla de Funcionarios, comuniquese con el Administrador !!"
            );
        }
               
        return $helpers->parserJson($data);
    }//FIN | FND00017
    
    
    /**
     * @Route("/finalizar-oficios-list", name="finalizar-oficios-list")
     * Creacion del Controlador: Finalizar Oficio asignado
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Fecha: 2017-09-21
     * Objetivo: Mostrar el listado de los Oficios que fueron asignados
     * y trabajar con ellos, luego Finzalizar la Actividad
     * @param number $idDeptoFunc Departaento Funcional que pertenece el Func.
     * @param number $idTipoFuncionario Tipo Funcionario que accede al sistema
     * @param number $idFuncionario Funcionario que accede al sistema
     * Funcion: FND00018
     */
    public function finalizarOficiosListAction(Request $request )
    {
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");
        
        //Recogemos el Hash y la Autorizacion del Mismo        
        $hash = $request->get("authorization", null);
        //Se Chekea el Token
        $checkToken = $helpers->authCheck($hash);
        
        // Parametros enviados por el Json
        $json = $request->get("json", null);
        $params = json_decode($json);
        
        //Evaluamos el Json
        if ($json != null) {
            $identity = $helpers->authCheck($hash, true);
            $em = $this
                ->getDoctrine()
                ->getManager();
            
            //Variables que vienen del Json ************************************
            //Recogemos el ID Funcionario , Depto Func *************************
            $depto_funcional = (isset($params->idDeptoFunc)) ? $params->idDeptoFunc : null;
            //$tipo_funcionario = (isset($params->idTipoFuncionario)) ? $params->idTipoFuncionario : null;
            $id_funcionario = $identity->sub;
            
            
            // Query para Obtener todos los Oficios de ese Funcionario de la ***
            // Tabla: TblCorrespondenciaEnc ************************************
            $usuario_asignado = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findBy(
                    array(
                        "idDeptoFuncional"      => $depto_funcional,                        
                        "idFuncionarioAsignado" => $id_funcionario,
                        "idEstado"              => [3,8]                        
                        //"idTipoDocumento"       => [1]
                    ), array("idCorrespondenciaEnc" => "ASC", "fechaIngreso" => "ASC") );

            // Condicion de la Busqueda
            if (count( $usuario_asignado ) >= 1 ) {
                $data = array(
                    "status" => "success",
                    "code"   => 200,
                    "data"   => $usuario_asignado
                );
            }else {
                $data = array(
                    "status" => "error",
                    "code"   => 400,
                    "msg"    => "No existe Datos de Oficios Asignados en la Tabla de Correspondencia para usted !!"
                );
            }
        }else {
            $data = array(
                "status" => "error",
                "code"   => 400,
                "msg"    => "No existe Datos en la Tabla de Correspondencia, comuniquese con el Administrador !!"
            );
        }
               
        return $helpers->parserJson($data);
    }//FIN | FND00018
    
    
    /**
     * @Route("/finalizar-oficios-det-list", name="finalizar-oficios-det-list")
     * Creacion del Controlador: Lista de Oficos en la Tabla Detalle
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Fecha: 2017-09-21
     * Objetivo: Mostrar el listado de los Oficios que fueron asignados
     * y trabajar con ellos, luego Finzalizar la Actividad
     * @param number idCorrespondenciaEnc Id de la Correspodencia ENC.
     * @param number idEstadoDet  Estado de la Correspondencia
     * @param number $idFuncionario Funcionario que accede al sistema
     * Funcion: FND00019
     */
    public function finalizarOficiosListDetAction(Request $request )
    {
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");
        
        //Recogemos el Hash y la Autorizacion del Mismo        
        $hash = $request->get("authorization", null);
        //Se Chekea el Token
        $checkToken = $helpers->authCheck($hash);
        
        // Parametros enviados por el Json
        $json = $request->get("json", null);
        $params = json_decode($json);
        
        //Evaluamos el Json
        if ($json != null) {
            $identity = $helpers->authCheck($hash, true);
            $em = $this
                ->getDoctrine()
                ->getManager();
            
            //Variables que vienen del Json ************************************
            //Recogemos el ID Funcionario , Depto Func *************************
            $id_corresp_enc = (isset($params->idCorrespondenciaEnc)) ? $params->idCorrespondenciaEnc : null;
            $id_estado_det = (isset($params->idEstadoDet)) ? $params->idEstadoDet : null;
            
            $id_funcionario = $identity->sub;
                        
            // Query para Obtener todos los Oficios de ese Funcionario de la ***
            // Tabla: TblCorrespondenciaDet ************************************
            $detalle_corresp = $em->getRepository("BackendBundle:TblCorrespondenciaDet")->findOneBy(
                    array(
                        "idCorrespondenciaEnc"  => $id_corresp_enc,
                        "idFuncionarioAsignado" => $id_funcionario,
                        "idEstado"              => $id_estado_det
                    ));

            // Condicion de la Busqueda
            if (count( $detalle_corresp ) >= 1 ) {
                $data = array(
                    "status" => "success",
                    "code"   => 200,
                    "data"   => $detalle_corresp
                );
            }else {
                $data = array(
                    "status" => "error",
                    "code"   => 400,
                    "msg"    => "No existe Datos de Oficios en ese Estado !!"
                );
            }
        }else {
            $data = array(
                "status" => "error",
                "code"   => 400,
                "msg"    => "No existe Datos en la Tabla de Correspondencia, comuniquese con el Administrador !!"
            );
        }
               
        return $helpers->parserJson($data);
    }//FIN | FND00019
    
    
    
}

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
     * @Route("/sub-direcciones-sreci-list", name="sub-direcciones-sreci-list")
     * Creacion del Controlador: Sub Direcciones de SRECI
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00001.1
     */
    public function subDireccionesSreciListAction(Request $request, $idDireccionSreci = null )
    {
        date_default_timezone_set('America/Tegucigalpa');
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");
        
        $em = $this->getDoctrine()->getManager();
        
        // Parametros de la Consulta
        $idDireccionSreciIn = $request->query->getInt("idDireccionSreci", null);
        
        $opt = 0;
        // Condicion de Direccion SRECI
        if( $idDireccionSreciIn != 0 || $idDireccionSreciIn != null ){
            $opt = 1;
            $dql = $em->createQuery('SELECT deptoF.idDeptoFuncional as id , '                    
                    . "deptoF.descDeptoFuncional as itemName, "
                    . 'deptoF.codDeptoFuncional as name, deptoF.inicialesDeptoFuncional as name2 '                                    
                    . 'FROM BackendBundle:TblDepartamentosFuncionales deptoF '
                    . 'INNER JOIN BackendBundle:TblDireccionesSreci dirSreci WITH dirSreci.idDireccionSreci = deptoF.idDireccionSreci '
                    . 'WHERE deptoF.idDireccionSreci = :idDireccionSreci '
                    . 'ORDER BY deptoF.idDeptoFuncional ' )
                    ->setParameter('idDireccionSreci', $idDireccionSreciIn); 
        }else {
            $opt = 2;
            $dql = $em->createQuery('SELECT deptoF.idDeptoFuncional as id , '
                                //. "CONCAT( deptoF.inicialesDeptoFuncional , ' | ', deptoF.descDeptoFuncional) as itemName, "
                                . "deptoF.descDeptoFuncional as itemName, "
                                . 'deptoF.codDeptoFuncional as name, deptoF.inicialesDeptoFuncional as name2 '                                    
                                . 'FROM BackendBundle:TblDepartamentosFuncionales deptoF '
                                . 'INNER JOIN BackendBundle:TblDireccionesSreci dirSreci WITH dirSreci.idDireccionSreci = deptoF.idDireccionSreci '
                                . 'ORDER BY deptoF.idDeptoFuncional ' );                    
        }
       
        // Ejecucion del Query
        $subDireccionesSreci = $dql->getResult();
             
        // Condicion de la Busqueda
        if (count( $subDireccionesSreci ) >= 1 ) {
            $data = array(
                "status" => "success",
                "code"   => 200,
                "optEje" => $opt,
                "data"   => $subDireccionesSreci
            );
        }else {
            $data = array(
                "status" => "error",
                "code"   => 400,
                "msg"    => "No existe Datos en la Tabla de Sub Direcciones de la SRECI, con los Parametros enviados, verifica la "
                . " Información. !!"
            );
        }
        
        return $helpers->parserJson($data);
    }// FIN | FND00001.1
    
    /**
     * @Route("/estados-user-list", name="estados-user-list")
     * Creacion del Controlador: Estados
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00001
     */
    public function estadosUsuarioListAction(Request $request)
    {
        date_default_timezone_set('America/Tegucigalpa');
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
        date_default_timezone_set('America/Tegucigalpa');
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");
        
        $em = $this->getDoctrine()->getManager();
        
        // Query para Obtener todos los Tipo de Funcionario de la SRECI ********
        // de la Tabla: TblTiposFuncionarios ***********************************
        // Incidencia: INC.00004 | Tipos de Funcionarios Desordenados 
        // Fecha : 2017-11-12 | 11:08 am
        // Reportada : Nahum Martinez | Admon. SICDOC
        // INI | NMA | INC.00004
        // Cambiamos el llamada del findAll por findBy con un Array de Ordenamiento
        //$tipoFunc = $em->getRepository("BackendBundle:TblTiposFuncionarios")->findAll();
        $tipoFunc = $em->getRepository("BackendBundle:TblTiposFuncionarios")->findBy(array(),array("descTipoFuncionario" => "ASC")) ;
        // FIN | NMA | INC.00004
        
        
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
        date_default_timezone_set('America/Tegucigalpa');
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
                    ) ,array("idDeptoFuncional" => "ASC") );

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
     * @Route("/com-vinculantes-subdir-sreci-list", name="com-vinculantes-subdir-sreci-list")
     * Creacion del Controlador: Departamentos Funcionales | Comuniacion Vinculante
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00003.1
     */
    public function deptoFuncionalComVinculanteListAction(Request $request)
    {
        date_default_timezone_set('America/Tegucigalpa');
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");
        
        $em = $this->getDoctrine()->getManager();
        
        $json = $request->get("json", null);
        $params = json_decode($json);
        
        //Evaluamos el Json
        if ($json != null) {
            //Variables que vienen del Json ************************************
            ////Recogemos el la Direccion **************************************
            $direccion_sreci    = (isset($params->idDireccionSreciComVinc)) ? $params->idDireccionSreciComVinc : null;
            
            // Query para Obtener todos los Estados de la Tabla: TblEstados
            $tipoFunc = $em->getRepository("BackendBundle:TblDepartamentosFuncionales")->findBy(
                    array(
                        "idDireccionSreci" => $direccion_sreci
                    ) ,array("idDeptoFuncional" => "ASC") );

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
    }//FIN | FND00003.1
    
    
    
    /**
     * @Route("/subdir-sreci-list-all", name="subdir-sreci-list-all")
     * Creacion del Controlador: Departamentos Funcionales All
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00003.1
     */
    public function deptoFuncionalListAllAction(Request $request)
    {
        date_default_timezone_set('America/Tegucigalpa');
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");
        
        $em = $this->getDoctrine()->getManager();
        
        $json = $request->get("json", null);
        $params = json_decode($json);
        
            //Variables que vienen del Json ***********************************************
            //Recogemos el la Direccion ********************************
            $direccion_sreci    = (isset($params->idDireccionSreci)) ? $params->idDireccionSreci : null;
            
            // Query para Obtener todos los Deptos. Funcionales de la  *********
            // Tabla: TblDepartamentosFuncionales ******************************            
            // Incidencia: INC.00006 | Deptos. Funcionales Desordenados
            // Fecha : 2017-11-12 | 11:15 am
            // Reportada : Nahum Martinez | Admon. SICDOC
            // INI | NMA | INC.00006
            // Cambiamos el llamada del findAll por findBy con un Array de Ordenamiento
            // $deptoFunc = $em->getRepository("BackendBundle:TblDepartamentosFuncionales")->findAll();
            $deptoFunc = $em->getRepository("BackendBundle:TblDepartamentosFuncionales")->findBy(array(),array("idDeptoFuncional" => "ASC")) ;
            // FIN | NMA | INC.00006
                        

            // Condicion de la Busqueda
            if (count( $deptoFunc ) >= 1 ) {
                $data = array(
                    "status" => "success",
                    "code"   => 200,
                    "data"   => $deptoFunc
                );
            }else {
                $data = array(
                    "status" => "error",
                    "code"   => 400,
                    "msg"    => "No existe Datos en la Tabla de Sub Direcciones !!"
                );
            }  
        return $helpers->parserJson($data);
    }//FIN | FND00003.1
    
    
    /**
     * @Route("/depto-func-user", name="depto-func-user")
     * Creacion del Controlador: Depto. Funcionales de User
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00003.1
     */
    public function deptoFuncionalUserAction(Request $request)
    {
        date_default_timezone_set('America/Tegucigalpa');
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
        date_default_timezone_set('America/Tegucigalpa');
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
     * @Route("/instituciones-sreci-list", name="/instituciones-sreci-list")
     * Creacion del Controlador: Instituciones
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00005
     */
    public function institucionesSreciAction(Request $request)
    {
        date_default_timezone_set('America/Tegucigalpa');
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
            
            $opt = 0;
            // Evaluamos que Parametro nos enviaron
            if( $pais_institucion != null && $tipo_institucion != null){
                $opt = 1;
                // Query para Obtener todos las Instituciones segun Parametros de la Tabla: TblInstituciones                
                $query = $em->createQuery('SELECT inst.idInstitucion, inst.codInstitucion, '
                                    . 'inst.descInstitucion, inst.perfilInstitucion, '
                                    ."DATE_SUB(inst.fechaIngreso, 0, 'DAY') AS fechaIngreso " 
                                    . 'FROM BackendBundle:TblInstituciones inst '                                    
                                    . 'INNER JOIN BackendBundle:TblPais pa WITH pa.idPais = inst.idPais '
                                    . 'INNER JOIN BackendBundle:TblTipoInstitucion tinst WITH  tinst.idTipoInstitucion = inst.idTipoInstitucion '
                                    . 'INNER JOIN BackendBundle:TblUsuarios user WITH  user.idUsuario = inst.idUsuarioCreador '
                                    . 'WHERE inst.idPais = :idPais AND inst.idTipoInstitucion = :idTipoInstitucion ' 
                                    . 'ORDER BY inst.descInstitucion ASC')
                    ->setParameter('idPais', $pais_institucion)->setParameter('idTipoInstitucion', $tipo_institucion ) ;
                    
                $institucionesSreci = $query->getResult();
                
            }else {
                // Query para Obtener todos las Instituciones de la Tabla: TblInstituciones                    
                // Tabla: TblDepartamentosFuncionales ******************************            
                // Incidencia: INC.00007 | Lista de Instituciones Desordenadas
                // Fecha : 2017-11-12 | 11:20 am
                // Reportada : Nahum Martinez | Admon. SICDOC
                // INI | NMA | INC.00007
                // Cambiamos el llamada del findAll por findBy con un Array de Ordenamiento
                // $institucionesSreci = $em->getRepository("BackendBundle:TblInstituciones")->findAll();
                //$institucionesSreci = $em->getRepository("BackendBundle:TblInstituciones")->findBy(array(),array("descInstitucion" => "ASC")) ;
                $opt = 2;
                
                $query = $em->createQuery('SELECT inst.idInstitucion, inst.codInstitucion, '
                                    . 'inst.descInstitucion, inst.perfilInstitucion, '
                                    ."DATE_SUB(inst.fechaIngreso, 0, 'DAY') AS fechaIngreso " 
                                    . 'FROM BackendBundle:TblInstituciones inst '                                    
                                    . 'INNER JOIN BackendBundle:TblPais pa WITH pa.idPais = inst.idPais '
                                    . 'INNER JOIN BackendBundle:TblTipoInstitucion tinst WITH  tinst.idTipoInstitucion = inst.idTipoInstitucion '
                                    . 'INNER JOIN BackendBundle:TblUsuarios user WITH  user.idUsuario = inst.idUsuarioCreador '
                                    //. 'WHERE c.idEstado IN (3,7,8) AND c.idDeptoFuncional = :idDeptoFuncional AND '
                                    //. 'c.idFuncionarioAsignado = :idFuncionarioAsignado ' 
                                    . 'ORDER BY inst.descInstitucion ASC') ;                    
                    
                $institucionesSreci = $query->getResult();
                
                // FIN | NMA | INC.00007                
            }         
            
            //Total de Instituciones
            $totalInstituciones = count($institucionesSreci);
            
            // Condicion de la Busqueda
            if ( $totalInstituciones  >= 1 ) {
                $data = array(
                    "status" => "success",
                    "code"   => 200,
                    "opt"    => $opt,
                    "recordsTotal" => $totalInstituciones,
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
     * @Route("/tipo-instituciones-sreci-list", name="tipo-instituciones-sreci-list")
     * Creacion del Controlador: Tipo de Instirucion
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00005.1
     */
    public function tipoInstitucionesAction(Request $request)
    {
        date_default_timezone_set('America/Tegucigalpa');
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");
        
        $em = $this->getDoctrine()->getManager();
        
        // Incidencia: INC.00002 | Tipos de Instituciones Desordenadas 
        // Fecha : 2017-11-12 | 10:57 am
        // Reportada : Wendy Flores | Directora DCPI
        // INI | NMA | INC.00002
        // Cambiamos el llamada del findAll por findBy con un Array de Ordenamiento
        //$tipoInstitucion = $em->getRepository("BackendBundle:TblTipoInstitucion")->findAll();
        $tipoInstitucion = $em->getRepository("BackendBundle:TblTipoInstitucion")->findBy(array(),array("descTipoInstitucion" => "ASC")) ;
        // FIN | NMA | INC.00002
                
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
     * @Route("/paises-list", name="paises-list")
     * Creacion del Controlador: Paises
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00006
     */
    public function paisesListAction(Request $request)
    {
        date_default_timezone_set('America/Tegucigalpa');
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");
        
        $em = $this->getDoctrine()->getManager();
        
        
        // Query para Obtener todos los Estados de la Tabla: TblPais
        // Ordenamos el Listado de los Paises de Forma Alfabética de < a >
        // Incidencia: INC.00001 | Paises Desordenados 
        // Fecha : 2017-11-12 | 10:50 am
        // Reportada : Wendy Flores | Directora DCPI
        // INI | NMA | INC.00001
        // Cambiamos el llamada del findAll por findBy con un Array de Ordenamiento
        //$paisList = $em->getRepository("BackendBundle:TblPais")->findAll();
        $paisList = $em->getRepository("BackendBundle:TblPais")->findBy(array(),array("descPais" => "ASC")) ;
        // FIN | NMA | INC.00001
        
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
        date_default_timezone_set('America/Tegucigalpa');
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
                "recordsTotal" => 8,
                "recordsFiltered" => 8,
                "pageLength" => 5,
                "draw" => 8,
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
    public function tipoDocumentoListAction(Request $request, $activo = null )
    {
        date_default_timezone_set('America/Tegucigalpa');
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");
        
        $em = $this->getDoctrine()->getManager();
        
        $opt = 0;
        
        $actDocument = $request->get("activo", null);
        
        //Seleccionamos el Caso de Busqueda
        switch ( $actDocument ){
            case 1:
                    // Query para Obtener todos los Estados de la Tabla: TblTipoDocumento
                    // Con parametro de Tipo de Documento
                    $tipo_documento = $em->getRepository("BackendBundle:TblTipoDocumento")->findBy(
                        array(
                            "activo" => TRUE,
                            "actSalida" => TRUE
                        ),array("idTipoDocumento" => "ASC")
                    );
                $opt = 1;
                break;                
            default :
                    // Query para Obtener todos los Estados de la Tabla: TblTipoDocumento
                    // Sin Parametros
                    $tipo_documento = $em->getRepository("BackendBundle:TblTipoDocumento")->findBy(
                        array(
                            "activo" => TRUE
                        ),array("idTipoDocumento" => "ASC")
                    );
                $opt = 2;
                break;
        } // Fin de Switch
        
        
        // Condicion de la Busqueda
        if (count($tipo_documento) >= 1 ) {
            $data = array(
                "status" => "success",
                "code"   => 200,
                "opt"    => $opt,
                "activo" => $activo,
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
        date_default_timezone_set('America/Tegucigalpa');
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
        date_default_timezone_set('America/Tegucigalpa');
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");
        
        $em = $this->getDoctrine()->getManager();
        
        // Incidencia: INC.00003 | Direcciones SRECI Desordenadas 
        // Fecha : 2017-11-12 | 11:01 am
        // Reportada : Wendy Flores | Directora DCPI
        // INI | NMA | INC.00003
        // Cambiamos el llamada del findAll por findBy con un Array de Ordenamiento
        //$direccion_sreci = $em->getRepository("BackendBundle:TblDireccionesSreci")->findAll();
        $direccion_sreci = $em->getRepository("BackendBundle:TblDireccionesSreci")->findBy(array(),array("descDireccionSreci" => "ASC")) ;
        // FIN | NMA | INC.00003
        
        
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
        date_default_timezone_set('America/Tegucigalpa');
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");
        
        $em = $this->getDoctrine()->getManager();
        
        // Query para Obtener todos los Encabezados de las Correspondencias ****
        // de la Tabla: TblCorrespondenciaEnc **********************************
        // Incidencia: INC.00003 | Correspondencia_Enc Desordenadas 
        // Fecha : 2017-11-12 | 11:03 am
        // Reportada : Wendy Flores | Directora DCPI
        // INI | NMA | INC.00003
        // Cambiamos el llamada del findAll por findBy con un Array de Ordenamiento
        //$com_enc = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findAll();
        $com_enc = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findBy(array(),array("codCorrespondenciaEnc" => "ASC")) ;
        // FIN | NMA | INC.00003
        
        $countAll = count($com_enc);
                
        
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
        //Seteo de variables Globales        
        date_default_timezone_set('America/Tegucigalpa');
        
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
        
        //Variable para veficar la Opcion de las Condiciones
        $opt = 0;
            
        // Parametro de Tipo de Funcionario | Director ID (6)
        $tipo_funcionario_entitie = $tipo_usuario->getIdTipoFuncionario(); // Obtenemos la Entidad Completa
        $tipo_funcionario = $tipo_funcionario_entitie->getIdTipoFuncionario(); // Obtenemos el Tipo de Funcionario
        // Es Director y solo el Puede Asignar Oficios
        if ( $tipo_funcionario === 6 || $tipo_funcionario === 3 ) {        
            // Evaluamos si si hizo una consulta desde la caja Search
            if ( $search != null ) {
               //Consulta con ParamSearch
               $opt = 1;
               
               /**************************************************************** 
                * Query para Obtener los Datos de la Consulta                 **                            
                * Incidencia: INC.00001 | Consulta Lenta | Metodo             **
                * ->createQuery (dql)... No es factible; porque hace varias   **
                * consultas, por las tablas Relacionadas                      **
                * Fecha : 2017-12-27 | 03:08 pm                               **  
                * Reportada : Nahum Martinez | Admon. SICDOC                  **
                * INI | NMA | INC.00001 ***************************************/
                
                $dql = $em->createQuery('SELECT DISTINCT c.idCorrespondenciaEnc, c.codCorrespondenciaEnc, c.codReferenciaSreci, '
                        ."DATE_SUB(c.fechaIngreso, 0, 'DAY') AS fechaIngreso, DATE_SUB(c.fechaMaxEntrega, 0, 'DAY') AS fechaMaxEntrega, "                                    
                        . 'tdoc.descTipoDocumento, '
                        . 'dfunc.idDeptoFuncional, dfunc.descDeptoFuncional, dfunc.inicialesDeptoFuncional, p.idUsuario,'
                        . 'c.descCorrespondenciaEnc, c.temaComunicacion, est.idEstado, est.descripcionEstado, fasig.idFuncionario, '
                        . 'fasig.nombre1Funcionario, fasig.nombre2Funcionario, fasig.apellido1Funcionario, fasig.apellido2Funcionario, '
                        . 'inst.descInstitucion, inst.perfilInstitucion '
                        . 'FROM BackendBundle:TblCorrespondenciaEnc c '                                    
                        . 'INNER JOIN BackendBundle:TblTipoDocumento tdoc WITH  tdoc.idTipoDocumento = c.idTipoDocumento '
                        . 'INNER JOIN BackendBundle:TblDepartamentosFuncionales dfunc WITH  dfunc.idDeptoFuncional = c.idDeptoFuncional '
                        . 'INNER JOIN BackendBundle:TblEstados est WITH  est.idEstado = c.idEstado '
                        . 'INNER JOIN BackendBundle:TblUsuarios p WITH  p.idUsuario = c.idUsuario '
                        . 'INNER JOIN BackendBundle:TblFuncionarios fasig WITH  fasig.idFuncionario = c.idFuncionarioAsignado '
                        . 'INNER JOIN BackendBundle:TblInstituciones inst WITH  inst.idInstitucion = c.idInstitucion '
                        . 'INNER JOIN BackendBundle:TblCorrespondenciaDet d WITH d.idCorrespondenciaEnc = c.idCorrespondenciaEnc '
                        . 'WHERE c.idDeptoFuncional LIKE :search '
                        . 'ORDER BY c.idCorrespondenciaEnc, c.codCorrespondenciaEnc DESC ' ) ;                              

               $query = $em->createQuery($dql)
                        ->setParameter("search", "%$search%");
            }else{
                //Consulta con Parametros de SubDireccion
                $opt = 2;
                                
                $dql = 'SELECT DISTINCT c.idCorrespondenciaEnc, c.codCorrespondenciaEnc, c.codReferenciaSreci, '
                        ."DATE_SUB(c.fechaIngreso, 0, 'DAY') AS fechaIngreso, DATE_SUB(c.fechaMaxEntrega, 0, 'DAY') AS fechaMaxEntrega, "                         
                        . 'dfunc.idDeptoFuncional, dfunc.descDeptoFuncional, dfunc.inicialesDeptoFuncional, '
                        . 'c.descCorrespondenciaEnc, c.temaComunicacion, est.idEstado, est.descripcionEstado, fasig.idFuncionario, '
                        . 'fasig.nombre1Funcionario, fasig.nombre2Funcionario, fasig.apellido1Funcionario, fasig.apellido2Funcionario, '
                        . 'inst.descInstitucion, inst.perfilInstitucion '
                        . 'FROM BackendBundle:TblCorrespondenciaEnc c '                         
                        . 'INNER JOIN BackendBundle:TblDepartamentosFuncionales dfunc WITH  dfunc.idDeptoFuncional = c.idDeptoFuncional '
                        . 'INNER JOIN BackendBundle:TblEstados est WITH  est.idEstado = c.idEstado '                        
                        . 'INNER JOIN BackendBundle:TblFuncionarios fasig WITH  fasig.idFuncionario = c.idFuncionarioAsignado '
                        . 'INNER JOIN BackendBundle:TblInstituciones inst WITH  inst.idInstitucion = c.idInstitucion '                        
                        . 'WHERE c.idDeptoFuncional = '. $idDeptoFuncional .' AND '
                        . "c.idEstado IN (7) "
                        . 'ORDER BY c.idCorrespondenciaEnc, c.codCorrespondenciaEnc DESC ' ;                               

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
                    "opction" => $opt,
                    "total_items_count"   => $total_items_count,
                    "page_actual"   => $page,
                    "items_per_page"   => $item_per_page,
                    "total_page"   => ceil( $total_items_count / $item_per_page ),
                    "data"   => $pagination
                );
        }else{
            //Consulta falsa
            $opt = 3;
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
                    "option" => $opt,
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
        date_default_timezone_set('America/Tegucigalpa');
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");                
        
        //Entity Manager
        $em = $this->getDoctrine()->getManager();
        
        $json = $request->get("json", null);
        $params = json_decode($json);
        
        //Evaluamos el Json
        if ($json != null) {            
            //Variables que vienen del Json ************************************
            //Recogemos el ID del Depto. Funcional *****************************
            $depto_funcional = (isset($params->idDeptoFunc)) ? $params->idDeptoFunc : null; 
            $tipo_usuario = (isset($params->idTipoFuncionarioModal)) ? $params->idTipoFuncionarioModal : null; 
            
            
            //Evaluamos, Si el Tipo de Usuario es Director General, solo vea los
            //Directores Subordinados ( TipoUsuario = 6 )
            if( $tipo_usuario == 5 ){
                // Query para Obtener todos los Funcionarios de la Tabla: TblFuncionarios
                $usuario_asignado = $em->getRepository("BackendBundle:TblFuncionarios")->findBy(
                    array(
                        //"idDeptoFuncional" => $depto_funcional                        
                        "idTipoFuncionario" => [6]                        
                    ));
            }else {
                // Query para Obtener todos los Funcionarios de la Tabla: TblFuncionarios
                $usuario_asignado = $em->getRepository("BackendBundle:TblFuncionarios")->findBy(
                array(
                    "idDeptoFuncional" => $depto_funcional                        
                ));
            }            
            
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
        date_default_timezone_set('America/Tegucigalpa');
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
     * @Route("/funcionarios-list-director", name="funcionarios-list-director")
     * Creacion del Controlador: Funcionarios Tipo Director
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.1
     * Funcion: FND00017.1
     */
    public function funcionariosDirectorListAction(Request $request)
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
            $id_depto_funcional = (isset($params->idDeptoFuncional)) ? $params->idDeptoFuncional : null;
            
            // Query para Obtener el Dato del Funcionario de la Tabla: TblFuncionarios
            $usuario_asignado = $em->getRepository("BackendBundle:TblFuncionarios")->findBy(
                    array(
                        "idDeptoFuncional" => $id_depto_funcional,
                        "idTipoFuncionario" => 6
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
                    "msg"    => "No existe Datos de Director en la Tabla de Funcionarios, comuniquese con el Administrador !!"
                );
            }
        }else {
            $data = array(
                "status" => "error",
                "code"   => 400,
                "msg"    => "Hay un Error con los Parametros enviados, revise la información o comuniquese con el Administrador !!"
            );
        }
               
        return $helpers->parserJson($data);
    }//FIN | FND00017.1
    
    
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
        //Seteo de variables Globales
        ini_set('memory_limit', '512M');
        date_default_timezone_set('America/Tegucigalpa');
        
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
            /*$usuario_asignado = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findBy(
                    array(
                        "idDeptoFuncional"      => $depto_funcional,                        
                        "idFuncionarioAsignado" => $id_funcionario,
                        "idEstado"              => [3,8,7]                        
                        //"idTipoDocumento"       => [1]
                    ), array("idCorrespondenciaEnc" => "ASC", "fechaIngreso" => "ASC") );*/
            $query = $em->createQuery('SELECT c.idCorrespondenciaEnc, c.codCorrespondenciaEnc, c.codReferenciaSreci, '
                                    ."DATE_SUB(c.fechaIngreso, 0, 'DAY') AS fechaIngreso, DATE_SUB(c.fechaMaxEntrega, 0, 'DAY') AS fechaMaxEntrega, "                                    
                                    . 'inst.descInstitucion, inst.perfilInstitucion, tdoc.descTipoDocumento, tdoc.idTipoDocumento, '
                                    . 'tcom.idTipoComunicacion, dfunc.idDeptoFuncional, fasig.idFuncionario, '
                                    . 'fasig.nombre1Funcionario, fasig.nombre2Funcionario, fasig.apellido1Funcionario, fasig.apellido2Funcionario, '
                                    . 'c.descCorrespondenciaEnc, c.temaComunicacion, est.idEstado, est.descripcionEstado '
                                    . 'FROM BackendBundle:TblCorrespondenciaEnc c '
                                    . 'INNER JOIN BackendBundle:TblEstados est WITH  est.idEstado = c.idEstado '
                                    . 'INNER JOIN BackendBundle:TblInstituciones inst WITH  inst.idInstitucion = c.idInstitucion '
                                    . 'INNER JOIN BackendBundle:TblDepartamentosFuncionales dfunc WITH  dfunc.idDeptoFuncional = c.idDeptoFuncional '
                                    . 'INNER JOIN BackendBundle:TblTipoDocumento tdoc WITH  tdoc.idTipoDocumento = c.idTipoDocumento '
                                    . 'INNER JOIN BackendBundle:TblDepartamentosFuncionales dep WITH dep.idDeptoFuncional = c.idDeptoFuncional '
                                    . 'INNER JOIN BackendBundle:TblFuncionarios func WITH func.idFuncionario = c.idFuncionarioAsignado '
                                    . 'INNER JOIN BackendBundle:TblTipoComunicacion tcom WITH tcom.idTipoComunicacion = c.idTipoComunicacion '
                                    . 'INNER JOIN BackendBundle:TblFuncionarios fasig WITH  fasig.idFuncionario = c.idFuncionarioAsignado '
                                    . 'WHERE c.idEstado IN (3,7,8) AND c.idDeptoFuncional = :idDeptoFuncional AND '
                                    . 'c.idFuncionarioAsignado = :idFuncionarioAsignado ' 
                                    . 'ORDER BY c.codCorrespondenciaEnc, c.idCorrespondenciaEnc ASC') 
                    ->setParameter('idDeptoFuncional', $depto_funcional)->setParameter('idFuncionarioAsignado', $id_funcionario ) ;
                    
            $usuario_asignado = $query->getResult();
            
            //Total de Resgitros por la Consulta
            $total_consulta = count( $usuario_asignado );
            // Condicion de la Busqueda
            if ( $total_consulta >= 1 ) {
                $data = array(
                    "status" => "success",
                    "code"   => 200,
                    "recordsTotal"  => $total_consulta,
                    "data"   => $usuario_asignado
                );
            }else {
                $data = array(
                    "status" => "error",
                    "code"   => 400,
                    "msg"    => "No existe Datos de Comunicacion Asignada en la Tabla de Correspondencia para usted !!"
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
        date_default_timezone_set('America/Tegucigalpa');
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
                    "msg"    => "No existe Datos de Comunicacion en ese Estado !!"
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
    
    
    
    /**
     * @Route("listas/funcionarios-list-all", name="listas/funcionarios-list-all")
     * Creacion del Controlador: Funcionarios Listado Completo
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00020
     */
    public function funcionariosListAllAction(Request $request)
    {
        date_default_timezone_set('America/Tegucigalpa');
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
            
            // Query para Obtener todos los Funcionarios de la Tabla: TblFuncionarios           
            // de la Tabla: TblTiposFuncionarios *******************************
            // Incidencia: INC.00005 | Funcionarios Desordenados ? Vaidar
            // Fecha : 2017-11-12 | 11:08 am
            // Reportada : Nahum Martinez | Admon. SICDOC
            // INI | NMA | INC.00005
            // Cambiamos el llamada del findAll por findBy con un Array de Ordenamiento
            $funcionario_all = $em->getRepository("BackendBundle:TblFuncionarios")->findAll();
            //$funcionario_all = $em->getRepository("BackendBundle:TblTiposFuncionarios")->findBy(array(),array("nombre1Funcionario" => "ASC")) ;
            // FIN | NMA | INC.00005
                       

            // Condicion de la Busqueda
            if (count( $funcionario_all ) >= 1 ) {
                $data = array(
                    "status" => "success",
                    "code"   => 200,
                    "data"   => $funcionario_all
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
    }//FIN | FND00020
    
    
    
    /**
     * @Route("listas/funcionarios-list-all-component", name="listas/funcionarios-list-all-component")
     * Creacion del Controlador: Funcionarios Listado Completo para el Componente
     * Selecter de Angular
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00021
     */
    public function funcionariosListAllComponentAction(Request $request)
    {
        date_default_timezone_set('America/Tegucigalpa');
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
            
            // Query para Obtener todos los Funcionarios de la Tabla: TblFuncionarios           
            // de la Tabla: TblTiposFuncionarios *******************************                       
            //$funcionario_all = $em->getRepository("BackendBundle:TblFuncionarios")->findAll();            
            // FIN | NMA | INC.00005
            
            $dql = $em->createQuery('SELECT funcAll.idFuncionario as id , '                                
                                //. "funcAll.apellido1Funcionario as itemName, "
                                . "CONCAT( funcAll.nombre1Funcionario , ' | ', funcAll.apellido1Funcionario) as itemName, "
                                . 'funcAll.apellido2Funcionario as name, funcAll.nombre1Funcionario as name2 '                                    
                                . 'FROM BackendBundle:TblFuncionarios funcAll '
                                //. 'INNER JOIN BackendBundle:TblDireccionesSreci dirSreci WITH dirSreci.idDireccionSreci = deptoF.idDireccionSreci '
                                . 'ORDER BY funcAll.idFuncionario ' );                    
       
            // Ejecucion del Query
            $funcionario_all = $dql->getResult();
                      

            // Condicion de la Busqueda
            if (count( $funcionario_all ) >= 1 ) {
                $data = array(
                    "status" => "success",
                    "code"   => 200,
                    "data"   => $funcionario_all
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
    }//FIN | FND00021
    
    
}

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
     * @Route("/depto-funcional-list", name="depto-funcional-list")
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
        
        // Query para Obtener todos los Estados de la Tabla: TblEstados
        $tipoFunc = $em->getRepository("BackendBundle:TblDepartamentosFuncionales")->findBy(
                array(
                    "idDireccionSreci" => 1
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
                "msg"    => "No existe Datos en la Tabla de Departamentos Funcionales !!"
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
    
    
    
    
    
    
    
}

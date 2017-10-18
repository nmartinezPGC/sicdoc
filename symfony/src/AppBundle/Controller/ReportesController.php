<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\JsonResponse;

// Funciones de Doctrine
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

//Importamos las Tablas a Relacionar
use BackendBundle\Entity\TblCorrespondenciaEnc;
use BackendBundle\Entity\TblCorrespondenciaDet;
use BackendBundle\Entity\TblUsuarios;
use BackendBundle\Entity\TblDocumentos;
use BackendBundle\Entity\TblInstituciones;
use BackendBundle\Entity\TblEstados;
use BackendBundle\Entity\TblDireccionesSreci;
use BackendBundle\Entity\TblSecuenciales;
use Swift_MessageAcceptanceTest;

/**
 * Description of ReportesController
 * Objetivo: Obtener los Reportesa a la BD
 * @author Nahum Martinez
 */
class ReportesController extends Controller{
    
    /**
     * @Route("/reporte-general", name="reporte-general")
     * Creacion del Controlador: Busqueda de Reporte General
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00001
     * Descripcion: Consulta que muestra la informacion general de la Comunicacion
     *              atraves de Parametros de Search
     * @param array $estados, $tipos Filtros de Estados a Buscar
     */
    public function reporteGeneralDetListAction(Request $request )
    {
        //Instanciamos el Servicio Helpers
        $helpers = $this->get("app.helpers");
        //Recoger el Hash
        //Recogemos el Hash y la Autorizacion del Mismo        
        $hash = $request->get("authorization", null);
        //Se Chekea el Token
        $checkToken = $helpers->authCheck($hash);
        
        //Evalua que el Token sea True
        if($checkToken == true){
            // Variable del LocalStorage
            $identity = $helpers->authCheck($hash, true);
            
            //Convertimos los Parametros POSt a Json
            $json = $request->get("json", null);
           
            //Comprobamos que Json no es Null
            if ($json != null) {
                //Decodificamos el Json
                $params = json_decode($json);

                //Parametros a Convertir                           
                //Datos generales de la Tabla
                $id_funcionario_asignado   = ($params->idFuncionarioAsignado != null) ? $params->idFuncionarioAsignado : 0;
                $id_sub_direccion          = ($params->idDireccion != null) ? $params->idDireccion : 0;                
                
                // Array de Tipo de Comunicacion
                $array_tipo_comunicacion   = ($params->idTipoComunicacion != null) ? $params->idTipoComunicacion : 0 ;
                //echo $array_tipo_comunicacion;
                // Array de Estados de Comunicacion
                $array_estado_comunicacion = ($params->idEstadoComunicacion != null) ? $params->idEstadoComunicacion : 0 ;
                
                // Fechas de Reporte
                $fecha_modifcacion         = new \DateTime('now');
                $fecha_final             = ($params->fechaFinal != null) ? $params->fechaFinal : null;
                $fecha_inicial               = ($params->fechaInicial != null) ? $params->fechaInicial : null;
                
                // Se convierte el Array en String
                $estados_array_convert      = implode(",", $array_estado_comunicacion);
                $funcionarios_array_convert = implode(",", $array_tipo_comunicacion);
                
                //Evaluamos que los Campos del Json  no sean Null ni 0. ********
                if( $fecha_inicial != null && $fecha_final != null )
                {                    
                    // Creacion del Metodo Create Query Builder | hace mas Efectiva la *****
                    // Busqueda a la BD  ***************************************************                    
                                        
                    $em = $this->getDoctrine()->getManager();
                    
                    // Opcion de las Condiciones de Seleccion
                    $opt;
                    // INI | Eval Params
                    //Evaluamos el Tipo de Query a Eejecutar
                    // Lanzamos el Query, con todos los Parametros *************
                    if( $id_sub_direccion != 0 && $id_funcionario_asignado != 0 ){                        
                        $opt = "1";
                        $query = $em->createQuery(
                            "SELECT p
                            FROM BackendBundle:TblCorrespondenciaEnc p
                            WHERE p.idEstado IN (".$estados_array_convert.") AND 
                                  p.idTipoDocumento IN (".$funcionarios_array_convert.") AND 
                                  p.fechaIngreso >= '".$fecha_inicial."' AND 
                                  p.fechaIngreso <= '".$fecha_inicial."' AND                             
                                  p.idDeptoFuncional = '".$id_sub_direccion."' AND 
                                  p.idFuncionarioAsignado = '".$id_funcionario_asignado."'  
                            ORDER BY p.idEstado ASC"
                        );
                    } else if ( $id_sub_direccion != 0 && $id_funcionario_asignado == 0 ) {                        
                        $opt = "2";
                        $query = $em->createQuery(
                            "SELECT p
                            FROM BackendBundle:TblCorrespondenciaEnc p
                            WHERE p.idEstado IN (".$estados_array_convert.") AND 
                                  p.idTipoDocumento IN (".$funcionarios_array_convert.") AND 
                                  p.fechaIngreso >= '".$fecha_inicial."' AND 
                                  p.fechaIngreso <= '".$fecha_inicial."' AND                             
                                  p.idDeptoFuncional = '4'                                  
                            ORDER BY p.idEstado ASC"
                        );
                    } else if ( $id_sub_direccion == 0 && $id_funcionario_asignado != 0 ) {                        
                        $opt = "3";
                        $query = $em->createQuery(
                            "SELECT p
                            FROM BackendBundle:TblCorrespondenciaEnc p
                            WHERE p.idEstado IN (".$estados_array_convert.") AND 
                                  p.idTipoDocumento IN (".$funcionarios_array_convert.") AND 
                                  p.fechaIngreso >= '".$fecha_inicial."' AND 
                                  p.fechaIngreso <= '".$fecha_inicial."' AND                             
                                  p.idFuncionarioAsignado = '".$id_funcionario_asignado."'                                  
                            ORDER BY p.idEstado ASC"
                        );
                    } else if ( $id_sub_direccion == 0 && $id_funcionario_asignado == 0 ){                        
                        $opt = "4";
                        
                        $query = $em->createQuery(
                            "SELECT p
                            FROM BackendBundle:TblCorrespondenciaEnc p
                            WHERE p.idEstado IN (".$estados_array_convert.") AND 
                                  p.idTipoDocumento IN (".$funcionarios_array_convert.") AND 
                                  p.fechaIngreso >= '".$fecha_inicial."' AND 
                                  p.fechaIngreso <= '".$fecha_final."' 
                            ORDER BY p.idEstado ASC"
                        );
                    } // FIN | Eval Params
                                                                    
                    
                    // ->setParameter('idEstado', $resultado2 );    
                    $correspondenciaFind = $query->getResult();                                  
                    
                    // Evalua el Resultado de la Consulta
                    $count_rest = count($correspondenciaFind); 
                    //Verificamos que el retorno de la Funcion sea = 0 ********* 
                    if( $count_rest > 0 ){
                        //Array de Mensajes
                        $data = array(
                            "status" => "success", 
                            "code"   => 200,
                            "val"    => $opt,
                            "func" => $id_funcionario_asignado,
                            "dir" => $id_sub_direccion,
                            "total"  => $count_rest,
                            "msg"    => "Se ha encontrado la Informacion solicitada",
                            "data"   => $correspondenciaFind
                        );                        
                    } else{
                        $data = array(
                            "status" => "error",                            
                            "code"   => 404,
                            "val"    => $opt,
                            "func" => $id_funcionario_asignado,
                            "dir" => $id_sub_direccion,                            
                            "msg"   => "Error al buscar, no existe registros con esta informacion, ".
                                       " por favor, revise los parametros a enviar !!"
                        );                       
                    } // Fin de Busqueda de la Comunicacion
                } else{
                        $data = array(
                            "status" => "error",
                            "desc"   => "Falta Informacion",
                            "code"   => 400, 
                            "msg"   => "Falta Ingresar información para poder continuar, "
                                    . "revise las fechas de generacion del reporte !!"
                        );                       
                }//Finaliza el Bloque de la validadacion de la Data en la Tabla
            } else {
                    //Array de Mensajes | Campos que Faltan del Json
                    $data = array(
                       "status" => "error",
                       "desc"   => "Eror al Enviar el Json, el Json no ha sido enviado",
                       "code"   => 400, 
                       "msg"   => "Informacion no encontrada, falta ingresar parametros, "
                                . "revisar la información ingresada !!"
                    );
            } // Evalua el Json que no sea Null            
        } else {
            $data = array(
                "status" => "error",
                "desc"   => "El Token, es invalido",    
                "code" => 404,                
                "msg" => "Autorizacion de Token no valida !!"                
            );
        } // FIN | Token
        //Retorno de la Funcion ************************************************
        return $helpers->parserJson($data);
    }//FIN | FND00001
    
}
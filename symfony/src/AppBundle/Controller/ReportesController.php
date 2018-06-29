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
        //Seteo de variables Globales
        //ini_set('memory_limit', '512M');
        date_default_timezone_set('America/Tegucigalpa');
        
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
                               
                // Array de Metogologia Comunicacion (Ingreso / Salida)
                $array_method_comunicacion = ($params->idComunicacion != null) ? $params->idComunicacion : 0 ;
                
                // Fechas de Reporte
                $fecha_modifcacion         = new \DateTime('now');
                $fecha_final             = ($params->fechaFinal != null) ? $params->fechaFinal : null;
                $fecha_inicial               = ($params->fechaInicial != null) ? $params->fechaInicial : null;
                
                // Se convierte el Array en String
                $estados_array_convert      = implode(",", $array_estado_comunicacion );
                $funcionarios_array_convert = implode(",", $array_tipo_comunicacion );
                $method_array_convert       = implode(",", $array_method_comunicacion );
                
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
                        /******************************************************* 
                        * Query para Obtener los Datos de la Consulta          *
                        * Incidencia: INC.00001 | Consulta Lenta | Metodo      *
                        * ->createQuery ... No es factible; porque hace varias *
                        * consultas, por las tablas Relacionadas               *
                        * Fecha : 2017-12-26 | 4:40 pm                         * 
                        * Reportada : Nahum Martinez | Admon. SICDOC           *
                        * INI | NMA | INC.00001 ********************************/
                        $opt = "1";                                              
                        
                        /***** Nuevo Metodo de Consulta a BD | Opcion #2 *******
                         *Consulta de Registros con Fechas, Arrays y Funcion. y*
                         * Sub Direccion                                       * 
                         *Params: Fechas, Arrays(Estados, Tipos), Funcionario  *           
                         ******************************************************/
                        $query = $em->createQuery('SELECT c.idCorrespondenciaEnc, c.codCorrespondenciaEnc, c.codReferenciaSreci, '
                                    . "DATE_SUB(c.fechaIngreso, 0, 'DAY') AS fechaIngreso, DATE_SUB(c.fechaFinalizacion, 0, 'DAY') AS fechaFinalizacion, "
                                    . "DATE_SUB(c.fechaMaxEntrega, 0, 'DAY') AS fechaMaxEntrega, "                                    
                                    . 'tcom.descTipoComunicacion, tcom.idTipoComunicacion, tdoc.descTipoDocumento, tdoc.idTipoDocumento, '
                                    . 'dfunc.idDeptoFuncional, dfunc.descDeptoFuncional, dfunc.inicialesDeptoFuncional,  '
                                    . 'fasig.idFuncionario, fasig.nombre1Funcionario, fasig.nombre2Funcionario, fasig.apellido1Funcionario, fasig.apellido2Funcionario, '
                                    . 'c.descCorrespondenciaEnc, c.temaComunicacion, inst.descInstitucion, inst.perfilInstitucion, est.idEstado, est.descripcionEstado '
                                    . 'FROM BackendBundle:TblCorrespondenciaEnc c '
                                    . 'INNER JOIN BackendBundle:TblEstados est WITH  est.idEstado = c.idEstado '
                                    . 'INNER JOIN BackendBundle:TblInstituciones inst WITH  inst.idInstitucion = c.idInstitucion '
                                    . 'INNER JOIN BackendBundle:TblDepartamentosFuncionales dfunc WITH  dfunc.idDeptoFuncional = c.idDeptoFuncional '                                    
                                    . 'INNER JOIN BackendBundle:TblDepartamentosFuncionales dep WITH dep.idDeptoFuncional = c.idDeptoFuncional '
                                    . 'INNER JOIN BackendBundle:TblFuncionarios func WITH func.idFuncionario = c.idFuncionarioAsignado '
                                    . 'INNER JOIN BackendBundle:TblTipoDocumento tdoc WITH  tdoc.idTipoDocumento = c.idTipoDocumento '
                                    . 'INNER JOIN BackendBundle:TblTipoComunicacion tcom WITH tcom.idTipoComunicacion = c.idTipoComunicacion '
                                    . 'INNER JOIN BackendBundle:TblFuncionarios fasig WITH  fasig.idFuncionario = c.idFuncionarioAsignado '
                                    . 'WHERE c.idEstado IN ('.$estados_array_convert.') AND '
                                    . 'c.idTipoDocumento IN ('.$funcionarios_array_convert.') AND '
                                    . 'c.idTipoComunicacion IN ('.$method_array_convert.') AND '
                                    . "c.fechaIngreso >= '".$fecha_inicial."' AND "
                                    . "c.fechaIngreso <= '".$fecha_final."' AND "                           
                                    . 'c.idDeptoFuncional = '.$id_sub_direccion.' AND ' 
                                    . 'c.idFuncionarioAsignado = '.$id_funcionario_asignado.' '
                                    . 'ORDER BY c.codCorrespondenciaEnc, c.idCorrespondenciaEnc ASC') ;                                                            
                    
                        //$correspondenciaFind = $query->getResult();
                        
                        
                        /*$query = $em->createQuery(
                            "SELECT p
                            FROM BackendBundle:TblCorrespondenciaEnc p
                            WHERE p.idEstado IN (".$estados_array_convert.") AND 
                                  p.idTipoDocumento IN (".$funcionarios_array_convert.") AND 
                                  p.fechaIngreso >= '".$fecha_inicial."' AND 
                                  p.fechaIngreso <= '".$fecha_inicial."' AND                             
                                  p.idDeptoFuncional = '".$id_sub_direccion."' AND 
                                  p.idFuncionarioAsignado = '".$id_funcionario_asignado."'  
                            ORDER BY p.idEstado ASC"
                        );*/
                        
                    } else if ( $id_sub_direccion != 0 && $id_funcionario_asignado == 0 ) {                        
                        //Todos Los Parametros de la Consulta
                        $opt = "222";
                        
                        /***** Nuevo Metodo de Consulta a BD | Opcion #2 *******
                         *Consulta de Registros con Fechas, Arrays y Funcion. y*
                         * Sub Direccion                                       * 
                         *Params: Fechas, Arrays(Estados, Tipos), Funcionario  *           
                         ******************************************************/
                        $query = $em->createQuery('SELECT c.idCorrespondenciaEnc, c.codCorrespondenciaEnc, c.codReferenciaSreci, '
                                    ."DATE_SUB(c.fechaIngreso, 0, 'DAY') AS fechaIngreso, DATE_SUB(c.fechaFinalizacion, 0, 'DAY') AS fechaFinalizacion, "
                                    . "DATE_SUB(c.fechaMaxEntrega, 0, 'DAY') AS fechaMaxEntrega, "
                                    . 'tcom.descTipoComunicacion, tcom.idTipoComunicacion, tdoc.descTipoDocumento, tdoc.idTipoDocumento, '
                                    . 'dfunc.idDeptoFuncional, dfunc.descDeptoFuncional, dfunc.inicialesDeptoFuncional,  '
                                    . 'fasig.idFuncionario, fasig.nombre1Funcionario, fasig.nombre2Funcionario, fasig.apellido1Funcionario, fasig.apellido2Funcionario, '
                                    . 'c.descCorrespondenciaEnc, c.temaComunicacion, inst.descInstitucion, inst.perfilInstitucion, est.idEstado, est.descripcionEstado '
                                    . 'FROM BackendBundle:TblCorrespondenciaEnc c '
                                    . 'INNER JOIN BackendBundle:TblEstados est WITH  est.idEstado = c.idEstado '
                                    . 'INNER JOIN BackendBundle:TblInstituciones inst WITH  inst.idInstitucion = c.idInstitucion '
                                    . 'INNER JOIN BackendBundle:TblDepartamentosFuncionales dfunc WITH  dfunc.idDeptoFuncional = c.idDeptoFuncional '                                    
                                    . 'INNER JOIN BackendBundle:TblDepartamentosFuncionales dep WITH dep.idDeptoFuncional = c.idDeptoFuncional '
                                    . 'INNER JOIN BackendBundle:TblFuncionarios func WITH func.idFuncionario = c.idFuncionarioAsignado '
                                    . 'INNER JOIN BackendBundle:TblTipoDocumento tdoc WITH  tdoc.idTipoDocumento = c.idTipoDocumento '
                                    . 'INNER JOIN BackendBundle:TblTipoComunicacion tcom WITH tcom.idTipoComunicacion = c.idTipoComunicacion '
                                    . 'INNER JOIN BackendBundle:TblFuncionarios fasig WITH  fasig.idFuncionario = c.idFuncionarioAsignado '
                                    . 'WHERE c.idEstado IN ('.$estados_array_convert.') AND '
                                    . 'c.idTipoDocumento IN ('.$funcionarios_array_convert.') AND '
                                    . 'c.idTipoComunicacion IN ('.$method_array_convert.') AND '
                                    . "c.fechaIngreso >= '".$fecha_inicial."' AND "
                                    . "c.fechaIngreso <= '".$fecha_final."' AND "                                    
                                    . 'c.idDeptoFuncional = '.$id_sub_direccion.' '
                                    . 'ORDER BY  c.idCorrespondenciaEnc, c.codCorrespondenciaEnc DESC ') ;   
                    
                        //$correspondenciaFind = $query->getResult();
                        
                        /*$query = $em->createQuery(
                            "SELECT p
                            FROM BackendBundle:TblCorrespondenciaEnc p
                            WHERE p.idEstado IN (".$estados_array_convert.") AND 
                                  p.idTipoDocumento IN (".$funcionarios_array_convert.") AND 
                                  p.fechaIngreso >= '".$fecha_inicial."' AND 
                                  p.fechaIngreso <= '".$fecha_inicial."' AND                             
                                  p.idDeptoFuncional = '".$id_sub_direccion."'                                  
                            ORDER BY p.idEstado ASC"
                        );*/
                    } else if ( $id_sub_direccion == 0 && $id_funcionario_asignado != 0 ) {                        
                        //3 parametros de la Consulta
                        $opt = "3";
                        
                        /***** Nuevo Metodo de Consulta a BD | Opcion #3 *******
                         *Consulta de Registros con Fechas, Arrays y Funcion.  * 
                         *Params: Fechas, Arrays(Estados, Tipos), Funcionario  *           
                         ******************************************************/
                        $query = $em->createQuery('SELECT c.idCorrespondenciaEnc, c.codCorrespondenciaEnc, c.codReferenciaSreci, '
                                    ."DATE_SUB(c.fechaIngreso, 0, 'DAY') AS fechaIngreso, DATE_SUB(c.fechaFinalizacion, 0, 'DAY') AS fechaFinalizacion, "
                                    . "DATE_SUB(c.fechaMaxEntrega, 0, 'DAY') AS fechaMaxEntrega, "
                                    . 'tcom.descTipoComunicacion, tcom.idTipoComunicacion, tdoc.descTipoDocumento, tdoc.idTipoDocumento, '
                                    . 'dfunc.idDeptoFuncional, dfunc.descDeptoFuncional, dfunc.inicialesDeptoFuncional,  '
                                    . 'fasig.idFuncionario, fasig.nombre1Funcionario, fasig.nombre2Funcionario, fasig.apellido1Funcionario, fasig.apellido2Funcionario, '
                                    . 'c.descCorrespondenciaEnc, c.temaComunicacion, inst.descInstitucion, inst.perfilInstitucion, est.idEstado, est.descripcionEstado '
                                    . 'FROM BackendBundle:TblCorrespondenciaEnc c '
                                    . 'INNER JOIN BackendBundle:TblEstados est WITH  est.idEstado = c.idEstado '
                                    . 'INNER JOIN BackendBundle:TblInstituciones inst WITH  inst.idInstitucion = c.idInstitucion '
                                    . 'INNER JOIN BackendBundle:TblDepartamentosFuncionales dfunc WITH  dfunc.idDeptoFuncional = c.idDeptoFuncional '                                    
                                    . 'INNER JOIN BackendBundle:TblDepartamentosFuncionales dep WITH dep.idDeptoFuncional = c.idDeptoFuncional '
                                    . 'INNER JOIN BackendBundle:TblFuncionarios func WITH func.idFuncionario = c.idFuncionarioAsignado '
                                    . 'INNER JOIN BackendBundle:TblTipoDocumento tdoc WITH  tdoc.idTipoDocumento = c.idTipoDocumento '
                                    . 'INNER JOIN BackendBundle:TblTipoComunicacion tcom WITH tcom.idTipoComunicacion = c.idTipoComunicacion '
                                    . 'INNER JOIN BackendBundle:TblFuncionarios fasig WITH  fasig.idFuncionario = c.idFuncionarioAsignado '
                                    . 'WHERE c.idEstado IN ('.$estados_array_convert.') AND '
                                    . 'c.idTipoDocumento IN ('.$funcionarios_array_convert.') AND '
                                    . 'c.idTipoComunicacion IN ('.$method_array_convert.') AND '
                                    . "c.fechaIngreso >= '".$fecha_inicial."' AND "
                                    . "c.fechaIngreso <= '".$fecha_final."' AND "
                                    . "c.idFuncionarioAsignado = '".$id_funcionario_asignado."' "
                                    . 'ORDER BY c.idCorrespondenciaEnc, c.codCorrespondenciaEnc ASC') ;                                                            
                    
                        //$correspondenciaFind = $query->getResult();
                        
                        /*$query = $em->createQuery(
                            "SELECT p
                            FROM BackendBundle:TblCorrespondenciaEnc p
                            WHERE p.idEstado IN (".$estados_array_convert.") AND 
                                  p.idTipoDocumento IN (".$funcionarios_array_convert.") AND 
                                  p.fechaIngreso >= '".$fecha_inicial."' AND 
                                  p.fechaIngreso <= '".$fecha_inicial."' AND                             
                                  p.idFuncionarioAsignado = '".$id_funcionario_asignado."'                                  
                            ORDER BY p.idEstado ASC"
                        );*/
                        
                    } else if ( $id_sub_direccion == 0 && $id_funcionario_asignado == 0 ){                        
                        //2 Parametros de la Consulta
                        $opt = "4";                        
                        
                        /***** Nuevo Metodo de Consulta a BD | Opcion #4 *******
                         *Consulta de Registros con Fechas y Arrays            * 
                         *Params: Fechas, Arrays(Estados, Tipos)               *           
                         ******************************************************/
                        $query = $em->createQuery('SELECT c.idCorrespondenciaEnc, c.codCorrespondenciaEnc, c.codReferenciaSreci, '
                                    ."DATE_SUB(c.fechaIngreso, 0, 'DAY') AS fechaIngreso, DATE_SUB(c.fechaFinalizacion, 0, 'DAY') AS fechaFinalizacion, "
                                    . "DATE_SUB(c.fechaMaxEntrega, 0, 'DAY') AS fechaMaxEntrega, "
                                    . 'tcom.descTipoComunicacion, tcom.idTipoComunicacion, tdoc.descTipoDocumento, tdoc.idTipoDocumento, '
                                    . 'dfunc.idDeptoFuncional, dfunc.descDeptoFuncional, dfunc.inicialesDeptoFuncional,  '
                                    . 'fasig.idFuncionario, fasig.nombre1Funcionario, fasig.nombre2Funcionario, fasig.apellido1Funcionario, fasig.apellido2Funcionario, '
                                    . 'c.descCorrespondenciaEnc, c.temaComunicacion, inst.descInstitucion, inst.perfilInstitucion, est.idEstado, est.descripcionEstado '
                                    . 'FROM BackendBundle:TblCorrespondenciaEnc c '
                                    . 'INNER JOIN BackendBundle:TblEstados est WITH  est.idEstado = c.idEstado '
                                    . 'INNER JOIN BackendBundle:TblInstituciones inst WITH  inst.idInstitucion = c.idInstitucion '
                                    . 'INNER JOIN BackendBundle:TblDepartamentosFuncionales dfunc WITH  dfunc.idDeptoFuncional = c.idDeptoFuncional '                                    
                                    . 'INNER JOIN BackendBundle:TblDepartamentosFuncionales dep WITH dep.idDeptoFuncional = c.idDeptoFuncional '
                                    . 'INNER JOIN BackendBundle:TblFuncionarios func WITH func.idFuncionario = c.idFuncionarioAsignado '
                                    . 'INNER JOIN BackendBundle:TblTipoDocumento tdoc WITH  tdoc.idTipoDocumento = c.idTipoDocumento '
                                    . 'INNER JOIN BackendBundle:TblTipoComunicacion tcom WITH tcom.idTipoComunicacion = c.idTipoComunicacion '
                                    . 'INNER JOIN BackendBundle:TblFuncionarios fasig WITH  fasig.idFuncionario = c.idFuncionarioAsignado '
                                    . 'WHERE c.idEstado IN ('.$estados_array_convert.') AND '
                                    . 'c.idTipoDocumento IN ('.$funcionarios_array_convert.') AND '
                                    . 'c.idTipoComunicacion IN ('.$method_array_convert.') AND '
                                    . "c.fechaIngreso >= '".$fecha_inicial."' AND "
                                    . "c.fechaIngreso <= '".$fecha_final."' "                                    
                                    . 'ORDER BY  c.idCorrespondenciaEnc, c.codCorrespondenciaEnc ASC') ;                                                            
                    
                        //$usuario_asignado = $query->getResult();
                        
                        /*$query = $em->createQuery(
                            "SELECT p
                            FROM BackendBundle:TblCorrespondenciaEnc p
                            WHERE p.idEstado IN (".$estados_array_convert.") AND 
                                  p.idTipoDocumento IN (".$funcionarios_array_convert.") AND 
                                  p.fechaIngreso >= '".$fecha_inicial."' AND 
                                  p.fechaIngreso <= '".$fecha_final."' 
                            ORDER BY p.idEstado ASC"
                        );*/
                        
                        // FIN | NMA | INC.00001
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
                            "option" => $opt,
                            "idFuncionarioAsignado"  => $id_funcionario_asignado,
                            "idDeptoFuncional"       => $id_sub_direccion,
                            "recordsTotal"  => $count_rest,
                            "msg"    => "Se ha encontrado la Informacion solicitada",
                            "data"   => $correspondenciaFind
                        );                        
                    } else{
                        $data = array(
                            "status" => "error",                            
                            "code"   => 504,
                            "option" => $opt,
                            "idFuncionarioAsignado" => $id_funcionario_asignado,
                            "idDeptoFuncional"      => $id_sub_direccion,                            
                            "msg"   => "Error al buscar, no existe registros con esta informacion, ".
                                       " por favor, revise los parametros a enviar !!"
                        );                       
                    } // Fin de Busqueda de la Comunicacion
                } else{
                        $data = array(
                            "status" => "error",
                            "desc"   => "Falta Informacion",
                            "code"   => 500, 
                            "msg"   => "Falta Ingresar información para poder continuar, "
                                    . "revise las fechas de generacion del reporte !!"
                        );                       
                }//Finaliza el Bloque de la validadacion de la Data en la Tabla
            } else {
                    //Array de Mensajes | Campos que Faltan del Json
                    $data = array(
                       "status" => "error",
                       "desc"   => "Eror al Enviar el Json, el Json no ha sido enviado",
                       "code"   => 500, 
                       "msg"   => "Informacion no encontrada, falta ingresar parametros, "
                                . "revisar la información ingresada !!"
                    );
            } // Evalua el Json que no sea Null            
        } else {
            $data = array(
                "status" => "error",
                "desc"   => "El Token, es invalido",    
                "code" => 501,                
                "msg" => "Autorizacion de Token no valida !!"                
            );
        } // FIN | Token
        //Retorno de la Funcion ************************************************
        return $helpers->parserJson($data);
    }//FIN | FND00001
 
    
    /**
     * @Route("/reporte-resumido", name="reporte-resumido")
     * Creacion del Controlador: Busqueda de Reporte Resumido
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00002
     * Descripcion: Consulta que muestra la informacion general de la Comunicacion
     *              atraves de Parametros de Search
     * @param filtro de Fechas a Buscar
     */
    public function reporteResumidoListAction(Request $request) 
    {
        //Seteo de variables Globales        
        date_default_timezone_set('America/Tegucigalpa');
        
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
                    // Fechas de Reporte
                    $fecha_modifcacion  = new \DateTime('now');
                    $fecha_final        = ($params->fechaFinal != null) ? $params->fechaFinal : null;
                    $fecha_inicial      = ($params->fechaInicial != null) ? $params->fechaInicial : null;
                    
                    $direccion_user     = $identity->idDireccion;
                    $depto_func_user    = $identity->idDeptoFuncional;
                    $tipo_user          = $identity->idTipoUser;
                    $tipo_func          = $identity->idTipoFunc;

                    //Evaluamos que los Campos del Json  no sean Null ni 0. ********
                    if( $fecha_inicial != null && $fecha_final != null )
                    {                    
                        // Creacion del Metodo Create Query Builder | hace mas Efectiva la *****
                        // Busqueda a la BD  ***************************************************                  

                        $em = $this->getDoctrine()->getManager();

                        // Opcion de las Condiciones de Seleccion
                        $opt;
                        // INI | Eval Params
                        // Evaluamos el Tipo de Query a Eejecutar
                        // Lanzamos el Query, con todos los Parametros *************
                        if( ($direccion_user != 0 && $tipo_user == 5) || ($direccion_user != 0 && $tipo_user == 1) ){                         
                            $opt = "1";
                            $query = $em->createQuery('SELECT c.idCorrespondenciaEnc, c.codCorrespondenciaEnc, c.codReferenciaSreci, '
                                        . "DATE_SUB(c.fechaIngreso, 0, 'DAY') AS fechaIngreso, DATE_SUB(c.fechaFinalizacion, 0, 'DAY') AS fechaFinalizacion, "
                                        . "DATE_SUB(c.fechaMaxEntrega, 0, 'DAY') AS fechaMaxEntrega, "                                    
                                        . 'tcom.descTipoComunicacion, tcom.idTipoComunicacion, tdoc.descTipoDocumento, tdoc.idTipoDocumento, '
                                        . 'dfunc.idDeptoFuncional, dfunc.descDeptoFuncional, dfunc.inicialesDeptoFuncional,  '
                                        . 'fasig.idFuncionario, fasig.nombre1Funcionario, fasig.nombre2Funcionario, fasig.apellido1Funcionario, fasig.apellido2Funcionario, '
                                        . 'c.descCorrespondenciaEnc, c.temaComunicacion, inst.descInstitucion, inst.perfilInstitucion, est.idEstado, est.descripcionEstado '
                                        . 'FROM BackendBundle:TblCorrespondenciaEnc c '
                                        . 'INNER JOIN BackendBundle:TblEstados est WITH  est.idEstado = c.idEstado '
                                        . 'INNER JOIN BackendBundle:TblInstituciones inst WITH  inst.idInstitucion = c.idInstitucion '
                                        . 'INNER JOIN BackendBundle:TblDepartamentosFuncionales dfunc WITH  dfunc.idDeptoFuncional = c.idDeptoFuncional '                                    
                                        . 'INNER JOIN BackendBundle:TblDireccionesSreci dep WITH dep.idDireccionSreci = c.idDireccionSreci '
                                        . 'INNER JOIN BackendBundle:TblFuncionarios func WITH func.idFuncionario = c.idFuncionarioAsignado '
                                        . 'INNER JOIN BackendBundle:TblTipoDocumento tdoc WITH  tdoc.idTipoDocumento = c.idTipoDocumento '
                                        . 'INNER JOIN BackendBundle:TblTipoComunicacion tcom WITH tcom.idTipoComunicacion = c.idTipoComunicacion '
                                        . 'INNER JOIN BackendBundle:TblFuncionarios fasig WITH  fasig.idFuncionario = c.idFuncionarioAsignado '                                        
                                        . "WHERE c.fechaIngreso >= '".$fecha_inicial."' AND "
                                        . "c.fechaIngreso <= '".$fecha_final."' AND "
                                        . 'dep.idDireccionSreci = '.$direccion_user.' AND '                                                                               
                                        . 'tcom.idTipoComunicacion = 1 AND '                                                                               
                                        . 'tdoc.idTipoDocumento = 1 '                                                                               
                                        . 'ORDER BY c.codCorrespondenciaEnc, c.idCorrespondenciaEnc ASC') ;
                        } else {
                            $opt = "2";
                            $query = $em->createQuery('SELECT c.idCorrespondenciaEnc, c.codCorrespondenciaEnc, c.codReferenciaSreci, '
                                        . "DATE_SUB(c.fechaIngreso, 0, 'DAY') AS fechaIngreso, DATE_SUB(c.fechaFinalizacion, 0, 'DAY') AS fechaFinalizacion, "
                                        . "DATE_SUB(c.fechaMaxEntrega, 0, 'DAY') AS fechaMaxEntrega, "                                    
                                        . 'tcom.descTipoComunicacion, tcom.idTipoComunicacion, tdoc.descTipoDocumento, tdoc.idTipoDocumento, '
                                        . 'dfunc.idDeptoFuncional, dfunc.descDeptoFuncional, dfunc.inicialesDeptoFuncional,  '
                                        . 'fasig.idFuncionario, fasig.nombre1Funcionario, fasig.nombre2Funcionario, fasig.apellido1Funcionario, fasig.apellido2Funcionario, '
                                        . 'c.descCorrespondenciaEnc, c.temaComunicacion, inst.descInstitucion, inst.perfilInstitucion, est.idEstado, est.descripcionEstado '
                                        . 'FROM BackendBundle:TblCorrespondenciaEnc c '
                                        . 'INNER JOIN BackendBundle:TblEstados est WITH  est.idEstado = c.idEstado '
                                        . 'INNER JOIN BackendBundle:TblInstituciones inst WITH  inst.idInstitucion = c.idInstitucion '
                                        . 'INNER JOIN BackendBundle:TblDepartamentosFuncionales dfunc WITH  dfunc.idDeptoFuncional = c.idDeptoFuncional '                                    
                                        . 'INNER JOIN BackendBundle:TblDireccionesSreci dep WITH dep.idDireccionSreci = c.idDireccionSreci '
                                        . 'INNER JOIN BackendBundle:TblFuncionarios func WITH func.idFuncionario = c.idFuncionarioAsignado '
                                        . 'INNER JOIN BackendBundle:TblTipoDocumento tdoc WITH  tdoc.idTipoDocumento = c.idTipoDocumento '
                                        . 'INNER JOIN BackendBundle:TblTipoComunicacion tcom WITH tcom.idTipoComunicacion = c.idTipoComunicacion '
                                        . 'INNER JOIN BackendBundle:TblFuncionarios fasig WITH  fasig.idFuncionario = c.idFuncionarioAsignado '                                        
                                        . "WHERE c.fechaIngreso >= '".$fecha_inicial."' AND "
                                        . "c.fechaIngreso <= '".$fecha_final."' AND "
                                        . 'dep.idDireccionSreci = '.$direccion_user.' AND '
                                        . 'dfunc.idDeptoFuncional = '.$depto_func_user.' AND '                                                                               
                                        . 'tcom.idTipoComunicacion = 1 AND '  
                                        . 'tdoc.idTipoDocumento = 1 '
                                        . 'ORDER BY c.codCorrespondenciaEnc, c.idCorrespondenciaEnc ASC') ;
                        }                        
                        
                        // Ejecuta el Query
                        $correspondenciaFind = $query->getResult();                                  

                        // Evalua el Resultado de la Consulta
                        $count_rest = count($correspondenciaFind); 
                        
                        //Verificamos que el retorno de la Funcion sea = 0 ********* 
                        if( $count_rest > 0 ){
                            //Array de Mensajes
                            $data = array(
                                "status" => "success", 
                                "code"   => 200,
                                "option" => $opt, 
                                "idDireccionSreci" => $direccion_user,
                                "recordsTotal"  => $count_rest,
                                "msg"    => "Se ha encontrado la Informacion solicitada",
                                "data"   => $correspondenciaFind
                            );                        
                        } else{
                            $data = array(
                                "status" => "error",                            
                                "code"   => 504,
                                "option" => $opt,
                                //"query"  => $query->getResult(),
                                "idDireccionSreci" => $direccion_user,
                                "msg"   => "Error al buscar, no existe registros con esta informacion, ".
                                           " por favor, revise los parametros a enviar !!"
                            );                       
                        } // Fin de Busqueda de la Comunicacion
                    } else{
                            $data = array(
                                "status" => "error",
                                "desc"   => "Falta Informacion",
                                "code"   => 500, 
                                "msg"   => "Falta Ingresar información para poder continuar, "
                                        . "revise las fechas de generacion del reporte !!"
                            );                       
                    }//Finaliza el Bloque de la validadacion de la Data en la Tabla
                } else {
                        //Array de Mensajes | Campos que Faltan del Json
                        $data = array(
                           "status" => "error",
                           "desc"   => "Eror al Enviar el Json, el Json no ha sido enviado",
                           "code"   => 500, 
                           "msg"   => "Informacion no encontrada, falta ingresar parametros, "
                                    . "revisar la información ingresada !!"
                        );
                } // Evalua el Json que no sea Null            
            } else {
                $data = array(
                    "status" => "error",
                    "desc"   => "El Token, es invalido",    
                    "code" => 501,                
                    "msg" => "Autorizacion de Token no valida !!"                
                );
            } // FIN | Token          
            //Retorno de la Funcion ************************************************
            return $helpers->parserJson($data);
    }//FIN | FND00002
    
}

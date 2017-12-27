<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\JsonResponse;
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
 * Description of ConsultasController
 * Objetivo: Obtener las consultas a ala BD
 * @author Nahum Martinez
 */
class ConsultasController extends Controller{
    
    /**
     * @Route("/consulta-general", name="consulta-general")
     * Creacion del Controlador: Busqueda de la Actividad de la Comunicacion
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00001
     * Descripcion: Consulta que muestra la informacion general de la Comunicacion
     *              atraves de Parametros de Search
     * @param array $estados Filtros de Estados a Buscar
     */
    public function consultaGeneralEncListAction(Request $request )
    {
        //Seteo de variables Globales
        ini_set('memory_limit', '512M');
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
                $id_funcionario_asignado   = $identity->sub;
                
                //Nombre de Usuario
                $name_funcionario_asignado   = $identity->nombre;
                $apellido_funcionario_asignado   = $identity->apellido;
                
                // Tipo de Funcionario
                $id_tipo_funcionario       = $identity->idTipoFunc;
                $id_depto_funcional        = $identity->idDeptoFuncional;
                
                $codigo_oficio_interno     = ($params->codOficioInterno != null) ? $params->codOficioInterno : null ;
                $codigo_oficio_externo     = ($params->codOficioExterno != null) ? $params->codOficioExterno : null ;
                $send_search               = ($params->searchValueSend != null) ? $params->searchValueSend : null ;
                
                $opcion_busqueda           = ($params->optUserFindId != null) ? $params->optUserFindId : null ;
                $id_estado = ($params->idEstado);
                $fecha_modifcacion         = new \DateTime('now');
                
                //Evaluamos que los Campos del Json  no sean Null ni 0. ********
                if( $id_funcionario_asignado != 0 )
                {
                    //Instancia del Doctrine
                    $em = $this->getDoctrine()->getManager();
                    
                    // Seteo de Datos Generales de la tabla: TblCorrespondenciaEnc
                    // Localizamos el Oficio que se va a Buscar
                    // Verificacion del Tipo de Funcionario  ************
                    // Realizamos Condicion con swith ( $id_tipo_funcionario )
                    switch ( $id_tipo_funcionario )
                    {
                        case 1: // Administrador del Sistema
                            // Query para Obtener los Datos de la Consulta******                            
                            // Incidencia: INC.00001 | Consulta Lenta | Metodo *
                            // ->findBy ... No es factible; porque hace varias *
                            // consultas, por las tablas Relacionadas **********
                            // Fecha : 2017-12-26 | 4:40 pm
                            // Reportada : Nahum Martinez | Admon. SICDOC
                            // INI | NMA | INC.00001
                            // Cambiamos el llamada del findAll por findBy con un Array de Ordenamiento
                            $query = $em->createQuery('SELECT DISTINCT c.idCorrespondenciaEnc, c.codCorrespondenciaEnc, c.codReferenciaSreci, '
                                    //. 'c.fechaIngreso, c.fechaMaxEntrega, '
                                    //. 'format_datetime(c.fechaIngreso, "d/M/y") AS fecha, c.fechaMaxEntrega, '
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
                                    . 'WHERE c.idEstado IN (3,4,5,6,7,8)' );
                                    //. 'c.idCorrespondenciaEnc = d.idCorrespondenciaEnc') ;
                            $correspondenciaFind = $query->getResult();
                            $opcion_salida = $codigo_oficio_interno;
                            break;
                        case 4: // Administrador de Correspondencia
                            $query = $em->createQuery('SELECT DISTINCT c.idCorrespondenciaEnc, c.codCorrespondenciaEnc, c.codReferenciaSreci, '
                                    . 'c.fechaIngreso, c.fechaMaxEntrega, tdoc.descTipoDocumento, '
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
                                    . 'WHERE c.idEstado IN (3,4,5,6,7,8) ' );
                                   
                            $correspondenciaFind = $query->getResult();                            
                            /*$correspondenciaFind = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")
                                ->findBy(
                                    array(                                        
                                        "idEstado" => [3,4,5,6,7,8]
                                    ), array("idCorrespondenciaEnc" => "ASC", "idEstado" => "ASC") );                             
                             */
                            $opcion_salida = $codigo_oficio_interno;
                            break;
                        case 6: // Director de Area
                            $query = $em->createQuery('SELECT DISTINCT c.idCorrespondenciaEnc, c.codCorrespondenciaEnc, c.codReferenciaSreci, '
                                    . 'c.fechaIngreso, c.fechaMaxEntrega, tdoc.descTipoDocumento, '
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
                                    . 'WHERE c.idEstado IN (3,4,5,6,7,8) AND c.idDeptoFuncional = '. $id_depto_funcional .' ' );
                                   
                            $correspondenciaFind = $query->getResult();
                            
                            /*$correspondenciaFind = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")
                                ->findBy(
                                    array(                                        
                                        "idDeptoFuncional"    => $id_depto_funcional,                                        
                                        "idEstado"            => [3,4,5,6,7,8]
                                    ));                             
                             */
                            $opcion_salida = $codigo_oficio_externo;
                            break;
                        case 2: // Analista de Cartera / Funcionario
                            $query = $em->createQuery('SELECT DISTINCT c.idCorrespondenciaEnc, c.codCorrespondenciaEnc, c.codReferenciaSreci, '
                                    . 'c.fechaIngreso, c.fechaMaxEntrega, tdoc.descTipoDocumento, '
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
                                    . 'WHERE c.idEstado IN (3,4,5,6,7,8) AND c.idFuncionarioAsignado = '. $id_funcionario_asignado .' ' );
                                    
                            $correspondenciaFind = $query->getResult();
                            /*$correspondenciaFind = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")
                                ->findBy(
                                    array(                                        
                                        "idFuncionarioAsignado"  => $id_funcionario_asignado,                                        
                                        "idEstado"               => [3,4,5,6,7,8]
                                    ));                             
                             */
                            $opcion_salida = $codigo_oficio_externo;
                            break;
                        
                        // FIN | NMA | INC.00001
                    } // FIN | Case                    
                                        
                    $totalCorrespondenciaFind = count($correspondenciaFind);
                    //Verificamos que el retorno de la Funcion sea = 0 ********* 
                    if($totalCorrespondenciaFind > 0 ){
                        //Array de Mensajes
                        $data = array(
                            "status" => "success", 
                            "code"   => 200,
                            "recordsTotal" => $totalCorrespondenciaFind,
                            "recordsFiltered" => $totalCorrespondenciaFind,
                            "draw" => $totalCorrespondenciaFind,
                            "msg"    => "Se ha encontrado la Informacion solicitada",
                            "data"   => $correspondenciaFind
                        );                        
                    } else{
                        $data = array(
                            "status" => "error",                            
                            "code"   => 504, 
                            "msg"   => "Error 504, No Existe Comunicaciones asignadas a: " 
                            . $name_funcionario_asignado . " - " . $apellido_funcionario_asignado . 
                                       " por favor contacte al Administrador !!"
                        );                       
                    } // Fin de Busqueda de la Comunicacion
                } else{
                        $data = array(
                            "status" => "error",
                            "desc"   => "Falta Informacion",
                            "code"   => 400, 
                            "msg"   => "Falta Ingresar información para poder continuar !!"
                        );                       
                }//Finaliza el Bloque de la validadacion de la Data en la Tabla
            } else {
                    //Array de Mensajes | Campos que Faltan del Json
                    $data = array(
                       "status" => "error",
                       "desc"   => "Eror al Enviar el Json, el Json no ha sido enviado",
                       "code"   => 400, 
                       "msg"   => "Informacion no encontrada, falta ingresar parametros, revisar la información ingresada !!"
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
    
    
    
    /**
     * @Route("/consulta-general-det", name="consulta-general-det")
     * Creacion del Controlador: Busqueda de la Actividad de la Comunicacion, Detalle
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND0002
     * Descripcion: Consulta que muestra la informacion general de la Comunicacion
     *              atraves de Parametros de Search, para obtener las distintas 
     *              Actividades con sus Detalles
     */
    public function consultaGeneralDetListAction(Request $request )
    {
        //Seteo de variables Globales
        ini_set('memory_limit', '512M');
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
                $id_funcionario_asignado   = $identity->sub;
                $codigo_oficio_interno     = ($params->codOficioInterno != null) ? $params->codOficioInterno : null ;
                $codigo_oficio_externo     = ($params->codOficioExterno != null) ? $params->codOficioExterno : null ;
                $send_search               = ($params->searchValueSend != null) ? $params->searchValueSend : null ;
                
                $opcion_busqueda           = ($params->optUserFindId != null) ? $params->optUserFindId : null ;
                
                $fecha_modifcacion         = new \DateTime('now');
                
                $id_correspondencia_enc   = ($params->idCorrespondenciaEnc != null) ? $params->idCorrespondenciaEnc : null ;
                
                               
                //Evaluamos que los Campos del Json  no sean Null ni 0. ********
                if( $codigo_oficio_interno != null || $codigo_oficio_externo != null 
                   && $id_funcionario_asignado != 0 || $opcion_busqueda != null )
                {
                    //Instancia del Doctrine
                    $em = $this->getDoctrine()->getManager();
                    
                    
                    // Obtenemos los Datos de Encabezado de la Actividad *******
                    // Seteo de Datos Generales de la tabla: TblCorrespondenciaEnc                    
                    $correspondenciaFind = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")
                        ->findOneBy(
                            array(
                                "idCorrespondenciaEnc" => $id_correspondencia_enc,
                                //"idFuncionarioAsignado" => $id_funcionario_asignado
                            ));
                    $opcion_salida = $codigo_oficio_interno;
                           
                    
                    // Obtenemos los Datos de Detalle de la Actividad **********
                    // Busqueda de Detale de la tabla: TblCorrespondenciaDet           
                    $correspondenciaDetFind = $em->getRepository("BackendBundle:TblCorrespondenciaDet")
                        ->findBy(
                            array(
                                "idCorrespondenciaEnc"  => $correspondenciaFind
                            ), array("idCorrespondenciaDet" => "ASC", "idEstado" => "ASC")  );
                                        
                    
                    //Verificamos que el retorno de la Funcion sea = 0 ********* 
                    if(count($correspondenciaDetFind) > 0 ){
                        //Array de Mensajes
                        $data = array(
                            "status" => "success", 
                            "code"   => 200,
                            "recordsTotal" => count($correspondenciaDetFind),
                            "recordsFiltered" => count($correspondenciaDetFind),
                            "draw" => count($correspondenciaDetFind),
                            "msg"    => "Se ha encontrado la Informacion solicitada",
                            "data"   => $correspondenciaDetFind
                        );                        
                    } else{
                        $data = array(
                            "status" => "error",                            
                            "code"   => 404, 
                            "msg"   => "Error al buscar, no existe una Comunicacion con este código, ". $opcion_salida . 
                                       " por favor ingrese otro !!"
                        );                       
                    } // Fin de Busqueda de la Comunicacion
                } else{
                        $data = array(
                            "status" => "error",
                            "desc"   => "Falta Informacion",
                            "code"   => 400, 
                            "msg"   => "Falta Ingresar información para poder continuar !!"
                        );                       
                }//Finaliza el Bloque de la validadacion de la Data en la Tabla
            } else {
                    //Array de Mensajes | Campos que Faltan del Json
                    $data = array(
                       "status" => "error",
                       "desc"   => "Eror al Enviar el Json, el Json no ha sido enviado",
                       "code"   => 400, 
                       "msg"   => "Informacion no encontrada, falta ingresar parametros, revisar la información ingresada !!"
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
    }//FIN | FND00002
    
}

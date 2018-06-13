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

use BackendBundle\Entity\TblBitacoraSecuencias;
use BackendBundle\Entity\TblSecuenciasComprometidas;


/**
 * Description of ComunesController
 *
 * @author Sammy Guergachi <sguergachi at gmail.com>
 */
class ComunesUnidadCorrespondenciaController extends Controller {
    //put your code here
    
    /**
     * @Route("/upload-documento", name="upload-documento")
     * Creacion del Controlador: Comunes PDF
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00001
     */
    public function uploadDocumentoAction(Request $request) {
        //Instanciamos el Servicio Helpers
        date_default_timezone_set('America/Tegucigalpa');
        //date_default_timezone_set('Australia/Sydney');
        $helpers = $this->get("app.helpers");        
        //Recoger el Hash
        //Recogemos el Hash y la Autrizacion del Mismo
        $hash = $request->get("authorization", null);
        
        // Nombre del Documento
        $file_nameIn = $request->get("name_pdf");
        
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
            $file      = $request->files->get("image");            
            
            //Se verifica que el fichero no venga Null
            if (!empty($file) && $file != null) {
                //Obtenemos la extencion del Fichero
                $ext = $file->guessExtension();
                //Comprobamos que la Extencion sea Aceptada
                if ($ext == "pdf" || $ext == "PDF" ||  $ext == "doc" || $ext == "docs" || $ext == "docx" ||
                    $ext == "xlsx" || $ext == "xls" || $ext == "ppt" || $ext == "pptx" ||
                    $ext == "png" || $ext == "jpeg" || $ext == "jpg") {                   
                    // Concatenmos al Nombre del Fichero la Fecha y la Extencion
                    //$file_name = time().".".$ext;
                    /* INC00001 | 2018-01-04
                    * Corregir la Extencion del PDF a pdf
                    */
                    if( $ext == "PDF" ){
                        $ext = "pdf";
                    }
                    //FIN | INC00001
                    
                    $file_name = $file_nameIn . "-" . date('Y-m-d'). "." .$ext; 
                    //$file_name = $file_nameIn . "." .$ext; 
                    //Movemos el Fichero
                    //$path_of_file = "uploads/correspondencia/correspondencia_".date('Y-m-d');
                    $path_of_file = "uploads/unidad-correspondencia/";
                    $file->move($path_of_file, $file_name);
                
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
        date_default_timezone_set('America/Tegucigalpa');
        //Instanciamos el Servicio Helpers y Jwt
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
            //Datos del Perfil del Usuario
            $id_usuario          = $identity->sub; // Id de Usuario
            $iniciales_direccion = $identity->inicialesDireccion; // Iniciales Direccion
            $iniciales_depto     = $identity->inicialesDeptoFuncional; // Iniciales Depto
                        
            $id_tipo_usuario = $identity->idTipoUser; // Tipo de Usuario 
            $id_tipo_funcionario = $identity->idTipoFunc; // Tipo de Funcionario
            
            $despacho = $identity->despacho; // Tipo de Funcionario
            
            // Año actual con 4 dígitos, ej 2013
            $anio_actual = date("Y");
            // Horario de 12 horas con ceros, de 01 a 12
            $time = time();
            //$hora_actual = date("d-m-Y (H:i:s)", $time);
            $hora_actual = date("H");
            
            // Fechas Nulas
            $fecha_null = new \DateTime('2999-12-31');
            
            $fecha_ingreso = new \DateTime('now');
            
            $hora_ingreso = new \DateTime('now');            
            $hora_ingreso->format('H:i');
            
            
            //******************************************************************
            //Declaracion del Entity Manager
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
            //Variables que vienen del Json ************************************
            // Recogemos el Pais y el Tipo de Institucion **********************
            $codigo_secuencia = (isset($params->codSecuencial)) ? $params->codSecuencial : null;
            $tabla_secuencia  = (isset($params->tablaSecuencia)) ? $params->tablaSecuencia : null;
            $tipo_documento   = (isset($params->idTipoDocumento)) ? $params->idTipoDocumento : null;
                        
            /* INC.00001 | 2018-01-08 ******************************************
             * Nuevos Parametros, esto con el fin de Segmentar las Secuencias 
             * por Direccion
            ********************************************************************/
            $tipo_funcionario   = (isset($params->idTipoUsuario)) ? $params->idTipoUsuario : null;
            $depto_funcional    = (isset($params->idDeptoFuncional)) ? $params->idDeptoFuncional : null;
            
            // Para el uso de las Secuencias por Direccion
            $direccion_sreci     = (isset($params->idDireccion)) ? $params->idDireccion : null;
            
            
            //Instacias de Usuarios y Deptos Funcionales
            //Usuarios 
            // Query para Obtener el Usuario de la Tabla: TblUsuarios
            $usuarioBitacora = $em->getRepository("BackendBundle:TblUsuarios")->findOneBy(
                array(
                    "idUsuario"   => $id_usuario, // Id de Usuario                    
                ));
            
            // Query para Obtener la Direccion SRECI de la Tabla: TblDireccionesSreci
            $usuarioDireccionBitacora = $em->getRepository("BackendBundle:TblDepartamentosFuncionales")->findOneBy(
                array(
                    "idDireccionSreci"   => $direccion_sreci, // Id de Direcion SRECI
                ));
            
            // Query para Obtener el Depto Funcional de la Tabla: TblDepartamentosFuncionales
            $usuarioDeptoBitacora = $em->getRepository("BackendBundle:TblDepartamentosFuncionales")->findOneBy(
                array(
                    "idDeptoFuncional"   => $depto_funcional, // Id de DeptoFuncional
                ));
            
            // 2018-02-06 | Nuevas Instacias de las Tablas
            // Query para Obtener el Estado de la Tabla: TblEstados
            $estadoSecuencia = $em->getRepository("BackendBundle:TblEstados")->findOneBy(
                array(
                    "idEstado" => 11, // Id de Estado
                ));
            
            // Query para Obtener el DeptFuncional de la Tabla: TblDepartamentosFuncionales
            $tipoDocumentoSecuencia = $em->getRepository("BackendBundle:TblTipoDocumento")->findOneBy(
                array(
                    "idTipoDocumento" => $tipo_documento, // Id de Tipo de Documento
                ));
            
           
            //$new_depto_funcional = $depto_funcional;
            /*Ejecucion de la Secuencia  segun Parametros *********************/
            if( $depto_funcional == 8 && $codigo_secuencia == $iniciales_direccion ){
                $despacho = 1;
                $sec = 1.1;
                //$new_depto_funcional = $depto_funcional;
                // Query para Obtener todos las secuencias segun Parametros de la Tabla: TblSecuenciales
                $secuencias  = $em->getRepository("BackendBundle:TblSecuenciales")->findOneBy(
                    array(
                        "codSecuencial"     => $codigo_secuencia, // Codigo de la Secuencia
                        "tablaSecuencia"    => $tabla_secuencia,  // Tabla de la Secuencia a Obtener
                        "idTipoDocumento"   => $tipo_documento, // Tipo de Documento (Oficio)
                        //"idTipoUsuario"     => $tipo_funcionario, // Tipo de Funcionario 
                        "idDeptoFuncional"  => 8, // Depto Funcional (Direccion)
                        //"idDireccionSreci"  => $direccion_sreci, // Direccion SRECI
                        //"idTipoFuncionario"  => $id_tipo_funcionario, // Direccion SRECI
                        //"reservada"       => "N"
                        "habilitada"       => TRUE,
                        "despacho"       => $despacho
                    ));

            }else if( $depto_funcional != 8 && $codigo_secuencia == $iniciales_direccion ) {
                $despacho = 0;
                $sec = 1.2;
                // Query para Obtener todos las secuencias segun Parametros de la Tabla: TblSecuenciales
                $secuencias  = $em->getRepository("BackendBundle:TblSecuenciales")->findOneBy(
                    array(
                        "codSecuencial"     => $codigo_secuencia, // Codigo de la Secuencia
                        "tablaSecuencia"    => $tabla_secuencia,  // Tabla de la Secuencia a Obtener
                        "idTipoDocumento"   => $tipo_documento, // Tipo de Documento (Oficio)
                        //"idTipoUsuario"     => $tipo_funcionario, // Tipo de Funcionario 
                        //"idDeptoFuncional"  => $depto_funcional, // Depto Funcional (Direccion)
                        //"idDireccionSreci"  => $direccion_sreci, // Direccion SRECI
                        //"idTipoFuncionario"  => $id_tipo_funcionario, // Direccion SRECI
                        //"reservada"       => "N"
                        "habilitada"       => TRUE,
                        "despacho"       => $despacho
                    ));
            }else{
                $despacho = 0;
                $sec = 1.3;
                // Query para Obtener todos las secuencias segun Parametros de la Tabla: TblSecuenciales
                $secuencias  = $em->getRepository("BackendBundle:TblSecuenciales")->findOneBy(
                    array(
                        "codSecuencial"     => $codigo_secuencia, // Codigo de la Secuencia
                        "tablaSecuencia"    => $tabla_secuencia,  // Tabla de la Secuencia a Obtener
                        "idTipoDocumento"   => $tipo_documento, // Tipo de Documento (Oficio)
                        //"idTipoUsuario"     => $tipo_funcionario, // Tipo de Funcionario 
                        //"idDeptoFuncional"  => $depto_funcional, // Depto Funcional (Direccion)
                        //"idDireccionSreci"  => $direccion_sreci, // Direccion SRECI
                        //"idTipoFuncionario"  => $id_tipo_funcionario, // Direccion SRECI
                        //"reservada"       => "N"
                        "habilitada"       => TRUE,
                        "despacho"       => $despacho
                    ));
            }
            
            
            /* INC.00002 | 2018-02-06 ******************************************
             * Consulta de Nueva Tabla de Secuencias Comprometidas (Tbl_Secuecni
             * as_Comprometidas)
             * Autor: Nahum martinez
             * Params: [codSecuencial, idTipoDocumento, idDeptoFuncional,
             *          idUsuario, idEstadoSecuencia]
            ********************************************************************/            
            // Query para Obtener todos la Secuencia de los Listados segun 
            // Parametros de la Tabla: TblSecuenciasComprometidas                
                $query = $em->createQuery('SELECT scom.valor1, scom.valor2 '                                    
                                    . 'FROM BackendBundle:TblSecuenciasComprometidas scom '                                    
                                    . 'INNER JOIN BackendBundle:TblEstados est WITH est.idEstado = scom.idEstadoSecuencia '
                                    . 'INNER JOIN BackendBundle:TblDepartamentosFuncionales depto WITH depto.idDeptoFuncional = scom.idDeptoFuncional '
                                    . 'INNER JOIN BackendBundle:TblTipoDocumento tdoc WITH tdoc.idTipoDocumento = scom.idTipoDocumento '
                                    . 'INNER JOIN BackendBundle:TblUsuarios user WITH user.idUsuario = scom.idUsuario '
                                    . 'WHERE scom.idEstadoSecuencia = :idEstadoSec AND '
                                    . 'scom.idTipoDocumento = :idTipoDocumento AND ' 
                                    . 'scom.idDeptoFuncional = :idDeptoFuncional AND ' 
                                    . 'scom.codSecuencial = :codSecuencial AND ' 
                                    . 'scom.idUsuario = :idUsuario ' 
                                    . 'ORDER BY scom.codSecuencial ASC')
                    ->setParameter('idEstadoSec', 11)->setParameter('idTipoDocumento', $tipo_documento )->setParameter('idUsuario', $id_usuario)
                    ->setParameter('idDeptoFuncional', $depto_funcional)->setParameter('codSecuencial', $codigo_secuencia );
                    
                $secuenciaComprometida = $query->getResult();
                
            //Secuencia encontrada
            $countSecuenciaComprometida = count( $secuenciaComprometida );
            
            
            //Opcion de Seleccion **********************************************
            $optSec = 0;
            //var_dump($countSecuenciaComprometida . " ---- " . $codigo_secuencia );
            //Evalua los Resultados de la Consulta
            if( $countSecuenciaComprometida == 0  && $codigo_secuencia == $iniciales_direccion ){
                // Condicion para Actualizar Datos de la Secuencia
                //if( $secuencias->getReservada() === "N"  ){
                    $optSec = 1.1;
                    $comprometidasSecuencias = new TblSecuenciasComprometidas();                     

                    //Opcion de Secuencia
                    if( $secuencias->getReservada() === "N"  ){                    
                        //Opcion de Secuencia
                        $optSec = "1.1.1";
                        $secuencias->setReservada('S');
                        
                        $comprometidasSecuencias->setCodSecuencial( $codigo_secuencia );
                        $comprometidasSecuencias->setValor1( $secuencias->getValor2() );
                        $comprometidasSecuencias->setValor2( $secuencias->getValor2() );
                        $comprometidasSecuencias->setIdUsuario( $usuarioBitacora );
                        $comprometidasSecuencias->setIdDeptoFuncional( $usuarioDeptoBitacora );
                        $comprometidasSecuencias->setIdTipoDocumento( $tipoDocumentoSecuencia );
                        $comprometidasSecuencias->setCodCorrespondenciaSreci( $secuencias->getValor2() . "-" . $iniciales_direccion . 
                                                  "-" . $iniciales_depto . "-" . $anio_actual );
                        $comprometidasSecuencias->setIdEstadoSecuencia( $estadoSecuencia );
                        $comprometidasSecuencias->setFechaCreacion( $fecha_ingreso ); // Fecha de Creacion
                        $comprometidasSecuencias->setFechaActualizacion( $fecha_null ); // Fecha de Actualizacion
                        $comprometidasSecuencias->setHoraCreacion( $hora_ingreso ); // Hora de Creacion
                        
                    }else if ( $secuencias->getReservada() === "S" ) {
                        $optSec = "1.1.2";
                        $secuencias->setValor2( $secuencias->getValor2() + 1 ); //Set de valor2 de Secuencia de Comunicacion                        
                        
                        $comprometidasSecuencias->setCodSecuencial( $codigo_secuencia );
                        $comprometidasSecuencias->setValor1( $secuencias->getValor2() - 1 );
                        $comprometidasSecuencias->setValor2( $secuencias->getValor2() );
                        $comprometidasSecuencias->setIdUsuario( $usuarioBitacora );
                        $comprometidasSecuencias->setIdDeptoFuncional( $usuarioDeptoBitacora );
                        $comprometidasSecuencias->setIdTipoDocumento( $tipoDocumentoSecuencia );
                        $comprometidasSecuencias->setCodCorrespondenciaSreci( $secuencias->getValor2() . "-" . $iniciales_direccion . 
                                                  "-" . $iniciales_depto . "-" . $anio_actual );
                        $comprometidasSecuencias->setIdEstadoSecuencia( $estadoSecuencia );
                        $comprometidasSecuencias->setFechaCreacion( $fecha_ingreso ); // Fecha de Creacion
                        $comprometidasSecuencias->setFechaActualizacion( $fecha_null ); // Fecha de Actualizacion
                        $comprometidasSecuencias->setHoraCreacion( $hora_ingreso ); // Hora de Creacion
                    }                   
                    

                     //Realizar la Persistencia de los Datos y enviar a la BD                
                    $em->persist($secuencias);
                    $em->persist( $comprometidasSecuencias );
                    //Realizar la actualizacion en el storage de la BD
                    $em->flush();
                    
                    $count_Sec = count($comprometidasSecuencias); 
                    $data = $comprometidasSecuencias;
                //}  else if ( $secuencias->getReservada() === "S" ) {
            } else if ( $countSecuenciaComprometida > 0 && $codigo_secuencia == $iniciales_direccion ) {
                    $optSec = 1.2;
                    //$comprometidasSecuencias = new TblSecuenciasComprometidas(); 
                    // Query para Obtener todos las Sec. de la Tabla: TblSecuenciasComprometidas
                    $comprometidasSecuencias = $em->getRepository("BackendBundle:TblSecuenciasComprometidas")->findOneBy(
                        array(
                            "codSecuencial"    => $codigo_secuencia, // Codigo de la Secuencia,
                            "idTipoDocumento"  => $tipo_documento, // Tipo de Documentos de la Secuencia,
                            "idDeptoFuncional" => $depto_funcional, // Depto Funcional de la Secuencia,
                            "idUsuario"        => $id_usuario, // Usuario de la Secuencia,
                            "idEstadoSecuencia" => 11 // Estado de la Secuencia,                            
                        ));
                    
                    $count_Sec = count($comprometidasSecuencias);
                    $data = $comprometidasSecuencias;
               // }             
            } else if ( $codigo_secuencia != $iniciales_direccion ) {                
                // Condicion para Actualizar Datos de la Secuencia
                if( $secuencias->getReservada() === "N"  ){                    
                    //Opcion de Secuencia
                    $optSec = 1.3;
                    $secuencias->setReservada('S');                          

                    // INI MOD.000001 | Ingresamos en la Bitacora | TblBitacoraSecuencias                             
                    // 2018-01-08
                    $bitacoraSecuencias = new TblBitacoraSecuencias(); 

                    $bitacoraSecuencias->setCodSecuencia( $secuencias->getCodSecuencial() );
                    $bitacoraSecuencias->setValor2Old( $secuencias->getValor2() );
                    $bitacoraSecuencias->setValor2New( $secuencias->getValor2() );
                    $bitacoraSecuencias->setIdUsuario( $usuarioBitacora );
                    $bitacoraSecuencias->setIdDeptoFuncional( $usuarioDeptoBitacora );

                     //Realizar la Persistencia de los Datos y enviar a la BD
                    $em->persist($secuencias);
                    $em->persist($bitacoraSecuencias);

                    //Realizar la actualizacion en el storage de la BD
                    $em->flush();
                    // *************************************************************
                } else if ( $secuencias->getReservada() === "S" ) {
                    //$secuencias->setReservada('N'); 
                    //Opcion de Secuencia
                    $optSec = 1.4;
                    $secuencias->setValor2( $secuencias->getValor2() + 1 ); //Set de valor2 de Secuencia de Comunicacion
                    //$secuencias->setReservada('N'); 

                    //MOD.000001 | Ingresamos en la Bitacora | TblBitacoraSecuencias                             
                    $bitacoraSecuencias = new TblBitacoraSecuencias(); 

                    $bitacoraSecuencias->setCodSecuencia( $secuencias->getCodSecuencial() );
                    $bitacoraSecuencias->setValor2Old( $secuencias->getValor2() );
                    $bitacoraSecuencias->setValor2New( $secuencias->getValor2() + 1);
                    $bitacoraSecuencias->setIdUsuario( $usuarioBitacora );
                    $bitacoraSecuencias->setIdDeptoFuncional( $usuarioDeptoBitacora );

                    //Realizar la Persistencia de los Datos y enviar a la BD
                    $em->persist($secuencias);
                    $em->persist($bitacoraSecuencias);

                    //Realizar la actualizacion en el storage de la BD
                    $em->flush();
                    // *************************************************************
                }
                //FIN | MOD.000001
                // Validar que la Secuencia no ha sido, reservada por Otro Usuario
                $count_Sec = count($secuencias); 
                $data = $secuencias;
            }                               
            
            // FIN | INC.00002            
              
                
            // Condicion de la Busqueda
            if ( $count_Sec >= 1 ) {
                // Actualizamos la Suencia a Reservada
                //Seteo de Datos Generales de la tabla                
                //Mensaje de Data Obtenida
                $data = array(
                    "status" => "success",
                    "msg"    => "Secuencia Encontrada",
                    "code"   => 200, 
                    "optSec" => $optSec,
                    "sec"    => $sec,
                    "findSec" => $countSecuenciaComprometida,
                    //"data"   => $secuencias
                    "data"   => $data
                );              
                
            }else {
                $data = array(
                    "status" => "error",
                    "code"   => 400,
                    "optSec" => $optSec,
                    "msg"    => "No existe Datos en la Tabla de Secuencias, comuniquese con el Administrador !!"
                );
            }
        }
        }else{
            $data = array(
               "status" => "error",
               "code" => 400,
               "msg" => "Autorización no valida, la sesión ha sido vencida !!"
            );  
        }
        return $helpers->parserJson($data);
    }//FIN | FND00002
    
    
    
    /**
     * @Route("comunes-unidad-correspondencia/documentos-upload-options", 
     * name="comunes-unidad-correspondencia/documentos-upload-options")
     * Creacion del Controlador: Comunes Documentos PDF
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00003
     */
    public function uploadDocumentoOpctionAction(Request $request) {
        //Instanciamos el Servicio Helpers        
        date_default_timezone_set('America/Tegucigalpa');
        $helpers = $this->get("app.helpers");

        $json = $request->get("json", null);
        $params = json_decode($json);
        
        $cod_contacto  = (isset($params->codigoSec)) ? $params->codigoSec : null;
        $cod_contacto2  = (isset($params->codigoSec2)) ? $params->codigoSec2 : null;
                
        // Nombre del Documento
        $file_nameIn = $request->get("name_pdf");
                
        //Recoger el Fichero que viene por el POST y lo guardamos el HD
        $file      = $request->files->get("name_pdf");
               
        
        //Recoger el Fichero que viene por el POST y lo guardamos el HD ********
            //Se verifica que el fichero no venga Null
            if (!empty($file) && $file != null) {
                //Obtenemos la extencion del Fichero
                $ext = $file->guessExtension();
                //$nameDoc = $file->guessName();
                //Comprobamos que la Extencion sea Aceptada
                if ($ext == "pdf" || $ext == "PDF" || $ext == "doc" || $ext == "docs" || $ext == "docx" ||
                    $ext == "xlsx" || $ext == "xls" || $ext == "ppt" || $ext == "pptx" ||
                    $ext == "png" || $ext == "jpg" || $ext == "jpeg" ) {                   
                    // Concatenmos al Nombre del Fichero la Fecha y la Extencion
                    if( $ext == "PDF" ){
                        $ext = "pdf";
                    }
                    
                    if( $ext == "PNG" ){
                        $ext = "png";
                    }
                    
                    if( $ext == "jpg" || $ext == "JPG" ){
                        $ext = "jpeg";
                    }
                    
                    
                    //$file_name = time().".".$ext;
                    $file_name = $file_nameIn . "-" . date('Y-m-d'). "." .$ext; 
                    //Movemos el Fichero
                    $path_of_file = "uploads/unidad-correspondencia/";
                    $file->move($path_of_file, $file_name);                    
                
                    // Devolvemos el Mensaje de Array
                    $data = array(
                        "status" => "success",
                        "code" => 200,
                        "msg" => "Document for user uploaded success !!",
                        "data" => $file_name
                    );
                } else {
                    // Devolvemos el Mensaje de Array, cuando la Imagen no sea valida
                    $data = array(
                        "status" => "error",
                        "code" => 400,
                        "msg" => "File not valid, please check this format !!"
                    );
                }
            }else{
                $data = array(
                    "status" => "error",
                    "code" => 400,
                    "msg" => "Document not upload !!" . $file_nameIn
                );                
            }            
        
        //Retorno de la Funcion ************************************************
        return $helpers->parserJson($data);
    } // FIN FND00003
    
    
    
}

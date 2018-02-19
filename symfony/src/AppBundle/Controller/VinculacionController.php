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
use BackendBundle\Entity\TblFuncionarios;
use BackendBundle\Entity\TblDepartamentosFuncionales;

use BackendBundle\Entity\TblCorrespondenciaEnc;
use BackendBundle\Entity\TblCorrespondenciaDet;

/**
 * Description of VinculacionController
 *
 * @author Nahum Martinez
 */
class VinculacionController extends Controller{
    //put your code here
    
    
    /**
     * @Route("/edit", name="edit")
     * Creacion del Controlador: Usuarios
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00001
     */
    public function editAction(Request $request) {
        date_default_timezone_set('America/Tegucigalpa');
        //Instanciamos el Servicio Helpers
        $helpers = $this->get("app.helpers");
        //Recoger el Hash
        //Recogemos el Hash y la Autrizacion del Mismo
        $hash = $request->get("authorization", null);
        //Se Chekea el Token
        $checkToken = $helpers->authCheck($hash);
        //Evalua que el Token sea True
        if($checkToken == true){
        //Ejecutamos todo el Codigo restante
        $identity = $helpers->authCheck($hash, true);    
        $em = $this->getDoctrine()->getManager();
        $usuario = $em->getRepository("BackendBundle:TblUsuarios")->findOneBy(
                array(
                    "idUsuario" => $identity->sub
            ));        
        $json = $request->get("json", null);
        $params = json_decode($json);
        
        //Array de Mensajes
        $data = $data = array(
                "status" => "error",                
                "code" => "400",                
                "msg" => "Usuario no actualizado, hay problemas en los Datos !!"                
            ); 
        
        //Evaluamos el Json
        if ($json != null) {
            //Variables que vienen del Json ***********************************************
            //Seccion de Identificacion ***************************************************
            //El ID no se incluye; ya que es un campo Serial            
            $cod_usuario = (isset($params->cod_usuario)) ? $params->cod_usuario : null;
            $iniciales = (isset($params->iniciales)) ? $params->iniciales : null;
            $nombre1 = (isset($params->nombre1) && ctype_alpha($params->nombre1) ) ? $params->nombre1 : null;
            $nombre2 = (isset($params->nombre2) && ctype_alpha($params->nombre2) ) ? $params->nombre2 : null;
            $apellido1 = (isset($params->apellido1) && ctype_alpha($params->apellido1) ) ? $params->apellido1 : null;
            $apellido2 = (isset($params->apellido2) && ctype_alpha($params->apellido2) ) ? $params->apellido2 : null;
            $email = (isset($params->email)) ? $params->email : null;            
            //Seccion de Relaciones entre Tablas ********************************************************************
            $cod_estado = (isset($params->cod_estado)) ? $params->cod_estado : null;
            $cod_tipo_funcionario = (isset($params->cod_tipo_funcionario)) ? $params->cod_tipo_funcionario : null;
            $cod_depto_funcional = (isset($params->cod_depto_funcional)) ? $params->cod_depto_funcional : null;
            $cod_tipo_usuario = (isset($params->cod_tipo_usuario)) ? $params->cod_tipo_usuario : null;                        
            //Datos de Bitacora *************************************************************************************
            $createdAt = new \DateTime("now");
            $image = null;
            $password = (isset($params->password)) ? $params->password : null;
            
            //Validamos el Email ************************************************************************************
            $emailConstraint = new Assert\Email();
            $emailConstraint->message = "El Email no es valido!!";
            
            $valid_email = $this->get("validator")->validate($email, $emailConstraint);
            //Entitie Manager Definition ****************************************************************************
            $em = $this->getDoctrine()->getManager();
            
            if ($email != null && count($valid_email) == 0 &&
                $nombre1 != null && $apellido1 != null ){
                //Instanciamos la Entidad TblUsuario *****************************************                
                //$usuario = new TblUsuarios();                
                //Seteamos los valores de Identificacion ***********************
                $usuario->setcodUsuario($cod_usuario);                
                $usuario->setNombre1Usuario($nombre1);
                $usuario->setNombre2Usuario($nombre2);
                $usuario->setApellido1Usuario($apellido1);
                $usuario->setApellido2Usuario($apellido2);
                $usuario->setEmailUsuario($email);
                //Seteamos los valores de Relaciones de Tablas *******************************
                //Instancia a la Tabla: TblEstados *****************************                
                $estados = $em->getRepository("BackendBundle:TblEstados")->findOneBy(
                            array(
                                "codEstado" => $cod_estado
                            )) ;
                $usuario->setCodEstado($estados);
                //Instancia a la Tabla: TblTipoFuncionarios ********************                
                $tipoFuncionario = $em->getRepository("BackendBundle:TblTiposFuncionarios")->findOneBy(
                            array(
                                "codTipoFuncionario" => $cod_tipo_funcionario
                            )) ;
                $usuario->setCodTipoFuncionario($tipoFuncionario);  
                //Instancia a la Tabla: TblDepartamentosFuncionales ************                
                $deptoFuncional = $em->getRepository("BackendBundle:TblDepartamentosFuncionales")->findOneBy(
                            array(
                                "codDeptoFuncional" => $cod_depto_funcional
                            )) ;
                $usuario->setCodDeptoFuncional($deptoFuncional);
                //Instancia a la Tabla: TblTipoUsuario *************************                
                $tipoUsuario = $em->getRepository("BackendBundle:TblTipoUsuario")->findOneBy(
                            array(
                                "codTipoUsuario" => $cod_tipo_usuario
                            )) ;
                $usuario->setCodTipoUsuario($tipoUsuario);
                //Seteamos el Resto de campos de la Tabla: TblUsuarios *********
                $usuario->setInicialesUsuario($iniciales);
                //Cifrar la Contraseña *****************************************
                if ($password != null) {                            
                    $pwd = hash('sha256', $password);                
                    $usuario->setPasswordUsuario($pwd);                
                }
                $usuario->setImagenUsuario($image);
                //Seteamos los valores de la Bitacora **************************
                $usuario->setFechaCreacion($createdAt);                
                //Verificacion del Codigo y Email en la Tabla: TblUsuarios *****                
                $isset_user_mail = $em->getRepository("BackendBundle:TblUsuarios")->findBy(
                        array(
                          "emailUsuario" => $email
                        ));
        
                //Verificacion del Codigo del Usuario **************************
                $isset_user_cod = $em->getRepository("BackendBundle:TblUsuarios")->findBy(
                        array(
                          "codUsuario" => $cod_usuario
                        ));
                //Verificamos que el retorno de la Funcion sea = 0 *************                
                if(count($isset_user_cod) == 0 || $identity->email == $email ){
                    $em->persist($usuario);
                    //$em->persist($estados);
                    //$em->persist($tipoUsuario);
                    //$em->persist($tiposFuncionarios);
                    //$em->persist($deptosFuncionales);                    
                    $em->flush();
                    //Seteamos el array de Mensajes a enviar *******************
                    $data = array(
                        "status" => "success",                
                        "code" => "200",                
                        "msg" => "Usuario actualizado !!"                
                    );
                } else {
                    $data = array(
                        "status" => "error",                
                        "code" => "400",                
                        "msg" => "Error al registrar, el Usuario ya existe !!"                
                    );
                }
            }
        } else {
            $data = array(
                "status" => "error",                
                "code" => "400",                
                "msg" => "Autorizacion de Token no valida !!"                
            );
        }
        }
        //Retorno de la Funcion ************************************************
        return $helpers->parserJson($data);
    } // FIN FND00001
    
    
   
    /**
     * Creacion del Controlador: Transforma Fechas Time Stamp
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00002
     */
    public function convertirFechasTimeStampAction( $fecha_time_stamp )
    {
        // Recibe los Parametros de la Funcion, en un Formato TimeStamp ********
        $fecha_time_stamp_In = $fecha_time_stamp;
        
        // Decodificamos el Json con su Campo de Fechas | es nesesario que se **
        // haga la Consulta a la BD por medio de Doctrine( getFechaConsulta())**
        //$fecha_transformar = json_encode($correspondenciaAsigna->getFechaMaxEntrega()->getTimestamp(), true );
        
        // Itaciamosla fecha y le Seteamos el valor TimeStamp del campo de la **
        // Consulta del Doctrine (getFechaConsulta()) **************************
        $fecha_set = new \DateTime();
        $fecha_set->setTimestamp( $fecha_time_stamp_In );
        
        //Salida del Formato a la fecha Convertida *****************************
        $fecha_salida =  $fecha_set->format('Y-m-d');
        
        return $fecha_salida;
    } // FIN | FND00002
    
    
    
    /**
     * @Route("/vinculacion-de-comunicacion", name="vinculacion-de-comunicacion")
     * Creacion del Controlador: Vinculacion de Comunicacion
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Fecha: 2018-02-16
     * Objetivo: Mostrar el listado de las Comunicaciones; para que puedan
     * Incluirse en la Informacion General de la Nueva Comunicacion (Entradas 
     * Relacionarlas con Salidas / Salidas Relacionarlas Con Entradas)
     * @param number $idDeptoFunc Departaento Funcional que pertenece el Func.
     * @param number $idTipoFuncionario Tipo Funcionario que accede al sistema
     * @param number $idFuncionario Funcionario que accede al sistema
     * Funcion: FND00003
     */
    public function comunicacionesVinculantesListAction(Request $request )
    {
        date_default_timezone_set('America/Tegucigalpa');
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");
        
        $em = $this->getDoctrine()->getManager();
        
        // Instanciamos el Json con los Parametros
        $json = $request->get("json", null);
        $params = json_decode($json);
        
        
        //Evaluamos el Json
        if ($json != null) {
            //Variables que vienen del Json ***********************************************
            ////Recogemos el Pais y el Tipo de Institucion ********************************
            $id_depto_funcional  = (isset($params->idDeptoFuncional )) ? $params->idDeptoFuncional : null;
            
            $id_tipo_documento = (isset($params->idTipoDocumento )) ? $params->idTipoDocumento : null;
            
            $array_tipo_comunicacion    = (isset($params->idTipoComunicacion )) ? $params->idTipoComunicacion : null;
            $tipocom_array_convert    = implode(",", $array_tipo_comunicacion);
        
            $opt = 0;
            
            // Condicion de Direccion SRECI
            if( $tipocom_array_convert != 0 || $tipocom_array_convert != null ){
                $opt = 1;                
                $dql = $em->createQuery('SELECT corrEnc.idCorrespondenciaEnc as id, corrEnc.codCorrespondenciaEnc as name, '                    
                        . "corrEnc.codReferenciaSreci as itemName, corrEnc.temaComunicacion as tema "                                                 
                        . 'FROM BackendBundle:TblCorrespondenciaEnc corrEnc '
                        . 'INNER JOIN BackendBundle:TblDepartamentosFuncionales deptoSreci WITH deptoSreci.idDeptoFuncional = corrEnc.idDeptoFuncional '
                        . 'INNER JOIN BackendBundle:TblTipoDocumento tipDoc WITH tipDoc.idTipoDocumento = corrEnc.idTipoDocumento '
                        . 'INNER JOIN BackendBundle:TblTipoComunicacion tipCom WITH tipCom.idTipoComunicacion = corrEnc.idTipoComunicacion '
                        . 'WHERE corrEnc.idDeptoFuncional = :idDeptoFuncional AND '
                        . ' corrEnc.idTipoDocumento = :idTipoDocumento AND '
                        . ' corrEnc.idTipoComunicacion IN (' . $tipocom_array_convert . ' ) '
                        . 'ORDER BY corrEnc.idCorrespondenciaEnc ' )
                        ->setParameter('idDeptoFuncional', $id_depto_funcional )
                        ->setParameter('idTipoDocumento', $id_tipo_documento ); 
            }else {
                $opt = 2;                
                $dql = $em->createQuery('SELECT corrEnc.idCorrespondenciaEnc as id, corrEnc.codCorrespondenciaEnc as name, '                    
                        . "corrEnc.codReferenciaSreci as itemName, corrEnc.temaComunicacion as tema "                                                 
                        . 'FROM BackendBundle:TblCorrespondenciaEnc corrEnc '
                        . 'INNER JOIN BackendBundle:TblDepartamentosFuncionales deptoSreci WITH deptoSreci.idDeptoFuncional = corrEnc.idDeptoFuncional '
                        . 'INNER JOIN BackendBundle:TblTipoDocumento tipDoc WITH tipDoc.idTipoDocumento = corrEnc.idTipoDocumento '
                        . 'INNER JOIN BackendBundle:TblTipoComunicacion tipCom WITH tipCom.idTipoComunicacion = corrEnc.idTipoComunicacion '
                        . 'WHERE corrEnc.idDeptoFuncional = :idDeptoFuncional AND '
                        . ' corrEnc.idTipoDocumento = :idTipoDocumento AND '
                        . ' corrEnc.idTipoComunicacion IN (1,2) '
                        . 'ORDER BY corrEnc.idCorrespondenciaEnc ' )
                        ->setParameter('idDeptoFuncional', $id_depto_funcional )
                        ->setParameter('idTipoDocumento', $id_tipo_documento );
            }

            // Ejecucion del Query
            $deptoFuncionalesSreci = $dql->getResult();

            $totComunicacion = count( $deptoFuncionalesSreci );
            
            // Condicion de la Busqueda
            if ( $totComunicacion >= 1 ) {
                $data = array(
                    "status" => "success",
                    "code"   => 200,
                    "optEje" => $opt,
                    "totReg" => $totComunicacion,
                    "data"   => $deptoFuncionalesSreci
                );
            }else {
                $data = array(
                    "status" => "error",
                    "code"   => 400,
                    "msg"    => "No existe Datos en la Tabla de Sub Direcciones de la SRECI, con los Parametros enviados, verifica la "
                    . " Información. !!"
                );
            }            
        }        
        // Retorno de la Data
        return $helpers->parserJson($data);
    }// FIN | FND00003
    
}

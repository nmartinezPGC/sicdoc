<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\JsonResponse;

//Importamos las Tablas a Relacionar
use BackendBundle\Entity\TblUsuarios;
use BackendBundle\Entity\TblPais;
use BackendBundle\Entity\TblTipoInstitucion;

use BackendBundle\Entity\TblInstituciones;
/**
 * Description of MantInstituciones
 *
 * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
 * @since 1.0
 * Fecha: 2017-11-20
 * Objetivo: Controlador de las Instituciones del Sistema
 */
class MantInstitucionesController extends Controller {
    /* Funcion de Agregar Institucion  *****************************************
     * @Route("/mantenimientos/mantenimiento-institucion-new", name="mantenimiento-institucion-new")
     * Parametros:                                                             * 
     * 1 ) Recibe un Objeto Request con el Metodo POST, el Json de la          *  
     *     Informacion.                                                        * 
     * Creacion del Controlador: Mantenimiento de Instituciones                *      
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>                    * 
     * @since 1.0                                                              * 
     * Funcion: FND00001                                                       * 
     ***************************************************************************/
    public function mantenimientoInstitucionNewAction(Request $request)
    {
        //Instanciamos el Servicio Helpers
        date_default_timezone_set('America/Tegucigalpa');
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");
        
        //Recogemos el Hash y la Autorizacion del Mismo        
        $hash = $request->get("authorization", null);
        //Se Chekea el Token
        $checkToken = $helpers->authCheck($hash);        
        
        // Valida el Token de la Peticion
        if($checkToken == true){
           // Seteo  de la Identidad del Ususario, con las variables del
           // Localsotrage
           $identity = $helpers->authCheck($hash, true); 
        
           // Parametros enviados por el Json
           $json = $request->get("json", null);
                      
            //Comprobamos que Json no es Null
            if ($json != null) {
               // Decodificamos el Json
               $params = json_decode($json);
               
               //Parametros a Convertir                           
               //Datos generales de la Tabla ***********************************                
               //Variables que vienen del Json *********************************
                //$cod_institucion        = ($params->codInstitucion != null) ? $params->codInstitucion : null ;
                $desc_institucion       = ($params->descInstitucion != null) ? $params->descInstitucion : null ;
                
                $perfil_institucion     = ($params->perfilInstitucion != null) ? $params->perfilInstitucion : null ;
                $direccion_institucion  = ($params->direccionInstitucion != null) ? $params->direccionInstitucion : null ;
                $telefono_institucion   = ($params->telefonoInstitucion != null) ? $params->telefonoInstitucion : null ;
                $celular_institucion    = ($params->celularInstitucion != null) ? $params->celularInstitucion : null ;
                $email_institucion      = ($params->emailInstitucion != null) ? $params->emailInstitucion : null ;                
                
                //Datos de Relaciones
                $id_pais_institucion    = ($params->idPaisInstitucion != null) ? $params->idPaisInstitucion : null ;
                $id_tipo_institucion    = ($params->idTipoInstitucion != null) ? $params->idTipoInstitucion : null ;
                
                // Fecha de Solicitud de Cambio
                $fecha_ingreso          = new \DateTime('now');                                                                               
                
                // Envio por Json el Codigo de Usuario
                $id_usuario_creador     = $identity->sub;                                                
                
                //Evalua si la Informacion de los Parametros es Null
                if( $id_usuario_creador != null && $id_usuario_creador != 0 ){
                    // Evalua si se Ingreso la descripcion de la Solicitud *****
                    if( $desc_institucion != null && $perfil_institucion != null ){
                        // Creacion del Metodo Create Query Builder | hace mas Efectiva la *****
                        // Busqueda a la BD  ***************************************************
                        $em = $this
                                ->getDoctrine()
                                ->getManager()
                                ->getRepository("BackendBundle:TblInstituciones");

                        // Declaracion del Alias de la tabla
                        $qb = $em->createQueryBuilder('a');

                        // Query a la BD
                        $qb->select('COUNT(a) ');
                        $qb->where('a.descInstitucion = :validDescInstitucion OR '
                                . 'a.perfilInstitucion LIKE :validperfilInstitucion ');
                        $qb->setParameter('validDescInstitucion', $desc_institucion )
                           ->setParameter('validperfilInstitucion', $perfil_institucion );

                        $count = $qb->getQuery()->getSingleScalarResult();
                        
                        //Verificamos que el retorno de la Funcion sea > 0 *****
                        // Encontro los Datos de la Comunicacion Solicitada ****
                        if( $count == 0 ){
                            
                            $em = $this
                                ->getDoctrine()
                                ->getManager();
                            
                            // Query a la BD
                            // Lanzamos la Consulta Errornea para Llenar la Condicion
                            $lastId = $em->createQueryBuilder()
                                            ->select('MAX(e.idInstitucion)')
                                            ->from('BackendBundle:TblInstituciones', 'e')
                                            ->getQuery()
                                            ->getSingleScalarResult();
                            
                            $lastIdConvert = (string) ( $lastId + 1 );
                                                   
                            // Ingresa en la BD la Solicitud ***************
                            //Seteo de Datos Generales de la tabla: TblInstituciones
                            $mantInstitucionNew = new TblInstituciones();

                            $mantInstitucionNew->setCodInstitucion( $lastIdConvert );
                            $mantInstitucionNew->setFechaIngreso( $fecha_ingreso );
                            
                            // Seteo de las Fechas de la Solicitud
                            $mantInstitucionNew->setDescInstitucion( $desc_institucion );
                            $mantInstitucionNew->setPerfilInstitucion ( $perfil_institucion );
                            $mantInstitucionNew->setDireccionInstitucion( $direccion_institucion );
                            $mantInstitucionNew->setTelefonoInstitucion( $telefono_institucion );
                            $mantInstitucionNew->setCelularInstitucion( $celular_institucion );
                            $mantInstitucionNew->setEmailInstitucion( $email_institucion );
                            

                            //Instanciamos de la Clase TblUsuarios    **********
                            $usuarioCreador = $em->getRepository("BackendBundle:TblUsuarios")->findOneBy(
                                array(
                                   "idUsuario" => $id_usuario_creador                        
                                ));                    
                            $mantInstitucionNew->setIdUsuarioCreador( $usuarioCreador ); //Set de Id de Usuario Creador
                            

                            //Instanciamos de la Clase TblPais    **************
                            $paisInstitucion = $em->getRepository("BackendBundle:TblPais")->findOneBy(
                                array(
                                   "idPais" => $id_pais_institucion                        
                                ));                    
                            $mantInstitucionNew->setIdPais( $paisInstitucion ); //Set de Id Pais de Institucion
                            

                            //Instanciamos de la Clase TblTipoInstitucion  *****
                            $tipoInstitucion = $em->getRepository("BackendBundle:TblTipoInstitucion")->findOneBy(
                                array(
                                   "idTipoInstitucion" => $id_tipo_institucion                        
                                ));                    
                            $mantInstitucionNew->setIdTipoInstitucion( $tipoInstitucion ); //Set de Id Tipo de Institucion
                                                        
                            
                            //Realizar la Persistencia de los Datos y enviar a la BD
                            $em->persist( $mantInstitucionNew );

                            //Realizar la actualizacion en el storage de la BD
                            $em->flush();                         


                            // Mensaje de Respuesta de la API
                            $data = array(
                                "status" => "success",                                
                                "code"   => 200, 
                                "option"   => "datos ingresados",
                                "totalCount" => $count,
                                "msg"    => "La Solicitud fue creada de manera exitosa",
                                //"data"   => count( $isset_cambio_fehas )
                            );                                                                                                                                            
                        }else{
                            $data = array(
                                "status"     => "error",                                
                                "code"       => 400,
                                "totalCount" => $count,                                
                                "msg"   => "Error 400, Ya existe una institucion con este nombre: ". $perfil_institucion . 
                                           " , y Iniciales " . $desc_institucion  . " por favor, valide que los Datos sean correctos !!"
                            ); 
                        }
                    }else{
                      //Array de Mensajes
                        $data = array(
                           "status" => "error",                       
                           "code"   => 400,
                           "option"   => "faltan parametros",
                           "msg"   => "Institucion no creada, falta ingresar las Iniciales o la Descripcion. !!"
                        );  
                    }                    
                }else{
                   //Array de Mensajes
                    $data = array(
                       "status" => "error",                       
                       "code"   => 400, 
                       "option" => "falta ingresar el id de usuario",
                       "msg"   => "Institucion no creada, falta ingresar el Usuario creador !!"
                    );
                }                
            }else{
             //Array de Mensajes
             $data = array(
                "status" => "error",
                "desc"   => "Eror al Enviar el Json, el Json no ha sido enviado",
                "code"   => 504, 
                "msg"   => "Solicitud no creada, falta ingresar los parametros !!"
             );
            }         
        }else{
            $data = array(
                "status" => "error",
                "desc"   => "El Token, es invalido",    
                "code" => 500,                
                "msg" => "Autorizacion de Token no valida !!"                
            );
        }
        // Retornamos la Data
        return $helpers->parserJson($data);
    }//FIN | FND00001
    
    
    /* Funcion de Editar Institucion  ******************************************
     * @Route("/mantenimientos/mantenimiento-institucion-edit", name="mantenimiento-institucion-edit")
     * Parametros:                                                             * 
     * 1 ) Recibe un Objeto Request con el Metodo POST, el Json de la          *  
     *     Informacion.                                                        * 
     * 2 ) Recibe el Codigo de la Institucion por medio de la Url.             *
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>                    *
     * @since 1.0                                                              * 
     * Funcion: FND00002                                                       * 
     ***************************************************************************/
    public function mantenimientoInstitucionEditAction(Request $request) 
    {
        //Instanciamos el Servicio Helpers
        date_default_timezone_set('America/Tegucigalpa');
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");
        
        //Recogemos el Hash y la Autorizacion del Mismo        
        $hash = $request->get("authorization", null);
        //Se Chekea el Token
        $checkToken = $helpers->authCheck($hash);        
        
        // Valida el Token de la Peticion
        if($checkToken == true){
           // Seteo  de la Identidad del Ususario, con las variables del
           // Localsotrage
           $identity = $helpers->authCheck($hash, true); 
        
           // Parametros enviados por el Json
           $json = $request->get("json", null);
                      
            //Comprobamos que Json no es Null
            if ($json != null) {
               // Decodificamos el Json
               $params = json_decode($json);
               
               //Parametros a Convertir                           
               //Datos generales de la Tabla ***********************************                
               //Variables que vienen del Json *********************************
                $id_institucion         = ($params->idInstitucion != null) ? $params->idInstitucion : null ;
                //$cod_institucion        = ($params->codInstitucion != null) ? $params->codInstitucion : null ;
                $desc_institucion       = ($params->descInstitucion != null) ? $params->descInstitucion : null ;
                
                $perfil_institucion     = ($params->perfilInstitucion != null) ? $params->perfilInstitucion : null ;
                $direccion_institucion  = ($params->direccionInstitucion != null) ? $params->direccionInstitucion : null ;
                $telefono_institucion   = ($params->telefonoInstitucion != null) ? $params->telefonoInstitucion : null ;
                $celular_institucion    = ($params->celularInstitucion != null) ? $params->celularInstitucion : null ;
                $email_institucion      = ($params->emailInstitucion != null) ? $params->emailInstitucion : null ;                
                
                //Datos de Relaciones
                $id_pais_institucion    = ($params->idPaisInstitucion != null) ? $params->idPaisInstitucion : null ;
                $id_tipo_institucion    = ($params->idTipoInstitucion != null) ? $params->idTipoInstitucion : null ;
                
                // Fecha de Solicitud de Cambio
                $fecha_ingreso          = new \DateTime('now');                                                                               
                
                // Envio por Json el Codigo de Usuario
                $id_usuario_creador     = $identity->sub;                                                
                
                //Evalua si la Informacion de los Parametros es Null
                if( $id_institucion != null && $id_institucion != 0 ){
                    // Evalua si se Ingreso la descripcion de la Solicitud *****
                    if( $desc_institucion != null && $perfil_institucion != null ){
                        // Creacion del Metodo Create Query Builder | hace mas Efectiva la *****
                        // Busqueda a la BD  ***************************************************
                        $em = $this
                                ->getDoctrine()
                                ->getManager()
                                ->getRepository("BackendBundle:TblInstituciones");

                        // Declaracion del Alias de la tabla
                        $qb = $em->createQueryBuilder('a');

                        // Query a la BD
                        $qb->select('COUNT(a) ');
                        $qb->where('a.idInstitucion = :validIdInstitucion ');
                        $qb->setParameter('validIdInstitucion', $id_institucion );

                        $count = $qb->getQuery()->getSingleScalarResult();
                        
                        //Verificamos que el retorno de la Funcion sea > 0 *****
                        // Encontro los Datos de la Comunicacion Solicitada ****
                        if( $count > 0 ){
                            
                            $em = $this
                                ->getDoctrine()
                                ->getManager();
                                                                                  
                            // Ingresa en la BD la Solicitud ***************
                            //Seteo de Datos Generales de la tabla: TblInstituciones                            
                            // Busca el Id del Oficio Detalle **********************
                            // Primero Buscamos el idCorrespondenciaEnc de la Tabla*
                            // Padre para Ubicarlo en la Tabla Hijo ****************
                            $mantInstitucionEdit = $em->getRepository("BackendBundle:TblInstituciones")->findOneBy(
                                array(
                                    "idInstitucion" => $id_institucion
                                ));


                            //$mantInstitucionEdit->setCodInstitucion( $cod_institucion );
                            //$mantInstitucionEdit->setFechaIngreso( $fecha_ingreso );
                            
                            // Seteo de las Fechas de la Solicitud
                            $mantInstitucionEdit->setDescInstitucion( $desc_institucion );
                            $mantInstitucionEdit->setPerfilInstitucion ( $perfil_institucion );
                            $mantInstitucionEdit->setDireccionInstitucion( $direccion_institucion );
                            $mantInstitucionEdit->setTelefonoInstitucion( $telefono_institucion );
                            $mantInstitucionEdit->setCelularInstitucion( $celular_institucion );
                            $mantInstitucionEdit->setEmailInstitucion( $email_institucion );
                            

                            //Instanciamos de la Clase TblUsuarios    **********
                            $usuarioModificador = $em->getRepository("BackendBundle:TblUsuarios")->findOneBy(
                                array(
                                   "idUsuario" => $id_usuario_creador                        
                                ));                    
                            $mantInstitucionEdit->setIdUsuarioCreador( $usuarioModificador ); //Set de Id de Usuario Creador
                            

                            //Instanciamos de la Clase TblPais    **************
                            $paisInstitucion = $em->getRepository("BackendBundle:TblPais")->findOneBy(
                                array(
                                   "idPais" => $id_pais_institucion                        
                                ));                    
                            $mantInstitucionEdit->setIdPais( $paisInstitucion ); //Set de Id Pais de Institucion
                            

                            //Instanciamos de la Clase TblTipoInstitucion  *****
                            $tipoInstitucion = $em->getRepository("BackendBundle:TblTipoInstitucion")->findOneBy(
                                array(
                                   "idTipoInstitucion" => $id_tipo_institucion                        
                                ));                    
                            $mantInstitucionEdit->setIdTipoInstitucion( $tipoInstitucion ); //Set de Id Tipo de Institucion
                                                        
                            
                            //Realizar la Persistencia de los Datos y enviar a la BD
                            $em->persist( $mantInstitucionEdit );

                            //Realizar la actualizacion en el storage de la BD
                            $em->flush();                         


                            // Mensaje de Respuesta de la API
                            $data = array(
                                "status" => "success",                                
                                "code"   => 200, 
                                "option"   => "datos modificados",
                                "totalCount" => $count,
                                "msg"    => "La Solicitud fue Modificada de manera exitosa",
                                //"data"   => count( $isset_cambio_fehas )
                            );                                                                                                                                            
                        }else{
                            $data = array(
                                "status"     => "error",                                
                                "code"       => 400,
                                "totalCount" => $count,                                
                                "msg"   => "Error 400, Ya existe una institucion con este nombre: ". $perfil_institucion . 
                                           " , por favor, valide que los Datos sean correctos !!"
                            ); 
                        }
                    }else{
                      //Array de Mensajes
                        $data = array(
                           "status" => "error",                       
                           "code"   => 400,
                           "option"   => "faltan parametros",
                           "msg"   => "Institucion no creada, falta ingresar las Iniciales o la Descripcion. !!"
                        );  
                    }                    
                }else{
                   //Array de Mensajes
                    $data = array(
                       "status" => "error",                       
                       "code"   => 400, 
                       "option" => "falta ingresar el id de usuario",
                       "msg"   => "Institucion no modificada, contacte al Administrador !!"
                    );
                }                
            }else{
             //Array de Mensajes
             $data = array(
                "status" => "error",
                "desc"   => "Eror al Enviar el Json, el Json no ha sido enviado",
                "code"   => 504, 
                "msg"   => "Solicitud no creada, falta ingresar los parametros !!"
             );
            }         
        }else{
            $data = array(
                "status" => "error",
                "desc"   => "El Token, es invalido",    
                "code" => 500,                
                "msg" => "Autorizacion de Token no valida !!"                
            );
        }
        // Retornamos la Data
        return $helpers->parserJson($data);
    }//FIN | FND00002
    
    
    
    /* Funcion de Buscar Institucion  ******************************************
     * @Route("/mantenimientos/mantenimiento-institucion-busca", name="mantenimiento-institucion-busca")
     * Creacion del Controlador: Busqueda de la Institucion                    * 
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>                    * 
     * @since 1.0                                                              * 
     * Funcion: FND00003                                                       * 
     ***************************************************************************/
    public function mantenimientoInstitucionSearchAction(Request $request)
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
            
            // Evaluamos que Parametro nos enviaron
            if( $pais_institucion != null && $tipo_institucion != null){
                // Query para Obtener todos las Instituciones segun Parametros de la Tabla: TblInstituciones                
                $query = $em->createQuery('SELECT inst.idInstitucion, inst.codInstitucion, '
                                    . 'inst.descInstitucion, inst.perfilInstitucion, '
                                    . 'inst.direccionInstitucion, inst.telefonoInstitucion, '
                                    . 'inst.celularInstitucion, inst.emailInstitucion, '
                                    ."DATE_SUB(inst.fechaIngreso, 0, 'DAY') AS fechaIngreso, " 
                                    ."pa.idPais, pa.descPais, tinst.idTipoInstitucion, tinst.descTipoInstitucion " 
                                    . 'FROM BackendBundle:TblInstituciones inst '                                    
                                    . 'INNER JOIN BackendBundle:TblPais pa WITH pa.idPais = inst.idPais '
                                    . 'INNER JOIN BackendBundle:TblTipoInstitucion tinst WITH  tinst.idTipoInstitucion = inst.idTipoInstitucion '
                                    . 'INNER JOIN BackendBundle:TblUsuarios user WITH  user.idUsuario = inst.idUsuarioCreador '
                                    . 'WHERE inst.idPais = :idPais AND inst.idTipoInstitucion = :idTipoInstitucion ' 
                                    . 'ORDER BY inst.descInstitucion, pa.descPais ASC')
                    ->setParameter('idPais', $pais_institucion)->setParameter('idTipoInstitucion', $tipo_institucion ) ;
                    
                $institucionesSreci = $query->getResult();
                
            } else {
                // Query para Obtener todos las Instituciones de la Tabla: TblInstituciones                    
                // Tabla: TblDepartamentosFuncionales ******************************            
                $query = $em->createQuery('SELECT inst.idInstitucion, inst.codInstitucion, '
                                    . 'inst.descInstitucion, inst.perfilInstitucion, '
                                    . 'inst.direccionInstitucion, inst.telefonoInstitucion, '
                                    . 'inst.celularInstitucion, inst.emailInstitucion, '
                                    ."DATE_SUB(inst.fechaIngreso, 0, 'DAY') AS fechaIngreso, "
                                    ."pa.idPais, pa.descPais, tinst.idTipoInstitucion, tinst.descTipoInstitucion "
                                    . 'FROM BackendBundle:TblInstituciones inst '                                    
                                    . 'INNER JOIN BackendBundle:TblPais pa WITH pa.idPais = inst.idPais '
                                    . 'INNER JOIN BackendBundle:TblTipoInstitucion tinst WITH  tinst.idTipoInstitucion = inst.idTipoInstitucion '
                                    . 'INNER JOIN BackendBundle:TblUsuarios user WITH  user.idUsuario = inst.idUsuarioCreador '                                    
                                    . 'ORDER BY inst.descInstitucion, pa.descPais ASC') ;                    
                    
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
    }//FIN | FND00003
    
}

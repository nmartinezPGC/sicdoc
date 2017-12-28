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
                $cod_institucion        = ($params->codInstitucion != null) ? $params->codInstitucion : null ;
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
    public function mantenimientoInstitucionEditAction(Request $request, $idInstitucion = null) 
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
                $cod_institucion        = ($params->codInstitucion != null) ? $params->codInstitucion : null ;
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
                                                                                  
                            // Ingresa en la BD la Solicitud ***************
                            //Seteo de Datos Generales de la tabla: TblInstituciones
                            $mantInstitucionNew = new TblInstituciones();

                            $mantInstitucionNew->setCodInstitucion( $cod_institucion );
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
               
               $em = $this
                ->getDoctrine()
                ->getManager();
               
               //Parametros a Convertir                           
               //Datos generales de la Tabla ***********************************                
               //Variables que vienen del Json *********************************
               //$cod_comunicacion = $request->query->get("codCorrespondencia ", null);
               $cod_comunicacion     = ($params->codCorrespondencia != null) ? $params->codCorrespondencia : null ;                             
                
                //Evalua si la Informacion de los Parametros es Null
                if( $cod_comunicacion != null ){
                    //Verificacion del Codigo de la Correspondencia *******************
                    $isset_corresp_cod = $em->getRepository("BackendBundle:TblCorrespondenciaEnc")->findOneBy(
                        array(
                             "codCorrespondenciaEnc" => $cod_comunicacion
                    ));
                    
                    //Verificamos que el retorno de la Funcion sea > 0 *****
                    // Encontro los Datos de la Comunicacion Solicitada ****
                    if( count($isset_corresp_cod) > 0 ){ 
                        // Asignacion de los valores de la Consulta
                        //$cod_usuario_creador = $isset_corresp_cod->getIdUsuario();
                        //Array de Mensajes
                        $data = array(
                           "status" => "success",                       
                           "code"   => 200, 
                           "msg"    => "Datos encontrados !!",
                           "data"   => $isset_corresp_cod
                        );  
                    } else {
                        //Array de Mensajes
                        $data = array(
                           "status" => "error",                       
                           "code"   => 400, 
                           "msg"    => "Datos no encontrados, no existe información con este código de Comunicación !!"                           
                        );
                    }                 
                }else{
                    //Array de Mensajes
                    $data = array(
                       "status" => "error",                      
                       "code"   => 400, 
                       "msg"   => "Falta ingresar el código de la comunicación!!"
                    );
                }                                   
            }else{
             //Array de Mensajes
             $data = array(
                "status" => "error",                
                "code"   => 400, 
                "msg"   => "Datos no encontrados, falta ingresar los parametros !!"
             );
            }         
        }else{
            $data = array(
                "status" => "error",
                "desc"   => "El Token, es invalido",    
                "code" => "400",                
                "msg" => "Autorizacion de Token no valida !!"                
            );
        }
        // Retornamos la Data
        return $helpers->parserJson($data);
    }//FIN | FND00003
    
}

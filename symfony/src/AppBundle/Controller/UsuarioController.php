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

/**
 * Description of UsuarioController
 *
 * @author Nahum Martinez
 */
class UsuarioController extends Controller{
    //put your code here
    
    
    /**
     * @Route("/new", name="new")
     * Creacion del Controlador: Usuarios
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00001
     */
    public function newAction(Request $request) {
        //Instanciamos el Servicio Helpers
        $helpers = $this->get("app.helpers");
        
        $json = $request->get("json", null);
        $params = json_decode($json);
        
        //Array de Mensajes
        $data = $data = array(
                "status" => "error",                
                "code" => "400",                
                "msg" => "Usuario no creado, hay problemas en los Datos !!"                
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
                $password != null && $nombre1 != null && $apellido1 != null ){
                //Instanciamos la Entidad TblUsuario *****************************************                
                $usuario = new TblUsuarios();                
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
                $pwd = hash('sha256', $password);                
                $usuario->setPasswordUsuario($pwd);                
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
                if(count($isset_user_cod) == 0){
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
                        "msg" => "Usuario creado !!"                
                    );
                } else {
                    $data = array(
                        "status" => "error",                
                        "code" => "400",                
                        "msg" => "Error al registrar, el Usuario ya existe !!"                
                    );
                }
            }
        }
        //Retorno de la Funcion ************************************************
        return $helpers->parserJson($data);
    } // FIN FND00001
    
    
    /**
     * @Route("/edit", name="edit")
     * Creacion del Controlador: Usuarios
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00002
     */
    public function editAction(Request $request) {
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
    } // FIN FND00002
    
    
    /**
     * @Route("/upload", name="upload")
     * Creacion del Controlador: Usuarios
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00003
     */
    public function uploadImageAction(Request $request) {
        //Instanciamos el Servicio Helpers
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
        $em = $this->getDoctrine()->getManager();
        //Buscamos el registro por el Id de Usaurio
        $usuario = $em->getRepository("BackendBundle:TblUsuarios")->findOneBy(
            array(
               "idUsuario" => $identity->sub
            ));
            //Recoger el Fichero que viene por el POST y lo guardamos el HD
            $file = $request->files->get("image");
            //Se verifica que el fichero no venga Null
            if (!empty($file) && $file != null) {
                //Obtenemos la extencion del Fichero
                $ext = $file->guessExtension();
                //Comprobamos que la Extencion sea Aceptada
                if ($ext == "jpeg" || $ext == "jpg" || $ext == "png" || $ext == "gif") {                   
                    // Concatenmos al Nombre del Fichero la Fecha y la Extencion
                    $file_name = time().".".$ext;
                    //Movemos el Fichero
                    $file->move("uploads/users", $file_name);

                    //Seteamos el valor de la Imagen dentro de la Tabla:Tblusuarios+
                    $usuario->setImagenUsuario($file_name);
                    $em->persist($usuario);
                    $em->flush();
                
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
    } // FIN FND00003
    
}

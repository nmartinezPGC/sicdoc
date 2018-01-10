<?php

namespace AppBundle\Services;

use Firebase\JWT\JWT;
/**
 * Description of JwtAuth
 *
 * @author Nahum Martinez
 */
class JwtAuth {
    //Propiedades de la Clase
    public $manager = null;
    public $key = "clave-secreta";
    //Metodo Constructor de la Clase
    public function __construct($manager) {
        $this->manager = $manager;
        $this->key = "clave-secreta";
    }
    
    public function signUp($email, $passwod, $getHash = null) {
        //Instanciamos el Servicio Helpers y Jwt
        //$helpers = $this->get("app.helpers");
        
        $key = $this->key;
        
        //Seteo de los Campos Email y Password de la Tabla TblUsuarios
        $user = $this->manager->getRepository('BackendBundle:TblUsuarios')->findOneBy(
                    array(
                        "emailUsuario" => $email,
                        "passwordUsuario" => $passwod
                    )                
                );
                
        //Variable de Registro y la validacion
        $signup = false;
        if (is_object($user)) {
            $signup = true;
        }
        //Evalua el valor de la variable $signup
        if ($signup == true ) {
            // Obtenemos el tipo de Usuario de la BD
            // Query para Obtener el Departamento al que corresponde de la Tabla: TblTipoUsuario
            $tipo_user = $this->manager->getRepository("BackendBundle:TblTipoUsuario")->findOneBy(
                array(
                    "idTipoUsuario" => $user->getIdTipoUsuario()
                ));        
        
            
            // Query para Obtener el Departamento al que corresponde de la Tabla: TblDepartamentosFuncionales
            $depto_user = $this->manager->getRepository("BackendBundle:TblDepartamentosFuncionales")->findOneBy(
                array(
                    "idDeptoFuncional" => $user->getIdDeptoFuncional()
                ));
            
        
            // Query para Obtener el Departamento al que corresponde de la Tabla: TblDireccionesSreci
            $direccion_user = $this->manager->getRepository("BackendBundle:TblDireccionesSreci")->findOneBy(
                array(
                    "idDireccionSreci" => $depto_user->getIdDireccionSreci()
                ));
            
            
            // Query para Obtener el Tipo de Funcionario al que corresponde de la Tabla: TblTiposFuncionarios
            $tipo_func_user = $this->manager->getRepository("BackendBundle:TblTiposFuncionarios")->findOneBy(
                array(
                    "idTipoFuncionario" => $user->getIdTipoFuncionario()
                ));
            
            // Query para Obtener el Funcionario al que corresponde de la Tabla: TblTiposFuncionarios
            $funcionario_user = $this->manager->getRepository("BackendBundle:TblFuncionarios")->findOneBy(
                array(
                    "idFuncionario" => $user->getIdUsuario()
                ));
            
            //Generamos el Token
            $token = array(           
                "sub"              => $user->getIdUsuario(),
                "codUser"          => $user->getCodUsuario(),
                "iniUser"          => $user->getInicialesUsuario(),
                "password"         => $user->getPasswordUsuario(),
                "email"            => $user->getEmailUsuario(),
                "nombre"           => $user->getNombre1Usuario(),
                "apellido"         => $user->getApellido1Usuario(),
                "imagenUsuario"    => $user->getImagenUsuario(),
                "idDireccion"      => $direccion_user->getIdDireccionSreci(),
                "inicialesDireccion"      => $direccion_user->getInicialesDireccionSreci(),
                "idDeptoFuncional" => $depto_user->getIdDeptoFuncional(),
                "inicialesDeptoFuncional" => $depto_user->getInicialesDeptoFuncional(),
                "idTipoUser"       => $tipo_user->getIdTipoUsuario(),
                "idTipoFunc"       => $tipo_func_user->getIdTipoFuncionario(),
                "idFuncionario"    => $funcionario_user->getIdFuncionario(),
                "iat" => time(),
                "exp" => time() + (7 * 24 * 60 * 60)
                //"data" => $helpers->parserJson($user)
            );
            //Definicion de la Salida del Token
            $jwt = JWT::encode($token, $key, 'HS256') ;
            $decoded = JWT::decode($jwt, $key, array('HS256')) ;
            
            if ($getHash != null || $getHash != false ) {
                //return $jwt;
                return array("status" => "success", "data" => $jwt);
            } else {
                //return $decoded;
                return array("status" => "success", "data" => $decoded);
            }            
            //return array("status" => "success", "data" => "Login success!!");
        } else {
            return array("status" => "error", "data" => "Usuario o Contraseña invalidas, revisa los datos otra ves!!");
        }
    }//FIN | signUp
        //
        
        //Metodo de validacion del Token
        public function checkToken($jwt, $getIdentity = false) {
            $key = $this->key;
            $auth = false;
            
            try {
                $decoded = JWT::decode($jwt, $key, array('HS256')) ;
            } catch (\UnexpectedValueException $e){
                $auth = false;
            } catch (\DomainException $e){
                $auth = false;
            }

            //Verifica la Decodificacion del Token; con el valor del Id
            if (isset($decoded->sub)) {
                $auth = true;
            } else {
                $auth = false;
            }
            
            //Pregunta por el valor de Identity
            if ($getIdentity == true) {
                return $decoded;
            }else{
                return $auth;
            }
        }//FIN | checkToken
        
    
}

<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }
    
    /**
     * @Route("/pais", name="pais")
     * Creacion del Controlador: Lista de Paiese
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00001
     */
    public function paisAction(Request $request)
    {
        //Instanciamos el Servicio Helpers
        $helpers = $this->get("app.helpers");
        //Recogemos el Hash y la Autrizacion del Mismo
        $hash = $request->get("authorization", null);
        //Se Chekea el Token
        $checkToken = $helpers->authCheck($hash, true);
        var_dump($checkToken);
        die();
        //return $helpers->parserJson($checkToken);
    }//FIN | FND00001
    
    /**
     * @Route("/login", name="login")
     * Creacion del Controlador: Login
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00002
     */
    public function loginAction(Request $request)
    {
        //Instanciamos el Servicio Helpers y Jwt
        $helpers = $this->get("app.helpers");
        $jwt_auth = $this->get("app.jwt_auth");
        //Recibimos Json por POST
        $json = $request->get("json", null);
        //Evalua el Resultado de $json
        if($json != null){
            $params = json_decode($json);
            //Evalua el Resultado del Email y el Password
            $email =  (isset($params->email)) ? $params->email : null;
            $password =  (isset($params->password)) ? $params->password : null;
            $getHash =  (isset($params->gethash)) ? $params->gethash : null;
            
            //Validamos el Email
            $emailConstraint = new Assert\Email();
            $emailConstraint->message = "El Email no es valido!!";
            
            $valid_email = $this->get("validator")->validate($email, $emailConstraint);
            //Cifrar la Contraseña *****************************************
            $pwd = hash('sha256', $password);
            //Valida el Conteo de la Funcion de validacion del Mail
            if(count($valid_email) == 0 && $password != null){                
                //Validacion del Token
                if($getHash == null || $getHash == "false"){
                  //Ejecucion del JWT;
                  $signup = $jwt_auth->signUp($email, $pwd);
                  //return $helpers->parserJson($signup);                  
                } else {
                  //Ejecucion del JWT;
                  $signup = $jwt_auth->signUp($email, $pwd, true);
                }
                //Retorno del Hash con JWT
                return new JsonResponse($signup);
            }else{
                //echo 'Data incorrect !!';
                return $helpers->parserJson(array(
                    "code"   => 400,
                    "status" => "error",
                    "data"   => "Falta ingresar información para continuar !!"
                ));                
            }            
        }else{
            return $helpers->parserJson(array(
                    "status" => "error",
                    "data" => "Send Json with Post!! !!"
                ));
        }
        
        //return $helpers->parserJson($paises);
    }//FIN | FND00002
    
}

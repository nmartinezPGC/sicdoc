<?php
/**
 * Description of Helpers
 *
 * @author Nahum Martinez
 */
namespace AppBundle\Services;

class Helpers {
    //put your code here
    public $jwt_autht;
    //Definicion del Constructor y Incluimos el Autenticador
    public function __construct($jwt_autht) {
        $this->jwt_autht = $jwt_autht;
    }
    
    //Servicio para Chekear el servicio Auth
    public function authCheck($hash, $getIdentity = false) {
        $jwt_auth = $this->jwt_autht;
        $auth = false;
        
        if ($hash != null) {
            if ($getIdentity == false) {
                $check_token = $jwt_auth->checkToken($hash);
                if ($check_token == true) {
                    $auth = true;
                }
            } else {
                $check_token = $jwt_auth->checkToken($hash, true);
                if (is_object($check_token)){
                    $auth = $check_token;
                }
            }
        }
        return $auth;        
    }

    /**
     * Transformacion de ls Datos a: Json, con Normalizer
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00001 
     */
    public function parserJson($data) {
        //Instanciamos el Objeto Normalizer
        $normalizer = array( new \Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer() );
        //Instanciamos el Objeto Encoder de PHP
        $encoders = array("json" => new \Symfony\Component\Serializer\Encoder\JsonEncoder());
        //Instanciamos el Objeto serializer de PHP
        $serializer = new \Symfony\Component\Serializer\Serializer($normalizer, $encoders);
        //Definicion del Objeto Json de PHP
        $json = $serializer->serialize($data, 'json');
       
        //Respuesta del Objeto Json a Formato Html
        $response = new \Symfony\Component\HttpFoundation\Response();
        $response->setContent($json);
        $response->headers->set("Content-Type", "application/json");
        
        //Retornamos la Respuesta
        return $response;        
    }//Fin de Funcion: FND00001
    
}

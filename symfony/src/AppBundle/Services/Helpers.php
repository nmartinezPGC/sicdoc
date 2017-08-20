<?php
/**
 * Description of Helpers
 *
 * @author Nahum Martinez
 */
namespace AppBundle\Services;

class Helpers {
    //put your code here
    
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

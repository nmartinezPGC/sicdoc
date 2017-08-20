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
    
    //Metodo Constructor de la Clase
    public function __construct($manager) {
        $this->manager = $manager;
    }
    
    public function signUp($email, $passwod, $getHash = null) {
        $key = "clave-secreta";
        
        $user = $this->manager->getRepository('BackendBundle:TblUsuarios')->findOneBy(
                    array(
                        ""
                    )                
                );        
    }
    
}

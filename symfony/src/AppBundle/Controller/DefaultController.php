<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

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
        //Definimos el Objeto Entitir Manager ($em)
        $em = $this->getDoctrine()->getManager();
        //Definicion del Repositorio de la Entidad Paises
        $paises = $em->getRepository('BackendBundle:TblPais')->findAll();
        //Convertimos el Objeto Json, llamando la Funcion parserJson(); y le
        //damos como parametro los Paises ($paises)
        return $helpers->parserJson($paises);
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
        //Instanciamos el Servicio Helpers
        $helpers = $this->get("app.helpers");
        //Recibimos Json por POST
        $json = $request->get("json", null);
        //Evalua el Resultado de $json
        if($json != null){
            $params = json_decode($json);
            //Evalua el Resultado del Email y el Password
            $email =  (isset($params->email)) ? $params->email : null;
            $password =  (isset($params->password)) ? $params->password : null;
            
            //Validamos el Email
            $emailConstraint = new Assert\Email();
            $emailConstraint->message = "El Email no es valido!!";
            
            $valid_email = $this->get("validator")->validate($email, $emailConstraint);
            if(count($valid_email) == 0 && $password != null){                
                echo 'Data success!!';
            }else{
                echo 'Data incorrect!!';
            }
            
        }else{
            echo "Send Json with Post!!";
            die();
        }
        
        //return $helpers->parserJson($paises);
    }//FIN | FND00002
    
}

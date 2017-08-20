<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
     * Creacion del Servicio: Lista de Paiese
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00001
     */
    public function paisAction(Request $request)
    {
        //Definimos el Objeto Entitir Manager ($em)
        $em = $this->getDoctrine()->getManager();
        //Definicion del Repositorio de la Entidad Paises
        $paises = $em->getRepository('BackendBundle:TblPais')->findAll();
        //var_dump($paises);
        //die();
        //Convertimos el Objeto Json, llamando la Funcion parserJson(); y le
        //damos como parametro los Paises ($paises)
        return $this->parserJson($paises);
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

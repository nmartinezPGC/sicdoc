<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\JsonResponse;
//Importamos las Tablas a Relacionar
use BackendBundle\Entity\TblInstituciones;
use BackendBundle\Entity\TblFuncionarios;
use BackendBundle\Entity\TblContactos;
use Swift_MessageAcceptanceTest;

/**
 * Description of ContactosController
 * Objetivo: Regiestro de COntactos
 * @author Nahum Martinez
 */
class ContactosController extends Controller{    
    
    /**
     * @Route("contactos/contactos-consulta", name="contactos/contactos-consulta")
     * Creacion del Controlador: Consulta de Contactos SRECI
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00001
     * Descripcion: Consulta que muestra la informacion general de los Contactos
     *              atraves de Parametros de Search
     * @param array $request Filtros de Contactos a Buscar
     */
    public function consultaGeneralContactoListAction(Request $request )
    {
        //Instanciamos el Servicio Helpers y Jwt  ******************************
        $helpers = $this->get("app.helpers");
        
        // Declaramos el Entity Manager  ***************************************
        $em = $this->getDoctrine()->getManager();
        
        // Query para Obtener todos los Contactos de la Tabla: TblContactos ****
        $contactos = $em->getRepository("BackendBundle:TblContactos")->findAll();
        
        // Condicion de la Busqueda
        if (count($contactos) >= 1 ) {
            $data = array(
                "status" => "success",
                "code"   => 200,
                "msg"    => "Se han encontrados los datos de Contactos",
                "data"   => $contactos
            );
        }else {
            $data = array(
                "status" => "error",
                "code"   => 400,
                "msg"    => "No existe Datos en la Tabla de Contactos !!"
            );
        }        
        return $helpers->parserJson($data);
    }//FIN | FND00001
    
    
    /**
     * @Route("contactos/contactos-new", name="contactos/contactos-new")
     * Creacion del Controlador: Consulta de Contactos SRECI
     * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
     * @since 1.0
     * Funcion: FND00002
     * Descripcion: Agregar Nuevo Contacto
     * @param array $request Campos a Ingresar
     */
    public function contactoNewAction(Request $request )
    {
        //Instanciamos el Servicio Helpers y Jwt  ******************************
        $helpers = $this->get("app.helpers");
        
        // INI | Valid Json
        if ($json != null) {
        //Variables que vienen del Json ************************************
        //Seccion de Identificacion ****************************************
        //El ID no se incluye en el Ingreso; ya que es un campo Serial                   
            $cod_contacto     = (isset($params->codContacto)) ? $params->codContacto : null;
            
            // Datos Generales
            $nombre1_contacto = (isset($params->primerNombre) && ctype_alpha($params->primerNombre) ) ? $params->primerNombre : null;
            $nombre2_contacto = (isset($params->segundoNombre) && ctype_alpha($params->segundoNombre) ) ? $params->segundoNombre : null;
            $apellido1_contacto = (isset($params->primerApellido) && ctype_alpha($params->primerApellido) ) ? $params->primerApellido : null;
            $apellido2_contacto = (isset($params->segundoApellido) && ctype_alpha($params->segundoApellido) ) ? $params->segundoApellido : null;
            
            // Datos de Contacto
            $email_1          = (isset($params->email1Contacto)) ? $params->email1Contacto  : null;            
            $email_2          = (isset($params->email2Contacto)) ? $params->email2Contacto  : null;            
            $telefono_1       = (isset($params->telefono1Contacto)) ? $params->telefono1Contacto  : null;            
            $telefono_2       = (isset($params->telefono2Contacto)) ? $params->telefono2Contacto  : null;            
            $celular_1        = (isset($params->celular1Contacto)) ? $params->celular1Contacto  : null;            
            $celular_2        = (isset($params->celular2Contacto)) ? $params->celular2Contacto  : null;            
                        
            // Relaciones de Tablas
            $id_institucion   = (isset($params->idInstitucion)) ? $params->idInstitucion  : null;
            $id_funcionario   = (isset($params->idFuncionario)) ? $params->idFuncionario  : null;
        
            //Verificacion del Codigo y Email en la Tabla: TblContactos ********                
            $isset_contact_mail = $em->getRepository("BackendBundle:TblContactos")
                ->findOneBy(
                    array(
                      "email1Contacto" => $email_1
                    ));

            //Verificacion del Nombre y Apellido *******************************
            $isset_contact_cod = $em->getRepository("BackendBundle:TblContactos")
                ->findOneBy(
                    array(
                      "nombre1Contacto"   => $nombre1_contacto,
                      "apellido1Contacto" => $apellido1_contacto 
                    ));
            
            if( count($isset_contact_mail) == 0 && count($isset_contact_cod) == 0 ){
                // Declaramos el Entity Manager  *******************************
                $em = $this->getDoctrine()->getManager();

                // Query para Obtener todos los Contactos de la Tabla: TblContactos ****
                $contactos = $em->getRepository("BackendBundle:TblContactos")->findAll();

                // Validar que los Campos no vengan vacios
                if ( $nombre1_contacto != null && $apellido1_contacto == 0 && 
                     $id_funcionario != 0 && $id_institucion != 0 ){
                    //Instanciamos la Entidad TblContactos *********************
                    $contactoNew = new TblContactos();
                    //Seteamos los valores de Identificacion *******************
                    $contactoNew->setCodContacto($cod_usuario);                
                    $contactoNew->setNombre1Contacto($nombre1_contacto);
                    $contactoNew->setNombre2Contacto($nombre2_contacto);
                    $contactoNew->setApellido1Contacto($apellido1_contacto);
                    $contactoNew->setApellido2Contacto($apellido2_contacto);
                    
                    // Seteamos los valores de contacto
                    $contactoNew->setEmail1Contacto($email_1);
                    $contactoNew->setEmail2Contacto($email_2);
                    $contactoNew->setCelular1Contacto($celular_1);
                    $contactoNew->setCelular2Contacto($celular_2);
                    $contactoNew->setTelefono1Contacto($telefono_1);
                    $contactoNew->setTelefono2Contacto($telefono_2);

                    //Seteamos los valores de Relaciones de Tablas *************
                    //Instancia a la Tabla: TblInstituciones *******************                
                    $institucion = $em->getRepository("BackendBundle:TblInstituciones")
                        ->findOneBy(
                            array(
                                "idInstitucion" => $id_institucion
                            )) ;
                    $contactoNew->setIdInstitucion($institucion);

                    //Instancia a la Tabla: TblFuncionarios *********************                
                    $funcionarios = $em->getRepository("BackendBundle:TblFuncionarios")
                        ->findOneBy(
                            array(
                                "idFuncionario" => $id_funcionario
                            )) ;
                    $contactoNew->setIdFuncionario($funcionarios);
                    
                    // Realizamos la Persistencia de los Datos
                    $em->persist($contactoNew);
                    
                    // Realizamos la Actualizacion a la BD
                    $em->flush();
                    
                    //Seteamos el array de Mensajes a enviar *******************
                    $data = array(
                        "status" => "success",                
                        "code" => "200",                
                        "msg" => "El Contacto, " . " " . $nombre1_contacto . " " . $apellido1_contacto . 
                                 " se ha creado satisfactoriamente."                 
                    );
                } else {
                    $data = array(
                        "status" => "error",                
                        "code"   => "400",                
                        "msg"    => "Falta Informacion por Ingresar !!"                
                    );
                } // FIN | Validacion de Datos Pendientes
            } else {
                $data = array(
                        "status" => "error",                
                        "code"   => "400",                
                        "msg"    => "Ya Existe un Contacto Ingresado con esta informacion !!"                
                    );
            } // FIN | Validacion de Existencia de Contacto
        } // FIN | Valid Json
        return $helpers->parserJson($data);
    }//FIN | FND00002
    
}

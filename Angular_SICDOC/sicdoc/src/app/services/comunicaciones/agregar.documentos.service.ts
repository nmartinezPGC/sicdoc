import { Component, Injectable } from '@angular/core';

//Clases nesesarias para el envio via Ajax
import { HttpModule,  Http, Response, Headers } from '@angular/http';
import 'rxjs/add/operator/catch';
import 'rxjs/add/operator/map';
import { Observable } from 'rxjs/Observable';

// Importamos la Clase de las Propiedades del Sistema
import { SystemPropertiesService } from "../shared/systemProperties.service";


@Injectable()
export class AgregarDocumentosService {
  //Propiedades de la Clases
  //URL Base de la Clase, Referencia a la API | Symfony
  public url:string;

  //Variables para el localStorage
  public identity;
  public token;

  //Constructor de la Clase
  constructor( private _http: Http,
               private _systemPropertiesService: SystemPropertiesService ) {
      this.url = this._systemPropertiesService.getmethodUrlService();
  }


  /****************************************************
  * Funcion: FND-00001
  * Fecha: 17-02-2018
  * Descripcion: Metodo Ajax, para Invocar el servicio
  * a la API ( vinculacionComunicacion/vinculacion-de-comunicacion ).
  * Objetivo: Listado de las Comunicacion por Depto,
  * para poder ser Vinculadas, al realizar un Ingreso /
  * Salida de Comunicacion.
  * Params: idTipoComunicacion | idTipoDocumento |
  * idDeptoFuncional
  *****************************************************/
  listaComunicacionVinculantes( params_comunicacion ){
      let json = JSON.stringify( params_comunicacion ); //Convertimos el Objeto a Json
      let params = "json=" + json + "&authorization=" + this.getToken(); // Instanciamos los Valorrs del Json con sus parametros
      let headers = new Headers({ 'Content-Type':'application/x-www-form-urlencoded'}); // Declaramos las Cabezeras

    return this._http.post(this.url + "/vinculacionComunicacion/vinculacion-de-comunicacion", params,
          { headers:headers })
           .map( res => res.json());
  } // FIN : FND-00001



  /****************************************************
  * Funcion: FND-00003
  * Fecha: 28-07-2017
  * Descripcion: Metodo para obtener los Datos de la
  * variable identity del localStorage
  * Objetivo: Seteo de las variables en json
  *****************************************************/
  getIdentity(){
    let identity = JSON.parse(localStorage.getItem('identity'));
    //Pregunta por el valor de la identity
      if(identity != "undefined"){
        this.identity = identity;
      }else{
        this.identity = null;
      }

    return this.identity;
  } // FIN : FND-00003


  /****************************************************
  * Funcion: FND-00004
  * Fecha: 28-07-2017
  * Descripcion: Metodo para obtener los Datos de la
  * variable identity del localStorage
  * Objetivo: Seteo de las variables en json
  *****************************************************/
  getToken(){
    //No hace el parse; porque no es Json
    let token = localStorage.getItem('token');
    //Pregunta por el valor del Token
      if(token != "undefined"){
        this.token = token;
      }else{
        this.token = null;
      }

    return this.token;
  } // FIN : FND-00004


}

import { Injectable } from '@angular/core';

//Clases nesesarias para el envio via Ajax
import { HttpModule,  Http, Response, Headers } from '@angular/http';
import 'rxjs/add/operator/catch';
import 'rxjs/add/operator/map';
import { Observable } from 'rxjs/Observable';

// Importamos la Clase de las Propiedades del Sistema
import { SystemPropertiesService } from "../shared/systemProperties.service";

@Injectable()
export class EditarComunicacionService {
  //Propiedades de la Clases
  //URL Base de la Clase, Referencia a la API | Symfony
  public url:string;
  public urlResourses:string;

  //Variables para el localStorage
  public identity;
  public token;

  //Constructor de la Clase
  constructor( private _http: Http,
               private _systemPropertiesService: SystemPropertiesService ) {
      // Instancia de los valores de las Propiesdades Globales
      this.url = this._systemPropertiesService.getmethodUrlService();
      this.urlResourses = this._systemPropertiesService.getmethodUrlResourses();
  }

  /****************************************************
  * Funcion: FND-00001
  * Fecha: 22-11-2017
  * Descripcion: Metodo Ajax, para Invocar el servicio
  * a la API ( mantenimientos/cambio-fecha ).
  * Objetivo: Ralizar el Cambio de la Fecha, de
  * Comunicacion
  *****************************************************/
  buscaComunicacion( busca_comunicacion ){
     let json = JSON.stringify( busca_comunicacion );
     let params = "json=" + json + "&authorization=" + this.getToken();
     //console.log(json);
     let headers = new Headers({ 'Content-Type':'application/x-www-form-urlencoded'});

    return this._http.post(this.url + "/mantenimientos/busca-comunicacion", params, { headers:headers }).map( res => res.json());
  } // FIN | FND-00001


  /****************************************************
  * Funcion: FND-00002
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
  } // FIN : FND-00002


  /****************************************************
  * Funcion: FND-00003
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
  } // FIN : FND-00003


  /*********************************************************
  * Funcion: FND-00004                                     *
  * Fecha: 03-05-2018                                      *
  * Descripcion: Metodo Ajax, para Invocar el servicio     *
  * a la API ( /correspondencia/edit-correspondencia ).    *
  * Objetivo: Editar de Correspondencia                    *
  *********************************************************/
  editarComunicacion( editar_to_register ){
      let json = JSON.stringify( editar_to_register );
      let params = "json=" + json + "&authorization=" + this.getToken();
      //console.log(json);
      let headers = new Headers({ 'Content-Type':'application/x-www-form-urlencoded'});
    // Retorno de la Funcion
    return this._http.post(this.url + "/correspondencia/edit-correspondencia", params, { headers:headers }).map( res => res.json());
  } // FIN : FND-00004



}

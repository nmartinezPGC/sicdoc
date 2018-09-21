import { Component, Injectable } from '@angular/core';

//Clases nesesarias para el envio via Ajax
import { HttpModule,  Http, Response, Headers } from '@angular/http';
import 'rxjs/add/operator/catch';
import 'rxjs/add/operator/map';
import { Observable } from 'rxjs/Observable';

// Importamos la Clase de las Propiedades del Sistema
import { SystemPropertiesService } from "../shared/systemProperties.service";

@Injectable()
export class SalidaCorrespondenciaService {
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


  /*********************************************************
  * Funcion: FND-00002                                     *
  * Fecha: 07-06-2018                                      *
  * Descripcion: Metodo Ajax, para Invocar el servicio     *
  * a la API ( /unidad-correspondencia/entrada-correspondencia ).*
  * Objetivo: Ingresar nueva Correspondencia                   *
  *********************************************************/
  registerNuevaCorrespondencia( correpondencia_to_register ){
      let json = JSON.stringify( correpondencia_to_register );
      let params = "json=" + json + "&authorization=" + this.getToken();
      //console.log(json);
      let headers = new Headers({ 'Content-Type':'application/x-www-form-urlencoded'});
    // Retorno de la Funcion
    return this._http.post(this.url + "/unidad-correspondencia/entrada-correspondencia", params, { headers:headers }).map( res => res.json());
  } // FIN : FND-00002


  /*********************************************************
  * Funcion: FND-00002.1                                   *
  * Fecha: 05-09-2018                                      *
  * Descripcion: Metodo Ajax, para Invocar el servicio     *
  * a la API ( /unidad-correspondencia/salida-correspondencia ).*
  * Objetivo: Ingresar Salida de Correspondencia           *
  *********************************************************/
  registerSalidaCorrespondencia( correpondencia_to_register ){
      let json = JSON.stringify( correpondencia_to_register );
      let params = "json=" + json + "&authorization=" + this.getToken();
      //console.log(json);
      let headers = new Headers({ 'Content-Type':'application/x-www-form-urlencoded'});
    // Retorno de la Funcion
    return this._http.post(this.url + "/unidad-correspondencia/salida-correspondencia", params, { headers:headers }).map( res => res.json());
  } // FIN : FND-00002.1


  /*********************************************************
  * Funcion: FND-00003                                     *
  * Fecha: 07-03-2018                                      *
  * Descripcion: Metodo Ajax, para Invocar el servicio     *
  * a la API ( /documentos/subir-documentos-comunicacion ).*
  * Objetivo: Ingresar nuevos Documentos                   *
  *********************************************************/
  registerDocumentos( documentos_to_register ){
      let json = JSON.stringify( documentos_to_register );
      let params = "json=" + json + "&authorization=" + this.getToken();
      //console.log(json);
      let headers = new Headers({ 'Content-Type':'application/x-www-form-urlencoded'});
    // Retorno de la Funcion
      return this._http.post(this.url + "/documentos-unidad-correspondencia/subir-documentos-comunicacion", params, { headers:headers }).map( res => res.json());
  } // FIN : FND-00003


  /*********************************************************
  * Funcion: FND-00004                                     *
  * Fecha: 08-03-2018                                      *
  * Descripcion: Metodo Ajax, para Invocar el servicio     *
  * a la API ( /documentos/borrar-documentos-comunicacion ).*
  * Objetivo: Ingresar nuevos Documentos                   *
  *********************************************************/
  deleteDocumentos( documentos_to_delete ){
      let json = JSON.stringify( documentos_to_delete );
      let params = "json=" + json + "&authorization=" + this.getToken();
      //console.log(json);
      let headers = new Headers({ 'Content-Type':'application/x-www-form-urlencoded'});
    // Retorno de la Funcion
    return this._http.post(this.url + "/unidad-correspondencia/borrar-documentos-comunicacion", params, { headers:headers }).map( res => res.json());
  } // FIN : FND-00003


  /*********************************************************
  * Funcion: FND-00005                                     *
  * Fecha: 07-03-2018                                      *
  * Descripcion: Metodo Ajax, para Invocar el servicio     *
  * a la API (/documentos-unidad-correspondencia/com-ingresadas-list).*
  * Objetivo: Ingresar nuevos Documentos                   *
  *********************************************************/
  documentosIngresados( documentos_to_register ){
      let json = JSON.stringify( documentos_to_register );
      let params = "json=" + json;
      //console.log(json);
      let headers = new Headers({ 'Content-Type':'application/x-www-form-urlencoded'});
    // Retorno de la Funcion
      return this._http.post(this.url + "/documentos-unidad-correspondencia/com-ingresadas-list", params, { headers:headers }).map( res => res.json());
  } // FIN : FND-00005



  /*********************************************************
  * Funcion: FND-00006                                    *
  * Fecha: 07-03-2018                                      *
  * Descripcion: Metodo Ajax, para Invocar el servicio     *
  * a la API ( //documentos-unidad-correspondencia/com-recibidas-list ).*
  * Objetivo: Ingresar nuevos Documentos                   *
  *********************************************************/
  documentosRecibidos( documentos_to_register ){
      let json = JSON.stringify( documentos_to_register );
      let params = "json=" + json;
      //console.log(json);
      let headers = new Headers({ 'Content-Type':'application/x-www-form-urlencoded'});
    // Retorno de la Funcion
      return this._http.post(this.url + "/documentos-unidad-correspondencia/com-recibidas-list", params, { headers:headers }).map( res => res.json());
  } // FIN : FND-00006


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

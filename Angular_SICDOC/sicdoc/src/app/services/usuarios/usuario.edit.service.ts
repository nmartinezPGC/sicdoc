import { Injectable } from '@angular/core';

//Clases nesesarias para el envio via Ajax
import { HttpModule,  Http, Response, Headers } from '@angular/http';
import 'rxjs/add/operator/catch';
import 'rxjs/add/operator/map';
import { Subject } from 'rxjs/Subject';
import { Observable } from 'rxjs/Observable';

// Importamos la Clase de las Propiedades del Sistema
import { SystemPropertiesService } from '../shared/systemProperties.service';

@Injectable()
export class UsuariosEditService {
  //Propiedades de la Clases
  //URL Base de la Clase, Referencia a la API | Symfony
  public url: string;
  public urlResourses: string;

  //Variables para el localStorage
  public identity;
  public token;

  //Constructor de la Clase
  constructor( private _http: Http,
               private _systemPropertiesService: SystemPropertiesService ) {
    this.url = this._systemPropertiesService.getmethodUrlService();
    this.urlResourses = this._systemPropertiesService.getmethodUrlResourses();
  }


  /****************************************************
  * Funcion: FND-00001
  * Fecha: 18-09-2017
  * Descripcion: Metodo para obtener los Datos de la
  * variable identity del localStorage
  * Objetivo: Seteo de las variables en json
  *****************************************************/
  getIdentity() {
    let identity = JSON.parse(localStorage.getItem('identity'));
    //Pregunta por el valor de la identity
      if (identity != 'undefined'){
        this.identity = identity;
      }else{
        this.identity = null;
      }

    return this.identity;
  } // FIN : FND-00001


  /****************************************************
  * Funcion: FND-00002
  * Fecha: 18-09-2017
  * Descripcion: Metodo para obtener los Datos de la
  * variable identity del localStorage
  * Objetivo: Seteo de las variables en json
  *****************************************************/
  getToken() {
    //No hace el parse; porque no es Json
    let token = localStorage.getItem('token');
    //Pregunta por el valor del Token
      if (token != 'undefined'){
        this.token = token;
      }else{
        this.token = null;
      }

    return this.token;
  } // FIN : FND-00002


  /****************************************************
  * Funcion: FND-00003
  * Fecha: 02-08-2018
  * Descripcion: Metodo Ajax, para Invocar el servicio
  * a la API ( usuarios/user-details ).
  * Objetivo: Listar el detalle del Usuario
  * Parametros: Id del Usuario
  *****************************************************/
  userDetail( user_to_find ) {
      let json = JSON.stringify( user_to_find );
      let params = 'json=' + json;
      //console.log(json);
      let headers = new Headers({ 'Content-Type': 'application/x-www-form-urlencoded'});

    return this._http.post(this.url + '/usuario/user-details',
                          params, { headers: headers }).map( res => res.json());
  } // FIN : FND-00003


  /****************************************************
  * Funcion: FND-00004
  * Fecha: 12-10-2017
  * Descripcion: Metodo Ajax, para Invocar el servicio
  * a la API ( contactos/contactos-new ).
  * Objetivo: Ingresar Nuevo Contacto
  *****************************************************/
  editUser( data_to_user ) {
      let json = JSON.stringify( data_to_user ); //Convertimos el Objeto a Json
      let params = 'json=' + json + '&authorization=' + this.getToken(); // Instanciamos los Valorrs del Json con sus parametros
      let headers = new Headers({ 'Content-Type': 'application/x-www-form-urlencoded'}); // Declaramos las Cabezeras

    return this._http.post(this.url + '/usuario/edit', params, { headers: headers }).map( res => res.json());
    // return this._http.post(this.url + "/login", params).map( res => res.json());
  } // FIN : FND-00004

}

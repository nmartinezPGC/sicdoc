import { Injectable } from '@angular/core';

//Clases nesesarias para el envio via Ajax
import { HttpModule,  Http, Response, Headers } from '@angular/http';
import 'rxjs/add/operator/catch';
import 'rxjs/add/operator/map';
import { Observable } from 'rxjs/Observable';

@Injectable()
export class IngresoComunicacionService {
  //Propiedades de la Clases
  //URL Base de la Clase, Referencia a la API | Symfony
  public url = "http://localhost/sicdoc/symfony/web/app_dev.php";
  // public url = "http://172.17.4.162/sicdoc/symfony/web/app.php";
  // public url = "http://172.17.3.141/sicdoc/symfony/web/app.php";

  //Variables para el localStorage
  public identity;
  public token;


  //Constructor de la Clase
  constructor( private _http: Http ) {  }


  /****************************************************
  * Funcion: FND-00001
  * Fecha: 28-07-2017
  * Descripcion: Metodo Ajax, para Invocar el servicio
  * a la API (login).
  * Objetivo: Logearse a la Aplicacion
  *****************************************************/
  signUp( user_to_login ){
      let json = JSON.stringify( user_to_login ); //Convertimos el Objeto a Json
      let params = "json=" + json;                // Instanciamos los Valorrs del Json con sus parametros
      let headers = new Headers({ 'Content-Type':'application/x-www-form-urlencoded'}); // Declaramos las Cabezeras

    return this._http.post(this.url + "/login", params, { headers:headers }).map( res => res.json());
  } // FIN : FND-00001


  /****************************************************
  * Funcion: FND-00002
  * Fecha: 31-08-2017
  * Descripcion: Metodo Ajax, para Invocar el servicio
  * a la API (correspondencia/new-correspondencia).
  * Objetivo: Ingresar nueva correspondencia
  *****************************************************/
  registerComunicacion( token, correspondencia_to_register ){
      let json = JSON.stringify( correspondencia_to_register );
      let params = "json=" + json + "&authorization=" + token;
      //console.log(json);
      let headers = new Headers({ 'Content-Type':'application/x-www-form-urlencoded'});

    return this._http.post(this.url + "/correspondencia/new-correspondencia", params, { headers:headers }).map( res => res.json());
  } // FIN : FND-00002


  /****************************************************
  * Funcion: FND-00002.1
  * Fecha: 25-09-2017
  * Descripcion: Metodo Ajax, para Invocar el servicio
  * a la API (correspondencia/new-correspondencia-tipo).
  * Objetivo: Ingresar nueva correspondencia
  *****************************************************/
  registerTipoComunicacion( token, correspondencia_to_register ){
      let json = JSON.stringify( correspondencia_to_register );
      let params = "json=" + json + "&authorization=" + token;
      //console.log(json);
      let headers = new Headers({ 'Content-Type':'application/x-www-form-urlencoded'});

    return this._http.post(this.url + "/correspondencia/new-correspondencia-tipo", params, { headers:headers }).map( res => res.json());
  } // FIN : FND-00002.1


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

import { Injectable } from '@angular/core';

//Clases nesesarias para el envio via Ajax
import { HttpModule,  Http, Response, Headers } from '@angular/http';
import 'rxjs/add/operator/catch';
import 'rxjs/add/operator/map';
import { Observable } from 'rxjs/Observable';

// Importamos la Clase de las Propiedades del Sistema
import { SystemPropertiesService } from "../shared/systemProperties.service";

@Injectable()
export class LoginService {
  //Propiedades de la Clases
  //URL Base de la Clase, Referencia a la API | Symfony
  public url:string;
  //public url = "http://localhost/sicdoc/symfony/web/app_dev.php";
  // public url = "http://172.17.0.250/sicdoc/symfony/web/app.php";

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
  * Fecha: 28-07-2017
  * Descripcion: Metodo Ajax, para Invocar el servicio
  * a la API (login).
  * Objetivo: Logearse a la Aplicacion
  *****************************************************/
  signUp( user_to_login ){
      let json = JSON.stringify( user_to_login );
      let params = "json=" + json;
      let headers = new Headers({ 'Content-Type':'application/x-www-form-urlencoded'});

    return this._http.post(this.url + "/login", params, { headers:headers }).map( res => res.json());
  }


  /****************************************************
  * Funcion: FND-00002
  * Fecha: 28-07-2017
  * Descripcion: Metodo Ajax, para Invocar el servicio
  * a la API (usuario/new).
  * Objetivo: Agregar nuevo Usuario
  *****************************************************/
  registerUser( user_to_register ){
      let json = JSON.stringify( user_to_register );
      let params = "json=" + json;
      //console.log(json);
      let headers = new Headers({ 'Content-Type':'application/x-www-form-urlencoded'});

    return this._http.post(this.url + "/usuario/new", params, { headers:headers }).map( res => res.json());
  }


  /****************************************************
  * Funcion: FND-00002.1
  * Fecha: 09-10-2017
  * Descripcion: Metodo Ajax, para Invocar el servicio
  * a la API ( usuario/change-pass-user ).
  * Objetivo: Cambiar Password a Usuario
  *****************************************************/
  changePassUser( user_to_change_pass ){
      let json = JSON.stringify( user_to_change_pass );
      let params = "json=" + json + "&authorization=" + this.getToken();
      //console.log(json);
      let headers = new Headers({ 'Content-Type':'application/x-www-form-urlencoded'});

    return this._http.post(this.url + "/usuario/change-pass-user", params, { headers:headers }).map( res => res.json());
  } // FIN | FND-00002


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
  }


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
  }


}

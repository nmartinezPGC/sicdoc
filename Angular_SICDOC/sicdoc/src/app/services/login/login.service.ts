import { Injectable } from '@angular/core';

//Clases nesesarias para el envio via Ajax
import { HttpModule,  Http, Response, Headers } from '@angular/http';
import 'rxjs/add/operator/catch';
import 'rxjs/add/operator/map';
import { Observable } from 'rxjs/Observable';

@Injectable()
export class LoginService {
  //Propiedades de la Clases
  //URL Base de la Clase, Referencia a la API | Symfony
  public url = "http://localhost/sicdoc/symfony/web/app_dev.php";

  //Variables para el localStorage
  public identity;
  public token;

  //Constructor de la Clase
  constructor( private _http: Http ) { }

  //Funcion de Resgistro
  signUp( user_to_login ){
      let json = JSON.stringify( user_to_login );
      let params = "json=" + json;
      let headers = new Headers({ 'Content-Type':'application/x-www-form-urlencoded'});


    return this._http.post(this.url + "/login", params, { headers:headers }).map( res => res.json());
  }

  //Funcion para convertir el localStorage
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


  //Funcion para convertir el localStorage
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

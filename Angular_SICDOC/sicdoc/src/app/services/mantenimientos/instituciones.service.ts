import { Injectable } from '@angular/core';

//Clases nesesarias para el envio via Ajax
import { HttpModule,  Http, Response, Headers } from '@angular/http';
import 'rxjs/add/operator/catch';
import 'rxjs/add/operator/map';
import { Observable } from 'rxjs/Observable';

@Injectable()
export class InstitucionesService {
  //Propiedades de la Clases
  //URL Base de la Clase, Referencia a la API | Symfony
  public url = "http://localhost/sicdoc/symfony/web/app_dev.php";
  // public url = "http://172.17.0.250/sicdoc/symfony/web/app.php";
  // public url = "http://172.17.3.141/sicdoc/symfony/web/app.php";
  // public url = "http://192.168.0.23/sicdoc/symfony/web/app.php";

  //Variables para el localStorage
  public identity;
  public token;


  //Constructor de la Clase
  constructor( private _http: Http ) { }


  /****************************************************
  * Funcion: FND-00001
  * Fecha: 01-01-2018
  * Descripcion: Metodo Ajax, para Invocar el servicio
  * a la API (mantenimientos/mantenimiento-institucion-new).
  * Objetivo: Solicitud de Nueva Institucion
  *****************************************************/
  solitarNuevaInstitucion( solicitud_institucion_new ){
    let json = JSON.stringify( solicitud_institucion_new );
    let params = "json=" + json + "&authorization=" + this.getToken();
    //console.log(json);
    let headers = new Headers({ 'Content-Type':'application/x-www-form-urlencoded'});

    return this._http.post(this.url + "/mantenimientos/mantenimiento-institucion-new", params, { headers:headers }).map( res => res.json());
  } // FIN | FND-00001


  /****************************************************
  * Funcion: FND-00001
  * Fecha: 01-01-2018
  * Descripcion: Metodo Ajax, para Invocar el servicio
  * a la API (mantenimientos/mantenimiento-institucion-edit).
  * Objetivo: Solicitud de Nueva Institucion
  *****************************************************/
  solitarEditInstitucion( solicitud_institucion_edit ){
    let json = JSON.stringify( solicitud_institucion_edit );
    let params = "json=" + json + "&authorization=" + this.getToken();
    //console.log(json);
    let headers = new Headers({ 'Content-Type':'application/x-www-form-urlencoded'});

    return this._http.post(this.url + "/mantenimientos/mantenimiento-institucion-edit", params, { headers:headers }).map( res => res.json());
  } // FIN | FND-00001


  /*****************************************************
  * Funcion: FND-00001
  * Fecha: 01-01-2018
  * Descripcion: Carga la lista con Paramtros Json
  * Objetivo: Obtener la lista serializada, con autoriza-
  * cion del Token (tiene que estar logeado)
  * (/mantenimientos/mantenimiento-institucion-busca).
  ******************************************************/
  listaIntitucionesGet( lista_comun, lista ){
    let json = JSON.stringify( lista_comun );

    let params = "json=" + json;
    let listaIn = lista;
    let headers = new Headers({ 'Content-Type':'application/x-www-form-urlencoded'});

    return this._http.post(this.url + "/mantenimientos/"+listaIn, params, { headers:headers }).map( res => res.json());
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

import { Injectable } from '@angular/core';

//Clases nesesarias para el envio via Ajax
import { HttpModule,  Http, Response, Headers } from '@angular/http';
import 'rxjs/add/operator/catch';
import 'rxjs/add/operator/map';
import { Observable } from 'rxjs/Observable';

@Injectable()
export class SolicitudCambioFechaService {
  //Propiedades de la Clases
  //URL Base de la Clase, Referencia a la API | Symfony
  // public url = "http://localhost/sicdoc/symfony/web/app_dev.php";
  // public url = "http://172.17.4.162/sicdoc/symfony/web/app.php";
  // public url = "http://172.17.3.141/sicdoc/symfony/web/app.php";
  public url = "http://192.168.0.23/sicdoc/symfony/web/app.php";

  //Variables para el localStorage
  public identity;
  public token;


  //Constructor de la Clase
  constructor( private _http: Http ) { }


  /****************************************************
  * Funcion: FND-00002
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
  } // FIN | FND-00002

  /****************************************************
  * Funcion: FND-00002
  * Fecha: 22-11-2017
  * Descripcion: Metodo Ajax, para Invocar el servicio
  * a la API ( mantenimientos/cambio-fecha ).
  * Objetivo: Ralizar el Cambio de la Fecha, de
  * Comunicacion
  *****************************************************/
  cambioFecha( cambio_feha ){
      let json = JSON.stringify( cambio_feha );
      let params = "json=" + json + "&authorization=" + this.getToken();
      //console.log(json);
      let headers = new Headers({ 'Content-Type':'application/x-www-form-urlencoded'});

    return this._http.post(this.url + "/mantenimientos/cambio-fecha", params, { headers:headers }).map( res => res.json());
  } // FIN | FND-00002


  /****************************************************
  * Funcion: FND-00002.1
  * Fecha: 22-11-2017
  * Descripcion: Metodo Ajax, para Invocar el servicio
  * a la API ( mantenimientos/solicitud-cambio-fecha ).
  * Objetivo: Solicitud de Cambio Fecha, Comunicacion
  *****************************************************/
  solitarCambioFecha( solicitud_cambio_fecha ){
      let json = JSON.stringify( solicitud_cambio_fecha );
      let params = "json=" + json + "&authorization=" + this.getToken();
      //console.log(json);
      let headers = new Headers({ 'Content-Type':'application/x-www-form-urlencoded'});

    return this._http.post(this.url + "/mantenimientos/solicitud-cambio-fecha", params, { headers:headers }).map( res => res.json());
  } // FIN | FND-00002.1


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

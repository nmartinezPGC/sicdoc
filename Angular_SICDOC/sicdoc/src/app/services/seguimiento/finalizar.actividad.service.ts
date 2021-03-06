import { Injectable } from '@angular/core';

//Clases nesesarias para el envio via Ajax
import { HttpModule,  Http, Response, Headers } from '@angular/http';
import 'rxjs/add/operator/catch';
import 'rxjs/add/operator/map';
import { Observable } from 'rxjs/Observable';

// Importamos la Clase de las Propiedades del Sistema
import { SystemPropertiesService } from "../shared/systemProperties.service";

@Injectable()
export class FinalizarActividadService {
  //Propiedades de la Clases
  //URL Base de la Clase, Referencia a la API | Symfony
  public url:string;
  public urlResourses:string;
  //public url = "http://localhost/sicdoc/symfony/web/app_dev.php";
  // public url = "http://172.17.0.250/sicdoc/symfony/web/app.php";

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
    getIdentity(){
      let identity = JSON.parse(localStorage.getItem('identity'));
      //Pregunta por el valor de la identity
        if(identity != "undefined"){
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
    } // FIN : FND-00002


    /****************************************************
    * Funcion: FND-00003
    * Fecha: 22-09-2017
    * Descripcion: Metodo Ajax, para Invocar el servicio
    * a la API ( finalizar-oficio-asignado ).
    * Objetivo: Finalizar Oficio - Asignado
    * Parametros: Descripcion, Actividad y Documento de
    *             Respuesta al Oficio Asignado.
    *****************************************************/
    finalizarOficioAsignado( token, correspondencia_to_finally ){
        let json = JSON.stringify( correspondencia_to_finally );
        let params = "json=" + json + "&authorization=" + token;
        //console.log(json);
        let headers = new Headers({ 'Content-Type':'application/x-www-form-urlencoded'});

      return this._http.post(this.url + "/seguimiento/finalizar-oficio-asignado", params, { headers:headers }).map( res => res.json());
    } // FIN : FND-00003


    /****************************************************
    * Funcion: FND-00004
    * Fecha: 24-09-2017
    * Descripcion: Metodo Ajax, para Invocar el servicio
    * a la API ( creacion-oficio-asignado ).
    * Objetivo: Finalizar Oficio - Asignado
    * Parametros: Descripcion, Actividad y Documento de
    *             Respuesta al Oficio Asignado.
    *****************************************************/
    creacionOficioAsignado( token, correspondencia_to_finally ){
        let json = JSON.stringify( correspondencia_to_finally );
        let params = "json=" + json + "&authorization=" + token;
        //console.log(json);
        let headers = new Headers({ 'Content-Type':'application/x-www-form-urlencoded'});

       return this._http.post(this.url + "/seguimiento/creacion-oficio-asignado", params, { headers:headers }).map( res => res.json());
    } // FIN : FND-00004


    /****************************************************
    * Funcion: FND-00005
    * Fecha: 27-09-2017
    * Descripcion: Metodo Ajax, para Invocar el servicio
    * a la API ( creacion-actividad-resp ).
    * Objetivo:Agregar Actividad a Comunicacion
    * Parametros: Descripcion, Actividad y Documento de
    *             Respuesta al Oficio Asignado.
    *****************************************************/
    agregarActividadResp( token, correspondencia_to_add_activity ){
        let json = JSON.stringify( correspondencia_to_add_activity );
        let params = "json=" + json + "&authorization=" + token;
        //console.log(json);
        let headers = new Headers({ 'Content-Type':'application/x-www-form-urlencoded'});

      return this._http.post(this.url + "/seguimiento/creacion-actividad-resp", params, { headers:headers }).map( res => res.json());
    } // FIN : FND-00005

    /****************************************************
    * Funcion: FND-00006
    * Fecha: 30-01-2018
    * Descripcion: Metodo Ajax, para Invocar el servicio
    * a la API ( anula-comunicacion ).
    * Objetivo: Anular la Comunicacion
    * Parametros: Descripcion, Actividad.
    *****************************************************/
    anulaComunicacion( token, correspondencia_to_anula_com ){
        let json = JSON.stringify( correspondencia_to_anula_com );
        let params = "json=" + json + "&authorization=" + token;
        //console.log(json);
        let headers = new Headers({ 'Content-Type':'application/x-www-form-urlencoded'});

      return this._http.post(this.url + "/seguimiento/anula-comunicacion", params, { headers:headers }).map( res => res.json());
    } // FIN : FND-00006

}

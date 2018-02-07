import { Injectable } from '@angular/core';

//Clases nesesarias para el envio via Ajax
import { HttpModule,  Http, Response, Headers } from '@angular/http';
import 'rxjs/add/operator/catch';
import 'rxjs/add/operator/map';
import { Subject } from 'rxjs/Subject';
import { Observable } from 'rxjs/Observable';

@Injectable()
export class ListasComunesService {
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

  /*****************************************************
  * Funcion: FND-00001
  * Fecha: 01-09-2017
  * Descripcion: Carga la lista con Paramtros Json
  * Objetivo: Obtener la lista serializada, con autoriza-
  * cion del Token (tiene que estar logeado)
  * (listascomunes).
  ******************************************************/
  listasComunes( lista_comun, lista ){
      let json = JSON.stringify( lista_comun );

      let params = "json=" + json;
      let listaIn = lista;
      let headers = new Headers({ 'Content-Type':'application/x-www-form-urlencoded'});

    return this._http.post(this.url + "/listas/"+listaIn, params, { headers:headers }).map( res => res.json());
  } // FIN : FND-00001


  /*****************************************************
  * Funcion: FND-00001.1
  * Fecha: 13-09-2017
  * Descripcion: Carga la lista sin Paramtros de Json
  * Objetivo: Obtener la lista serializada
  * (listascomunes).
  ******************************************************/
  listasComunesGet( lista_comun, lista ){
      let json = JSON.stringify( lista_comun );

      let params = "json=" + json;
      let listaIn = lista;
      let headers = new Headers({ 'Content-Type':'application/x-www-form-urlencoded'});

    return this._http.get(this.url + "/listas/" + listaIn, params).map( res => res.json());
  } // FIN : FND-00001.1


  /*****************************************************
  * Funcion: FND-00001.2
  * Fecha: 13-09-2017
  * Descripcion: Carga la lista sin Paramtros de Json
  * Objetivo: Obtener la lista serializada
  * (listascomunes).
  ******************************************************/
  listasOficiosDir( page = null ){
      if( page == null ){
        page = 1;
      }

    return this._http.get(this.url + "/listas/asignar-oficios-list?page=" + page).map( res => res.json());
  } // FIN : FND-00001.2


  /*****************************************************
  * Funcion: FND-00002
  * Fecha: 01-09-2017
  * Descripcion: Carga la lista de Parametros con Token
  * Objetivo: Obtener la lista segun autiriztion del
  * Token y el servicio invocado
  * (comunes).
  ******************************************************/
  listasComunesToken( lista_comun_token, lista ){
      let json = JSON.stringify( lista_comun_token );

      let params  = "json=" + json + "&authorization=" + this.getToken();
      let listaIn = lista;
      let headers = new Headers({ 'Content-Type':'application/x-www-form-urlencoded'});

    return this._http.post(this.url + "/comunes/" + listaIn, params, { headers:headers }).map( res => res.json());
  }


  /*****************************************************
  * Funcion: FND-00002.1.1
  * Fecha: 01-11-2017
  * Descripcion: Carga la lista de Documentos con Token,
  * como autorizacion para poder verlos o descargarlos
  * Objetivo: Obtener la lista segun autiriztion del
  * Token y el servicio invocado
  * ( documentos/listar-documentos ).
  ******************************************************/
  listasDocumentosToken( lista_comun_token, lista ){
      let json = JSON.stringify( lista_comun_token );

      let params  = "json=" + json + "&authorization=" + this.getToken();
      let listaIn = lista;
      let headers = new Headers({ 'Content-Type':'application/x-www-form-urlencoded'});

    return this._http.post(this.url + "/documentos/" + listaIn, params, { headers:headers }).map( res => res.json());
  } // FIN | FND-00002.1.1


  /*****************************************************
  * Funcion: FND-00002.1
  * Fecha: 21-09-2017
  * Descripcion: Carga la lista de Parametros con Token
  * Objetivo: Obtener la lista segun autiriztion del
  * Token y el servicio invocado
  * ( listas/ ).
  ******************************************************/
  listasComunesTokenListas( lista_comun_token, lista ){
      let json = JSON.stringify( lista_comun_token );

      let params  = "json=" + json + "&authorization=" + this.getToken();
      let listaIn = lista;
      let headers = new Headers({ 'Content-Type':'application/x-www-form-urlencoded'});

    return this._http.post(this.url + "/listas/" + listaIn, params, { headers:headers }).map( res => res.json());
  }


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

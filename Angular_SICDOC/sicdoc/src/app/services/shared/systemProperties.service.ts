import { Injectable } from '@angular/core';

//Clases nesesarias para el envio via Ajax
import { HttpModule,  Http, Response, Headers } from '@angular/http';
import 'rxjs/add/operator/catch';
import 'rxjs/add/operator/map';
import { Observable } from 'rxjs/Observable';

declare var $:any;

@Injectable()
export class SystemPropertiesService {
  // Declaracion de las variables del Service
  public progressBar;

  constructor( private _http: Http){}

  // Varibles Gloables de Inicio del Systema
  // Entorno Localhost
  public urlLocalConfig:string = "http://localhost/sicdoc/symfony/web/app_dev.php";
  public urlLocalResourse:string = "http://localhost/sicdoc/symfony/web/";

  // Entorno de Servidor
  public urlServerConfig:string = "http://172.17.0.250/sicdoc/symfony/web/app.php";
  public urlServerResourse:string = "http://172.17.0.250/sicdoc/symfony/web/";
  
  // Indicador del entorno a Copilar | 1 = Server | 2 = Localhost
  public indicatorIPCompiler:number = 2;

  /****************************************************
  * Funcion: FND-00001
  * Fecha: 17-02-2018
  * Descripcion: Genera la Url para los Services
  * Objetivo: Genera Url de los Services
  *****************************************************/
  getmethodUrlService(){
    // Instanciamos el Indicador del Entorno de Compilacion
    let indicadorIp:number = this.indicatorIPCompiler;

    let urlEnviroment:string;
    // Evalua el Entorno y Proporciona la URL
    if( indicadorIp == 1 ){
      urlEnviroment = this.urlServerConfig;
    }else if ( indicadorIp == 2 ){
      urlEnviroment = this.urlLocalConfig;
    }

    return urlEnviroment;
  } // FIN : 00001


  /****************************************************
  * Funcion: FND-00002
  * Fecha: 17-02-2018
  * Descripcion: Recupera la Ruta de los Recursos del
  * Servidor
  * Objetivo: Recupera la Ruta de los Recursos
  *****************************************************/
  getmethodUrlResourses(){
    // Instanciamos el Indicador del Entorno de Compilacion
    let indicadorIp:number = this.indicatorIPCompiler;

    let urlEnviromentResourse:string;
    // Evalua el Entorno y Proporciona la URL
    if( indicadorIp == 1 ){
      urlEnviromentResourse = this.urlServerResourse;
    }else if ( indicadorIp == 2 ){
      urlEnviromentResourse = this.urlLocalResourse;
    }

    return urlEnviromentResourse;
  } // FIN : 00002


}

import { Injectable } from '@angular/core';

//Clases nesesarias para el envio via Ajax
import { HttpModule,  Http, Response, Headers } from '@angular/http';
import 'rxjs/add/operator/catch';
import 'rxjs/add/operator/map';
import { Subject } from 'rxjs/Subject';
import { Observable } from 'rxjs/Observable';

// Importamos la Clase de las Propiedades del Sistema
import { SystemPropertiesService } from "./systemProperties.service";

@Injectable()
export class UploadService {
  public progressBar;

  // Map de la API
  public url:string;
  // public url = "http://localhost/sicdoc/symfony/web/app_dev.php"; // Local
  // public url = "http://172.17.0.250/sicdoc/symfony/web/app.php"; // Server


  constructor( private _http: Http,
               private _systemPropertiesService: SystemPropertiesService ) { 
      this.url = this._systemPropertiesService.getmethodUrlService();
    }

  //Variables para el localStorage
  public identity;
  public token;

  /****************************************************
  * Funcion: FND-00001
  * Fecha: 29-07-2017
  * Descripcion: Metodo Ajax, para Crear la Imagen del
  * usuario
  * Objetivo: Cargar Imagen de Perfil
  *****************************************************/
  makeFileRequest(token, url: string, params: Array<string>, files: Array<File>){
      return new Promise((resolve, reject) => {
        var formData: any = new FormData();
        var xhr = new XMLHttpRequest();

        var name_file_input = params[0];
        var name_pdf = params[1];

        for(var i = 0; i < files.length; i ++){
            formData.append( name_file_input, files[i], files[i].name );
            formData.append( "name_pdf", name_pdf );
            //alert( files[i].name );
        }

        formData.append("authorization", token);
        // formData.append("name_pdf", name_pdf );
        //alert(xhr.readyState);
        xhr.onreadystatechange = function(){
            if(xhr.readyState === 4){
              if(xhr.status === 200){
                resolve(JSON.parse(xhr.response));
              }else{
                resolve(JSON.parse(xhr.response));
              }
            }
        }

        // Listener de la barra de Progreso
        xhr.upload.addEventListener( "progress", function(event: any){
            var percent = ( event.loaded / event.total ) * 100;
            let prc = Math.round(percent).toString();
            document.getElementById("upload-progress-bar").setAttribute("value", prc);
            document.getElementById("upload-progress-bar").style.width = prc + "%";
            document.getElementById("upload-progress-bar").innerHTML = Math.round(percent) + " % subido ... espera a que cargue" ;
        }, false);

        // Listener para el Fichero ya Subido
        xhr.addEventListener("load", function(){
          document.getElementById("upload-progress-bar").innerHTML = " Por fin se Subio !!" ;
          let prc = "0";
          document.getElementById("upload-progress-bar").setAttribute("value", prc);
          document.getElementById("upload-progress-bar").setAttribute("aria-valuenow", "0");
          document.getElementById("upload-progress-bar").style.width = prc + "%";
        }, false);

        // Error en la Subida
        xhr.addEventListener("error", function(){
          document.getElementById("upload-progress-bar").innerHTML = " Error en la Subida !!" ;
        }, false);

        // Error en la Subida
        xhr.addEventListener("abort", function(){
          document.getElementById("upload-progress-bar").innerHTML = " Subida Abortada !!" ;
        }, false);

        //Enviar por Post en File
        xhr.open("POST", url, true);
        xhr.send(formData);


      });
  }


  /****************************************************
  * Funcion: FND-00002
  * Fecha: 12-10-2017
  * Descripcion: Metodo Ajax, para Crear la Imagen del
  * usuario
  * Objetivo: Cargar Imagen de Perfil
  *****************************************************/
  makeFileRequestNoToken(url: string, params: Array<string>, files: Array<File>){
      return new Promise((resolve, reject) => {
        var formData: any = new FormData();
        var xhr = new XMLHttpRequest();

        // var name_file_input = params[0];
        // var name_pdf = params[1];

        let tamanoArray = files.length;
        // alert(tamanoArray);

        for(var i = 0; i < files.length; i ++){
          var name_file_input = params[0];
          var name_pdf = params[1];

            formData.append( name_file_input, files[i], files[i].name );
            formData.append( "name_pdf", name_pdf );
            //alert( files[i].name );
        }

        // formData.append("name_pdf", name_pdf );
        //alert(xhr.readyState);
        xhr.onreadystatechange = function(){
            if(xhr.readyState === 4){
              if(xhr.status === 200){
                resolve(JSON.parse(xhr.response));
              }else{
                resolve(JSON.parse(xhr.response));
              }
            }
        }

        // Listener de la barra de Progreso
        xhr.upload.addEventListener( "progress", function(event: any){
            var percent = ( event.loaded / event.total ) * 100;
            let prc = Math.round(percent).toString();
            document.getElementById("upload-progress-bar").setAttribute("value", prc);
            document.getElementById("upload-progress-bar").style.width = prc + "%";
            document.getElementById("upload-progress-bar").innerHTML = Math.round(percent) + " % subido ... espera a que cargue" ;
        }, false);

        // Listener para el Fichero ya Subido
        xhr.addEventListener("load", function(){
          document.getElementById("upload-progress-bar").innerHTML = " Por fin se Subio !!" ;
          let prc = "0";
          document.getElementById("upload-progress-bar").setAttribute("value", prc);
          document.getElementById("upload-progress-bar").setAttribute("aria-valuenow", "0");
          document.getElementById("upload-progress-bar").style.width = prc + "%";
        }, false);

        // Error en la Subida
        xhr.addEventListener("error", function(){
          document.getElementById("upload-progress-bar").innerHTML = " Error en la Subida !!" ;
        }, false);

        // Error en la Subida
        xhr.addEventListener("abort", function(){
          document.getElementById("upload-progress-bar").innerHTML = " Subida Abortada !!" ;
        }, false);

        //Enviar por Post en File
        xhr.open("POST", url, true);
        xhr.send(formData);


      });
  } //FIN | FND-00002


  /****************************************************
  * Funcion: FND-00003
  * Fecha: 14-02-2018
  * Descripcion: Metodo Ajax, para Borrar el Documento
  * Objetivo: Borrar Documento del Servidor
  *****************************************************/
  borrarDocumento( nombre_documento ){
      let json = JSON.stringify( nombre_documento );

      let params = "json=" + json + "&authorization=" + this.getToken();
      let headers = new Headers({ 'Content-Type':'application/x-www-form-urlencoded'});

    return this._http.post(this.url + "/documentos/borrar-documento-server", params, { headers:headers }).map( res => res.json());
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

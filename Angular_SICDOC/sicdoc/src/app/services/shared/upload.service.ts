import { Injectable } from '@angular/core';

//Clases nesesarias para el envio via Ajax
import { HttpModule,  Http, Response, Headers } from '@angular/http';
import 'rxjs/add/operator/catch';
import 'rxjs/add/operator/map';
import { Observable } from 'rxjs/Observable';

@Injectable()
export class UploadService {
  public progressBar;


  constructor( private _http: Http){}


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

        for(var i = 0; i < files.length; i ++){
            formData.append( name_file_input, files[i], files[i].name );
        }

        formData.append("authorization", token);
        alert(xhr.readyState);
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

}

import { Component, Injectable } from '@angular/core';

//Clases nesesarias para el envio via Ajax
import { HttpModule,  Http, Response, Headers } from '@angular/http';
import 'rxjs/add/operator/catch';
import 'rxjs/add/operator/map';
import { Subject } from 'rxjs/Subject';
import { Observable } from 'rxjs/Observable';

// Importamos la Clase de las Propiedades del Sistema
import { SystemPropertiesService } from "./systemProperties.service";

@Injectable()
export class CaseSecuencesService {
  // Propiedades de la Clases
  // URL Base de la Clase, Referencia a la API | Symfony
  public url:string;

  //Variables para el localStorage
  public identity;
  public token;

  // Parametros de Data
  public paramsSend;

  //Constructor de la Clase
  constructor( private _http: Http,
              private _systemPropertiesService: SystemPropertiesService ) {
    // Realiza la Instacia a la URL de la API
    this.url = this._systemPropertiesService.getmethodUrlService();
  }

  /*****************************************************************************
  * Funcion: FND-00001
  * Fecha: 06-03-2018
  * Descripcion: Ejecuta el Swich, de los Datos a Enviar, por los Parametros de
  * Tipo de Comunicacion y Tipo de Documento
  * Objetivo: Obtener la Secuenia del Tipo de Documento y Tipo de Comunicacion
  * Params: idTipoComunicacion, idTipoDocumento
  * ( caseSecuence ).
  *****************************************************************************/
  caseSecuence( idTipoComunicacionIn:number, idTipoDocumentoIn:number ){
      // this.url = this._systemPropertiesService.getmethodUrlService();
      // Dato del retorno de la Secuecia
      this.paramsSend = {
        "codSecuencial"  : "",
        "tablaSecuencia" : "",
        "idTipoDocumento" : ""
      };
      //let listaIn = lista;

      // Evaluacion de la Informacion enviada por la Funcion
      if( idTipoDocumentoIn == 1 ){
       // Verifica si el Tipo de Comunicacion es Entrada (1) / Salida (2)
       if( idTipoComunicacionIn == 1 ){
         this.paramsSend.codSecuencial = "COM-IN-DET-OFI";
         this.paramsSend.tablaSecuencia = "tbl_comunicacion_det";
         this.paramsSend.idTipoDocumento = idTipoDocumentoIn;
       } else {
         this.paramsSend.codSecuencial = "COM-OUT-DET-OFI";
         this.paramsSend.tablaSecuencia = "tbl_comunicacion_det";
         this.paramsSend.idTipoDocumento = idTipoDocumentoIn ;
       }

      } else if ( idTipoDocumentoIn == 2 ) {
       // Verifica si el Tipo de Comunicacion es Entrada (1) / Salida (2)
       if( idTipoComunicacionIn == 1 ){
         this.paramsSend.codSecuencial = "COM-IN-DET-MEMO";
         this.paramsSend.tablaSecuencia = "tbl_comunicacion_det";
         this.paramsSend.idTipoDocumento = idTipoDocumentoIn;
       } else {
         this.paramsSend.codSecuencial = "COM-OUT-DET-MEMO";
         this.paramsSend.tablaSecuencia = "tbl_comunicacion_det";
         this.paramsSend.idTipoDocumento = idTipoDocumentoIn;
       }

      } else if ( idTipoDocumentoIn == 3 ) {
       // Verifica si el Tipo de Comunicacion es Entrada (1) / Salida (2)
       if( idTipoComunicacionIn == 1 ){
         this.paramsSend.codSecuencial = "COM-IN-DET-NOTA-VERBAL";
         this.paramsSend.tablaSecuencia = "tbl_comunicacion_det";
         this.paramsSend.idTipoDocumento = idTipoDocumentoIn;
       } else {
         this.paramsSend.codSecuencial = "COM-OUT-DET-NOTA-VERBAL";
         this.paramsSend.tablaSecuencia = "tbl_comunicacion_det";
         this.paramsSend.idTipoDocumento = idTipoDocumentoIn;
       }

     } else if ( idTipoDocumentoIn == 4 ) {
       // Verifica si el Tipo de Comunicacion es Entrada (1) / Salida (2)
       if( idTipoComunicacionIn == 1 ){
         this.paramsSend.codSecuencial = "COM-IN-DET-CIRCULAR";
         this.paramsSend.tablaSecuencia = "tbl_comunicacion_det";
         this.paramsSend.idTipoDocumento = idTipoDocumentoIn;
       } else {
         this.paramsSend.codSecuencial = "COM-OUT-DET-CIRCULAR";
         this.paramsSend.tablaSecuencia = "tbl_comunicacion_det";
         this.paramsSend.idTipoDocumento = idTipoDocumentoIn;
       }

      } else if ( idTipoDocumentoIn == 5 ) {
       // Verifica si el Tipo de Comunicacion es Entrada (1) / Salida (2)
       if( idTipoComunicacionIn == 1 ){
         this.paramsSend.codSecuencial = "COM-IN-DET-MAIL";
         this.paramsSend.tablaSecuencia = "tbl_comunicacion_det_mail";
         this.paramsSend.idTipoDocumento = idTipoDocumentoIn;
       } else {
         this.paramsSend.codSecuencial = "COM-OUT-DET-MAIL";
         this.paramsSend.tablaSecuencia = "tbl_comunicacion_det_mail";
         this.paramsSend.idTipoDocumento = idTipoDocumentoIn;
       }

     } else if ( idTipoDocumentoIn == 7 ){
       // Verifica si el Tipo de Comunicacion es Entrada (1) / Salida (2)
       if( idTipoComunicacionIn == 1 ){
         this.paramsSend.codSecuencial = "COM-IN-DET-CALL";
         this.paramsSend.tablaSecuencia = "tbl_comunicacion_det_call";
         this.paramsSend.idTipoDocumento = idTipoDocumentoIn;
       } else if ( idTipoComunicacionIn == 2 ) {
         this.paramsSend.codSecuencial = "COM-OUT-DET-CALL";
         this.paramsSend.tablaSecuencia = "tbl_comunicacion_det_call";
         this.paramsSend.idTipoDocumento = idTipoDocumentoIn;
       }

      } else if ( idTipoDocumentoIn == 8 ) {
       // Verifica si el Tipo de Comunicacion es Entrada (1) / Salida (2)
       if( idTipoComunicacionIn == 1 ){
         this.paramsSend.codSecuencial = "COM-IN-DET-VERB";
         this.paramsSend.tablaSecuencia = "tbl_comunicacion_det_verb";
         this.paramsSend.idTipoDocumento = idTipoDocumentoIn;
       } else {
         this.paramsSend.codSecuencial = "COM-OUT-DET-VERB";
         this.paramsSend.tablaSecuencia = "tbl_comunicacion_det_verb";
         this.paramsSend.idTipoDocumento = idTipoDocumentoIn;
       }

     }else if ( idTipoDocumentoIn == 9 ) {
       // Verifica si el Tipo de Comunicacion es Entrada (1) / Salida (2)
       if( idTipoComunicacionIn == 1 ){
         this.paramsSend.codSecuencial = "COM-IN-DET-REUNION";
         this.paramsSend.tablaSecuencia = "tbl_comunicacion_det";
         this.paramsSend.idTipoDocumento = idTipoDocumentoIn;
       } else {
         this.paramsSend.codSecuencial = "COM-OUT-DET-REUNION";
         this.paramsSend.tablaSecuencia = "tbl_comunicacion_det";
         this.paramsSend.idTipoDocumento = idTipoDocumentoIn;
       }
     }// Fin de Condicion

     // console.log(this.paramsSend);

    // Retorno de los Datos
    return this.paramsSend;
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

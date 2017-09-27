import { Injectable } from '@angular/core';

//Clases nesesarias para el envio via Ajax
import { HttpModule,  Http, Response, Headers } from '@angular/http';
import 'rxjs/add/operator/catch';
import 'rxjs/add/operator/map';
import { Observable } from 'rxjs/Observable';

declare var $:any;

@Injectable()
export class CreateDomService {
  public progressBar;


  constructor( private _http: Http){}

  // Varibles del DOM
  public numIdGenerate = 1;

/****************************************************
* Funcion: FND-00002
* Fecha: 26-09-2017
* Descripcion: Genera Items de DOM Dinanamicos
* Objetivo: Genera Items de DOM Dinanamicos
*****************************************************/
methodApped(){
  //$("#btn2").click(function(){
    this.numIdGenerate = this.numIdGenerate +1;
      $("#listaComun").append("<li id='lista"+ this.numIdGenerate +"'>Appended item</li>");
      //alert($(this).attr("id"));
  //});
} // FIN : 00001

clickOn(){
  $('#listaComun').click(function(e){
    var id = e.target;
    e.currentTarget;
    alert('Id del Hijo ' + id);
    // ...
  });
}


/****************************************************
* Funcion: FND-00002
* Fecha: 26-09-2017
* Descripcion: Genera Items de DOM Dinanamicos
* Objetivo: Genera Items de DOM Dinanamicos
*****************************************************/
methodRemove( paramsId:string ){
  //alert('Hola Remove  ' + paramsId);
  //$("#btn2").click(function(){
      this.clickOn();
      $( "#listaComun" ).remove( ":contains('"+  paramsId + " ')" );

  //});
} // FIN : 00001



}

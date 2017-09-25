import { Component, OnInit } from '@angular/core';

import { FormGroup, FormControl, Validators } from '@angular/forms';

import { RouterModule, Routes, ActivatedRoute, Router } from '@angular/router';

import { HttpModule,  Http, Response, Headers } from '@angular/http';

// Lirerias para el AutoComplete
import {Observable} from 'rxjs/Observable';
import 'rxjs/add/operator/startWith';
import 'rxjs/add/operator/map';

//Importamos los Servicios
import { ListasComunesService } from '../../services/shared/listas.service'; //Servico Listas Comunes
import { FinalizarActividadService } from '../../services/seguimiento/finalizar.actividad.service'; //Servicio de la Clase Finalizar

// Importamos los Pipes de la Forma
import { GenerateDatePipe } from '../../pipes/common/generate.date.pipe';
import { SearchFilterPipe } from '../../pipes/common/generate.search.pipe';

// Importamos la CLase Usuarios del Modelo
import { FinalizarActividad } from '../../models/seguimiento/finalizar.actividad.model'; // Modelo a Utilizar

import { AppComponent } from '../../app.component'; //Servico del Principal

import { NgForm }    from '@angular/forms'; // Para el uso del Formulario

// Declaramos las variables para jQuery
declare var jQuery:any;
declare var $:any;

@Component({
  selector: 'finalizar-actividad',
  templateUrl: '../../views/seguimiento/finalizar.actividad.component.html',
  styleUrls: ['../../views/seguimiento/finalizar.actividad.component.css'],
  providers: [ FinalizarActividadService, ListasComunesService ]
})
export class FinalizarActividadComponent implements OnInit {
  public titulo:string = "Finalizar Actividad";
  public fechaHoy:Date = new Date();
  public fechafin:string;

  // Variables de Confirmacion
  public confirmaExit:number = 1;
  public optionModal:number = 1;


  // parametros multimedia
  public loading  = 'show';
  public loading_table  = 'hide';
  public loading_tr  = 'hide';

  // Variables de Mensajeria y Informaicon
   public data;
   public errorMessage;
   public status;
   public mensajes;

   // Objeto que Controlara la Forma
   forma:FormGroup;

  // Json de los listas de los Oficios por usuario
  public JsonOutgetlistaOficiosAll:any[];
  public JsonOutgetlistaOficiosAllDet:any[];
  public JsonOutgetCodigoSecuenciaDet:any[];
  public JsonOutgetCodigoSecuenciaOfiResp:any[];

  // Parametros del Modelo
  private tableFinalizarActividadList;

  // Variables del localStorage
  public identity;
  public localStorageJSON;
  public paramsDetalleJson;

  // Variables Modales
  public codOficioIntModal;
  public codOficioActModal;
  public codOficioRefModal;
  public idDeptoFuncionalModal;
  public nombre1FuncModal;
  public nombre2FuncModal;
  public apellido1FuncModal;
  public apellido2FuncModal;
  public idFuncModal;
  public idCorrepEncModal;


  // Instacia del Modelo
  public finalizarOficios: FinalizarActividad;

  // Propiedades de la Secuencial
  private paramsSecuenciaDet;
  private paramsSecuenciaOficioRespuesta;
  public codigoSecuenciaDet; // Secuencia en Texto del Oficio
  public codigoSecuenciaOficioRespuesta; // Secuencia en Texto del Oficio
  public valorSecuenciaDet; // Secuencial del Oficio
  public valorSecuenciaOficioRespuesta; // Secuencial del Oficio

  constructor( private _listasComunes: ListasComunesService,
               private _finalizarOficio: FinalizarActividadService,
               private _router: Router,
               private _route: ActivatedRoute,
               private _appComponent: AppComponent,
               private _http: Http ) { }

 /*****************************************************
 * Funcion: ngOnInit()
 * Fecha: 22-09-2017
 * Descripcion: Funcion inicial de Angular para cargar
 * los disitintos metodos del Component
 * Objetivo: Cargar Components y tener la Comunicacion
 *           de la BD, Llamando a la API
 ******************************************************/
  ngOnInit() {
    // Iniciamos los Parametros de Instituciones
    this.tableFinalizarActividadList = {
      "codOficioInt":"",
      "codOficioRef":"",
      "idDeptoFunc":"",
      "nombreFuncAsig":"",
      "apellidoFuncAsig":"",
      "idFuncionarioModal":""
    };

    // Iniciamos los Parametros de Usuarios a Depto Funcionales
    this.localStorageJSON = {
      "idUser":"",
      "idTipoFunc":"",
      "idDeptoFunc":""
    };

    // Iniciamos los Parametros para Dato de Detalle por Estado
    this.paramsDetalleJson = {
      "idCorrespondenciaEnc":"",
      "idEstadoDet":""
    };

    // Iniciamos los Parametros de Secuenciales | Oficio Final
    this.paramsSecuenciaDet = {
      "codSecuencial"  : "",
      "tablaSecuencia" : "",
      "idTipoDocumento" : ""
    };

    // Iniciamos los Parametros de Secuenciales | Oficio de Respuesta
    this.paramsSecuenciaOficioRespuesta = {
      "codSecuencial"  : "",
      "tablaSecuencia" : "",
      "idTipoDocumento" : ""
    };


    // Definicion de la Insercion de los Datos de Nuevo Usuario
    this.finalizarOficios = new FinalizarActividad(null, null, null, null,null, null, null, null, null, null,  null,  null, null, 5,  null, null, null, null, null, null);

    // Inicializamos el Llenado de las Tablas
    this.getlistaFinalizarOficiosTable();

    // Generar la Lista de Secuenciales
    this.listarCodigoCorrespondenciaDet();
    this.listarCodigoCorrespondenciaOfiResp();

  }


  /*****************************************************
  * Funcion: onSubmit()
  * Fecha: 22-09-2017
  * Descripcion: Funcion que envia la Informacion del
  * Forlulario al Service ya serializada por el Model y
  * Luego este llama a la API para interractuar con BD
  * Objetivo: Actualizar datos de Fincalizacion de
  *           Ofico en la BD, Llamando a la API
  ******************************************************/
  onSubmit(forma:NgForm, opcion ){
    // Parametros de la Lista de los Funcionarios
    // Variable del localStorage, para obtener el Id del Usuario (sub) y luego
    // la parseamos a Objeto Javascript
    let opcionExecute:string;

    this.identity = JSON.parse(localStorage.getItem('identity'));
    this.finalizarOficios.idDeptoFuncional = this.identity.idDeptoFuncional;
    this.finalizarOficios.idFuncionarioAsigmado = this.identity.sub;

    // Parametros de l Secuenciales
    this.codigoSecuenciaDet    = this.JsonOutgetCodigoSecuenciaDet[0].codSecuencial;
    this.codigoSecuenciaOficioRespuesta   = this.JsonOutgetCodigoSecuenciaOfiResp[0].codSecuencial;
    this.valorSecuenciaDet     = this.JsonOutgetCodigoSecuenciaDet[0].valor2 + 1;
    this.valorSecuenciaOficioRespuesta     = this.JsonOutgetCodigoSecuenciaOfiResp[0].valor2 + 1;
    //console.log( this.JsonOutgetCodigoSecuenciaDet );

    // Secuenciales de la Tabla correspondencia detalle
    this.finalizarOficios.codCorrespondenciaDet = this.codigoSecuenciaDet + "-" + this.valorSecuenciaDet;
    this.finalizarOficios.codCorrespondenciaNewOfi = this.codigoSecuenciaOficioRespuesta;
    this.finalizarOficios.secuenciaComunicacionDet = this.valorSecuenciaDet;
    this.finalizarOficios.secuenciaComunicacionNewOfi = this.valorSecuenciaOficioRespuesta;
    this.finalizarOficios.secuenciaComunicacionNewOfiAct = this.valorSecuenciaOficioRespuesta - 1;

    // Asignamos los valores al JSON Principal
    this.finalizarOficios.codOficioInterno = this.codOficioIntModal;
    this.finalizarOficios.codOficioExterno = this.codOficioRefModal;
    this.finalizarOficios.nombre1FuncionarioAsigmado = this.nombre1FuncModal;
    this.finalizarOficios.nombre2FuncionarioAsigmado = this.nombre2FuncModal;
    this.finalizarOficios.apellido1FuncionarioAsigmado = this.apellido1FuncModal;
    this.finalizarOficios.apellido2FuncionarioAsigmado = this.apellido2FuncModal;
    this.finalizarOficios.codOficioRespuesta = this.idCorrepEncModal;

    // Inicializamos la Instacia al Metodo de la API
      let token1 = this._finalizarOficio.getToken();
      this.loading = 'show';
      this.loading_table = 'show';
      //console.log( this.finalizarOficios );

      // Evalua que Opcion va a Enviar por el Formulario
      if( opcion == 1 ){
        opcionExecute = "finalizarOficioAsignado";
        // Opcion de Finalizacion de Comunicacion
        this._finalizarOficio.finalizarOficioAsignado(token1, this.finalizarOficios).subscribe(
          response => {
              // Obtenemos el Status de la Peticion
              this.status = response.status;
              this.mensajes = response.msg;

              // Condicionamos la Respuesta
              if(this.status != "success"){
                  this.status = "error";
                  this.mensajes = response.msg;
                  if(this.loading = 'show'){
                    this.loading = 'hidden';
                    this.loading_table = 'hide';
                  }
                  alert(this.mensajes);
              }else{
                //this.resetForm();
                this.loading = 'hidden';
                this.loading_table = 'hide';
                this.ngOnInit();
                setTimeout(function() {
                  $('#t_and_c_m').modal('hide');
                }, 600);
                // this.alertShow();
              }
          }, error => {
              //Regisra cualquier Error de la Llamada a la API
              this.errorMessage = <any>error;

              //Evaluar el error
              if(this.errorMessage != null){
                console.log(this.errorMessage);
                this.mensajes = this.errorMessage;
                alert("Error en la Petición !!" + this.errorMessage);

                if(this.loading = 'show'){
                  this.loading = 'hidden';
                  this.loading_table = 'hidden';
                }
              }
          });
      }else {
        this.finalizarOficios.idEstadoAsigna = 8;
        // Opcion de Creacion de Oficio de Respuesta
        this._finalizarOficio.creacionOficioAsignado(token1, this.finalizarOficios).subscribe(
          response => {
              // Obtenemos el Status de la Peticion
              this.status = response.status;
              this.mensajes = response.msg;

              // Condicionamos la Respuesta
              if(this.status != "success"){
                  this.status = "error";
                  this.mensajes = response.msg;
                  if(this.loading = 'show'){
                    this.loading = 'hidden';
                    this.loading_table = 'hide';
                  }
                  alert(this.mensajes);
              }else{
                //this.resetForm();
                this.loading = 'hidden';
                this.loading_table = 'hide';
                this.ngOnInit();
                setTimeout(function() {
                  $('#t_and_c_m2').modal('hide');
                }, 600);

                // this.alertShow();
              }
          }, error => {
              //Regisra cualquier Error de la Llamada a la API
              this.errorMessage = <any>error;

              //Evaluar el error
              if(this.errorMessage != null){
                console.log(this.errorMessage);
                this.mensajes = this.errorMessage;
                alert("Error en la Petición !!" + this.errorMessage);

                if(this.loading = 'show'){
                  this.loading = 'hidden';
                  this.loading_table = 'hidden';
                }
              }
          });
      }

  } // Fin | Metodo onSubmit


  /*****************************************************
  * Funcion: FND-00001
  * Fecha: 21-09-2017
  * Descripcion: Carga la Lista de los Oficios de la BD
  * que pertenecen al usaurio Logeado
  * Objetivo: Obtener la lista de los Oficios de las
  * Comunicaciones de la BD, Llamando a la API, por su
  * metodo ( finalizar-oficios-list ).
  ******************************************************/
  getlistaFinalizarOficiosTable() {
    // Laoding
    this.loading = 'show';

    // Parametros de la Lista de los Funcionarios
    // Variable del localStorage, para obtener el Id del Usuario (sub) y luego
    // la parseamos a Objeto Javascript
    this.identity = JSON.parse(localStorage.getItem('identity'));
    //this.localStorageJSON.idUser = this.identity.sub;
    this.localStorageJSON.idDeptoFunc = this.identity.idDeptoFuncional;

    // Llamar al metodo, de Login para Obtener la Identidad
    this._listasComunes.listasComunesTokenListas( this.localStorageJSON ,"finalizar-oficios-list").subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetlistaOficiosAll = response.data;
            if( this.loading = 'show' ){
              this.loading = 'hidden';
            }
            alert(response.msg);
          }else{
            this.JsonOutgetlistaOficiosAll = response.data;

            this.loading = 'hidden';
            //console.log(this.JsonOutgetlistaOficiosAll);
          }
        });
  } // FIN | FND-00001


  /*****************************************************
  * Funcion: FND-00001.1
  * Fecha: 24-09-2017
  * Descripcion: Carga la Lista de los Oficios de la BD
  * que pertenecen al usaurio Logeado por Estado
  * Objetivo: Obtener la lista de los Oficios de las
  * Comunicaciones de la BD, Llamando a la API, por su
  * metodo ( finalizar-oficios-det-list ).
  ******************************************************/
  getlistaOficiosDetalle( idCorrespondenciaEncIn:number, idEstadoDetIn:number ) {
    // Parametros de la Lista de los Funcionarios
    // Variable del localStorage, para obtener el Id del Usuario (sub) y luego
    // la parseamos a Objeto Javascript
    this.paramsDetalleJson.idCorrespondenciaEnc = idCorrespondenciaEncIn;
    this.paramsDetalleJson.idEstadoDet = idEstadoDetIn;
    this.loading_table = 'show';
    // Llamar al metodo, de Login para Obtener la Identidad
    this._listasComunes.listasComunesTokenListas( this.paramsDetalleJson ,"finalizar-oficios-det-list").subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetlistaOficiosAllDet = response.data;
            if( this.loading = 'show' ){
              this.loading = 'hidden';
              this.loading_table = 'hide';
            }
            alert(response.msg);
          }else{
            this.JsonOutgetlistaOficiosAllDet = response.data;

            this.loading = 'hidden';
            this.loading_table = 'hide';
            this.idCorrepEncModal = response.data.codCorrespondenciaDet;
            //console.log( response.data.codCorrespondenciaDet );
          }
        });
  } // FIN | FND-00001.1


  /****************************************************
  * Funcion: FND-00002
  * Fecha: 22-09-2017
  * Descripcion: Funcion que Obtiene los Datos del Oficio
  * Utilizando parametros del Json de la tabla
  * Objetivo: Obtener los Datos del Oficio seleccionado
  *****************************************************/
  datoOficio( codOficioIntIn:string, codOficioRefIn:string, idDeptoIn:number,
             nombre1funcionarioAsignadoIn:string, apellido1funcionarioAsignadoIn:string,
             nombre2funcionarioAsignadoIn:string, apellido2funcionarioAsignadoIn:string,
             idFuncionarioIn:number, idEstadoAsign:number, idOficioEnc:number ){
   // Seteo de las varibles de la Funcion
    this.codOficioIntModal = codOficioIntIn;
    this.codOficioRefModal = codOficioRefIn;
    this.idDeptoFuncionalModal = idDeptoIn;
    this.nombre1FuncModal = nombre1funcionarioAsignadoIn;
    this.nombre2FuncModal = nombre2funcionarioAsignadoIn;
    this.apellido1FuncModal = apellido1funcionarioAsignadoIn;
    this.apellido2FuncModal = apellido2funcionarioAsignadoIn;
    this.idFuncModal = idFuncionarioIn;

    // Limpia los Campos de las Descripciones
    this.finalizarOficios.descripcionOficio = "";
    this.finalizarOficios.actividadOficio = "";
    this.idCorrepEncModal = "";

    // Llamamos el Oficio Detalle que tiene el estado Asignado
    this.getlistaOficiosDetalle( idOficioEnc, idEstadoAsign );

    // Cambia el valor de optionModal
    this.optionModal = 2;

 } // FIN : FND-00002


 /****************************************************
 * Funcion: FND-00003
 * Fecha: 22-09-2017
 * Descripcion: Funcion que nos permite asegurarnos de
 * salir de la ventana Modal, ya que esta borra los
 * Datos que se hallan metido anteriormente
 * Objetivo: Validar l salida de ventana modal
 *****************************************************/
 validExit(){
  let confirmaExitIn;
  confirmaExitIn = confirm('Estas seguro de salir de la ventana sin grabar los cambios?');

  // Preguntamos por la respuesta del usuario
  if( confirmaExitIn == 1 && this.confirmaExit == 1){
    alert('Adios');
  }else{
    alert('Seguimos');
    return;
  }

 } // FIN : FND-00003


 /*****************************************************
 * Funcion: FND-00004
 * Fecha: 22-09-2017
 * Descripcion: Obtiene la siguiente secuencia
 * Objetivo: Obtener el secuencial de la tabla
 * indicada con su cosigo
 * (gen-secuencia-comunicacion-in).
 ******************************************************/
  listarCodigoCorrespondenciaDet(){
   this.paramsSecuenciaDet.codSecuencial = "COM-IN-DET-OFI";
   this.paramsSecuenciaDet.tablaSecuencia = "tbl_comunicacion_det";
   this.paramsSecuenciaDet.idTipoDocumento = "1";
   let nextCodComunicacion:string = "";
   //Llamar al metodo, de Login para Obtener la Identidad
   //console.log(this.params);
   this._listasComunes.listasComunesToken(this.paramsSecuenciaDet, "gen-secuencia-comunicacion-in" ).subscribe(
       response => {
         // login successful so redirect to return url
         if(response.status == "error"){
           //Mensaje de alerta del error en cuestion
           this.JsonOutgetCodigoSecuenciaDet = response.data;
           alert(response.msg);

         }else{
           this.JsonOutgetCodigoSecuenciaDet = response.data;
           //console.log( this.JsonOutgetCodigoSecuenciaDet );
         }
       });
 } // FIN : FND-00004


 /*****************************************************
 * Funcion: FND-00005
 * Fecha: 23-09-2017
 * Descripcion: Obtiene la siguiente secuencia
 * Objetivo: Obtener el secuencial de la tabla
 * indicada con su cosigo
 * (gen-secuencia-comunicacion-in).
 ******************************************************/
  listarCodigoCorrespondenciaOfiResp(){
   this.paramsSecuenciaOficioRespuesta.codSecuencial = "SCPI";
   this.paramsSecuenciaOficioRespuesta.tablaSecuencia = "tbl_comunicacion_enc";
   this.paramsSecuenciaOficioRespuesta.idTipoDocumento = "1";
   let nextCodComunicacion:string = "";
   //Llamar al metodo, de Login para Obtener la Identidad
   //console.log(this.params);
   this._listasComunes.listasComunesToken( this.paramsSecuenciaOficioRespuesta, "gen-secuencia-comunicacion-in" ).subscribe(
       response => {
         // login successful so redirect to return url
         if(response.status == "error"){
           //Mensaje de alerta del error en cuestion
           this.JsonOutgetCodigoSecuenciaOfiResp = response.data;
           alert(response.msg);

         }else{
           this.JsonOutgetCodigoSecuenciaOfiResp = response.data;
           //console.log( this.JsonOutgetCodigoSecuenciaDet );
         }
       });
 } // FIN : FND-00005

}

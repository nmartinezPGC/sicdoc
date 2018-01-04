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
import { UploadService } from '../../services/shared/upload.service'; //Servico Carga de Arhcivos
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
  providers: [ FinalizarActividadService, ListasComunesService, UploadService ]
})
export class FinalizarActividadComponent implements OnInit {
  public titulo:string = "Agregar / Finalizar Actividad";
  public fechaHoy:Date = new Date();
  public fechafin:string;

  // Variables de Confirmacion
  public confirmaExit:number = 1;
  public optionModal:number = 1;


  // parametros multimedia
  public loading  = 'show';
  public loading_table  = 'hide';
  public loading_tr  = 'hide';

  public loadTabla1:boolean = false;
  public loadTabla2:boolean = false;

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
  // public JsonOutgetCodigoSecuenciaDet:any[];
  public JsonOutgetCodigoSecuenciaDet;
  // public JsonOutgetCodigoSecuenciaOfiResp:any[];
  public JsonOutgetCodigoSecuenciaOfiResp;
  // public JsonOutgetCodigoSecuenciaActividadAgregar:any[];
  public JsonOutgetCodigoSecuenciaActividadAgregar;

  // Array de Documentos de Comunicacion
  public JsonOutgetListaDocumentos = [];

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
  private paramsSecuenciaActividadAgregar;
  public codigoSecuencia; // Secuencia en Texto del Oficio
  public codigoSecuenciaDet; // Secuencia en Texto del Oficio
  public codigoSecuenciaOficioRespuesta; // Secuencia en Texto del Oficio
  public codigoSecuenciaRespActividad; // Secuencia en Texto del Oficio
  public valorSecuenciaDet; // Secuencial del Oficio
  public valorSecuencia; // Secuencial del Oficio
  public valorSecuenciaOficioRespuesta; // Secuencial del Oficio
  public valorSecuenciaRespActividad; // Secuencial del Oficio

  public codigoSec; // Secuencial del Oficio

  // Variables para la Persistencia de los Datos en los Documentos
  public nextDocumento:number = 1;
  public extencionDocumento:string;
  public seziDocumento:number;


  constructor( private _listasComunes: ListasComunesService,
               private _uploadService: UploadService,
               private _finalizarOficio: FinalizarActividadService,
               private _router: Router,
               private _route: ActivatedRoute,
               private _appComponent: AppComponent,
               private _http: Http ) {

                //  // Inicializamos laTabla
                //  this.fillDataTable();
}

 /*****************************************************
 * Funcion: ngOnInit()
 * Fecha: 22-09-2017
 * Descripcion: Funcion inicial de Angular para cargar
 * los disitintos metodos del Component
 * Objetivo: Cargar Components y tener la Comunicacion
 *           de la BD, Llamando a la API
 ******************************************************/
  ngOnInit() {
    // Inicio de Detalle correspondencia
    this.JsonOutgetCodigoSecuenciaDet = {
      "codSecuencial" : "",
      "valor2" : ""
    }

    // Inicio de Detalle correspondencia
    this.JsonOutgetCodigoSecuenciaOfiResp = {
      "codSecuencial" : "",
      "valor2" : ""
    }
    // Inicio de Detalle correspondencia
    this.JsonOutgetCodigoSecuenciaActividadAgregar = {
      "codSecuencial" : "",
      "valor2" : ""
    }

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


    // Iniciamos los Parametros de Secuenciales | Agregar Actividad
    this.paramsSecuenciaActividadAgregar = {
      "codSecuencial"  : "",
      "tablaSecuencia" : "",
      "idTipoDocumento" : ""
    };

    // Definicion de la Insercion de los Datos de Nuevo Usuario
    this.finalizarOficios = new FinalizarActividad(null, null, null, null,null, null, null,
                               null, null, null,  null,  null, null, 5,  null, null, null,
                               null, null, null, null, null, null, null, null);

    // Inicializamos el Llenado de las Tablas
    this.getlistaFinalizarOficiosTable();


    // Array de los Documentos enviados
    this.JsonOutgetListaDocumentos = [];

    // Generar la Lista de Secuenciales
    // this.listarCodigoCorrespondenciaDet(  );
    //this.listarCodigoCorrespondenciaOfiResp();

    // Inicializamos laTabla
    // this.fillDataTable();

  } // FIN | ngOnInit()



  /*****************************************************
  * Funcion: closeModal()
  * Fecha: 10-11-2017
  * Descripcion: Funcion que cierra la ventana Modal de
  * la Pantalla de forma Automatica
  * Objetivo: Cerrar la Modal despues de la Accion del
  * Submit
  ******************************************************/
  closeModal( nameBotton ){
    setTimeout(function() {
      // $('#t_and_c_m').modal('hide');
      $( nameBotton ).click();
    }, 600);
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
    // this.codigoSecuenciaDet             = this.JsonOutgetCodigoSecuenciaDet[0].codSecuencial;
    this.codigoSecuenciaDet             = this.JsonOutgetCodigoSecuenciaDet.codSecuencial;
    // this.codigoSecuenciaOficioRespuesta = this.JsonOutgetCodigoSecuenciaOfiResp[0].codSecuencial;
    this.codigoSecuenciaOficioRespuesta = this.JsonOutgetCodigoSecuenciaOfiResp.codSecuencial;

    // this.codigoSecuenciaRespActividad   = this.JsonOutgetCodigoSecuenciaActividadAgregar[0].codSecuencial;
    this.codigoSecuenciaRespActividad   = this.JsonOutgetCodigoSecuenciaActividadAgregar.codSecuencial;
    // this.valorSecuenciaDet              = this.JsonOutgetCodigoSecuenciaDet[0].valor2 + 1;
    this.valorSecuenciaDet              = this.JsonOutgetCodigoSecuenciaDet.valor2 + 1;
    // this.valorSecuenciaOficioRespuesta  = this.JsonOutgetCodigoSecuenciaOfiResp[0].valor2 + 1;
    this.valorSecuenciaOficioRespuesta  = this.JsonOutgetCodigoSecuenciaOfiResp.valor2 + 1;
    // this.valorSecuenciaRespActividad    = this.JsonOutgetCodigoSecuenciaActividadAgregar[0].valor2 + 1;
    this.valorSecuenciaRespActividad    = this.JsonOutgetCodigoSecuenciaActividadAgregar.valor2 + 1;
    //console.log( this.JsonOutgetCodigoSecuenciaDet );

    // Secuenciales de la Tabla correspondencia detalle
    // ----------- Codigos de las Secuencias --------------------
    this.finalizarOficios.codCorrespondenciaDet = this.codigoSecuenciaDet + "-" + this.valorSecuenciaDet;
    this.finalizarOficios.secuenciaComunicacionFind = this.codigoSecuenciaDet;
    this.finalizarOficios.codCorrespondenciaNewOfi = this.codigoSecuenciaOficioRespuesta;
    this.finalizarOficios.codCorrespondenciaRespAct = this.codigoSecuenciaRespActividad;

    // -----------Valores de las Secuencias ----------------------
    this.finalizarOficios.secuenciaComunicacionDet = this.valorSecuenciaDet;
    this.finalizarOficios.secuenciaComunicacionNewOfi = this.valorSecuenciaOficioRespuesta;
    this.finalizarOficios.secuenciaComunicacionNewOfiAct = this.valorSecuenciaOficioRespuesta - 1;
    this.finalizarOficios.secuenciaComunicacionNewRespActividad = this.valorSecuenciaRespActividad;

    // Asignamos los valores al JSON Principal
    this.finalizarOficios.codOficioInterno = this.codOficioIntModal;
    this.finalizarOficios.codOficioExterno = this.codOficioRefModal;
    this.finalizarOficios.nombre1FuncionarioAsigmado = this.nombre1FuncModal;
    this.finalizarOficios.nombre2FuncionarioAsigmado = this.nombre2FuncModal;
    this.finalizarOficios.apellido1FuncionarioAsigmado = this.apellido1FuncModal;
    this.finalizarOficios.apellido2FuncionarioAsigmado = this.apellido2FuncModal;
    this.finalizarOficios.codOficioRespuesta = this.idCorrepEncModal;

    // Asignamos el valor al tipo de Documento
    this.finalizarOficios.idTipoDocumento = this.paramsSecuenciaDet.idTipoDocumento;

    // Inicializamos la Instacia al Metodo de la API
      let token1 = this._finalizarOficio.getToken();
      this.loading = 'show';
      this.loading_table = 'show';
      // console.log( this.finalizarOficios );

      // Evalua que Opcion va a Enviar por el Formulario ***********************
      // Opcion de Finalizar Oficio ********************************************
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
                //console.log(response.data);

                // Cerramos la ventana Modal de Forma Automatica
                // setTimeout(function() {
                //   $('#t_and_c_m').modal('hide');
                // }, 600);
                this.closeModal('#closeModalFinCom');
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
      // Opcion de Oficio de respuesta *****************************************
      }else if ( opcion == 2 ) {
        this.finalizarOficios.idEstadoAsigna = 8;
        // console.log(this.finalizarOficios);
        // Opcion de Creacion de Comunicacion de Respuesta
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

                // console.log(response.data);
                // Cerramos la ventana Modal de Forma Automatica
                // setTimeout(function() {
                //   $('#t_and_c_m2').modal('hide');
                // }, 600);
                this.closeModal('#closeModalComResp');

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
      // Opcion de Agregar Actividad *******************************************
    }else if ( opcion == 3 ){
        opcionExecute = "agregarActividadResp";
        // Opcion de Agregar Actividad
        // console.log(this.finalizarOficios);
        this._finalizarOficio.agregarActividadResp(token1, this.finalizarOficios).subscribe(
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
                // console.log(response.data);
                // setTimeout(function() {
                //   $('#t_and_c_m3').modal('hide');
                //   // $('#t_and_c_m3').dialog('hide');
                // }, 600);
                this.closeModal('#closeModalAgreCom');
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
  * Funcion: FND-00004
  * Fecha: 29-07-2017
  * Descripcion: Carga la Imagen de usuario desde el File
  * Objetivo: Obtener la imagen que se carga desde el
  * control File de HTML
  * (fileChangeEvent).
  ******************************************************/

  public filesToUpload: Array<File>;
  public resultUpload;

  fileChangeEvent(fileInput: any){
    //console.log('Evento Chge Lanzado'); , codDocumentoIn:string
    this.filesToUpload = <Array<File>>fileInput.target.files;

    // Direccion del Metodo de la API
    // let url = "http://localhost/sicdoc/symfony/web/app_dev.php/comunes/documentos-upload-options";
    let url = "http://172.17.4.162/sicdoc/symfony/web/app.php/comunes/documentos-upload-options";
    // let url = "http://172.17.3.141/sicdoc/symfony/web/app.php/comunes/upload-documento";
    // let url = "http://192.168.0.23/sicdoc/symfony/web/app.php/comunes/upload-documento";

    // Variables del Metodo
    let  error:string;
    let  status:string;
    let  codigoSec:string;

    // Tamaño
    let sizeByte:number = this.filesToUpload[0].size;
    let siezekiloByte:number =  Math.round( sizeByte / 1024 );

    this.seziDocumento = siezekiloByte;

    let type = this.filesToUpload[0].type;

    var filename = $("#pdfDocumento").val();

    // Use a regular expression to trim everything before final dot
    this.extencionDocumento = filename.replace(/^.*\./, '');

    alert('Ext Doc ' + this.extencionDocumento);


    // Seteamos el valore del Nombre del Documento
    // let secComunicacion = this.JsonOutgetCodigoSecuenciaActividadAgregar[0].valor2 + 1;
    let secComunicacion = this.JsonOutgetCodigoSecuenciaActividadAgregar.valor2 + 1;
    // codigoSec = this.JsonOutgetCodigoSecuenciaActividadAgregar[0].codSecuencial + '-' + secComunicacion;
    codigoSec = this.JsonOutgetCodigoSecuenciaActividadAgregar.codSecuencial + '-' + secComunicacion;

    this._uploadService.makeFileRequestNoToken( url, [ 'name_pdf', codigoSec], this.filesToUpload ).then(
        ( result ) => {
          this.resultUpload = result;
          status = this.resultUpload.status;
          // console.log(this.resultUpload);
          if(status === "error"){
            console.log(this.resultUpload);
            alert(this.resultUpload.msg);
          }
          // this.finalizarOficios.pdfDocumento = this.resultUpload.data;
          // Añadimos a la Tabla Temporal los Items Subidos
          this.createNewFileInput( codigoSec );
        },
        ( error ) => {
          alert(error);
          console.log(error);
        });
  } // FIN : FND-00004


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
    this.loadTabla1 = false;

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
            this.loadTabla1 = true;

            // Inicializamos laTabla
            this.fillDataTable();
            // console.log(this.JsonOutgetlistaOficiosAll);
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
  getlistaOficiosDetalle( idCorrespondenciaEncIn:number, idEstadoDetIn:number,
                          idTipoDocumento:number, idTipoComunicacion:number) {
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

            // Buscamos el Codigo de la Secuencia que vamos a Grabar | SCPI ****
            // Antes el llamdado venia desde:FND-00002
            this.listarCodigoCorrespondenciaDet( idTipoDocumento, idTipoComunicacion );

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
             idFuncionarioIn:number, idEstadoAsign:number, idOficioEnc:number,
             idTipoDocumento:number, idTipoComunicacion:number ){
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
    this.getlistaOficiosDetalle( idOficioEnc, idEstadoAsign, idTipoDocumento, idTipoComunicacion );

    // Buscamos el Codigo de la Secuencia que vamos a Grabar
    // this.listarCodigoCorrespondenciaDet( idTipoDocumento, idTipoComunicacion );
    // Se Comentta el Llamado; porque estropea el Acceso a la Cache de Symfony *
    // Se hace el Llamado desde: FND-00001.1

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
  listarCodigoCorrespondenciaDet( idTipoDocumento:number, idTipoComunicacion:number ){
    //Llamar al metodo, de Login para Obtener la Identidad

    if( idTipoDocumento == 1 ){
      // Verifica si el Tipo de Comunicacion es Entrada (1) / Salida (2)
      if( idTipoComunicacion == 1 ){
        this.paramsSecuenciaDet.codSecuencial = "COM-IN-DET-OFI";
        this.paramsSecuenciaDet.tablaSecuencia = "tbl_comunicacion_det";
        this.paramsSecuenciaDet.idTipoDocumento = idTipoDocumento ;
      } else {
        this.paramsSecuenciaDet.codSecuencial = "COM-OUT-DET-OFI";
        this.paramsSecuenciaDet.tablaSecuencia = "tbl_comunicacion_det";
        this.paramsSecuenciaDet.idTipoDocumento = idTipoDocumento ;
      }

    } else if ( idTipoDocumento == 2 ) {
      // Verifica si el Tipo de Comunicacion es Entrada (1) / Salida (2)
      if( idTipoComunicacion == 1 ){
        this.paramsSecuenciaDet.codSecuencial = "COM-IN-DET-MEMO";
        this.paramsSecuenciaDet.tablaSecuencia = "tbl_comunicacion_det";
        this.paramsSecuenciaDet.idTipoDocumento = idTipoDocumento;
      } else {
        this.paramsSecuenciaDet.codSecuencial = "COM-OUT-DET-MEMO";
        this.paramsSecuenciaDet.tablaSecuencia = "tbl_comunicacion_det";
        this.paramsSecuenciaDet.idTipoDocumento = idTipoDocumento;
      }

    } else if ( idTipoDocumento == 3 ) {
      // Verifica si el Tipo de Comunicacion es Entrada (1) / Salida (2)
      if( idTipoComunicacion == 1 ){
        this.paramsSecuenciaDet.codSecuencial = "COM-IN-DET-NOTA-VERBAL";
        this.paramsSecuenciaDet.tablaSecuencia = "tbl_comunicacion_det";
        this.paramsSecuenciaDet.idTipoDocumento = idTipoDocumento;
      } else {
        this.paramsSecuenciaDet.codSecuencial = "COM-OUT-DET-NOTA-VERBAL";
        this.paramsSecuenciaDet.tablaSecuencia = "tbl_comunicacion_det";
        this.paramsSecuenciaDet.idTipoDocumento = idTipoDocumento;
      }

    } else if ( idTipoDocumento == 4 ) {
      // Verifica si el Tipo de Comunicacion es Entrada (1) / Salida (2)
      if( idTipoComunicacion == 1 ){
        this.paramsSecuenciaDet.codSecuencial = "COM-IN-DET-CIRCULAR";
        this.paramsSecuenciaDet.tablaSecuencia = "tbl_comunicacion_det";
        this.paramsSecuenciaDet.idTipoDocumento = idTipoDocumento;
      } else {
        this.paramsSecuenciaDet.codSecuencial = "COM-OUT-DET-CIRCULAR";
        this.paramsSecuenciaDet.tablaSecuencia = "tbl_comunicacion_det";
        this.paramsSecuenciaDet.idTipoDocumento = idTipoDocumento;
      }

    } else if ( idTipoDocumento == 5 ) {
      // Verifica si el Tipo de Comunicacion es Entrada (1) / Salida (2)
      if( idTipoComunicacion == 1 ){
        this.paramsSecuenciaDet.codSecuencial = "COM-IN-DET-MAIL";
        this.paramsSecuenciaDet.tablaSecuencia = "tbl_comunicacion_det_mail";
        this.paramsSecuenciaDet.idTipoDocumento = idTipoDocumento;
      } else {
        this.paramsSecuenciaDet.codSecuencial = "COM-OUT-DET-MAIL";
        this.paramsSecuenciaDet.tablaSecuencia = "tbl_comunicacion_det_mail";
        this.paramsSecuenciaDet.idTipoDocumento = idTipoDocumento;
      }

    } else if ( idTipoDocumento == 7 ){
      // Verifica si el Tipo de Comunicacion es Entrada (1) / Salida (2)
      if( idTipoComunicacion == 1 ){
        this.paramsSecuenciaDet.codSecuencial = "COM-IN-DET-CALL";
        this.paramsSecuenciaDet.tablaSecuencia = "tbl_comunicacion_det_call";
        this.paramsSecuenciaDet.idTipoDocumento = idTipoDocumento;
      } else {
        this.paramsSecuenciaDet.codSecuencial = "COM-OUT-DET-CALL";
        this.paramsSecuenciaDet.tablaSecuencia = "tbl_comunicacion_det_call";
        this.paramsSecuenciaDet.idTipoDocumento = idTipoDocumento;
      }

    } else if ( idTipoDocumento == 8 ) {
      // Verifica si el Tipo de Comunicacion es Entrada (1) / Salida (2)
      if( idTipoComunicacion == 1 ){
        this.paramsSecuenciaDet.codSecuencial = "COM-IN-DET-VERB";
        this.paramsSecuenciaDet.tablaSecuencia = "tbl_comunicacion_det_verb";
        this.paramsSecuenciaDet.idTipoDocumento = idTipoDocumento;
      } else {
        this.paramsSecuenciaDet.codSecuencial = "COM-OUT-DET-VERB";
        this.paramsSecuenciaDet.tablaSecuencia = "tbl_comunicacion_det_verb";
        this.paramsSecuenciaDet.idTipoDocumento = idTipoDocumento;
      }

    }else if ( idTipoDocumento == 9 ) {
      // Verifica si el Tipo de Comunicacion es Entrada (1) / Salida (2)
      if( idTipoComunicacion == 1 ){
        this.paramsSecuenciaDet.codSecuencial = "COM-IN-DET-REUNION";
        this.paramsSecuenciaDet.tablaSecuencia = "tbl_comunicacion_det";
        this.paramsSecuenciaDet.idTipoDocumento = idTipoDocumento;
      } else {
        this.paramsSecuenciaDet.codSecuencial = "COM-OUT-DET-REUNION";
        this.paramsSecuenciaDet.tablaSecuencia = "tbl_comunicacion_det";
        this.paramsSecuenciaDet.idTipoDocumento = idTipoDocumento;
      }

    }// Fin de Condicion


    //Llamar al metodo, de Login para Obtener la Identidad
    // console.log('Entro en 1 listarCodigoCorrespondenciaDet');
    this._listasComunes.listasComunesToken(this.paramsSecuenciaDet, "gen-secuencia-comunicacion-in" ).subscribe(
       response => {
         // login successful so redirect to return url
         if(response.status == "error"){
           //Mensaje de alerta del error en cuestion
           this.JsonOutgetCodigoSecuenciaDet = response.data;
           alert(response.msg);

         }else{
           this.JsonOutgetCodigoSecuenciaDet = response.data;
           // Ejecutamos el llamado al la Segunda Secuencia
           this.listarCodigoCorrespondenciaOfiResp( idTipoDocumento );
          //  console.log( this.JsonOutgetCodigoSecuenciaDet );
           //this.listarCodigoCorrespondenciaAgregarActividad( idTipoDocumentoFuc );
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
  listarCodigoCorrespondenciaOfiResp( idTipoDocumento:number ){

    // Validamos que el Tipo de Documentos pertenesaca al Adecuado
    let idTipoDocumentoIN = idTipoDocumento;

    if( idTipoDocumentoIN == 1 ){
      this.paramsSecuenciaOficioRespuesta.codSecuencial = "SCPI";
      this.paramsSecuenciaOficioRespuesta.tablaSecuencia = "tbl_comunicacion_enc";
      this.paramsSecuenciaOficioRespuesta.idTipoDocumento = idTipoDocumentoIN;
    } else if ( idTipoDocumentoIN == 2 ) {
      this.paramsSecuenciaOficioRespuesta.codSecuencial = "SCPI";
      this.paramsSecuenciaOficioRespuesta.tablaSecuencia = "tbl_comunicacion_enc";
      this.paramsSecuenciaOficioRespuesta.idTipoDocumento = idTipoDocumentoIN;
    } else if ( idTipoDocumentoIN == 3 ) {
      this.paramsSecuenciaOficioRespuesta.codSecuencial = "SCPI";
      this.paramsSecuenciaOficioRespuesta.tablaSecuencia = "tbl_comunicacion_enc";
      this.paramsSecuenciaOficioRespuesta.idTipoDocumento = idTipoDocumentoIN;
    } else if ( idTipoDocumentoIN == 4 ) {
      this.paramsSecuenciaOficioRespuesta.codSecuencial = "SCPI";
      this.paramsSecuenciaOficioRespuesta.tablaSecuencia = "tbl_comunicacion_enc";
      this.paramsSecuenciaOficioRespuesta.idTipoDocumento = idTipoDocumentoIN;
    }else {
      this.paramsSecuenciaOficioRespuesta.codSecuencial = "SCPI";
      this.paramsSecuenciaOficioRespuesta.tablaSecuencia = "tbl_comunicacion_enc";
      this.paramsSecuenciaOficioRespuesta.idTipoDocumento = "1";
    }

    //Llamar al metodo, de Login para Obtener la Identidad
    //console.log(this.params);
     // console.log('Entro en 2 listarCodigoCorrespondenciaOfiResp');
     // console.log(this.paramsSecuenciaOficioRespuesta);

   this._listasComunes.listasComunesToken( this.paramsSecuenciaOficioRespuesta, "gen-secuencia-comunicacion-in" ).subscribe(
       response => {
         // Data response false or true
         if(response.status == "error"){
           //Mensaje de alerta del error en cuestion
           this.JsonOutgetCodigoSecuenciaOfiResp = response.data;
           // console.log('1. Error JsonOutgetCodigoSecuenciaOfiResp');
           alert(response.msg);

         }else{
           this.JsonOutgetCodigoSecuenciaOfiResp = response.data;
           // console.log( this.JsonOutgetCodigoSecuenciaOfiResp );
         }
       },
         ( error ) => {
           alert(error);
           console.log(error);
         });
 } // FIN : FND-00005


 /*****************************************************
 * Funcion: FND-00005
 * Fecha: 23-09-2017
 * Descripcion: Obtiene la siguiente secuencia
 * Objetivo: Obtener el secuencial de la tabla
 * indicada con su cosigo
 * (gen-secuencia-comunicacion-in).
 ******************************************************/
  listarCodigoCorrespondenciaAgregarActividad( idTipoDocumentoIn:number, idTipoComunicacion:number ){
    // Condicion del Secuencial Segun el Tipo de Documento
    //Evaluamos el valor del Tipo de Documento
    // console.log('Tipo Documento ' + idTipoDocumentoIn);
    // console.log('Tipo Comunicacion ' + idTipoComunicacion);

    // Iniciamos los Parametros de Secuenciales | Agregar Actividad
    this.paramsSecuenciaActividadAgregar = {
      "codSecuencial"  : "",
      "tablaSecuencia" : "",
      "idTipoDocumento" : ""
    };

     if( idTipoDocumentoIn == 1 ){
      // Verifica si el Tipo de Comunicacion es Entrada (1) / Salida (2)
      if( idTipoComunicacion == 1 ){
        this.paramsSecuenciaActividadAgregar.codSecuencial = "COM-IN-DET-OFI";
        this.paramsSecuenciaActividadAgregar.tablaSecuencia = "tbl_comunicacion_det";
        this.paramsSecuenciaActividadAgregar.idTipoDocumento = idTipoDocumentoIn ;
      } else {
        this.paramsSecuenciaActividadAgregar.codSecuencial = "COM-OUT-DET-OFI";
        this.paramsSecuenciaActividadAgregar.tablaSecuencia = "tbl_comunicacion_det";
        this.paramsSecuenciaActividadAgregar.idTipoDocumento = idTipoDocumentoIn ;
      }

     } else if ( idTipoDocumentoIn == 2 ) {
      // Verifica si el Tipo de Comunicacion es Entrada (1) / Salida (2)
      if( idTipoComunicacion == 1 ){
        this.paramsSecuenciaActividadAgregar.codSecuencial = "COM-IN-DET-MEMO";
        this.paramsSecuenciaActividadAgregar.tablaSecuencia = "tbl_comunicacion_det";
        this.paramsSecuenciaActividadAgregar.idTipoDocumento = idTipoDocumentoIn;
      } else {
        this.paramsSecuenciaActividadAgregar.codSecuencial = "COM-OUT-DET-MEMO";
        this.paramsSecuenciaActividadAgregar.tablaSecuencia = "tbl_comunicacion_det";
        this.paramsSecuenciaActividadAgregar.idTipoDocumento = idTipoDocumentoIn;
      }

     } else if ( idTipoDocumentoIn == 3 ) {
      // Verifica si el Tipo de Comunicacion es Entrada (1) / Salida (2)
      if( idTipoComunicacion == 1 ){
        this.paramsSecuenciaActividadAgregar.codSecuencial = "COM-IN-DET-NOTA-VERBAL";
        this.paramsSecuenciaActividadAgregar.tablaSecuencia = "tbl_comunicacion_det";
        this.paramsSecuenciaActividadAgregar.idTipoDocumento = idTipoDocumentoIn;
      } else {
        this.paramsSecuenciaActividadAgregar.codSecuencial = "COM-OUT-DET-NOTA-VERBAL";
        this.paramsSecuenciaActividadAgregar.tablaSecuencia = "tbl_comunicacion_det";
        this.paramsSecuenciaActividadAgregar.idTipoDocumento = idTipoDocumentoIn;
      }

    } else if ( idTipoDocumentoIn == 4 ) {
      // Verifica si el Tipo de Comunicacion es Entrada (1) / Salida (2)
      if( idTipoComunicacion == 1 ){
        this.paramsSecuenciaActividadAgregar.codSecuencial = "COM-IN-DET-CIRCULAR";
        this.paramsSecuenciaActividadAgregar.tablaSecuencia = "tbl_comunicacion_det";
        this.paramsSecuenciaActividadAgregar.idTipoDocumento = idTipoDocumentoIn;
      } else {
        this.paramsSecuenciaActividadAgregar.codSecuencial = "COM-OUT-DET-CIRCULAR";
        this.paramsSecuenciaActividadAgregar.tablaSecuencia = "tbl_comunicacion_det";
        this.paramsSecuenciaActividadAgregar.idTipoDocumento = idTipoDocumentoIn;
      }

     } else if ( idTipoDocumentoIn == 5 ) {
      // Verifica si el Tipo de Comunicacion es Entrada (1) / Salida (2)
      if( idTipoComunicacion == 1 ){
        this.paramsSecuenciaActividadAgregar.codSecuencial = "COM-IN-DET-MAIL";
        this.paramsSecuenciaActividadAgregar.tablaSecuencia = "tbl_comunicacion_det_mail";
        this.paramsSecuenciaActividadAgregar.idTipoDocumento = idTipoDocumentoIn;
      } else {
        this.paramsSecuenciaActividadAgregar.codSecuencial = "COM-OUT-DET-MAIL";
        this.paramsSecuenciaActividadAgregar.tablaSecuencia = "tbl_comunicacion_det_mail";
        this.paramsSecuenciaActividadAgregar.idTipoDocumento = idTipoDocumentoIn;
      }

    } else if ( idTipoDocumentoIn == 7 ){
      // Verifica si el Tipo de Comunicacion es Entrada (1) / Salida (2)
      if( idTipoComunicacion == 1 ){
        this.paramsSecuenciaActividadAgregar.codSecuencial = "COM-IN-DET-CALL";
        this.paramsSecuenciaActividadAgregar.tablaSecuencia = "tbl_comunicacion_det_call";
        this.paramsSecuenciaActividadAgregar.idTipoDocumento = idTipoDocumentoIn;
      } else if ( idTipoComunicacion == 2 ) {
        this.paramsSecuenciaActividadAgregar.codSecuencial = "COM-OUT-DET-CALL";
        this.paramsSecuenciaActividadAgregar.tablaSecuencia = "tbl_comunicacion_det_call";
        this.paramsSecuenciaActividadAgregar.idTipoDocumento = idTipoDocumentoIn;
      }

     } else if ( idTipoDocumentoIn == 8 ) {
      // Verifica si el Tipo de Comunicacion es Entrada (1) / Salida (2)
      if( idTipoComunicacion == 1 ){
        this.paramsSecuenciaActividadAgregar.codSecuencial = "COM-IN-DET-VERB";
        this.paramsSecuenciaActividadAgregar.tablaSecuencia = "tbl_comunicacion_det_verb";
        this.paramsSecuenciaActividadAgregar.idTipoDocumento = idTipoDocumentoIn;
      } else {
        this.paramsSecuenciaActividadAgregar.codSecuencial = "COM-OUT-DET-VERB";
        this.paramsSecuenciaActividadAgregar.tablaSecuencia = "tbl_comunicacion_det_verb";
        this.paramsSecuenciaActividadAgregar.idTipoDocumento = idTipoDocumentoIn;
      }

    }else if ( idTipoDocumentoIn == 9 ) {
      // Verifica si el Tipo de Comunicacion es Entrada (1) / Salida (2)
      if( idTipoComunicacion == 1 ){
        this.paramsSecuenciaActividadAgregar.codSecuencial = "COM-IN-DET-REUNION";
        this.paramsSecuenciaActividadAgregar.tablaSecuencia = "tbl_comunicacion_det";
        this.paramsSecuenciaActividadAgregar.idTipoDocumento = idTipoDocumentoIn;
      } else {
        this.paramsSecuenciaActividadAgregar.codSecuencial = "COM-OUT-DET-REUNION";
        this.paramsSecuenciaActividadAgregar.tablaSecuencia = "tbl_comunicacion_det";
        this.paramsSecuenciaActividadAgregar.idTipoDocumento = idTipoDocumentoIn;
      }

    }// Fin de Condicion

     // console.log(this.paramsSecuenciaActividadAgregar);

     //Llamar al metodo, de Login para Obtener la Identidad
     //console.log(this.params);
     //  console.log('Entro en 3 listarCodigoCorrespondenciaAgregarActividad');
     this._listasComunes.listasComunesToken( this.paramsSecuenciaActividadAgregar, "gen-secuencia-comunicacion-in" ).subscribe(
         response => {
           // login successful so redirect to return url
           if(response.status == "error"){
             //Mensaje de alerta del error en cuestion
             this.JsonOutgetCodigoSecuenciaActividadAgregar = response.data;
             //alert(response.msg);
             alert('ha ocurrido un error, pulsa F5 para recargar la pagina, si persiste comunicate con el Administrador');
           }else{
             this.JsonOutgetCodigoSecuenciaActividadAgregar = response.data;
            //  console.log( this.JsonOutgetCodigoSecuenciaActividadAgregar );
           }
         });
 } // FIN : FND-00005


 /*****************************************************
 * Funcion: FND-00006
 * Fecha: 06-10-2017
 * Descripcion: Realiza el llenado de la Tabla con Todos
 * los Filtros
 * Params: Array de los Estado y Tipos Comunicacion de
 * los Checkbox
 ******************************************************/
 fillDataTable(){
   setTimeout(function () {
     $ (function () {
         $('#example').DataTable({
           "destroy": true,
           // Tamaño de la Pagina
           "pageLength": 5,
           // Cambiar las Propiedades de Lenguaje
           "language":{
               "lengthMenu": "Mostrar _MENU_ registros por pagina",
               "info": "Mostrando pagina _PAGE_ de _PAGES_",
                   "infoEmpty": "No hay registros disponibles",
                   "infoFiltered": "(filtrada de _MAX_ registros)",
                   "loadingRecords": "Cargando...",
                   "processing":     "Procesando...",
                   "search": "Buscar:",
                   "zeroRecords":    "No se encontraron registros coincidentes",
                   "paginate": {
                       "next":       "Siguiente",
                       "previous":   "Anterior"
                   },
           },
         });
     });
   }, 500);
 } // FIN | FND-00006


 /*****************************************************
 * Funcion: FND-00011
 * Fecha: 18-10-2017
 * Descripcion: Creacion de nuevo File input
 * ( createNewFileInput ).
 ******************************************************/
 createNewFileInput( nameDoc ){
  // Actualiza el valor de la Secuencia
  let secActual = this.nextDocumento - 1;
  let mesAct = this.fechaHoy.getMonth() + 1;

  // Mes Actual
  let final_month = mesAct.toString();
  if( mesAct <= 9 ){
    final_month = "0" + final_month;
  }


  // Dia del Mes
  let day = this.fechaHoy.getDate(); // Dia
  let final_day = day.toString();
  if( day <= 9 ){
    final_day = "0" + final_day;
  }

  let newSecAct = nameDoc + "-"  + this.fechaHoy.getFullYear() +  "-" + final_month + "-" + final_day;


  this.JsonOutgetListaDocumentos.push({
    "nameDoc": newSecAct,
    "extDoc": this.extencionDocumento,
    "pesoDoc": this.seziDocumento
  });


  this.finalizarOficios.pdfDocumento = this.JsonOutgetListaDocumentos;

  // $("#newTable").append('<tr> ' +
  //                    '   <th scope="row">'+ secActual +'</th> ' +
  //                    '   <td>' + newSecAct + '</td> ' +
  //                    '   <td>'+ this.extencionDocumento +'</td> ' +
  //                    '   <td>'+ this.seziDocumento +'</td> ' +
  //                    '   <td><a style="cursor: pointer" id="delDoc"> Borrar </a></td> ' +
  //                    ' </tr>');

 } // FIN | FND-00011


}

import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { FormGroup, FormArray,FormBuilder, FormControl, Validators } from '@angular/forms';
import { RouterModule, Routes, ActivatedRoute, Router } from '@angular/router';
import { HttpModule,  Http, Response, Headers } from '@angular/http';

// Lirerias para el AutoComplete
import {Observable, Subscription, Subject} from 'rxjs';
import 'rxjs/add/operator/startWith';
import 'rxjs/add/operator/map';

//Importamos los Servicios
import { EditarComunicacionService } from '../../../services/comunicaciones/editar.comunicacion.service'; //Servico de Editar Comunicacion
import { ListasComunesService } from '../../../services/shared/listas.service'; //Servico Listas Comunes
import { CaseSecuencesService } from '../../../services/shared/caseSecuences.service'; //Servico caseSecuence

import { AppComponent } from '../../../app.component'; //Servico del Login

import { NgForm }    from '@angular/forms';

// Importamos la CLase Usuarios del Modelo
import { EditarComunicacionModel } from '../../../models/comunicaciones/editar.comunicacion.model'; // Modelo a Utilizar

// Libreria toasty
import {ToastyService, ToastyConfig, ToastyComponent, ToastOptions, ToastData} from 'ng2-toasty';

declare var $:any;

@Component({
  selector: 'app-editar.comunicacion',
  templateUrl: './editar.comunicacion.component.html',
  styleUrls: ['./editar.comunicacion.component.css'],
  providers: [ ListasComunesService, EditarComunicacionService, CaseSecuencesService ]
})
export class EditarComunicacionComponent implements OnInit {
  // Propiedades de la Clase
  // Datos de la Vetana
  public titulo:string = "Editar Comunicación";
  public fechaHoy:Date = new Date();

  public data;
  public errorMessage;
  public status;
  public statusConsultaCom;
  public mensajes;

  // Loader
  public loading = "hide";
  public hideButton:boolean = false;

  public idEstadoModal:number = 5;

  // parametros multimedia
  public loading_table  = 'hide';
  public loading_tr  = 'hide';
  public loading_tableIn = 'hide';
  public loadTabla2:boolean = true;

  public  codigoSec:string;
  public nombreDoc:string;

  // Datos de la Consulta
  public datosConsulta;
  public temaComFind;
  public descComFind;

  // Llenamos las Lista del HTML
  public JsonOutgetComunicacionChange:any[];
  public JsonOutgetComunicacionFind:any[];
  public JsonOutgetlistaDireccionSRECI:any[];
  public JsonOutgetlistaSubDireccionSRECI:any[];

  // Json de AutoCompleter Funcionarios
  public JsonOutgetlistaFuncionarios:any[];

  // Variables de Datos de envio
  public paramSearchValueSend:string = "";

  public paramsDocumentos;

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
  //Nueva variable
  public idTipoComunicacionModal;
  public idTipoDocumentoModal;
  //Datos de Representacion
  public temaComunicacionModal;
  public institucionComunicaiconModal;
  public descinstitucionComunicaiconModal;

  // Secuencia de Nuevo Regsitro
  private paramsSecuenciaActividadAgregar;
  public JsonOutgetCodigoSecuenciaActividadAgregar;

  //Json de Fucnonarios en List
  public paramsFuncionariosList;


  // Instacia de la variable del Modelo | Json de Parametros
  public _editarComunicacionModel: EditarComunicacionModel;
  addForm: FormGroup; // form group instance

  // Propieadades del AutoComplete
  itemList = [];
  selectedItems = [];
  settings = {};

  // Variable de Sistema
  public urlConfigLocal:string;
  public urlResourseLocal:string;
  public urlComplete:string;


  // Propiedades de Toasty
  getTitle(title:string, num: number): string {
        return title + ' se cerrara en ' +  num + ' segundos ';
  }

  getMessage(msg:string, num: number): string {
      // return msg + ' ' + num;
      return msg;
  }


  /****************************************************************************
  * Constructor de la Clase
  ****************************************************************************/
  constructor( private _listasComunes: ListasComunesService,
               private _EditarComunicacionService: EditarComunicacionService,
               private _caseSecuencesService: CaseSecuencesService,
               private _router: Router,
               private _route: ActivatedRoute,
               private _appComponent: AppComponent,
               private changeDetectorRef: ChangeDetectorRef,
               private _http: Http,
               private toastyService:ToastyService) {
       // Codigo del Constructor
       // Seteo de la Ruta de la Url Config
       this.urlConfigLocal = this._EditarComunicacionService.url;
       this.urlResourseLocal = this._EditarComunicacionService.urlResourses;
       this.urlComplete = this.urlResourseLocal + "uploads/correspondencia/";

       //Carga el Listado de Funcionarios de la SRECI
       // this.getlistaFuncionariosSreci();

       // Cinfuguracion de los Selects
       this.settings = {
         singleSelection: true,
         text: "Selecciona el Funcionario que se Trasladara la Comunicación ... ",
         searchPlaceholderText: "Busca al Funcionario por su Nombre ó Apellido",
         enableSearchFilter: true
       };

  } // Fin de Constructor


  /************************************************
   * Funcion de Inicio de la Clase de Traslado de
   * la Comunicación
  *************************************************/
  ngOnInit() {
      // Definicion de la Insercion de los Datos de Documentos
      this._editarComunicacionModel = new EditarComunicacionModel(1,
            "", "", "", "", "",
            0, "0", 0, 0, "3", 0, 0, "0",
            "0", "0","0", "0", "0",
            0, 0,
            0, 0,
            "0", "0", "0", "0",
            "", "", "",
            "",
            "",
            "",
            "", "", "",
            "", "", "", "" ,"", "", "", "",
            "", "",
            "", "", "");

      // Iniciamos los Parametros de Sub Direcciones
      this.datosConsulta = {
        "temaComunicacion"    : "",
        "descComunicacion"    : "",
        "fechaFechaIngreso"   : "",
        "fechaFechaEntrega"   : "",
        "emailUserCreador"    : "",
        "idComunicacion"      : "",
        "idTipoComunicacion"  : "",
        "idTipoDocumento"     : ""
      };


     // Iniciamos el Json de los Datos de los Fucnonarios
     this.paramsFuncionariosList = {
       "idDeptoFuncional" : "",
       "idDireccionSreci" : ""
     };

     //Reseteamos los Datos de multiselect
     this.selectedItems = [];
     this.itemList = [];

     // Lista de Direcciones
     // this.getlistaDireccionesSRECI();
  } // FIN | ngOnInit()


  /*********************************************************
   * Funcion de Grabacion del Editar de Comunicación
   * Nuevos en la BD
   * Fecha: 2018-03-06
  **********************************************************/
  onSubmit(forma:NgForm){
    // Envio de los Datos, a la API, con el fin de registrarlo en la BD
    this.loading = "show";
    //console.log( this._editarComunicacionModel );

    this._EditarComunicacionService.editarComunicacion( this._editarComunicacionModel ).subscribe(
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
              }
              // alert(this.mensajes);
              this.addToast(4, 'Error:', this.mensajes);
          }else{
            this.loading = 'hidden';
            this.ngOnInit();

            //Cerramos el Loading
            //this.closeModal("#closeModalFinCom");
            //Oculta el Div de Alerta despues de 3 Segundos
            setTimeout(function() {
                $("#alertSuccess").fadeOut(1500);
            },3000);

            //Mensage la Alerta de Success | Submit
            this.addToast(2, 'Confirmado: ', this.mensajes);
          }
      }, error => {
          //Regisra cualquier Error de la Llamada a la API
          this.errorMessage = <any>error;

          //Evaluar el error
          if(this.errorMessage != null){
            console.log(this.errorMessage);
            this.mensajes = this.errorMessage;
            this.addToast(4,"Error: Contacta al Administrador ",this.mensajes);
            //Oculta el Div de Alerta despues de 3 Segundos
            setTimeout(function() {
                $("#alertError").fadeOut(1500);
            },3000);

            //Ocultamos el Loading
            if(this.loading = 'show'){
              this.loading = 'hidden';
            }
          }
      });
  } // FIN | OnngSubmit()


  /****************************************************
  * Funcion: FND-00001
  * Fecha: 22-11-2017
  * Descripcion: Funcion que Obtiene los datos de la
  * Consulta a la BD de la Comunicacion
  * Objetivo: Datos de la Comunicacion
  *****************************************************/
  buscaComunicacion() {
    // console.log(this._editarComunicacionModel);
    if( this._editarComunicacionModel.codCorrespondencia == null ||
        this._editarComunicacionModel.codCorrespondencia == '' ){
      //Mensaje de Alerta
      this.addToast(4,'Error:',
                    'Debes de Ingresar el No. Interno de Comunicación, para continuar.');
      return -1;
    }
    // Mostramos el Loader
    this.loading = "show";

    this.paramSearchValueSend = this._editarComunicacionModel.codCorrespondencia;

    // Solicitud del Servicio de la Busqueda
    this._EditarComunicacionService.buscaComunicacion( this._editarComunicacionModel ).subscribe(
        response => {
          // successful so redirect to return url
          //alert(response.status);
          this.statusConsultaCom = response.status;

          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            // alert(response.msg);
            this.addToast(4, 'Error:', response.msg);
            this.JsonOutgetComunicacionChange = response.data;
            //Reset de los datos
            this.datosConsulta.temaComunicacion = "";
            this.datosConsulta.descComunicacion = "";
            this.datosConsulta.fechaFechaIngreso = "";
            this.datosConsulta.fechaFechaEntrega = "";
            this.datosConsulta.emailUserCreador = "";
            this.datosConsulta.idComunicacion = "";
            this.datosConsulta.idTipoComunicacion = "";
            this.datosConsulta.idTipoDocumento = "";

            // Oculatamos el Loader
            this.loading = "hide";
            this.hideButton = false;

            this.ngOnInit();
          }else{
            //this.data = JSON.stringify(response.data);

            this.JsonOutgetComunicacionChange = response.data;
            // Seteo de los Datos al JsonOutgetComunicacionFind

            this.valoresdataEncJson( this.JsonOutgetComunicacionChange );

            // Ocultamos el Loader
            this.loading = "hide";
            this.hideButton = true;
            // console.log( this.JsonOutgetComunicacionChange );
          }
        });
  } // FIN : FND-00001


  /*****************************************************
  * Funcion: FND-00001.2
  * Fecha: 23-11-2017
  * Descripcion: Seteo de los valores de la Busqueda del
  * datosConsulta
  ******************************************************/
  valoresdataEncJson( dataIn ){
    // Instanciamos los Valores al Json de retorno, que Utilizara el Html
    if( dataIn != null ){
      //dataIn = JSON.stringify( dataIn );
      // Relaciones de las Tablas
      this.datosConsulta.idComunicacion       = dataIn[0].idCorrespondenciaEnc;
      this.datosConsulta.idTipoComunicacion   = dataIn[0].idTipoComunicacion;
      this.datosConsulta.idTipoDocumento      = dataIn[0].idTipoDocumento;

      // Datos Generales
      this.datosConsulta.temaComunicacion = dataIn[0].temaComunicacion;
      this.datosConsulta.descComunicacion = dataIn[0].descCorrespondenciaEnc;
      this.datosConsulta.fechaFechaIngreso = dataIn[0].fechaIngreso;

      // Contenido
      this._editarComunicacionModel.idTipoDocumento = dataIn[0].idTipoDocumento;
      this._editarComunicacionModel.descCorrespondencia = dataIn[0].descCorrespondenciaEnc;
      this._editarComunicacionModel.temaCorrespondencia = dataIn[0].temaComunicacion;
      // Usuario Creador
      this._editarComunicacionModel.idUsuarioAsaignado    = dataIn[0].idUsuario;
      //Fechas
      this._editarComunicacionModel.fechaMaxEntrega      = dataIn[0].fechaMaxEntrega;
      this._editarComunicacionModel.fechaIngreso         = dataIn[0].fechaIngreso;
      this._editarComunicacionModel.horaIngreso          = dataIn[0].horaIngreso;
      this._editarComunicacionModel.horaFinalizacion     = dataIn[0].horaFinalizacion;

      this._editarComunicacionModel.codReferenciaSreci    =  dataIn[0].codReferenciaSreci;

      // datos de los funcionarios
      this._editarComunicacionModel.nombre1Usuario        =  dataIn[0].nombre1Usuario;
      this._editarComunicacionModel.nombre2Usuario        =  dataIn[0].nombre2Usuario;
      this._editarComunicacionModel.apellido1Usuario      =  dataIn[0].apellido1Usuario;
      this._editarComunicacionModel.apellido2Usuario      =  dataIn[0].apellido2Usuario;
      this._editarComunicacionModel.nombre1Funcionario    =  dataIn[0].nombre1Funcionario;
      this._editarComunicacionModel.nombre2Funcionario    =  dataIn[0].nombre2Funcionario;
      this._editarComunicacionModel.apellido1Funcionario  =  dataIn[0].apellido1Funcionario;
      this._editarComunicacionModel.apellido2Funcionario  =  dataIn[0].apellido2Funcionario;

      //Deptos Funcionales
      this._editarComunicacionModel.idDeptoFuncional        =  dataIn[0].idDeptoFuncional;
      this._editarComunicacionModel.descDeptoFuncional      =  dataIn[0].descDeptoFuncional;
      this._editarComunicacionModel.inicialesDeptoFuncional =  dataIn[0].inicialesDeptoFuncional;

      //Direcciones Sreci
      this._editarComunicacionModel.descDireccionSreci      =  dataIn[0].descDireccionSreci;
      this._editarComunicacionModel.inicialesDireccionSreci =  dataIn[0].inicialesDireccionSreci;
      this._editarComunicacionModel.descEstado =  dataIn[0].descripcionEstado;


      // Asignacion a Datos de Modales
      this.codOficioIntModal = this._editarComunicacionModel.codCorrespondencia;
      this.codOficioRefModal = this._editarComunicacionModel.codReferenciaSreci;

    } else {
    //alert('Datos out');
      this.datosConsulta.temaComunicacion = "";
      this.datosConsulta.descComunicacion = "";
      this.datosConsulta.fechaFechaIngreso = "";
      this.datosConsulta.fechaFechaEntrega = "";
      this.datosConsulta.emailUserCreador = "";
      this.datosConsulta.idComunicacion = "";
      this.datosConsulta.idTipoComunicacion = "";
      this.datosConsulta.idTipoDocumento = "";
    }
  } // FIN | FND-00001.2


  /*****************************************************
  * Funcion: FND-00001.4
  * Fecha: 28-02-2018
  * Descripcion: Limpiar el Formulario
  * Objetivo: Limpiar el Formulario
  ******************************************************/
  cleanForm(){
    this.ngOnInit();
  }

  /*****************************************************
  * Funcion: FND-00007
  * Fecha: 31-03-2018
  * Descripcion: Libreria Toasty para los mensajes
  * Objetivo: Metodo de msg en la APP
  ******************************************************/
  addToast(options:number,title:string, msg:string) {
       let interval = 1000;
       let timeoutIn = 11000;
       let seconds = timeoutIn / 1000;
       let subscription: Subscription;

       let toastOptions: ToastOptions = {
           title: this.getTitle(title,0),
           msg: this.getMessage(msg,0),
           showClose: true,
           timeout: timeoutIn,
           theme: 'bootstrap',
           onAdd: (toast: ToastData) => {
               //console.log('Toast ' + toast.id + ' has been added!');
               // Run the timer with 1 second iterval
               let observable = Observable.interval(interval);
               // Start listen seconds beat
               subscription = observable.subscribe((count: number) => {
                   // Update title of toast
                   toast.title = this.getTitle(title, ( seconds - count - 1 ) );
                   // Update message of toast
                   toast.msg = this.getMessage(msg, count);
                   // Extra condition to hide Toast after 10 sec
                   if (count > 10) {
                       // We use toast id to identify and hide it
                       this.toastyService.clear(toast.id);
                   }
               });

           },
           onRemove: function(toast: ToastData) {
               //console.log('Toast ' + toast.id + ' has been removed!');
               // Stop listenning
               subscription.unsubscribe();
           }
       };

       switch ( options ) {
           case 0: this.toastyService.default(toastOptions); break; //default
           case 1: this.toastyService.info(toastOptions); break; //info
           case 2: this.toastyService.success(toastOptions); break; //success
           case 3: this.toastyService.wait(toastOptions); break; //wait
           case 4: this.toastyService.error(toastOptions); break; //error
           case 5: this.toastyService.warning(toastOptions); break; //warning
       }
   } //FIN | FND-00007


}

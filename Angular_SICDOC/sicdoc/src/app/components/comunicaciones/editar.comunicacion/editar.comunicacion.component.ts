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

import { VinculacionComunicacionService } from '../../../services/comunicaciones/vinculacion.service'; //Servico Vinculacion de Comunicacion

// Libreria toasty
import {ToastyService, ToastyConfig, ToastyComponent, ToastOptions, ToastData} from 'ng2-toasty';

declare var $:any;

@Component({
  selector: 'app-editar.comunicacion',
  templateUrl: './editar.comunicacion.component.html',
  styleUrls: ['./editar.comunicacion.component.css'],
  providers: [ ListasComunesService, EditarComunicacionService, CaseSecuencesService, VinculacionComunicacionService ]
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

  private paramsSubDirComVinculante;
  public JsonOutgetlistaSubDireccionSRECIComVinculantes:any[]; // Uso para las Comunicaciones viculantes | 2018-02-20

  public JsonOutgetlistaTiposDocumentos:any[];

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

  private params;

  // Llenamos las Lista del HTML
  public JsonOutgetComunicacionChange:any[];
  public JsonOutgetComunicacionFind:any[];
  public JsonOutgetlistaDireccionSRECI:any[];
  public JsonOutgetlistaSubDireccionSRECI:any[];

  public JsonOutgetlistaPaises:any[];
  public JsonOutgetlistaTipoInstitucion:any[];
  public JsonOutgetlistaInstitucion:any[];

  // Json de AutoCompleter Funcionarios
  public JsonOutgetlistaFuncionarios:any[];

  // Variables de Datos de envio
  public paramSearchValueSend:string = "";

  public paramsComVinculante; // Parametros para las Comunicacion Vinculantes
  public JsonOutgetlistaComunicacionVinculante:any[];  // Json para las Comunciacnon Vinculantes
  public paramsDocumentos;

  // Select de Vinculacion de Comunicacion
  itemComunicacionVincList = [];
  selectedComunicacionVincItems = [];
  settingsComunicacionVinc = {};

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
               private toastyService:ToastyService,
               private _vinculacionComunicacionService: VinculacionComunicacionService,) {
       // Codigo del Constructor
       // Seteo de la Ruta de la Url Config
       this.urlConfigLocal = this._EditarComunicacionService.url;
       this.urlResourseLocal = this._EditarComunicacionService.urlResourses;
       this.urlComplete = this.urlResourseLocal + "uploads/correspondencia/";

       //Carga el Listado de Funcionarios de la SRECI
       // this.getlistaFuncionariosSreci();

       // Cinfuguracion de los Selects
       this.settings = {
         singleSelection: false,
         text: "Selecciona las Direcciones acompañantes ... ",
         selectAllText: 'Selecciona Todos',
         unSelectAllText: 'Deselecciona Todos',
         enableSearchFilter: true,
         badgeShowLimit: 6
       };

       // Configuracion del Select Dinamico
       this.settingsComunicacionVinc = {
        singleSelection: false,
        text: "Selecciona las Comunicaciones Vinculantes ... ",
        selectAllText: 'Selecciona Todas',
        enableCheckAll: false,
        unSelectAllText: 'Deselecciona Todas',
        searchPlaceholderText: 'Selecciona la Comunicación que relaciona el tema ...',
        enableSearchFilter: true,
        limitSelection:7,
        badgeShowLimit: 7,
        maxHeight: 170,
        //limitSelection:6
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
            0, 0, "0", 0, 0, "3", 0, 0, "0",
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
            "", "", "",
            "", "", "");

            this.selectedItems = [];
            this.selectedComunicacionVincItems = [];
            this.itemList = [];
            this.itemComunicacionVincList = [];

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

     // Iniciamos los Parametros de Instituciones
     this.params = {
       "idPais"  : "",
       "idTipoInstitucion"  : ""
     };

     // Iniciamos los Parametros de Sub Direcciones Comunicacones Vinculantes
     this.paramsSubDirComVinculante = {
       "idDireccionSreciComVinc"  : ""
     };

     this.paramsComVinculante = {
       "idDeptoFuncional"  : "",
       "idTipoDocumento"  : "",
       "idTipoComunicacion"  : ""
     };

     // Lista de Direcciones
     // this.getlistaDireccionesSRECI();
     this.getlistaPaises();
     this.getlistaTipoInstituciones();

     this.getlistaTipoDocumentos();

     this.getlistaDireccionesSRECI();

  } // FIN | ngOnInit()

  /******************************************************
   * Funiones de Seleccion en Nuevo Control del Listas
   * Metodologia: ng-selectec2
   * Fecha: 2018-01-19
   * Casos de uso: Lista de las Comunicacones viculantes
  *******************************************************/
  onItemComVinculanteSelect(item: any) {
    console.log(item);
    this.JsonOutgetlistaComunicacionVinculante = this.selectedComunicacionVincItems ;
    this._editarComunicacionModel.comunicacionesVinculantes = this.JsonOutgetlistaComunicacionVinculante;
    console.log( this._editarComunicacionModel.comunicacionesVinculantes );
  }

  OnItemComVinculanteDeSelect(item: any) {
    console.log(item);
    this.JsonOutgetlistaComunicacionVinculante = this.selectedComunicacionVincItems ;
    this._editarComunicacionModel.comunicacionesVinculantes = this.JsonOutgetlistaComunicacionVinculante;
    console.log( this._editarComunicacionModel.comunicacionesVinculantes );
  }

  onSelectComVinculanteAll(items: any) {
    console.log(items);
    this.JsonOutgetlistaComunicacionVinculante = this.selectedComunicacionVincItems ;
    this._editarComunicacionModel.comunicacionesVinculantes = this.JsonOutgetlistaComunicacionVinculante;
    console.log( this._editarComunicacionModel.comunicacionesVinculantes );
  }

  onComVinculanteDeSelectAll(items: any) {
    console.log(items);
    this.JsonOutgetlistaComunicacionVinculante = this.selectedComunicacionVincItems ;
    this._editarComunicacionModel.comunicacionesVinculantes = this.JsonOutgetlistaComunicacionVinculante;
    console.log( this._editarComunicacionModel.comunicacionesVinculantes );
  }



  /*********************************************************
   * Funcion de Grabacion del Editar de Comunicación
   * Nuevos en la BD
   * Fecha: 2018-03-06
  **********************************************************/
  onSubmit(forma:NgForm){
    // Envio de los Datos, a la API, con el fin de registrarlo en la BD
    //Validacion de Accion de Mensaje
    let confirmaPeticion = confirm('Esta seguro de actualizar la información');
    if( confirmaPeticion == false ){
      return -1;
    }

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
      this._editarComunicacionModel.descComunicacionAnterior = dataIn[0].descCorrespondenciaEnc;
      this._editarComunicacionModel.temaCorrespondencia = dataIn[0].temaComunicacion;
      this._editarComunicacionModel.temaComunicacionAnterior = dataIn[0].temaComunicacion;

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

      //Institucion
      this._editarComunicacionModel.idPais =  dataIn[0].idPais;
      this._editarComunicacionModel.idTipoInstitucion    =  dataIn[0].idTipoInstitucion;
      this._editarComunicacionModel.idInstitucion        =  dataIn[0].idInstitucion;
      this._editarComunicacionModel.idInstitucionAnterior =  dataIn[0].idInstitucion;

      this.getlistaInstituciones();

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

   /******************************************************
   * Funcion: FND-00008
   * Fecha: 30-07-2017
   * Descripcion: Carga la Lista de los Paises.
   * Objetivo: Obtener la lista de los Paises de la BD,
   * Llamando a la API, por su metodo (paises-list).
   *******************************************************/
   getlistaPaises() {
     //Llamar al metodo, de Login para Obtener la Identidad
     this._listasComunes.listasComunes("","paises-list").subscribe(
         response => {
           // successful so redirect to return url
           this.mensajes = response.msg;
           if(response.status == "error"){
             //Mensaje de alerta del error en cuestion
             this.JsonOutgetlistaPaises = response.data;
             // alert(response.msg);
             this.addToast(4,"Error",this.mensajes);
           }else{
             //this.data = JSON.stringify(response.data);
             this.JsonOutgetlistaPaises = response.data;
           }
         });
   } // FIN : FND-00008


   /*****************************************************
   * Funcion: FND-00009
   * Fecha: 29-07-2017
   * Descripcion: Carga la Lista de los Tipos de
   * Instituciones
   * Objetivo: Obtener la lista de los Tipos de
   * Instituciones de la BD, Llamando a la API, por su
   * metodo (tipo-instituciones-sreci-list).
   ******************************************************/
   getlistaTipoInstituciones() {
     //Llamar al metodo, de Login para Obtener la Identidad
     this._listasComunes.listasComunes("","tipo-instituciones-sreci-list").subscribe(
         response => {
           // successful so redirect to return url
           this.mensajes = response.msg;
           if(response.status == "error"){
             //Mensaje de alerta del error en cuestion
             this.JsonOutgetlistaTipoInstitucion = response.data;
             // alert(response.msg);
             this.addToast(4,"Error",this.mensajes);
           }else{
             //this.data = JSON.stringify(response.data);
             this.JsonOutgetlistaTipoInstitucion = response.data;
           }
         });
   } // FIN : FND-00009


   /*****************************************************
   * Funcion: FND-000010
   * Fecha: 31-07-2017
   * Descripcion: Carga la Lista de las Instituciones
   * Objetivo: Obtener la lista de los Tipos de usuarios
   * de la BD, Llamando a la API, por su metodo
   * (instituciones-sreci-list).
   ******************************************************/
   getlistaInstituciones() {
       this.params.idPais = this._editarComunicacionModel.idPais;
       this.params.idTipoInstitucion = this._editarComunicacionModel.idTipoInstitucion;
       //Llamar al metodo, de Login para Obtener la Identidad
       //console.log(this.params);
       this._listasComunes.listasComunes(this.params,"instituciones-sreci-list").subscribe(
         response => {
           // successful so redirect to return url
           this.mensajes = response.msg;
           if(response.status == "error"){
             //Mensaje de alerta del error en cuestion
             this.JsonOutgetlistaInstitucion = response.data;
             // alert(response.msg);
             this.addToast(4,"Error",this.mensajes);
           }else{
             this.JsonOutgetlistaInstitucion = response.data;
             // console.log(response.data);
           }
         });
    } // FIN : FND-000010


    /*****************************************************
    * Funcion: FND-00011.2
    * Fecha: 20-01-2018
    * Descripcion: Limpia el Arreglo de Contactos
    * ( cleanComunicacionVinculante ).
    ******************************************************/
    cleanComunicacionVinculante(){
      //Borra el Contenido del Arreglo de Comunicacones Vinculante
      this.paramsComVinculante = {
        "idDeptoFuncional"  : "",
        "idTipoDocumento"  : "",
        "idTipoComunicacion"  : ""
      };
    }


    /*****************************************************
    * Funcion: FND-000021
    * Fecha: 20-02-2018
    * Descripcion: Carga la Lista de las Direcciones de
    * SRECI
    * Objetivo: Obtener la lista de las Direcciones SRECI
    * de la BD, Llamando a la API, por su metodo
    * (dir-sreci-list).
    ******************************************************/
    getlistaDireccionesSRECI() {
      //Llamar al metodo, de Login para Obtener la Identidad
      this._listasComunes.listasComunes("","dir-sreci-list").subscribe(
          response => {
            // login successful so redirect to return url
            if(response.status == "error"){
              //Mensaje de alerta del error en cuestion
              // alert(response.msg);
              //Alerta de Mensajes
              this.addToast(4,"Error",response.msg);
            }else{
              //this.data = JSON.stringify(response.data);
              this.JsonOutgetlistaDireccionSRECI = response.data;
            }
          });
    } // FIN : FND-000021


    /*****************************************************
    * Funcion: FND-000022
    * Fecha: 20-02-2018
    * Descripcion: Carga la Lista de las Sub Direcciones de
    * SRECI
    * Objetivo: Obtener la lista de las Direcciones SRECI
    * de la BD, Llamando a la API, por su metodo
    * ( subdir-sreci-list ).
    ******************************************************/
    getlistaSubDireccionesSRECIComVinculante() {
      //Llamar al metodo, de Login para Obtener la Identidad
      this.paramsSubDirComVinculante.idDireccionSreciComVinc = this._editarComunicacionModel.idDireccionSreci;

      console.log(this.paramsSubDirComVinculante);

      this._listasComunes.listasComunes( this.paramsSubDirComVinculante,"com-vinculantes-subdir-sreci-list").subscribe(
          response => {
            // login successful so redirect to return url
            if(response.status == "error"){
              //Mensaje de alerta del error en cuestion
              this.JsonOutgetlistaSubDireccionSRECIComVinculantes = response.data;
              // alert(response.msg);

              //Alerta de Mensajes
              this.addToast(4,"Error",response.msg);
            }else{
              //this.data = JSON.stringify(response.data);
              this.JsonOutgetlistaSubDireccionSRECIComVinculantes = response.data;
            }
          });
    } // FIN : FND-000022

    /*****************************************************
    * Funcion: FND-00001
    * Fecha: 25-09-2017
    * Descripcion: Carga la Lista de los Tipos de Documentos
    * Objetivo: Obtener la lista de los Tipos de usuarios
    * de la BD, Llamando a la API, por su metodo
    * ( tipo-documento-list ).
    ******************************************************/
      getlistaTipoDocumentos() {
        this._listasComunes.listasComunes( "" ,"tipo-documento-list?activo=1").subscribe(
          response => {
            // login successful so redirect to return url
            if(response.status == "error"){
              //Mensaje de alerta del error en cuestion
              this.JsonOutgetlistaTiposDocumentos = response.data;
              // alert(response.msg);
              this.addToast(4,"Error",response.msg);
            }else{
              this.JsonOutgetlistaTiposDocumentos = response.data;
              // console.log( this.JsonOutgetlistaTiposDocumentos );
            }
          });
    } // FIN : FND-00001


    /********************************************************
    * Funcion: FND-0000020
    * Fecha: 16-02-2018
    * Descripcion: Carga la Lista de Todas las Comunicación
    * que tiene el Departamento Funcional del Usuario
    * Objetivo: Obtener la lista de Todas las Comunicaciones
    * de la BD, Llamando a la API, por su metodo
    * Params: idDeptoFuncional, idTipoDocumento, idTipoComunicacion
    * ( vinculacionComunicacion/vinculacion-de-comunicacion ).
    **********************************************************/
    getlistaComunicacionVinculanteAll(idOpcion:number) {
      // Llamamos al Servicio que provee todas las Comunicaciones por DeptoFuncional
      // Condicionamos la Busqueda
      if( idOpcion == 1 ){
        this.paramsComVinculante.idDeptoFuncional = this._editarComunicacionModel.idDeptoFuncional;
        this.paramsComVinculante.idTipoDocumento  = this._editarComunicacionModel.idTipoDocumento;
        this.paramsComVinculante.idTipoComunicacion = [1];
        //console.log('Caso #1 Ingreso');
      }else if ( idOpcion == 2 ){
        this.paramsComVinculante.idDeptoFuncional = this._editarComunicacionModel.idDeptoFuncional;
        this.paramsComVinculante.idTipoDocumento  = this._editarComunicacionModel.idTipoDocumento;
        this.paramsComVinculante.idTipoComunicacion = [2];
        //console.log('Caso #2 Salidas');
      }else if ( idOpcion == 3 ){
        this.paramsComVinculante.idDeptoFuncional = this._editarComunicacionModel.idDeptoFuncional;
        this.paramsComVinculante.idTipoDocumento  = this._editarComunicacionModel.idTipoDocumento;
        this.paramsComVinculante.idTipoComunicacion = [1,2];
        //console.log('Caso #3 Ambas');
      }

      //console.log(this.paramsComVinculante);
      this._vinculacionComunicacionService.listaComunicacionVinculantes( this.paramsComVinculante ).subscribe(
          response => {
            // login successful so redirect to return url
            if(response.status == "error"){
              //Mensaje de alerta del error en cuestion
              this.JsonOutgetlistaComunicacionVinculante = response.data;
              this.itemComunicacionVincList = [];
              // alert(response.msg);
              //Alerta de Mensajes
              this.addToast(4,"Error",response.msg);
            }else{
              this.JsonOutgetlistaComunicacionVinculante = response.data;

              this.itemComunicacionVincList = this.JsonOutgetlistaComunicacionVinculante;
              //console.log( this.JsonOutgetlistaComunicacionVinculante );
            }
          });
    } // FIN : FND-0000020


}

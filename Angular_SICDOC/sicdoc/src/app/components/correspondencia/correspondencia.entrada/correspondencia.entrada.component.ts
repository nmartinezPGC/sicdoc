import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { FormGroup, FormArray,FormBuilder, FormControl, Validators } from '@angular/forms';
import { RouterModule, Routes, ActivatedRoute, Router } from '@angular/router';
import { HttpModule,  Http, Response, Headers } from '@angular/http';

// Lirerias para el AutoComplete
import {Observable, Subscription, Subject} from 'rxjs';
import 'rxjs/add/operator/startWith';
import 'rxjs/add/operator/map';

//Importamos los Servicios
import { EntradaCorrespondenciaService } from '../../../services/correspondencia/entrada.correspondencia.service'; //Servico de Ingreso de Correspondencia
import { ListasComunesService } from '../../../services/shared/listas.service'; //Servico Listas Comunes
import { UploadService } from '../../../services/shared/upload.service'; //Servico Carga de Arhcivos
import { CaseSecuencesService } from '../../../services/shared/caseSecuences.service'; //Servico caseSecuence

import { AppComponent } from '../../../app.component'; //Servico del Login

import { NgForm }    from '@angular/forms';

// Importamos la CLase Usuarios del Modelo
import { EntradaCorrespondenciaModel } from '../../../models/correspondencia/entrada.correspondencia/entrada.correspondencia.model'; // Modelo a Utilizar

// Libreria toasty
import {ToastyService, ToastyConfig, ToastyComponent, ToastOptions, ToastData} from 'ng2-toasty';

declare var $:any;

@Component({
  selector: 'app-correspondencia.entrada',
  templateUrl: './correspondencia.entrada.component.html',
  styleUrls: ['./correspondencia.entrada.component.css'],
  providers: [ ListasComunesService, EntradaCorrespondenciaService, CaseSecuencesService, UploadService ]
})
export class CorrespondenciaEntradaComponent implements OnInit {
  // Propiedades de la Clase
  // Datos de la Vetana
  public titulo:string = "Ingresar Correspondencia";
  public fechaHoy:Date = new Date();

  public data;
  public errorMessage;
  public status;
  public statusConsultaCom;
  public mensajes;

  // variables de Identificacion
  public identity;
  public token;

  private params;

  // Loader
  public loading = "hide";
  public hideButton:boolean = false;

  // parametros multimedia
  public loading_table  = 'hide';
  public loading_tr  = 'hide';
  public loading_tableIn = 'hide';
  public loadTabla2:boolean = true;

  public  codigoSec:string;
  public nombreDoc:string;

  // Variable de Sistema
  public urlConfigLocal:string;
  public urlResourseLocal:string;
  public urlComplete:string;

  // Propieadades del AutoComplete
  itemList = [];
  selectedItems = [];
  settings = {};

  // Json de Documentos
  public JsonOutgetlistaDocumentos:any[];
  public JsonOutgetlistaDocumentosNew:any[];

  public JsonOutgetlistaDireccionSRECI:any[];

  public JsonOutgetlistaTiposDocumentos:any[];

  public paramsDocumentos;

  // Propiedades de los Resumenes
  public countOficiosIngresados;
  public countOficiosPendientes;
  public countNotasIngresados;
  public countNotasPendientes;

  private paramsIdTipoComSend; // Parametros para el tipo de COmunicacion enviados

  // Json del Recuento de Datos
  public JsonOutgetListaOficiosIngresados:any[];
  public JsonOutgetListaOficiosPendientes:any[];
  public JsonOutgetListaNotasIngresados:any[];
  public JsonOutgetListaNotasFinalizados:any[];

  // Array de Documentos de Comunicacion
  public JsonOutgetListaDocumentos = [];
  public JsonOutgetListaDocumentosNew = [];
  public JsonOutgetListaDocumentosHist = [];

  private JsonOutgetListaDocumentosDelete;

  // Variables para la Persistencia de los Datos en los Documentos
  public nextDocumento:number = 1;
  public extencionDocumento:string;
  public seziDocumento:number;

  public JsonOutgetCodigoSecuenciaActividadAgregar;

  // Instacia de la variable del Modelo | Json de Parametros
  public _entradaCorrespondenciaModel: EntradaCorrespondenciaModel;
  addForm: FormGroup; // form group instance

  // Propiedades de la Institucion
  public JsonOutgetlistaPaises:any[];
  public JsonOutgetlistaTipoInstitucion:any[];
  public JsonOutgetlistaInstitucion:any[];


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
               private _EntradaCorrespondenciaService: EntradaCorrespondenciaService,
               private _caseSecuencesService: CaseSecuencesService,
               private _uploadService: UploadService,
               private _router: Router,
               private _route: ActivatedRoute,
               private _appComponent: AppComponent,
               private changeDetectorRef: ChangeDetectorRef,
               private _http: Http,
               private toastyService:ToastyService) {
       // Codigo del Constructor
       // Seteo de la Ruta de la Url Config
       this.urlConfigLocal = this._EntradaCorrespondenciaService.url;
       this.urlResourseLocal = this._EntradaCorrespondenciaService.urlResourses;
       this.urlComplete = this.urlResourseLocal + "uploads/unidad-correspondencia/";

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
   * Funcion de Inicio de la Clase de Ingreso de
   * las Entradas de Comunicación
  *************************************************/
  ngOnInit() {
    // Hacemos que la variable del Local Storge este en la API
    this.identity = JSON.parse(localStorage.getItem('identity'));

    // Definicion de la Insercion de los Datos de Correspondencia
    this._entradaCorrespondenciaModel = new EntradaCorrespondenciaModel(1,
          "", "", "", "",
          0, 0, 0,
          this.identity.sub, "7", 0, "0",
          "", null);

    // Json de Documento a Borrar
    this.JsonOutgetListaDocumentosDelete = {
      "codDocument": "",
      "extDocument": "",
      "indicadorExt":""
    }

    // Array de los Documentos enviados
    this.JsonOutgetListaDocumentos = [];
    this.JsonOutgetListaDocumentosNew = [];
    this.JsonOutgetListaDocumentosHist = [];

    this.nextDocumento = 0;

    // Json de Documento a Borrar
    this.JsonOutgetListaDocumentosDelete = {
      "codDocument": "",
      "extDocument": "",
      "indicadorExt": "",
      "idCorrespondenciaDet": "",
      "idCorrespondenciaEnc": "",
      "descDocumento": ""
    }

    $("#newTable").children().remove();

    // Limpiamos el Array de los Documentos
    this.JsonOutgetListaDocumentos = [];

    this._entradaCorrespondenciaModel.pdfDocumento = "";

    // Inicio de Detalle correspondencia
    this.JsonOutgetCodigoSecuenciaActividadAgregar = {
      "codSecuencial" : "",
      "valor2" : ""
    };

   //Reseteamos los Datos de multiselect
   this.selectedItems = [];
   this.itemList = [];

   // Iniciamos los Parametros de Instituciones
   this.params = {
     "idPais"  : "",
     "idTipoInstitucion"  : ""
   };

   // Inicializamos los Parametros de Tipo Comunicacion
   this.paramsIdTipoComSend = {
     "idTipoCom" : "",
     "idFuncionarioAsignado" : "",
     "idTipoDoc" : "",
   }

   // Lista de Direcciones
   // Llamado a la Opcion de llenado de las Sub Direcciones
   this.getlistaDireccionesSRECI();

   // this.getlistaDireccionesSRECI();
   // Lsita de Tipo de Documentos
   this.getlistaTipoDocumentos();
   this.getlistaPaises();
   this.getlistaTipoInstituciones();

   //Area de Resumenes de las Correspondencia
   this.getlistaOficosIngresados();
   this.getlistaOficosRecibidos();

   this.getlistaNotasIngresados();
   this.getlistaNotasRecibidos();

  }


  /*********************************************************
   * Funcion de Grabacion del Traslado de Comunicación
   * Nuevos en la BD
   * Fecha: 2018-03-06
  **********************************************************/
  onSubmit(forma:NgForm){
    // Envio de los Datos, a la API, con el fin de registrarlo en la BD
    this.loading = "show";

    this._EntradaCorrespondenciaService.registerNuevaCorrespondencia( this._entradaCorrespondenciaModel ).subscribe(
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
            this.addToast(2, 'Confirmado:', this.mensajes);
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
       this.params.idPais = this._entradaCorrespondenciaModel.idPais;
       this.params.idTipoInstitucion = this._entradaCorrespondenciaModel.idTipoInstitucion;
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
    * Funcion: FND-00002
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

     let newSecAct = this.codigoSec + "-"  + this.fechaHoy.getFullYear() +  "-" + final_month + "-" + final_day;

     this.JsonOutgetListaDocumentosNew.push({
       "nameDoc": newSecAct,
       "extDoc": this.extencionDocumento,
       "pesoDoc": this.seziDocumento,
       "nombreDoc" : this.nombreDoc
     });


     this._entradaCorrespondenciaModel.pdfDocumento = this.JsonOutgetListaDocumentosNew;
     console.log(this.JsonOutgetListaDocumentosNew);
    } // FIN | FND-00002


    /*****************************************************
    * Funcion: FND-00003
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
      let url = this.urlConfigLocal + "/comunes-unidad-correspondencia/documentos-upload-options";

      // Variables del Metodo
      let  error:string;
      let  status:string;
      let  codigoSec:string;

      // Tamaño
      let sizeByte:number = this.filesToUpload[0].size;
      let siezekiloByte:number =  Math.round( sizeByte / 1024 );

      this.seziDocumento = ( siezekiloByte / 1024 );

      let type = this.filesToUpload[0].type;
      let nameDoc = this.filesToUpload[0].name;

      // incluir - 2018-02-27
      this.nombreDoc = nameDoc;

      var filename = $("#pdfDocumento").val();

      // Use a regular expression to trim everything before final dot
      this.extencionDocumento = filename.replace(/^.*\./, '');

      //alert('Ext Doc ' + this.extencionDocumento);

      //Modificacion; Cuando la extencion es PDF => pdf
        if( this.extencionDocumento == "PDF" ){
          this.extencionDocumento = "pdf";
        }else if( this.extencionDocumento == "jpg" || this.extencionDocumento == "JPG" ) {
          this.extencionDocumento = "jpeg";
        }

      // Seteamos el valore del Nombre del Documento
      let secComunicacion = this.JsonOutgetCodigoSecuenciaActividadAgregar.valor2;
      // codigoSec = this.JsonOutgetCodigoSecuenciaActividadAgregar[0].codSecuencial + '-' + secComunicacion;
      codigoSec = this.JsonOutgetCodigoSecuenciaActividadAgregar.codSecuencial + '-' + secComunicacion;

      this.codigoSec = nameDoc + '-' + this.nextDocumento;
      this.nextDocumento = this.nextDocumento + 1;

      // Parametro para documento Seleccionado
      this._entradaCorrespondenciaModel.pdfDocumento = this.codigoSec;

      this._uploadService.makeFileRequestNoToken( url, [ 'name_pdf', this.codigoSec], this.filesToUpload ).then(
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
    } // FIN : FND-00003


    /*****************************************************
    * Funcion: FND-00018
    * Fecha: 15-02-2018
    * Descripcion: Delete de nuevo File input, en Tabla
    * ( deleteRowHomeForm ).
    ******************************************************/
    deleteRowHomeForm(homeFormIndex: number, codDocumentoIn:string, extDocumentoIn:string){
      // Borra el Elemento al Json
      this.JsonOutgetListaDocumentosNew.splice(homeFormIndex,1);
      this.changeDetectorRef.detectChanges();
      this._entradaCorrespondenciaModel.pdfDocumento = "";

      console.log(codDocumentoIn + ' ---- ' + extDocumentoIn);

      // Ejecutamos la Fucnion que Borra el Archivo desde le Servidor
      // Indicador = 1; ya que lleva la Externcion del Documento
      this.borrarDocumentoServer(codDocumentoIn, extDocumentoIn, 1);
      console.log(this.JsonOutgetListaDocumentosNew);
    } // FIN | FND-00018


    /*****************************************************
    * Funcion: FND-00019
    * Fecha: 15-02-2018
    * Descripcion: Metodo para Borrar Documento desde el
    * Servidor
    * metodo ( borrar-documento-server ).
    ******************************************************/
    borrarDocumentoServer(codDocumentoIn:string, extDocumentoIn:string, indicadorExt:number) {
      //Llamar al metodo, de Login para Obtener la Identidad
      // Agrega Items al Json
      this.JsonOutgetListaDocumentosDelete.codDocument =  codDocumentoIn;
      console.log('borrarDocumentoServer ++++  ' + this.JsonOutgetListaDocumentosDelete.codDocument);
      // Cambiamos la Extencion si es jpg
      if( extDocumentoIn == "jpg" || extDocumentoIn == "JPG" ){
        extDocumentoIn = "jpeg";
      }else if ( extDocumentoIn == "PNG" ){
        extDocumentoIn = "png";
      }

      this.JsonOutgetListaDocumentosDelete.extDocument = extDocumentoIn;
      this.JsonOutgetListaDocumentosDelete.indicadorExt = indicadorExt;

      //Ejecucion del Servicio de Borrar Documento del Servidor
      this._uploadService.borrarDocumentoUC( this.JsonOutgetListaDocumentosDelete ).subscribe(
          response => {
            // login successful so redirect to return url
            if(response.status == "error"){
              //Mensaje de alerta del error en cuestion
              this.addToast(4,"Error: ",response.msg);
            }
          });
    } // FIN : FND-00019


    /*****************************************************
    * Funcion: FND-00007
    * Fecha: 31-08-2017
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
              this.addToast(4,"Error", response.msg);
            }else{
              //this.data = JSON.stringify(response.data);
              this.JsonOutgetlistaDireccionSRECI = response.data;
            }
          });
    } // FIN : FND-00007



    /*****************************************************
    * Funcion: FND-00008
    * Fecha: 25-09-2017
    * Descripcion: Carga la Lista de los Tipos de Documentos
    * Objetivo: Obtener la lista de los Tipos de usuarios
    * de la BD, Llamando a la API, por su metodo
    * ( tipo-documento-list ).
    ******************************************************/
      getlistaTipoDocumentos() {
        this._listasComunes.listasComunes( 2 ,"tipo-documento-list?activo=2").subscribe(
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
    } // FIN : FND-00008


    /*****************************************************
    * Funcion: FND-00009
    * Fecha: 11-09-2017
    * Descripcion: Carga de los Oficios que se han ingresado
    * a la Tabla tbl_comunicacion_enc
    * Objetivo: Obtener la lista de los Oficios Ingresados
    * de la BD, Llamando a la API, por su metodo
    * (com-ingresada-list).
    ******************************************************/
    getlistaOficosIngresados() {
      //Llamar al metodo, de Contador de Comunicaciones Pendientes
      this.paramsIdTipoComSend.idTipoCom = 2;
      this.paramsIdTipoComSend.idFuncionarioAsignado = this.identity.idFuncionario;
      this.paramsIdTipoComSend.idTipoDoc = 1;

      this._EntradaCorrespondenciaService.documentosIngresados( this.paramsIdTipoComSend ).subscribe(
          response => {
            // login successful so redirect to return url
            if(response.status == "error"){
              //Mensaje de alerta del error en cuestion
              this.JsonOutgetListaOficiosIngresados = response.data;
              this.countOficiosIngresados = "0";
              //alert(response.msg);
            }else{
              //this.data = JSON.stringify(response.data);
              this.JsonOutgetListaOficiosIngresados = response.data;
              this.countOficiosIngresados = this.JsonOutgetListaOficiosIngresados;
              //alert(this.countOficios);
            }
          });
    } // FIN : FND-00009


    /*****************************************************
    * Funcion: FND-00009.1
    * Fecha: 11-09-2017
    * Descripcion: Carga de los Oficios que se han ingresado
    * a la Tabla tbl_comunicacion_enc
    * Objetivo: Obtener la lista de los Oficios Ingresados
    * de la BD, Llamando a la API, por su metodo
    * (com-ingresada-list).
    ******************************************************/
    getlistaNotasIngresados() {
      //Llamar al metodo, de Contador de Comunicaciones Pendientes
      this.paramsIdTipoComSend.idTipoCom = 2;
      this.paramsIdTipoComSend.idFuncionarioAsignado = this.identity.idFuncionario;
      this.paramsIdTipoComSend.idTipoDoc = 3;

      this._EntradaCorrespondenciaService.documentosIngresados( this.paramsIdTipoComSend ).subscribe(
          response => {
            // login successful so redirect to return url
            if(response.status == "error"){
              //Mensaje de alerta del error en cuestion
              this.JsonOutgetListaNotasIngresados = response.data;
              this.countNotasIngresados = "0";
              //alert(response.msg);
            }else{
              //this.data = JSON.stringify(response.data);
              this.JsonOutgetListaNotasIngresados = response.data;
              this.countNotasIngresados = this.JsonOutgetListaNotasIngresados;
              //alert(this.countOficios);
            }
          });
    } // FIN : FND-00009.1


    /*****************************************************
    * Funcion: FND-000010
    * Fecha: 11-09-2017
    * Descripcion: Carga de los Oficios que se han ingresado
    * a la Tabla tbl_comunicacion_enc
    * Objetivo: Obtener la lista de los Oficios Ingresados
    * de la BD, Llamando a la API, por su metodo
    * (com-ingresada-list).
    ******************************************************/
    getlistaOficosRecibidos() {
      //Llamar al metodo, de Contador de Comunicaciones Pendientes
      this.paramsIdTipoComSend.idTipoCom = 2;
      this.paramsIdTipoComSend.idFuncionarioAsignado = this.identity.idFuncionario;
      this.paramsIdTipoComSend.idTipoDoc = 1;

      this._EntradaCorrespondenciaService.documentosRecibidos( this.paramsIdTipoComSend ).subscribe(
          response => {
            // login successful so redirect to return url
            if(response.status == "error"){
              //Mensaje de alerta del error en cuestion
              this.JsonOutgetListaOficiosPendientes = response.data;
              this.countOficiosPendientes = "0";
              //alert(response.msg);
            }else{
              //this.data = JSON.stringify(response.data);
              this.JsonOutgetListaOficiosPendientes = response.data;
              this.countOficiosPendientes = this.JsonOutgetListaOficiosPendientes;
              //alert(this.countOficios);
            }
          });
    } // FIN : FND-000010


    /*****************************************************
    * Funcion: FND-000010.1
    * Fecha: 11-09-2017
    * Descripcion: Carga de los Oficios que se han ingresado
    * a la Tabla tbl_comunicacion_enc
    * Objetivo: Obtener la lista de los Oficios Ingresados
    * de la BD, Llamando a la API, por su metodo
    * (com-ingresada-list).
    ******************************************************/
    getlistaNotasRecibidos() {
      //Llamar al metodo, de Contador de Comunicaciones Pendientes
      this.paramsIdTipoComSend.idTipoCom = 2;
      this.paramsIdTipoComSend.idFuncionarioAsignado = this.identity.idFuncionario;
      this.paramsIdTipoComSend.idTipoDoc = 3;

      this._EntradaCorrespondenciaService.documentosRecibidos( this.paramsIdTipoComSend ).subscribe(
          response => {
            // login successful so redirect to return url
            if(response.status == "error"){
              //Mensaje de alerta del error en cuestion
              this.JsonOutgetListaNotasFinalizados = response.data;
              this.countNotasIngresados = "0";
              //alert(response.msg);
            }else{
              //this.data = JSON.stringify(response.data);
              this.JsonOutgetListaNotasFinalizados = response.data;
              this.countNotasIngresados = this.JsonOutgetListaNotasFinalizados;
              //alert(this.countOficios);
            }
          });
    } // FIN : FND-000010.1




}

import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { FormGroup, FormArray,FormBuilder, FormControl, Validators } from '@angular/forms';
import { RouterModule, Routes, ActivatedRoute, Router } from '@angular/router';
import { HttpModule,  Http, Response, Headers } from '@angular/http';
//import { FileSelectDirective, FileDropDirective, FileUploader } from 'ng2-file-upload/ng2-file-upload'; // Liberia de Documentos

//Libreria toasty
import {ToastyService, ToastyConfig, ToastyComponent, ToastOptions, ToastData} from 'ng2-toasty';

// Lirerias para el AutoComplete
import {Observable, Subscription, Subject} from 'rxjs';
import 'rxjs/add/operator/startWith';
import 'rxjs/add/operator/map';

//Importamos los Servicios
import { AgregarDocumentosService } from '../../../services/comunicaciones/agregar.documentos.service'; //Servico de Agregar Documentos
import { ListasComunesService } from '../../../services/shared/listas.service'; //Servico Listas Comunes
import { UploadService } from '../../../services/shared/upload.service'; //Servico Carga de Arhcivos
import { CaseSecuencesService } from '../../../services/shared/caseSecuences.service'; //Servico caseSecuence

import { AppComponent } from '../../../app.component'; //Servico del Login

import { NgForm }    from '@angular/forms';

// Importamos la CLase Usuarios del Modelo
import { AgregarDocumentoModel } from '../../../models/comunicaciones/agregar.documentos.model'; // Modelo a Utilizar

// Libreria de AutoComplete
import { CompleterService, CompleterData, CompleterItem } from 'ng2-completer';

declare var $:any;

// const URL = '/api/';
const URL = 'http://localhost/sicdoc/symfony/web/app_dev.php/web/uploads/correspondencia';

@Component({
  selector: 'app-agregar.documentos',
  templateUrl: './agregar.documentos.component.html',
  styleUrls: ['./agregar.documentos.component.css'],
  providers: [ ListasComunesService, UploadService, AgregarDocumentosService, UploadService, CaseSecuencesService ]
})
export class AgregarDocumentosComponent implements OnInit {
  // Propiedades de la Clase
  //public uploader:FileUploader = new FileUploader({url: URL});
  public hasBaseDropZoneOver:boolean = false;
  public hasAnotherDropZoneOver:boolean = false;

  // Datos de la Vetana
  public titulo:string = "Documentos de la Comunicación";
  public fechaHoy:Date = new Date();

  // Loader
  public loading = "hide";
  public hideButton:boolean = false;

  public idEstadoModal:number = 5;

  public  codigoSec:string;
  public nombreDoc:string;

  // Llenamos las Lista del HTML
  public JsonOutgetComunicacionChange:any[];
  public JsonOutgetComunicacionFind:any[];

  // Json de Documentos
  public JsonOutgetlistaDocumentos:any[];
  public JsonOutgetlistaDocumentosNew:any[];
  public paramsDocumentos;

  // Datos de la Consulta
  public datosConsulta;
  public temaComFind;
  public descComFind;

  public data;
  public errorMessage;
  public status;
  public statusConsultaCom;
  public mensajes;

  // parametros multimedia
  public loading_table  = 'hide';
  public loading_tr  = 'hide';
  public loading_tableIn = 'hide';
  public loadTabla2:boolean = true;

  public urlConfigLocal:string;
  public urlResourseLocal:string;
  public urlComplete:string;

  // Variables de Datos de envio
  public paramSearchValueSend:string = "";

  // Array de Documentos de Comunicacion
  public JsonOutgetListaDocumentos = [];
  public JsonOutgetListaDocumentosNew = [];
  public JsonOutgetListaDocumentosHist = [];
  // Array de Documentos de Comunicacion a Borrar
  private JsonOutgetListaDocumentosDelete;

  public JsonOutgetCodigoSecuenciaActividadAgregar;

  private paramsSecuenciaActividadAgregar;

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

  // Variables para la Persistencia de los Datos en los Documentos
  public nextDocumento:number = 1;
  public extencionDocumento:string;
  public seziDocumento:number;

  // Instacia de la variable del Modelo | Json de Parametros
  public _documentModel: AgregarDocumentoModel;

  // Propiedades de Toasty
  getTitle(title:string, num: number): string {
        return title + ' se cerrara en ' +  num + ' segundos ';
  }

  getMessage(msg:string, num: number): string {
      // return msg + ' ' + num;
      return msg;
  }

  addForm: FormGroup; // form group instance

  constructor( private _listasComunes: ListasComunesService,
               private _agregarDocumentosService: AgregarDocumentosService,
               private _uploadService: UploadService,
               private _caseSecuencesService: CaseSecuencesService,
               private _router: Router,
               private _route: ActivatedRoute,
               private _appComponent: AppComponent,
               private changeDetectorRef: ChangeDetectorRef,
               private _http: Http,
               private toastyService:ToastyService) {
     // Codigo del Constructor
     // Seteo de la Ruta de la Url Config
     this.urlConfigLocal = this._agregarDocumentosService.url;
     this.urlResourseLocal = this._agregarDocumentosService.urlResourses;
     this.urlComplete = this.urlResourseLocal + "uploads/correspondencia/";
  }


  /************************************************
   * Funcion de Inicio de la Clase de Documentos de
   * la Comunicación
  *************************************************/
  ngOnInit() {
    // Inicializacion del Model
    //Iniciamos los Parametros de Json de Documentos
    this.paramsDocumentos = {
      "searchValueSend" : ""
    };

    // Definicion de la Insercion de los Datos de Documentos
    this._documentModel = new AgregarDocumentoModel(1,
          "", "", "", "", "",
          0, "0", 0, 0, "7", 1, 0, "0",
          "0", "0","0",
          0, 0,
          0, 0,
          "0", "0", "0", "0",
          "", "", "",
          "",
          "",
          "",
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

      // Inicio de Detalle correspondencia
      this.JsonOutgetCodigoSecuenciaActividadAgregar = {
        "codSecuencial" : "",
        "valor2" : ""
      };

      $("#newTable").children().remove();

      // Array de los Documentos enviados
      this.JsonOutgetListaDocumentos = [];
      this.JsonOutgetListaDocumentosNew = [];
      this.JsonOutgetListaDocumentosHist = [];

      this._documentModel.pdfDocumento = "";

      // Json de Documento a Borrar
      this.JsonOutgetListaDocumentosDelete = {
        "codDocument": "",
        "extDocument": "",
        "indicadorExt": "",
        "idCorrespondenciaDet": "",
        "idCorrespondenciaEnc": "",
        "descDocumento": ""
      }

  } // FIN | ngOnInit()


  /*********************************************************
   * Funcion de Grabacion de los Documentos Nuevos en la BD
   * Fecha: 2018-03-06
  **********************************************************/
  onSubmit(forma:NgForm){
    //validamos que se suba el documento
    if( this._documentModel.pdfDocumento == "" ){
      this.addToast(4,"Error: ", "Debes subir el documento para continuar ... ");
      return;
    }

    // Envio de los Datos, a la API, con el fin de registrarlo en la BD
    this.loading = "show";

    this._agregarDocumentosService.registerDocumentos( this._documentModel ).subscribe(
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
              alert(this.mensajes);
          }else{
            this.loading = 'hidden';
            this.ngOnInit();

            //Cerramos el Loading
            this.closeModal("#closeModalFinCom");
            //Oculta el Div de Alerta despues de 3 Segundos
            this.addToast(2,"En hora buena: ",response.msg);
            setTimeout(function() {
                $("#alertSuccess").fadeOut(1500);
            },3000);
          }
      }, error => {
          //Regisra cualquier Error de la Llamada a la API
          this.errorMessage = <any>error;

          //Evaluar el error
          if(this.errorMessage != null){
            console.log(this.errorMessage);
            this.mensajes = this.errorMessage;

            //Oculta el Div de Alerta despues de 3 Segundos
            this.addToast(4,"Error: ", this.mensajes);
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


  /****************************************************
  * Funcion: FND-00001
  * Fecha: 22-11-2017
  * Descripcion: Funcion que Obtiene los datos de la
  * Consulta a la BD de la Comunicacion
  * Objetivo: Datos de la Comunicacion
  *****************************************************/
  buscaComunicacion() {
    // console.log(this._modSolicitudCambioFechas);
    // Mostramos el Loader
    this.loading = "show";

    $("#newTable").children().remove();

    this.paramSearchValueSend = this._documentModel.codCorrespondencia;

    // Solicitud del Servicio de la Busqueda
    this._agregarDocumentosService.buscaComunicacion( this._documentModel ).subscribe(
        response => {
          // login successful so redirect to return url
          //alert(response.status);
          this.statusConsultaCom = response.status;

          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            alert(response.msg);
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
      this._documentModel.idTipoDocumento = dataIn[0].idTipoDocumento;
      this._documentModel.descCorrespondencia = dataIn[0].descCorrespondenciaEnc;
      this._documentModel.temaCorrespondencia = dataIn[0].temaComunicacion;
      // Usuario Creador
      this._documentModel.idUsuarioAsaignado = dataIn[0].idUsuario;
      //Fechas
      this._documentModel.fechaMaxEntrega = dataIn[0].fechaMaxEntrega;
      this._documentModel.fechaIngreso = dataIn[0].fechaIngreso;
      // console.log( this._modSolicitudCambioFechas.fechaMaxEntrega );

      // Ejecutamos el Llamado a la Lista de Documentos
      //Llamado de la Funcion de los Documentos
      this.paramsDocumentos.searchValueSend =  this._documentModel.codCorrespondencia;
      this._documentModel.codReferenciaSreci =  dataIn[0].codReferenciaSreci;

      // Llamado a la Lista de Documentos previamente Ingresados
      this.getlistaDocumentosTable();

      // Asignacion a Datos de Modales
      this.codOficioIntModal = this._documentModel.codCorrespondencia;
      this.codOficioRefModal = this._documentModel.codReferenciaSreci;

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
  * Funcion: FND-00001.3
  * Fecha: 01-11-2017
  * Descripcion: Carga la Lista de los Documentos de la
  * Comunicacion de la BD que pertenecen al usaurio
  * Logeado, en el Detalle
  * Objetivo: Obtener la lista de los Documentos de las
  * Comunicaciones de la BD, Llamando a la API, por su
  * metodo ( documentos/listar-documentos ).
  ******************************************************/
  getlistaDocumentosTable() {
    // Laoding
    this.loading_table = 'show';
    this.loadTabla2 = false;
    // Llamar al metodo, de Service para Obtener los Datos de la Comunicacion
    this._listasComunes.listasDocumentosToken( this.paramsDocumentos, "listar-documentos" ).subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetlistaDocumentos = response.data;
            // Oculta los Loaders
            this.loading_table = 'hide';
            this.loadTabla2 = true;
            //Mensaje de notificacion
            this.addToast(4,"Error: ",response.msg);
            // alert(response.msg);
          }else{
            this.JsonOutgetlistaDocumentos = response.data;
            //this.valoresdataDetJson ( response.data );
            this.loading_table = 'hide';
            this.loadTabla2 = true;
            // console.log( this.JsonOutgetlistaDocumentos );
          }
        });
  } // FIN | FND-00001.3



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


   this._documentModel.pdfDocumento = this.JsonOutgetListaDocumentosNew;
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
   let url = this.urlConfigLocal + "/comunes/documentos-upload-options";

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
   // let secComunicacion = this.JsonOutgetCodigoSecuenciaActividadAgregar[0].valor2 + 1;
   // let secComunicacion = this.JsonOutgetCodigoSecuenciaActividadAgregar.valor2 + 1;
   let secComunicacion = this.JsonOutgetCodigoSecuenciaActividadAgregar.valor2;
   // codigoSec = this.JsonOutgetCodigoSecuenciaActividadAgregar[0].codSecuencial + '-' + secComunicacion;
   codigoSec = this.JsonOutgetCodigoSecuenciaActividadAgregar.codSecuencial + '-' + secComunicacion;

   this.codigoSec = codigoSec + '-' + this.nextDocumento;
   this.nextDocumento = this.nextDocumento + 1;

   // Parametro para documento Seleccionado
   this._documentModel.pdfDocumento = this.codigoSec;

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
 * Funcion: FND-000004
 * Fecha: 23-09-2017
 * Descripcion: Obtiene la siguiente secuencia
 * Objetivo: Obtener el secuencial de la tabla
 * indicada con su cosigo
 * (gen-secuencia-comunicacion-in).
 ******************************************************/
  listarCodigoCorrespondenciaAgregarActividad(){
    // Evalua si hay datos de la Consulta
    if( this._documentModel.descCorrespondencia == null || this._documentModel.descCorrespondencia == '' ){
      alert('Debes de buscar la Comunicación antes de Ingresar mas Documentos');
      this.closeModal("#closeModalFinCom");
      return -1;
    }

    //Condicion del Secuencial Segun el Tipo de Documento
    //Evaluamos el valor del Tipo de Documento
    // Iniciamos los Parametros de Secuenciales | Agregar Actividad
    this.paramsSecuenciaActividadAgregar = {
      "codSecuencial"  : "",
      "tablaSecuencia" : "",
      "idTipoDocumento" : ""
    };

    this.loading_table = "show";

    //Objetivo: Seleccionar la Secuencia
    let paramsSendIn =
              this._caseSecuencesService.caseSecuence( this.datosConsulta.idTipoComunicacion, this.datosConsulta.idTipoDocumento );

    // Asignamos los valores a los parametros de Secuencia a Enviar
    this.paramsSecuenciaActividadAgregar.codSecuencial = paramsSendIn.codSecuencial;
    this.paramsSecuenciaActividadAgregar.tablaSecuencia = paramsSendIn.tablaSecuencia;
    this.paramsSecuenciaActividadAgregar.idTipoDocumento = paramsSendIn.idTipoDocumento;

     //Llamar al metodo, de Login para Obtener la Identidad
     // console.log(this.paramsSecuenciaActividadAgregar);
     // console.log('Entro en 3 listarCodigoCorrespondenciaAgregarActividad()');
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
             // Lo enviamos al Model de la Clase
             this._documentModel.secuenciaComunicacionDet = this.JsonOutgetCodigoSecuenciaActividadAgregar.valor2;
             this._documentModel.codCorrespondenciaDet = this.JsonOutgetCodigoSecuenciaActividadAgregar.codSecuencial;
             console.log( this._documentModel.codCorrespondenciaDet );

             // Hide Loading
             this.loading_table = "hide";
           }
         });
  } // FIN : FND-000004


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
    this._documentModel.pdfDocumento = "";

    // Ejecutamos la Fucnion que Borra el Archivo desde le Servidor
    // Indicador = 1; ya que lleva la Externcion del Documento
    this.borrarDocumentoServer(codDocumentoIn, extDocumentoIn, 1);
    console.log(this.JsonOutgetListaDocumentosNew);
  } // FIN | FND-00018


  /*****************************************************
  * Funcion: FND-00018.1
  * Fecha: 15-02-2018
  * Descripcion: Delete de nuevo File input, en Tabla
  * ( deleteRowHomeFormSelect ).
  ******************************************************/
  deleteRowHomeFormSelect(codDocumentoIn:string, urlDocumentoIn:string, descDocumentoIn:string,
                          idCorrespondenciaDetIn:number, idCorrespondenciaEncIn:number){
    // Confirmacion de la Accion de Borrado de Documento
    let confirmaPeticion = confirm('Estas Seguro de Borrar el Documento?');
    if( confirmaPeticion == false ){
      return -1;
    }
    //Loader
    this.loading = "show";
    //Llamar al metodo, de Login para Obtener la Identidad
    // Agrega Items al Json

    this.JsonOutgetListaDocumentosDelete.codDocument =  codDocumentoIn;
    this.JsonOutgetListaDocumentosDelete.extDocument = urlDocumentoIn;
    this.JsonOutgetListaDocumentosDelete.indicadorExt = 2;
    this.JsonOutgetListaDocumentosDelete.idCorrespondenciaDet = idCorrespondenciaDetIn;
    this.JsonOutgetListaDocumentosDelete.idCorrespondenciaEnc = idCorrespondenciaEncIn;
    this.JsonOutgetListaDocumentosDelete.descDocumento = descDocumentoIn;

    // Solicitud del Servicio de la Busqueda
    this._agregarDocumentosService.deleteDocumentos( this.JsonOutgetListaDocumentosDelete ).subscribe(
        response => {
          //alert(response.status);
          this.statusConsultaCom = response.status;

          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            //Mensaje de notificacion
            this.addToast(4,"Error: ",response.msg);
            // alert(response.msg);

            // Oculatamos el Loader
            this.loading = "hide";
          }else{
            // Ejecutamos la Fucnion que Borra el Archivo desde le Servidor
            this.borrarDocumentoServer(codDocumentoIn, urlDocumentoIn, 2);

            // Listado de Documentos Actualizdo
            $("#newTable").children().remove();
            this.getlistaDocumentosTable();

            // Ocultamos el Loader
            this.loading = "hide";
            //Mensaje de alerta del error en cuestion
            //Mensaje de notificacion
            this.addToast(2,"En hora buena: ",response.msg);
            // alert(response.msg);
          }
        });
  } // FIN | FND-00018.1


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

    // Cambiamos la Extencion si es jpg
    if( extDocumentoIn == "jpg" || extDocumentoIn == "JPG" ){
      extDocumentoIn = "jpeg";
    }else if ( extDocumentoIn == "PNG" ){
      extDocumentoIn = "png";
    }

    this.JsonOutgetListaDocumentosDelete.extDocument = extDocumentoIn;
    this.JsonOutgetListaDocumentosDelete.indicadorExt = indicadorExt;

    //Ejecucion del Servicio de Borrar Documento del Servidor
    this._uploadService.borrarDocumento( this.JsonOutgetListaDocumentosDelete ).subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            //Mensaje de notificacion
            this.addToast(4,"Error: ",response.msg);
            // alert(response.msg);
          }
        });
  } // FIN : FND-00019


  /*****************************************************
  * Funcion: FND-000020
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
           timeout: 7000,
           theme: 'bootstrap',
           onAdd: (toast: ToastData) => {
               console.log('Toast ' + toast.id + ' has been added!');
               // Run the timer with 1 second iterval
               let observable = Observable.interval(interval);
               // Start listen seconds beat
               subscription = observable.subscribe((count: number) => {
                   // Update title of toast
                   toast.title = this.getTitle(title, ( seconds - count - 1 ));
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
               console.log('Toast ' + toast.id + ' has been removed!');
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
   } //FIN | FND-000020



}

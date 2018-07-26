import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { FormGroup, FormArray,FormBuilder, FormControl, Validators } from '@angular/forms';
import { RouterModule, Routes, ActivatedRoute, Router } from '@angular/router';
import { HttpModule,  Http, Response, Headers } from '@angular/http';

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

import { AppComponent } from '../../../app.component'; //Servico del Login

import { NgForm }    from '@angular/forms';

// Importamos la CLase Usuarios del Modelo
import { EditUsuarios } from '../../../models/usuarios/edit.usuario.model'; // Modelo a Utilizar

// Libreria de AutoComplete
import { CompleterService, CompleterData, CompleterItem } from 'ng2-completer';

declare var $:any;

@Component({
  selector: 'app-modifica.usuario',
  templateUrl: './modifica.usuario.component.html',
  styleUrls: ['./modifica.usuario.component.css'],
  providers: [ ListasComunesService, UploadService, AgregarDocumentosService, UploadService ]
})
export class ModificaUsuarioComponent implements OnInit {
  // Propiedades de la Clase
  //public uploader:FileUploader = new FileUploader({url: URL});
  public hasBaseDropZoneOver:boolean = false;
  public hasAnotherDropZoneOver:boolean = false;

  // Datos de la Vetana
  public titulo:string = "Editar Prefil de Usuario";
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
  public _editUserModel: EditUsuarios;

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
     this.urlComplete = this.urlResourseLocal + "uploads/users/";
  }

  /************************************************
   * Funcion de Inicio de la Clase de Documentos de
   * la Comunicación
  *************************************************/
  ngOnInit() {
    // Inicializacion del Model
    // Iniciamos los Parametros de Json de Documentos
    this.paramsDocumentos = {
      "searchValueSend" : ""
    };

    // Definicion de la Insercion de los Datos de Documentos
    this._editUserModel = new EditUsuarios(1,
          "", "", "", "", "",
          "", "", "", "",
          "", 0, 0, 0, 0,
          "",
          0, 0,
          null);

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

      // Array de los Documentos enviados
      this.JsonOutgetListaDocumentos = [];
      this.JsonOutgetListaDocumentosNew = [];
      this.JsonOutgetListaDocumentosHist = [];

      // this._editUserModel.pdfDocumento = "";

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
    this._editUserModel.pdfDocumento = this.codigoSec;

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


   this._editUserModel.pdfDocumento = this.JsonOutgetListaDocumentosNew;
   console.log(this.JsonOutgetListaDocumentosNew);
  } // FIN | FND-00002

}

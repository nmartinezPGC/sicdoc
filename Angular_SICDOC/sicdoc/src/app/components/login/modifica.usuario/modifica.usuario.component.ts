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
import { UsuariosEditService } from '../../../services/usuarios/usuario.edit.service'; //Servico Editar Usuario

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
  providers: [ ListasComunesService, UploadService, AgregarDocumentosService, UploadService, UsuariosEditService ]
})
export class ModificaUsuarioComponent implements OnInit {
  // Propiedades de la Clase
  //public uploader:FileUploader = new FileUploader({url: URL});
  public hasBaseDropZoneOver:boolean = false;
  public hasAnotherDropZoneOver:boolean = false;

  // Datos de la Vetana
  public titulo:string = "Editar Perfil de Usuario";
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

  public identity;
  public token;

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

  // Parametros de la Conuslta del Usuario
  private JsonOutDetailsUser;
  private paramsJsonSendDetailsUser;

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
               private _usuariosEditService: UsuariosEditService,
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
    // Hacemos que la variable del Local Storge este en la API
    this.identity = JSON.parse(localStorage.getItem('identity'));
    this.token = localStorage.getItem('token');

    // Iniciamos los Parametros de Json de Documentos
    this.paramsDocumentos = {
      "searchValueSend" : ""
    };

    // Iniciamos los Parametros de Json de Consulta
    this.paramsJsonSendDetailsUser = {
      "idUser" : this.identity.sub
    };

    // Inicializacion del Model
    // Definicion de la Insercion de los Datos de Documentos
    this._editUserModel = new EditUsuarios(this.identity.sub,
          "", "", "", "", "",
          this.identity.email, "", "", "",
          "", 0, 0, 0, 0,
          "",
          0, 0,
          null);

      // Array de los Documentos enviados
      this.JsonOutgetListaDocumentos = [];
      this.JsonOutgetListaDocumentosNew = [];
      this.JsonOutgetListaDocumentosHist = [];

      this._editUserModel.pdfDocumento = "";

      // Cargamos el Detalle del Usuario
      this.getUserDetails();

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
    let url = this.urlConfigLocal + "/usuario/upload-image-user";

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
    codigoSec = this.identity.codUser;

    this.codigoSec = codigoSec + '-' + this.nextDocumento;
    this.nextDocumento = this.nextDocumento + 1;

    // Parametro para documento Seleccionado
    this._editUserModel.pdfDocumento = this.codigoSec;

    this._uploadService.makeFileRequest( this.token, url, [ 'image', this.codigoSec], this.filesToUpload ).then(
        ( result ) => {
          this.resultUpload = result;
          status = this.resultUpload.status;
          // console.log(this.resultUpload);
          if(status === "error"){
            console.log(this.resultUpload);
            alert(this.resultUpload.msg);
          }
          // Añadimos a la Tabla Temporal los Items Subidos
          // this.createNewFileInput( codigoSec );
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


  /*****************************************************
  * Funcion: FND-000003
  * Fecha: 02-08-2018
  * Descripcion: Carga el detalle del Usuario consultado
  * Objetivo: Obtener el detalle del Usuario a Modificar
  * metodo ( usuarios/user-details ).
  ******************************************************/
  getUserDetails() {
    // Laoding
    this.loading = 'show';
    // Llamar al metodo, de Service para Obtener los Datos de la Comunicacion
    this._usuariosEditService.userDetail( this.paramsJsonSendDetailsUser ).subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutDetailsUser = response.data;
            // Oculta los Loaders
            this.loading = 'hide';
            //Mensaje de notificacion
            alert(response.msg);
          }else{
            this.JsonOutDetailsUser = response.data;
            //this.valoresdataDetJson ( response.data );
            this.loading = 'hide';
            console.log( this.JsonOutDetailsUser );

            // Instancia de los Datos en el Documento
            this.cargarPerfil( this.JsonOutDetailsUser );
          }
        });
  } // FIN | FND-000003


  /*****************************************************
  * Funcion: FND-000004
  * Fecha: 02-08-2018
  * Descripcion: Carga el detalle del Usuario consultado
  * a los inputs del DOM
  * Objetivo: Obtener el detalle del Usuario a Modificar
  ******************************************************/
  cargarPerfil( data_to_user ) {
    // Seteamos los vaolres de la Consulta al DOM
    this._editUserModel.primerNombre = data_to_user.nombre1Usuario;
    this._editUserModel.segundoNombre = data_to_user.nombre2Usuario;
    this._editUserModel.primerApellido = data_to_user.apellido1Usuario;
    this._editUserModel.segundoApellido = data_to_user.apellido2Usuario;

    this._editUserModel.codUsuario = data_to_user.codUsuario;
    this._editUserModel.inicialesUsuario = data_to_user.inicialesUsuario;
  } // FIN | FND-000004


  /*****************************************************
  * Funcion: FND-000003
  * Fecha: 02-08-2018
  * Descripcion: Enviar los parametros del usuario para
  * su posterior edicion en la BD
  * Objetivo: Actualizar datos del Usuario a Modificar
  * metodo ( usuarios/edit ).
  ******************************************************/
  editUser() {
    // Laoding
    this.loading = 'show';
    // Llamado al Method de Editar Usuario
    this._usuariosEditService.editUser( this._editUserModel ).subscribe(
        response => {
          // editUser Method
          if(response.status == "error"){
            // Oculta los Loaders
            this.loading = 'hide';
            //Mensaje de notificacion
            alert(response.msg);
          }else{
            this.loading = 'hide';
            console.log( this._editUserModel );
            //Mensaje de notificacion
            alert(response.msg);
          }
    });
  }

  cleanForm() {
    this.ngOnInit();
  }

}

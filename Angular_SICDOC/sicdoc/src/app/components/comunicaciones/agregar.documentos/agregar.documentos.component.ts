import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { FormGroup, FormArray,FormBuilder, FormControl, Validators } from '@angular/forms';
import { RouterModule, Routes, ActivatedRoute, Router } from '@angular/router';
import { HttpModule,  Http, Response, Headers } from '@angular/http';
import { FileSelectDirective, FileDropDirective, FileUploader } from 'ng2-file-upload/ng2-file-upload'; // Liberia de Documentos

// Lirerias para el AutoComplete
import {Observable} from 'rxjs/Observable';
import 'rxjs/add/operator/startWith';
import 'rxjs/add/operator/map';

//Importamos los Servicios
import { AgregarDocumentosService } from '../../../services/comunicaciones/agregar.documentos.service'; //Servico de Agregar Documentos
import { SolicitudCambioFechaService } from '../../../services/mantenimientos/solicitud.cambio.fecha.service'; //Servico del Matenimiento
import { ListasComunesService } from '../../../services/shared/listas.service'; //Servico Listas Comunes
import { UploadService } from '../../../services/shared/upload.service'; //Servico Carga de Arhcivos

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
  providers: [ ListasComunesService, UploadService, AgregarDocumentosService ]
})
export class AgregarDocumentosComponent implements OnInit {
  // Propiedades de la Clase
  public uploader:FileUploader = new FileUploader({url: URL});
  public hasBaseDropZoneOver:boolean = false;
  public hasAnotherDropZoneOver:boolean = false;

  // Datos de la Vetana
  public titulo:string = "Documentos de la Comunicación";

  // Loader
  public loading = "hide";

  // Llenamos las Lista del HTML
  public JsonOutgetComunicacionChange:any[];
  public JsonOutgetComunicacionFind:any[];

  // Json de Documentos
  public JsonOutgetlistaDocumentos:any[];
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

  // Instacia de la variable del Modelo | Json de Parametros
  public _documentModel: AgregarDocumentoModel;
  addForm: FormGroup; // form group instance

  constructor( private _listasComunes: ListasComunesService,
               private _solicitudCambioFechaService: SolicitudCambioFechaService,
               private _agregarDocumentosService: AgregarDocumentosService,
               private _router: Router,
               private _route: ActivatedRoute,
               private _appComponent: AppComponent,
               private _http: Http) {
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
          "");

      // Iniciamos los Parametros de Sub Direcciones
      this.datosConsulta = {
        "temaComunicacion"  : "",
        "descComunicacion"  : "",
        "fechaFechaIngreso"  : "",
        "fechaFechaEntrega"  : "",
        "emailUserCreador"  : ""
      };

      $("#newTable").children().remove();

  } // FIN | ngOnInit()


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

            // Oculatamos el Loader
            this.loading = "hide";

            this.ngOnInit();
          }else{
            //this.data = JSON.stringify(response.data);

            this.JsonOutgetComunicacionChange = response.data;
            // Seteo de los Datos al JsonOutgetComunicacionFind

            this.valoresdataEncJson( this.JsonOutgetComunicacionChange );

            // Ocultamos el Loader
            this.loading = "hide";
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
      console.log( dataIn[0].temaComunicacion );
      this.datosConsulta.temaComunicacion = dataIn[0].temaComunicacion;
      this.datosConsulta.descComunicacion = dataIn[0].descCorrespondenciaEnc;
      this.datosConsulta.fechaFechaIngreso = dataIn[0].fechaIngreso;

      // Contenido
      this._documentModel.descCorrespondencia = dataIn[0].descCorrespondenciaEnc;
      this._documentModel.temaCorrespondencia = dataIn[0].temaComunicacion;
      // Usuario Creador
      // this._modSolicitudCambioFechas.idUserCreador = dataIn.idUsuario.idUsuario;
      //Fechas
      this._documentModel.fechaMaxEntrega = dataIn[0].fechaMaxEntrega;
      this._documentModel.fechaIngreso = dataIn[0].fechaIngreso;
      // console.log( this._modSolicitudCambioFechas.fechaMaxEntrega );

      // Ejecutamos el Llamado a la Lista de Documentos
      //Llamado de la Funcion de los Documentos
      this.paramsDocumentos.searchValueSend =  this._documentModel.codCorrespondencia;
      this.getlistaDocumentosTable();
    } else {
    //alert('Datos out');
      this.datosConsulta.temaComunicacion = "";
      this.datosConsulta.descComunicacion = "";
      this.datosConsulta.fechaFechaIngreso = "";
      this.datosConsulta.fechaFechaEntrega = "";
      this.datosConsulta.emailUserCreador = "";
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
            alert(response.msg);
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

}

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


  // Instacia de la variable del Modelo | Json de Parametros
  public _documentModel: AgregarDocumentoModel;
  addForm: FormGroup; // form group instance

  constructor( private _listasComunes: ListasComunesService,
               private _agregarDocumentosService: AgregarDocumentosService,
               private _router: Router,
               private _route: ActivatedRoute,
               private _appComponent: AppComponent,
               private _http: Http) {
     // Codigo del Constructor
  }


  /************************************************
   * Funcion de Inicio de la Clase de Documentos de
   * la Comunicación
  *************************************************/
  ngOnInit() {
    // Inicializacion del Model
    // Definicion de la Insercion de los Datos de Documentos
    this._documentModel = new AgregarDocumentoModel(1,
          "", "", "", "", "",
          0, "0", 0, 0, "7", 1, 0, "0",
          "0", "0",
          0, 0,
          0, 0,
          "0", "0", "0", "0",
          "", "", "",
          "",
          "",
          "",
          "");
  } // FIN | ngOnInit()



}

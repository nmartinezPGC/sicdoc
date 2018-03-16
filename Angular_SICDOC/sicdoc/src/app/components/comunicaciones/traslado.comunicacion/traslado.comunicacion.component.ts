import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { FormGroup, FormArray,FormBuilder, FormControl, Validators } from '@angular/forms';
import { RouterModule, Routes, ActivatedRoute, Router } from '@angular/router';
import { HttpModule,  Http, Response, Headers } from '@angular/http';

// Lirerias para el AutoComplete
import {Observable} from 'rxjs/Observable';
import 'rxjs/add/operator/startWith';
import 'rxjs/add/operator/map';

//Importamos los Servicios
import { TrasladoComunicacionService } from '../../../services/comunicaciones/traslado.comunicacion.service'; //Servico de Agregar Documentos
import { ListasComunesService } from '../../../services/shared/listas.service'; //Servico Listas Comunes

import { AppComponent } from '../../../app.component'; //Servico del Login

import { NgForm }    from '@angular/forms';

// Importamos la CLase Usuarios del Modelo
import { TrasladoComunicacionModel } from '../../../models/comunicaciones/traslado.comunicacion.model'; // Modelo a Utilizar

// Libreria de AutoComplete
import { CompleterService, CompleterData, CompleterItem } from 'ng2-completer';

declare var $:any;

@Component({
  selector: 'app-traslado.comunicacion',
  templateUrl: './traslado.comunicacion.component.html',
  styleUrls: ['./traslado.comunicacion.component.css'],
  providers: [ ListasComunesService, TrasladoComunicacionService ]
})
export class TrasladoComunicacionComponent implements OnInit {
  // Propiedades de la Clase
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


  // Instacia de la variable del Modelo | Json de Parametros
  public _trasladoComunicacionModel: TrasladoComunicacionModel;
  addForm: FormGroup; // form group instance


  // Variable de Sistema
  public urlConfigLocal:string;
  public urlResourseLocal:string;
  public urlComplete:string;


  // INI Constructor
  constructor( private _listasComunes: ListasComunesService,
               private _TrasladoComunicacionService: TrasladoComunicacionService,
               private _router: Router,
               private _route: ActivatedRoute,
               private _appComponent: AppComponent,
               private changeDetectorRef: ChangeDetectorRef,
               private _http: Http) {
       // Codigo del Constructor
       // Seteo de la Ruta de la Url Config
       this.urlConfigLocal = this._TrasladoComunicacionService.url;
       this.urlResourseLocal = this._TrasladoComunicacionService.urlResourses;
       this.urlComplete = this.urlResourseLocal + "uploads/correspondencia/";

  } // Fin de Constructor



  // INI - Init
  ngOnInit() {


  } // Fin - Init

}

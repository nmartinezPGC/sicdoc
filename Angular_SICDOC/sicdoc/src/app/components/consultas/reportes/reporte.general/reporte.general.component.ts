import { Component, OnInit } from '@angular/core';

import { FormGroup, FormControl, Validators } from '@angular/forms';

import { RouterModule, Routes, ActivatedRoute, Router } from '@angular/router';

import { HttpModule,  Http, Response, Headers } from '@angular/http';

// Lirerias para el AutoComplete
import {Observable} from 'rxjs/Observable';
import 'rxjs/add/operator/startWith';
import 'rxjs/add/operator/map';

import { AppComponent } from '../../../../app.component'; //Servico del Login

import { NgForm }    from '@angular/forms';

// Importamos los Pipes de la Forma
import { GenerateDatePipe } from '../../../../pipes/common/generate.date.pipe';
import { SearchFilterPipe } from '../../../../pipes/common/generate.search.pipe';

import { DateAdapter, NativeDateAdapter } from '@angular/material';

import {DataSource} from '@angular/cdk/collections';

import 'rxjs/add/observable/of';

// Importamos los Services de la Clase
import { ListasComunesService } from '../../../../services/shared/listas.service'; //Servico Listas Comunes
import { ReporteGeneralService } from '../../../../services/consultas/reporte.general.service'; //Servico Reporte General

// Modelo a Utilizar
import { ReporteGeneral } from '../../../../models/consultas/reporte.general.model'; // Modelo a Utilizar


@Component({
  selector: 'app-reporte-general',
  templateUrl: './reporte.general.component.html',
  styleUrls: ['./reporte.general.component.css'],
  providers: [ ListasComunesService, ReporteGeneralService]
})
export class ReporteGeneralComponent implements OnInit {
  public titulo = "Generacion de Reporte";

  // variables del localStorage
  public identity;
  public token;
  public userId;

  // parametros multimedia
  public loading  = 'hide';
  public loading_table  = 'hide';
  public loading_tr  = 'hide';
  public loading_tableIn = 'hide';

  public loadTabla1:boolean = false;
  public loadTabla2:boolean = false;

  // Instacia del Objeto Model de la Clase
  public _ModelReporteGeneral: ReporteGeneral;

  // Parametros de los Json de la Aplicacion
  public JsonOutgetlistaComunicacionDet:any[];
  public JsonOutgetlistaComunicacionEnc:any[];


  // Variables de envio al Json del Modelo
  public idEstadoArray:any[];
  public idTipoComunicacionArray:any[];

  // Area de Fechas
  public tableConsultaFechas;

  // Parametros de ventana Modal
  public codOficioIntModal;
  public codOficioRefModal;
  public nombre1FuncModal; // Primer Nombre Funcionario Asignado
  public nombre2FuncModal; // Primer Nombre Funcionario Asignado
  public apellido1FuncModal; // Primer Nombre Funcionario Asignado
  public apellido2FuncModal; // Primer Nombre Funcionario Asignado

  // Ini | Definicion del Constructor
  constructor( private _listasComunes: ListasComunesService,
               private _reporteGeneralService: ReporteGeneralService,
               private _router: Router,
               private _route: ActivatedRoute,
               private _appComponent: AppComponent,
               private _http: Http ){
     // Llenado de la Tabla de Encabezado
    //  this.fillDataTable();
  } // Fin | Definicion del Constructor

  ngOnInit() {
  }

}

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

import { CompleterService, CompleterData, CompleterItem } from 'ng2-completer';

import 'rxjs/Rx';

// Libreria para Crear el PDF
import * as jsPDF from "jspdf";
// import * as jsPDF from 'jspdf';
import * as autoTable from 'jspdf-autotable';

// Declaramos las variables para jQuery
declare var jQuery:any;
declare var $:any;

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

  // Variables de Captura de msg
  public status;
  public mensajes;
  public errorMessage;

  // parametros multimedia
  public loading  = 'hide';
  public loading_table  = 'hide';
  public loading_tr  = 'hide';
  public loading_tableIn = 'hide';

  public loadTabla1:boolean = false;
  public loadTabla2:boolean = false;

  protected searchStrDireccion: string;
  protected searchStrFuncionario: string;
  protected captain: string;
  protected dataServiceSubDireccion: CompleterData;
  protected dataServiceFuncionario: CompleterData;

  // Instacia del Objeto Model de la Clase
  public _ModelReporteGeneral: ReporteGeneral;

  // Parametros de los Json de la Aplicacion
  public JsonOutgetlistaSubDireccion:any[];
  public JsonOutgetlistaFuncionarios:any[];
  public JsonOutgetlistaInstitucion:any[];
  public JsonOutgetReporteComunicaion:any[];


  // Variables de envio al Json del Modelo
  public idEstadoComunicacionArray;
  public idTipoComunicacionArray;

  // Area de Fechas
  public tableConsultaFechas;

  // Objeto que Controlara la Forma
  forma:FormGroup;

  // Area de Direcciones
  protected selectedDireccion: string;
  protected selectedFuncionario: string;

  // Valores de Estados de Comunicacion
  public idEstadoIngreso:number = 7;
  public idEstadoAsignado:number = 3;
  public idEstadoEnRespuesta:number = 8;
  public idEstadoFinalizado:number = 5;


  // Valores de Tipos de Comunicacion
  public idTipoComOficio:number = 1;
  public idTipoComCorreos:number = 5;
  public idTipoComLlamadas:number = 7;
  public idTipoComVerbal:number = 8;

  // Ini | Definicion del Constructor
  constructor( private _listasComunes: ListasComunesService,
               private _reporteGeneralService: ReporteGeneralService,
               private _router: Router,
               private _route: ActivatedRoute,
               private _appComponent: AppComponent,
               private _http: Http,
               private completerService: CompleterService ){
     // Llenado de la Tabla de Encabezado
    //  this.fillDataTable();
  } // Fin | Definicion del Constructor


  /*****************************************************
  * Fecha: 15-10-2017
  * Funcion: generatePDF
  * Descripcion: Metodo que Genera el PDF, con la Data
  * ya Cargada con los criterios de seleccion
  ******************************************************/
  generatePDF() {
    var doc = new jsPDF();

    var elementHandler = {
    '#editor': function (element, renderer) {
      return true;
    }
    };
    var source = window.document.getElementById("basic-table");
    doc.fromHTML(
        source,
        15,
        15);

        doc.save('documento.pdf');

  } // FIN | generatePDF


  /*****************************************************
  * Funcion: FND-00006
  * Fecha: 06-10-2017
  * Descripcion: Realiza el llenado de la Tabla con Todos
  * los Filtros
  * Params: Array de los Estado y Tipos Comunicacion de
  * los Checkbox
  ******************************************************/
  fillDataTable(){
    setTimeout(function () {
      $ (function () {
        $('#example').DataTable( {
            dom: 'Bfrtip',
            buttons: [
               // Boton de Imprimir
                {
                  extend: 'print',
                  text: 'Imprimir Todos',
                  message: 'Listado de Comunicaciones',
                  title: 'Informe de Comunicaciones',
                  orientation: 'landscape',
                  pageSize: 'A4',
                },

                // Boton de Importar a PDF
                {
                  extend: 'pdfHtml5',
                  orientation: 'landscape',
                  pageSize: 'A4',
                  title: 'Informe de Comunicaciones',
                  text: 'Exportar a PDF',
                },
                /*{
                  extend: 'excelHtml5',
                  customize: function( xlsx ) {
                      var sheet = xlsx.xl.worksheets['sheet1.xml'];

                      $('row c[r^="C"]', sheet).attr( 's', '2' );
                  }
              }*/
            ]
        } );
      });
    }, 7000);
  } // FIN | FND-00006


  /*****************************************************
  * Funcion: ngOnInit
  * Fecha: 05-10-2017
  * Descripcion: Metodo inicial del Programa
  ******************************************************/
  ngOnInit() {
    // Seteamos los valores de busqueda del Search
    this.searchStrDireccion = "";
    this.searchStrFuncionario = "";

    let array = [];
    // Definicion de la Insercion de los Datos de Nuevo Contacto
    this._ModelReporteGeneral = new ReporteGeneral( null, null, 0, 0,
                                this.idEstadoComunicacionArray, this.idTipoComunicacionArray,
                                null, null);

    // Llenado de la Lista de Sub Direcciones
    this.getlistaSubDireccionesAll();

    // Llenado de la Lista de Funcionarios
    this.getlistaFuncionariosSreci();

  } // FIN ngOnInit


  /*****************************************************
  * Funcion: onSubmit
  * Fecha: 11-10-2017
  * Descripcion: Metodo que envia la Informacion
  ******************************************************/
  onSubmit(forma:NgForm){
      // Parseo de parametros que no se seleccionan
      this.loading_tableIn = 'show';
      let timeAll = false;
      // Ejecucion de los Array de Estados
      this.sendEstComunicacion();
      // Ejecucion de los Array de Tipos
      this.sendTiposComunicacion();

      this._ModelReporteGeneral.idEstadoComunicacion = this.idEstadoComunicacionArray;
      this._ModelReporteGeneral.idTipoComunicacion = this.idTipoComunicacionArray;

      console.log( this._ModelReporteGeneral );
      this._reporteGeneralService.comunicacionFind( this._ModelReporteGeneral).subscribe(
        response => {
            // Obtenemos el Status de la Peticion
            this.status = response.status;
            this.mensajes = response.msg;

            // Condicionamos la Respuesta
            // alert('Paso 1 ' + this.status);
            if(this.status != "success"){
                this.status = "error";
                this.mensajes = response.msg;
                if(this.loading_tableIn = 'show'){
                  this.loading_tableIn = 'hidden';
                }
                //alert('Error Data ' +  this.mensajes);
            }else{
              //this.resetForm();
              this.loading_tableIn = 'hidden';
              // this.ngOnInit();
              // window.location.reload();
              //alert(response.msg);

              this.JsonOutgetReporteComunicaion = response.data;
              console.log(this.JsonOutgetReporteComunicaion);

              // Cargamos la Tabla con las Instancias
              this.fillDataTable();

            }
        }, error => {
            //Regisra cualquier Error de la Llamada a la API
            this.errorMessage = <any>error;

            //Evaluar el error
            if(this.errorMessage != null){
              console.log(this.errorMessage);
              this.mensajes = this.errorMessage;
              alert("Error en la Petición !!" + this.errorMessage);

              if(this.loading = 'show'){
                this.loading = 'hidden';
              }
            }
        });
  } // Fin | Metodo onSubmit


  /*****************************************************
  * Funcion: FND-00001
  * Fecha: 14-10-2017
  * Descripcion: Chekear todas las Opciones
  ******************************************************/
  checkTodos(){
    $('.custom-control-input 1').each(function () {
        if (this.checked) $(this).attr("checked", false);
        else $(this).prop("checked", true);
    });
  } // FIN | FND-00001


  /*****************************************************
  * Funcion: FND-00002
  * Fecha: 14-10-2017
  * Descripcion: Enviar Estados de la Comunicacion
  ******************************************************/
  sendEstComunicacion() {
     let selectedEstComunicacion = [];
     let selectedEstComunicacionIn = [];
     $(":checkbox[name=chkEstCom]").each(function() {
       if (this.checked) {
         // agregas cada elemento.
         selectedEstComunicacion.push( parseInt( $(this).val() ) );
       }
     });

     this.idEstadoComunicacionArray =  selectedEstComunicacion ;
     console.log(this.idEstadoComunicacionArray);
  }// FIN FND-00002


  /*****************************************************
  * Funcion: FND-00003
  * Fecha: 14-10-2017
  * Descripcion: Enviar Tipos de la Comunicacion
  ******************************************************/
  sendTiposComunicacion() {
     var selectedTipoComunicacion = [];
     $(":checkbox[name=chkTipoCom]").each(function() {
       if (this.checked) {
         // agregas cada elemento.
         selectedTipoComunicacion.push( parseInt( $(this).val() ) );
       }
     });
     this.idTipoComunicacionArray = selectedTipoComunicacion;
  } // FIN FND-00003


  /*****************************************************
  * Funcion: FND-00004
  * Fecha: 11-10-2017
  * Descripcion: Funcion para AutoCompletar y sacar el
  * Id de la Data de la Tabla tblInstituciones
  * Params: $event
  ******************************************************/
  protected onSelectedDireccion( item: CompleterItem ) {
    this.selectedDireccion = item? item.originalObject.idDeptoFuncional : "";
    // Seteamos y Parseamos a Int el idInstitucion
    this._ModelReporteGeneral.idDireccion = parseInt(this.selectedDireccion);
  } // FIN | FND-00004


  /*****************************************************
  * Funcion: FND-00004.1
  * Fecha: 12-10-2017
  * Descripcion: Funcion para AutoCompletar y sacar el
  * Id de la Data de la Tabla TblFunionarios
  * Params: $event
  ******************************************************/
  protected onSelectedFuncionarios( item: CompleterItem ) {
    this.selectedFuncionario = item? item.originalObject.idFuncionario : "";
    // Seteamos y Parseamos a Int el idContactoSreci
    this._ModelReporteGeneral.idFuncionarioAsignado = parseInt(this.selectedFuncionario);
  } // FIN | FND-00004.1


  /*****************************************************
  * Funcion: FND-00005
  * Fecha: 31-07-2017
  * Descripcion: Carga la Lista de las Instituciones
  * Objetivo: Obtener la lista de los Tipos de usuarios
  * de la BD, Llamando a la API, por su metodo
  * (instituciones-sreci-list).
  ******************************************************/
  getlistaInstituciones() {
    // Llamamos al Servicio que provee todas las Instituciones
    this._listasComunes.listasComunes("","instituciones-sreci-list").subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetlistaInstitucion = response.data;
            alert(response.msg);

          }else{
            this.JsonOutgetlistaInstitucion = response.data;
            // console.log(response.data);
          }
        });
  } // FIN : FND-00005


  /*****************************************************
  * Funcion: FND-00006
  * Fecha: 31-07-2017
  * Descripcion: Carga la Lista de las Direcciones
  * Objetivo: Obtener la lista de las Sub Direcciones
  * de la BD, Llamando a la API, por su metodo
  * ( subdir-sreci-list ).
  ******************************************************/
  getlistaSubDireccionesAll() {
    // Llamamos al Servicio que provee todas las Sub Direcciones
    this.loading = 'show';
    this._listasComunes.listasComunes("","subdir-sreci-list-all").subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetlistaSubDireccion = response.data;
            alert(response.msg);
          }else{
            this.JsonOutgetlistaSubDireccion = response.data;
            // console.log(response.data);
            this.loading = 'hide';
            this.dataServiceSubDireccion = this.completerService.local(
                    this.JsonOutgetlistaSubDireccion, 'descDeptoFuncional,inicialesDeptoFuncional', 'descDeptoFuncional');
          }
        });
  } // FIN : FND-00006


  /*****************************************************
  * Funcion: FND-00007
  * Fecha: 12-10-2017
  * Descripcion: Carga la Lista de Todos los Funcionarios
  * Objetivo: Obtener la lista de los Funcionarios de la
  * de la BD, Llamando a la API, por su metodo
  * ( listas/funcionarios-list-all ).
  ******************************************************/
  getlistaFuncionariosSreci() {
    // Llamamos al Servicio que provee todas las Instituciones
    this.loading = 'show';
    this._listasComunes.listasComunes("","funcionarios-list-all").subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetlistaFuncionarios = response.data;
            alert(response.msg);

          }else{
            this.JsonOutgetlistaFuncionarios = response.data;
            console.log(response.data);
            this.loading = 'hide';
            this.dataServiceFuncionario = this.completerService.local(this.JsonOutgetlistaFuncionarios, 'nombre1Funcionario,apellido1Funcionario',
                  'nombre1Funcionario,apellido1Funcionario,apellido2Funcionario,telefonoFuncionario,emailFuncionario');
          }
        });
  } // FIN : FND-00007


}

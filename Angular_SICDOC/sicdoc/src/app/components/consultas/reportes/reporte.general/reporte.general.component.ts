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
// import * as jsPDF from 'jspdf'

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

  public fechaHoy:Date = new Date();
  public titulo = "Generación de Reporte";

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
  public idComunicacionArray;

  // Area de Fechas
  public tableConsultaFechas;

  // Objeto que Controlara la Forma
  forma:FormGroup;

  // Visualizacion de Componentes
  public showCardDirecion: boolean = false;
  public showCardFuncionario: boolean = false;

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
  public idTipoComMemo:number = 2;
  public idTipoComNotVerb:number = 3;
  public idTipoComCircular:number = 4;
  public idTipoComCorreos:number = 5;
  public idTipoComLlamadas:number = 7;
  public idTipoComVerbal:number = 8;
  public idTipoComReunion:number = 9;

  // Valores de Ingreso / Salida de Comunicación
  public idIngreso:number = 1;
  public idSalida:number = 2;

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


  /****************************************************
  * Funcion: FND-00001
  * Fecha: 28-07-2017
  * Descripcion: que Carga, Los Script de la Pagina
  * Objetivo: cargar los scripts, nesesarios para el
  * uso de la pagina
  *****************************************************/
  public loadScript(url) {
    // console.log('preparing to load...')
    let node = document.createElement('script');
    node.src = url;
    node.type = 'text/javascript';
    document.getElementsByTagName('head')[0].appendChild(node);
  } // FIN : FND-00001


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
    // Trabaja con las Fechas
    // Actualiza el valor de la Secuencia
    let mesAct = this.fechaHoy.getMonth() + 1;

    // Mes Actual *************************
    let final_month = mesAct.toString();
    if( mesAct <= 9 ){
      final_month = "0" + final_month;
    }

    // Dia del Mes *************************
    let day = this.fechaHoy.getDate(); // Dia
    let final_day = day.toString();
    if( day <= 9 ){
      final_day = "0" + final_day;
    }

    // Seteo de la Fecha Final
    let newFecha = this.fechaHoy.getFullYear() +  "-" + final_month + "-" + final_day;

    setTimeout(function () {
      $ (function () {
        $('#example').DataTable( {
            dom: 'Bfrtip',
            fixedHeader: true,
            "autoWidth": false,
            // Tamaño de la Pagina
            "pageLength": 10,
            // Refresca la Data y Borra de Memoria los Datos anteriores
            destroy: true,
            retrieve: true,

            // Cambiar las Propiedades de Lenguaje
            "language":{
                "lengthMenu": "Mostrar _MENU_ registros por pagina",
                "info": "Mostrando pagina _PAGE_ de _PAGES_",
                    "infoEmpty": "No hay registros disponibles",
                    "infoFiltered": "(filtrada de _MAX_ registros)",
                    "loadingRecords": "Cargando...",
                    "processing":     "Procesando...",
                    "search": "Buscar:",
                    "zeroRecords":    "No se encontraron registros coincidentes",
                    "paginate": {
                        "next":       "Siguiente",
                        "previous":   "Anterior"
                    },
            },

            // Ocultar Columnas
            "columnDefs": [
                  { // Columna de Ingreso / Salida
                      "targets": [ 0 ],
                      "visible": false,
                      "searchable": true
                  },
                  /*{ // Columna de Codigo Comunicación
                      "targets": [ 2 ],
                      "visible": false,
                      "searchable": true
                  },*/
                  {//fECHAS
                    "targets": [ 6 ],
                    "visible": false,
                    "searchable": true
                  },
                  {
                    "targets": [ 7 ],
                    "visible": false,
                    "searchable": true
                  },
                  { // Columna de Descripcion
                      "targets": [ 10 ],
                      "visible": false,
                      "searchable": true
                  },
            ],

            // paging: false,
            buttons: [
               // Boton de excelHtml5
                 {
                    extend: 'excelHtml5',
                    //title: 'Informe de Comunicaciones' + ' / ' + newFecha,
                    title: 'Informe de Comunicaciones ' + ' del: ' + $("#dtFechaIni").val() + ' hasta: ' + $("#dtFechaFin").val(),
                      text: 'Exportar en Excel',
                      customize: function( xlsx ) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];
                        $('row:first c', sheet).attr( 's', '42' );
                      }
                },

               // Boton de Imprimir
                {
                  extend: 'print',
                  utoPrint: false,
                  exportOptions: {
                    columns: ':visible',
                  },
                  customize: function (win) {
                    $(win.document.body).find('table').addClass('display').css('font-size', '10px');
                    $(win.document.body).find('tr:nth-child(odd) td').each(function(index){
                        $(this).css('background-color','#D0D0D0');
                    });
                    $(win.document.body).find('h1').css('text-align','center');
                  },
                  text: 'Imprimir Todos',
                  message: 'Listado de Comunicaciones',
                  //title: 'Informe de Comunicaciones' + ' / ' + newFecha,
                  title: 'Informe de Comunicaciones ' + ' del: ' + $("#dtFechaIni").val() + ' hasta: ' + $("#dtFechaFin").val(),
                  orientation: 'landscape',
                  pageSize: 'A4',
                },

                // Boton de Importar a PDF
                {
                  extend: 'pdfHtml5',
                  orientation: 'landscape',
                  pageSize: 'A4',
                  title: 'Informe de Comunicaciones ' + ' del: ' + $("#dtFechaIni").val() + ' hasta: ' + $("#dtFechaFin").val(),
                  text: 'Exportar a PDF',
                  exportOptions: {
                    columns: [ 1, 2, 3, 4, 5, 8, 10, 11 ]
                  },
                  customize: function(doc){
                    doc.pageMargins = [10,15,10,10];
                  }
                },

                /*{
                  extend: 'excelHtml5',
                  customize: function( xlsx ) {
                      var sheet = xlsx.xl.worksheets['sheet1.xml'];

                      $('row c[r^="C"]', sheet).attr( 's', '2' );
                  }
              }*/
            ],
            //select: true
        } );
        this.loading_tableIn = 'show';
      });
    }, 500);
    this.loading_tableIn = 'hide';
  } // FIN | FND-00006


  /*****************************************************
  * Funcion: ngOnInit
  * Fecha: 05-10-2017
  * Descripcion: Metodo inicial del Programa
  ******************************************************/
  ngOnInit() {
    // Hacemos que la variable del Local Storge este en la API
    this.identity = JSON.parse(localStorage.getItem('identity'));
    // Seteamos los valores de busqueda del Search
    this.searchStrDireccion = "";
    this.searchStrFuncionario = "";

    let array = [];
    // Definicion de la Insercion de los Datos de Nuevo Contacto
    this._ModelReporteGeneral = new ReporteGeneral( null, null, 0, 0,
                                this.idEstadoComunicacionArray, this.idTipoComunicacionArray, this.idComunicacionArray,
                                null, null);

    // Llenado de la Lista de Sub Direcciones
    this.getlistaSubDireccionesAll();

    // Llenado de la Lista de Funcionarios
    this.getlistaFuncionariosSreci();

    // Cargamos el Script de Jquery nesesarios
    // this.loadScript('https://code.jquery.com/jquery-1.12.4.js');

    // Condicion del Depto Funcional y su Perfil
    if ( this.identity.idTipoFunc == 4 || this.identity.idTipoFunc == 1 ) {
      // Seteamos y Parseamos a Int el idInstitucion
      // alert('Paso 1');
      this.showCardDirecion = true;
      this.showCardFuncionario = true;
      //this._ModelReporteGeneral.idDireccion = this.identity.idDeptoFuncional;
    } else if ( this.identity.idTipoFunc == 6 ) {
      // alert('Paso 2');
      this.showCardDirecion = false;
      this.showCardFuncionario = true;
      this._ModelReporteGeneral.idDireccion = this.identity.idDeptoFuncional;
    } else {
      // alert('Paso 3');
      this.showCardDirecion = false;
      this.showCardFuncionario = false;
      this._ModelReporteGeneral.idDireccion = this.identity.idDeptoFuncional;
      this._ModelReporteGeneral.idFuncionarioAsignado = this.identity.sub;
    }

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

      // Llamamos al Metodo de Reconstruccion de la Tabla
      this.recargaTable(),

      // Ejecucion de los Array de Estados
      this.sendEstComunicacion();
      // Ejecucion de los Array de Tipos
      this.sendTiposComunicacion();
      // Ejecucion de los Array de Method de Comunicación
      this.sendComunicacion();

      //Modelos de Envio: Tipos, Estados y Metodologia de Ingreso
      this._ModelReporteGeneral.idEstadoComunicacion = this.idEstadoComunicacionArray;
      this._ModelReporteGeneral.idTipoComunicacion = this.idTipoComunicacionArray;
      this._ModelReporteGeneral.idComunicacion = this.idComunicacionArray;

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
                  this.loading_tableIn = 'hide';
                  this.JsonOutgetReporteComunicaion = response.data;
                  alert('Error 404: ' +  this.mensajes);
                  this.fillDataTable();
                }
            }else{
              //this.resetForm();
              this.loading_tableIn = 'hide';
              // window.location.reload();
              //alert(response.msg);
              //Json de la Data Consultada
              this.JsonOutgetReporteComunicaion = response.data;
              // Cargamos la Tabla con las Instancias
              this.fillDataTable();
              console.log(this.JsonOutgetReporteComunicaion);

              // this.ngOnInit();

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
                this.loading = 'hide';
              }
            }
        });
  } // Fin | Metodo onSubmit



  /*****************************************************
  * Funcion: FND-00001-1
  * Fecha: 31-10-2017
  * Descripcion: Reconstruir la Tabla, para nuevo uso
  ******************************************************/
  recargaTable(){
    // Declaracion del Objeto de la Tabla
    var table = $('#example').DataTable();

    // Reiniciamos los vaores de la Tabla y la volvemos a Diseñar
    table
      .clear()
      .draw();

    // Destruimos la Instacia que fue Generada
    $("#example").dataTable().fnDestroy();
    // $('#example tbody > tr').remove();
  } // FIN | FND-00001-1


  /*****************************************************
  * Funcion: FND-00001
  * Fecha: 14-10-2017
  * Descripcion: Chekear todas las Opciones, de Estados
  * de Comunicación
  ******************************************************/
  checkTodosEstadosCom(){
    $('.chkAllEstadosCom').each(function () {
    //$('#chkAll').each(function () {
        if (this.checked) $(this).attr("checked", false);
        else $(this).prop("checked", true);
    });
  } // FIN | FND-00001


  /*****************************************************
  * Funcion: FND-00001.1
  * Fecha: 26-12-2017
  * Descripcion: Chekear todas las Opciones, de Tipo de
  * Comunicación
  ******************************************************/
  checkTodosTipoCom(){
    $('.chkAllTiposCom').each(function () {
    //$('#chkAll').each(function () {
        if (this.checked) $(this).attr("checked", false);
        else $(this).prop("checked", true);
    });
  } // FIN | FND-00001.1


  /*****************************************************
  * Funcion: FND-00001.2
  * Fecha: 27-04-2018
  * Descripcion: Chekear todas las Opciones, de Tipo de
  * Comunicación (Ingreso / Salida)
  ******************************************************/
  checkTodosCom(){
    $('.chkAllCom').each(function () {
    //$('#chkAll').each(function () {
        if (this.checked) $(this).attr("checked", false);
        else $(this).prop("checked", true);
    });
  } // FIN | FND-00001.2


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
  * Funcion: FND-00003.1
  * Fecha: 27-04-2018
  * Descripcion: Enviar Entrada de Comunicación
  ******************************************************/
  sendComunicacion() {
     var selectedComunicacion = [];
     $(":checkbox[name=chkAllCom]").each(function() {
       if (this.checked) {
         // agregas cada elemento.
         selectedComunicacion.push( parseInt( $(this).val() ) );
       }
     });
     this.idComunicacionArray = selectedComunicacion;
  } // FIN FND-00003.1


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
            this.dataServiceFuncionario = this.completerService.local(this.JsonOutgetlistaFuncionarios, 'nombre1Funcionario,nombre2Funcionario,apellido1Funcionario',
                  'nombre1Funcionario,apellido1Funcionario,emailFuncionario');
          }
        });
  } // FIN : FND-00007


}

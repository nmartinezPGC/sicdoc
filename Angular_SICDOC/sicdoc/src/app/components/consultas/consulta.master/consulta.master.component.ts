import { Component, OnInit } from '@angular/core';

import { FormGroup, FormControl, Validators } from '@angular/forms';

import { RouterModule, Routes, ActivatedRoute, Router } from '@angular/router';

import { HttpModule,  Http, Response, Headers } from '@angular/http';

// Lirerias para el AutoComplete
import {Observable} from 'rxjs/Observable';
import 'rxjs/add/operator/startWith';
import 'rxjs/add/operator/map';

import { AppComponent } from '../../../app.component'; //Servico del Login

import { NgForm }    from '@angular/forms';

// Importamos los Pipes de la Forma
import { GenerateDatePipe } from '../../../pipes/common/generate.date.pipe';
import { SearchFilterPipe } from '../../../pipes/common/generate.search.pipe';

import { DateAdapter, NativeDateAdapter } from '@angular/material';

import {DataSource} from '@angular/cdk/collections';

import 'rxjs/add/observable/of';

// Importamos los Services de la Clase
import { ListasComunesService } from '../../../services/shared/listas.service'; //Servico Listas Comunes
import { ConsultaMasterService } from '../../../services/consultas/consulta.master.service'; //Servico Consulta Master

// Modelo a Utilizar
import { ConsultaMaster } from '../../../models/consultas/consulta.master.model'; // Modelo a Utilizar

// Declaramos las variables para jQuery
declare var jQuery:any;
declare var $:any;

@Component({
  selector: 'consulta.master.component',
  templateUrl: './consulta.master.component.html',
  styleUrls: ['./consulta.master.component.css'],
  providers: [ ListasComunesService, ConsultaMasterService]
})
export class ConsultaMasterComponent implements OnInit {

  public titulo = "Consulta de Comunicación";

  minDate = Date.now();
  maxDate = new Date(2017, 12, 1);


  // variables del localStorage
  public identity;
  public token;
  public userId;
  public jwtUser;

  // parametros multimedia
  public loading  = 'hide';
  public loading_table  = 'hide';
  public loading_tr  = 'hide';
  public loading_tableIn = 'hide';

  public loadTabla1:boolean = false;
  public loadTabla2:boolean = false;

  // Instacia del Objeto Model de la Clase
  public consultaMasterEnc: ConsultaMaster;

  // Parametros de los Json de la Aplicacion
  public JsonOutgetlistaComunicacionDet:any[];
  public JsonOutgetlistaComunicacionEnc:any[];
  public JsonOutgetlistaComunicacionEncNew:any[];


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
               private _consultaMasterService: ConsultaMasterService,
               private _router: Router,
               private _route: ActivatedRoute,
               private _appComponent: AppComponent,
               private _http: Http ){
     // Llenado de la Tabla de Encabezado
     this.fillDataTable();

     //this.otraFill();
  } // Fin | Definicion del Constructor


  /*****************************************************
  * Funcion: ngOnInit
  * Fecha: 05-10-2017
  * Descripcion: Metodo inicial del Programa
  ******************************************************/
  ngOnInit() {
    // Hacemos que la variable del Local Storge este en la API
    this.identity = JSON.parse(localStorage.getItem('identity'));
    this.userId = this.identity.sub;

    //Token de la session
    this.token = localStorage.getItem('token');
    this.jwtUser = this.token;

    // // Iniciamos los Parametros de Fechas de la Consulta
    this.tableConsultaFechas = {
      "fechaCreacion"    : "",
      "fechaMaxEntrega"  : ""
    };

    this.idEstadoArray = [3, 5, 7, 8 ];
    this.idTipoComunicacionArray = [1, 5, 7, 8 ];
    // Definicion de la Insercion de los Datos de Nueva Comunicacion
    this.consultaMasterEnc = new ConsultaMaster (null, null, null, null, this.userId, null, 1, this.idEstadoArray, this.idTipoComunicacionArray);

    // Carga la Tabla (Con el Model PreCargado)
    this.otraFill( this.consultaMasterEnc, this.jwtUser );

    // Ejecucion de la Lista de Comunicacion de Usuario Logeado
    this.getlistaComunicacionEncTableFind();


    // console.log(this.JsonOutgetlistaComunicacionEnc);

    // Llenado de la Tabla de Encabezado
    // this.fillDataTable();
  } // Fin | ngOnInit


  /*****************************************************
  * Funcion: FND-00001
  * Fecha: 05-10-2017
  * Descripcion: Carga la Lista de la Comunicacion de la
  * BD que pertenecen al usaurio Logeado, en el Encabezado
  * Objetivo: Obtener la lista de los Oficios de las
  * Comunicaciones de la BD, Llamando a la API, por su
  * metodo ( consulta-general ).
  * Params: Modelo de la Clase (idEstado[],
            tipoComunicacion[], fechaInici , fechaFin )
  ******************************************************/
  getlistaComunicacionEncTableFind() {
    // Laoding
    this.loading = 'show';
    this.loadTabla1 = false;
    console.log( this.consultaMasterEnc );
    // Llamar al metodo, de Service para Obtener los Datos de la Comunicacion
    this._consultaMasterService.comunicacionFind( this.consultaMasterEnc ).subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetlistaComunicacionEnc = response.data;
            this.JsonOutgetlistaComunicacionEncNew = response;

            alert(response.msg);
          }else{
            this.JsonOutgetlistaComunicacionEnc = response.data;
            this.JsonOutgetlistaComunicacionEncNew = response;
            //this.valoresdataDetJson ( response.data );

            this.loading = 'hidden';
            this.loadTabla1 = true;
            console.log( this.JsonOutgetlistaComunicacionEnc );

          }
        });
  } // FIN | FND-00001


  /*****************************************************
  * Funcion: FND-00001.1
  * Fecha: 06-10-2017
  * Descripcion: Carga la Lista de la Comunicacion de la
  * BD que pertenecen al usaurio Logeado, en el Detalle
  * Objetivo: Obtener la lista de los Oficios de las
  * Comunicaciones de la BD, Llamando a la API, por su
  * metodo ( consulta-general ).
  * Params: Modelo de la Clase (idEstado[],
            tipoComunicacion[], fechaInici , fechaFin )
  ******************************************************/
  getlistaComunicacionDetTableFind( idCorrespondenciaEncIn:number ) {
    // Laoding
    this.loading_table = 'show';
    this.loadTabla2 = false;
    this.consultaMasterEnc.idCorrespondenciaEnc = idCorrespondenciaEncIn;
    // console.log( this.consultaMasterEnc );
    // Llamar al metodo, de Service para Obtener los Datos de la Comunicacion
    this._consultaMasterService.comunicacionDetFind( this.consultaMasterEnc ).subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetlistaComunicacionDet = response.data;

            alert(response.msg);
          }else{
            this.JsonOutgetlistaComunicacionDet = response.data;
            //this.valoresdataDetJson ( response.data );
            this.loading_table = 'hide';
            this.loadTabla2 = true;
            console.log( this.JsonOutgetlistaComunicacionDet );
          }
        });
  } // FIN | FND-00001.1


  /*****************************************************
  * Funcion: FND-00002
  * Fecha: 05-10-2017
  * Descripcion: Contener las opciones de selccion de los
  * Estados que el usuario desee
  * Params: Array de los Estados de los Checkbox
  ******************************************************/
  checkIdEstadosSend( optChk ){
    // Condicion para Evaluar las Opciones Chekeadas
    let optChecked = optChk;
    // Opcion de Ingresado
        if( this.idEstadoArray.includes(optChecked) ) {
          var i = this.idEstadoArray.indexOf( optChecked );
          // Comprobamos que no este dentro del Array
          if ( i !== -1 ) {
              this.idEstadoArray.splice( i, 1 );
          }
          // this.idEstadoArray.splice(optChecked);
          alert('Estados Esta en el Array ' + this.idEstadoArray);
        }else{
          this.idEstadoArray.push(optChecked);
          alert('Estados No Esta en el Array ' + this.idEstadoArray );
        }
  } // FIN | FND-00002


  /*****************************************************
  * Funcion: FND-00002
  * Fecha: 05-10-2017
  * Descripcion: Contener las opciones de selccion de los
  * Tipo de Comunicacion que el usuario desee
  * Params: Array de los Tipo Comunicacion de los Checkbox
  ******************************************************/
  checkIdTipoComunicacionSend( optChk ){
    // Condicion para Evaluar las Opciones Chekeadas
    let optChecked = optChk;
    // Opcion de Ingresado
        if( this.idTipoComunicacionArray.includes(optChecked) ) {
          var i = this.idTipoComunicacionArray.indexOf( optChecked );
          // Comprobamos que no este dentro del Array
          if ( i !== -1 ) {
              this.idTipoComunicacionArray.splice( i, 1 );
          }
          // this.idEstadoArray.splice(optChecked);
          alert('Tipo Comunicacion Esta en el Array ' + this.idTipoComunicacionArray);
        }else{
          this.idTipoComunicacionArray.push(optChecked);
          alert('Tipo Comunicacion No Esta en el Array ' + this.idTipoComunicacionArray );
        }
  } // FIN | FND-00002


  /*****************************************************
  * Funcion: FND-00002.1
  * Fecha: 18-12-2017
  * Descripcion: Tabla de DataTable con Metodo Ajax y
  *  envio de la Informacion son JSON
  * Params: modelClass (Clase del Modelo), authorization
  ******************************************************/
  otraFill( modelClass, authorization ){
    $ (function () {
    var table =  $('#example2').DataTable( {
        "processing": true,
        //"serverSide": true,
        "destroy": true,
        "ajax": {
            "type": "POST",
            "dataType": "json",
            /* "data" :JSON.stringify(formData),
            * Preparacion de los Parametros del Json
            * Param 1 : Datos del Modelo de la Clase
            * Param 2 : Dato de la Autorizacion del Token */
            "data" : { "json": JSON.stringify( modelClass ),
                      "authorization":authorization } ,
            //"contentType": "application/json",
            "contentType": "application/x-www-form-urlencoded",
            //"url": "http://localhost/sicdoc/symfony/web/app_dev.php/listas/estados-comunicacion-list",
            "url": "http://localhost/sicdoc/symfony/web/app_dev.php/consultas/consulta-general",
        },

        // Tabla response
        "responsive": true,

        // Seleccionable
        "select": true,

        // Definicion de las Columnas de la Tabla
        "columns" : [
          { "data": "codCorrespondenciaEnc" },
          { "data": "codReferenciaSreci" },
          { "data": "idTipoDocumento.descTipoDocumento" },
          { "data": "fechaIngreso.timestamp" },
          { "data": "fechaMaxEntrega.timestamp" },
          { "data": "idInstitucion.descInstitucion" },
          { "data": "temaComunicacion" },
          { "data": "idEstado.descripcionEstado" },
          {
            "data": null,
            "defaultContent": "<a href='/#' data-toggle='modal' data-target='#t_and_c_m'> "+
            " <img src='/assets/icons/comunicacion/zoom-interface-symbol-of-text-paper-with-a-magnifier-glass_blue_24x24.png'  "+
                  "class='d-inline-block align-top' title='Ver Detalle de Comunicación'> "+
            "</a>"
          }
        ],

        // Tamaño de la Pagina
        "pageLength": 5,
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
      } );


      $('#example2 tr').on('click', function(){
        var dato = $(this).find('td:first').html();
        alert(dato);
      });

} ); // Fin de Funcion de Tabla
  }

  /*****************************************************
  * Funcion: FND-00003
  * Fecha: 06-10-2017
  * Descripcion: Realiza el llenado de la Tabla con Todos
  * los Filtros
  * Params: Array de los Estado y Tipos Comunicacion de
  * los Checkbox
  ******************************************************/
  fillDataTable(){
    setTimeout(function () {
      $ (function () {
          $('#example').DataTable({
            "destroy": true,
            // Barra Vertical de la Tabla
            //scrollY:        '50vh',
            //scrollCollapse: true,
            "fixedHeader": true,
            "autoWidth": false,
            // Tamaño de la Pagina
            "pageLength": 5,
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
          });
          this.loading_tableIn = 'show';
      });
    }, 20000);
    this.loading_tableIn = 'hide';
  } // FIN | FND-00003


  /****************************************************
  * Funcion: FND-00004
  * Fecha: 06-10-2017
  * Descripcion: Funcion que Obtiene los Datos del Oficio
  * Utilizando parametros del Json de la tabla
  * Objetivo: Obtener los Datos del Oficio seleccionado
  *****************************************************/
  datoOficio( codOficioIntIn:string, codOficioRefIn:string, idDeptoIn:number,
             nombre1funcionarioAsignadoIn:string, apellido1funcionarioAsignadoIn:string,
             nombre2funcionarioAsignadoIn:string, apellido2funcionarioAsignadoIn:string,
             idFuncionarioIn:number, idEstadoAsign:number, idOficioEnc:number ){
   // Seteo de las varibles de la Funcion
    this.codOficioIntModal = codOficioIntIn;
    this.codOficioRefModal = codOficioRefIn;
    // this.idDeptoFuncionalModal = idDeptoIn;
    this.nombre1FuncModal = nombre1funcionarioAsignadoIn;
    this.nombre2FuncModal = nombre2funcionarioAsignadoIn;
    this.apellido1FuncModal = apellido1funcionarioAsignadoIn;
    this.apellido2FuncModal = apellido2funcionarioAsignadoIn;
    // this.idFuncModal = idFuncionarioIn;
    this.loadTabla2 = false;

    // Limpia los Campos de las Descripciones
    // this.finalizarOficios.descripcionOficio = "";
    // this.finalizarOficios.actividadOficio = "";
    // this.idCorrepEncModal = "";

    // Llamamos el Metodo de carga de la Tabla de las Actividades
    this.getlistaComunicacionDetTableFind( idOficioEnc );

    // Cambia el valor de optionModal
    // this.optionModal = 2;
 } // FIN : FND-00004


  public datoEnc:DatosEnc[] = this.JsonOutgetlistaComunicacionEnc;


} // FIN de Clase | Consulta Master


// Interface de la Clase
export interface DatosEnc {
  idCorrespondenciaEnc: number;
  codCorrespondenciaEnc: string;
  codReferenciaSreci: string;
  temaComunicacion: string;
  descCorrespondenciaEnc: string;
}

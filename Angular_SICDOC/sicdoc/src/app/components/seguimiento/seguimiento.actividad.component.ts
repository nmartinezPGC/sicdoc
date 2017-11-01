import { Component, OnInit } from '@angular/core';

import { FormGroup, FormControl, Validators } from '@angular/forms';

import { RouterModule, Routes, ActivatedRoute, Router } from '@angular/router';

import { HttpModule,  Http, Response, Headers } from '@angular/http';

// Lirerias para el AutoComplete
import {Observable} from 'rxjs/Observable';
import 'rxjs/add/operator/startWith';
import 'rxjs/add/operator/map';

//Importamos los Servicios
import { ListasComunesService } from '../../services/shared/listas.service'; //Servico Listas Comunes
import { SeguimientoActividadService } from '../../services/seguimiento/seguimiento.actividad.service'; //Servicio de la Clase Finalizar

import { DatatablesActividadPendientesComponent } from './datatables-actividad-pendientes/datatables-actividad-pendientes.component';

// Importamos los Pipes de la Forma
import { GenerateDatePipe } from '../../pipes/common/generate.date.pipe';
import { SearchFilterPipe } from '../../pipes/common/generate.search.pipe';

// Importamos la CLase SeguimientoActividad del Modelo
import { SeguimientoActividad } from '../../models/seguimiento/seguimiento.actividad.model'; // Modelo a Utilizar

import { AppComponent } from '../../app.component'; //Servico del Principal

import { NgForm }    from '@angular/forms'; // Para el uso del Formulario

// Declaramos las variables para jQuery
declare var jQuery:any;
declare var $:any;

@Component({
  selector: 'seguimiento.actividad.component.ts',
  templateUrl: '../../views/seguimiento/seguimiento.actividad.component.html',
  styleUrls: ['../../views/seguimiento/seguimiento.actividad.component.css'],
  providers: [ SeguimientoActividadService, ListasComunesService ]
})
export class SeguimientoActividadComponent implements OnInit {
  public titulo:string = "Seguimiento de Actividad";
  public fechaHoy:Date = new Date();
  public fechafin:string;

  // Variables para la Busqueda
  public opcionSearch:string = "Opcion de Busqueda ";
  public optUserFindId:number;

  // parametros multimedia
  public loading  = 'hide';
  public loading_table  = 'hide';
  public loading_tr  = 'hide';

  public loadTabla2:boolean = false;

  // Variables de Mensajeria y Informaicon
  //  public data;
   public errorMessage;
   public status;
   public mensajes;

   // Credenciales localStorage
   public identity;
   public token;


   // Datos de la Buqueda
   // Area de Identificacion
   public codigoInternoCom; // Codigo Interno de la Comunicacion
   public codigoReferenciaCom; // Codigo Referencia de la Comunicacion
   // Area de Fechas


   // Parametros para el localStorageJSON
   public localStorageJSON

   // Objeto que Controlara la Forma
   forma:FormGroup;

  // Json de los listas de los Oficios por usuario
  public JsonOutgetComunicacionFind:any[];
  public JsonOutgetlistaSeguimiento:any[];

  public datosDet;

  // Parametros del Modelo
  private tableSeguimientoActividadList;
  private tableSeguimientoActividadListDet;

  // DataTable
  public data:any[];
  public filterQuery = "";
  public rowsOnPage = 5;
  public sortBy = "email";
  public sortOrder = "asc"

  // Instacia del Modelo
  public seguimientoActividad: SeguimientoActividad;

  constructor( private _listasComunes: ListasComunesService,
               private _seguimientoActividad: SeguimientoActividadService,
               private _router: Router,
               private _route: ActivatedRoute,
               private _appComponent: AppComponent,
               private _http: Http ) { }


 /*****************************************************
 * Funcion: ngOnInit()
 * Fecha: 22-09-2017
 * Descripcion: Funcion inicial de Angular para cargar
 * los disitintos metodos del Component
 * Objetivo: Cargar Components y tener la Comunicacion
 *           de la BD, Llamando a la API
 ******************************************************/
  ngOnInit() {
    // Iniciamos los Parametros de Instituciones
    this.tableSeguimientoActividadListDet = {"fechaSalida":""}

    // Tabla Iniical del Encabezado de la Comunicacion
    this.tableSeguimientoActividadList = {
      // Area de Identificacion
      "codComunicacionInterno":"",
      "codComunicacionReferencia":"",
      // Area de Fechas
      "fechaIngreso":"",
      "fechaEntrega":"",
      "fechaModificacion":"",
      "fechaFinalizacion":"",
      // Area de Informacion General
      "institucionReceptora":"",
      "direccionSreci":"",
      "subDireccionSreci":"",
      "tipoComunicacion":"",
      "estadoComunicacion":"",
      "usuarioCreador":"",
      "usuarioAsignado":"",
      // Area de Descripcion de la Comunicacion
      "temaComunicacion":"",
      "contenidoComunicacion":""
    };

    // Iniciamos los Parametros de Usuarios a Depto Funcionales
    this.localStorageJSON = {
      "idUser":"",
      "idTipoFunc":"",
      "idDeptoFunc":""
    };


    // Definicion de la Insercion de los Datos de Nuevo Usuario
    this.seguimientoActividad = new SeguimientoActividad(null, null, null, null, null, null );

    // Inicializamos el Llenado de las Tablas
    // this.getlistaFinalizarOficiosTable();

    // Generar la Lista de Secuenciales
    // this.listarCodigoCorrespondenciaDet();
    // this.getlistaComunicacionDetTableFind();

  } // FIN | ngOnInit()


  /*****************************************************
  * Funcion: FND-00001
  * Fecha: 02-10-2017
  * Descripcion: Evalua la Opcion seleccionada
  * Objetivo: Obtener la Opcion de Busqueda que el Usuario
  * Selecciono
  * Parametro: Opcion de Busqueda (Number)
  ******************************************************/
  selecOpcionSearch( opcionSeleccionada ){
    // Parametro de Busqueda
    let optSelec = opcionSeleccionada;
    switch ( optSelec )
    {
      case 1: // No. Interno de Comunicacion
        this.opcionSearch = "No. Interno de Comunicacion";
        this.optUserFindId = 1;
      break;
      case 2: // No. Referencia de Comunicacion
        this.opcionSearch = "No. Referencia de Comunicacion";
        this.optUserFindId = 2;
      break;
      // case 3: // No. Tema de Comunicacion
      //   this.opcionSearch = "Tema de Comunicacion";
      // break;
    } // FIN Case
  } // FIN | FND-00001


  /****************************************************
  * Funcion: FND-00001.1
  * Fecha: 13-09-2017
  * Descripcion: Funcion que convierte las fechas
  * Objetivo: Obtener las fechas para la tabla
  *****************************************************/
   timeConverter(UNIX_timestamp){
      let a = new Date( UNIX_timestamp * 1000);
  //  alert(UNIX_timestamp);
       let diaFechamaxima = String( a.getDay() );
       let mesFechamaxima = String( a.getMonth() + 1 );
       let anioFechamaxima = String( a.getFullYear() );

       // Condicion de dias y Mese < 10
       if(diaFechamaxima.length < 2 ){
         diaFechamaxima =  '0' + diaFechamaxima;
       }else if(mesFechamaxima.length < 2 ){
         mesFechamaxima =  '0' + mesFechamaxima;
       }
       // Agrupamos las secciones de las Fechas
       let time = diaFechamaxima + '-' + mesFechamaxima + '-' + anioFechamaxima ;
       // retorna la fecha convertida
       //console.log(UNIX_timestamp);
       return time;
   } // FIN : FND-00001.1


  /*****************************************************
  * Funcion: FND-00002
  * Fecha: 02-10-2017
  * Descripcion: Carga la Lista de la Comunicacion de la
  * BD que pertenecen al usaurio Logeado
  * Objetivo: Obtener la lista de los Oficios de las
  * Comunicaciones de la BD, Llamando a la API, por su
  * metodo ( seguimiento-search-comunicacion-enc ).
  ******************************************************/
  getlistaComunicacionEncTableFind() {
    // Loading
    this.loading = 'show';
    this.loading_table = 'hide';
    this.loadTabla2 = false;
    // Parametros de la Lista de los Funcionarios
    // Variable del localStorage, para obtener el Id del Usuario (sub) y luego
    // la parseamos a Objeto Javascript
    this.identity = JSON.parse(localStorage.getItem('identity'));
    //this.localStorageJSON.idUser = this.identity.sub;
    this.localStorageJSON.idDeptoFunc = this.identity.idDeptoFuncional;

    // Evaluamos si la Opcion es Nula
    if( this.optUserFindId == null ){
      alert('Falta selecionar una opcion para continuar.');
      this.loading = 'hide';
      return;
    }else if ( this.seguimientoActividad.searchValueSend == null ) {
      alert('Debes ingresar el criterio de busqueda para continuar.');
      this.loading = 'hide';
      $('#searchValueSend').focus();
      return;
    }

    // Evalua que manda a buscar
    if( this.optUserFindId == 1 ){
        this.seguimientoActividad.codOficioInterno = this.seguimientoActividad.searchValueSend;
    }else if ( this.optUserFindId == 2 ) {
        this.seguimientoActividad.codOficioExterno = this.seguimientoActividad.searchValueSend;
    }
    // Seteo de los valores del Json que se envia
    // this.seguimientoActividad.searchValueSend = "";
    this.seguimientoActividad.optUserFindId = this.optUserFindId;
    this.seguimientoActividad.idFuncionarioAsignado = this.identity.sub;
    console.log( this.seguimientoActividad );
    // Llamar al metodo, de Service para Obtener los Datos de la Comunicacion
    this._seguimientoActividad.comunicacionFind( this.seguimientoActividad ).subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetComunicacionFind = response.data;
            if( this.loading = 'show' ){
              this.loading = 'hidden';
            }
            alert(response.msg);
          }else{
            this.JsonOutgetComunicacionFind = response.data;
            // Seteo de los Datos al JsonOutgetComunicacionFind
            this.valoresdataEncJson( response.data );

            // Consulta de las Actividades TblCorrespondenciaDet
            this.getlistaComunicacionDetTableFind();

            this.loading = 'hidden';
            console.log( response.data );
          }
        });
  } // FIN | FND-00002


  /*****************************************************
  * Funcion: FND-00002.1
  * Fecha: 03-10-2017
  * Descripcion: Carga la Lista de la Comunicacion de la
  * BD que pertenecen al usaurio Logeado, en el Detalle
  * Objetivo: Obtener la lista de los Oficios de las
  * Comunicaciones de la BD, Llamando a la API, por su
  * metodo ( seguimiento-search-comunicacion-det ).
  ******************************************************/
  getlistaComunicacionDetTableFind() {
    // Laoding
    this.loading_table = 'show';
    this.loadTabla2 = false;
    // Llamar al metodo, de Service para Obtener los Datos de la Comunicacion
    this._seguimientoActividad.comunicacionDetFind( this.seguimientoActividad ).subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetlistaSeguimiento = response.data;

            alert(response.msg);
          }else{
            this.JsonOutgetlistaSeguimiento = response.data;
            //this.valoresdataDetJson ( response.data );
            this.loading_table = 'hide';
            this.loadTabla2 = true;
            console.log( this.JsonOutgetlistaSeguimiento );
          }
        });
  } // FIN | FND-00002.1



  /*****************************************************
  * Funcion: FND-00003
  * Fecha: 03-10-2017
  * Descripcion: Seteo de los valores de la Busqueda del
  * JsonOutgetComunicacionFind
  ******************************************************/
  valoresdataEncJson( dataIn ){
    // Instanciamos los Valores al Json de retorno, que Utilizara el Html
    // Area de Identificacion
    this.tableSeguimientoActividadList.codComunicacionInterno    = dataIn.codCorrespondenciaEnc;
    this.tableSeguimientoActividadList.codComunicacionReferencia = dataIn.codReferenciaSreci;
    // Area de Fechas
    this.tableSeguimientoActividadList.fechaIngreso      = dataIn.fechaIngreso.timestamp;
    this.tableSeguimientoActividadList.fechaEntrega      = dataIn.fechaMaxEntrega.timestamp;
    // this.tableSeguimientoActividadList.fechaModificacion = dataIn.fechaModificacion.timestamp;
    this.tableSeguimientoActividadList.fechaFinalizacion = dataIn.fechaFinalizacion.timestamp;
    // Area de Informacion General
    this.tableSeguimientoActividadList.institucionReceptora = dataIn.idInstitucion.descInstitucion + ' | ' + dataIn.idInstitucion.perfilInstitucion;
    this.tableSeguimientoActividadList.direccionSreci = dataIn.idDireccionSreci.inicialesDireccionSreci + ' | ' + dataIn.idDireccionSreci.descDireccionSreci;
    this.tableSeguimientoActividadList.subDireccionSreci = dataIn.idDeptoFuncional.inicialesDeptoFuncional + ' | ' + dataIn.idDeptoFuncional.descDeptoFuncional;
    this.tableSeguimientoActividadList.tipoComunicacion = dataIn.idTipoDocumento.descTipoDocumento;
    this.tableSeguimientoActividadList.estadoComunicacion = dataIn.idEstado.descripcionEstado;
    this.tableSeguimientoActividadList.usuarioCreador =
          dataIn.idUsuario.nombre1Usuario + ' ' +  dataIn.idUsuario.nombre2Usuario
          + ' ' +
          dataIn.idUsuario.apellido1Usuario + ' ' +  dataIn.idUsuario.apellido2Usuario ;
    this.tableSeguimientoActividadList.usuarioAsignado =
          dataIn.idFuncionarioAsignado.nombre1Funcionario + ' ' + dataIn.idFuncionarioAsignado.nombre2Funcionario
          + ' ' +
          dataIn.idFuncionarioAsignado.apellido1Funcionario + ' ' + dataIn.idFuncionarioAsignado.apellido2Funcionario;
    // Area de Descripcion de Comunicacion
    this.tableSeguimientoActividadList.temaComunicacion = dataIn.temaComunicacion;
    this.tableSeguimientoActividadList.contenidoComunicacion = dataIn.descCorrespondenciaEnc;
  } // FIN | FND-00003


}

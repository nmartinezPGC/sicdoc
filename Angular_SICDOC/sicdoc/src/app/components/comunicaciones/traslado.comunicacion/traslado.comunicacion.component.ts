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
import { CaseSecuencesService } from '../../../services/shared/caseSecuences.service'; //Servico caseSecuence

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
  providers: [ ListasComunesService, TrasladoComunicacionService, CaseSecuencesService ]
})
export class TrasladoComunicacionComponent implements OnInit {
  // Propiedades de la Clase
  // Datos de la Vetana
  public titulo:string = "Traslado de Comunicación";
  public fechaHoy:Date = new Date();

  public data;
  public errorMessage;
  public status;
  public statusConsultaCom;
  public mensajes;

  // Loader
  public loading = "hide";
  public hideButton:boolean = false;

  public idEstadoModal:number = 5;

  // parametros multimedia
  public loading_table  = 'hide';
  public loading_tr  = 'hide';
  public loading_tableIn = 'hide';
  public loadTabla2:boolean = true;

  public  codigoSec:string;
  public nombreDoc:string;

  // Datos de la Consulta
  public datosConsulta;
  public temaComFind;
  public descComFind;

  // Llenamos las Lista del HTML
  public JsonOutgetComunicacionChange:any[];
  public JsonOutgetComunicacionFind:any[];

  // Json de AutoCompleter Funcionarios
  public JsonOutgetlistaFuncionarios:any[];

  // Variables de Datos de envio
  public paramSearchValueSend:string = "";

  public paramsDocumentos;

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

  // Secuencia de Nuevo Regsitro
  private paramsSecuenciaActividadAgregar;
  public JsonOutgetCodigoSecuenciaActividadAgregar;


  // Instacia de la variable del Modelo | Json de Parametros
  public _trasladoComunicacionModel: TrasladoComunicacionModel;
  addForm: FormGroup; // form group instance

  // Propieadades del AutoComplete
  itemList = [];
  selectedItems = [];
  settings = {};


  // Variable de Sistema
  public urlConfigLocal:string;
  public urlResourseLocal:string;
  public urlComplete:string;


  /****************************************************************************
  * Constructor de la Clase
  ****************************************************************************/
  constructor( private _listasComunes: ListasComunesService,
               private _TrasladoComunicacionService: TrasladoComunicacionService,
               private _caseSecuencesService: CaseSecuencesService,
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

       //Carga el Listado de Funcionarios de la SRECI
       this.getlistaFuncionariosSreci();

       // Cinfuguracion de los Selects
       this.settings = {
         singleSelection: true,
         text: "Selecciona el Funcionario que se Trasladara la Comunicación ... ",
         searchPlaceholderText: "Busca al Funcionario por su Nombre ó Apellido",
         enableSearchFilter: true         
       };

  } // Fin de Constructor


  /************************************************
   * Funcion de Inicio de la Clase de Traslado de
   * la Comunicación
  *************************************************/
  ngOnInit() {
      // Definicion de la Insercion de los Datos de Documentos
      this._trasladoComunicacionModel = new TrasladoComunicacionModel(1,
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
            "", "", "");

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


  } // FIN | ngOnInit()


  /*********************************************************
   * Funcion de Grabacion del Traslado de Comunicación
   * Nuevos en la BD
   * Fecha: 2018-03-06
  **********************************************************/
  onSubmit(forma:NgForm){
    // Envio de los Datos, a la API, con el fin de registrarlo en la BD
    this.loading = "show";

    this._TrasladoComunicacionService.registerTrasladoComunicacion( this._trasladoComunicacionModel ).subscribe(
      response => {
          // Obtenemos el Status de la Peticion
          this.status = response.status;
          this.mensajes = response.msg;

          // Condicionamos la Respuesta
          if(this.status != "success"){
              this.status = "error";
              this.mensajes = response.msg;
              if(this.loading = 'show'){
                this.loading = 'hidden';
              }
              alert(this.mensajes);
          }else{
            this.loading = 'hidden';
            this.ngOnInit();

            //Cerramos el Loading
            this.closeModal("#closeModalFinCom");
            //Oculta el Div de Alerta despues de 3 Segundos
            setTimeout(function() {
                $("#alertSuccess").fadeOut(1500);
            },3000);
          }
      }, error => {
          //Regisra cualquier Error de la Llamada a la API
          this.errorMessage = <any>error;

          //Evaluar el error
          if(this.errorMessage != null){
            console.log(this.errorMessage);
            this.mensajes = this.errorMessage;

            //Oculta el Div de Alerta despues de 3 Segundos
            setTimeout(function() {
                $("#alertError").fadeOut(1500);
            },3000);

            //Ocultamos el Loading
            if(this.loading = 'show'){
              this.loading = 'hidden';
            }
          }
      });
  } // FIN | OnngSubmit()


  /******************************************************
   * Funiones de Seleccion en Nuevo Control del Listas
   * Metodologia: ng-selectec2
   * Fecha: 2018-01-15
   * Casos de uso: Lista de Instituciones, Paises, Tipo
   * de Institucion, Contactos
  *******************************************************/
  onItemSelect(item: any) {
    console.log(item);
    this.JsonOutgetlistaFuncionarios = this.selectedItems ;
    this._trasladoComunicacionModel.subDireccionesSreciAcom = this.JsonOutgetlistaFuncionarios;
    console.log( this._trasladoComunicacionModel.subDireccionesSreciAcom );
  }

  OnItemDeSelect(item: any) {
    console.log(item);
    this.JsonOutgetlistaFuncionarios = this.selectedItems ;
    this._trasladoComunicacionModel.subDireccionesSreciAcom = this.JsonOutgetlistaFuncionarios;
    console.log( this._trasladoComunicacionModel.subDireccionesSreciAcom );
  }

  onSelectAll(items: any) {
    console.log(items);
    this.JsonOutgetlistaFuncionarios = this.selectedItems ;
    this._trasladoComunicacionModel.subDireccionesSreciAcom = this.JsonOutgetlistaFuncionarios;
    console.log( this._trasladoComunicacionModel.subDireccionesSreciAcom );
  }

  onDeSelectAll(items: any) {
    console.log(items);
    this.JsonOutgetlistaFuncionarios = this.selectedItems ;
    this._trasladoComunicacionModel.subDireccionesSreciAcom = this.JsonOutgetlistaFuncionarios;
    console.log( this._trasladoComunicacionModel.subDireccionesSreciAcom );
  }


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

    this.paramSearchValueSend = this._trasladoComunicacionModel.codCorrespondencia;

    // Solicitud del Servicio de la Busqueda
    this._TrasladoComunicacionService.buscaComunicacion( this._trasladoComunicacionModel ).subscribe(
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
            this.datosConsulta.idComunicacion = "";
            this.datosConsulta.idTipoComunicacion = "";
            this.datosConsulta.idTipoDocumento = "";

            // Oculatamos el Loader
            this.loading = "hide";
            this.hideButton = false;

            this.ngOnInit();
          }else{
            //this.data = JSON.stringify(response.data);

            this.JsonOutgetComunicacionChange = response.data;
            // Seteo de los Datos al JsonOutgetComunicacionFind

            this.valoresdataEncJson( this.JsonOutgetComunicacionChange );

            // Ocultamos el Loader
            this.loading = "hide";
            this.hideButton = true;
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
      // Relaciones de las Tablas
      this.datosConsulta.idComunicacion       = dataIn[0].idCorrespondenciaEnc;
      this.datosConsulta.idTipoComunicacion   = dataIn[0].idTipoComunicacion;
      this.datosConsulta.idTipoDocumento      = dataIn[0].idTipoDocumento;

      // Datos Generales
      this.datosConsulta.temaComunicacion = dataIn[0].temaComunicacion;
      this.datosConsulta.descComunicacion = dataIn[0].descCorrespondenciaEnc;
      this.datosConsulta.fechaFechaIngreso = dataIn[0].fechaIngreso;

      // Contenido
      this._trasladoComunicacionModel.idTipoDocumento = dataIn[0].idTipoDocumento;
      this._trasladoComunicacionModel.descCorrespondencia = dataIn[0].descCorrespondenciaEnc;
      this._trasladoComunicacionModel.temaCorrespondencia = dataIn[0].temaComunicacion;
      // Usuario Creador
      this._trasladoComunicacionModel.idUsuarioAsaignado = dataIn[0].idUsuario;
      //Fechas
      this._trasladoComunicacionModel.fechaMaxEntrega = dataIn[0].fechaMaxEntrega;
      this._trasladoComunicacionModel.fechaIngreso = dataIn[0].fechaIngreso;

      this._trasladoComunicacionModel.codReferenciaSreci =  dataIn[0].codReferenciaSreci;

      // Asignacion a Datos de Modales
      this.codOficioIntModal = this._trasladoComunicacionModel.codCorrespondencia;
      this.codOficioRefModal = this._trasladoComunicacionModel.codReferenciaSreci;

    } else {
    //alert('Datos out');
      this.datosConsulta.temaComunicacion = "";
      this.datosConsulta.descComunicacion = "";
      this.datosConsulta.fechaFechaIngreso = "";
      this.datosConsulta.fechaFechaEntrega = "";
      this.datosConsulta.emailUserCreador = "";
      this.datosConsulta.idComunicacion = "";
      this.datosConsulta.idTipoComunicacion = "";
      this.datosConsulta.idTipoDocumento = "";
    }
  } // FIN | FND-00001.2


  /*****************************************************
  * Funcion: FND-00001.4
  * Fecha: 28-02-2018
  * Descripcion: Limpiar el Formulario
  * Objetivo: Limpiar el Formulario
  ******************************************************/
  cleanForm(){
    this.ngOnInit();
  }


  /*****************************************************
  * Funcion: closeModal()
  * Fecha: 10-11-2017
  * Descripcion: Funcion que cierra la ventana Modal de
  * la Pantalla de forma Automatica
  * Objetivo: Cerrar la Modal despues de la Accion del
  * Submit
  ******************************************************/
  closeModal( nameBotton ){
    setTimeout(function() {
      // $('#t_and_c_m').modal('hide');
      $( nameBotton ).click();
    }, 600);
  }



  /*****************************************************
  * Funcion: FND-000004
  * Fecha: 23-09-2017
  * Descripcion: Obtiene la siguiente secuencia
  * Objetivo: Obtener el secuencial de la tabla
  * indicada con su cosigo
  * (gen-secuencia-comunicacion-in).
  ******************************************************/
   listarCodigoCorrespondenciaAgregarActividad(){
     // Evalua si hay datos de la Consulta
     if( this._trasladoComunicacionModel.descCorrespondencia == null || this._trasladoComunicacionModel.descCorrespondencia == '' ){
       alert('Debes de buscar la Comunicación antes de Ingresar mas Documentos');
       this.closeModal("#closeModalFinCom");
       return -1;
     }

     //Condicion del Secuencial Segun el Tipo de Documento
     //Evaluamos el valor del Tipo de Documento
     // Iniciamos los Parametros de Secuenciales | Agregar Actividad
     this.paramsSecuenciaActividadAgregar = {
       "codSecuencial"  : "",
       "tablaSecuencia" : "",
       "idTipoDocumento" : ""
     };

     this.loading_table = "show";

     //Objetivo: Seleccionar la Secuencia
     let paramsSendIn =
               this._caseSecuencesService.caseSecuence( this.datosConsulta.idTipoComunicacion, this.datosConsulta.idTipoDocumento );

     // Asignamos los valores a los parametros de Secuencia a Enviar
     this.paramsSecuenciaActividadAgregar.codSecuencial = paramsSendIn.codSecuencial;
     this.paramsSecuenciaActividadAgregar.tablaSecuencia = paramsSendIn.tablaSecuencia;
     this.paramsSecuenciaActividadAgregar.idTipoDocumento = paramsSendIn.idTipoDocumento;

      //Llamar al metodo, de Login para Obtener la Identidad
      // console.log(this.paramsSecuenciaActividadAgregar);
      // console.log('Entro en 3 listarCodigoCorrespondenciaAgregarActividad()');
      this._listasComunes.listasComunesToken( this.paramsSecuenciaActividadAgregar, "gen-secuencia-comunicacion-in" ).subscribe(
          response => {
            // login successful so redirect to return url
            if(response.status == "error"){
              //Mensaje de alerta del error en cuestion
              this.JsonOutgetCodigoSecuenciaActividadAgregar = response.data;
              //alert(response.msg);
              alert('ha ocurrido un error, pulsa F5 para recargar la pagina, si persiste comunicate con el Administrador');
            }else{
              this.JsonOutgetCodigoSecuenciaActividadAgregar = response.data;
              // Lo enviamos al Model de la Clase
              this._trasladoComunicacionModel.secuenciaComunicacionDet = this.JsonOutgetCodigoSecuenciaActividadAgregar.valor2;
              this._trasladoComunicacionModel.codCorrespondenciaDet = this.JsonOutgetCodigoSecuenciaActividadAgregar.codSecuencial;
              console.log( this._trasladoComunicacionModel.codCorrespondenciaDet );

              // Hide Loading
              this.loading_table = "hide";
            }
          });
   } // FIN : FND-000004



   /*****************************************************
   * Funcion: FND-00001.2
   * Fecha: 12-10-2017
   * Descripcion: Carga la Lista de Todos los Funcionarios
   * Objetivo: Obtener la lista de los Funcionarios de la
   * de la BD, Llamando a la API, por su metodo
   * ( listas/funcionarios-list-all ).
   ******************************************************/
   getlistaFuncionariosSreci() {
     // Llamamos al Servicio que provee todas los Funcionarios
     this._listasComunes.listasComunes("","funcionarios-list-all-component").subscribe(
         response => {
           // login successful so redirect to return url
           if(response.status == "error"){
             //Mensaje de alerta del error en cuestion
             this.JsonOutgetlistaFuncionarios = response.data;
             alert(response.msg);
             this.itemList = [];

           }else{
             this.JsonOutgetlistaFuncionarios = response.data;
             // console.log(response.data);
             // Cargamos el compoenete de AutoCompletar

             this.itemList = this.JsonOutgetlistaFuncionarios;
             console.log(this.JsonOutgetlistaFuncionarios);
           }
         });
   } // FIN : FND-00001.2


}

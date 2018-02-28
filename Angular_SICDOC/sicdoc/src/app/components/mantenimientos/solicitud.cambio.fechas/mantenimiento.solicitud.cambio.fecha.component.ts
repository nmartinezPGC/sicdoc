import { Component, OnInit } from '@angular/core';
import { RouterModule, Routes, ActivatedRoute, Router } from '@angular/router';

import { HttpModule,  Http, Response, Headers } from '@angular/http';

//Importamos los Servicios
import { SolicitudCambioFechaService } from '../../../services/mantenimientos/solicitud.cambio.fecha.service'; //Servico del Matenimiento
import { ListasComunesService } from '../../../services/shared/listas.service'; //Servico Listas Comunes
import { UploadService } from '../../../services/shared/upload.service'; //Servico Listas Comunes

// Importamos los Pipes de la Forma
import { GenerateDatePipe } from '../../../pipes/common/generate.date.pipe';

import { AppComponent } from '../../../app.component'; //Servico del Login

import { NgForm }    from '@angular/forms';

import { FormGroup, FormControl, Validators }    from '@angular/forms';

// Importamos la CLase Solicitud de Cambio de Fecha del Modelo
import { SolicitudCambioFecha } from '../../../models/mantenimientos/solicitud.cambio.fechas.model'; //Model del Login

// Declaramos las variables para jQuery
declare var jQuery:any;
declare var $:any;

@Component({
  selector: 'app-mantenimiento-instituciones',
  templateUrl: '../../../views/mantenimientos/solicitud.cambio.fecha/solicitud.cambio.fecha.component.html',
  styleUrls: ['../../../views/mantenimientos/solicitud.cambio.fecha/solicitud.cambio.fecha.component.css'],
  providers: [SolicitudCambioFechaService, ListasComunesService, UploadService]
})

export class MantenimientoSolicitudCambioFechasComponent implements OnInit{
  public titulo:string = "Solicitud de Cambio de Fecha";

  public fechaHoy:Date = new Date();
  public fechafin:string;

  // Instacia de la variable del Modelo
  public _modSolicitudCambioFechas: SolicitudCambioFecha;

  // Objeto que Controlara la Forma
  forma:FormGroup;

  public data;
  public errorMessage;
  public status;
  public statusConsultaCom;
  public mensajes;

  public identity;
  public token;

  // Array de SubDirecciones
  private paramsSubDir;
  // public passwordConfirmation:string;

  // Datos de la Consulta
  public temaComunicacion:string;
  public descComunicacion:string;
  public temaFechaIngreso:string;
  public temaFechaEntrega:string;

  // Datos de la Consulta
  public datosConsulta;
  public temaComFind;
  public descComFind;

  // Variables de Generacion de las Listas de los Dropdow
  // Llenamos las Lista del HTML
  public JsonOutgetComunicacionChange:any[];
  public JsonOutgetComunicacionFind:any[];

  public JsonOutgetlistaTipoFuncionario:any[];
  public JsonOutgetlistaDeptosFuncionales:any[];
  public JsonOutgetlistaTipoUsuario:any[];
  public JsonOutgetlistaDireccionSRECI:any[];
  public JsonOutgetlistaSubDireccionSRECI:any[];
  // public JsonOut:any[];

  // Loader
  public loading = "hide";


  // Definicion del Constructor
  constructor( private _solicitudCambioFechaService: SolicitudCambioFechaService,
               private _listasComunes: ListasComunesService,
               private _uploadService: UploadService,
               private _router: Router,
               private _route: ActivatedRoute,
               private _appComponent: AppComponent,
               private _http: Http){
    // Todo aqui
  }

  // Metodo OnInit
  ngOnInit(){
    // Iniciamos los Parametros de Sub Direcciones
    this.datosConsulta = {
      "temaComunicacion"  : "",
      "descComunicacion"  : "",
      "fechaFechaIngreso"  : "",
      "fechaFechaEntrega"  : "",
      "emailUserCreador"  : ""
    };

    // Convertimos las Fechas a una Default
    this.convertirFecha();

    // this.getlistaDeptosFuncionales();
    // this.getlistaTipoUsuarios();

    // Definicion de la Insercion de los Datos de Nuevo Usuario
    this._modSolicitudCambioFechas = new SolicitudCambioFecha ( "", "", "", 0, "", "", this.fechafin, "","");

  }


  // Metodo onSubmit
  onSubmit(forma:NgForm){
      console.log( this._modSolicitudCambioFechas );
      // Mostramos el Loader
      this.loading = "show";

      let confirmaResp = confirm('Esta Seguro de Grabar');
      // Comprobamos que Existe la Comunicacion
      if( this.statusConsultaCom != 'error' && confirmaResp == true ){
        // parseInt(this.user.idTipoUsuario);
        this._solicitudCambioFechaService.solitarCambioFecha( this._modSolicitudCambioFechas ).subscribe(
          response => {
              // Obtenemos el Status de la Peticion
              this.status = response.status;
              this.mensajes = response.msg;
              // console.log( this.mensajes );
              // Condicionamos la Respuesta
              if(this.status != "success"){
                  this.status = "error";
                  alert( this.mensajes );

                  // Ocultamos el Loader
                  this.loading = "hide";
                  $("#codCorrespondencia").focus();
              }else{
                this.ngOnInit();
                // Ocultamos el Loader
                this.loading = "hide";
              }
          }, error => {
              //Regisra cualquier Error de la Llamada a la API
              this.errorMessage = <any>error;

              //Evaluar el error
              if(this.errorMessage != null){
                console.log(this.errorMessage);
                this.mensajes = this.errorMessage;
                alert("Error en la Petición !!" + this.errorMessage);
              }
          });
      } else {
        // Ocultamos el Loader
        this.loading = "hide";
        //alert("Nos has Ingresado, la Comunicacion de Solicitud !!");
        return;
      }

  } // FIN | Metodo onSubmit


  /****************************************************
  * Funcion: FND-00001.1
  * Fecha: 22-11-2017
  * Descripcion: Funcion que Obtiene los datos de la
  * Consulta a la BD de la Comunicacion
  * Objetivo: Datos de la Comunicacion
  *****************************************************/
  buscaComunicacion() {
    // console.log(this._modSolicitudCambioFechas);
    // Mostramos el Loader
    this.loading = "show";

    // Solicitud del Servicio de la Busqueda
    this._solicitudCambioFechaService.buscaComunicacion( this._modSolicitudCambioFechas ).subscribe(
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
          }else{
            //this.data = JSON.stringify(response.data);

            this.JsonOutgetComunicacionChange = response.data;
            // Seteo de los Datos al JsonOutgetComunicacionFind

            this.valoresdataEncJson( this.JsonOutgetComunicacionChange );

            // Ocultamos el Loader
            this.loading = "hide";
            console.log( this.JsonOutgetComunicacionChange );
          }
        });
  } // FIN : FND-00001.1


  /*****************************************************
  * Funcion: FND-00001.2
  * Fecha: 23-11-2017
  * Descripcion: Seteo de los valores de la Busqueda del
  * datosConsulta
  ******************************************************/
  valoresdataEncJson( dataIn ){
    // Instanciamos los Valores al Json de retorno, que Utilizara el Html
    if( dataIn != null ){
      this.datosConsulta.temaComunicacion = dataIn[0].temaComunicacion;
      this.datosConsulta.descComunicacion = dataIn[0].descCorrespondenciaEnc;
      this.datosConsulta.fechaFechaIngreso = dataIn[0].fechaIngreso;
      this.datosConsulta.fechaFechaEntrega = dataIn[0].fechaMaxEntrega;
      this.datosConsulta.emailUserCreador = dataIn[0].emailUsuario;

      //Datos de envio por el Model
      // Codigos
      this._modSolicitudCambioFechas.codCorrespondenciaExt = dataIn[0].codReferenciaSreci;
      // Contenido
      this._modSolicitudCambioFechas.descComunicacion = dataIn[0].descCorrespondenciaEnc;
      this._modSolicitudCambioFechas.temaComunicacion = dataIn[0].temaComunicacion;
      // Usuario Creador
      this._modSolicitudCambioFechas.idUserCreador = dataIn[0].idUsuario;
      //Fechas
      this._modSolicitudCambioFechas.fechaMaxEntrega = this.datosConsulta.fechaFechaEntrega;
      console.log( this._modSolicitudCambioFechas.fechaMaxEntrega );
      this._modSolicitudCambioFechas.emailUserCreador = this.datosConsulta.emailUserCreador;
      // this._modSolicitudCambioFechas.idUserCreador = dataIn.idUsuario.idUsuario;
    } else {
      this.datosConsulta.temaComunicacion = "";
      this.datosConsulta.descComunicacion = "";
      this.datosConsulta.fechaFechaIngreso = "";
      this.datosConsulta.fechaFechaEntrega = "";
      this.datosConsulta.emailUserCreador = "";
    }
  } // FIN | FND-00001.2


  /****************************************************
  * Funcion: FND-00001.2
  * Fecha: 11-09-2017
  * Descripcion: Funcion que convierte las fechas a
  * String y le suma 5 dias
  * Objetivo: Sumar 5 dias a la fecha Maxima de entrega
  *****************************************************/
  convertirFecha() {
    let day = String(this.fechaHoy.getDate() + 5 );
    let month = String(this.fechaHoy.getMonth() + 1 );
    const year = String(this.fechaHoy.getFullYear() );

    if(day.length < 2  ){
      //alert("Dia Falta el 0");
      day = "0" + day;
    }else if(month.length < 2){
      //alert("Mes Falta el 0");
      month = "0" + month;
    }
    this.fechafin = year + "-" + month + "-" + day ;
    //alert("Dia " + day + " Mes " + month + " Año " + year);
  } // FIN : FND-00001.2


  /*****************************************************
  * Funcion: FND-00005
  * Fecha: 29-07-2017
  * Descripcion: Carga la Lsita de los Tipos de Usuarios
  * Objetivo: Obtener la lista de los Tipos de usuarios
  * de la BD, Llamando a la API, por su metodo
  * (tipo-usuario-list).
  ******************************************************/
  getlistaTipoUsuarios() {
    //Llamar al metodo, de Login para Obtener la Identidad
    this._listasComunes.listasComunes("","tipo-usuario-list").subscribe(
        response => {
          // login successful so redirect to return url
          //alert(response.status);
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            alert("Msg Error");
            alert(response.msg);
          }else{
            //this.data = JSON.stringify(response.data);
            this.JsonOutgetlistaTipoUsuario = response.data;
            // console.log(response.data);
          }
        });
  } // FIN : FND-00005


}

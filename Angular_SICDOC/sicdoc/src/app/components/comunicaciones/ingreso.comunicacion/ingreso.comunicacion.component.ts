import { Component, OnInit } from '@angular/core';

import { FormGroup, FormControl, Validators } from '@angular/forms';

import { RouterModule, Routes, ActivatedRoute, Router } from '@angular/router';

import { HttpModule,  Http, Response, Headers } from '@angular/http';

// Lirerias para el AutoComplete
import {Observable} from 'rxjs/Observable';
import 'rxjs/add/operator/startWith';
import 'rxjs/add/operator/map';

//Importamos los Servicios
import { LoginService } from '../../../services/login/login.service'; //Servico del Login
import { IngresoComunicacionService } from '../../../services/comunicaciones/ingreso.service'; //Servico del Comunicaciones
import { ListasComunesService } from '../../../services/shared/listas.service'; //Servico Listas Comunes
import { UploadService } from '../../../services/shared/upload.service'; //Servico Carga de Arhcivos

import { AppComponent } from '../../../app.component'; //Servico del Login

import { NgForm }    from '@angular/forms';

// Importamos la CLase Usuarios del Modelo
import { Usuarios } from '../../../models/usuarios/usuarios.model'; // Servico del Login
import { Comunicaciones } from '../../../models/comunicaciones/comunicacion.model'; // Modelo a Utilizar

declare var $:any;

@Component({
  selector: 'ingreso.comunicacion-tipo',
  templateUrl: './ingreso.comunicacion.component.html',
  styleUrls: ['./ingreso.comunicacion.component.css'],
  providers: [ IngresoComunicacionService ,LoginService, ListasComunesService, UploadService]
})
export class IngresoComunicacionPorTipoComponent implements OnInit {
  public titulo:string = "Salida de Comunicación";
  public fechaHoy:Date = new Date();
  public fechafin:string;
  public identity;
  public token;

  // Parametros para listas
  private paramsSubDir;
  private params;
  private paramsSubDirAcom;
  private paramsSecuencia;
  private paramsSecuenciaDet;
  private paramsSecuenciaIn;
  private paramsSecuenciaDetIn;

  // Instacia de la variable del Modelo | Json de Parametros
  public user:Usuarios;
  public comunicacion: Comunicaciones;

  // Propiedad de Loader
  public loading      = 'show';
  public alertSuccess = 'show';
  public alertError   = 'show';

  public status;
  public mensajes;
  public errorMessage;

  // Parametros de los Json de la Aplicacion
  public JsonOutgetlistaTiposDocumentos:any[];
  public JsonOutgetlistaDireccionSRECIAcom:any[];
  public JsonOutgetlistaSubDireccionSRECIAcom:any[];
  public JsonOutgetlistaSubDireccionSRECI:any[];
  public JsonOutgetlistaPaises:any[];
  public JsonOutgetlistaTipoInstitucion:any[];
  public JsonOutgetlistaInstitucion:any[];

  // Secuencias
  public JsonOutgetCodigoSecuenciaNew:any[];
  public JsonOutgetCodigoSecuenciaDet:any[];
  public codigoSecuencia:string;
  public valorSecuencia;
  public valorSecuenciaAct;
  public codigoSecuenciaDet;
  public valorSecuenciaDet;
  public valorSecuenciaDetAct;


  // Objeto que Controlara la Forma
  forma:FormGroup;

  // Ini | Definicion del Constructor
  constructor( private _loginService: LoginService,
               private _listasComunes: ListasComunesService,
               private _uploadService: UploadService,
               private _ingresoComunicacion: IngresoComunicacionService,
               private _router: Router,
               private _route: ActivatedRoute,
               private _appComponent: AppComponent,
               private _http: Http){
  } // Fin | Definicion del Constructor


  // Metodod onInit de la Formulario
  ngOnInit() {
    // Hacemos que la variable del Local Storge este en la API
    this.identity = JSON.parse(localStorage.getItem('identity'));

    // Iniciamoslos valores de los Prametros de listasComunes
    // Iniciamos los Parametros de Sub Direcciones Acompañantes
    this.paramsSubDirAcom = {
      "idDireccionSreci"  : ""
    };

    // Iniciamos los Parametros de Instituciones
    this.params = {
      "idPais"  : "",
      "idTipoInstitucion"  : ""
    };

    // Iniciamos los Parametros de Secuenciales
    this.paramsSecuencia = {
      "codSecuencial"  : "",
      "tablaSecuencia"  : "",
      "idTipoDocumento"  : ""
    };


    // Iniciamos los Parametros de Secuenciales
    this.paramsSecuenciaDet = {
      "codSecuencial"  : "",
      "tablaSecuencia"  : "",
      "idTipoDocumento"  : ""
    };

    // Iniciamos los Parametros de Encabezado de Conunicacion
    this.paramsSecuenciaIn = {
      "codSecuencial"  : "",
      "tablaSecuencia"  : "",
      "idTipoDocumento"  : ""
    };

    // Iniciamos los Parametros de Detalle de Comunicacion
    this.paramsSecuenciaDetIn = {
      "codSecuencial"  : "",
      "tablaSecuencia"  : "",
      "idTipoDocumento"  : ""
    };

    // Lsita de Tipo de Documentos
    this.getlistaTipoDocumentos();
    this.getlistaPaises();
    this.getlistaTipoInstituciones();

    this.getlistaDireccionesSRECIAcom();

    // Definicion de la Insercion de los Datos de Nueva Comunicacion
    this.comunicacion = new Comunicaciones(1, "","",  "", "", "",  0, "0", 0, 0, "7", 1, 0,"0", this.fechafin , null,  0, 0,  0, 0,  "", "", "", "", "", "",  "");

    // Eventos de Señaloizacion
    this.loading = "hide";

  } // Fin Metodo onInit()


  // Ini | Metodo onSubmit
  onSubmit(forma:NgForm){
      // Parseo de parametros que no se seleccionan
      // Parseo de parametros que no se seleccionan
      this.codigoSecuencia    = this.JsonOutgetCodigoSecuenciaNew[0].codSecuencial;
      this.valorSecuencia     = this.JsonOutgetCodigoSecuenciaNew[0].valor2 + 1;
      this.valorSecuenciaAct     = this.JsonOutgetCodigoSecuenciaNew[0].valor2;

      this.codigoSecuenciaDet = this.JsonOutgetCodigoSecuenciaDet[0].codSecuencial;
      this.valorSecuenciaDet  = this.JsonOutgetCodigoSecuenciaDet[0].valor2 + 1;
      this.valorSecuenciaDetAct  = this.JsonOutgetCodigoSecuenciaDet[0].valor2;

      // Secuenciales de la Tabla correspondencia Encabenzado
      this.comunicacion.codCorrespondencia = this.codigoSecuencia;
      this.comunicacion.secuenciaComunicacionIn = this.valorSecuencia;
      this.comunicacion.secuenciaComunicacionInAct = this.valorSecuenciaAct;

      // Secuenciales de la Tabla correspondencia detalle
      this.comunicacion.codCorrespondenciaDet = this.codigoSecuenciaDet;
      this.comunicacion.secuenciaComunicacionDet = this.valorSecuenciaDet;
      this.comunicacion.secuenciaComunicacionDetAct = this.valorSecuenciaDetAct;

      // Parametro para documento Seleccionado
      // Evaluamos si el Tipo de User no es Administrador
      if( this.identity.idTipoFunc != 4 && this.identity.idTipoFunc != 6){
          this.comunicacion.idEstado = "3";
          this.comunicacion.idDeptoFuncional = this.identity.idDeptoFuncional;
          this.comunicacion.idDireccionSreci = this.identity.idDireccion;
          this.comunicacion.idUsuarioAsaignado = this.identity.sub;
      }else if( this.identity.idTipoFunc == 6 ){
        this.comunicacion.idEstado = "7";
        this.comunicacion.idDeptoFuncional = this.identity.idDeptoFuncional;
        this.comunicacion.idDireccionSreci = this.identity.idDireccion;
        this.comunicacion.idUsuarioAsaignado = this.identity.sub;
      }else
      {
        this.comunicacion.idEstado = "7";
      }

      console.log( this.comunicacion );
      let token1 = this._ingresoComunicacion.getToken();
      this.loading = 'show';
      this._ingresoComunicacion.registerTipoComunicacion(token1, this.comunicacion).subscribe(
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

                //alert(this.mensajes);
            }else{
              //this.resetForm();
              this.loading = 'hidden';
              this.ngOnInit();
              // this.alertShow();
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
  * Fecha: 25-09-2017
  * Descripcion: Carga la Lista de los Tipos de Documentos
  * Objetivo: Obtener la lista de los Tipos de usuarios
  * de la BD, Llamando a la API, por su metodo
  * ( tipo-documento-list ).
  ******************************************************/
    getlistaTipoDocumentos() {
    this._listasComunes.listasComunes( "" ,"tipo-documento-list").subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetlistaTiposDocumentos = response.data;
            alert(response.msg);

          }else{
            this.JsonOutgetlistaTiposDocumentos = response.data;
            //console.log(response.data);
          }
        });
  } // FIN : FND-00001


  /*****************************************************
  * Funcion: FND-00001.1
  * Fecha: 25-09-2017
  * Descripcion: Carga la Lista de las Direcciones de
  * SRECI Acompañantes del Tema
  * Objetivo: Obtener la lista de las Direcciones SRECI
  * de la BD, Llamando a la API, por su metodo
  * (dir-sreci-list).
  ******************************************************/
  getlistaDireccionesSRECIAcom() {
    //Llamar al metodo, de Login para Obtener la Identidad
    this._listasComunes.listasComunes("","dir-sreci-list").subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            alert(response.msg);
          }else{
            //this.data = JSON.stringify(response.data);
            this.JsonOutgetlistaDireccionSRECIAcom = response.data;
          }
        });
  } // FIN : FND-00001.1


  /*****************************************************
  * Funcion: FND-00001.2
  * Fecha: 25-09-2017
  * Descripcion: Carga la Lista de las Sub Direcciones de
  * SRECI Acompañantes
  * Objetivo: Obtener la lista de las Direcciones SRECI
  * de la BD, Llamando a la API, por su metodo
  * (subdir-sreci-list).
  ******************************************************/
  getlistaSubDireccionesSRECIAcom() {
    //Llamar al metodo, de Login para Obtener la Identidad
    this.paramsSubDirAcom.idDireccionSreci = this.comunicacion.idDireccionSreciAcom;

    this._listasComunes.listasComunes( this.paramsSubDirAcom,"subdir-sreci-list").subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetlistaSubDireccionSRECIAcom = response.data;
            alert(response.msg);
          }else{
            //this.data = JSON.stringify(response.data);
            this.JsonOutgetlistaSubDireccionSRECIAcom = response.data;
          }
        });
  } // FIN : FND-00001.2


  /******************************************************
  * Funcion: FND-00002
  * Fecha: 25-09-2017
  * Descripcion: Carga la Lista de los Paises.
  * Objetivo: Obtener la lista de los Paises de la BD,
  * Llamando a la API, por su metodo (paises-list).
  *******************************************************/
  getlistaPaises() {
    //Llamar al metodo, de Login para Obtener la Identidad
    this._listasComunes.listasComunes("","paises-list").subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetlistaPaises = response.data;
            alert(response.msg);
          }else{
            //this.data = JSON.stringify(response.data);
            this.JsonOutgetlistaPaises = response.data;
          }
        });
  } // FIN : FND-00002


  /*****************************************************
  * Funcion: FND-00002.1
  * Fecha: 25-09-2017
  * Descripcion: Carga la Lista de los Tipos de
  * Instituciones
  * Objetivo: Obtener la lista de los Tipos de
  * Instituciones de la BD, Llamando a la API, por su
  * metodo (tipo-instituciones-sreci-list).
  ******************************************************/
  getlistaTipoInstituciones() {
    //Llamar al metodo, de Login para Obtener la Identidad
    this._listasComunes.listasComunes("","tipo-instituciones-sreci-list").subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetlistaTipoInstitucion = response.data;
            alert(response.msg);
          }else{
            //this.data = JSON.stringify(response.data);
            this.JsonOutgetlistaTipoInstitucion = response.data;
          }
        });
  } // FIN : FND-00002.1


  /*****************************************************
  * Funcion: FND-00002.2
  * Fecha: 25-09-2017
  * Descripcion: Carga la Lista de las Instituciones
  * Objetivo: Obtener la lista de los Tipos de usuarios
  * de la BD, Llamando a la API, por su metodo
  * (instituciones-sreci-list).
  ******************************************************/
    getlistaInstituciones() {
    this.params.idPais = this.comunicacion.idPais;
    this.params.idTipoInstitucion = this.comunicacion.idTipoInstitucion;
    //Llamar al metodo, de Login para Obtener la Identidad
    //console.log(this.params);

    this._listasComunes.listasComunes(this.params,"instituciones-sreci-list").subscribe(
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
  } // FIN : FND-00002.2


  /*****************************************************
  * Funcion: FND-00003
  * Fecha: 25-09-2017
  * Descripcion: Obtiene la siguiente secuencia
  * Objetivo: Obtener el secuencial de la tabla
  * indicada con su cosigo
  * (gen-secuencia-comunicacion-in).
  ******************************************************/
   getCodigoCorrespondencia(){
     //Llamar al metodo, de Login para Obtener la Identidad
     this.paramsSecuenciaIn.idTipoDocumento = this.comunicacion.idTipoDocumento;
     //alert(this.comunicacion.idTipoDocumento);
     //Evaluamos el valor del Tipo de Documento
     if( this.paramsSecuenciaIn.idTipoDocumento == 1 ){
       this.paramsSecuencia.codSecuencial = "COM-OUT-OFI";
       this.paramsSecuencia.tablaSecuencia = "tbl_comunicacion_enc";
       this.paramsSecuencia.idTipoDocumento = this.paramsSecuenciaIn.idTipoDocumento ;
     } else if ( this.paramsSecuenciaIn.idTipoDocumento == 5 ) {
       this.paramsSecuencia.codSecuencial = "COM-OUT-MAIL";
       this.paramsSecuencia.tablaSecuencia = "tbl_comunicacion_mail";
       this.paramsSecuencia.idTipoDocumento = this.paramsSecuenciaIn.idTipoDocumento;
     } else if ( this.paramsSecuenciaIn.idTipoDocumento == 7 ){
       this.paramsSecuencia.codSecuencial = "COM-OUT-CALL";
       this.paramsSecuencia.tablaSecuencia = "tbl_comunicacion_call";
       this.paramsSecuencia.idTipoDocumento = this.paramsSecuenciaIn.idTipoDocumento;
     } else if ( this.paramsSecuenciaIn.idTipoDocumento == 8 ) {
       this.paramsSecuencia.codSecuencial = "COM-OUT-VERB";
       this.paramsSecuencia.tablaSecuencia = "tbl_comunicacion_verb";
       this.paramsSecuencia.idTipoDocumento = this.paramsSecuenciaIn.idTipoDocumento;
     }// Fin de Condicion

    //Llamar al metodo, de Login para Obtener la Identidad
    this._listasComunes.listasComunesToken(this.paramsSecuencia, "gen-secuencia-comunicacion-in" ).subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetCodigoSecuenciaNew = response.data;
            alert(response.msg);

          }else{
            this.JsonOutgetCodigoSecuenciaNew = response.data;
            console.log(response.data);
            this.getCodigoCorrespondenciaDet( this.paramsSecuenciaIn.idTipoDocumento );
          }
        });

  } // FIN : FND-00003


  /*****************************************************
  * Funcion: FND-00003.1
  * Fecha: 25-09-2017
  * Descripcion: Obtiene la siguiente secuencia
  * Objetivo: Obtener el secuencial de la tabla
  * indicada con su cosigo
  * (gen-secuencia-comunicacion-in).
  ******************************************************/
   getCodigoCorrespondenciaDet( idTipoDocumentoIn:number ){
     //Llamar al metodo, de Login para Obtener la Identidad
    //  this.paramsSecuenciaDetIn.idTipoDocumento = this.comunicacion.idTipoDocumento;
     //alert(this.comunicacion.idTipoDocumento);
     //Evaluamos el valor del Tipo de Documento
     if( idTipoDocumentoIn == 1 ){
       this.paramsSecuenciaDet.codSecuencial = "COM-OUT-DET-OFI";
       this.paramsSecuenciaDet.tablaSecuencia = "tbl_comunicacion_det";
       this.paramsSecuenciaDet.idTipoDocumento = idTipoDocumentoIn ;
     } else if ( idTipoDocumentoIn == 5 ) {
       this.paramsSecuenciaDet.codSecuencial = "COM-OUT-DET-MAIL";
       this.paramsSecuenciaDet.tablaSecuencia = "tbl_comunicacion_det_mail";
       this.paramsSecuenciaDet.idTipoDocumento = idTipoDocumentoIn;
     } else if ( idTipoDocumentoIn == 7 ){
       this.paramsSecuenciaDet.codSecuencial = "COM-OUT-DET-CALL";
       this.paramsSecuenciaDet.tablaSecuencia = "tbl_comunicacion_det_call";
       this.paramsSecuenciaDet.idTipoDocumento = idTipoDocumentoIn;
     } else if ( idTipoDocumentoIn == 8 ) {
       this.paramsSecuenciaDet.codSecuencial = "COM-OUT-DET-VERB";
       this.paramsSecuenciaDet.tablaSecuencia = "tbl_comunicacion_det_verb";
       this.paramsSecuenciaDet.idTipoDocumento = idTipoDocumentoIn;
     }// Fin de Condicion

    //Llamar al metodo, de Login para Obtener la Identidad
    //console.log(this.params);
    this._listasComunes.listasComunesToken(this.paramsSecuenciaDet, "gen-secuencia-comunicacion-in" ).subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetCodigoSecuenciaDet = response.data;
            alert(response.msg);

          }else{
            this.JsonOutgetCodigoSecuenciaDet = response.data;
            console.log(response.data);
          }
        });

  } // FIN : FND-00003.1


}

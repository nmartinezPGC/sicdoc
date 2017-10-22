import { Component, OnInit, NgZone } from '@angular/core';
import { FormGroup, FormControl, Validators } from '@angular/forms';

import { RouterModule, Routes, ActivatedRoute, Router } from '@angular/router';

import { HttpModule,  Http, Response, Headers } from '@angular/http';

// Lirerias para el AutoComplete
import {Observable} from 'rxjs/Observable';
import 'rxjs/add/operator/startWith';
import 'rxjs/add/operator/map';

//Importamos los Servicios
import { LoginService } from '../../services/login/login.service'; //Servico del Login
import { IngresoComunicacionService } from '../../services/comunicaciones/ingreso.service'; //Servico del Comunicaciones
import { ListasComunesService } from '../../services/shared/listas.service'; //Servico Listas Comunes
import { UploadService } from '../../services/shared/upload.service'; //Servico Carga de Arhcivos

import { AppComponent } from '../../app.component'; //Servico del Login

import { NgForm }    from '@angular/forms';

// Importamos la CLase Usuarios del Modelo
import { Usuarios } from '../../models/usuarios/usuarios.model'; // Servico del Login
import { Comunicaciones } from '../../models/comunicaciones/comunicacion.model'; // Modelo a Utilizar

// Declaramos las variables para jQuery
declare var jQuery:any;
declare var $:any;

@Component({
  selector: 'app-ingreso-comunicacion',
  templateUrl: '../../views/comunicaciones/ingreso.component.html',
  styleUrls: ['../../views/comunicaciones/style.component.css'],
  providers: [ IngresoComunicacionService ,LoginService, ListasComunesService, UploadService]
})


export class IngresoComunicacionComponent implements OnInit{

  public titulo:string = "Ingreso de Comunicación";
  public fechaHoy:Date = new Date();
  public fechafin:string;

  private params;
  private paramsSecuencia;
  private paramsSecuenciaDet;
  private paramsSubDir;
  private paramsSubDirAcom;
  private paramsTipoFuncionario; // Parametros para el Filtro de Funcionario

  private paramsDocumentosSend; // Parametros para los Documentos enviados


  private paramsSecuenciaIn;

  // Propiedad de Loader
  public loading      = 'show';
  public alertSuccess = 'show';
  public alertError   = 'show';

  // Propiedades de los Resumenes
  public countOficiosIngresados;
  public countOficiosPendientes;
  public countOficiosFinalizados;

  // Instacia de la variable del Modelo | Json de Parametros
  public user:Usuarios;
  public comunicacion: Comunicaciones;
//
  // Objeto que Controlara la Forma
  forma:FormGroup;

 // Variables de Mensajeria y Informaicon
  public data;
  public errorMessage;
  public status;
  public mensajes;

  public pdfUpLoad;

  // variables de Identificacion
  public identity;
  public token;

  // Variables de Secuencias
  public codigoSecuencia; // Secuencia en Texto del Oficio
  public valorSecuencia; // Secuencial del Oficio
  public codigoSecuenciaDet; // Secuencia en Texto del Oficio
  public valorSecuenciaDet; // Secuencial del Oficio
  public emailDireccionIN; // Correo de la Direccion

  // Datos del Funcionario
  public emailDireccionFuncionarioIN; // Correo del Funcionario
  public nombreFuncionarioIN; // Nombre Funcionario
  public apellidoFuncionarioIN; // Apellido Funcionario


  // Variables de Generacion de las Listas de los Dropdow
  // Llenamos las Lista del HTML
  public JsonOutgetlistaEstados:any[];
  public JsonOutgetlistaTipoUsuario:any[];
  public JsonOutgetlistaPaises:any[];
  public JsonOutgetlistaTipoInstitucion:any[];
  public JsonOutgetlistaInstitucion:any[];
  public JsonOutgetlistaDireccionSRECI:any[];
  public JsonOutgetlistaDireccionSRECIAcom:any[];
  public JsonOutgetlistaSubDireccionSRECI:any[];
  public JsonOutgetlistaSubDireccionSRECIAcom:any[];

  public JsonOutgetlistaTipoFuncionariosSRECI:any[];
  public JsonOutgetlistaFuncionariosSRECI:any[];
  public JsonOutgetlistaTiposDocumentos:any[];

  public JsonOutgetCodigoSecuenciaNew:any[];
  public JsonOutgetCodigoSecuenciaDet:any[];

  // Json del Recuento de Datos
  public JsonOutgetListaOficiosIngresados:any[];
  public JsonOutgetListaOficiosPendientes:any[];
  public JsonOutgetListaOficiosFinalizados:any[];

  // Array de Documentos de Comunicacion
  public JsonOutgetListaDocumentos = [];


  // Variabls para validaciones de Seleccionado
  public maxlengthCodReferencia = "38"; // Defaul Correo
  public minlengthCodReferencia = "5"; // Defaul Correo
  public pattern ="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$"; // Defaul Correo


  // Variables para la Persistencia de los Datos en los Documentos
  public nextDocumento:number = 1;
  public extencionDocumento:string;
  public seziDocumento:number;

  // Variables del Metodo
  public  error:string;
  // public  status:string;
  public  codigoSec:string;


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


  // INI | Metodo OnInit
  ngOnInit(){
    // Hacemos que la variable del Local Storge este en la API
    this.identity = JSON.parse(localStorage.getItem('identity'));

    // Iniciamos los Parametros de Instituciones
    this.params = {
      "idPais"  : "",
      "idTipoInstitucion"  : ""
    };

    // Iniciamos los Parametros de Secuenciales
    this.paramsSecuencia = {
      "codSecuencial"  : "",
      "tablaSecuencia" : "",
      "idTipoDocumento" : ""
    };

    // Iniciamos los Parametros de Secuenciales
    this.paramsSecuenciaDet = {
      "codSecuencial"  : "",
      "tablaSecuencia" : "",
      "idTipoDocumento" : ""
    };

    // Iniciamos los Parametros de Sub Direcciones
    this.paramsSubDir = {
      "idDireccionSreci"  : ""
    };

    // Iniciamos los Parametros de Sub Direcciones Acompañantes
    this.paramsSubDirAcom = {
      "idDireccionSreci"  : ""
    };

    // Iniciamos los Parametros de Usuarios a Asignar el Oficio
    this.paramsTipoFuncionario = {
      "idTipoFuncionario"  : ""
    };


    // Iniciamos los Parametros de Encabezado de Conunicacion
    this.paramsSecuenciaIn = {
      "codSecuencial"  : "",
      "tablaSecuencia"  : "",
      "idTipoDocumento"  : ""
    };


    // this.JsonOutgetListaDocumentos = {
    //   "nameDoc":"",
    //   "extDoc":"",
    //   "pesoDoc":""
    // };

    // Lsita de Tipo de Documentos
    this.getlistaTipoDocumentos();

    // Inicializacion de las Listas
    this.getlistaEstadosComunicacion();
    this.getlistaPaises();
    this.getlistaTipoInstituciones();
    this.getlistaDireccionesSRECI();

    // Direcciones Acompañantes
    this.getlistaDireccionesSRECIAcom();

    // Generar la Lista de Secuenciales
    this.listarCodigoCorrespondencia();
    //this.listarCodigoCorrespondenciaDet();

    // Tipo Funcionarios de la SRECI
    this.getlistaTipoFuncionariosSRECI();

    // Convertimos las Fechas a una Default
    this.convertirFecha();

    // Definicion de la Insercion de los Datos de Nueva Comunicacion
    this.comunicacion = new Comunicaciones(1, "", "", "", "", "",
                                           0, "0", 0, 0, "7", 1, 0, "0",
                                           this.fechafin , null,
                                           0, 0,  0, 0,
                                           "", "", "", "", "", "", "", "",
                                           "");

    // Llenamos la Lsita de Sub Direcciones despues de los Campos Default
    this.getlistaSubDireccionesSRECI();

    // Llenamos la Lsita Funcionarios despues de los Campos Default
    // this.getlistaUsuariosAsinadosSRECI();

    // Resumenes de la Pantalla
    this.getlistaOficosIngresados();
    this.getlistaOficosPendientes();
    this.getlistaOficosFinalizados();


    // Eventos de Señaloizacion

    this.loading = "hide";


    // this.removeFileInput();


    // this.getlistaSubDireccionesSRECIAcom();

    // this.loadScript('../assets/js/ingreso.comunicacion.component.js');
  } // Fin | Metodo ngOnInit



  // Ini | Metodo onSubmit
  onSubmit(forma:NgForm){
      //this.validCampos();
      // Parseo de parametros que no se seleccionan
      this.codigoSecuencia    = this.JsonOutgetCodigoSecuenciaNew[0].codSecuencial;
      this.valorSecuencia     = this.JsonOutgetCodigoSecuenciaNew[0].valor2 + 1;
      this.codigoSecuenciaDet = this.JsonOutgetCodigoSecuenciaDet[0].codSecuencial;
      this.valorSecuenciaDet  = this.JsonOutgetCodigoSecuenciaDet[0].valor2 + 1;

      // Secuenciales de la Tabla correspondencia Encabenzado
      // this.comunicacion.codCorrespondencia = this.codigoSecuencia + "-" + this.valorSecuencia;
      this.comunicacion.codCorrespondencia = this.codigoSecuencia;
      this.comunicacion.secuenciaComunicacionIn = this.valorSecuencia;

      // Secuenciales de la Tabla correspondencia detalle
      // this.comunicacion.codCorrespondenciaDet = this.codigoSecuenciaDet + "-" + this.valorSecuenciaDet;
      this.comunicacion.codCorrespondenciaDet = this.codigoSecuenciaDet;
      this.comunicacion.secuenciaComunicacionDet = this.valorSecuenciaDet;

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

      let token1 = this._ingresoComunicacion.getToken();
      this.loading = 'show';
      console.log(this.comunicacion);
      this._ingresoComunicacion.registerComunicacion(token1, this.comunicacion).subscribe(
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


  /****************************************************
  * Funcion: FND-00001.1
  * Fecha: 01-09-2017
  * Descripcion: Limpia todo el Fomulario, las variables
  * Objetivo: Resetear el Formulario uso de la pagina
  *****************************************************/
  public resetForm() {
    // Iniciamos los Parametros de Instituciones
    this.params = {
      "idPais"  : "",
      "idTipoInstitucion"  : ""
    };

    // Iniciamos los Parametros de Secuenciales
    this.paramsSecuencia = {
      "codSecuencial"  : "",
      "tablaSecuencia" : "",
      "idTipoDocumento" : ""
    };

    // Iniciamos los Parametros de Secuenciales
    this.paramsSecuenciaDet = {
      "codSecuencial"  : "",
      "tablaSecuencia" : "",
      "idTipoDocumento" : ""
    };

    // Iniciamos los Parametros de Sub Direcciones
    this.paramsSubDir = {
      "idDireccionSreci"  : ""
    };

    // Iniciamos los Parametros de Sub Direcciones Acompañantes
    this.paramsSubDirAcom = {
      "idDireccionSreci"  : ""
    };

    // Iniciamos los Parametros de Usuarios a Asignar Oficios
    this.paramsTipoFuncionario = {
      "idTipoFuncionario"  : ""
    };

    this.status = "hide";
    this.loading = "hide";

    this.comunicacion = new Comunicaciones(1, "", "",  "", "", "",  0, "0", 0, 0 ,"7", 1, 0, "0",  "", "",  0, 0,  0, 0,  "","","","",  "", "",  "", "", "");
  } // FIN : FND-00001.1


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


  /****************************************************
  * Funcion: FND-00001.3
  * Fecha: 11-09-2017
  * Descripcion: Funcion que valida los Campos
  * del Formulario de ingreso de Oficios
  *****************************************************/
  validCampos() {
    // Definicion de las variables del Formulario
    let codigoRefIn = this.comunicacion.codCorrespondencia;

    if( codigoRefIn.length < 5 ){
      alert("Falta Ingresar el Codigo de Referencia del Oficio.");
      this.mensajes = "Falta Ingresar el Codigo de Referencia del Oficio";
      return false;
    }
    //alert("Dia " + day + " Mes " + month + " Año " + year);
  } // FIN : FND-00001.3


  /****************************************************
  * Funcion: FND-00001.3
  * Fecha: 11-09-2017
  * Descripcion: Funcion que valida los Campos
  * del Formulario de ingreso de Oficios
  *****************************************************/
  alertShow() {
    // Definicion de las variables del Formulario
    //setTimeout(this.status = "hide", 3000);
    // this.status = "hide";
    setTimeout(function(){ alert("Hello"); }, 3000);
    setTimeout(function(){ this.status = "hide"; }, 3000);
  } // FIN : FND-00001.3


  /*****************************************************
  * Funcion: FND-00002
  * Fecha: 30-07-2017
  * Descripcion: Carga la Lista de los Estados de la BD
  * Objetivo: Obtener la lista de los Estados de las
  * Comunicaciones de la BD, Llamando a la API, por su
  * metodo (estados-comunicacion-list).
  ******************************************************/
  getlistaEstadosComunicacion() {
    //Llamar al metodo, de Login para Obtener la Identidad
    this._listasComunes.listasComunes("","estados-comunicacion-list").subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetlistaEstados = response.data;
            alert(response.msg);
          }else{
            this.JsonOutgetlistaEstados = response.data;
          }
        });
  } // FIN : FND-00002


  /******************************************************
  * Funcion: FND-00003
  * Fecha: 30-07-2017
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
  } // FIN : FND-00003


  /*****************************************************
  * Funcion: FND-00004
  * Fecha: 29-07-2017
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
  } // FIN : FND-00004


  /*****************************************************
  * Funcion: FND-00004.1
  * Fecha: 31-07-2017
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
   } // FIN : FND-00004.1


  /*****************************************************
  * Funcion: FND-00004.2
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
  } // FIN : FND-00004.2



  /*****************************************************
  * Funcion: FND-00004.3
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
       this.paramsSecuencia.codSecuencial = "COM-IN-OFI";
       this.paramsSecuencia.tablaSecuencia = "tbl_comunicacion_enc";
       this.paramsSecuencia.idTipoDocumento = this.paramsSecuenciaIn.idTipoDocumento;
       this.comunicacion.codReferenciaSreci = "";
       // Disable codReferenciaSreci
      //  $( "#codReferenciaSreci" ).prop( "disabled", true );
       // Seteo de variable de validaciones | Oficio de Salida
       this.maxlengthCodReferencia = "30";
       this.minlengthCodReferencia = "5";
       this.pattern ="";

     } else if ( this.paramsSecuenciaIn.idTipoDocumento == 2 ) {
       this.paramsSecuencia.codSecuencial = "COM-IN-MEMO";
       this.paramsSecuencia.tablaSecuencia = "tbl_comunicacion_enc";
       this.paramsSecuencia.idTipoDocumento = this.paramsSecuenciaIn.idTipoDocumento;
       this.comunicacion.codReferenciaSreci = "";
       // Disable codReferenciaSreci
      //  $( "#codReferenciaSreci" ).prop( "disabled", true );
       // Seteo de variable de validaciones | Oficio de Salida
       this.maxlengthCodReferencia = "30";
       this.minlengthCodReferencia = "5";
       this.pattern ="";

     } else if ( this.paramsSecuenciaIn.idTipoDocumento == 3 ) {
       this.paramsSecuencia.codSecuencial = "COM-IN-NOTA-VERBAL";
       this.paramsSecuencia.tablaSecuencia = "tbl_comunicacion_enc";
       this.paramsSecuencia.idTipoDocumento = this.paramsSecuenciaIn.idTipoDocumento;
       this.comunicacion.codReferenciaSreci = "";
       // Disable codReferenciaSreci
      //  $( "#codReferenciaSreci" ).prop( "disabled", true );
       // Seteo de variable de validaciones | Oficio de Salida
       this.maxlengthCodReferencia = "30";
       this.minlengthCodReferencia = "5";
       this.pattern ="";

     } else if ( this.paramsSecuenciaIn.idTipoDocumento == 4 ) {
       this.paramsSecuencia.codSecuencial = "COM-IN-CIRCULAR";
       this.paramsSecuencia.tablaSecuencia = "tbl_comunicacion_enc";
       this.paramsSecuencia.idTipoDocumento = this.paramsSecuenciaIn.idTipoDocumento;
       this.comunicacion.codReferenciaSreci = "";
       // Disable codReferenciaSreci
      //  $( "#codReferenciaSreci" ).prop( "disabled", true );
       // Seteo de variable de validaciones | Oficio de Salida
       this.maxlengthCodReferencia = "30";
       this.minlengthCodReferencia = "5";
       this.pattern ="";

     } else if ( this.paramsSecuenciaIn.idTipoDocumento == 5 ) {
       this.paramsSecuencia.codSecuencial = "COM-IN-MAIL";
       this.paramsSecuencia.tablaSecuencia = "tbl_comunicacion_mail";
       this.paramsSecuencia.idTipoDocumento = this.paramsSecuenciaIn.idTipoDocumento;
       this.comunicacion.codReferenciaSreci = "";
       // Disable codReferenciaSreci
       $( "#codReferenciaSreci" ).prop( "disabled", false );
       // Seteo de variable de validaciones | Correo
       this.maxlengthCodReferencia = "38";
       this.minlengthCodReferencia = "10";
       this.pattern ="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$";

     } else if ( this.paramsSecuenciaIn.idTipoDocumento == 7 ){
       this.paramsSecuencia.codSecuencial = "COM-IN-CALL";
       this.paramsSecuencia.tablaSecuencia = "tbl_comunicacion_call";
       this.paramsSecuencia.idTipoDocumento = this.paramsSecuenciaIn.idTipoDocumento;
       this.comunicacion.codReferenciaSreci = "";
       // Disable codReferenciaSreci
       $( "#codReferenciaSreci" ).prop( "disabled", false );
       // Seteo de variable de validaciones | Llamada
       this.maxlengthCodReferencia = "8";
       this.minlengthCodReferencia = "8";
       this.pattern ="^([0-9])*$";

     } else if ( this.paramsSecuenciaIn.idTipoDocumento == 8 ) {
       this.paramsSecuencia.codSecuencial = "COM-IN-VERB";
       this.paramsSecuencia.tablaSecuencia = "tbl_comunicacion_verb";
       this.paramsSecuencia.idTipoDocumento = this.paramsSecuenciaIn.idTipoDocumento;
       this.comunicacion.codReferenciaSreci = "";
       // Disable codReferenciaSreci
       $( "#codReferenciaSreci" ).prop( "disabled", false );
       // Seteo de variable de validaciones | Llamada
       this.maxlengthCodReferencia = "38";
       this.minlengthCodReferencia = "15";
       this.pattern ="";

     } else if ( this.paramsSecuenciaIn.idTipoDocumento == 9 ) {
       this.paramsSecuencia.codSecuencial = "COM-IN-REUNION";
       this.paramsSecuencia.tablaSecuencia = "tbl_comunicacion_reunion";
       this.paramsSecuencia.idTipoDocumento = this.paramsSecuenciaIn.idTipoDocumento;
       this.comunicacion.codReferenciaSreci = "";
       // Disable codReferenciaSreci
       $( "#codReferenciaSreci" ).prop( "disabled", false );
       // Seteo de variable de validaciones | Llamada
       this.maxlengthCodReferencia = "38";
       this.minlengthCodReferencia = "15";
       this.pattern ="";

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
            // Ejecutamos la Funcion de Secuencia de Detalle
            this.getCodigoCorrespondenciaDet( this.paramsSecuenciaIn.idTipoDocumento );
          }
        });
  } // FIN : FND-00004.3


  /*****************************************************
  * Funcion: FND-00004.3.1
  * Fecha: 25-09-2017
  * Descripcion: Obtiene la siguiente secuencia
  * Objetivo: Obtener el secuencial de la tabla
  * indicada con su cosigo
  * (gen-secuencia-comunicacion-in).
  ******************************************************/
  getCodigoCorrespondenciaDet( idTipoDocumentoIn:number ){
     //Llamar al metodo, de Login para Obtener la Identidad
    //  this.paramsSecuenciaDetIn.idTipoDocumento = this.comunicacion.idTipoDocumento;
     //Evaluamos el valor del Tipo de Documento
     if( idTipoDocumentoIn == 1 ){
       this.paramsSecuenciaDet.codSecuencial = "COM-IN-DET-OFI";
       this.paramsSecuenciaDet.tablaSecuencia = "tbl_comunicacion_det";
       this.paramsSecuenciaDet.idTipoDocumento = idTipoDocumentoIn ;
     } else if ( idTipoDocumentoIn == 2 ) {
       this.paramsSecuenciaDet.codSecuencial = "COM-IN-DET-MEMO";
       this.paramsSecuenciaDet.tablaSecuencia = "tbl_comunicacion_det";
       this.paramsSecuenciaDet.idTipoDocumento = idTipoDocumentoIn;
     } else if ( idTipoDocumentoIn == 3 ) {
       this.paramsSecuenciaDet.codSecuencial = "COM-IN-DET-NOTA-VERBAL";
       this.paramsSecuenciaDet.tablaSecuencia = "tbl_comunicacion_det";
       this.paramsSecuenciaDet.idTipoDocumento = idTipoDocumentoIn;
     } else if ( idTipoDocumentoIn == 4 ) {
       this.paramsSecuenciaDet.codSecuencial = "COM-IN-DET-CIRCULAR";
       this.paramsSecuenciaDet.tablaSecuencia = "tbl_comunicacion_det";
       this.paramsSecuenciaDet.idTipoDocumento = idTipoDocumentoIn;
     } else if ( idTipoDocumentoIn == 5 ) {
       this.paramsSecuenciaDet.codSecuencial = "COM-IN-DET-MAIL";
       this.paramsSecuenciaDet.tablaSecuencia = "tbl_comunicacion_det_mail";
       this.paramsSecuenciaDet.idTipoDocumento = idTipoDocumentoIn;
     } else if ( idTipoDocumentoIn == 7 ){
       this.paramsSecuenciaDet.codSecuencial = "COM-IN-DET-CALL";
       this.paramsSecuenciaDet.tablaSecuencia = "tbl_comunicacion_det_call";
       this.paramsSecuenciaDet.idTipoDocumento = idTipoDocumentoIn;
     } else if ( idTipoDocumentoIn == 8 ) {
       this.paramsSecuenciaDet.codSecuencial = "COM-IN-DET-VERB";
       this.paramsSecuenciaDet.tablaSecuencia = "tbl_comunicacion_det_verb";
       this.paramsSecuenciaDet.idTipoDocumento = idTipoDocumentoIn;
     } else if ( idTipoDocumentoIn == 9 ) {
       this.paramsSecuenciaDet.codSecuencial = "COM-IN-DET-REUNION";
       this.paramsSecuenciaDet.tablaSecuencia = "tbl_comunicacion_det_reunion";
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
  } // FIN : FND-00004.3.1


  /*****************************************************
  * Funcion: FND-00005
  * Fecha: 29-07-2017
  * Descripcion: Carga la Imagen de usuario desde el File
  * Objetivo: Obtener la imagen que se carga desde el
  * control File de HTML
  * (fileChangeEvent).
  ******************************************************/
  public filesToUpload: Array<File>;
  public resultUpload;

  fileChangeEvent(fileInput: any){
    //console.log('Evento Chge Lanzado'); , codDocumentoIn:string
    this.filesToUpload = <Array<File>>fileInput.target.files;

    // Direccion del Metodo de la API
    let url = "http://localhost/sicdoc/symfony/web/app_dev.php/comunes/documentos-upload-options";
    // let url = "http://172.17.3.90/sicdoc/symfony/web/app.php/comunes/upload-documento";
    // let url = "http://192.168.0.15/sicdoc/symfony/web/app.php/comunes/upload-documento";

    // Parametros de las Secuencias
    this.codigoSecuencia = this.JsonOutgetCodigoSecuenciaNew[0].codSecuencial;
    this.valorSecuencia  = this.JsonOutgetCodigoSecuenciaNew[0].valor2 + 1;
    this.codigoSec = this.codigoSecuencia + "-" + this.valorSecuencia;

    this.codigoSec = this.codigoSec + '-' + this.nextDocumento;
    this.nextDocumento = this.nextDocumento + 1;

    // Tamaño
    let sizeByte:number = this.filesToUpload[0].size;
    let siezekiloByte:number =  Math.round( sizeByte / 1024 );

    this.seziDocumento = siezekiloByte;

    let type = this.filesToUpload[0].type;

    var filename = $("#pdfDocumento").val();

    // Use a regular expression to trim everything before final dot
    this.extencionDocumento = filename.replace(/^.*\./, '');
        // alert(this.extencionDocumento);

    // this.paramsDocs.nombreDocumento = this.consultaContactos.nombre1Contacto + ' '
                                    // + this.consultaContactos.apellido1Contacto;

    //  this.paramsDocs.optDocumento = optDoc;

     let sendParms = "json=" + "";
     let codigoSec2 = "sdsd";

     // Parametro para documento Seleccionado
     this.comunicacion.pdfDocumento = this.codigoSec;

    //  console.log(this.paramsDocs);

    // Ejecutamos el Servicio con los Parametros
    this._uploadService.makeFileRequestNoToken( url, [ 'name_pdf', this.codigoSec ], this.filesToUpload ).then(
        ( result ) => {
          this.resultUpload = result;
          status = this.resultUpload.status;
          console.log(this.resultUpload);
          if(status === "error"){
            console.log(this.resultUpload);
            alert(this.resultUpload.msg);
          } else {
            // Añadimos a la Tabla Temporal los Items Subidos
            this.createNewFileInput();
          }
          // alert(this.resultUpload.data);
        },
        ( error ) => {
          alert(error);
          console.log(error);
        });
  } // FIN : FND-00004



  /*****************************************************
  * Funcion: FND-00006
  * Fecha: 01-09-2017
  * Descripcion: Obtiene la siguiente secuencia
  * Objetivo: Obtener el secuencial de la tabla
  * indicada con su cosigo
  * (gen-secuencia-comunicacion-in).
  ******************************************************/
   listarCodigoCorrespondencia(){
    this.paramsSecuencia.codSecuencial = "COM-IN-OFI";
    this.paramsSecuencia.tablaSecuencia = "tbl_comunicacion_enc";
    this.paramsSecuencia.idTipoDocumento = "1";
    let nextCodComunicacion:string = "";
    //Llamar al metodo, de Login para Obtener la Identidad
    //console.log(this.params);
    this._listasComunes.listasComunesToken(this.paramsSecuencia, "gen-secuencia-comunicacion-in" ).subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetCodigoSecuenciaNew = response.data;
            alert(response.msg);

          }else{
            this.JsonOutgetCodigoSecuenciaNew = response.data;
            this.listarCodigoCorrespondenciaDet();
            //console.log(response.data);
          }
        });
   } // FIN : FND-00006


  /*****************************************************
  * Funcion: FND-00006.1
  * Fecha: 03-09-2017
  * Descripcion: Obtiene la siguiente secuencia
  * Objetivo: Obtener el secuencial de la tabla
  * indicada con su cosigo
  * (gen-secuencia-comunicacion-in).
  ******************************************************/
   listarCodigoCorrespondenciaDet(){
    this.paramsSecuenciaDet.codSecuencial = "COM-IN-DET-OFI";
    this.paramsSecuenciaDet.tablaSecuencia = "tbl_comunicacion_det";
    this.paramsSecuenciaDet.idTipoDocumento = "1";
    let nextCodComunicacion:string = "";
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
            //console.log(response.data);
          }
        });
   } // FIN : FND-00006.1


  /*****************************************************
  * Funcion: FND-00006.1
  * Fecha: 01-09-2017
  * Descripcion: Ejecutamos la logica, para concatenar,
  * el Nuevo Codigo de la Comunicacion
  * Objetivo: Concannnntenar el Nuevo Codigo y usarlo
  * (gen-secuencia-comunicacion-in).
  ******************************************************/
   generarCodigoCorrespondencia( cadenaCodigoConverir ){
    //Llamar al metodo, de Login para Obtener la Identidad
    //console.log(this.params);
    let json = JSON.stringify( cadenaCodigoConverir );
    console.log(cadenaCodigoConverir);
   } // FIN : FND-00006.1

  /*****************************************************
  * Funcion: FND-00007
  * Fecha: 31-08-2017
  * Descripcion: Carga la Lista de las Direcciones de
  * SRECI
  * Objetivo: Obtener la lista de las Direcciones SRECI
  * de la BD, Llamando a la API, por su metodo
  * (dir-sreci-list).
  ******************************************************/
  getlistaDireccionesSRECI() {
    //Llamar al metodo, de Login para Obtener la Identidad
    this._listasComunes.listasComunes("","dir-sreci-list").subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            alert(response.msg);
          }else{
            //this.data = JSON.stringify(response.data);
            this.JsonOutgetlistaDireccionSRECI = response.data;
          }
        });
  } // FIN : FND-00007


  /*****************************************************
  * Funcion: FND-00007.1.1
  * Fecha: 31-08-2017
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
  } // FIN : FND-00007.1.1


  /*****************************************************
  * Funcion: FND-00007.1
  * Fecha: 31-08-2017
  * Descripcion: Carga la Lista de las Sub Direcciones de
  * SRECI
  * Objetivo: Obtener la lista de las Direcciones SRECI
  * de la BD, Llamando a la API, por su metodo
  * (subdir-sreci-list).
  ******************************************************/
  getlistaSubDireccionesSRECI() {
    //Llamar al metodo, de Login para Obtener la Identidad
    this.paramsSubDir.idDireccionSreci = this.comunicacion.idDireccionSreci;

    this._listasComunes.listasComunes( this.paramsSubDir,"subdir-sreci-list").subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetlistaSubDireccionSRECI = response.data;
            alert(response.msg);
          }else{
            //this.data = JSON.stringify(response.data);
            this.JsonOutgetlistaSubDireccionSRECI = response.data;
          }
        });
  } // FIN : FND-00007.1


  /*****************************************************
  * Funcion: FND-00007.1.1.2
  * Fecha: 15-09-2017
  * Descripcion: Carga la Lista de las Tipos de Funciona-
  * rios de la SRECI, para poder filtrar Usuarios
  * Objetivo: Obtener la lista de Tipos de Funcionario
  * SRECI
  * de la BD, Llamando a la API, por su metodo
  * ( tipo-funcionario-list ).
  ******************************************************/
  getlistaTipoFuncionariosSRECI() {
    //Llamar al metodo, de Login para Obtener la Identidad

    this._listasComunes.listasComunes( "","tipo-funcionario-list").subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetlistaTipoFuncionariosSRECI = response.data;
            alert(response.msg);
          }else{
            //this.data = JSON.stringify(response.data);
            this.JsonOutgetlistaTipoFuncionariosSRECI = response.data;
          }
        });
  } // FIN : FND-00007.1.1.2


  /*****************************************************
  * Funcion: FND-00007.1.1.3
  * Fecha: 15-09-2017
  * Descripcion: Carga la Lista de las Funcionaios de
  * la SRECI, para poder asignarles el Oficios a Ingresar
  * Objetivo: Obtener la lista de Funcionarios SRECI
  * de la BD, Llamando a la API, por su metodo
  * ( funcionarios-list ).
  * Params = paramsTipoFuncionario
  ******************************************************/
  getlistaUsuariosAsinadosSRECI() {
    //Llamar al metodo, de Login para Obtener la Identidad
    this.paramsTipoFuncionario.idTipoFuncionario = this.comunicacion.idTipoFuncionario;

    this._listasComunes.listasComunes( this.paramsTipoFuncionario ,"funcionarios-list").subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetlistaFuncionariosSRECI = response.data;
            alert(response.msg);
          }else{
            //this.data = JSON.stringify(response.data);
            this.JsonOutgetlistaFuncionariosSRECI = response.data;
          }
        });
  } // FIN : FND-00007.1.1.3


  /*****************************************************
  * Funcion: FND-00007.1.2
  * Fecha: 31-08-2017
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
  } // FIN : FND-00007.1.2


  /*****************************************************
  * Funcion: FND-00008
  * Fecha: 11-09-2017
  * Descripcion: Carga de los Oficios que se han ingresado
  * a la Tabla tbl_comunicacion_enc
  * Objetivo: Obtener la lista de los Oficios Ingresados
  * de la BD, Llamando a la API, por su metodo
  * (com-ingresada-list).
  ******************************************************/
  getlistaOficosIngresados() {
    //Llamar al metodo, de Login para Obtener la Identidad

    this._listasComunes.listasComunes( "", "com-ingresadas-list").subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetListaOficiosIngresados = response.data;
            this.countOficiosIngresados = "0";
            //alert(response.msg);
          }else{
            //this.data = JSON.stringify(response.data);
            this.JsonOutgetListaOficiosIngresados = response.data;
            this.countOficiosIngresados = this.JsonOutgetListaOficiosIngresados;
            //alert(this.countOficios);
          }
        });
  } // FIN : FND-00008


  /*****************************************************
  * Funcion: FND-00009
  * Fecha: 12-09-2017
  * Descripcion: Carga de los Oficios que se estan Pend.
  * a la Tabla tbl_comunicacion_enc
  * Objetivo: Obtener la lista de los Oficios Pendientes
  * de la BD, Llamando a la API, por su metodo
  * (com-pendientes-list).
  ******************************************************/
  getlistaOficosPendientes() {
    //Llamar al metodo, de Login para Obtener la Identidad

    this._listasComunes.listasComunes( "", "com-pendientes-list").subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetListaOficiosPendientes = response.data;
            this.countOficiosPendientes = "0";
            //alert(response.msg);
          }else{
            //this.data = JSON.stringify(response.data);
            this.JsonOutgetListaOficiosPendientes = response.data;
            this.countOficiosPendientes = this.JsonOutgetListaOficiosPendientes;
            //alert(this.countOficios);
          }
        });
  } // FIN : FND-00009


  /*****************************************************
  * Funcion: FND-00010
  * Fecha: 12-09-2017
  * Descripcion: Carga de los Oficios que se estan Final.
  * a la Tabla tbl_comunicacion_enc
  * Objetivo: Obtener la lista de los Oficios Finalizados
  * de la BD, Llamando a la API, por su metodo
  * (com-finalizados-list).
  ******************************************************/
  getlistaOficosFinalizados() {
    //Llamar al metodo, de Login para Obtener la Identidad

    this._listasComunes.listasComunes( "", "com-finalizados-list").subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetListaOficiosFinalizados = response.data;
            this.countOficiosFinalizados = "0";
            //alert(response.msg);
          }else{
            //this.data = JSON.stringify(response.data);
            this.JsonOutgetListaOficiosFinalizados = response.data;
            this.countOficiosFinalizados = this.JsonOutgetListaOficiosFinalizados;
            //alert(this.countOficios);
          }
        });
  } // FIN : FND-00010


  /*****************************************************
  * Funcion: FND-00011
  * Fecha: 18-10-2017
  * Descripcion: Creacion de nuevo File input
  * ( createNewFileInput ).
  ******************************************************/
  createNewFileInput(){
   // Actualiza el valor de la Secuencia
   let secActual = this.nextDocumento - 1;
   let newSecAct = this.codigoSec + "-"  + this.fechaHoy.getFullYear() +  "-" + this.fechaHoy.getMonth() + "-" + this.fechaHoy.getDate();


   this.JsonOutgetListaDocumentos.push({
     "nameDoc": newSecAct,
     "extDoc": this.extencionDocumento,
     "pesoDoc": this.seziDocumento
   });

   console.log(this.JsonOutgetListaDocumentos);

   $("#newTable").append('<tr> ' +
                      '   <th scope="row">'+ secActual +'</th> ' +
                      '   <td>' + newSecAct + '</td> ' +
                      '   <td>'+ this.extencionDocumento +'</td> ' +
                      '   <td>'+ this.seziDocumento +'</td> ' +
                      '   <td><a style="cursor: pointer" id="delDoc"> Borrar </a></td> ' +
                      ' </tr>');

  } // FIN | FND-00011
  

  /*****************************************************
  * Funcion: FND-00012
  * Fecha: 19-10-2017
  * Descripcion: Remove File input
  * ( removeFileInput ).
  ******************************************************/
  removeFileInput() {
    // var s = '#fileIn';
    // var $s = $(s).find('#contFile').remove().end();
      $("#delDoc").click( function(){
        // this.JsonOutgetListaDocumentos.splice(0, 1);
        alert('Ejecuto Fun');
          console.log( this.JsonOutgetListaDocumentos );
      });
  } // FIN | FND-00012


  /*****************************************************
  * Funcion: FND-00001
  * Fecha: 14-10-2017
  * Descripcion: Chekear todas las Opciones
  ******************************************************/
  checkTodos(){
    $('.form-control-file').each(function () {
        // if (this.checked) $(this).attr("checked", false);
        //else $(this).prop("checked", true);s
        let name = $('#pdfDocumento')[0].name;
        console.log('Pasa por : ' + name);
    });
  } // FIN | FND-00001


} // // FIN : export class IngresoComunicacionComponent

// Interface de Parametros | de Instituciones
export interface paramsInstituciones{
   idPais:string;
   idTipoInstitucion:string;
}

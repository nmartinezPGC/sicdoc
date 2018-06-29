import { Component, OnInit, NgZone, ChangeDetectorRef } from '@angular/core';
import { FormGroup, FormControl, Validators } from '@angular/forms';

import { RouterModule, Routes, ActivatedRoute, Router } from '@angular/router';

import { HttpModule,  Http, Response, Headers } from '@angular/http';

// Lirerias para el AutoComplete
import {Observable, Subscription, Subject} from 'rxjs';
import 'rxjs/add/operator/startWith';
import 'rxjs/add/operator/map';

// Libreria para Crear el PDF
import * as jsPDF from "jspdf";
import 'jspdf-autotable';

//Importamos los Servicios
import { LoginService } from '../../services/login/login.service'; //Servico del Login
import { IngresoComunicacionService } from '../../services/comunicaciones/ingreso.service'; //Servico del Comunicaciones
import { ListasComunesService } from '../../services/shared/listas.service'; //Servico Listas Comunes
import { VinculacionComunicacionService } from '../../services/comunicaciones/vinculacion.service'; //Servico Vinculacion de Comunicacion
import { UploadService } from '../../services/shared/upload.service'; //Servico Carga de Arhcivos
import { CaseSecuencesService } from '../../services/shared/caseSecuences.service'; //Servico caseSecuence

// Contact Service
import { ContactosService } from '../../services/contactos/contacto.service'; //Servico La Clase Contactos

import { AppComponent } from '../../app.component'; //Servico del Login

import { NgForm }    from '@angular/forms';

// Importamos la CLase Usuarios del Modelo
import { Usuarios } from '../../models/usuarios/usuarios.model'; // Servico del Login
import { Comunicaciones } from '../../models/comunicaciones/comunicacion.model'; // Modelo a Utilizar

// Importamos la CLase Usuarios del Modelo
import { Contactos } from '../../models/contactos/contacto.model'; // Servico del Login

// Libreria de AutoComplete
import { CompleterService, CompleterData, CompleterItem } from 'ng2-completer';
import { stringify } from '@angular/core/src/util';

//Libreria toasty
import {ToastyService, ToastyConfig, ToastyComponent, ToastOptions, ToastData} from 'ng2-toasty';

// Declaramos las variables para jQuery
declare var jQuery:any;
declare var $:any;

@Component({
  selector: 'app-ingreso-comunicacion',
  templateUrl: '../../views/comunicaciones/ingreso.component.html',
  styleUrls: ['../../views/comunicaciones/style.component.css'],
  providers: [ IngresoComunicacionService ,LoginService, ListasComunesService, UploadService,
              ContactosService, VinculacionComunicacionService, CaseSecuencesService]
})


export class IngresoComunicacionComponent implements OnInit{
  // Datos Generales de la Clase
  public titulo:string = "Ingreso de Comunicación";
  public fechaHoy:Date = new Date();
  public fechafin:string;

  public urlConfigLocal:string;
  public urlResourseLocal:string;
  public urlComplete:string;

  //variables del AtuoComplete

  private params;
  private paramsSecuencia;
  private paramsSecuenciaDet;
  private paramsSubDir;
  private paramsSubDirAcom;
  private paramsSubDirComVinculante;
  private paramsTipoFuncionario; // Parametros para el Filtro de Funcionario

  private paramsSecuenciaActividadAgregar;// Parametro para seleccionar el Secuencial

  private paramsDocumentosSend; // Parametros para los Documentos enviados

  private paramsIdTipoComSend; // Parametros para el tipo de COmunicacion enviados

  private paramsSecuenciaIn;

  // Propiedad de Loader
  public loading      = 'show';
  public alertSuccess = 'show';
  public alertError   = 'show';

  // AutoComplete
  protected searchStrFunc: string;
  protected dataServiceFunc: CompleterData;
  protected selectedFuncionario: string = "" ;
  protected selectedFuncionarioAll: string = "";
  protected selectedFuncionarioAllSend:any[] = [];

  // Propiedades de los Resumenes
  // Oficios
  public countOficiosIngresados;
  public countOficiosPendientes;
  public countOficiosFinalizados;

  // Memoramdums
  public countMemosIngresados;
  public countMemosPendientes;
  public countMemosFinalizados;

  // Correos
  public countCorreosIngresados;
  public countCorreosPendientes;
  public countCorreosFinalizados;

  // Llamadas
  public countLlamadasIngresados;
  public countLlamadasPendientes;
  public countLlamadasFinalizados;

  // FIN de Encabezados ****************************

  // Instacia de la variable del Modelo | Json de Parametros
  public user:Usuarios;
  public comunicacion: Comunicaciones;

  // Instacia del Objeto Model de la Clase
  public consultaContactos: Contactos;
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
  public JsonOutgetlistaSubDireccionSRECIComVinculantes:any[]; // Uso para las Comunicaciones viculantes | 2018-02-20
  public JsonOutgetlistaSubDireccionSRECIAcom:any[];

  public JsonOutgetlistaTipoFuncionariosSRECI:any[];
  public JsonOutgetlistaFuncionariosSRECI:any[];
  public JsonOutgetlistaFuncionariosDirectoresSRECI:any[];
  public JsonOutgetlistaTiposDocumentos:any[];

  // public JsonOutgetCodigoSecuenciaNew:any[];
  public JsonOutgetCodigoSecuenciaNew;
  // public JsonOutgetCodigoSecuenciaDet:any[];
  public JsonOutgetCodigoSecuenciaDet;

  // Json del Recuento de Datos
  // Oficios
  public JsonOutgetListaOficiosIngresados:any[];
  public JsonOutgetListaOficiosPendientes:any[];
  public JsonOutgetListaOficiosFinalizados:any[];

  // Memoramdums
  public JsonOutgetListaMemosIngresados:any[];
  public JsonOutgetListaMemosPendientes:any[];
  public JsonOutgetListaMemosFinalizados:any[];

  // Correos
  public JsonOutgetListaCorreosIngresados:any[];
  public JsonOutgetListaCorreosPendientes:any[];
  public JsonOutgetListaCorreosFinalizados:any[];

  // Llamadas
  public JsonOutgetListaLlamadasIngresados:any[];
  public JsonOutgetListaLlamadasPendientes:any[];
  public JsonOutgetListaLlamadasFinalizados:any[];

  // FIN de Encabezados **********************

  // Json de AutoCompleter Funcionarios
  public JsonOutgetlistaFuncionarios:any[];
  public JsonOutgetlistaSubDireccionesSrec:any[];
  public JsonOutgetlistaComunicacionVinculante:any[];  // Json para las Comunciacnon Vinculantes

  // Array de Documentos de Comunicacion
  public JsonOutgetListaDocumentos = [];
  // public JsonOutgetListaDocumentosDelete:any[];
  private JsonOutgetListaDocumentosDelete;


  // Variabls para validaciones de Seleccionado
  public maxlengthCodReferencia = "38"; // Defaul Correo
  public minlengthCodReferencia = "5"; // Defaul Correo
  // public pattern ="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$"; // Defaul Correo
  public pattern ="^[-\w.%+]{1,64}@(?:[A-Z0-9-]{1,63}\.){1,125}[A-Z]{2,63}$"; // Defaul Correo


  // Variables para la Persistencia de los Datos en los Documentos
  public nextDocumento:number = 1;
  public extencionDocumento:string;
  public seziDocumento:number;
  public nombreDoc:string;

  // Variables del Metodo
  public  error:string;
  // public  status:string;
  public  codigoSec:string;

  // Variables para ng-selecter2
  // Select de Sub Direcciones
  itemList = [];
  selectedItems = [];
  settings = {};

  // Select de Vinculacion de Comunicacion
  itemComunicacionVincList = [];
  selectedComunicacionVincItems = [];
  settingsComunicacionVinc = {};

  public paramsComVinculante; // Parametros para las Comunicacion Vinculantes
  public paramsTipoComunicacion = []; // Parametros para el Tipo de Comunicacion

  public JsonOutgetListaSubDireccionesAcomp = [];

  public idTipoComunicacionArray;

   // 218-02-12
  // Variable de Comunicacion Sin Seguimiento
  public comunicacionSinSeguimiento:number = 0;
  public comunicacionSinSeguimientoNew:number = 0;

  // Propiedades de Toasty
  getTitle(title:string, num: number): string {
        return title + ' se cerrara en ' +  num + ' segundos ';
  }

  getMessage(msg:string, num: number): string {
      // return msg + ' ' + num;
      return msg;
  }

   userForm: FormGroup;

  // Ini | Definicion del Constructor
  constructor( private _loginService: LoginService,
               private _listasComunes: ListasComunesService,
               private _uploadService: UploadService,
               private _caseSecuencesService: CaseSecuencesService,
               private _consultaContactoService: ContactosService,
               private _ingresoComunicacion: IngresoComunicacionService,
               private _router: Router,
               private _route: ActivatedRoute,
               private _appComponent: AppComponent,
               private _http: Http,
               private completerService: CompleterService,
               private changeDetectorRef: ChangeDetectorRef,
               private _vinculacionComunicacionService: VinculacionComunicacionService,
               private toastyService:ToastyService){
     // Llamado al Servicio de lista de Los Funcionarios SRECI
     this.getlistaFuncionariosSreci();

     // Configuracion del Select Dinamico
     this.settings = {
       singleSelection: false,
       text: "Selecciona las Direcciones acompañantes ... ",
       selectAllText: 'Selecciona Todos',
       unSelectAllText: 'Deselecciona Todos',
       searchPlaceholderText: 'Selecciona la Dirección que Acompaña el Tema',
       enableSearchFilter: true,
       badgeShowLimit: 7,
       maxHeight: 170,
       //limitSelection:6
     };

     // Configuracion del Select Dinamico
     this.settingsComunicacionVinc = {
      singleSelection: false,
      text: "Selecciona las Comunicaciones Vinculantes ... ",
      selectAllText: 'Selecciona Todas',
      enableCheckAll: false,
      unSelectAllText: 'Deselecciona Todas',
      searchPlaceholderText: 'Selecciona la Comunicación que relaciona el tema ...',
      enableSearchFilter: true,
      limitSelection:7,
      badgeShowLimit: 7,
      maxHeight: 170,
      //limitSelection:6
    };

     // Seteo de la Ruta de la Url Config
     this.urlConfigLocal = this._ingresoComunicacion.url;
     this.urlResourseLocal = this._ingresoComunicacion.urlResourses;
     this.urlComplete = this.urlResourseLocal + "uploads/correspondencia/";

  } // Fin | Definicion del Constructor

  /*****************************************************
  * Fecha: 15-10-2017
  * Funcion: generatePDF
  * Descripcion: Metodo que Genera el PDF, con la Data
  * ya Cargada con los criterios de seleccion
  ******************************************************/
  generatePDF() {
    //var imgData = 'data:image/jpeg;base64,'+ Base64.encode('Koala.jpeg');
    var img = new Image();
    img.src = 'assets/images/sreci.png';
    var doc = new jsPDF("p", "mm", "a4");
    // var doc = new jsPDF();

   doc.addImage(img, 'png', 130, 5, 70, 25 );
   //doc.addImage('assets/images/sreci.png', 'PNG', 15, 40, 200, 114);
   doc.text(10, 40, 'Oficio No. ' + this.comunicacion.codReferenciaSreci); // Numero de Comunicación
   doc.text(110, 50, 'Tegucigalpa M.D.C., ' + this.fechaHoy.getDay() +  ' de ' + this.fechaHoy.getMonth() + ' de ' + this.fechaHoy.getFullYear() );

   // Datos Generales
   doc.text(10, 60, 'Sr(a) ');
   doc.setFontType("bold");
   doc.text(10, 70, 'Nombre de Persona de Destino');
   doc.setFontType("normal");
   doc.text(10, 80, 'Cargo de Persona');
   doc.text(10, 90, 'Institucion');
   //doc.setFont("times");
   //doc.setFontType("italic");

   // Contenido de Comunicacion
   doc.text(10, 100, 'Tema de Comunicacion ' + this.comunicacion.temaCorrespondencia); //append email id in pdf
   // doc.setFontType("bold");
   doc.setTextColor(255, 0, 0); //set font color to red
   var lines = doc.splitTextToSize(this.comunicacion.descCorrespondencia, 220);
   doc.text(10, 110, lines); //append first name in pdf

   doc.text(100, 160, this.comunicacion.codReferenciaSreci); //append last name in pdf
   /*doc.addPage(); // add new page in pdf
   doc.setTextColor(165, 0, 0);
   doc.text(10, 20, 'extra page to write');*/
   // doc.save('Comunicación_' + this.comunicacion.codReferenciaSreci + '.pdf'); // Save the PDF with name "katara"...
   window.open(doc.output('bloburl'), '_blank');
  } // FIN | generatePDF


  // INI | Metodo OnInit
  ngOnInit(){

    // Hacemos que la variable del Local Storge este en la API
    this.identity = JSON.parse(localStorage.getItem('identity'));

    // Seteo del Json de Secuecnias Encabezados
    this.JsonOutgetCodigoSecuenciaNew = {
      "codSecuencial" : "",
      "valor2" : ""
    }

    // Seteo del Json de Secuecnias Encabezados
    this.JsonOutgetCodigoSecuenciaDet = {
      "codSecuencial" : "",
      "valor2" : ""
    }

    // Inicializamos los Parametros de Tipo Comunicacion
    this.paramsIdTipoComSend = {
      "idTipoCom" : "",
      "idFuncionarioAsignado" : "",
      "idTipoDoc" : "",
    }

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

    // Iniciamos los Parametros de Sub Direcciones Comunicacones Vinculantes
    this.paramsSubDirComVinculante = {
      "idDireccionSreciComVinc"  : ""
    };

    // Iniciamos los Parametros de Usuarios a Asignar el Oficio
    this.paramsTipoFuncionario = {
      "idTipoFuncionario"  : "",
      "idDeptoFuncional"  : ""
    };


    // Iniciamos los Parametros de Encabezado de Conunicacion
    this.paramsSecuenciaIn = {
      "codSecuencial"  : "",
      "tablaSecuencia"  : "",
      "idTipoDocumento"  : ""
    };

    this.paramsComVinculante = {
      "idDeptoFuncional"  : "",
      "idTipoDocumento"  : "",
      "idTipoComunicacion"  : ""
    };

    this.paramsTipoComunicacion = [];

    // Array de los Documentos enviados
    this.JsonOutgetListaDocumentos = [];

    // Json de Documento a Borrar
    this.JsonOutgetListaDocumentosDelete = {
      "codDocument": "",
      "extDocument": "",
      "indicadorExt":""
    }

    this.nextDocumento = 0;

    $("#newTable").children().remove();

    // Limpiamos el Textarea de los COntactos
    $("#contacAddCC").val();

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
    // this.listarCodigoCorrespondencia();
    //this.listarCodigoCorrespondenciaDet();

    // Tipo Funcionarios de la SRECI
    this.getlistaTipoFuncionariosSRECI();

    // Convertimos las Fechas a una Default
    this.convertirFecha();

    this.searchStrFunc = "";

    // Deselecciona la Opcion de Sin Seguimiento
    $("#estadoFin").val(2);

    $(".chkSinSeguimiento").attr("checked", false);


    // Definicion de la Insercion de los Datos de Nueva Comunicacion
    this.comunicacion = new Comunicaciones(1, "", "", "", "", "",
                                           0, "0", 0, 0, "7", 1, 0, 0, "0", "0",
                                           this.fechafin  , null,
                                           0, 0,  0, 0, 0,
                                           "", "", "", "", "", "", "", "",
                                           "", null, null, null );


    // Llamado a la Opcion de llenado de las Sub Direcciones
    this.getlistaSubDireccionesSreciAll();

    this.selectedItems = [];
    this.selectedComunicacionVincItems = [];
    this.itemList = [];
    this.itemComunicacionVincList = [];


    // Llenamos la Lsita de Sub Direcciones despues de los Campos Default
    this.getlistaSubDireccionesSRECI();

    // Limpiamos el Array de los Documentos
    this.comunicacion.pdfDocumento = "";

    this.JsonOutgetListaDocumentos = [];

    //Borra el Contenido del Arreglo de Contactos
    this.comunicacion.setTomail = "";

    // Llenamos la Lsita Funcionarios despues de los Campos Default
    // this.getlistaUsuariosAsinadosSRECI();

    // Resumenes de la Pantalla
    // Oficios
    this.getlistaOficosIngresados();
    this.getlistaOficosPendientes();
    this.getlistaOficosFinalizados();
    // Memos
    this.getlistaMemosPendientes();
    this.getlistaMemosFinalizados();

    // Correos
    this.getlistaCorreosPendientes();
    this.getlistaCorreosFinalizados();

    // Llamadas
    this.getlistaLlamadasPendientes();
    this.getlistaLlamadasFinalizados();

    // FIN de Ejecucuon de Encabezados

    // Eventos de Señaloizacion
    this.loading = "hide";

    $(".fakeRadio").attr('checked', false);

    // this.getlistaComunicacionVinculanteAll();
    // this.removeFileInput();
    // this.getlistaSubDireccionesSRECIAcom();

    // this.loadScript('../assets/js/ingreso.comunicacion.component.js');
  } // Fin | Metodo ngOnInit


  /******************************************************
   * Funiones de Seleccion en Nuevo Control del Listas
   * Metodologia: ng-selectec2
   * Fecha: 2018-01-15
   * Casos de uso: Lista de Instituciones, Paises, Tipo
   * de Institucion, Contactos
  *******************************************************/
  onItemSelect(item: any) {
    console.log(item);
    this.JsonOutgetListaSubDireccionesAcomp = this.selectedItems ;
    this.comunicacion.subDireccionesSreciAcom = this.JsonOutgetListaSubDireccionesAcomp;
    console.log( this.comunicacion.subDireccionesSreciAcom );
  }

  OnItemDeSelect(item: any) {
    console.log(item);
    this.JsonOutgetListaSubDireccionesAcomp = this.selectedItems ;
    this.comunicacion.subDireccionesSreciAcom = this.JsonOutgetListaSubDireccionesAcomp;
    console.log( this.comunicacion.subDireccionesSreciAcom );
  }

  onSelectAll(items: any) {
    console.log(items);
    this.JsonOutgetListaSubDireccionesAcomp = this.selectedItems ;
    this.comunicacion.subDireccionesSreciAcom = this.JsonOutgetListaSubDireccionesAcomp;
    console.log( this.comunicacion.subDireccionesSreciAcom );
  }

  onDeSelectAll(items: any) {
    console.log(items);
    this.JsonOutgetListaSubDireccionesAcomp = this.selectedItems ;
    this.comunicacion.subDireccionesSreciAcom = this.JsonOutgetListaSubDireccionesAcomp;
    console.log( this.comunicacion.subDireccionesSreciAcom );
  }


  /******************************************************
   * Funiones de Seleccion en Nuevo Control del Listas
   * Metodologia: ng-selectec2
   * Fecha: 2018-01-19
   * Casos de uso: Lista de las Comunicacones viculantes
  *******************************************************/
  onItemComVinculanteSelect(item: any) {
    console.log(item);
    this.JsonOutgetlistaComunicacionVinculante = this.selectedComunicacionVincItems ;
    this.comunicacion.comunicacionesVinculantes = this.JsonOutgetlistaComunicacionVinculante;
    console.log( this.comunicacion.comunicacionesVinculantes );
  }

  OnItemComVinculanteDeSelect(item: any) {
    console.log(item);
    this.JsonOutgetlistaComunicacionVinculante = this.selectedComunicacionVincItems ;
    this.comunicacion.comunicacionesVinculantes = this.JsonOutgetlistaComunicacionVinculante;
    console.log( this.comunicacion.comunicacionesVinculantes );
  }

  onSelectComVinculanteAll(items: any) {
    console.log(items);
    this.JsonOutgetlistaComunicacionVinculante = this.selectedComunicacionVincItems ;
    this.comunicacion.comunicacionesVinculantes = this.JsonOutgetlistaComunicacionVinculante;
    console.log( this.comunicacion.comunicacionesVinculantes );
  }

  onComVinculanteDeSelectAll(items: any) {
    console.log(items);
    this.JsonOutgetlistaComunicacionVinculante = this.selectedComunicacionVincItems ;
    this.comunicacion.comunicacionesVinculantes = this.JsonOutgetlistaComunicacionVinculante;
    console.log( this.comunicacion.comunicacionesVinculantes );
  }


  /*Funcion de Recarga de la Pagina*/
  refresh(): void {
    window.location.reload();
  }


  // Ini | Metodo onSubmit
  onSubmit(forma:NgForm){
      //this.validCampos();
      // this.listarCodigoCorrespondencia();
      // Parseo de parametros que no se seleccionan
      // Datos de la Secuencia Enabezado
      // this.codigoSecuencia    = this.JsonOutgetCodigoSecuenciaNew[0].codSecuencial;
      this.codigoSecuencia    = this.JsonOutgetCodigoSecuenciaNew.codSecuencial;
      // this.valorSecuencia     = this.JsonOutgetCodigoSecuenciaNew[0].valor2 + 1;
      this.valorSecuencia     = this.JsonOutgetCodigoSecuenciaNew.valor2 + 1;

      // Datos de la Secuencia Detalle
      // this.codigoSecuenciaDet = this.JsonOutgetCodigoSecuenciaDet[0].codSecuencial;
      this.codigoSecuenciaDet = this.JsonOutgetCodigoSecuenciaDet.codSecuencial;
      // this.valorSecuenciaDet  = this.JsonOutgetCodigoSecuenciaDet[0].valor2 + 1;
      this.valorSecuenciaDet  = this.JsonOutgetCodigoSecuenciaDet.valor2 + 1;

      // Secuenciales de la Tabla correspondencia Encabenzado
      // this.comunicacion.codCorrespondencia = this.codigoSecuencia + "-" + this.valorSecuencia;
      this.comunicacion.codCorrespondencia = this.codigoSecuencia;
      this.comunicacion.secuenciaComunicacionIn = this.valorSecuencia;

      // Secuenciales de la Tabla correspondencia detalle
      // this.comunicacion.codCorrespondenciaDet = this.codigoSecuenciaDet + "-" + this.valorSecuenciaDet;
      this.comunicacion.codCorrespondenciaDet = this.codigoSecuenciaDet;
      this.comunicacion.secuenciaComunicacionDet = this.valorSecuenciaDet;

      // Parametro para documento Seleccionado
      // Caso 1 ) Evaluamos si el Tipo de User no es Administrador ( 2, 3, 5 )
      if( this.identity.idTipoFunc != 4 && this.identity.idTipoFunc != 1 && this.identity.idTipoFunc != 6 ){
          // Evalua si se Activo la Comunicacion sin Seguimiento
          if( $('#estadoFin').val() == 1 ){ // Perfil Tipo Ingreso
            this.comunicacion.idEstado = "5";
            this.comunicacion.idDeptoFuncional = this.identity.idDeptoFuncional;
            this.comunicacion.idDireccionSreci = this.identity.idDireccion;
            this.comunicacion.idUsuarioAsaignado = this.identity.sub;
          }else {
            this.comunicacion.idEstado = "3";
            this.comunicacion.idDeptoFuncional = this.identity.idDeptoFuncional;
            this.comunicacion.idDireccionSreci = this.identity.idDireccion;
            this.comunicacion.idUsuarioAsaignado = this.identity.sub;
          }
      // Caso 2 ) Evaluamos si el Tipo de User es Administrador ( 1, 4 ) sin Asignacion a Director
      }else if( (this.identity.idTipoFunc == 1 || this.identity.idTipoFunc == 4 ) &&
                 this.comunicacion.idUsuarioAsaignado == 0 ){
          // Evalua si se Activo la Comunicacion sin Seguimiento
          if( $('#estadoFin').val() == 1 ){ // Perfil Tipo Ingreso
            this.comunicacion.idEstado = "5";
            this.comunicacion.idDeptoFuncional = this.identity.idDeptoFuncional;
            this.comunicacion.idDireccionSreci = this.identity.idDireccion;
            this.comunicacion.idUsuarioAsaignado = this.identity.sub;
          }else {
            this.comunicacion.idEstado = "3";
            this.comunicacion.idDeptoFuncional = this.identity.idDeptoFuncional;
            this.comunicacion.idDireccionSreci = this.identity.idDireccion;
            this.comunicacion.idUsuarioAsaignado = this.identity.sub;
          }
      // Caso 3 ) Evaluamos si el Tipo de User Administrador ( 1 ) con Asignacion a Director
      }else if( (this.identity.idTipoFunc == 1 || this.identity.idTipoFunc == 4 ) &&
                 this.comunicacion.idUsuarioAsaignado != 0 ){
          // Evalua si se Activo la Comunicacion sin Seguimiento
          if( $("#estadoFin").val() == 1 ){
            this.comunicacion.idEstado = "5";
          }else{
            this.comunicacion.idEstado = "7";
          }
      // Caso 4 ) Evaluamos si el Tipo de User Director ( 6 )
      }else if( this.identity.idTipoFunc == 6 ){
        // Evalua si se Activo la Comunicacion sin Seguimiento
        if( $('#estadoFin').val() == 1 ){ // Perfil Tipo Director
          this.comunicacion.idEstado = "5";
          this.comunicacion.idDeptoFuncional = this.identity.idDeptoFuncional;
          this.comunicacion.idDireccionSreci = this.identity.idDireccion;
          this.comunicacion.idUsuarioAsaignado = this.identity.sub;
        }else {
          this.comunicacion.idEstado = "7";
          this.comunicacion.idDeptoFuncional = this.identity.idDeptoFuncional;
          this.comunicacion.idDireccionSreci = this.identity.idDireccion;
          this.comunicacion.idUsuarioAsaignado = this.identity.sub;
        }
        console.log('Paso 4 Director');
      }

      console.log( this.comunicacion.subDireccionesSreciAcom );

      //Asignacion de Token de Sesion
      let token1 = this._ingresoComunicacion.getToken();
      this.loading = 'show';

      // Llamado al Procedo de Registro de Comunicacion
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
                this.addToast(4,"Error",this.mensajes);
            }else{
              // this.resetForm();
              this.loading = 'hidden';

              this.ngOnInit();
              //this.refresh();
              // this.alertShow();
              this.addToast(2,"Confirmado",this.mensajes);
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
              this.addToast(4,"Error: Contacta al Administrador ",this.mensajes);
              //alert("Error en la Petición !!" + this.errorMessage);
              //Oculta el Div de Alerta despues de 3 Segundos
              setTimeout(function() {
                  $("#alertError").fadeOut(1500);
              },3000);

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

    // Iniciamos los Parametros de Sub Direcciones Comunicacones Vinculantes
    this.paramsSubDirComVinculante = {
      "idDireccionSreciComVinc"  : ""
    };

    // Iniciamos los Parametros de Usuarios a Asignar Oficios
    this.paramsTipoFuncionario = {
      "idTipoFuncionario"  : "",
      "idDeptoFuncional"  : ""
    };

    this.status = "hide";
    this.loading = "hide";

    // Definicion de la Insercion de los Datos de Nueva Comunicacion
    this.comunicacion = new Comunicaciones(1, "", "", "", "", "",
                                           0, "0", 0, 0, "7", 1, 0, 0, "0", "0",
                                           this.fechafin  , null,
                                           0, 0,  0, 0, 0,
                                           "", "", "", "", "", "", "", "",
                                           "", null, null, null );

   // Limpiamos el Array de los Documentos
   this.comunicacion.pdfDocumento = "";

   this.JsonOutgetListaDocumentos = [];

   //Borra el Contenido del Arreglo de Contactos
   this.comunicacion.setTomail = "";

   this.JsonOutgetListaDocumentosDelete = {
     "codDocument": "",
     "extDocument": ""
   }

  } // FIN : FND-00001.1


  /****************************************************
  * Funcion: FND-00001.2
  * Fecha: 11-09-2017
  * Descripcion: Funcion que convierte las fechas a
  * String y le suma 5 dias
  * Objetivo: Sumar 5 dias a la fecha Maxima de entrega
  *****************************************************/
  convertirFecha() {
    //Funcion de Sumatoria de Fechas
    var d = new Date();
    let calendario = this.sumarDias(d, 5);
    let diaCal = String(calendario.getDate() );
    let mesCal = String(calendario.getMonth() + 1 );
    let anioCal = String(calendario.getFullYear() );

    if(diaCal.length < 2  ){
      //alert("Dia Falta el 0");
      diaCal = "0" + diaCal;
    }
    if(mesCal.length < 2){
      //alert("Mes Falta el 0");
      mesCal = "0" + mesCal;
    }

    //Retorna la fecha seteada
    this.fechafin = anioCal + "-" + mesCal + "-" + diaCal ;
    //this.fechafin = day + "-" + month + "-" + year;
    // console.log("Dia " + day + " Mes " + month + " Año " + year);
  } // FIN : FND-00001.2

  /* Función que suma o resta días a una fecha, si el parámetro
   días es negativo restará los días*/
  sumarDias(fecha, dias){
    fecha.setDate(fecha.getDate() + dias);
    return fecha;
  }

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
            this.addToast(4,"Error",this.mensajes);
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
          // successful so redirect to return url
          this.mensajes = response.msg;
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetlistaPaises = response.data;
            // alert(response.msg);
            this.addToast(4,"Error",this.mensajes);
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
          // successful so redirect to return url
          this.mensajes = response.msg;
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetlistaTipoInstitucion = response.data;
            // alert(response.msg);
            this.addToast(4,"Error",this.mensajes);
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
          // successful so redirect to return url
          this.mensajes = response.msg;
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetlistaInstitucion = response.data;
            // alert(response.msg);
            this.addToast(4,"Error",this.mensajes);
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
          // successful so redirect to return url
          this.mensajes = response.msg;
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetlistaTiposDocumentos = response.data;
            // alert(response.msg);
            this.addToast(4,"Error",this.mensajes);
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
     //Llamar al metodo, de Codigo de Secuencias para Obtener la Identidad
     this.paramsSecuenciaIn.idTipoDocumento = this.comunicacion.idTipoDocumento;
     this.paramsSecuenciaIn.idTipoComunicacion = 1;
     //alert(this.comunicacion.idTipoDocumento);
     //Generamos la Instancia para los datos por Defaul
     this.comunicacion.idPais = 0;
     this.comunicacion.idTipoInstitucion = 0;
     this.comunicacion.idInstitucion = 0;

     this.JsonOutgetlistaInstitucion = [];

     //Evaluamos el valor del Tipo de Documento
     if( this.paramsSecuenciaIn.idTipoDocumento == 1 ){
       /*this.paramsSecuencia.codSecuencial = "COM-IN-OFI";
       this.paramsSecuencia.tablaSecuencia = "tbl_comunicacion_enc";
       this.paramsSecuencia.idTipoDocumento = this.paramsSecuenciaIn.idTipoDocumento;*/
       this.comunicacion.codReferenciaSreci = "";
       // Disable codReferenciaSreci
       // Seteo de variable de validaciones | Oficio de Salida
       this.maxlengthCodReferencia = "30";
       this.minlengthCodReferencia = "5";
       this.pattern ="";

     } else if ( this.paramsSecuenciaIn.idTipoDocumento == 2 ) {
       /*this.paramsSecuencia.codSecuencial = "COM-IN-MEMO";
       this.paramsSecuencia.tablaSecuencia = "tbl_comunicacion_enc";
       this.paramsSecuencia.idTipoDocumento = this.paramsSecuenciaIn.idTipoDocumento;*/
       this.comunicacion.codReferenciaSreci = "";
       // Disable codReferenciaSreci
       // Seteo de variable de validaciones | Oficio de Salida
       this.maxlengthCodReferencia = "30";
       this.minlengthCodReferencia = "5";
       this.pattern ="";

       //Seteamos la Institcuion por Defecto sreci
       this.comunicacion.idPais = 1;
       this.comunicacion.idTipoInstitucion = 1;

       /* Carga el listado de la Instituciones de los Parametros **************/
       this.getlistaInstituciones();
       this.comunicacion.idInstitucion = 7;

     } else if ( this.paramsSecuenciaIn.idTipoDocumento == 3 ) {
       /*this.paramsSecuencia.codSecuencial = "COM-IN-NOTA-VERBAL";
       this.paramsSecuencia.tablaSecuencia = "tbl_comunicacion_enc";
       this.paramsSecuencia.idTipoDocumento = this.paramsSecuenciaIn.idTipoDocumento;*/
       this.comunicacion.codReferenciaSreci = "";
       // Disable codReferenciaSreci
      //  $( "#codReferenciaSreci" ).prop( "disabled", true );
       // Seteo de variable de validaciones | Oficio de Salida
       this.maxlengthCodReferencia = "30";
       this.minlengthCodReferencia = "5";
       this.pattern ="";

     } else if ( this.paramsSecuenciaIn.idTipoDocumento == 4 ) {
       /*this.paramsSecuencia.codSecuencial = "COM-IN-CIRCULAR";
       this.paramsSecuencia.tablaSecuencia = "tbl_comunicacion_enc";
       this.paramsSecuencia.idTipoDocumento = this.paramsSecuenciaIn.idTipoDocumento;*/
       this.comunicacion.codReferenciaSreci = "";
       // Disable codReferenciaSreci
       // Seteo de variable de validaciones | Oficio de Salida
       this.maxlengthCodReferencia = "30";
       this.minlengthCodReferencia = "5";
       this.pattern ="";

       //Seteamos la Institcuion por Defecto sreci
       this.comunicacion.idPais = 1;
       this.comunicacion.idTipoInstitucion = 1;

       /* Carga el listado de la Instituciones de los Parametros **************/
       this.getlistaInstituciones();
       this.comunicacion.idInstitucion = 7;

     } else if ( this.paramsSecuenciaIn.idTipoDocumento == 5 ) {
       /*this.paramsSecuencia.codSecuencial = "COM-IN-MAIL";
       this.paramsSecuencia.tablaSecuencia = "tbl_comunicacion_mail";
       this.paramsSecuencia.idTipoDocumento = this.paramsSecuenciaIn.idTipoDocumento;*/
       this.comunicacion.codReferenciaSreci = "";
       // Disable codReferenciaSreci
       $( "#codReferenciaSreci" ).prop( "disabled", false );
       // Seteo de variable de validaciones | Correo
       this.maxlengthCodReferencia = "38";
       this.minlengthCodReferencia = "10";
      //  this.pattern ="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$";
       this.pattern ="^[^@]+@[^@]+\.[a-zA-Z]{2,}$";

     } else if ( this.paramsSecuenciaIn.idTipoDocumento == 7 ){
       /*this.paramsSecuencia.codSecuencial = "COM-IN-CALL";
       this.paramsSecuencia.tablaSecuencia = "tbl_comunicacion_call";
       this.paramsSecuencia.idTipoDocumento = this.paramsSecuenciaIn.idTipoDocumento;*/
       this.comunicacion.codReferenciaSreci = "";
       // Disable codReferenciaSreci
       $( "#codReferenciaSreci" ).prop( "disabled", false );
       // Seteo de variable de validaciones | Llamada
       this.maxlengthCodReferencia = "8";
       this.minlengthCodReferencia = "8";
       this.pattern ="^([0-9])*$";

     } else if ( this.paramsSecuenciaIn.idTipoDocumento == 8 ) {
       /*this.paramsSecuencia.codSecuencial = "COM-IN-VERB";
       this.paramsSecuencia.tablaSecuencia = "tbl_comunicacion_verb";
       this.paramsSecuencia.idTipoDocumento = this.paramsSecuenciaIn.idTipoDocumento;*/
       this.comunicacion.codReferenciaSreci = "";
       // Disable codReferenciaSreci
       $( "#codReferenciaSreci" ).prop( "disabled", false );
       // Seteo de variable de validaciones | Llamada
       this.maxlengthCodReferencia = "38";
       this.minlengthCodReferencia = "15";
       this.pattern ="";

     } else if ( this.paramsSecuenciaIn.idTipoDocumento == 9 ) {
       /*this.paramsSecuencia.codSecuencial = "COM-IN-REUNION";
       this.paramsSecuencia.tablaSecuencia = "tbl_comunicacion_enc";
       this.paramsSecuencia.idTipoDocumento = this.paramsSecuenciaIn.idTipoDocumento;*/
       this.comunicacion.codReferenciaSreci = "";
       // Disable codReferenciaSreci
       $( "#codReferenciaSreci" ).prop( "disabled", false );
       // Seteo de variable de validaciones | Llamada
       this.maxlengthCodReferencia = "38";
       this.minlengthCodReferencia = "15";
       this.pattern ="";

     }else if ( this.paramsSecuenciaIn.idTipoDocumento == 10 ) {
       /*this.paramsSecuencia.codSecuencial = "COM-IN-EVENTO";
       this.paramsSecuencia.tablaSecuencia = "tbl_comunicacion_enc";
       this.paramsSecuencia.idTipoDocumento = this.paramsSecuenciaIn.idTipoDocumento;*/
       this.comunicacion.codReferenciaSreci = "";
       // Disable codReferenciaSreci
       $( "#codReferenciaSreci" ).prop( "disabled", false );
       // Seteo de variable de validaciones | Llamada
       this.maxlengthCodReferencia = "38";
       this.minlengthCodReferencia = "15";
       this.pattern ="";
     }// Fin de Condicion

     //Objetivo: Seleccionar la Secuencia
     let paramsSendIn =
               this._caseSecuencesService.caseSecuenceCab( this.paramsSecuenciaIn.idTipoComunicacion, this.paramsSecuenciaIn.idTipoDocumento );

     // Asignamos los valores a los parametros de Secuencia a Enviar
     this.paramsSecuencia.codSecuencial = paramsSendIn.codSecuencial;
     this.paramsSecuencia.tablaSecuencia = paramsSendIn.tablaSecuencia;
     this.paramsSecuencia.idTipoDocumento = paramsSendIn.idTipoDocumento;


    //Llamar al metodo, de Login para Obtener la Identidad
    this._listasComunes.listasComunesToken(this.paramsSecuencia, "gen-secuencia-comunicacion-in" ).subscribe(
        response => {
          // successful so redirect to return url
          this.mensajes = response.msg;
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetCodigoSecuenciaNew = response.data;
            alert(response.msg);
            this.addToast(4,"Error",this.mensajes);
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
     this.paramsSecuenciaDet.idTipoDocumento = this.comunicacion.idTipoDocumento;
     this.paramsSecuenciaDet.idTipoComunicacion = 1;

     //Evaluamos el valor del Tipo de Documento
    /* if( idTipoDocumentoIn == 1 ){
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
       this.paramsSecuenciaDet.tablaSecuencia = "tbl_comunicacion_det";
       this.paramsSecuenciaDet.idTipoDocumento = idTipoDocumentoIn;
     } else if ( idTipoDocumentoIn == 10 ) {
       this.paramsSecuenciaDet.codSecuencial = "COM-IN-DET-EVENTO";
       this.paramsSecuenciaDet.tablaSecuencia = "tbl_comunicacion_det";*/
       //this.paramsSecuenciaDet.idTipoDocumento = idTipoDocumentoIn;
    // } // Fin de Condicion

     //Objetivo: Seleccionar la Secuencia
     let paramsSendIn =
               this._caseSecuencesService.caseSecuence( this.paramsSecuenciaDet.idTipoComunicacion, this.paramsSecuenciaDet.idTipoDocumento );

     // Asignamos los valores a los parametros de Secuencia a Enviar
     this.paramsSecuenciaDet.codSecuencial = paramsSendIn.codSecuencial;
     this.paramsSecuenciaDet.tablaSecuencia = paramsSendIn.tablaSecuencia;
     this.paramsSecuenciaDet.idTipoDocumento = paramsSendIn.idTipoDocumento;

    //Llamar al metodo, de Login para Obtener la Identidad
    //console.log(this.params);
    this._listasComunes.listasComunesToken(this.paramsSecuenciaDet, "gen-secuencia-comunicacion-in" ).subscribe(
        response => {
          // successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetCodigoSecuenciaDet = response.data;
            alert(response.msg);
            this.addToast(4,"Error",response.msg);
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
    //console.log('Evento Change Lanzado'); , codDocumentoIn:string
    // Validamos que se ha llenado los datos Generales, para continuar

    // Setea el array de las Imagenes a Subir
    this.filesToUpload = <Array<File>>fileInput.target.files;

    // Direccion del Metodo de la API
    let url = this.urlConfigLocal + "/comunes/documentos-upload-options";
    // let url = "http://localhost/sicdoc/symfony/web/app_dev.php/comunes/documentos-upload-options";
    // let url = "http://172.17.0.250/sicdoc/symfony/web/app.php/comunes/documentos-upload-options";


    // Parametros de las Secuencias
    // this.codigoSecuencia = this.JsonOutgetCodigoSecuenciaNew[0].codSecuencial;
    this.codigoSecuencia = this.JsonOutgetCodigoSecuenciaNew.codSecuencial;
    // this.valorSecuencia  = this.JsonOutgetCodigoSecuenciaNew[0].valor2 + 1;
    this.valorSecuencia  = this.JsonOutgetCodigoSecuenciaNew.valor2 + 1;
    this.codigoSec = this.codigoSecuencia + "-" + this.valorSecuencia;

    this.codigoSec = this.codigoSec + '-' + this.nextDocumento;
    this.nextDocumento = this.nextDocumento + 1;

    // Tamaño
    let sizeByte:number = this.filesToUpload[0].size;
    let siezekiloByte:number =  Math.round( sizeByte / 1024 );

    this.seziDocumento = ( siezekiloByte / 1024 );

    // Datos del Nuevo Documento
    let type = this.filesToUpload[0].type;
    let nameDoc = this.filesToUpload[0].name;

    this.nombreDoc = nameDoc;

    var filename = $("#pdfDocumento").val();

    // Use a regular expression to trim everything before final dot
    this.extencionDocumento = filename.replace(/^.*\./, '');

    //Modificacion; Cuando la extencion es PDF => pdf
      if( this.extencionDocumento == "PDF" ){
        this.extencionDocumento = "pdf";
      }else if( this.extencionDocumento == "jpg" ) {
        this.extencionDocumento = "jpeg";
      }


     let sendParms = "json=" + "";

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
            // alert(this.resultUpload.msg);
            this.addToast(4,"Error",this.resultUpload.msg);
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
            this.addToast(4,"Error",response.msg);
          }else{
            this.JsonOutgetCodigoSecuenciaNew = response.data;

            // Generamos el Secuencial del Detalle
            this.listarCodigoCorrespondenciaDet();
            console.log(response.data);
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
            // alert(response.msg);
            this.addToast(4,"Error", response.msg);
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
            // alert(response.msg);
            this.addToast(4,"Error", response.msg);
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
            // alert(response.msg);
            this.addToast(4,"Error", response.msg);
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
            this.addToast(4,"Error", response.msg);
          }else{
            //this.data = JSON.stringify(response.data);
            this.JsonOutgetlistaSubDireccionSRECI = response.data;
          }
        });
  } // FIN : FND-00007.1



  /*****************************************************
  * Funcion: FND-00007.1.1
  * Fecha: 20-02-2018
  * Descripcion: Carga la Lista de las Sub Direcciones de
  * SRECI
  * Objetivo: Obtener la lista de las Direcciones SRECI
  * de la BD, Llamando a la API, por su metodo
  * ( subdir-sreci-list ).
  ******************************************************/
  getlistaSubDireccionesSRECIComVinculante() {
    //Llamar al metodo, de Login para Obtener la Identidad
    this.paramsSubDirComVinculante.idDireccionSreciComVinc = this.comunicacion.idDireccionSreciComVinc;

    console.log(this.paramsSubDirComVinculante);

    this._listasComunes.listasComunes( this.paramsSubDirComVinculante,"com-vinculantes-subdir-sreci-list").subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetlistaSubDireccionSRECIComVinculantes = response.data;
            // alert(response.msg);
            this.addToast(4,"Error", response.msg);
          }else{
            //this.data = JSON.stringify(response.data);
            this.JsonOutgetlistaSubDireccionSRECIComVinculantes = response.data;
          }
        });
  } // FIN : FND-00007.1.1


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
            // alert(response.msg);
            this.addToast(4,"Error", response.msg);
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
    // this.paramsTipoFuncionario.idTipoFuncionario = this.comunicacion.idDeptoFuncional;

    this._listasComunes.listasComunes( this.paramsTipoFuncionario ,"funcionarios-list").subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetlistaFuncionariosSRECI = response.data;
            // alert(response.msg);
            this.addToast(4,"Error", response.msg);
          }else{
            //this.data = JSON.stringify(response.data);
            this.JsonOutgetlistaFuncionariosSRECI = response.data;
            this.getlistaUsuariosDirectorSRECI();
            // console.log(this.JsonOutgetlistaFuncionariosSRECI);
          }
        });
  } // FIN : FND-00007.1.1.3


  /*****************************************************
  * Funcion: FND-00007.1.1.4
  * Fecha: 18-01-2018
  * Descripcion: Carga la Lista de las Funcionaios de
  * la SRECI, Tipo Director para poder asignarles el Oficios
  * a Ingresar.
  * Objetivo: Obtener la lista de Funcionarios SRECI
  * de la BD, Llamando a la API, por su metodo
  * ( funcionarios-list-director ).
  * Params = paramsIdDeptoFuncional
  ******************************************************/
  getlistaUsuariosDirectorSRECI() {
    //Llamar al metodo, de listasComunes para Obtener la Identidad
    this.paramsTipoFuncionario.idDeptoFuncional = this.comunicacion.idDeptoFuncional;

    this._listasComunes.listasComunes( this.paramsTipoFuncionario ,"funcionarios-list-director").subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetlistaFuncionariosDirectoresSRECI = response.data;
            // alert(response.msg);
            this.addToast(4,"Error", response.msg);
          }else{
            //this.data = JSON.stringify(response.data);
            this.JsonOutgetlistaFuncionariosDirectoresSRECI = response.data;
            console.log(this.JsonOutgetlistaFuncionariosDirectoresSRECI);
            this.comunicacion.idDeptoFuncional = this.JsonOutgetlistaFuncionariosDirectoresSRECI[0].idDeptoFuncional.idDeptoFuncional;
            this.comunicacion.idDireccionSreci = this.JsonOutgetlistaFuncionariosDirectoresSRECI[0].idDeptoFuncional.idDireccionSreci.idDireccionSreci;
            this.comunicacion.idUsuarioAsaignado = this.JsonOutgetlistaFuncionariosDirectoresSRECI[0].idFuncionario;
            // this.comunicacion.idFuncionario = JsonOutgetlistaFuncionariosDirectoresSRECI[0].idFuncionario;
          }
        });
  } // FIN : FND-00007.1.1.4


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
            // alert(response.msg);
            this.addToast(4,"Error", response.msg);
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
    //Llamar al metodo, de Contador de Comunicaciones Pendientes
    this.paramsIdTipoComSend.idTipoCom = 1;
    this.paramsIdTipoComSend.idFuncionarioAsignado = this.identity.idFuncionario;
    this.paramsIdTipoComSend.idTipoDoc = 1;

    this._listasComunes.listasComunes( this.paramsIdTipoComSend, "com-ingresadas-list").subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetListaOficiosIngresados = response.data;
            this.countOficiosIngresados = "0";
            //alert(response.msg);
            // this.addToast(4,"Error", response.msg);
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
    //Llamar al metodo, de Contador de Comunicaciones Pendientes
    this.paramsIdTipoComSend.idTipoCom = 1;
    this.paramsIdTipoComSend.idFuncionarioAsignado = this.identity.idFuncionario;
    this.paramsIdTipoComSend.idTipoDoc = 1;

    this._listasComunes.listasComunes( this.paramsIdTipoComSend, "com-pendientes-list").subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetListaOficiosPendientes = response.data;
            this.countOficiosPendientes = "0";
            //alert(response.msg);
            // this.addToast(4,"Error", response.msg);
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
    //Llamar al metodo, de Contador de Comunicaciones Pendientes
    this.paramsIdTipoComSend.idTipoCom = 1;
    this.paramsIdTipoComSend.idFuncionarioAsignado = this.identity.idFuncionario;
    this.paramsIdTipoComSend.idTipoDoc = 1;


    this._listasComunes.listasComunes( this.paramsIdTipoComSend, "com-finalizados-list").subscribe(
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
  * Fecha: 08-11-2017
  * Descripcion: Carga de los Memos que se estan Final.
  * a la Tabla tbl_comunicacion_enc
  * Objetivo: Obtener la lista de los Oficios Memoramdums
  * de la BD, Llamando a la API, por su metodo
  * (com-finalizados-list).
  ******************************************************/
  getlistaMemosFinalizados() {
    //Llamar al metodo, de Contador de Comunicaciones Pendientes
    this.paramsIdTipoComSend.idTipoCom = 1;
    this.paramsIdTipoComSend.idFuncionarioAsignado = this.identity.idFuncionario;
    this.paramsIdTipoComSend.idTipoDoc = 2;

    this._listasComunes.listasComunes( this.paramsIdTipoComSend, "com-finalizados-list").subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetListaMemosFinalizados = response.data;
            this.countMemosFinalizados = "0";
            //alert(response.msg);
          }else{
            //this.data = JSON.stringify(response.data);
            this.JsonOutgetListaMemosFinalizados = response.data;
            this.countMemosFinalizados = this.JsonOutgetListaMemosFinalizados;
            //alert(this.countOficios);
          }
        });
  } // FIN : FND-00011


  /*****************************************************
  * Funcion: FND-00011.1
  * Fecha: 17-01-2018
  * Descripcion: Limpia el Arreglo de Contactos
  * ( cleanContact ).
  ******************************************************/
  cleanContact(){
    //Borra el Contenido del Arreglo de Contactos
    this.comunicacion.setTomail = "";
  } // FIN : FND-00011.1


  /*****************************************************
  * Funcion: FND-00011.2
  * Fecha: 20-01-2018
  * Descripcion: Limpia el Arreglo de Contactos
  * ( cleanComunicacionVinculante ).
  ******************************************************/
  cleanComunicacionVinculante(){
    //Borra el Contenido del Arreglo de Comunicacones Vinculante
    this.paramsComVinculante = {
      "idDeptoFuncional"  : "",
      "idTipoDocumento"  : "",
      "idTipoComunicacion"  : ""
    };

    // Limpia los radio Buttons que este Chequedo
    $(".fakeRadio").attr('checked', false);

    // Inicializa el itemList de las Comunicaciones Viculantes
    this.itemComunicacionVincList = [];

    this.comunicacion.idTipoDocumentoComVinc = "0";
    this.comunicacion.idDeptoFuncionalComVinc = 0;
    this.comunicacion.idDireccionSreciComVinc = 0;
  } // FIN : FND-00011.2



  /*****************************************************
  * Funcion: FND-00012
  * Fecha: 08-11-2017
  * Descripcion: Carga de los Memos que se estan Pend.
  * a la Tabla tbl_comunicacion_enc
  * Objetivo: Obtener la lista de los Oficios Memoramdums
  * de la BD, Llamando a la API, por su metodo
  * (com-pendientes-list).
  ******************************************************/
  getlistaMemosPendientes() {
    //Llamar al metodo, de Contador de Comunicaciones Pendientes
    this.paramsIdTipoComSend.idTipoCom = 1;
    this.paramsIdTipoComSend.idFuncionarioAsignado = this.identity.idFuncionario;
    this.paramsIdTipoComSend.idTipoDoc = 2;

    this._listasComunes.listasComunes( this.paramsIdTipoComSend, "com-pendientes-list").subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetListaMemosPendientes = response.data;
            this.countMemosPendientes = "0";
            //alert(response.msg);
          }else{
            //this.data = JSON.stringify(response.data);
            this.JsonOutgetListaMemosPendientes = response.data;
            this.countMemosPendientes = this.JsonOutgetListaMemosPendientes;
            //alert(this.countOficios);
          }
        });
  } // FIN : FND-00012


  /*****************************************************
  * Funcion: FND-00013
  * Fecha: 08-11-2017
  * Descripcion: Carga de los Correos que se estan Final.
  * a la Tabla tbl_comunicacion_enc
  * Objetivo: Obtener la lista de los Oficios Correos
  * de la BD, Llamando a la API, por su metodo
  * (com-finalizados-list).
  ******************************************************/
  getlistaCorreosFinalizados() {
    //Llamar al metodo, de Contador de Comunicaciones Pendientes
    this.paramsIdTipoComSend.idTipoCom = 1;
    this.paramsIdTipoComSend.idFuncionarioAsignado = this.identity.idFuncionario;
    this.paramsIdTipoComSend.idTipoDoc = 5;

    this._listasComunes.listasComunes( this.paramsIdTipoComSend, "com-finalizados-list").subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetListaCorreosFinalizados = response.data;
            this.countCorreosFinalizados = "0";
            //alert(response.msg);
          }else{
            //this.data = JSON.stringify(response.data);
            this.JsonOutgetListaCorreosFinalizados = response.data;
            this.countCorreosFinalizados = this.JsonOutgetListaCorreosFinalizados;
            //alert(this.countOficios);
          }
        });
  } // FIN : FND-00011


  /*****************************************************
  * Funcion: FND-00014
  * Fecha: 08-11-2017
  * Descripcion: Carga de los Correos que se estan Pend.
  * a la Tabla tbl_comunicacion_enc
  * Objetivo: Obtener la lista de los Oficios Correos
  * de la BD, Llamando a la API, por su metodo
  * (com-pendientes-list).
  ******************************************************/
  getlistaCorreosPendientes() {
    //Llamar al metodo, de Contador de Comunicaciones Pendientes
    this.paramsIdTipoComSend.idTipoCom = 1;
    this.paramsIdTipoComSend.idFuncionarioAsignado = this.identity.idFuncionario;
    this.paramsIdTipoComSend.idTipoDoc = 5;

    this._listasComunes.listasComunes( this.paramsIdTipoComSend, "com-pendientes-list").subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetListaCorreosPendientes = response.data;
            this.countCorreosPendientes = "0";
            //alert(response.msg);
          }else{
            //this.data = JSON.stringify(response.data);
            this.JsonOutgetListaCorreosPendientes = response.data;
            this.countCorreosPendientes = this.JsonOutgetListaCorreosPendientes;
            //alert(this.countOficios);
          }
        });
  } // FIN : FND-00014


  /*****************************************************
  * Funcion: FND-00015
  * Fecha: 08-11-2017
  * Descripcion: Carga de los Llamadas que se estan Final.
  * a la Tabla tbl_comunicacion_enc
  * Objetivo: Obtener la lista de los Oficios Llamadas
  * de la BD, Llamando a la API, por su metodo
  * (com-finalizados-list).
  ******************************************************/
  getlistaLlamadasFinalizados() {
    //Llamar al metodo, de Contador de Comunicaciones Pendientes
    this.paramsIdTipoComSend.idTipoCom = 1;
    this.paramsIdTipoComSend.idFuncionarioAsignado = this.identity.idFuncionario;
    this.paramsIdTipoComSend.idTipoDoc = 7;

    this._listasComunes.listasComunes( this.paramsIdTipoComSend, "com-finalizados-list").subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetListaLlamadasFinalizados = response.data;
            this.countLlamadasFinalizados = "0";
            //alert(response.msg);
          }else{
            //this.data = JSON.stringify(response.data);
            this.JsonOutgetListaLlamadasFinalizados = response.data;
            this.countLlamadasFinalizados = this.JsonOutgetListaLlamadasFinalizados;
            //alert(this.countOficios);
          }
        });
  } // FIN : FND-00011


  /*****************************************************
  * Funcion: FND-00016
  * Fecha: 08-11-2017
  * Descripcion: Carga de los Correos que se estan Pend.
  * a la Tabla tbl_comunicacion_enc
  * Objetivo: Obtener la lista de los Oficios Llamadas
  * de la BD, Llamando a la API, por su metodo
  * (com-pendientes-list).
  ******************************************************/
  getlistaLlamadasPendientes() {
    //Llamar al metodo, de Contador de Comunicaciones Pendientes
    this.paramsIdTipoComSend.idTipoCom = 1;
    this.paramsIdTipoComSend.idFuncionarioAsignado = this.identity.idFuncionario;
    this.paramsIdTipoComSend.idTipoDoc = 7;

    this._listasComunes.listasComunes( this.paramsIdTipoComSend, "com-pendientes-list").subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetListaLlamadasPendientes = response.data;
            this.countLlamadasPendientes = "0";
            //alert(response.msg);
          }else{
            //this.data = JSON.stringify(response.data);
            this.JsonOutgetListaLlamadasPendientes = response.data;
            this.countLlamadasPendientes = this.JsonOutgetListaLlamadasPendientes;
            //alert(this.countOficios);
          }
        });
  } // FIN : FND-00016


  /*****************************************************
  * Funcion: FND-00017
  * Fecha: 18-10-2017
  * Descripcion: Creacion de nuevo File input
  * ( createNewFileInput ).
  ******************************************************/
  createNewFileInput(){
   // Actualiza el valor de la Secuencia
   let secActual = this.nextDocumento - 1;
   let mesAct = this.fechaHoy.getMonth() + 1;

   // Mes Actual
   let final_month = mesAct.toString();
   if( mesAct <= 9 ){
     final_month = "0" + final_month;
   }

   // Dia del Mes
   let day = this.fechaHoy.getDate(); // Dia
   let final_day = day.toString();
   if( day <= 9 ){
     final_day = "0" + final_day;
   }

   let newSecAct = this.codigoSec + "-"  + this.fechaHoy.getFullYear() +  "-" + final_month + "-" + final_day;


   // Agrega Items al Json
   this.JsonOutgetListaDocumentos.push({
     "nameDoc": newSecAct,
     "extDoc": this.extencionDocumento,
     "pesoDoc": this.seziDocumento,
     "nombreDoc" : this.nombreDoc
   });


   // seteo del Parametro Json de los Documentos
   this.comunicacion.pdfDocumento = this.JsonOutgetListaDocumentos;

   // 2018-02-15
   // Se comentta debido a que ahora se hace con Angular
   /*$("#newTable").append('<tr> ' +
                      '   <th scope="row">'+ secActual +'</th> ' +
                      '   <td>' + newSecAct + '</td> ' +
                      '   <td>'+ this.extencionDocumento +'</td> ' +
                      '   <td>'+ this.seziDocumento +'</td> ' +
                      '   <td><a style="cursor: pointer" class="delDoc"> Borrar </a></td> ' +
                      ' </tr>');*/

   console.log(this.JsonOutgetListaDocumentos);

  } // FIN | FND-00017



  /*****************************************************
  * Funcion: FND-00003.1
  * Fecha: 12-10-2017
  * Descripcion: Funcion para AutoCompletar y sacar el
  * Id de la Data de la Tabla TblFunionarios
  * Params: $event
  ******************************************************/
  protected onSelectedFunc( item: CompleterItem ) {
    // Validar si hay datos Previos

    if( this.comunicacion.setTomail == '' ){
      this.selectedFuncionarioAll = '';
    }

    if( this.selectedFuncionarioAll == '' ){
      // alert( this.selectedFuncionarioAll );
      this.selectedFuncionario = item? item.originalObject.emailFuncionario : "";
      this.selectedFuncionarioAll = this.selectedFuncionario;
    }else {
      this.selectedFuncionario = this.selectedFuncionario + ',' + item? item.originalObject.emailFuncionario : "";
      this.selectedFuncionarioAll = this.selectedFuncionarioAll + ',' + this.selectedFuncionario;
    }

     this.comunicacion.setTomail = this.selectedFuncionarioAll;
  } // FIN | FND-00003.1


  /*****************************************************
  * Funcion: FND-00001.2
  * Fecha: 12-10-2017
  * Descripcion: Carga la Lista de Todos los Funcionarios
  * Objetivo: Obtener la lista de los Funcionarios de la
  * de la BD, Llamando a la API, por su metodo
  * ( listas/funcionarios-list-all ).
  ******************************************************/
  getlistaFuncionariosSreci() {
    // Llamamos al Servicio que provee todas las Instituciones
    this._listasComunes.listasComunes("","funcionarios-list-all").subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetlistaFuncionarios = response.data;
            // alert(response.msg);
            this.addToast(4,"Error", response.msg);
          }else{
            this.JsonOutgetlistaFuncionarios = response.data;
            // console.log(response.data);
            // Cargamos el compoenete de AutoCompletar
            this.dataServiceFunc = this.completerService.local(this.JsonOutgetlistaFuncionarios, 'nombre1Funcionario,apellido1Funcionario',
                  'nombre1Funcionario,apellido1Funcionario,apellido2Funcionario,telefonoFuncionario,emailFuncionario');

            // console.log(this.JsonOutgetlistaFuncionarios);
          }
        });
  } // FIN : FND-00001.2


  /*****************************************************
  * Funcion: FND-00001.2.1
  * Fecha: 12-02-2018
  * Descripcion: Carga la Lista de Todas Sub Direcciones
  * Objetivo: Obtener la lista de Todas Sub Direcciones
  * de la BD, Llamando a la API, por su metodo
  * ( sub-direcciones-sreci-list ).
  ******************************************************/
  getlistaSubDireccionesSreciAll() {
    // Llamamos al Servicio que provee todas las Instituciones
    // Llamamos al Servicio que provee todas las Instituciones
    let direcconSreci = 0;

    if( this.comunicacion.idDireccionSreciAcom != null || this.comunicacion.idDireccionSreciAcom != 0 ){
        direcconSreci = this.comunicacion.idDireccionSreciAcom;
    }else {
      direcconSreci = 0;
    }

    this._listasComunes.listasComunes("","sub-direcciones-sreci-list?idDireccionSreci=" + direcconSreci).subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetlistaSubDireccionesSrec = response.data;
            this.itemList = [];
            // alert(response.msg);
            this.addToast(4,"Error", response.msg);
          }else{
            this.JsonOutgetlistaSubDireccionesSrec = response.data;

            this.itemList = this.JsonOutgetlistaSubDireccionesSrec;
            console.log( this.itemList  );
          }
        });
  } // FIN : FND-00001.2.1


  /*****************************************************
  * Funcion: FND-000017
  * Fecha: 12-02-2018
  * Descripcion: Chekear Comunicacion sin Seguimiento
  ******************************************************/
  checkSinSinSeguimiento(){
    $('.chkSinSeguimiento').each(function () {
        if (this.checked) {
          //alert('Activado ' + this.comunicacionSinSeguimientoNew );
          $("#estadoFin").val(1);
        } else{
          // alert('Activado ' + this.comunicacionSinSeguimientoNew );
          $("#estadoFin").val(2);
        }
    });
  } // FIN | FND-000017



  /*****************************************************
  * Funcion: FND-00018
  * Fecha: 15-02-2018
  * Descripcion: Delete de nuevo File input, en Tabla
  * ( deleteRowHomeForm ).
  ******************************************************/
  deleteRowHomeForm(homeFormIndex: number, codDocumentoIn:string, extDocumentoIn:string){
    // Borra el Elemento al Json
    this.JsonOutgetListaDocumentos.splice(homeFormIndex,1);
    this.changeDetectorRef.detectChanges();
    this.comunicacion.pdfDocumento = "";

    // Ejecutamos la Fucnion que Borra el Archivo desde le Servidor
    this.borrarDocumentoServer(codDocumentoIn, extDocumentoIn);
    console.log(this.JsonOutgetListaDocumentos);
  }


  /*****************************************************
  * Funcion: FND-00019
  * Fecha: 15-02-2018
  * Descripcion: Metodo para Borrar Documento desde el
  * Servidor
  * metodo ( borrar-documento-server ).
  ******************************************************/
  borrarDocumentoServer(codDocumentoIn:string, extDocumentoIn:string) {
    //Llamar al metodo, de Login para Obtener la Identidad
    // Agrega Items al Json
    this.JsonOutgetListaDocumentosDelete.codDocument =  codDocumentoIn;
    this.JsonOutgetListaDocumentosDelete.indicadorExt = 1;

    // Cambiamos la Extencion si es jpg
    if( extDocumentoIn == "jpg" ){
      extDocumentoIn = "jpeg";
    }
    this.JsonOutgetListaDocumentosDelete.extDocument = extDocumentoIn;

    this._uploadService.borrarDocumento( this.JsonOutgetListaDocumentosDelete ).subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            //this.JsonOutgetlistaEstados = response.data;
            // alert(response.msg);
            this.addToast(4,"Error", response.msg);
          }
        });
  } // FIN : FND-00019



  /********************************************************
  * Funcion: FND-0000020
  * Fecha: 16-02-2018
  * Descripcion: Carga la Lista de Todas las Comunicación
  * que tiene el Departamento Funcional del Usuario
  * Objetivo: Obtener la lista de Todas las Comunicaciones
  * de la BD, Llamando a la API, por su metodo
  * Params: idDeptoFuncional, idTipoDocumento, idTipoComunicacion
  * ( vinculacionComunicacion/vinculacion-de-comunicacion ).
  **********************************************************/
  getlistaComunicacionVinculanteAll(idOpcion:number) {
    // Llamamos al Servicio que provee todas las Comunicaciones por DeptoFuncional
    // Condicionamos la Busqueda
    if( idOpcion == 1 ){
      this.paramsComVinculante.idDeptoFuncional = this.comunicacion.idDeptoFuncionalComVinc;
      this.paramsComVinculante.idTipoDocumento  = this.comunicacion.idTipoDocumentoComVinc;
      this.paramsComVinculante.idTipoComunicacion = [1];
      //console.log('Caso #1 Ingreso');
    }else if ( idOpcion == 2 ){
      this.paramsComVinculante.idDeptoFuncional = this.comunicacion.idDeptoFuncionalComVinc;
      this.paramsComVinculante.idTipoDocumento  = this.comunicacion.idTipoDocumentoComVinc;
      this.paramsComVinculante.idTipoComunicacion = [2];
      //console.log('Caso #2 Salidas');
    }else if ( idOpcion == 3 ){
      this.paramsComVinculante.idDeptoFuncional = this.comunicacion.idDeptoFuncionalComVinc;
      this.paramsComVinculante.idTipoDocumento  = this.comunicacion.idTipoDocumentoComVinc;
      this.paramsComVinculante.idTipoComunicacion = [1,2];
      //console.log('Caso #3 Ambas');
    }

    //console.log(this.paramsComVinculante);
    this._vinculacionComunicacionService.listaComunicacionVinculantes( this.paramsComVinculante ).subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetlistaComunicacionVinculante = response.data;
            this.itemComunicacionVincList = [];
            // alert(response.msg);
            this.addToast(4,"Error", response.msg);
          }else{
            this.JsonOutgetlistaComunicacionVinculante = response.data;

            this.itemComunicacionVincList = this.JsonOutgetlistaComunicacionVinculante;
            //console.log( this.JsonOutgetlistaComunicacionVinculante );
          }
        });
  } // FIN : FND-0000020


  /********************************************************
  * Funcion: FND-0000021
  * Fecha: 22-02-2018
  * Descripcion: Evalua Si la Comunicacion es de Tipo: Interna
  * Memo, Circular
  * Objetivo: Evalua Comunicaciones Internas
  * de la BD, Llamando a la API, por su metodo
  * Params: idTipoDocumento
  * ( vinculacionComunicacion/vinculacion-de-comunicacion ).*/
  comunicacionInterna(){
  }

  /*****************************************************
  * Funcion: FND-000023
  * Fecha: 31-03-2018
  * Descripcion: Libreria Toasty para los mensajes
  * Objetivo: Metodo de msg en la APP
  ******************************************************/
  addToast(options:number,title:string, msg:string) {
      let interval = 1000;
      let timeoutIn = 11000;
      let seconds = timeoutIn / 1000;
      let subscription: Subscription;

       let toastOptions: ToastOptions = {
           title: this.getTitle(title,0),
           msg: this.getMessage(msg,0),
           showClose: true,
           timeout: 7000,
           theme: 'bootstrap',
           onAdd: (toast: ToastData) => {
               console.log('Toast ' + toast.id + ' has been added!');
               // Run the timer with 1 second iterval
               let observable = Observable.interval(interval);
               // Start listen seconds beat
               subscription = observable.subscribe((count: number) => {
                   // Update title of toast
                   toast.title = this.getTitle(title, ( seconds - count - 1 ));
                   // Update message of toast
                   toast.msg = this.getMessage(msg, count);
                   // Extra condition to hide Toast after 10 sec
                   if (count > 10) {
                       // We use toast id to identify and hide it
                       this.toastyService.clear(toast.id);
                   }
               });

           },
           onRemove: function(toast: ToastData) {
               console.log('Toast ' + toast.id + ' has been removed!');
               // Stop listenning
               subscription.unsubscribe();
           }
       };

       switch ( options ) {
           case 0: this.toastyService.default(toastOptions); break; //default
           case 1: this.toastyService.info(toastOptions); break; //info
           case 2: this.toastyService.success(toastOptions); break; //success
           case 3: this.toastyService.wait(toastOptions); break; //wait
           case 4: this.toastyService.error(toastOptions); break; //error
           case 5: this.toastyService.warning(toastOptions); break; //warning
       }
   } //FIN | FND-000023


} // // FIN : export class IngresoComunicacionComponent

// Interface de Parametros | de Instituciones
export interface paramsInstituciones{
   idPais:string;
   idTipoInstitucion:string;
}

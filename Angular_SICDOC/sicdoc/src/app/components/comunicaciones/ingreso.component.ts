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

// Declaramos las variables para jQuery
declare var jQuery:any;
declare var $:any;

@Component({
  selector: 'app-ingreso-comunicacion',
  templateUrl: '../../views/comunicaciones/ingreso.component.html',
  styleUrls: ['../../views/comunicaciones/style.component.css'],
  providers: [ IngresoComunicacionService ,LoginService, ListasComunesService, UploadService,
              ContactosService]
})


export class IngresoComunicacionComponent implements OnInit{

  public titulo:string = "Ingreso de Comunicación";
  public fechaHoy:Date = new Date();
  public fechafin:string;

  //variables del AtuoComplete


  private params;
  private paramsSecuencia;
  private paramsSecuenciaDet;
  private paramsSubDir;
  private paramsSubDirAcom;
  private paramsTipoFuncionario; // Parametros para el Filtro de Funcionario

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
  public JsonOutgetlistaSubDireccionSRECIAcom:any[];

  public JsonOutgetlistaTipoFuncionariosSRECI:any[];
  public JsonOutgetlistaFuncionariosSRECI:any[];
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

  // Array de Documentos de Comunicacion
  public JsonOutgetListaDocumentos = [];


  // Variabls para validaciones de Seleccionado
  public maxlengthCodReferencia = "38"; // Defaul Correo
  public minlengthCodReferencia = "5"; // Defaul Correo
  // public pattern ="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$"; // Defaul Correo
  public pattern ="^[-\w.%+]{1,64}@(?:[A-Z0-9-]{1,63}\.){1,125}[A-Z]{2,63}$"; // Defaul Correo


  // Variables para la Persistencia de los Datos en los Documentos
  public nextDocumento:number = 1;
  public extencionDocumento:string;
  public seziDocumento:number;

  // Variables del Metodo
  public  error:string;
  // public  status:string;
  public  codigoSec:string;

  itemList = [];
   // selectedItems = [];
   selectedItems = [];
   settings = {};

   userForm: FormGroup;

  // Ini | Definicion del Constructor
  constructor( private _loginService: LoginService,
               private _listasComunes: ListasComunesService,
               private _uploadService: UploadService,
               private _consultaContactoService: ContactosService,
               private _ingresoComunicacion: IngresoComunicacionService,
               private _router: Router,
               private _route: ActivatedRoute,
               private _appComponent: AppComponent,
               private _http: Http,
               private completerService: CompleterService){
     // Llamado al Servicio de lista de Los Funcionarios SRECI
     this.getlistaFuncionariosSreci();

  } // Fin | Definicion del Constructor


  // INI | Metodo OnInit
  ngOnInit(){
    //ini prueba
    this.itemList = [
      { "id": 1, "itemName": "India" },
      { "id": 2, "itemName": "Singapore" },
      { "id": 3, "itemName": "Australia" },
      { "id": 4, "itemName": "Canada" },
      { "id": 5, "itemName": "South Korea" },
      { "id": 6, "itemName": "Brazil" }
    ];

    this.selectedItems = [
      { "id": 1, "itemName": "India" },
      { "id": 2, "itemName": "Singapore" },
      { "id": 3, "itemName": "Australia" },
      { "id": 4, "itemName": "Canada" }];
    this.settings = {
      singleSelection: false,
      text: "Select Countries",
      selectAllText: 'Select All',
      unSelectAllText: 'UnSelect All',
      enableSearchFilter: true,
      badgeShowLimit: 3
    };


    ///FIN prueba

    //Llamamos el evento de Borrar ña Fila Seleccionada
    // $(document).on('click', '.delDoc', function (event) {
    //   event.preventDefault();
    //     var valores = $(this).parents("tr").find("td")[0].innerHTML;
    //     $(this).closest('tr').remove();
    //
    //     alert('Hola ' + valores);
    // });

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

    // Array de los Documentos enviados
    this.JsonOutgetListaDocumentos = [];

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


    // Definicion de la Insercion de los Datos de Nueva Comunicacion
    this.comunicacion = new Comunicaciones(1, "", "", "", "", "",
                                           0, "0", 0, 0, "7", 1, 0, "0",
                                           this.fechafin , null,
                                           0, 0,  0, 0,
                                           "", "", "", "", "", "", "", "",
                                           "", null);

    // Llenamos la Lsita de Sub Direcciones despues de los Campos Default
    this.getlistaSubDireccionesSRECI();


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

    // this.removeFileInput();
    // this.getlistaSubDireccionesSRECIAcom();
    // this.loadScript('../assets/js/ingreso.comunicacion.component.js');
  } // Fin | Metodo ngOnInit


  onItemSelect(item: any) {
    console.log(item);
    console.log(this.selectedItems);
  }
  OnItemDeSelect(item: any) {
    console.log(item);
    console.log(this.selectedItems);
  }
  onSelectAll(items: any) {
    console.log(items);
  }
  onDeSelectAll(items: any) {
    console.log(items);
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
            }else{
              //this.resetForm();
              this.loading = 'hidden';
              this.ngOnInit();
              // this.alertShow();
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

    // Iniciamos los Parametros de Usuarios a Asignar Oficios
    this.paramsTipoFuncionario = {
      "idTipoFuncionario"  : ""
    };

    this.status = "hide";
    this.loading = "hide";

    this.comunicacion = new Comunicaciones(1, "", "",  "", "", "",  0, "0", 0, 0 ,"7", 1, 0,
                                          "0",  "", "",  0, 0,  0, 0,  "","","","",  "", "",
                                          "", "", "", null);

   // Limpiamos el Array de los Documentos
   this.comunicacion.pdfDocumento = "";

   this.JsonOutgetListaDocumentos = [];

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
      //  this.pattern ="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$";
       this.pattern ="^[^@]+@[^@]+\.[a-zA-Z]{2,}$";

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
       this.paramsSecuencia.tablaSecuencia = "tbl_comunicacion_enc";
       this.paramsSecuencia.idTipoDocumento = this.paramsSecuenciaIn.idTipoDocumento;
       this.comunicacion.codReferenciaSreci = "";
       // Disable codReferenciaSreci
       $( "#codReferenciaSreci" ).prop( "disabled", false );
       // Seteo de variable de validaciones | Llamada
       this.maxlengthCodReferencia = "38";
       this.minlengthCodReferencia = "15";
       this.pattern ="";

     }else if ( this.paramsSecuenciaIn.idTipoDocumento == 10 ) {
       this.paramsSecuencia.codSecuencial = "COM-IN-EVENTO";
       this.paramsSecuencia.tablaSecuencia = "tbl_comunicacion_enc";
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
       this.paramsSecuenciaDet.tablaSecuencia = "tbl_comunicacion_det";
       this.paramsSecuenciaDet.idTipoDocumento = idTipoDocumentoIn;
     } else if ( idTipoDocumentoIn == 10 ) {
       this.paramsSecuenciaDet.codSecuencial = "COM-IN-DET-EVENTO";
       this.paramsSecuenciaDet.tablaSecuencia = "tbl_comunicacion_det";
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
    // Validamos que se ha llenado los datos Generales, para continuar
    // this.validDatosGen();

    // Setea el array de las Imagenes a Subir
    this.filesToUpload = <Array<File>>fileInput.target.files;

    // Direccion del Metodo de la API
    // let url = "http://localhost/sicdoc/symfony/web/app_dev.php/comunes/documentos-upload-options";
    let url = "http://172.17.4.162/sicdoc/symfony/web/app.php/comunes/documentos-upload-options";
    // let url = "http://172.17.3.141/sicdoc/symfony/web/app.php/comunes/upload-documento";
    // let url = "http://192.168.0.23/sicdoc/symfony/web/app.php/comunes/upload-documento";


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

    this.seziDocumento = siezekiloByte;

    let type = this.filesToUpload[0].type;

    var filename = $("#pdfDocumento").val();

    // Use a regular expression to trim everything before final dot
    this.extencionDocumento = filename.replace(/^.*\./, '');

    //Modificacion; Cuando la extencion es PDF => pdf
      if( this.extencionDocumento == "PDF" ){
        //alert(this.extencionDocumento);
        this.extencionDocumento = "pdf"
      }

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
            // console.log(this.JsonOutgetlistaFuncionariosSRECI);
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


   this.JsonOutgetListaDocumentos.push({
     "nameDoc": newSecAct,
     "extDoc": this.extencionDocumento,
     "pesoDoc": this.seziDocumento
   });


   this.comunicacion.pdfDocumento = this.JsonOutgetListaDocumentos;

   $("#newTable").append('<tr> ' +
                      '   <th scope="row">'+ secActual +'</th> ' +
                      '   <td>' + newSecAct + '</td> ' +
                      '   <td>'+ this.extencionDocumento +'</td> ' +
                      '   <td>'+ this.seziDocumento +'</td> ' +
                      // '   <td><a style="cursor: pointer" class="delDoc"> Borrar </a></td> ' +
                      ' </tr>');

   console.log(this.JsonOutgetListaDocumentos);

  } // FIN | FND-00017

  /*****************************************************
  * Funcion: FND-00017
  * Fecha: 18-10-2017
  * Descripcion: Creacion de nuevo File input
  * ( createNewFileInput ).
  ******************************************************/
  deleteItemTable(){
    $(document).on('click', '.delDoc', function (event) {
      event.preventDefault();
      $(this).closest('tr').remove();
    });
  }

  /*****************************************************
  * Funcion: FND-00018
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
  } // FIN | FND-00018


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


  /*****************************************************
  * Funcion: FND-00001
  * Fecha: 14-10-2017
  * Descripcion: Chekear todas las Opciones
  ******************************************************/
  validDatosGen(){
    if( this.comunicacion.codReferenciaSreci == null ){
         alert('Debes llenar los datos generales, para poder seleccionar los documentos.');
         return;
    }
  }


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
            alert(response.msg);

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


} // // FIN : export class IngresoComunicacionComponent

// Interface de Parametros | de Instituciones
export interface paramsInstituciones{
   idPais:string;
   idTipoInstitucion:string;
}

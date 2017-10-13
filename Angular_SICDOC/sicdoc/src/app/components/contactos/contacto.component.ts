import { Component, OnInit, Inject } from '@angular/core';

import { FormGroup, FormControl, Validators } from '@angular/forms';

import { RouterModule, Routes, ActivatedRoute, Router } from '@angular/router';

import { HttpModule,  Http, Response, Headers, RequestOptions } from '@angular/http';

// Lirerias para el AutoComplete
import {Observable} from 'rxjs/Observable';
import 'rxjs/add/operator/startWith';
import 'rxjs/add/operator/map';

//Importamos los Servicios
import { ListasComunesService } from '../../services/shared/listas.service'; //Servico Listas Comunes
import { UploadService } from '../../services/shared/upload.service'; //Servico Carga de Arhcivos
import { ContactosService } from '../../services/contactos/contacto.service'; //Servico La Clase Contactos

import { AppComponent } from '../../app.component'; //Servico del Login

import { NgForm }    from '@angular/forms';

// Importamos la CLase Usuarios del Modelo
import { Contactos } from '../../models/contactos/contacto.model'; // Servico del Login

import { CompleterService, CompleterData, CompleterItem } from 'ng2-completer';

import 'rxjs/Rx';

// Declaramos las variables para jQuery
declare var jQuery:any;
declare var $:any;

@Component({
  selector: 'app-contactos',
  templateUrl: '../../views/contactos/contactos.component.html',
  styleUrls: ['../../views/contactos/contactos.component.css'],
  providers: [ ContactosComponent , ContactosService , ListasComunesService, UploadService]
})
export class ContactosComponent implements OnInit {
  public titulo = "Contactos";
  public newTittle = "Ingreso de Contacto";

  protected searchStr: string;
  protected searchStrFunc: string;
  protected captain: string;
  protected dataService: CompleterData;
  protected dataServiceFunc: CompleterData;


  protected selectedInstitucion: string;
  protected selectedFuncionario: string;
  // variables del localStorage
  public identity;
  public token;
  public userId;

  // Variables de Captura de msg
  public status;
  public mensajes;
  public errorMessage;

  // Instacia del Objeto Model de la Clase
  public consultaContactos: Contactos;

  // parametros multimedia
  public loading  = 'show';
  public loading_table  = 'hide';
  public loading_tr  = 'hide';
  public loading_tableIn = 'hide';

  public loadTabla1:boolean = false;
  public loadTabla2:boolean = false;

  // Parametros de los Json de la Aplicacion
  public JsonOutgetlistaContactosDet:any[];
  public JsonOutgetlistaContactosEnc:any[];
  public JsonOutgetlistaInstitucion:any[];
  public JsonOutgetlistaFuncionarios:any[];

  public JsonLimpio;


  // Ini | Definicion del Constructor
  constructor( private _listasComunes: ListasComunesService,
               private _router: Router,
               private _consultaContactoService: ContactosService,
               private _uploadService: UploadService,
               private _route: ActivatedRoute,
               private _appComponent: AppComponent,
               private _http: Http,
               private completerService: CompleterService ) {
    // Llenado de la Tabla de Encabezado
    this.fillDataTable();

    // this.dataService = completerService.local(this.searchData, 'colors', 'colors');
    // this.dataService = completerService.local(this.JsonOutgetlistaContactosEnc, 'idContacto', 'idContacto');
    // console.log(this.JsonOutgetlistaContactosEnc);
    // console.log(this.dataService);
  } // Fin | Definicion del Constructor


  /*****************************************************
  * Funcion: ngOnInit
  * Fecha: 11-10-2017
  * Descripcion: Metodo inicial del Programa
  ******************************************************/
  ngOnInit() {
    // Hacemos que la variable del Local Storge este en la API
    //this.identity = JSON.parse(localStorage.getItem('identity'));
    //this.userId = this.identity.sub;
    this.searchStr = "";
    this.searchStrFunc = "";

    // Definicion de la Insercion de los Datos de Nuevo Contacto
    this.consultaContactos = new Contactos ( 0, null, null, null, null, null,
                                             null, null,  0, 0,
                                             null, null,  0, 0, null, null);

    // Ejecucion de la Lista de Instituciones
    this.getlistaInstituciones();

    // Ejecucion de la Lista Funcionarios
    this.getlistaFuncionariosSreci();

    // Ejecucion de la Lista de Contactos
    this.getlistaContactosTableFind();
  } // Fin | ngOnInit


  /*****************************************************
  * Funcion: onSubmit
  * Fecha: 11-10-2017
  * Descripcion: Metodo que envia la Informacion
  ******************************************************/
  public filesToUpload: Array<File>;
  public resultUpload;

  onSubmit(forma:NgForm){
      // Parseo de parametros que no se seleccionan
      // this.filesToUpload = <Array<File>>this.consultaContactos.imgDocumento.target.files;
      // Parametro para documento Seleccionado
      this.loading_table = 'show';
      console.log( this.consultaContactos );
      this._consultaContactoService.newContact( this.consultaContactos, "", "").subscribe(
        response => {
            // Obtenemos el Status de la Peticion
            this.status = response.status;
            this.mensajes = response.msg;

            // Condicionamos la Respuesta
            alert('Paso 1 ' + this.status);
            if(this.status != "success"){
                this.status = "error";
                this.mensajes = response.msg;
                if(this.loading_table = 'show'){
                  this.loading_table = 'hidden';
                }

                alert('Error Data ' +  this.mensajes);
            }else{
              //this.resetForm();
              this.loading = 'hidden';
              // this.ngOnInit();
              // Llenado de la Tabla de Encabezado
              this.fillDataTable();
              
              this.loading_table = 'hide';
              // alert('Send Data ' +  this.mensajes);
              setTimeout(function() {
                $('#t_and_c_m').modal('hide');
              }, 600);
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
  * Fecha: 11-10-2017
  * Descripcion: Carga la Lista de los Contactos de la
  * BD que han sido ingresados
  * Objetivo: Obtener la lista de los Contactos de la BD,
  * Llamando a la API, por su metodo
  * ( contactos/contactos-consulta ).
  * Params: Modelo de la Clase ( Modelo de la Clase )
  ******************************************************/
  getlistaContactosTableFind() {
    // Laoding
    this.loading = 'show';
    this.loadTabla1 = false;
    // console.log( this.consultaContactos );
    // Llamar al metodo, de Service para Obtener los Datos de los Contactos
    this._consultaContactoService.contactoFindAll( this.consultaContactos ).subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetlistaContactosEnc = response.data;

            alert(response.msg);
          }else{
            this.JsonOutgetlistaContactosEnc = response.data;
            //this.valoresdataDetJson ( response.data );
            this.JsonLimpio = this.JsonOutgetlistaContactosEnc;
            this.loading = 'hidden';
            this.loadTabla1 = true;

            // console.log(this.JsonOutgetlistaInstitucion); // Lista de Instituciones
            // console.log(this.JsonOutgetlistaFuncionarios); // Lista de Funcionarios
            // console.log(this.JsonOutgetlistaContactosEnc); // Tabla de Contactos
            this.dataService = this.completerService.local(this.JsonOutgetlistaInstitucion, 'descInstitucion,perfilInstitucion', 'perfilInstitucion');
            this.dataServiceFunc = this.completerService.local(this.JsonOutgetlistaFuncionarios, 'nombre1Funcionario,apellido1Funcionario',
                  'nombre1Funcionario,apellido1Funcionario,apellido2Funcionario,telefonoFuncionario,emailFuncionario');
            console.log(this.dataServiceFunc);
          }
        });
  } // FIN | FND-00001


  /*****************************************************
  * Funcion: FND-00001.1
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
            //console.log(response.data);
          }
        });
  } // FIN : FND-00001.1


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
            //console.log(response.data);
          }
        });
  } // FIN : FND-00001.2


  /*****************************************************
  * Funcion: FND-00002
  * Fecha: 06-10-2017
  * Descripcion: Realiza el llenado de la Tabla con Todos
  * los Filtros
  * Params: none
  ******************************************************/
  fillDataTable(){
    setTimeout(function () {
      $ (function () {
          $('#example').DataTable();
          this.loading_tableIn = 'show';
      });
    }, 8000);
    this.loading_tableIn = 'hide';
  } // FIN | FND-00002


  /*****************************************************
  * Funcion: FND-00003
  * Fecha: 11-10-2017
  * Descripcion: Funcion para AutoCompletar y sacar el
  * Id de la Data de la Tabla tblInstituciones
  * Params: $event
  ******************************************************/
  protected onSelected( item: CompleterItem ) {
    this.selectedInstitucion = item? item.originalObject.idInstitucion : "";
    // Seteamos y Parseamos a Int el idInstitucion
    this.consultaContactos.idInstitucion = parseInt(this.selectedInstitucion);
  } // FIN | FND-00003


  /*****************************************************
  * Funcion: FND-00003.1
  * Fecha: 12-10-2017
  * Descripcion: Funcion para AutoCompletar y sacar el
  * Id de la Data de la Tabla TblFunionarios
  * Params: $event
  ******************************************************/
  protected onSelectedFunc( item: CompleterItem ) {
    this.selectedFuncionario = item? item.originalObject.idFuncionario : "";
    // Seteamos y Parseamos a Int el idContactoSreci
    this.consultaContactos.idContactoSreci = parseInt(this.selectedFuncionario);
  } // FIN | FND-00003.1


  /*****************************************************
  * Funcion: FND-00004
  * Fecha: 29-07-2017
  * Descripcion: Carga la Imagen de usuario desde el File
  * Objetivo: Obtener la imagen que se carga desde el
  * control File de HTML
  * (fileChangeEvent).
  ******************************************************/
  // public filesToUpload: Array<File>;
  // public resultUpload;

  fileChangeEvent(fileInput: any){
    //console.log('Evento Chge Lanzado'); , codDocumentoIn:string
    this.filesToUpload = <Array<File>>fileInput.target.files;

    // Direccion del Metodo de la API
    let url = "http://localhost/sicdoc/symfony/web/app_dev.php/contactos/contacto-upload-perfil";
    // let url = "http://172.17.3.90/sicdoc/symfony/web/app.php/comunes/upload-documento";
    // let url = "http://192.168.0.15/sicdoc/symfony/web/app.php/comunes/upload-documento";

    // Variables del Metodo
    let  error:string;
    let  status:string;
    let  codigoSec:string;

    // Seteamos el valore del Nombre del Documento
    codigoSec = this.consultaContactos.nombre1Contacto + ' ' + this.consultaContactos.apellido1Contacto;


    // Ejecutamos el Servicio con los Parametros
    this._uploadService.makeFileRequestNoToken( url, [ 'name_pdf', codigoSec ], this.filesToUpload ).then(
        ( result ) => {
          this.resultUpload = result;
          status = this.resultUpload.status;
          console.log(this.resultUpload);
          if(status === "error"){
            console.log(this.resultUpload);
            alert(this.resultUpload.msg);
          }
          this.consultaContactos.pdfDocumento = this.resultUpload.data;
          // this.mensajes = this.resultUpload.msg;
        },
        ( error ) => {
          alert(error);
          console.log(error);
        });
  } // FIN : FND-00004


} // FIN | Clase

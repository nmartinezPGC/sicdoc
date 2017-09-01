import { Component, OnInit } from '@angular/core';
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


//Importamos los Javascript
//import '../../views/login/register.component';

@Component({
  selector: 'app-ingreso-comunicacion',
  templateUrl: '../../views/comunicaciones/ingreso.component.html',
  styleUrls: ['../../views/comunicaciones/style.component.css'],
  providers: [ IngresoComunicacionService ,LoginService, ListasComunesService, UploadService]
})

export class IngresoComunicacionComponent implements OnInit{
  public titulo:string = "Ingreso de Comunicación";
  public fechaHoy:Date = new Date();
  private params;

  // Instacia de la variable del Modelo
  public user:Usuarios;
  public comunicacion: Comunicaciones;

  // Objeto que Controlara la Forma
  forma:FormGroup;

  public data;
  public errorMessage;
  public status;
  public mensajes;

  public identity;
  public token;
  // public passwordConfirmation:string;

  // Variables de Generacion de las Listas de los Dropdow
  // Llenamos las Lista del HTML
  public JsonOutgetlistaEstados:any[];
  public JsonOutgetlistaTipoUsuario:any[];
  public JsonOutgetlistaPaises:any[];
  public JsonOutgetlistaTipoInstitucion:any[];
  public JsonOutgetlistaInstitucion:any[];
  public JsonOutgetlistaDireccionSRECI:any[];


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
    // Iniciamos los Parametros de Instituciones
    this.params = {
      "idPais"  : "",
      "idTipoInstitucion"  : ""
    };
    // Inicializacion de las Listas
    this.getlistaEstadosComunicacion();
    this.getlistaPaises();
    this.getlistaTipoInstituciones();
    this.getlistaDireccionesSRECI();
    // this.getlistaInstituciones();

    // Definicion de la Insercion de los Datos de Nuevo Usuario
    this.comunicacion = new Comunicaciones(1, "", "", "", "",  0, 0, 0, 0,  this.fechaHoy, this.fechaHoy,  0, 0,  0);
    //this.loadScript('../assets/js/register.component.js');
  } // Fin | Metodo ngOnInit


  // Ini | Metodo onSubmit
  onSubmit(forma:NgForm){
      console.log(this.comunicacion);
      // parseInt(this.user.idTipoUsuario);ssss
      this._ingresoComunicacion.registerComunicacion(this.comunicacion).subscribe(
        response => {
            // Obtenemos el Status de la Peticion
            this.status = response.status;
            this.mensajes = response.msg;

            // Condicionamos la Respuesta
            if(this.status != "success"){
                this.status = "error";
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
            alert("Msg Error");
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
            alert("Msg Error");
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
            alert("Msg Error");
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
  private parametros:paramsInstituciones[] = [{"idPais":"1", "idTipoInstitucion":"1" }];

  getlistaInstituciones() {
    this.params.idPais = this.comunicacion.idPais;
    this.params.idTipoInstitucion = this.comunicacion.idTipoInstitucion;
    //Llamar al metodo, de Login para Obtener la Identidad
    console.log(this.params);

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
    //console.log('Evento Chge Lanzado');
    this.filesToUpload = <Array<File>>fileInput.target.files;

    let token = this._loginService.getToken();
    let url = "http://localhost/sicdoc/symfony/web/app_dev.php/comu/upload-image-user";

    this._uploadService.makeFileRequest( token, url, ['image'], this.filesToUpload ).then(
        ( result ) => {
          this.resultUpload = result;
          console.log(this.resultUpload);
        },
        ( error ) => {
          alert("error");
          console.log(error);
        });
  } // FIN : FND-00005


  /*****************************************************
  * Funcion: FND-00006
  * Fecha: 31-08-2017
  * Descripcion: Carga la Imagen de usuario desde el File
  * Objetivo: Obtener la imagen que se carga desde el
  * control File de HTML
  * (fileChangeEvent).
  ******************************************************/

  /*generarCodigoCorrespondencia(){
    //console.log('Evento Chge Lanzado');
    this.filesToUpload = <Array<File>>fileInput.target.files;

    let token = this._loginService.getToken();
    let url = "http://localhost/sicdoc/symfony/web/app_dev.php/comu/upload-image-user";

    this._uploadService.makeFileRequest( token, url, ['image'], this.filesToUpload ).then(
        ( result ) => {
          this.resultUpload = result;
          console.log(this.resultUpload);
        },
        ( error ) => {
          alert("error");
          console.log(error);
        });
  } // FIN : FND-00006*/


  /*****************************************************
  * Funcion: FND-00007
  * Fecha: 31-08-2017
  * Descripcion: Carga la Lista de las Direcciones de
  * SRECI
  * Objetivo: Obtener la lista de las Direcciones SRECI
  * de la BD, Llamando a la API, por su metodo
  * (depto-funcional-list).
  ******************************************************/
  getlistaDireccionesSRECI() {
    //Llamar al metodo, de Login para Obtener la Identidad
    this._listasComunes.listasComunes("","depto-funcional-list").subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            alert("Msg Error");
            alert(response.msg);
          }else{
            //this.data = JSON.stringify(response.data);
            this.JsonOutgetlistaDireccionSRECI = response.data;
          }
        });
  } // FIN : FND-00007


} // // FIN : export class IngresoComunicacionComponent

// Interface de Parametros | de Instituciones
export interface paramsInstituciones{
   idPais:string;
   idTipoInstitucion:string;
}

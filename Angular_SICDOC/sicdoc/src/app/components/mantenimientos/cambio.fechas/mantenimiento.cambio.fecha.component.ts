import { Component, OnInit } from '@angular/core';
import { RouterModule, Routes, ActivatedRoute, Router } from '@angular/router';

import { HttpModule,  Http, Response, Headers } from '@angular/http';

//Importamos los Servicios
import { LoginService } from '../../../services/login/login.service'; //Servico del Login
import { ListasComunesService } from '../../../services/shared/listas.service'; //Servico Listas Comunes
import { UploadService } from '../../../services/shared/upload.service'; //Servico Listas Comunes


import { AppComponent } from '../../../app.component'; //Servico del Login

import { NgForm }    from '@angular/forms';

import { FormGroup, FormControl, Validators }    from '@angular/forms';

// Importamos la CLase Intituciones del Modelo
import { Instituciones } from '../../../models/mantenimientos/instituciones.model'; //Model del Login


@Component({
  selector: 'app-mantenimiento-instituciones',
  templateUrl: '../../../views/mantenimientos/instituciones/instituciones.component.html',
  styleUrls: ['../../../views/mantenimientos/instituciones/style.component.css'],
  providers: [LoginService, ListasComunesService, UploadService]
})

export class MantenimientoInstitucionesComponent implements OnInit{
  public titulo:string = "Mantenimiento de Instituciones";

  // Instacia de la variable del Modelo
  public _modIntituciones:Instituciones;

  // Objeto que Controlara la Forma
  forma:FormGroup;

  public data;
  public errorMessage;
  public status;
  public mensajes;

  public identity;
  public token;

  // Array de SubDirecciones
  private paramsSubDir;
  // public passwordConfirmation:string;

  // Variables de Generacion de las Listas de los Dropdow
  // Llenamos las Lista del HTML
  public JsonOutgetlistaEstados:any[];
  public JsonOutgetlistaTipoFuncionario:any[];
  public JsonOutgetlistaDeptosFuncionales:any[];
  public JsonOutgetlistaTipoUsuario:any[];
  public JsonOutgetlistaDireccionSRECI:any[];
  public JsonOutgetlistaSubDireccionSRECI:any[];
  // public JsonOut:any[];


  // Definicion del Constructor
  constructor( private _loginService: LoginService,
               private _listasComunes: ListasComunesService,
               private _uploadService: UploadService,
               private _router: Router,
               private _route: ActivatedRoute,
               private _appComponent: AppComponent,
               private _http: Http){

    // Construimos las Validaciones del Formulario
    this.forma = new FormGroup({
      // Arreglo de la Estructura del Form
      'codigoUsuario': new FormControl('00002'),
      'primerNombre': new FormControl('Juan'),
      'primerApellido': new FormControl('Perez')
    });

  }

  // Metodo OnInit
  ngOnInit(){
    // Iniciamos los Parametros de Sub Direcciones
    this.paramsSubDir = {
      "idDireccionSreci"  : ""
    };

    // Inicializacion de las Listas
    this.getlistaDireccionesSRECI();
    // this.getlistaEstados();
    this.getlistaTipoFuncionario();
    // this.getlistaDeptosFuncionales();
    this.getlistaTipoUsuarios();

    // Definicion de la Insercion de los Datos de Nuevo Usuario
    this._modIntituciones = new Instituciones(0, "", "", "",
    "", null, null, "",
    0, 0, 0);
    //this.loadScript('../assets/js/register.component.js');
  }


  // Metodo onSubmit
  onSubmit(forma:NgForm){
      console.log(this._modIntituciones);
      // parseInt(this.user.idTipoUsuario);
      this._loginService.registerUser(this._modIntituciones).subscribe(
        response => {
            // Obtenemos el Status de la Peticion
            this.status = response.status;
            this.mensajes = response.msg;

            // Condicionamos la Respuesta
            if(this.status != "success"){
                this.status = "error";
            }else{
              this.ngOnInit();
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
  }


  /*****************************************************
  * Funcion: FND-00002
  * Fecha: 28-07-2017
  * Descripcion: Carga la Lista de los Estados de la BD
  * Objetivo: Obtener la lista de los Estados de los
  * Usurios de la BD, Llamando a la API, por su metodo
  * (estados-user-list).
  ******************************************************/
  getlistaEstados() {
    //Llamar al metodo, de Login para Obtener la Identidad
    this._listasComunes.listasComunes("","estados-user-list").subscribe(
        response => {
          // login successful so redirect to return url
          //alert(response.status);
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            alert("Msg Error");
            alert(response.msg);
          }else{
            this.JsonOutgetlistaEstados = response.data;
            // console.log(response.data);

          }
        });
  } // FIN : FND-00002



  /******************************************************
  * Funcion: FND-00003
  * Fecha: 28-07-2017
  * Descripcion: Carga la Lista de los Tipos de
  * Funcionarios.
  * Objetivo: Obtener la lista de los Tipos de Funciona.
  * de la BD, Llamando a la API, por su metodo
  * (tipo-funcionario-list).
  *******************************************************/
  getlistaTipoFuncionario() {
    //Llamar al metodo, de Login para Obtener la Identidad
    this._listasComunes.listasComunes("","tipo-funcionario-list").subscribe(
        response => {
          // login successful so redirect to return url
          //alert(response.status);
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            alert("Msg Error");
            alert(response.msg);
          }else{
            //this.data = JSON.stringify(response.data);
            this.JsonOutgetlistaTipoFuncionario = response.data;
            // console.log(response.data);

          }
        });
  } // FIN : FND-00003



  /*****************************************************
  * Funcion: FND-00004
  * Fecha: 28-07-2017
  * Descripcion: Carga la Lsita de los Departamentos
  * Funcionales de la SRECI.
  * Objetivo: Obtener la lista de los Departamentos Func.
  * de la BD, Llamando a la API, por su metodo
  * (depto-funcional-user-list).
  ******************************************************/
  getlistaDeptosFuncionales() {
    //Llamar al metodo, de Login para Obtener la Identidad
    this._listasComunes.listasComunes("","depto-funcional-user-list").subscribe(
        response => {
          // login successful so redirect to return url
          //alert(response.status);
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            alert("Msg Error");
            alert(response.msg);
          }else{
            //this.data = JSON.stringify(response.data);
            this.JsonOutgetlistaDeptosFuncionales = response.data;
            // console.log(response.data);

          }
        });
  } // FIN : FND-00004



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


  /*****************************************************
  * Funcion: FND-00006
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
    // let url = "http://localhost/sicdoc/symfony/web/app_dev.php/comu/upload-image-user";
    // let url = "http://172.17.0.250/sicdoc/symfony/web/app.php/comu/upload-image-user";
    
    this._uploadService.makeFileRequest( token, url, ['image'], this.filesToUpload ).then(
        ( result ) => {
          this.resultUpload = result;
          console.log(this.resultUpload);
        },
        ( error ) => {
          alert("error");
          console.log(error);
        });
  } // FIN : FND-00006


  /*****************************************************
  * Funcion: FND-00007
  * Fecha: 18-09-2017
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
  * Funcion: FND-00007.1
  * Fecha: 18-09-2017
  * Descripcion: Carga la Lista de las Sub Direcciones de
  * SRECI
  * Objetivo: Obtener la lista de las Direcciones SRECI
  * de la BD, Llamando a la API, por su metodo
  * (subdir-sreci-list).
  ******************************************************/
  getlistaSubDireccionesSRECI() {
    //Llamar al metodo, de Login para Obtener la Identidad
    // this.paramsSubDir.idDireccionSreci = this._modIntituciones.idDireccionSreci;

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




}

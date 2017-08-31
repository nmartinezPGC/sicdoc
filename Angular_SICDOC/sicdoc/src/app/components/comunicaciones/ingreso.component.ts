import { Component, OnInit } from '@angular/core';
import { RouterModule, Routes, ActivatedRoute, Router } from '@angular/router';

import { HttpModule,  Http, Response, Headers } from '@angular/http';

//Importamos los Servicios
import { LoginService } from '../../services/login/login.service'; //Servico del Login
import { IngresoComunicacionService } from '../../services/comunicaciones/ingreso.service'; //Servico del Comunicaciones
import { ListasComunesService } from '../../services/shared/listas.service'; //Servico Listas Comunes
import { UploadService } from '../../services/shared/upload.service'; //Servico Carga de Arhcivos


import { AppComponent } from '../../app.component'; //Servico del Login

import { NgForm }    from '@angular/forms';

import { FormGroup, FormControl, Validators }    from '@angular/forms';

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
  public JsonOutgetlistaTipoFuncionario:any[];
  public JsonOutgetlistaDeptosFuncionales:any[];
  public JsonOutgetlistaTipoUsuario:any[];
  public JsonOutgetlistaPaises:any[];
  // public JsonOut:any[];


  // Definicion del Constructor
  constructor( private _loginService: LoginService,
               private _listasComunes: ListasComunesService,
               private _uploadService: UploadService,
               private _ingresoComunicacion: IngresoComunicacionService,
               private _router: Router,
               private _route: ActivatedRoute,
               private _appComponent: AppComponent,
               private _http: Http){  }

  // Metodo OnInit
  ngOnInit(){
    // Inicializacion de las Listas
    this.getlistaEstados();
    this.getlistaPaises();
    // alert('**********************');
    // this.getlistaTipoFuncionario();
    // this.getlistaDeptosFuncionales();
    // this.getlistaTipoUsuarios();

    // Definicion de la Insercion de los Datos de Nuevo Usuario
    this.comunicacion = new Comunicaciones(1, "", "", "",  0, 0, 0, 0,  "", "",  0);
    //this.loadScript('../assets/js/register.component.js');
  }


  // Metodo onSubmit
  onSubmit(forma:NgForm){
      console.log(this.user);
      // parseInt(this.user.idTipoUsuario);ssss
      this._loginService.registerUser(this.user).subscribe(
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
  }



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
  } // FIN : 00001



  /*****************************************************
  * Funcion: FND-00002
  * Fecha: 28-07-2017
  * Descripcion: Carga la Lista de los Estados de la BD
  * Objetivo: Obtener la lista de los Estados de los
  * Usurios de la BD, Llamando a la API, por su metodo
  * (estadosUsuarioList).
  ******************************************************/
  getlistaEstados() {
    //Llamar al metodo, de Login para Obtener la Identidad
    this._listasComunes.listasComunes("","estados-comunicacion-list").subscribe(
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
  * Fecha: 30-07-2017
  * Descripcion: Carga la Lista de los Paiese.
  * Objetivo: Obtener la lista de los Paises de la BD,
  * Llamando a la API, por su metodo
  * (paises).
  *******************************************************/
  getlistaPaises() {
    //Llamar al metodo, de Login para Obtener la Identidad
    this._listasComunes.listasComunes("","lista-paises").subscribe(
        response => {
          // login successful so redirect to return url
          //alert(response.status);
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            alert("Msg Error");
            alert(response.msg);
          }else{
            //this.data = JSON.stringify(response.data);
            this.JsonOutgetlistaPaises = response.data;
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
  * (deptoFuncionalList).
  ******************************************************/
  getlistaDeptosFuncionales() {
    //Llamar al metodo, de Login para Obtener la Identidad
    this._listasComunes.listasComunes("","deptoFuncionalList").subscribe(
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
  * (tipoUsuarioList).
  ******************************************************/
  getlistaTipoUsuarios() {
    //Llamar al metodo, de Login para Obtener la Identidad
    this._listasComunes.listasComunes("","tipoUsuarioList").subscribe(
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

    this._uploadService.makeFileRequest( token, url, ['image'], this.filesToUpload ).then(
        ( result ) => {
          this.resultUpload = result;
          console.log(this.resultUpload);
        },
        ( error ) => {
          alert("error");
          console.log(error);
        });
  }

}

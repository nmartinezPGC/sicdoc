import { Component, OnInit } from '@angular/core';

import { RouterModule, Router, ActivatedRoute } from '@angular/router';

// Libretias para la Comunicacion HTTp
import { HttpModule,  Http, Response, Headers } from '@angular/http';

//Importamos los Servicios nesesarios
import { LoginService } from './services/login/login.service'; //Servico del Login

// Importamos la CLase Usuarios del Modelo
import { Usuarios } from './models/usuarios/usuarios.model'; //Model del Login

// Librerias para el Formulario
import { NgForm }    from '@angular/forms';

import { FormGroup, FormControl, Validators }    from '@angular/forms';

// Importamos la Clase de las Propiedades del Sistema
import { SystemPropertiesService } from "./services/shared/systemProperties.service";

// Declaramos las variables para jQuery
declare var jQuery:any;
declare var $:any;

import sha256  from 'sha.js';

@Component({
  selector: 'app-root',
  templateUrl: 'views/shared/layout.component.html',
  styleUrls: ['./app.component.css'],
  providers: [LoginService, RouterModule, SystemPropertiesService]
})
export class AppComponent implements OnInit{
  title = 'app';
  public identity;
  public token;

  // Imagen de Usuario
  public imgUser;
  public status;
  public mensajes;
  public errorMessage;

  // Instacia de la variable del Modelo
  public user:Usuarios;

  // Objeto que Controlara la Forma
  forma:FormGroup;

  // Json que se envia con Parametros
  public jsonSendChangePass;

  // parametros para loading
  public loading = 'hide';

  // Password Actual
  public passwordUsuairoAct;
  public passIdentity:string;

  public url:string;
  public urlComplete:string;

  // public sha256 = require('js-sha256').sha256;

  constructor( private _loginService: LoginService,
               private _router: Router,
               private _route: ActivatedRoute,
               private _http: Http,
               private _systemPropertiesService: SystemPropertiesService ) { 
    this.url = this._systemPropertiesService.getmethodUrlResourses();
    this.urlComplete = this.url + "/uploads/users/";
    //Codigo del Constructor
  }

  // Metodo ngOnInit()
  ngOnInit(){
    // Instancia de los valores del Json
    this.jsonSendChangePass = {
      "idUserChange":"",
      "passWordUserAct":"",
      "passWordUserActSha":"",
      "passWordUserNew":"",
      "passWordUserConfirm":""
    }

    //Igualamos los valores de las variables, con las del Servico
    this.identity = this._loginService.getIdentity();
    this.token = this._loginService.getToken();

    // Instancia del Modelo de la Clase Usuarios
    this.user = new Usuarios(1, "", "", "", "", "",   "", "", "",   "7", 0, 0, 0, 0,  "", null, null);
  } //FIN | ngOnInit()


  // Metodo onSubmit
  onSubmit(forma:NgForm){
      // Seteamos los valores de las variables
      this.jsonSendChangePass.idUserChange = this.identity.sub;
      // Contraseña en SHA256
      this.jsonSendChangePass.passWordUserAct    = "PassAct";
      this.jsonSendChangePass.passWordUserActSha = this.identity.password;
      this.jsonSendChangePass.passWordUserNew     = this.user.passwordUsuairo;
      this.jsonSendChangePass.passWordUserConfirm = this.user.passwordConfirmation;

      // Efecto de Carga
      this.loading = 'show';

      console.log( this.jsonSendChangePass );

      // Ejecuta el llamado a la API para realizar el Cambio de Contraseña
      this._loginService.changePassUser( this.jsonSendChangePass ).subscribe(
        response => {
            // Obtenemos el Status de la Peticion
            this.status = response.status;
            this.mensajes = response.msg;

            // Condicionamos la Respuesta
            if(this.status != "success"){
                this.status = "error";
            }else{
              // Evaluamos el code Response
              // alert('Codigo de Response ' + response.code);
              if( response.code == "200" ){
                // Reiniciamos Todo
                this.ngOnInit();

                alert(this.mensajes);
                //console.log(response.data);
                setTimeout(function() {
                  $('#myModal').modal('hide');
                }, 600);
                this.loading = 'hide';

                //Redirecciona a la Pagina Login
                this._router.navigateByUrl('/login/1');
              }else {
                alert(this.mensajes);
                this.loading = 'hide';
                // return;
              }

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
  } // FIN | onSubmit


  /****************************************************
  * Funcion: FND-00001
  * Fecha: 09-10-2017
  * Descripcion: Limpia los valores del Formulario
  * Objetivo: Limpiar los valores del Formulario
  *****************************************************/
  public cleanForm() {
    // console.log('preparing to load...')
    this.user.passwordConfirmation = '';
    this.user.passwordUsuairo = '';
    this.jsonSendChangePass.passWordUserAct = '';
  } // FIN : FND-00001

}

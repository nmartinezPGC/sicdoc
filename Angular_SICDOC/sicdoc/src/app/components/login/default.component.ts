import { Component, OnInit } from '@angular/core';
import { RouterModule, Routes, ActivatedRoute, Router } from '@angular/router';

import { HttpModule,  Http, Response, Headers } from '@angular/http';

//Importamos los Servicios nesesarios
import { LoginService } from '../../services/login/login.service'; //Servico del Login

import { AppComponent } from '../../app.component'; //Servico del Login

// Librerias para el Formulario
// import { NgForm }    from '@angular/forms';

// import { FormGroup, FormControl, Validators }    from '@angular/forms';

// Importamos la CLase Usuarios del Modelo
// import { Usuarios } from '../../models/usuarios/usuarios.model'; //Model del Login

@Component({
  selector: 'app-default',
  templateUrl: '../../views/shared/default.component.html',
  styleUrls: ['../../app.component.css'],
  providers: [LoginService, RouterModule]
})
export class DefaultComponent {
  public titulo = "Portada";
  public identity;
  public token ;

  // Imagen de Usuario
  public imgUser;
  public status;
  public mensajes;
  public errorMessage;

  // Instacia de la variable del Modelo
  // public user:Usuarios;

  // Objeto que Controlara la Forma
  // forma:FormGroup;

  constructor( private _loginService: LoginService,
               private _router: Router,
               private _route: ActivatedRoute,
               private _http: Http,
               private _appComponent: AppComponent){
    //Codigo del Constructor
  }


  ngOnInit(){
    //Igualamos los valores de las variables, con las del Servico
    this.identity = this._loginService.getIdentity();
    this.token = this._loginService.getToken();


    if(this.identity == null){
      //alert('Hola Mundo');
      //Se ejecuta la Funcion de Inicio del Componente de
      // AppComponent, para actualizar el Menu
      this._appComponent.ngOnInit();
      //this._router.navigate(["/login"]);
      this._router.navigateByUrl('/login');

      // Redireccionamos a la Pagina Oficial
      //window.location.href= "/login";
    }
  }

}

import { Component, OnInit } from '@angular/core';
import { RouterModule, Routes, ActivatedRoute, Router } from '@angular/router';

//Importamos los Servicios nesesarios
import { LoginService } from '../../services/login/login.service'; //Servico del Login

import { AppComponent } from '../../app.component'; //Servico del Login

@Component({
  selector: 'app-default',
  templateUrl: '../../views/shared/default.component.html',
  providers: [LoginService, RouterModule]
})
export class DefaultComponent {
  public titulo = "Portada";
  public identity ;
  public token ;

  constructor( private _loginService: LoginService, private _router: Router,
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

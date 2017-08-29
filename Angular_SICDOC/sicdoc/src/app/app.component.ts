import { Component, OnInit } from '@angular/core';

import { RouterModule, Router, ActivatedRoute } from '@angular/router';

//Importamos los Servicios nesesarios
import { LoginService } from './services/login/login.service'; //Servico del Login

@Component({
  selector: 'app-root',
  templateUrl: 'views/shared/layout.component.html',
  providers: [LoginService, RouterModule]
})
export class AppComponent implements OnInit{
  title = 'app';
  public identity;
  public token;

  constructor( private _loginService: LoginService, private router: Router){
    //Codigo del Constructor
  }

  ngOnInit(){
    //Igualamos los valores de las variables, con las del Servico
    this.identity = this._loginService.getIdentity();
    this.token = this._loginService.getToken();

    //console.log(this.identity);
    //console.log(this.token);
  }

}

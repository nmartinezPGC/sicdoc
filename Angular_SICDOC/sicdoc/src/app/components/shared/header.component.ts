import { Component, OnInit } from '@angular/core';

//Importamos los Servicios nesesarios
import { LoginService } from '../../services/login/login.service'; //Servico del Login

@Component({
  selector: 'app-header',
  templateUrl: '../../views/shared/header.component.html'
})
export class HeaderComponent {
  public identity;
  public token;

  constructor(private _loginService: LoginService){ }

  ngOnInit(){
    //Igualamos los valores de las variables, con las del Servico
    // this.identity = this._loginService.getIdentity();
    // this.token = this._loginService.getToken();

    // console.log(this.identity);
    // console.log(this.token);

  }

}

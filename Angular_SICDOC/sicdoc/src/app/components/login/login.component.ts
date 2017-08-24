import { Component, OnInit } from '@angular/core';
import { RouterModule, Routes, ActivatedRoute, Router } from '@angular/router';

//Importamos los Servicios
import { UsuariosService, Usuario } from '../../services/usuarios/usuarios.service';
import { LoginService } from '../../services/login/login.service'; //Servico del Login

//Providers de los Accesos a Ajax
import { HttpModule,  Http, Response, Headers } from '@angular/http';

import { NgForm }    from '@angular/forms';

//Imprtamos las Rutas
import { APP_ROUTING } from '../../app.routes';

@Component({
  selector: 'app-login',
  templateUrl: '../../views/login/login.html',
  styleUrls: ['../../views/login/style.component.css'],
  providers: [LoginService]
})
export class LoginComponent implements OnInit {
  public titulo: string = "Por favor Identificate";
  //Parametros Generales
  //Objeto Json del Usuario
  public user;
  public errorMessage;
  public identity;
  public token;


  constructor( private _loginService: LoginService){
    //Codigo del Constructor
  }

  ngOnInit() {
    //Parametros del Login
    //alert(this._loginService.signup());
      this.user = {
        "email"    : "",
        "password" : "",
        "gethash"  : "false"
      }
  }

  onSubmit(){
    console.log(this.user);
    this._loginService.signup(this.user).subscribe(
        response => {
          alert(response);
        },
        error => {
          this.errorMessage = <any>error;

            if(this.errorMessage != null){
              console.log(this.errorMessage);
              alert("Error en la Petición !!");
            }
        }
    );
  }


}

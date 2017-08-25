import { Component, OnInit } from '@angular/core';
import { RouterModule, Routes, ActivatedRoute, Router } from '@angular/router';

//Importamos los Servicios
import { UsuariosService, Usuario } from '../../services/usuarios/usuarios.service';
import { LoginService } from '../../services/login/login.service'; //Servico del Login

//Providers de los Accesos a Ajax
import { HttpModule,  Http, Response, Headers } from '@angular/http';

import { NgForm }    from '@angular/forms';

import { FormsModule }   from '@angular/forms';

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

  //Url de Respuesta
  public returnUrl: string;

  constructor( private _loginService: LoginService, private router: Router){
    //Codigo del Constructor
  }

  ngOnInit() {
    //Parametros del Login
      this.user = {
        "email"    : "",
        "password" : "",
        "gethash"  : "false"
      }

      //Local Storage de la API
      let ide = this._loginService.getIdentity();
      let tk = this._loginService.getToken();

    console.log(ide);
    console.log(tk);
  }

  //Funcion que se lanza al Momento de enviar el Formulario de Login
  onSubmit(forma:NgForm){
    //console.log(this.user);
    //Llamar al metodo, de Login para Obtener la Identidad
    this._loginService.signUp(this.user).subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            alert(response.data);
          }else if (response.status == "success" ){
            //LocalStorage
            let identity = response.data;
            this.identity = identity;

            if(this.identity.length <= 1 ){
                alert('Error en el Servidor');
            }else{
              if(!identity.status){
                localStorage.setItem('identity', JSON.stringify(identity));

                //Volvemos a llamar el Servicio Ajax, para obtener el Token
                this.user.gethash = "true";
                this._loginService.signUp(this.user).subscribe(
                    response => {
                        let token = response.data;
                        this.token = token;

                        if(this.token.length <= 0){
                            alert("Error en el servidor");
                        }else{
                          if(!this.token.status){
                            localStorage.setItem( 'token', token );

                            //Redirecciona a la Pagina Oficial
                            this.router.navigateByUrl('/defautl');
                          }
                        }
                    },
                    error => {
                        //Regisra cualquier Error de la Llamada a la API
                        this.errorMessage = <any>error;

                        //Evaluar el error
                        if(this.errorMessage != null){
                          console.log(this.errorMessage);
                          alert("Error en la Petición !!" + this.errorMessage);
                        }
                    }
              );
            } // identity status
          } //identity <= 0
        } // status = success

        },
        error => {
            //Regisra cualquier Error de la Llamada a la API
            this.errorMessage = <any>error;

            //Evaluar el error
            if(this.errorMessage != null){
              console.log(this.errorMessage);
              alert("Error en la Petición !!" + this.errorMessage);
            }
        }
    );
  }

}

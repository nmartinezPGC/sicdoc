import { Component, OnInit } from '@angular/core';
import { RouterModule, Routes, ActivatedRoute, Router } from '@angular/router';

//Importamos los Servicios
import { UsuariosService, Usuario } from '../../services/usuarios/usuarios.service';
import { LoginService } from '../../services/login/login.service'; //Servico del Login

import { AppComponent } from '../../app.component'; //Servico del Login

//Providers de los Accesos a Ajax
import { HttpModule,  Http, Response, Headers } from '@angular/http';

import { NgForm }    from '@angular/forms';

import { FormsModule }   from '@angular/forms';

//Imprtamos las Rutas
import { APP_ROUTING } from '../../app.routes';

@Component({
  selector: 'app-login',
  templateUrl: '../../views/login/login.component.html',
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

  // Variables para Perfiles
  public idTipoUsuario;

  //Url de Respuesta
  public returnUrl: string;

  constructor( private _loginService: LoginService,
               private _router: Router,
               private _route: ActivatedRoute,
               private _appComponent: AppComponent){
    //Codigo del Constructor
  }

  ngOnInit() {
    //Variables de paso para el logout
    this._route.params.subscribe( params => {
      let logout = + params["id"];

      // Realizamos el Logout de la Aplicacion
      if(logout == 1){
        // Quitamos las variables del Storage
        localStorage.removeItem('identity');
        localStorage.removeItem('token');
        localStorage.removeItem('JsonPar');

        // Seteamos las variables a null
        this.identity = null;
        this.token = null;
        this.idTipoUsuario = null;

        //Se ejecuta la Funcion de Inicio del Componente de
        // AppComponent, para actualizar el Menu
        this._appComponent.ngOnInit();
        //this._router.navigate(["/login"]);
        this._router.navigateByUrl('/login');
        // Redireccionamos a la Pagina Oficial
        window.location.href= "/login";
      }
    });

    //Parametros del Login
      this.user = {
        "email"    : "",
        "password" : "",
        "gethash"  : "false"
      }

      //Local Storage de la API
      let identity = this._loginService.getIdentity();
      let token = this._loginService.getToken();

      // this.idTipoUsuario = identity.idTipoUser;
      // alert( this.idTipoUsuario );

      // Evaluamos que no tengamos variables de LocalStorage, asi si existe una
      // se redirige a la Index
      if( identity != null && identity.sub){
          this._router.navigate(["/index"]);
      }

  }

  //Funcion que se lanza al Momento de enviar el Formulario de Login
  onSubmit(forma:NgForm){
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

                            //Se ejecuta la Funcion de Inicio del Componente de
                            // AppComponent, para actualizar el Menu
                            this._appComponent.ngOnInit();
                            //Redirecciona a la Pagina Oficial
                            this._router.navigateByUrl('/defautl');
                          }
                        }
                    },
                    error => {
                        //Regisra cualquier Error de la Llamada a la API
                        this.errorMessage = <any>error;

                        //Evaluar el error
                        if(this.errorMessage != null){
                          console.log(this.errorMessage);
                          alert(response);
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

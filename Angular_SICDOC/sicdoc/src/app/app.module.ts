import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { HttpModule } from '@angular/http';

//Rutas
import { APP_ROUTING } from './app.routes';

//Servicios
import { UsuariosService }  from "./services/usuarios/usuarios.service";
import { LoginService } from './services/login/login.service';

//Compoenentes
import { AppComponent } from './app.component';
import { DefaultComponent } from "./components/login/default.component";
import { LoginComponent } from "./components/login/login.component";
import { RegisterComponent } from "./components/login/register.component";

import { NavbarComponent } from "./components/shared/navbar.component"; //NavBar de Tareas del Proyecto
import { HeaderComponent } from "./components/shared/header.component"; //Header de Tareas del Proyecto

@NgModule({
  declarations: [
    AppComponent,
    //Seccion de Login
    DefaultComponent,
    LoginComponent,
    RegisterComponent,
    NavbarComponent,
    HeaderComponent

  ],
  imports: [
    BrowserModule,
    //importamos este para las llamdas a Ajax
    HttpModule,
    APP_ROUTING
  ],
  providers: [
    UsuariosService,
    LoginService
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }

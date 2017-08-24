import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';

//Rutas
import { APP_ROUTING } from './app.routes';

//Servicios


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
    APP_ROUTING
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }

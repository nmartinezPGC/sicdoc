import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { HttpModule } from '@angular/http';

//Libreria para poder Usar los Formularios de Html y Indexarlos con Angular (uso del #)
import { FormsModule, ReactiveFormsModule  }   from '@angular/forms';
import { NgForm }    from '@angular/forms';
// Import your library
// import { AutocompleteModule } from 'ng2-input-autocomplete';

// Importamos la Libreria del Pipe
import { Pipe, PipeTransform } from '@angular/core';

// Pipes Personalizados de uso Comun
import { GenerateDatePipe } from './pipes/common/generate.date.pipe';
import { SearchFilterPipe } from './pipes/common/generate.search.pipe';

//Libreria para usar Rutas
import { APP_ROUTING } from './app.routes';

//Servicios de la Aplicacion ***************************************************
import { UsuariosService }  from "./services/usuarios/usuarios.service";
import { LoginService } from './services/login/login.service';
import { IngresoComunicacionService } from './services/comunicaciones/ingreso.service';
import { AgregarActividadService } from './services/seguimiento/agregar.actividad.service';
import { FinalizarActividadService } from './services/seguimiento/finalizar.actividad.service';

//Compoenentes *****************************************************************
import { AppComponent } from './app.component';
import { DefaultComponent } from "./components/login/default.component";
import { LoginComponent } from "./components/login/login.component";
import { RegisterComponent } from "./components/login/register.component";
import { FinalizarActividadComponent } from './components/seguimiento/finalizar.actividad.component';

// Area de Ingreso de Oficios
import { IngresoComunicacionComponent } from "./components/comunicaciones/ingreso.component";

// Area de Seguimiento
import { IngresoActividadComponent } from "./components/seguimiento/agregar.actividad.component";


import { NavbarComponent } from "./components/shared/navbar.component"; //NavBar de Tareas del Proyecto
import { HeaderComponent } from "./components/shared/header.component";
import { PdfComponent } from './components/pdf/pdf.component'; //Header de Tareas del Proyecto


@NgModule({
  declarations: [
    AppComponent,
    //Seccion de Login
    DefaultComponent,
    LoginComponent,
    RegisterComponent,
    IngresoComunicacionComponent,
    IngresoActividadComponent,
    NavbarComponent,
    HeaderComponent,
    GenerateDatePipe,
    SearchFilterPipe,
    FinalizarActividadComponent,
    PdfComponent
  ],
  imports: [
    BrowserModule,
    //importamos este para las llamdas a Ajax
    HttpModule,
    FormsModule,
    ReactiveFormsModule,
    APP_ROUTING
  ],
  providers: [
    UsuariosService,
    LoginService,
    IngresoComunicacionService,
    IngresoActividadComponent,
    AgregarActividadService,
    FinalizarActividadService
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }

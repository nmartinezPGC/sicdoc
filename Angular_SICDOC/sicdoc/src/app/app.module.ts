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
import { SeguimientoActividadService } from './services/seguimiento/seguimiento.actividad.service';

//Compoenentes *****************************************************************
import { AppComponent } from './app.component';
import { DefaultComponent } from "./components/login/default.component";
import { LoginComponent } from "./components/login/login.component";
import { RegisterComponent } from "./components/login/register.component";
import { FinalizarActividadComponent } from './components/seguimiento/finalizar.actividad.component';
import { IngresoComunicacionPorTipoComponent } from './components/comunicaciones/ingreso.comunicacion/ingreso.comunicacion.component';
import { SeguimientoActividadComponent } from './components/seguimiento/seguimiento.actividad.component'; //Header de Tareas del Proyecto

// Area de Ingreso de Oficios
import { IngresoComunicacionComponent } from "./components/comunicaciones/ingreso.component";

// Area de Seguimiento
import { IngresoActividadComponent } from "./components/seguimiento/agregar.actividad.component";

import { NavbarComponent } from "./components/shared/navbar.component"; //NavBar de Tareas del Proyecto
import { HeaderComponent } from "./components/shared/header.component";
import { PdfComponent } from './components/pdf/pdf.component';
import { DatatablesActividadPendientesComponent } from './components/seguimiento/datatables-actividad-pendientes/datatables-actividad-pendientes.component';

// import de Materialize
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
/*import { MdButtonModule, MdMenuModule, MdSidenavModule, MdGridListModule, MdExpansionModule,
       MdDatepickerModule, MdInputModule, MdCheckboxModule, MdNativeDateModule, MdTableModule} from '@angular/material';*/

// Imports de las Consultas de la Aplicacion
import { ConsultaMasterComponent } from './components/consultas/consulta.master/consulta.master.component';
import { ConsultaMasterService } from './services/consultas/consulta.master.service';

// Imports de los Contactos SRECI
import { ContactosComponent } from './components/contactos/contacto.component';
import { ContactosService } from './services/contactos/contacto.service';

// Imports de los Mantenimientos
import { MantenimientoInstitucionesComponent } from './components/mantenimientos/instituciones/mantenimiento.institucion.component';
import { InstitucionesService } from './services/mantenimientos/instituciones.service';
import { MantenimientoSolicitudCambioFechasComponent } from './components/mantenimientos/solicitud.cambio.fechas/mantenimiento.solicitud.cambio.fecha.component';
import { SolicitudCambioFechaService } from './services/mantenimientos/solicitud.cambio.fecha.service';
//import { InstitucionesService } from './services/mantenimientos/instituciones.service';


// Imports de AutoComplete
import { Ng2CompleterModule } from "ng2-completer";

// import { MultiselectDropdownModule } from 'angular-2-dropdown-multiselect';

import { AngularMultiSelectModule } from 'angular2-multiselect-dropdown/angular2-multiselect-dropdown';

// Imports de Pluguin de Subir Imagenes
// import { ImageUploadModule } from "angular2-image-upload";
import { ReporteGeneralComponent } from './components/consultas/reportes/reporte.general/reporte.general.component';

// Imports de la Clase de Properties del System
import { SystemPropertiesService } from './services/shared/systemProperties.service';
import { AgregarDocumentosComponent } from './components/comunicaciones/agregar.documentos/agregar.documentos.component';
import { TrasladoComunicacionComponent } from './components/comunicaciones/traslado.comunicacion/traslado.comunicacion.component';

//import tosty
import {ToastyModule} from 'ng2-toasty';
import { EditarComunicacionComponent } from './components/comunicaciones/editar.comunicacion/editar.comunicacion.component';

//import Chart Modules
import { ChartsModule } from 'ng2-charts';
import { ChartHomeComponent } from './components/charts/chart.home/chart.home.component';
import { ReporteResumidoComponent } from './components/consultas/reportes/reporte.resumido/reporte.resumido.component';
import { CorrespondenciaEntradaComponent } from './components/correspondencia/correspondencia.entrada/correspondencia.entrada.component';
import { CorrespondenciaSalidaComponent } from './components/correspondencia/correspondencia.salida/correspondencia.salida.component';
import { AcuseRecibidoComponent } from './components/correspondencia/acuse-recibido/acuse-recibido.component';
import { ModificaUsuarioComponent } from './components/login/modifica.usuario/modifica.usuario.component';

@NgModule({
  declarations: [
    AppComponent,
    //Seccion de Login
    DefaultComponent,
    LoginComponent,
    RegisterComponent,
    //Seccion de Comunicaciones
    IngresoComunicacionComponent,
    IngresoActividadComponent,
    NavbarComponent,
    HeaderComponent,
    GenerateDatePipe,
    SearchFilterPipe,
    FinalizarActividadComponent,
    PdfComponent,
    IngresoComunicacionPorTipoComponent,
    SeguimientoActividadComponent,
    DatatablesActividadPendientesComponent,
    //Seccion de Consultas
    ConsultaMasterComponent,
    ContactosComponent,
    ReporteGeneralComponent,
    //Seccion de Mantenimientos
    MantenimientoInstitucionesComponent,
    MantenimientoSolicitudCambioFechasComponent,
    AgregarDocumentosComponent,
    TrasladoComunicacionComponent,
    EditarComunicacionComponent,
    ChartHomeComponent,
    ReporteResumidoComponent,
    CorrespondenciaEntradaComponent,
    CorrespondenciaSalidaComponent,
    AcuseRecibidoComponent,
    ModificaUsuarioComponent
  ],
  imports: [
    BrowserModule,
    //importamos este para las llamdas a Ajax
    HttpModule,
    FormsModule,
    ReactiveFormsModule,
    APP_ROUTING,
    // importamos los Modules de Materialize
    /*BrowserAnimationsModule, MdButtonModule, MdMenuModule, MdSidenavModule,
    MdGridListModule, MdExpansionModule, MdDatepickerModule, MdInputModule,
    MdCheckboxModule, MdNativeDateModule, MdTableModule*/
    Ng2CompleterModule,
    // ImageUploadModule.forRoot(),
    //Seccion de AngularMultiSelectModule
    AngularMultiSelectModule,
    ToastyModule.forRoot(),
    ChartsModule
  ],
  exports: [
    BrowserModule, ToastyModule
  ],
  providers: [
    UsuariosService,
    LoginService,
    IngresoComunicacionService,
    IngresoActividadComponent,
    AgregarActividadService,
    FinalizarActividadService,
    SeguimientoActividadService,
    ContactosService,
    InstitucionesService,
    SolicitudCambioFechaService,
    SystemPropertiesService
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }

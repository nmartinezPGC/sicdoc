import { Component } from '@angular/core';
import { RouterModule, Routes, ActivatedRoute, Router, ROUTER_CONFIGURATION } from '@angular/router';

//Importamos todas las Rutas que deseamos movernos
import { LoginComponent } from './components/login/login.component';
import { DefaultComponent } from './components/login/default.component';
import { RegisterComponent } from './components/login/register.component';
// Menu de comunicaciones
import { IngresoComunicacionComponent } from './components/comunicaciones/ingreso.component';
import { IngresoComunicacionPorTipoComponent } from './components/comunicaciones/ingreso.comunicacion/ingreso.comunicacion.component';
// Menu de Seguimiento
import { IngresoActividadComponent } from './components/seguimiento/agregar.actividad.component';
import { FinalizarActividadComponent } from './components/seguimiento/finalizar.actividad.component';
import { SeguimientoActividadComponent } from './components/seguimiento/seguimiento.actividad.component';
import { PdfComponent } from './components/pdf/pdf.component';

// Imports de las Consultas de la Aplicacion
import { ConsultaMasterComponent } from './components/consultas/consulta.master/consulta.master.component';
import { ReporteGeneralComponent } from './components/consultas/reportes/reporte.general/reporte.general.component';

// Imports de los Mantenimientos
import { MantenimientoInstitucionesComponent } from './components/mantenimientos/instituciones/mantenimiento.institucion.component';

// Imports de los Contactos
import { ContactosComponent } from './components/contactos/contacto.component';

export const APP_ROUTES: Routes = [
  //Ruta por defecto
  { path: 'index', component: DefaultComponent },
  { path: 'login', component: LoginComponent },
  { path: 'login/:id', component: LoginComponent },
  { path: 'registro', component: RegisterComponent },
  // Mapeo del Menu de Comunicaciones
  { path: 'ingreso-comunicacion', component: IngresoComunicacionComponent },
  { path: 'ingreso-comunicacion-tipo', component: IngresoComunicacionPorTipoComponent },

  // Mapeo de Menu de Seguimiento
  { path: 'asignar-actividad', component: IngresoActividadComponent },
  { path: 'asignar-actividad/:page', component: IngresoActividadComponent },
  { path: 'agregar-actividad', component: IngresoActividadComponent },
  { path: 'agregar-actividad/:page', component: IngresoActividadComponent },
  { path: 'finalizar-actividad', component: FinalizarActividadComponent },
  { path: 'finalizar-actividad/:page', component: IngresoActividadComponent },
  { path: 'seguimiento-actividad', component: SeguimientoActividadComponent },
  { path: 'pdf', component: PdfComponent },

  // Mapeo de Menu de Consultas
  { path: 'consulta-maestra-comunicacion', component: ConsultaMasterComponent },
  { path: 'consulta-generacion-reporte', component: ReporteGeneralComponent },

  // Mapeo de Menu de Mantenimientos
  { path: 'mantenimiento-instituciones', component: MantenimientoInstitucionesComponent },
  // { path: 'consulta-generacion-reporte', component: ReporteGeneralComponent },

  // Mapeo de Opcion de Contactos
  { path: 'contactos-sreci', component: ContactosComponent },

  // Ruta Default
  { path: '**', pathMatch: 'full', redirectTo: 'index' }
];

export const APP_ROUTING = RouterModule.forRoot(APP_ROUTES, { useHash:true });
//export const APP_ROUTING = RouterModule.forRoot(APP_ROUTES);

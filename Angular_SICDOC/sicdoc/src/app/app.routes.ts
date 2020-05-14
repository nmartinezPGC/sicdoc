import { Component } from '@angular/core';
import { RouterModule, Routes, ActivatedRoute, Router, ROUTER_CONFIGURATION } from '@angular/router';

//Importamos todas las Rutas que deseamos movernos
import { LoginComponent } from './components/login/login.component';
import { DefaultComponent } from './components/login/default.component';
import { RegisterComponent } from './components/login/register.component';
import { ModificaUsuarioComponent } from './components/login/modifica.usuario/modifica.usuario.component';
// Menu de comunicaciones
import { IngresoComunicacionComponent } from './components/comunicaciones/ingreso.component';
import { IngresoComunicacionPorTipoComponent } from './components/comunicaciones/ingreso.comunicacion/ingreso.comunicacion.component';
import { EditarComunicacionComponent } from './components/comunicaciones/editar.comunicacion/editar.comunicacion.component';
import { AgregarDocumentosComponent } from './components/comunicaciones/agregar.documentos/agregar.documentos.component';
import { TrasladoComunicacionComponent } from './components/comunicaciones/traslado.comunicacion/traslado.comunicacion.component';

// Menu de Seguimiento
import { IngresoActividadComponent } from './components/seguimiento/agregar.actividad.component';
import { FinalizarActividadComponent } from './components/seguimiento/finalizar.actividad.component';
import { SeguimientoActividadComponent } from './components/seguimiento/seguimiento.actividad.component';
import { PdfComponent } from './components/pdf/pdf.component';

// Imports de las Consultas de la Aplicacion
import { ConsultaMasterComponent } from './components/consultas/consulta.master/consulta.master.component';
import { ReporteGeneralComponent } from './components/consultas/reportes/reporte.general/reporte.general.component';
import { ReporteResumidoComponent } from './components/consultas/reportes/reporte.resumido/reporte.resumido.component';

// Imports de los Mantenimientos
import { MantenimientoInstitucionesComponent } from './components/mantenimientos/instituciones/mantenimiento.institucion.component';
import { MantenimientoSolicitudCambioFechasComponent } from './components/mantenimientos/solicitud.cambio.fechas/mantenimiento.solicitud.cambio.fecha.component';
//import { MantenimientoInstitucionesComponent } from './components/mantenimientos/instituciones/mantenimiento.institucion.component';

// Imports de los Contactos
import { ContactosComponent } from './components/contactos/contacto.component';

//imports de Correspondencia
import { CorrespondenciaEntradaComponent } from './components/correspondencia/correspondencia.entrada/correspondencia.entrada.component';
import { CorrespondenciaSalidaComponent } from './components/correspondencia/correspondencia.salida/correspondencia.salida.component';
import { AcuseRecibidoComponent } from './components/correspondencia/acuse-recibido/acuse-recibido.component';

export const APP_ROUTES: Routes = [
  //Ruta por defecto
  { path: 'index', component: DefaultComponent },
  { path: 'auth/login', component: LoginComponent },
  { path: 'auth/login/:id', component: LoginComponent },
  { path: 'auth/usuario-nuevo', component: RegisterComponent },
  { path: 'auth/editar-usuario', component: ModificaUsuarioComponent },

  // Mapeo del Menu de Comunicaciones
  { path: 'comunicacion/ingreso-de-comunicacion', component: IngresoComunicacionComponent },
  { path: 'comunicacion/salida-de-comunicacion', component: IngresoComunicacionPorTipoComponent },
  { path: 'comunicacion/editar-comunicacion', component: EditarComunicacionComponent },
  { path: 'comunicacion/documentos-de-comunicacion', component: AgregarDocumentosComponent },
  { path: 'comunicacion/traslado-de-comunicacion', component: TrasladoComunicacionComponent },

  // Mapeo de Menu de Seguimiento
  { path: 'seguimiento/asignar-comunicacion', component: IngresoActividadComponent },
  { path: 'seguimiento/asignar-comunicacion/:page', component: IngresoActividadComponent },
  // { path: 'agregar-actividad', component: IngresoActividadComponent },
  // { path: 'agregar-actividad/:page', component: IngresoActividadComponent },
  { path: 'seguimiento/finalizar-comunicacion', component: FinalizarActividadComponent },
  { path: 'seguimiento/finalizar-comunicacion/:page', component: IngresoActividadComponent },
  { path: 'seguimiento/seguimiento-comunicacion', component: SeguimientoActividadComponent },
  { path: 'pdf', component: PdfComponent },

  // Mapeo de Menu de Consultas
  { path: 'consultas/consulta-maestra-comunicacion', component: ConsultaMasterComponent },
  { path: 'consultas/consulta-generacion-reporte', component: ReporteGeneralComponent },
  { path: 'consultas/consulta-reporte-resumido', component: ReporteResumidoComponent },

  // Mapeo de Menu de Mantenimientos
  { path: 'mantenimientos/mantenimiento-instituciones', component: MantenimientoInstitucionesComponent },
  { path: 'mantenimientos/solicitud-cambio-fecha', component: MantenimientoSolicitudCambioFechasComponent },
  { path: 'mantenimientos/cambio-fecha', component: MantenimientoInstitucionesComponent },

  // Mapeo de Opcion de Contactos
  { path: 'contactos/contactos-sreci', component: ContactosComponent },

  // Mapeo de Opcion de Correspondencia
  { path: 'correspondencia/entrada-de-correspondencia', component: CorrespondenciaEntradaComponent },
  { path: 'correspondencia/salida-de-correspondencia', component: CorrespondenciaSalidaComponent },
  { path: 'correspondencia/recepcion-de-correspondencia', component: AcuseRecibidoComponent },

  // Ruta Default
  { path: '**', pathMatch: 'full', redirectTo: 'index' }
];

export const APP_ROUTING = RouterModule.forRoot(APP_ROUTES, { useHash: true });
//export const APP_ROUTING = RouterModule.forRoot(APP_ROUTES);

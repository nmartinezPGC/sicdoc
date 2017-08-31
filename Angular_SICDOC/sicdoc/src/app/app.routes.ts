import { Component } from '@angular/core';
import { RouterModule, Routes, ActivatedRoute, Router, ROUTER_CONFIGURATION } from '@angular/router';

//Importamos todas las Rutas que deseamos movernos
import { LoginComponent } from './components/login/login.component';
import { DefaultComponent } from './components/login/default.component';
import { RegisterComponent } from './components/login/register.component';
import { IngresoComunicacionComponent } from './components/comunicaciones/ingreso.component';

export const APP_ROUTES: Routes = [
  //Ruta por defecto

  { path: 'index', component: DefaultComponent },
  { path: 'login', component: LoginComponent },
  { path: 'login/:id', component: LoginComponent },
  { path: 'registro', component: RegisterComponent },
  // Mapeo del Menu de Comunicaciones
  { path: 'ingreso-comunicacion', component: IngresoComunicacionComponent },

  { path: '**', pathMatch: 'full', redirectTo: 'index' }
];

//export const APP_ROUTING = RouterModule.forRoot(APP_ROUTES, { useHash:true });
export const APP_ROUTING = RouterModule.forRoot(APP_ROUTES);

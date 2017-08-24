import { RouterModule, Routes } from '@angular/router';

//Importamos todas las Rutas que deseamos movernos
import { LoginComponent } from './components/login/login.component';
import { DefaultComponent } from './components/login/default.component';
import { RegisterComponent } from './components/login/register.component';

const APP_ROUTES: Routes = [
  //Ruta por defecto

  { path: 'index', component: DefaultComponent },
  { path: 'login', component: LoginComponent },
  { path: 'registro', component: RegisterComponent },
  { path: '**', pathMatch: 'full', redirectTo: 'index' }
];

export const APP_ROUTING = RouterModule.forRoot(APP_ROUTES, { useHash:true });

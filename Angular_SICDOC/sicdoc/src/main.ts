import { enableProdMode } from '@angular/core';
import { platformBrowserDynamic } from '@angular/platform-browser-dynamic';

//Importamos las Rutas del Provider
import { APP_ROUTING } from "./app/app.routes" ;

import { HttpModule,  Http } from '@angular/http';

import { AppModule } from './app/app.module';
import { environment } from './environments/environment';

import {FormsModule, ReactiveFormsModule} from '@angular/forms';

if (environment.production) {
  enableProdMode();
}

platformBrowserDynamic().bootstrapModule(AppModule, [HttpModule, APP_ROUTING]).catch(Error => console.log(Error))  ;

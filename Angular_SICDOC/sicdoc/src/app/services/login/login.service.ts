import { Injectable } from '@angular/core';

//Clases nesesarias para el envio via Ajax
import { HttpModule,  Http, Response, Headers } from '@angular/http';
import 'rxjs/add/operator/catch';
import 'rxjs/add/operator/map';
import { Observable } from 'rxjs/Observable';

@Injectable()
export class LoginService {
  //Propiedades de la Clases
  public url = "http://localhost/sicdoc/symfony/web/app_dev.php";


  constructor( private _http: Http ) {  }

  //Funcion de Resgistro
  signup( user_to_login ){
      let json = JSON.stringify( user_to_login );
      let params = "json=" + json;
      let headers = new Headers({ 'Content-Type':'application/x-www-form-urlencoded'});

    return this._http.post(this.url + "/login", params, { headers:headers }).map( res => res.json());
  }

}

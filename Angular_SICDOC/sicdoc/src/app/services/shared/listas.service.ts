import { Injectable } from '@angular/core';

//Clases nesesarias para el envio via Ajax
import { HttpModule,  Http, Response, Headers } from '@angular/http';
import 'rxjs/add/operator/catch';
import 'rxjs/add/operator/map';
import { Subject } from 'rxjs/Subject';
import { Observable } from 'rxjs/Observable';

@Injectable()
export class ListasComunesService {
  //Propiedades de la Clases
  //URL Base de la Clase, Referencia a la API | Symfony
  public url = "http://localhost/sicdoc/symfony/web/app_dev.php";

  //Variables para el localStorage
  public identity;
  public token;

  //Constructor de la Clase
  constructor( private _http: Http ) { }

  // Funcion que llama las Lsitas de los Estados
  listasComunes( lista_comun, lista ){
      let json = JSON.stringify( lista_comun );

      let params = "json=" + json;
      let listaIn = lista;
      let headers = new Headers({ 'Content-Type':'application/x-www-form-urlencoded'});

    return this._http.post(this.url + "/listas/"+listaIn, params, { headers:headers }).map( res => res.json());
  }

}

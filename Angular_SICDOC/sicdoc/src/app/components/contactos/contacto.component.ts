import { Component, OnInit, Inject } from '@angular/core';

import { FormGroup, FormControl, Validators } from '@angular/forms';

import { RouterModule, Routes, ActivatedRoute, Router } from '@angular/router';

import { HttpModule,  Http, Response, Headers } from '@angular/http';

// Lirerias para el AutoComplete
import {Observable} from 'rxjs/Observable';
import 'rxjs/add/operator/startWith';
import 'rxjs/add/operator/map';

//Importamos los Servicios
import { ListasComunesService } from '../../services/shared/listas.service'; //Servico Listas Comunes
import { UploadService } from '../../services/shared/upload.service'; //Servico Carga de Arhcivos
import { ContactosService } from '../../services/contactos/contacto.service'; //Servico La Clase Contactos

import { AppComponent } from '../../app.component'; //Servico del Login

import { NgForm }    from '@angular/forms';

// Importamos la CLase Usuarios del Modelo
import { Contactos } from '../../models/contactos/contacto.model'; // Servico del Login

import { CompleterService, CompleterData, CompleterItem } from 'ng2-completer';

import 'rxjs/Rx';

// Declaramos las variables para jQuery
declare var jQuery:any;
declare var $:any;

@Component({
  selector: 'app-contactos',
  templateUrl: '../../views/contactos/contactos.component.html',
  styleUrls: ['../../views/contactos/contactos.component.css'],
  providers: [ ContactosComponent , ContactosService , ListasComunesService, UploadService]
})
export class ContactosComponent implements OnInit {
  public titulo = "Contactos";
  public newTittle = "Ingreso de Contacto";

  protected searchStr: string;
  protected captain: string;
  protected dataService: CompleterData;
  protected searchData = [
    { colors: 'red', value: '#f00', id: 1 },
    { colors: 'green', value: '#0f0', id: 2 },
    { colors: 'blue', value: '#00f', id: 3 },
    { colors: 'cyan', value: '#0ff', id: 4 },
    { colors: 'magenta', value: '#f0f', id: 5 },
    { colors: 'yellow', value: '#ff0', id: 6 },
    { colors: 'black', value: '#000', id: 7 }
  ];
  protected captains = ['James T. Kirk', 'Benjamin Sisko', 'Jean-Luc Picard', 'Spock', 'Jonathan Archer', 'Hikaru Sulu', 'Christopher Pike', 'Rachel Garrett' ];

  protected selectedColor: string;
  // variables del localStorage
  public identity;
  public token;
  public userId;

  // Instacia del Objeto Model de la Clase
  public consultaContactos: Contactos;

  // parametros multimedia
  public loading  = 'show';
  public loading_table  = 'hide';
  public loading_tr  = 'hide';
  public loading_tableIn = 'hide';

  public loadTabla1:boolean = false;
  public loadTabla2:boolean = false;

  // Parametros de los Json de la Aplicacion
  public JsonOutgetlistaContactosDet:any[];
  public JsonOutgetlistaContactosEnc:any[];

  public JsonLimpio;


  // Ini | Definicion del Constructor
  constructor( private _listasComunes: ListasComunesService,
               private _router: Router,
               private _consultaContactoService: ContactosService,
               private _route: ActivatedRoute,
               private _appComponent: AppComponent,
               private _http: Http,
               private completerService: CompleterService ) {
    // Llenado de la Tabla de Encabezado
    //  this.fillDataTable();
    // this.dataService = completerService.local(this.searchData, 'colors', 'colors');
    // this.dataService = completerService.local(this.JsonOutgetlistaContactosEnc, 'idContacto', 'idContacto');
    // console.log(this.JsonOutgetlistaContactosEnc);
    // console.log(this.dataService);
  } // Fin | Definicion del Constructor


  /*****************************************************
  * Funcion: ngOnInit
  * Fecha: 11-10-2017
  * Descripcion: Metodo inicial del Programa
  ******************************************************/
  ngOnInit() {
    // Hacemos que la variable del Local Storge este en la API
    //this.identity = JSON.parse(localStorage.getItem('identity'));
    //this.userId = this.identity.sub;
    this.searchStr = "";

    // Definicion de la Insercion de los Datos de Nuevo Contacto
    this.consultaContactos = new Contactos ( 0, null, null, null, null, null,
                                             null, null,  0, 0,
                                             null, null,  0, 0);

    // Ejecucion de la Lista de Comunicacion de Usuario Logeado
    this.getlistaContactosTableFind();
    //console.log(this.JsonOutgetlistaContactosEnc);
    // this.dataService = this.completerService.local(this.JsonOutgetlistaContactosEnc, 'idContacto', 'idContacto');

    // console.log(this.dataService);
  } // Fin | ngOnInit

  /*****************************************************
  * Funcion: FND-00001
  * Fecha: 11-10-2017
  * Descripcion: Carga la Lista de los Contactos de la
  * BD que han sido ingresados
  * Objetivo: Obtener la lista de los Contactos de la BD,
  * Llamando a la API, por su metodo
  * ( contactos/contactos-consulta ).
  * Params: Modelo de la Clase ( Modelo de la Clase )
  ******************************************************/
  getlistaContactosTableFind() {
    // Laoding
    this.loading = 'show';
    this.loadTabla1 = false;
    // console.log( this.consultaContactos );
    // Llamar al metodo, de Service para Obtener los Datos de los Contactos
    this._consultaContactoService.contactoFindAll( this.consultaContactos ).subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetlistaContactosEnc = response.data;

            alert(response.msg);
          }else{
            this.JsonOutgetlistaContactosEnc = response.data;
            //this.valoresdataDetJson ( response.data );
            this.JsonLimpio = this.JsonOutgetlistaContactosEnc;
            this.loading = 'hidden';
            this.loadTabla1 = true;

            console.log(this.JsonOutgetlistaContactosEnc);
            this.dataService = this.completerService.local(this.JsonOutgetlistaContactosEnc, 'nombre1Contacto', 'nombre1Contacto');

            console.log(this.dataService);
            // console.log( this.JsonOutgetlistaContactosEnc );
            // console.log( this.JsonLimpio );
          }
        });
  } // FIN | FND-00001


  /*****************************************************
  * Funcion: FND-00002
  * Fecha: 06-10-2017
  * Descripcion: Realiza el llenado de la Tabla con Todos
  * los Filtros
  * Params: none
  ******************************************************/
  fillDataTable(){
    setTimeout(function () {
      $ (function () {
          $('#example').DataTable();
          this.loading_tableIn = 'show';
      });
    }, 8000);
    this.loading_tableIn = 'hide';
  } // FIN | FND-00002


  /*****************************************************
  * Funcion: FND-00003
  * Fecha: 11-10-2017
  * Descripcion: Funcion para AutoCompletar y sacar el
  * Id de la Data
  * Params: $event
  ******************************************************/
  protected onSelected( item: CompleterItem ) {
    this.selectedColor = item? item.originalObject.apellido1Contacto : "";
    // alert('Hola Mundo ' + item );
  } // FIN | FND-00003

} // FIN | Clase

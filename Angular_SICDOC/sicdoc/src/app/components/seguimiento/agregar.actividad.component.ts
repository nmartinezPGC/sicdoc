import { Component, OnInit } from '@angular/core';
import { FormGroup, FormControl, Validators } from '@angular/forms';

import { RouterModule, Routes, ActivatedRoute, Router } from '@angular/router';

import { HttpModule,  Http, Response, Headers } from '@angular/http';

// Lirerias para el AutoComplete
import {Observable} from 'rxjs/Observable';
import 'rxjs/add/operator/startWith';
import 'rxjs/add/operator/map';

//Importamos los Servicios
import { LoginService } from '../../services/login/login.service'; //Servico del Login
import { ListasComunesService } from '../../services/shared/listas.service'; //Servico Listas Comunes

import { AppComponent } from '../../app.component'; //Servico del Principal

import { NgForm }    from '@angular/forms';


@Component({
  selector: 'app-agregar-actividad',
  templateUrl: '../../views/seguimiento/agregar.actividad.component.html',
  styleUrls: ['../../views/seguimiento/agregar.actividad.component.css'],
  providers: [ LoginService, ListasComunesService ]
})

export class IngresoActividadComponent implements OnInit{
  public titulo:string = "Agregar Actividad";
  public fechaHoy:Date = new Date();
  public fechafin:string;

  // Campos de la tabla
  private tableAgregarActividad;
  private tableAgregarActividadCodOficio:any[];
  private tableAgregarActividadFechamaxima;

  // variables del Paginador
  public pages;
  public pagePrev = 1;
  public pageNext = 1;



  // Campos del Modal PopUp
  private codOficioModal:string;
  private temaOficioModal:string;
  private institucionOficioModal:string;


  // parametros multimedia
  public loading  = 'show';
  public status;
  public errorMessage;



  // Json de los listas de los Oficios por usuario
  public JsonOutgetlistaOficiosAll:any[];
  // public JsonOutgetlistaEstados:any[];
  // public JsonOutgetlistaEstados:any[];


  // Definicion del constructor
  constructor( private _listasComunes: ListasComunesService,
              private _router: Router,
              private _route: ActivatedRoute,
              private _appComponent: AppComponent,
              private _http: Http
  ){}


  // Json de Muestra la hacer el Component
  public usuarios:Usuario[] = [{
      nombre: "Aquaman",
      bio: "El poder más reconocido de Aquaman es la capacidad telepática para comunicarse con la vida marina, la cual puede convocar a grandes distancias.",
      img: "assets/img/aquaman.png",
      aparicion: "1941-11-01",
      casa:"DC"
    },
    {
      nombre: "Batman",
      bio: "Los rasgos principales de Batman se resumen en «destreza física, habilidades deductivas y obsesión». La mayor parte de las características básicas de los cómics han variado por las diferentes interpretaciones que le han dado al personaje.",
      img: "assets/img/batman.png",
      aparicion: "1939-05-01",
      casa:"DC"
    }];

  // INI | Metodo OnInit
  ngOnInit(){
    // Iniciamos los Parametros de Instituciones
    this.tableAgregarActividad = {
      "estado"  : "Ingresado",
      "numero"  : "121212",
      "nombre"  : "SRECI",
      "tema"  : "Tema 1",
      "fecha"  : "2017-09-012"
    };

    this.getlistaAsinarOficios();

  }

  //Metodo para Obtener el Array
  getUsuarios():Usuario[]{
    return this.usuarios;
  }


  /*****************************************************
  * Funcion: FND-00001
  * Fecha: 13-09-2017
  * Descripcion: Carga la Lista de los Oficios de la BD
  * que pertenecen al usaurio Logeado
  * Objetivo: Obtener la lista de los Oficios de las
  * Comunicaciones de la BD, Llamando a la API, por su
  * metodo (asignar-oficios-list).
  ******************************************************/
  getlistaAsinarOficios() {
    // parametro de la Paginador
    this._route.params.subscribe(  params =>  {
        let page =  +params["page"];
        if( !page ){
          page = 1;
        }

        // Laoding
        this.loading = 'show';
    //Llamar al metodo, de Listado de Funcionarios
    this._listasComunes.listasOficiosDir( page ).subscribe(
        response => {
            this.status = response.status;

            if( this.status != "success" ){
              this.status = "error";
            }else{
              this.JsonOutgetlistaOficiosAll = response.data;
              // this.tableAgregarActividadCodOficio = this.JsonOutgetlistaOficiosAll[0].codReferenciaSreci;
              // this.tableAgregarActividadFechamaxima = this.JsonOutgetlistaOficiosAll[0].fechaMaxEntrega;
              this.loading = 'hidden';
              //console.log( this.JsonOutgetlistaOficiosAll );

              this.pages = [];

                // Llenamos el numero de paginas a Mostrar
                for(let i = 0; i < response.total_pages; i++ ){
                  this.pages.push(i);
                }
                // alert(page);
                // Evaluamos el desbode de la paginas anteriores
                if( page >= 2 ){
                  this.pagePrev = ( page - 1 );
                }else{
                  this.pagePrev = page;
                }

                // Evaluamos el desbode de la paginas anteriores
                if( page < response.total_pages || page == 1 ){
                  this.pageNext = ( page + 1 );
                }else{
                  this.pageNext = page;
                }
            } // Llamado con datos Buenos
        },
        error => {
            this.errorMessage = <any>error;

            if( this.errorMessage != null ){
              console.log( this.errorMessage );
              alert( "Errro en la petición." );
            }
        }); // Fin de Llamado al Servicios

    }); // Fin de parametros
  } // FIN : FND-00001



  /****************************************************
  * Funcion: FND-00001.1
  * Fecha: 13-09-2017
  * Descripcion: Funcion que convierte las fechas
  * Objetivo: Obtener las fechas para la tabla
  *****************************************************/
   timeConverter(UNIX_timestamp){
     let a = new Date( UNIX_timestamp * 1000);
  //  alert(UNIX_timestamp);
       let diaFechamaxima = String( a.getDay() );
       let mesFechamaxima = String( a.getMonth() + 1 );
       let anioFechamaxima = String( a.getFullYear() );

       // Condicion de dias y Mese < 10
       if(diaFechamaxima.length < 2 ){
         diaFechamaxima =  '0' + diaFechamaxima;
       }else if(mesFechamaxima.length < 2 ){
         mesFechamaxima =  '0' + mesFechamaxima;
       }
       // Agrupamos las secciones de las Fechas
       let time = diaFechamaxima + '-' + mesFechamaxima + '-' + anioFechamaxima ;
       // retorna la fecha convertida
       return time;
   } // FIN : FND-00001.1


   /****************************************************
   * Funcion: FND-00001.2
   * Fecha: 13-09-2017
   * Descripcion: Funcion que ejecuta la conulta a la BD
   * para obtener la informacion de los Funcionarios a
   * cargo de la Directora
   * Objetivo: Obtener funcionarios que estan a cargo de
   * la Directora
   *****************************************************/
   funcionariosListDir( idSubDireccionSreci ){
    //  alert(codOficio);
     this.codOficioModal = idSubDireccionSreci;
     this.tableAgregarActividadCodOficio = idSubDireccionSreci;
   } // FIN : FND-00001.2


   /****************************************************
   * Funcion: FND-00001.3
   * Fecha: 13-09-2017
   * Descripcion: Funcion que convierte las fechas
   * Objetivo: Obtener las fechas para la tabla
   *****************************************************/
   datoOficio( codOficio ){
    //  alert(codOficio);
     this.codOficioModal = codOficio;
     this.tableAgregarActividadCodOficio = codOficio;
   } // FIN : FND-00001.3



} // Fin del Component

export interface Usuario{
   nombre:string;
   bio:string;
   img:string;
   aparicion:string;
   casa:string;
}

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


// Importamos la CLase Usuarios del Modelo
import { AgregarActividad } from '../../models/seguimiento/agregar.actividad.model'; // Modelo a Utilizar

// Importamos Jquery
// Declaramos las variables para jQuery
declare var jQuery:any;
declare var $:any;

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

  // Instacia del Modelo
  public asignarOficios: AgregarActividad;

  // Objeto que Controlara la Forma
  forma:FormGroup;

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
    this.tableAgregarActividad = [
      ["Ingresado", "", "", "","",""],
      ["121212" ,"", "", "","",""],
      ["SRECI", "", "", "","",""],
      ["Tema 1" , "", "", "","",""],
      ["2017-09-12", "", "", "","",""],
      ["dsd" , "", "", "","",""]
    ];

    this.getlistaAsinarOficios();

    // Definicion de la Insercion de los Datos de Nuevo Usuario
    this.asignarOficios = new AgregarActividad(null, null);

    // ejecucion de la Tabla
    //$('#example').dataTable();
    // $('#example').dataTable( {
    //     "ajax": function (data, callback, settings) {
    //       callback(
    //         JSON.parse( localStorage.getItem('JsonPar') )
    //   );
    // }
    // } );


//     var dataSet = [
//     [ "Tiger Nixon", "System Architect", "Edinburgh", "5421", "2011/04/25", "$320,800" ],
//     [ "Garrett Winters", "Accountant", "Tokyo", "8422", "2011/07/25", "$170,750" ],
//     [ "Ashton Cox", "Junior Technical Author", "San Francisco", "1562", "2009/01/12", "$86,000" ],
//     [ "Cedric Kelly", "Senior Javascript Developer", "Edinburgh", "6224", "2012/03/29", "$433,060" ],
//     [ "Airi Satou", "Accountant", "Tokyo", "5407", "2008/11/28", "$162,700" ],
//     [ "Brielle Williamson", "Integration Specialist", "New York", "4804", "2012/12/02", "$372,000" ],
//     [ "Herrod Chandler", "Sales Assistant", "San Francisco", "9608", "2012/08/06", "$137,500" ],
//     [ "Rhona Davidson", "Integration Specialist", "Tokyo", "6200", "2010/10/14", "$327,900" ],
//     [ "Colleen Hurst", "Javascript Developer", "San Francisco", "2360", "2009/09/15", "$205,500" ],
//     [ "Sonya Frost", "Software Engineer", "Edinburgh", "1667", "2008/12/13", "$103,600" ],
//     [ "Jena Gaines", "Office Manager", "London", "3814", "2008/12/19", "$90,560" ],
//     [ "Quinn Flynn", "Support Lead", "Edinburgh", "9497", "2013/03/03", "$342,000" ],
//     [ "Charde Marshall", "Regional Director", "San Francisco", "6741", "2008/10/16", "$470,600" ],
//     [ "Haley Kennedy", "Senior Marketing Designer", "London", "3597", "2012/12/18", "$313,500" ],
//     [ "Tatyana Fitzpatrick", "Regional Director", "London", "1965", "2010/03/17", "$385,750" ],
//     [ "Michael Silva", "Marketing Designer", "London", "1581", "2012/11/27", "$198,500" ],
//     [ "Paul Byrd", "Chief Financial Officer (CFO)", "New York", "3059", "2010/06/09", "$725,000" ],
//     [ "Gloria Little", "Systems Administrator", "New York", "1721", "2009/04/10", "$237,500" ],
//     [ "Bradley Greer", "Software Engineer", "London", "2558", "2012/10/13", "$132,000" ],
//     [ "Dai Rios", "Personnel Lead", "Edinburgh", "2290", "2012/09/26", "$217,500" ],
//     [ "Jenette Caldwell", "Development Lead", "New York", "1937", "2011/09/03", "$345,000" ],
//     [ "Yuri Berry", "Chief Marketing Officer (CMO)", "New York", "6154", "2009/06/25", "$675,000" ],
//     [ "Caesar Vance", "Pre-Sales Support", "New York", "8330", "2011/12/12", "$106,450" ],
//     [ "Doris Wilder", "Sales Assistant", "Sidney", "3023", "2010/09/20", "$85,600" ],
//     [ "Angelica Ramos", "Chief Executive Officer (CEO)", "London", "5797", "2009/10/09", "$1,200,000" ],
//     [ "Gavin Joyce", "Developer", "Edinburgh", "8822", "2010/12/22", "$92,575" ],
//     [ "Jennifer Chang", "Regional Director", "Singapore", "9239", "2010/11/14", "$357,650" ],
//     [ "Brenden Wagner", "Software Engineer", "San Francisco", "1314", "2011/06/07", "$206,850" ],
//     [ "Fiona Green", "Chief Operating Officer (COO)", "San Francisco", "2947", "2010/03/11", "$850,000" ],
//     [ "Shou Itou", "Regional Marketing", "Tokyo", "8899", "2011/08/14", "$163,000" ],
//     [ "Michelle House", "Integration Specialist", "Sidney", "2769", "2011/06/02", "$95,400" ],
//     [ "Suki Burks", "Developer", "London", "6832", "2009/10/22", "$114,500" ],
//     [ "Prescott Bartlett", "Technical Author", "London", "3606", "2011/05/07", "$145,000" ],
//     [ "Gavin Cortez", "Team Leader", "San Francisco", "2860", "2008/10/26", "$235,500" ],
//     [ "Martena Mccray", "Post-Sales support", "Edinburgh", "8240", "2011/03/09", "$324,050" ],
//     [ "Unity Butler", "Marketing Designer", "San Francisco", "5384", "2009/12/09", "$85,675" ]
// ];
//
// let serial = JSON.stringify( this.JsonOutgetlistaOficiosAll );
// console.log(dataSet);
// $(document).ready(function() {
//     $('#example').DataTable( {
//         data: dataSet,
//         columns: [
//             { title: "Name" },
//             { title: "Position" },
//             { title: "Office" },
//             { title: "Extn." },
//             { title: "Start date" },
//             { title: "Salary" }
//         ]
//     } );
// } );





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
    this._route.params.subscribe( params => {
      let page = +params["page"];
      if( !page ){
        page = 1;
      }

    // Laoding
    this.loading = 'show';

    //Llamar al metodo, de Login para Obtener la Identidad
    // this._listasComunes.listasComunes("","asignar-oficios-list").subscribe(
    //     response => {
    //       // login successful so redirect to return url
    //       if(response.status == "error"){
    //         //Mensaje de alerta del error en cuestion
    //         this.JsonOutgetlistaOficiosAll = response.data;
    //         alert(response.msg);
    //       }else{
    //         this.JsonOutgetlistaOficiosAll = response.data;
    //         this.tableAgregarActividadCodOficio = this.JsonOutgetlistaOficiosAll[0].codReferenciaSreci;
    //         this.tableAgregarActividadFechamaxima = this.JsonOutgetlistaOficiosAll[0].fechaMaxEntrega;
    //         this.loading = 'hidden';
    //         console.log(this.JsonOutgetlistaOficiosAll);
    //         localStorage.setItem('JsonPar', JSON.stringify(  this.JsonOutgetlistaOficiosAll ));
    //       }
    //     });


    //Llamar al metodo, de Listado de Funcionarios
    this._listasComunes.listasComunesGet("",  "asignar-oficios-page-list?page=" + page ).subscribe(
        response => {
            this.status = response.status;

            if( this.status != "success" ){
              this.status = "error";
            }else{
              this.JsonOutgetlistaOficiosAll = response.data;
              // this.tableAgregarActividadCodOficio = this.JsonOutgetlistaOficiosAll[0].codReferenciaSreci;
              // this.tableAgregarActividadFechamaxima = this.JsonOutgetlistaOficiosAll[0].fechaMaxEntrega;
              this.loading = 'hidden';
              console.log( this.JsonOutgetlistaOficiosAll );

              this.pages = [];

                // Llenamos el numero de paginas a Mostrar
                for(let i = 0; i < response.total_page ; i++ ){
                  this.pages.push(i);
                }
                // alert(page);
                // Evaluamos el desbode de la paginas anteriores
                if( page >= 2 ){
                  this.pagePrev = ( page - 1 );
                  // alert('Click 1' + 'Page ' + page);
                }else{
                  this.pagePrev = page;
                  // alert('Click 2' + 'Page ' + page);
                }

                // Evaluamos el desbode de la paginas anteriores
                if( page < response.total_page || page == 1 ){
                  this.pageNext = ( page + 1 );
                  // alert('Click 3' + 'Page ' + page);
                }else{
                  this.pageNext = page;
                  // alert('Click 4' + 'Page ' + page);
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


   /****************************************************
   * Funcion: FND-00001.4
   * Fecha: 14-09-2017
   * Descripcion: Funcion busca los Oficios desde la caja
   * de texto arriba de la Caja
   * Objetivo: Obtener los oficios de la BD
   *****************************************************/
   buscarOficio( codOficio:string ){
    console.log( codOficio );
    //  this.codOficioModal = codOficio;
    //  this.tableAgregarActividadCodOficio = codOficio;
  } // FIN : FND-00001.4



} // Fin del Component

export interface Usuario{
   nombre:string;
   bio:string;
   img:string;
   aparicion:string;
   casa:string;
}

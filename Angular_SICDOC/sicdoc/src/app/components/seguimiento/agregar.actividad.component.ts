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

// Importamos los Pipes de la Forma
import { GenerateDatePipe } from '../../pipes/common/generate.date.pipe';
import { SearchFilterPipe } from '../../pipes/common/generate.search.pipe';

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
  public titulo:string = "Asignar Actividad";
  public fechaHoy:Date = new Date();
  public fechafin:string;

  public txtFname;

  // Instacia del Modelo
  public asignarOficios: AgregarActividad;

  // Objeto que Controlara la Forma
  forma:FormGroup;

  public checkWork = 0;

  // Campos de la tabla
  private tableAgregarActividad;
  // private tableAgregarActividadCodOficio:any[];
  private tableAgregarActividadCodOficio;
  private tableAgregarActividadFechamaxima;

  // variables del Paginador
  public pages;
  public pagePrev = 1;
  public pageNext = 1;

  public identity;

  // Campos del Modal PopUp
  private codOficioModal:string;
  private idDeptoFuncionalModal:string;
  private temaOficioModal:string;
  private institucionOficioModal:string;
  private nombreFuncModal:string;
  private apellidoFuncModal:string;
  private idFuncModal:string;

  public paramsTable;

  // Parametro de Depto. Funcional del User
  public deptoUser;
  private deptoFuncionalUser:number;
  public deptoUserParse;
  public idFuncionarioParse;
  public deptoUserJson;


  // parametros multimedia
  public loading  = 'show';
  public status;
  public errorMessage;


  // Json de los listas de los Oficios por usuario
  public JsonOutgetlistaOficiosAll:any[];
  public JsonOutgetlistaFuncionariosDisponibles:any[];
  // public JsonOutgetlistaEstados:any[];


  // Definicion del constructor
  constructor( private _listasComunes: ListasComunesService,
              private _router: Router,
              private _route: ActivatedRoute,
              private _appComponent: AppComponent,
              private _http: Http
  ){}


  // INI | Metodo OnInit
  ngOnInit(){
    // Iniciamos los Parametros de Instituciones
    this.tableAgregarActividad = {
      "codOficio":"",
      "idDeptoFunc":"",
      "nombreFuncAsig":"",
      "apellidoFuncAsig":"",
      "idFuncionarioModal":""
    };

    // Iniciamos los Parametros de Usuarios a Depto Funcionales
    this.deptoUserJson = {
      "idUser":""
    };

    // Inicializamos las Listas del Formulario
    //this.getlistaAsinarOficios();

    // Definicion de la Insercion de los Datos de Nuevo Usuario
    this.asignarOficios = new AgregarActividad(null, null, null);

    // Lista de la tabla de Funcionarios
    this.deptoFuncional();

  }


  /*****************************************************
  * Funcion: FND-00001
  * Fecha: 13-09-2017
  * Descripcion: Carga la Lista de los Oficios de la BD
  * que pertenecen al usaurio Logeado
  * Objetivo: Obtener la lista de los Oficios de las
  * Comunicaciones de la BD, Llamando a la API, por su
  * metodo (asignar-oficios-page-list).
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

    // console.log( this.idFuncModal );
    //Llamar al metodo, de Listado de Funcionarios
    this._listasComunes.listasComunesGet("",  "asignar-oficios-page-list?page=" + page +
                                              "&idDeptoFuncional=" + this.deptoFuncionalUser +
                                              "&idUsuario=" + this.deptoUserJson.idUser ).subscribe(
        response => {
            this.status = response.status;

            if( this.status != "success" ){
              this.status = "error";
            }else{
              this.JsonOutgetlistaOficiosAll = response.data;
              // this.tableAgregarActividadCodOficio = this.JsonOutgetlistaOficiosAll[0].codReferenciaSreci;
              // this.tableAgregarActividadFechamaxima = this.JsonOutgetlistaOficiosAll[0].fechaMaxEntrega;
              this.loading = 'hidden';

              // Array de las Paginas
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
       //console.log(UNIX_timestamp);
       return time;
   } // FIN : FND-00001.1


   /****************************************************
   * Funcion: FND-00001.2
   * Fecha: 13-09-2017
   * Descripcion: Funcion que ejecuta la consulta a la BD
   * para obtener la informacion de los Funcionarios a
   * cargo de la Directora
   * Objetivo: Obtener funcionarios que estan a cargo de
   * la Directora
   * ( funcionarios-depto-list )
   *****************************************************/
   funcionariosListDir( idDeptoFuncIn ){
    // Parametros de la Lista de los Funcionarios
    this.tableAgregarActividad.idDeptoFunc = idDeptoFuncIn;
    // console.log( this.tableAgregarActividad);
    this._listasComunes.listasComunes( this.tableAgregarActividad ,"funcionarios-depto-list").subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetlistaFuncionariosDisponibles = response.data;
            alert(response.msg);
          }else{
            //this.data = JSON.stringify(response.data);
            this.JsonOutgetlistaFuncionariosDisponibles = response.data;
          }
        });

    //  this.codOficioModal = idSubDireccionSreci;
    //  this.tableAgregarActividadCodOficio = idSubDireccionSreci;
   } // FIN : FND-00001.2


   /****************************************************
   * Funcion: FND-00001.3
   * Fecha: 15-09-2017
   * Descripcion: Funcion que ejecuta la consulta a la BD
   * para obtener la informacion del Depto. Funcinal del
   * Usuario que tiene activa la sesion
   * Objetivo: Obtener el Depto Funcinal del Usuario
   *****************************************************/
   deptoFuncional(){
    // Parametros de la Lista de los Funcionarios
    // Variable del localStorage, para obtener el Id del Usuario (sub) y luego
    // la parseamos a Objeto Javascript
    this.identity = JSON.parse(localStorage.getItem('identity'));
    this.deptoUserJson.idUser = this.identity.sub;
    // console.log( this.deptoUserJson.idUser );
    // Llamado al Metodo ajax de la Lista del Depto. Funcional del user
    this._listasComunes.listasComunes( this.deptoUserJson ,"depto-func-user").subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.deptoUser = response.data;
            alert(response.msg);
          }else{
            //this.data = JSON.stringify(response.data);
            this.deptoUser = response.data;
            // Parseamos la data del response
            this.deptoUserParse = this.deptoUser.idDeptoFuncional.idDeptoFuncional;
            // console.log( this.deptoUserParse );
            this.deptoFuncionalUser = this.deptoUserParse;

            // Ejecutamos la Funcion que carga la tabla de los Oficios Ingresados
            // listos para poder ser Asignados
            this.getlistaAsinarOficios()

            // Ejecutamos la Funcion que carga la tabla de los Funcionarios Disponibles
            this.funcionariosListDir( this.deptoUserParse );

          }
        });
  } // FIN : FND-00001.3


   /****************************************************
   * Funcion: FND-00001.3
   * Fecha: 13-09-2017
   * Descripcion: Funcion que convierte las fechas
   * Objetivo: Obtener las fechas para la tabla
   *****************************************************/
   datoOficio( codOficio, idDepto, nombrefuncionarioAsignado, apellidofuncionarioAsignado,
              idFuncionarioModal ){
    //  alert(codOficio);
     this.codOficioModal = codOficio;
     this.idDeptoFuncionalModal = idDepto;
     this.nombreFuncModal = nombrefuncionarioAsignado;
     this.apellidoFuncModal = apellidofuncionarioAsignado;
     this.idFuncModal = idFuncionarioModal;

    // Asigna los Valores al Json de Modal
     this.tableAgregarActividad.codOficio = codOficio;
     this.tableAgregarActividad.idDeptoFunc = idDepto;
     this.tableAgregarActividad.nombreFuncAsig = nombrefuncionarioAsignado;
     this.tableAgregarActividad.apellidoFuncAsig = apellidofuncionarioAsignado;
     this.tableAgregarActividad.idFuncAsig = idFuncionarioModal;
    //  this.funcionariosListDir( idDepto );

   } // FIN : FND-00001.3


   /****************************************************
   * Funcion: FND-00001.4
   * Fecha: 14-09-2017
   * Descripcion: Funcion busca los Oficios desde la caja
   * de texto arriba de la Caja
   * Objetivo: Obtener los oficios de la BD
   *****************************************************/
   buscarOficio( codOficio:string ){
    //console.log( codOficio );
    //  this.codOficioModal = codOficio;
    //  this.tableAgregarActividadCodOficio = codOficio;
  } // FIN : FND-00001.4


  /****************************************************
  * Funcion: FND-00001.5
  * Fecha: 16-09-2017
  * Descripcion: Funcion para Asignar el Oficio al Fun-
  * cionario seleccionado
  * Objetivo: Asignar el Oficio al Funcionario
  *****************************************************/
  asignarOficioFuncionario( codOficioIn:string, idFuncionarioIn:number ){
   // Confirmamos que el Usuario acepte el Cambio
   let confirma = confirm('Estas seguro de Asignar este Oficio a este Funcionario ?');
   if( confirma == true){
      alert( 'Ejecucion de Funcion: ' + codOficioIn + ' idFuncionarioIn ' + idFuncionarioIn);
   }else{
     alert( 'No has Aceptado el Cambio ' );
     this.checkWork = 0;
   }


   //  this.codOficioModal = codOficio;
   //  this.tableAgregarActividadCodOficio = codOficio;
 } // FIN : FND-00001.5


} // Fin del Component

export interface Usuario{
   nombre:string;
   bio:string;
   img:string;
   aparicion:string;
   casa:string;
}

import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { FormGroup, FormControl, Validators } from '@angular/forms';

import { RouterModule, Routes, ActivatedRoute, Router } from '@angular/router';

import { HttpModule,  Http, Response, Headers } from '@angular/http';

// Lirerias para el AutoComplete
import {Observable} from 'rxjs/Observable';
import 'rxjs/add/operator/startWith';
import 'rxjs/add/operator/map';

//Importamos los Servicios
import { ListasComunesService } from '../../services/shared/listas.service'; //Servico Listas Comunes
import { AgregarActividadService } from '../../services/seguimiento/agregar.actividad.service'; //Servico Listas Comunes

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
  providers: [ AgregarActividadService, ListasComunesService ]
})

export class IngresoActividadComponent implements OnInit{
  public titulo:string = "Asignar Comunicación";
  public fechaHoy:Date = new Date();
  public fechafin:string;

  public txtFname;

  // Instacia del Modelo
  public asignarOficios: AgregarActividad;

  // Objeto que Controlara la Forma
  forma:FormGroup;

  public checkWork = 0;

  // Variables de Mensajeria y Informaicon
   public data;
   public errorMessage;
   public status;
   public mensajes;

  // Campos de la tabla
  private tableAgregarActividad;
  // private tableAgregarActividadCodOficio:any[];
  private tableAgregarActividadCodOficio;
  private tableAgregarActividadFechamaxima;
  private asignaFuncionarioSend; // Json de Datos de Envio a Asignar

  //Parametros de documentos subidos anteriores
  public JsonOutgetlistaDocumentosUpload:any[];
  public paramsDocumentos;

  public loadTabla2:boolean = false;

  public urlConfigLocal:string;
  public urlResourseLocal:string;
  public urlComplete:string;

  // variables del Paginador
  public pages;
  public pagePrev = 1;
  public pageNext = 1;

  public identity;

  // Campos del Modal PopUp
  private codOficioIntModal:string;
  private codOficioRefModal:string;
  private idDeptoFuncionalModal:number;
  private temaOficioModal:string;
  private institucionOficioModal:string;
  private nombre1FuncModal:string;
  private nombre2FuncModal:string;
  private apellido1FuncModal:string;
  private apellido2FuncModal:string;
  private idFuncModal:number;
  private idEstadoModal:number;
  private descEstadoModal:string = "Asignar";


  // Campos del Modal PopUp | Asignacion
  private codOficioIntModalAsignacion:string;
  private codOficioRefModalAsignacion:string;
  private idDeptoFuncionalModalAsignacion:number;
  private temaOficioModalAsignacion:string;
  private institucionOficioModalAsignacion:string;
  private nombre1FuncModalAsignacion:string;
  private nombre2FuncModalAsignacion:string;
  private apellido1FuncModalAsignacion:string;
  private apellido2FuncModalAsignacion:string;
  private idFuncModalAsignacion:number;
  private idEstadoModalAsignacion:number;
  private descEstadoModalAsignacion:string = "Asignar";

  public paramsTable;

  // Parametro de Depto. Funcional del User
  public deptoUser;
  private deptoFuncionalUser:number;
  public deptoUserParse;
  public idFuncionarioParse;
  public deptoUserJson;


  // parametros multimedia
  public loading  = 'show';
  public loading_table  = 'hide';
  public loading_tr  = 'hide';

  public loadTabla1:boolean = false;

  // Parametros para aplicar a los Estilos
  public colorestado:number = 1;
  public itemEstado:number = 0;
  public strEstadoOficio:string = "text-primary";
  // public strEstadoAsignado:string = "badge badge-pill badge-warning";
  // public strEstadoFinzalizado:string = "badge badge-pill badge-warning";
  // public strEstadoVencido:string = "badge badge-pill badge-warning";


  //variables de Confirmacion
  public confirma;

  // Json de los listas de los Oficios por usuario
  public JsonOutgetlistaOficiosAll:any[];
  public JsonOutgetlistaFuncionariosDisponibles:any[];
  // public JsonOutgetlistaEstados:any[];


  // Definicion del constructor
  constructor( private _listasComunes: ListasComunesService,
              private _asignaOficio: AgregarActividadService,
              private _router: Router,
              private _route: ActivatedRoute,
              private _appComponent: AppComponent,
              private _http: Http,
              private changeDetectorRef: ChangeDetectorRef,){
    // Seteo de la Ruta de la Url Config
    this.urlConfigLocal = this._asignaOficio.url;
    this.urlResourseLocal = this._asignaOficio.urlResourses;
    this.urlComplete = this.urlResourseLocal + "uploads/correspondencia/";
  }


  closeModal( nameBotton ){
    setTimeout(function() {
      // $('#t_and_c_m').modal('hide');
      $( nameBotton ).click();
    }, 600);
  }

  // INI | Metodo OnInit
  ngOnInit(){
    // Iniciamos los Parametros de Asignar Actividad
    this.tableAgregarActividad = {
      "codOficioInt":"",
      "codOficioRef":"",
      "idDeptoFunc":"",
      "nombreFuncAsig":"",
      "apellidoFuncAsig":"",
      "idFuncionarioModal":"",
      "idTipoFuncionarioModal":""
    };

    // Iniciamos los Parametros de Usuarios a Depto Funcionales
    this.deptoUserJson = {
      "idUser":""
    };


    // Iniciamos los Parametros de Envio para la Asignacion del Oficio
    this.asignaFuncionarioSend = {
      "codOficio":"",
      "codOficioRef":"",
      "idDeptoFunc":"",
      "nombreFuncAsig":"",
      "apellidoFuncAsig":"",
      "idFuncionarioModal":""
    }

    // Inicializamos las Listas del Formulario
    //this.getlistaAsinarOficios();

    // Definicion de la Insercion de los Datos de Oficio Asignado
    this.asignarOficios = new AgregarActividad(null, null, 0, null, null, null, null,
      "Estimad@, favor dar Seguimiento a la Comunicación", null, 3, null);

    // Lista de la tabla de Funcionarios
    this.deptoFuncional();

    //Iniciamos los Parametros de Json de Documentos
    this.paramsDocumentos = {
      "searchValueSend" : ""
    };


    setTimeout(function () {
      $ (function () {
          $('#example').DataTable({
            // Refresca la Data y Borra de Memoria los Datos anteriores
            destroy: true,
            retrieve: true,
            // Barra Vertical de la Tabla
            scrollY:       "450px",
            scrollX:        true,
            scrollCollapse: true,

            //Selecciona las Filas
            select: true
          });
      });
    }, 500);

  } // Fin de Metodo ngOnInit()


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
    // Iniciamos el Proceso
    this._route.params.subscribe( params => {
      let page = +params["page"];
      if( !page ){
        page = 1;
      }

    // Laoding
    this.loading = 'show';

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

              this.loadTabla1 = true;

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
              // console.log( this.errorMessage );
              // Recarga la Pagina cuando hay un Error de Cache
              window.location.reload();
              // alert( "Errror en la petición pulsa F5 para recargar la pagina, de persistir el Error, contacte al Administrador" );
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
    this.identity = JSON.parse(localStorage.getItem('identity'));
    this.tableAgregarActividad.idTipoFuncionarioModal = this.identity.idTipoUser;

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
            console.log(this.JsonOutgetlistaFuncionariosDisponibles);
          }
        },
        error => {
            this.errorMessage = <any>error;

            if( this.errorMessage != null ){
              // console.log( this.errorMessage );
              // Recarga la Pagina cuando hay un Error de Cache
              window.location.reload();
              // alert( "Errror en la petición pulsa F5 para recargar la pagina, de persistir el Error, contacte al Administrador" );
            }
        }); // Fin de Llamado al Servicios

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
   * Descripcion: Funcion que Obtiene los Datos del Oficio
   * Utilizando parametros del Json de la tabla
   * Objetivo: Obtener los Datos del Oficio seleccionado
   *****************************************************/
   datoOficio( codOficioIntIn:string, codOficioRefIn:string, idDeptoIn:number,
              nombre1funcionarioAsignadoIn:string, apellido1funcionarioAsignadoIn:string,
              nombre2funcionarioAsignadoIn:string, apellido2funcionarioAsignadoIn:string,
              idFuncionarioIn:number, idEstadoAsign:number  ){
    // Previa validacion de los Datos por el Estado del Oficio
    this.idEstadoModal = idEstadoAsign;
    if( idEstadoAsign == 5 ){ // Oficio esta Finalizado
      alert('La Comunicación no puede ser Asignado; ya que esta en un estado Finalizado.');
      this.descEstadoModal = "Finalizado";
      return;
    }else if( idEstadoAsign == 3 ){
        this.descEstadoModal = "Asignado";
    }

    // Seteo de las varibles de la Funcion
     this.codOficioIntModal = codOficioIntIn;
     this.codOficioRefModal = codOficioRefIn;
     this.idDeptoFuncionalModal = idDeptoIn;
     this.nombre1FuncModal = nombre1funcionarioAsignadoIn;
     this.nombre2FuncModal = nombre2funcionarioAsignadoIn;
     this.apellido1FuncModal = apellido1funcionarioAsignadoIn;
     this.apellido2FuncModal = apellido2funcionarioAsignadoIn;
     this.idFuncModal = idFuncionarioIn;


    // Asigna los Valores al Json de Modal
     this.tableAgregarActividad.codOficioInt = codOficioIntIn;
     this.tableAgregarActividad.codOficioRef = codOficioRefIn;
     this.tableAgregarActividad.idDeptoFunc = idDeptoIn;
     this.tableAgregarActividad.nombreFuncAsig = nombre1funcionarioAsignadoIn;
     this.tableAgregarActividad.nombreFuncAsig = nombre2funcionarioAsignadoIn;
     this.tableAgregarActividad.apellidoFuncAsig = apellido1funcionarioAsignadoIn;
     this.tableAgregarActividad.apellidoFuncAsig = apellido2funcionarioAsignadoIn;
     this.tableAgregarActividad.idFuncAsig = idFuncionarioIn;
    //  this.funcionariosListDir( idDepto );

   } // FIN : FND-00001.3



   /****************************************************
   * Funcion: FND-00001.3.1
   * Fecha: 15-12-2017
   * Descripcion: Funcion que valida si el usuario es el
   * valido a ingresar
   * Utilizando parametros nombre y apellido
   * Objetivo: Obtener la confirmacion del usuario selec-
   * cionado
   *****************************************************/
   confirmUser(codOficioIntModalAsignacionIn, codOficioRefModalAsignacionIn, idFuncModalAsignacionIn,
              nombre1FuncModalAsignacionIn, apellido1FuncModalAsignacionIn, nombre2FuncModalAsignacionIn, apellido2FuncModalAsignacion,
              flagEvent){
     //this.confirma = confirm('Esta seguro de Asignar este Oficio a: ' + nombre1FuncModalAsignacionIn + ' ' + apellido1FuncModalAsignacionIn + ' ?');
     //if(this.confirma == true){
     if(codOficioIntModalAsignacionIn != null){
       //Asignamos las variables del Modal | usuario seleccionado
       this.codOficioIntModalAsignacion = codOficioIntModalAsignacionIn;
       this.codOficioRefModalAsignacion = codOficioRefModalAsignacionIn;
       this.idFuncModalAsignacion = idFuncModalAsignacionIn;
       this.nombre1FuncModalAsignacion = nombre1FuncModalAsignacionIn;
       this.apellido1FuncModalAsignacion = apellido1FuncModalAsignacionIn;
       this.nombre2FuncModalAsignacion = nombre2FuncModalAsignacionIn;
       this.apellido2FuncModalAsignacion = apellido2FuncModalAsignacion;

       // Condicionamos que el evento sea Commit, de lo contrario solo es una consulta
       if (flagEvent == 1) {
         this.asignarOficios.buscadorOficio = "Estimad@, " + nombre1FuncModalAsignacionIn + " " + apellido1FuncModalAsignacionIn + ", " + "favor dar Seguimiento a la Comunicación";
       } else if (flagEvent == 2) {
         this.asignarOficios.buscadorOficio;
       }
       // this.changeDetectorRef.detectChanges();

       return true;
     }else{
       return false;
     }
   }


  /****************************************************
  * Funcion: FND-00001.5
  * Fecha: 16-09-2017
  * Descripcion: Funcion para Asignar el Oficio al Fun-
  * cionario seleccionado
  * Objetivo: Asignar el Oficio al Funcionario
  * Metodo API: ( seguimiento/asignar-oficio )
  * Params: Codigo Oficio Interno, Externo (Referencia)
  *         Id Funcionario, Nombre y Apellido Funcionario
  *****************************************************/
  asignarOficioFuncionario( codOficioInternoIn:string, codOficioReferenciaIn:string, idFuncionarioAsignIn:number,
                          nombre1FuncionarioAsign:string, apellido1FuncionarioAsign:string,
                          nombre2FuncionarioAsign:string, apellido2FuncionarioAsign:string  ){
   // Recolectamos los Parametros de la Pagina que envia la Funcion
   // 1 ) Preguntamos por si esta escogiendo el Mismo Funionario
   if( this.idFuncModal == idFuncionarioAsignIn ){
     alert('No puedes asignar esta Comunicación al Funcionario: ' + nombre1FuncionarioAsign + ' ' + apellido1FuncionarioAsign + ', porque ya lo tiene asignado.');
     return;
   }
   // 2 ) Si la Condicion retorna verdadero, Obtenmos los valores de la Funcion
   this.asignarOficios.codOficioInterno  = codOficioInternoIn;
   this.asignarOficios.codOficioExterno  = codOficioReferenciaIn;
   this.asignarOficios.idFuncionarioAsigmado  = idFuncionarioAsignIn;
   this.asignarOficios.nombre1FuncionarioAsigmado  = nombre1FuncionarioAsign;
   this.asignarOficios.nombre2FuncionarioAsigmado  = nombre2FuncionarioAsign;
   this.asignarOficios.apellido1FuncionarioAsigmado  = apellido1FuncionarioAsign;
   this.asignarOficios.apellido2FuncionarioAsigmado  = apellido2FuncionarioAsign;

   // 3 ) Confirmamos que el Usuario acepte el Cambio
   this.confirma = confirm('Esta seguro de Asignar esta Comunicación al Funcionario: ' + nombre1FuncionarioAsign + ' ' + apellido1FuncionarioAsign + ' ?');

   // 3.1 ) Detectamos los Cambios del Formulario y Indicamos que el evento es Commit

   if( this.confirma == true && this.confirmUser(this.asignarOficios.codOficioInterno, this.asignarOficios.codOficioExterno, this.asignarOficios.idFuncionarioAsigmado,
                        this.asignarOficios.nombre1FuncionarioAsigmado, this.asignarOficios.apellido1FuncionarioAsigmado,
                        this.asignarOficios.nombre2FuncionarioAsigmado, this.asignarOficios.apellido2FuncionarioAsigmado, 2  ) == true){
   // 4 ) Ejecutamos el llamado al Metodo de la API ( /seguimiento/asignar-oficio )
      let token1 = this._asignaOficio.getToken();
      this.loading_table = 'show';
      // console.log(this.asignarOficios);

    this._asignaOficio.asignarOficio( token1, this.asignarOficios ).subscribe(
        response => {
            // Obtenemos el Status de la Peticion
            this.status = response.status;
            this.mensajes = response.msg;

            // Condicionamos la Respuesta
            if(this.status != "success"){
                this.status = "error";
                this.mensajes = response.msg;
                if(this.loading_table = 'show'){
                  this.loading_table = 'hidden';
                }

                //alert(this.mensajes);
            }else{
              //this.resetForm();
              this.loading_table = 'hidden';
              this.strEstadoOficio = "text-warning";
              // Asignamos los Nuevo Valores sin salir del PopUp Modal
              this.codOficioIntModal = codOficioInternoIn;
              this.codOficioRefModal = codOficioReferenciaIn;
              // this.idDeptoFuncionalModal = idFuncionarioAsignIn;
              this.nombre1FuncModal = nombre1FuncionarioAsign;
              this.nombre2FuncModal = nombre2FuncionarioAsign;
              this.apellido1FuncModal = apellido1FuncionarioAsign;
              this.apellido2FuncModal = apellido2FuncionarioAsign;
              this.idFuncModal = idFuncionarioAsignIn;

              this.ngOnInit();
              alert( response.msg );
              // setTimeout(function() {
              //   $('#t_and_c_m').modal('hide');
              // }, 600);
              this.closeModal('#closeModalFinCom');
            }
        }, error => {
            //Regisra cualquier Error de la Llamada a la API
            this.errorMessage = <any>error;

            //Evaluar el error
            if(this.errorMessage != null){
              console.log(this.errorMessage);
              this.mensajes = this.errorMessage;
              alert("Error en la Petición !!" + this.errorMessage);

              if(this.loading_table = 'show'){
                this.loading_table = 'hidden';
              }

            }
        }); // FIN de Llamado Ajax | Peticion a la API
   }else{
    //  alert( 'No has Aceptado el Cambio ' );
     this.checkWork = 0;
   }

 } // FIN : FND-00001.5


 /*****************************************************
 * Funcion: FND-000014
 * Fecha: 01-11-2017
 * Descripcion: Carga la Lista de los Documentos de la
 * Comunicacion de la BD que pertenecen al usaurio
 * Logeado, en el Detalle
 * Objetivo: Obtener la lista de los Documentos de las
 * Comunicaciones de la BD, Llamando a la API, por su
 * metodo ( documentos/listar-documentos ).
 ******************************************************/
 getlistaDocumentosTable() {
   // Laoding
   this.loading_table = 'show';
   this.loadTabla2 = false;
   this.paramsDocumentos.searchValueSend =  this.codOficioIntModal;
   console.log( "Entra en getlistaDocumentosTable " + this.paramsDocumentos);
   // Llamar al metodo, de Service para Obtener los Datos de la Comunicacion
   this._listasComunes.listasDocumentosToken( this.paramsDocumentos, "listar-documentos" ).subscribe(
       response => {
         // login successful so redirect to return url
         if(response.status == "error"){
           //Mensaje de alerta del error en cuestion
          this.JsonOutgetlistaDocumentosUpload = response.data;
           // Oculta los Loaders
           this.loading_table = 'hide';
           this.loadTabla2 = true;
           alert(response.msg);
         }else{
           this.JsonOutgetlistaDocumentosUpload = response.data;
           //this.valoresdataDetJson ( response.data );
           this.loading_table = 'hide';
           this.loadTabla2 = true;
           console.log( this.JsonOutgetlistaDocumentosUpload );
         }
       });
 } // FIN | FND-000014


} // Fin del Component

export interface Usuario{
   nombre:string;
   bio:string;
   img:string;
   aparicion:string;
   casa:string;
}

import { Component, OnInit } from '@angular/core';
import { RouterModule, Routes, ActivatedRoute, Router } from '@angular/router';

import { HttpModule,  Http, Response, Headers } from '@angular/http';

//Importamos los Servicios
import { InstitucionesService } from '../../../services/mantenimientos/instituciones.service'; //Servico de Tipo Institucion
import { ListasComunesService } from '../../../services/shared/listas.service'; //Servico Listas Comunes
import { UploadService } from '../../../services/shared/upload.service'; //Servico Listas Comunes


import { AppComponent } from '../../../app.component'; //Servico del Login

import { NgForm }    from '@angular/forms';

import { FormGroup, FormControl, Validators }    from '@angular/forms';

// Importamos la CLase Intituciones del Modelo
import { Instituciones } from '../../../models/mantenimientos/instituciones.model'; //Model de las Instituciones

// Declaramos las variables para jQuery
declare var jQuery:any;
declare var $:any;

@Component({
  selector: 'app-mantenimiento-instituciones',
  templateUrl: '../../../views/mantenimientos/instituciones/instituciones.component.html',
  styleUrls: ['../../../views/mantenimientos/instituciones/style.component.css'],
  providers: [InstitucionesService, ListasComunesService, UploadService]
})

export class MantenimientoInstitucionesComponent implements OnInit{
  public titulo:string = "Mantenimiento de Instituciones";

  // Instacia de la variable del Modelo
  public _modIntituciones:Instituciones;

  // Objeto que Controlara la Forma
  forma:FormGroup;

  public data;
  public errorMessage;
  public status;
  public mensajes;

  public identity;
  public token;

  //Parametro de Opcion a Ejecutar
  public optEjecutar:string;

  // Variables de Generacion de las Listas de los Dropdow
  // Llenamos las Lista del HTML
  public JsonOutgetlistaPaises:any[];
  public JsonOutgetlistaInstitucion:any[];
  public JsonOutgetlistaTipoInstitucion:any[];
  
  // parametros multimedia
  public loading  = 'show';

  // Definicion del Constructor
  constructor( private _mantIntitucionService: InstitucionesService,
               private _listasComunes: ListasComunesService,
               private _uploadService: UploadService,
               private _router: Router,
               private _route: ActivatedRoute,
               private _appComponent: AppComponent,
               private _http: Http){

  }

  // Metodo OnInit
  ngOnInit(){
    //Instancia de las Listas
    this.getlistaPaises();
        
    //Lista de Todas Instituciones
    this.getlistaInstituciones();
    
    //Lista de Tipo de Intituciones
    this.getlistaTipoInstitucion();

    // Definicion de la Insercion de los Datos de Nuevo Usuario
    this._modIntituciones = new Instituciones(0, "", "", "", 
                                            "", 0, 0, "",
                                            0, 0, 0);    
  }


  // Metodo onSubmit
  onSubmit(forma:NgForm){
      console.log(this._modIntituciones);
      //Opcion a Ejecutar
      //Asignacion de variable de Opcion a Ejecutar | optEjecutar
      //this.optEjecutar = "modificarInstitucion";
        if( this.optEjecutar == "modificarInstitucion" ){         
          //Ejecucion del Llamado a la APIRest
        this._mantIntitucionService.solitarEditInstitucion(this._modIntituciones).subscribe(
          response => {
              // Obtenemos el Status de la Peticion
              this.status = response.status;
              this.mensajes = response.msg;
              
              // Condicionamos la Respuesta
              if(this.status != "success"){
                  this.status = "error";
                  this.ngOnInit();
              }else{
                this.ngOnInit();                
                // alert(this.mensajes);
              }
          }, error => {
              //Regisra cualquier Error de la Llamada a la API
              this.errorMessage = <any>error;

              //Evaluar el error
              if(this.errorMessage != null){
                console.log(this.errorMessage);
                this.mensajes = this.errorMessage;
                alert("Error en la Petición !!" + this.errorMessage);
              }
          });
      }else if( this.optEjecutar == "nuevaInstitucion" ){        
        //Ejecucion del Llamado a la APIRest
        this._mantIntitucionService.solitarNuevaInstitucion(this._modIntituciones).subscribe(
          response => {
              // Obtenemos el Status de la Peticion
              this.status = response.status;
              this.mensajes = response.msg;

              // Condicionamos la Respuesta
              if(this.status != "success"){
                  this.status = "error";
              }else{
                this.ngOnInit();
                // alert(this.mensajes);
              }
          }, error => {
              //Regisra cualquier Error de la Llamada a la API
              this.errorMessage = <any>error;

              //Evaluar el error
              if(this.errorMessage != null){
                console.log(this.errorMessage);
                this.mensajes = this.errorMessage;
                alert("Error en la Petición !!" + this.errorMessage);
              }
          });
      }

      
  }


  /*****************************************************
  * Funcion: FND-00001
  * Fecha: 01-01-2018
  * Descripcion: Carga la Lsita de las Intituciones
  * de la SRECI.
  * Objetivo: Obtener la lista de las Intituciones
  * de la BD, Llamando a la API, por su metodo
  * (instituciones-sreci-list).
  * Params: Sin Parametros | Todas las Intituciones
  ******************************************************/
  getlistaInstituciones() {
    /*Llamar al metodo, Lista de Instituciones All, sin
    * sin Parametros*/
    this._mantIntitucionService.listaIntitucionesGet("","mantenimiento-institucion-busca").subscribe(
        response => {
          //Intituciones Lista All successful          
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion            
            this.JsonOutgetlistaInstitucion = response.data;
            alert(response.msg);
          }else{            
            this.JsonOutgetlistaInstitucion = response.data;
            console.log(this.JsonOutgetlistaInstitucion);
            this.fillDataTable();
          }
        });
  } // FIN : FND-00001



  /*****************************************************
  * Funcion: FND-00002
  * Fecha: 01-01-2018
  * Descripcion: Carga la Lista de los Tipos de Intitucion
  * Objetivo: Obtener la lista de los Tipos de Institucion
  * de la BD, Llamando a la API, por su metodo
  * (tipo-instituciones-sreci-list).
  ******************************************************/
  getlistaTipoInstitucion() {
    //Llamar al metodo, de Tipo de Instituciones
    this._listasComunes.listasComunes("","tipo-instituciones-sreci-list").subscribe(
        response => {
          // Tipo Institucion successful          
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion            
            this.JsonOutgetlistaTipoInstitucion = response.data;
            alert(response.msg);
          }else{            
            this.JsonOutgetlistaTipoInstitucion = response.data;
            console.log(this.JsonOutgetlistaTipoInstitucion);

          }
        });
  } // FIN : FND-00002


  /*****************************************************
  * Funcion: FND-00003
  * Fecha: 06-10-2017
  * Descripcion: Realiza el llenado de la Tabla con Todos
  * los Filtros
  * Params: Array de los Estado y Tipos Comunicacion de
  * los Checkbox
  ******************************************************/
  fillDataTable(){
    // this.loading = 'show';
    setTimeout(function () {
      $ (function () {
          $('#example').DataTable({
            "destroy": true,            
            "fixedHeader": true,
            "autoWidth": false,
            // Tamaño de la Pagina
            "pageLength": 5,
            // Cambiar las Propiedades de Lenguaje
            "language":{
                "lengthMenu": "Mostrar _MENU_ registros por pagina",
                "info": "Mostrando pagina _PAGE_ de _PAGES_",
                    "infoEmpty": "No hay registros disponibles",
                    "infoFiltered": "(filtrada de _MAX_ registros)",
                    "loadingRecords": "Cargando...",
                    "processing":     "Procesando...",
                    "search": "Buscar:",
                    "zeroRecords":    "No se encontraron registros coincidentes",
                    "paginate": {
                        "next":       "Siguiente",
                        "previous":   "Anterior"
                    },
            },
          });
          // this.loading = 'show';
      });
    }, 500);
    this.loading = 'hide';
  } // FIN | FND-00003


  /******************************************************
  * Funcion: FND-00004
  * Fecha: 25-09-2017
  * Descripcion: Carga la Lista de los Paises.
  * Objetivo: Obtener la lista de los Paises de la BD,
  * Llamando a la API, por su metodo (paises-list).
  *******************************************************/
  getlistaPaises() {
    //Llamar al metodo, de Login para Obtener la Identidad
    this._listasComunes.listasComunes("","paises-list").subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetlistaPaises = response.data;
            alert(response.msg);
          }else{
            //this.data = JSON.stringify(response.data);
            this.JsonOutgetlistaPaises = response.data;
          }
        });
  } // FIN : FND-00004


  /******************************************************
  * Funcion: FND-00005
  * Fecha: 01-01-2018
  * Descripcion: Funcion de Seleccion unica de Institucion
  * Objetivo: Obtener el Detalle de la Institucion Selecc.
  *******************************************************/
  datoInstitucion( idInstitucionIN:number, codInstitucionIN:string, 
                   descInstitucionIN:string, perfilInstitucionIN:string,
                   direccionInstitucionIN:string, telefonoInstitucionIN:number, celularInstitucionIN:number,
                   emailInstitucionIN:string, idPaisIN:number, idTipoInstitucionIN:number  ) {
    //Funcion de Detalle de la Institucion
    //Datos Generales
    this._modIntituciones.idInstitucion  = idInstitucionIN;
    this._modIntituciones.codInstitucion = codInstitucionIN;
    this._modIntituciones.descInstitucion = descInstitucionIN;
    this._modIntituciones.perfilInstitucion = perfilInstitucionIN;
    //Datos de Comunicacion
    this._modIntituciones.direccionInstitucion = direccionInstitucionIN;
    this._modIntituciones.telefonoInstitucion = telefonoInstitucionIN;
    this._modIntituciones.celularInstitucion = celularInstitucionIN;
    this._modIntituciones.emailInstitucion = emailInstitucionIN;
    // Datos de Relaciones de Tablas
    this._modIntituciones.idPaisInstitucion = idPaisIN;
    this._modIntituciones.idTipoInstitucion = idTipoInstitucionIN;

    //Asignacion de variable de Opcion a Ejecutar | optEjecutar
    this.optEjecutar = "modificarInstitucion";
  } // FIN : FND-00005


  /******************************************************
  * Funcion: FND-00006
  * Fecha: 01-01-2018
  * Descripcion: Funcion de Limpieza de Formulario
  * Objetivo: de Limpieza de Formulario
  *******************************************************/
  resetForm(){
    // Limpieza del Formulario
    this._modIntituciones = new Instituciones(0, "", "", "", 
    "", null, null, "",
    0, 0, 0);

    //Asignacion de variable de Opcion a Ejecutar | optEjecutar
    this.optEjecutar = "nuevaInstitucion";
  } // FIN : FND-00006



}

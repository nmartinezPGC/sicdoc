import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { RouterModule, Routes, ActivatedRoute, Router } from '@angular/router';
import { HttpModule,  Http, Response, Headers } from '@angular/http';

import { AppComponent } from '../../../app.component'; //Servico del Login

// Lirerias para el AutoComplete
import {Observable, Subscription, Subject} from 'rxjs';
import 'rxjs/add/operator/startWith';
import 'rxjs/add/operator/map';

//Importamos los Servicios
import { ListasComunesService } from '../../../services/shared/listas.service'; //Servico Listas Comunes
import { ChartsService } from '../../../services/charts/charts.service'; //Servico Charts

//Libreria toasty
import {ToastyService, ToastyConfig, ToastyComponent, ToastOptions, ToastData} from 'ng2-toasty';

@Component({
  selector: 'app-chart-home',
  templateUrl: './chart.home.component.html',
  providers: [ ListasComunesService, ChartsService ]
})
export class ChartHomeComponent implements OnInit {
  //Parametros de la Clase
  private paramsIdTipoComSend; // Parametros para el tipo de COmunicacion enviados

  // variables de Identificacion
  public identity;
  public token;

  // Propiedad de Loader
  public loading      = 'hide';
  public showChart    = 'hide';
  public alertSuccess = 'show';
  public alertError   = 'show';

  // Json del Recuento de Datos
  // Oficios
  public JsonOutgetListaOficiosIngresados:any[];
  public JsonOutgetListaOficiosPendientes:any[];
  public JsonOutgetListaOficiosFinalizados:any[];

  // Memoramdums
  public JsonOutgetListaMemosIngresados:any[];
  public JsonOutgetListaMemosPendientes:any[];
  public JsonOutgetListaMemosFinalizados:any[];

  // Correos
  public JsonOutgetListaNotasIngresados:any[];
  public JsonOutgetListaNotasPendientes:any[];
  public JsonOutgetListaNotasFinalizados:any[];

  // Llamadas
  public JsonOutgetListaCicularesIngresados:any[];
  public JsonOutgetListaCicularesPendientes:any[];
  public JsonOutgetListaCicularesFinalizados:any[];

  // FIN de Encabezados **********************

  // Propiedades de los Resumenes
  // Oficios
  public countOficiosIngresados:number;
  public countOficiosPendientes:number;
  public countOficiosFinalizados:number;

  // Memoramdums
  public countMemosIngresados:number;
  public countMemosPendientes:number;
  public countMemosFinalizados:number;

  // Correos
  public countCorreosIngresados:number;
  public countCorreosPendientes:number;
  public countCorreosFinalizados:number;

  // Llamadas
  public countLlamadasIngresados:number;
  public countLlamadasPendientes:number;
  public countLlamadasFinalizados:number;

  // FIN de Encabezados ****************************

  // Propiedades de Toasty
  getTitle(title:string, num: number): string {
        return title + ' se cerrara en ' +  num + ' segundos ';
  }

  getMessage(msg:string, num: number): string {
      // return msg + ' ' + num;
      return msg;
  }


  //Definicion de l Grafico de Barras del HOME
  public barChartOptions:any = {
    scaleShowVerticalLines: false,
    responsive: true
  };
  public barChartLabels:string[] = ['Oficios', 'Memos', 'Notas', 'Circulares'];
  public barChartType:string = 'bar';
  public barChartLegend:boolean = true;

  //colors of Grafico
  public lineChartColors:Array<any> = [
    { // gris - Ingresado
      backgroundColor: 'rgba(196, 194, 194, 0.6)',
      borderColor: 'rgba(232, 229, 229, 0.2)'
    },
    /*{ // verde - Ingresado
      backgroundColor: 'rgba(39, 174, 96, 0.6)',
      borderColor: 'rgba(30, 132, 73, 0.2)'
    },*/
    { // Amarillo - Pendiente
      backgroundColor: 'rgba(241, 196, 15, 0.6)',
      borderColor: 'rgba(247, 220, 111, 0.2)'
    },
    { // Azul - Resuelto
      backgroundColor: 'rgba(93, 173, 226, 0.6)',
      borderColor: 'rgba(46, 134, 193, 0.2)'
    },
    { // Rojo -Anulado
      backgroundColor: 'rgba(241, 148, 138, 0.8)',
      borderColor: 'rgba(192, 57, 43, 0.2)'
    }
  ];

  //Total de Comunicaciones
  public _totalOficios:number;
  public _totalMemos:number;
  public _totalNotas:number;
  public _totalCirculares:number;
  public _totalCorreos:number;
  public _totalLlamadas:number;
  public _totalReuniones:number;

  //Resumen del Chart
  public _arrayTotales:number[] ;
  public _arrayTotalesIngresado:number[] ;
  public _arrayTotalesPendiente:number[] ;
  public _arrayTotalesResuelto:number[] ;
  public _arrayTotalesAnulado:number[] ;


  // public barChartData:any[];
  //{data: [0, 0, 0, 0], label: 'Ingresado'},

  public barChartData:any[] = [
    {data: this._arrayTotales, label: 'Total'},
    {data: [0, 0, 0, 0], label: 'Pendiente'},
    {data: [0, 0, 0, 0], label: 'Resuelto'},
    {data: [0, 0, 0, 0], label: 'Anulado'}
  ];


  // Ini | Definicion del Constructor
  constructor( private _listasComunes: ListasComunesService,
               private _chartService: ChartsService,
               private _router: Router,
               private _route: ActivatedRoute,
               private _appComponent: AppComponent,
               private _http: Http,
               private changeDetectorRef: ChangeDetectorRef,
               private toastyService:ToastyService){
      //Codigo frl Constrcutor

 }

 ngOnInit(){
   // Hacemos que la variable del Local Storge este en la API
   this.identity = JSON.parse(localStorage.getItem('identity'));

   // Inicializamos los Parametros de Tipo Comunicacion
   this.paramsIdTipoComSend = {
     "idTipoCom" : "",
     "idFuncionarioAsignado" : "",
     "idTipoDoc" : "",
   }

   //Array de los Resumenes de las Comunicaciones
   this.getlistaOficosIngresados();
   //this.getlistaMemosIngresados();
   //this.getlistaNotasIngresados();
   //this.getlistaCircularesIngresados();

   // Eventos de Señaloizacion
   this.loading = 'show';
   this.showChart = 'hide';

 }

  // events
  public chartClicked(e:any):void {
    console.log(e);
  }

  public chartHovered(e:any):void {
    console.log(e);
  }

  /*
   * Cargar
  */
  public randomize():void {
    // Only Change 3 values
    let data = [
      Math.round(Math.random() * 100),
      59,
      80,
      (Math.random() * 100),
      56,
      (Math.random() * 100),
      40];

    let clone = JSON.parse(JSON.stringify(this.barChartData));

    //instacia de la valores de la data de los Resumen
    let barOficio = JSON.parse(JSON.stringify(this.JsonOutgetListaOficiosIngresados));
    let barMemo = JSON.parse(JSON.stringify(this.JsonOutgetListaMemosIngresados));
    let barNotas = JSON.parse(JSON.stringify(this.JsonOutgetListaNotasIngresados));
    let barCircular = JSON.parse(JSON.stringify(this.JsonOutgetListaCicularesIngresados));

    console.log('Imprime bar Oficio *********** ');
    console.log(barOficio[0]);

    console.log('Imprime bar Memo *********** ');
    console.log(barMemo[0]);

    console.log('Imprime bar Notas *********** ');
    console.log(barNotas[0]);

    console.log('Imprime bar Circular *********** ');
    console.log(barCircular[0]);

    console.log('Suma de totales');
    this._totalOficios = Number(barOficio[0].PENDIENTE) + Number(barOficio[0].RESUELTO) + Number(barOficio[0].ANULADO);
    this._totalMemos   = Number(barMemo[0].PENDIENTE) + Number(barMemo[0].RESUELTO) + Number(barMemo[0].ANULADO);
    this._totalNotas   = Number(barNotas[0].PENDIENTE) + Number(barNotas[0].RESUELTO) + Number(barNotas[0].ANULADO);
    this._totalCirculares = Number(barCircular[0].PENDIENTE) + Number(barCircular[0].RESUELTO) + Number(barCircular[0].ANULADO);
    console.log( this._totalOficios );

    //Data de Totales
    this._arrayTotales          = [ this._totalOficios, this._totalMemos, this._totalNotas, this._totalCirculares];
    //this._arrayTotalesIngresado = [ 10, 15, 2,  3];
    this._arrayTotalesPendiente = [ barOficio[0].PENDIENTE, barMemo[0].PENDIENTE, barNotas[0].PENDIENTE,  barCircular[0].PENDIENTE];
    this._arrayTotalesResuelto  = [ barOficio[0].RESUELTO, barMemo[0].RESUELTO, barNotas[0].RESUELTO,  barCircular[0].RESUELTO];
    this._arrayTotalesAnulado   = [ barOficio[0].ANULADO, barMemo[0].ANULADO, barNotas[0].ANULADO,  barCircular[0].ANULADO];

    //Clonamos el Dato de l Array del Chart
    clone[0].data = this._arrayTotales;
    //clone[1].data = this._arrayTotalesIngresado;
    clone[1].data = this._arrayTotalesPendiente;
    clone[2].data = this._arrayTotalesResuelto;
    clone[3].data = this._arrayTotalesAnulado;

    // Setea el Nuevo valor del Array del Chart
    this.barChartData = clone;

    // Eventos de Señaloizacion
    this.showChart = 'show';
    this.loading = 'hide';

    /**
     * (My guess), for Angular to recognize the change in the dataset
     * it has to change the dataset variable directly,
     * so one way around it, is to clone the data, change it and then
     * assign it;
     */
  }


  /*
  * Declaracion de las Funciones Principales de la Clase
  */
  /*****************************************************
  * Funcion: FND-00008
  * Fecha: 11-09-2017
  * Descripcion: Carga de los Oficios que se han ingresado
  * a la Tabla tbl_comunicacion_enc
  * Objetivo: Obtener la lista de los Oficios Ingresados
  * de la BD, Llamando a la API, por su metodo
  * (com-ingresada-list).
  ******************************************************/
  getlistaOficosIngresados() {
    //Llamar al metodo, de Contador de Comunicaciones Pendientes
    this.paramsIdTipoComSend.idTipoCom = 1;
    // this.paramsIdTipoComSend.idFuncionarioAsignado = this.identity.idFuncionario;
    // this.paramsIdTipoComSend.idTipoDoc = 1;

    // Eventos de Señaloizacion
    this.loading = 'show';

    this._chartService.arrayComunicacion( this.paramsIdTipoComSend ).subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetListaOficiosIngresados = response.data;
            //this.countOficiosIngresados = Number(this.JsonOutgetListaOficiosIngresados);
            this.addToast(4,"Error", response.msg);
            this.loading = 'hide';
          }else{
            //this.data = JSON.stringify(response.data);
            this.JsonOutgetListaOficiosIngresados = response.data;
            //this.countOficiosIngresados =  Number(this.JsonOutgetListaOficiosIngresados);
            //console.log(this.JsonOutgetListaOficiosIngresados);
            this.getlistaMemosIngresados();
          }
        });
  } // FIN : FND-00008


  /*****************************************************
  * Funcion: FND-00008
  * Fecha: 11-09-2017
  * Descripcion: Carga de los Oficios que se han ingresado
  * a la Tabla tbl_comunicacion_enc
  * Objetivo: Obtener la lista de los Oficios Ingresados
  * de la BD, Llamando a la API, por su metodo
  * (com-ingresada-list).
  ******************************************************/
  getlistaMemosIngresados() {
    //Llamar al metodo, de Contador de Comunicaciones Pendientes
    this.paramsIdTipoComSend.idTipoCom = 2;
    //this.paramsIdTipoComSend.idFuncionarioAsignado = this.identity.idFuncionario;
    //this.paramsIdTipoComSend.idTipoDoc = 1;

    this._chartService.arrayComunicacion( this.paramsIdTipoComSend ).subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetListaMemosIngresados = response.data;
            this.addToast(4,"Error", response.msg);
          }else{
            //this.data = JSON.stringify(response.data);
            this.JsonOutgetListaMemosIngresados = response.data;
            this.getlistaNotasIngresados();

            //this.randomize();
          }
        });
  } // FIN : FND-00008



  /*****************************************************
  * Funcion: FND-00008
  * Fecha: 11-09-2017
  * Descripcion: Carga de los Oficios que se han ingresado
  * a la Tabla tbl_comunicacion_enc
  * Objetivo: Obtener la lista de los Oficios Ingresados
  * de la BD, Llamando a la API, por su metodo
  * (com-ingresada-list).
  ******************************************************/
  getlistaNotasIngresados() {
    //Llamar al metodo, de Contador de Comunicaciones Pendientes
    this.paramsIdTipoComSend.idTipoCom = 3;
    //this.paramsIdTipoComSend.idFuncionarioAsignado = this.identity.idFuncionario;
    //this.paramsIdTipoComSend.idTipoDoc = 1;

    this._chartService.arrayComunicacion( this.paramsIdTipoComSend ).subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetListaNotasIngresados = response.data;
            this.addToast(4,"Error", response.msg);
          }else{
            //this.data = JSON.stringify(response.data);
            this.JsonOutgetListaNotasIngresados = response.data;

            this.getlistaCircularesIngresados();
            //this.randomize();
          }
        });
  } // FIN : FND-00008


  /*****************************************************
  * Funcion: FND-00008
  * Fecha: 11-09-2017
  * Descripcion: Carga de los Oficios que se han ingresado
  * a la Tabla tbl_comunicacion_enc
  * Objetivo: Obtener la lista de los Oficios Ingresados
  * de la BD, Llamando a la API, por su metodo
  * (com-ingresada-list).
  ******************************************************/
  getlistaCircularesIngresados() {
    //Llamar al metodo, de Contador de Comunicaciones Pendientes
    this.paramsIdTipoComSend.idTipoCom = 4;
    //this.paramsIdTipoComSend.idFuncionarioAsignado = this.identity.idFuncionario;
    //this.paramsIdTipoComSend.idTipoDoc = 1;

    this._chartService.arrayComunicacion( this.paramsIdTipoComSend ).subscribe(
        response => {
          // login successful so redirect to return url
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            this.JsonOutgetListaCicularesIngresados = response.data;
            this.addToast(4,"Error", response.msg);
          }else{
            //this.data = JSON.stringify(response.data);
            this.JsonOutgetListaCicularesIngresados = response.data;

            this.randomize();
          }
        });
  } // FIN : FND-00008



  /*****************************************************
  * Funcion: FND-000023
  * Fecha: 31-03-2018
  * Descripcion: Libreria Toasty para los mensajes
  * Objetivo: Metodo de msg en la APP
  ******************************************************/
  addToast(options:number,title:string, msg:string) {
      let interval = 1000;
      let timeoutIn = 11000;
      let seconds = timeoutIn / 1000;
      let subscription: Subscription;

       let toastOptions: ToastOptions = {
           title: this.getTitle(title,0),
           msg: this.getMessage(msg,0),
           showClose: true,
           timeout: 7000,
           theme: 'bootstrap',
           onAdd: (toast: ToastData) => {
               console.log('Toast ' + toast.id + ' has been added!');
               // Run the timer with 1 second iterval
               let observable = Observable.interval(interval);
               // Start listen seconds beat
               subscription = observable.subscribe((count: number) => {
                   // Update title of toast
                   toast.title = this.getTitle(title, ( seconds - count - 1 ));
                   // Update message of toast
                   toast.msg = this.getMessage(msg, count);
                   // Extra condition to hide Toast after 10 sec
                   if (count > 10) {
                       // We use toast id to identify and hide it
                       this.toastyService.clear(toast.id);
                   }
               });

           },
           onRemove: function(toast: ToastData) {
               console.log('Toast ' + toast.id + ' has been removed!');
               // Stop listenning
               subscription.unsubscribe();
           }
       };

       switch ( options ) {
           case 0: this.toastyService.default(toastOptions); break; //default
           case 1: this.toastyService.info(toastOptions); break; //info
           case 2: this.toastyService.success(toastOptions); break; //success
           case 3: this.toastyService.wait(toastOptions); break; //wait
           case 4: this.toastyService.error(toastOptions); break; //error
           case 5: this.toastyService.warning(toastOptions); break; //warning
       }
   } //FIN | FND-000023

}

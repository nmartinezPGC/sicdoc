import { Component, OnInit, Inject } from '@angular/core';

import { FormGroup, FormControl, Validators } from '@angular/forms';

import { RouterModule, Routes, ActivatedRoute, Router } from '@angular/router';

import { HttpModule, Http, Response, Headers, RequestOptions } from '@angular/http';

// Lirerias para el AutoComplete
import { Observable } from 'rxjs/Observable';
import 'rxjs/add/operator/startWith';
import 'rxjs/add/operator/map';

//Importamos los Servicios
import { ListasComunesService } from '../../services/shared/listas.service'; //Servico Listas Comunes
import { UploadService } from '../../services/shared/upload.service'; //Servico Carga de Arhcivos
import { ContactosService } from '../../services/contactos/contacto.service'; //Servico La Clase Contactos

import { AppComponent } from '../../app.component'; //Servico del Login

import { NgForm } from '@angular/forms';

// Importamos la CLase Usuarios del Modelo
import { Contactos } from '../../models/contactos/contacto.model'; // Servico del Login

import { CompleterService, CompleterData, CompleterItem } from 'ng2-completer';

import 'rxjs/Rx';

// Declaramos las variables para jQuery
declare var jQuery: any;
declare var $: any;

@Component({
  selector: 'app-contactos',
  templateUrl: '../../views/contactos/contactos.component.html',
  styleUrls: ['../../views/contactos/contactos.component.css'],
  providers: [ContactosComponent, ContactosService, ListasComunesService, UploadService]
})
export class ContactosComponent implements OnInit {
  // Datos Generales de la Clase
  public titulo = 'Contactos';
  public newTittle = 'Ingreso de Contacto';

  public urlConfigLocal: string;
  public urlResoursesLocal: string;
  public urlComplete: string;

  protected searchStr: string;
  protected searchStrFunc: string;
  protected captain: string;
  protected dataService: CompleterData;
  protected dataServiceFunc: CompleterData;


  protected selectedInstitucion: string;
  protected selectedFuncionario: string;
  // variables del localStorage
  public identity;
  public token;
  public userId;

  // Variables de Captura de msg
  public status;
  public mensajes;
  public errorMessage;

  // Instacia del Objeto Model de la Clase
  public consultaContactos: Contactos;

  public fechaHoy: Date = new Date();

  // parametros multimedia
  public loading = 'show';
  public loading_table = 'hide';
  public loading_tr = 'hide';
  public loading_tableIn = 'hide';

  public loadTabla1: boolean = false;
  public loadTabla2: boolean = false;

  // Parametros de los Json de la Aplicacion
  public JsonOutgetlistaContactosDet: any[];
  public JsonOutgetlistaContactosEnc: any[];
  public JsonOutgetlistaInstitucion: any[];
  public JsonOutgetlistaFuncionarios: any[];
  public JsonOutgetlistaPaises: any[];

  public JsonLimpio;
  public paramsDocs;

  public JsonOutgetlistaTipoContacto: any[];
  public JsonOutgetlistaTratoContacto: any[];


  // Ini | Definicion del Constructor
  constructor(private _listasComunes: ListasComunesService,
    private _router: Router,
    private _consultaContactoService: ContactosService,
    private _uploadService: UploadService,
    private _route: ActivatedRoute,
    private _appComponent: AppComponent,
    private _http: Http,
    private completerService: CompleterService) {
    // Ejecucion de la Lista de Contactos
    this.getlistaContactosTableFind();
    // Llenado de la Tabla de Encabezado
    // this.fillDataTable();

    // Seteo de la Ruta de la Url Config
    this.urlResoursesLocal = this._consultaContactoService.urlResourses;
    this.urlConfigLocal = this._consultaContactoService.url;
    this.urlComplete = this.urlResoursesLocal + 'uploads/contactos/perfiles/';
  } // Fin | Definicion del Constructor


  /*****************************************************
  * Funcion: ngOnInit
  * Fecha: 11-10-2017
  * Descripcion: Metodo inicial del Programa
  ******************************************************/
  ngOnInit() {
    // Hacemos que la variable del Local Storge este en la API
    // this.identity = JSON.parse(localStorage.getItem('identity'));
    // this.userId = this.identity.sub;
    this.searchStr = '';
    this.searchStrFunc = '';

    // Iniciamos los Parametros de Documentos
    this.paramsDocs = {
      'nombreDocumento': '',
      'optDocumento': ''
    };

    // Definicion de la Insercion de los Datos de Nuevo Contacto
    this.consultaContactos = new Contactos(0, null, null, null, null, null,
      null, null,
      null, null, null, null,
      null, null, 0, 0, 0, null, null,
      null, null, null);

    // Ejecucion de la Lista de Instituciones
    this.getlistaInstituciones();

    // Ejecucion de la Lista Funcionarios
    this.getlistaFuncionariosSreci();

    // Tipo de Contacto
    this.getlistaTipoContacto();

    // Trato de COntacto
    this.getlistaTratoContacto();

    // Pais de Contacto
    this.getlistaPaises();
    // Ejecucion de la Lista de Contactoss
    // this.getlistaContactosTableFind();

    // Llenado de la Tabla de Encabezado
    // this.fillDataTable();
  } // Fin | ngOnInit


  closeModal(nameBotton) {
    setTimeout(function () {
      // $('#t_and_c_m').modal('hide');
      $(nameBotton).click();
    }, 600);
  }


  /*****************************************************
  * Funcion: onSubmit
  * Fecha: 11-10-2017
  * Descripcion: Metodo que envia la Informacion
  ******************************************************/
  // tslint:disable-next-line: member-ordering
  public filesToUpload: Array<File>;
  // tslint:disable-next-line: member-ordering
  public resultUpload;

  onSubmit(forma: NgForm) {
    // Parseo de parametros que no se seleccionan
    // this.filesToUpload = <Array<File>>this.consultaContactos.imgDocumento.target.files;
    // Parametro para documento Seleccionado
    this.loading_table = 'show';
    console.log(this.consultaContactos);
    this._consultaContactoService.newContact(this.consultaContactos, '', '').subscribe(
      response => {
        // Obtenemos el Status de la Peticion
        this.status = response.status;
        this.mensajes = response.msg;

        // Condicionamos la Respuesta
        // alert('Paso 1 ' + this.status);
        if (this.status !== 'success') {
          this.status = 'error';
          this.mensajes = response.msg;
          if (this.loading_table = 'show') {
            this.loading_table = 'hidden';
          }
          alert('Error Data ' + this.mensajes);
        } else {
          // this.resetForm();
          this.loading = 'hidden';
          // this.ngOnInit();
          window.location.reload();
          // Ejecucion de la Lista de Contactos
          // this.getlistaContactosTableFind();
          // Llenado de la Tabla de Encabezado
          // this.fillDataTable();

          this.loading_table = 'hide';
          alert(this.mensajes);
          setTimeout(function () {
            $('#t_and_c_m').modal('hide');
          }, 600);
        }
      }, error => {
        // Regisra cualquier Error de la Llamada a la API
        this.errorMessage = <any>error;

        // Evaluar el error
        if (this.errorMessage != null) {
          console.log(this.errorMessage);
          this.mensajes = this.errorMessage;
          alert('Error en la Petición !!' + this.errorMessage);

          if (this.loading = 'show') {
            this.loading = 'hidden';
          }
        }
      });
  } // Fin | Metodo onSubmit


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

    this._consultaContactoService.contactoFindAll(this.consultaContactos).subscribe(
      response => {
        // login successful so redirect to return url
        if (response.status == 'error') {
          // Mensaje de alerta del error en cuestion
          this.JsonOutgetlistaContactosEnc = response.data;

          // Mensaje de Alerta de no enconrar los Datos
          // Dispose de los Loaders
          this.loading = 'hidden';
          this.loadTabla1 = true;
        } else {
          this.JsonOutgetlistaContactosEnc = response.data;
          // this.valoresdataDetJson ( response.data );
          this.JsonLimpio = this.JsonOutgetlistaContactosEnc;

          this.loading = 'hidden';
          this.loadTabla1 = true;
          // Llenado de la Tabla de Encabezado
          this.fillDataTable();
          // console.log(this.dataServiceFunc);
        }
      });
  } // FIN | FND-00001


  /*****************************************************
  * Funcion: FND-00001.1
  * Fecha: 31-07-2017
  * Descripcion: Carga la Lista de las Instituciones
  * Objetivo: Obtener la lista de los Tipos de usuarios
  * de la BD, Llamando a la API, por su metodo
  * (instituciones-sreci-list).
  ******************************************************/
  getlistaInstituciones() {
    // Llamamos al Servicio que provee todas las Instituciones
    this._listasComunes.listasComunes('', 'instituciones-sreci-list').subscribe(
      response => {
        // login successful so redirect to return url
        if (response.status == 'error') {
          // Mensaje de alerta del error en cuestion
          this.JsonOutgetlistaInstitucion = response.data;
          alert(response.msg);

        } else {
          this.JsonOutgetlistaInstitucion = response.data;

          // tslint:disable-next-line: max-line-length
          this.dataService = this.completerService.local(this.JsonOutgetlistaInstitucion, 'descInstitucion,perfilInstitucion', 'descInstitucion,perfilInstitucion');
          // console.log(response.data);
        }
      });
  } // FIN : FND-00001.1


  /*****************************************************
  * Funcion: FND-00001.2
  * Fecha: 12-10-2017
  * Descripcion: Carga la Lista de Todos los Funcionarios
  * Objetivo: Obtener la lista de los Funcionarios de la
  * de la BD, Llamando a la API, por su metodo
  * ( listas/funcionarios-list-all ).
  ******************************************************/
  getlistaFuncionariosSreci() {
    // Llamamos al Servicio que provee todas las Instituciones
    this._listasComunes.listasComunes('', 'funcionarios-list-all').subscribe(
      response => {
        // login successful so redirect to return url
        if (response.status == 'error') {
          // Mensaje de alerta del error en cuestion
          this.JsonOutgetlistaFuncionarios = response.data;
          alert(response.msg);

        } else {
          this.JsonOutgetlistaFuncionarios = response.data;
          this.dataServiceFunc = this.completerService.local(this.JsonOutgetlistaFuncionarios, 'nombre1Funcionario,apellido1Funcionario',
            'nombre1Funcionario,apellido1Funcionario,apellido2Funcionario,celularFuncionario,emailFuncionario');
          // console.log(response.data);
        }
      });
  } // FIN : FND-00001.2


  /*****************************************************
  * Funcion: FND-00002
  * Fecha: 06-10-2017
  * Descripcion: Realiza el llenado de la Tabla con Todos
  * los Filtros
  * Params: none
  ******************************************************/
  fillDataTable() {
    // Trabaja con las Fechas
    // Actualiza el valor de la Secuencia
    const mesAct = this.fechaHoy.getMonth() + 1;

    // Mes Actual *************************
    let final_month = mesAct.toString();
    if (mesAct <= 9) {
      final_month = '0' + final_month;
    }

    // Dia del Mes *************************
    const day = this.fechaHoy.getDate(); // Dia
    let final_day = day.toString();
    if (day <= 9) {
      final_day = '0' + final_day;
    }

    // Seteo de la Fecha Final
    const newFecha = this.fechaHoy.getFullYear() + '-' + final_month + '-' + final_day;

    setTimeout(function () {
      $(function () {
        $('#example').DataTable({
          // Refresca la Data y Borra de Memoria los Datos anteriores
          destroy: true,
          retrieve: true,
          // Barra Vertical de la Tabla
          scrollY: '450px',
          scrollX: true,
          scrollCollapse: true,

          /*"fixedHeader": true,
          "autoWidth": false,*/
          // Tamaño de la Pagina
          'pageLength': 5,
          // Cambiar las Propiedades de Lenguaje
          'language': {
            'lengthMenu': 'Mostrar _MENU_ registros por pagina',
            'info': 'Mostrando pagina _PAGE_ de _PAGES_',
            'infoEmpty': 'No hay registros disponibles',
            'infoFiltered': '(filtrada de _MAX_ registros)',
            'loadingRecords': 'Cargando...',
            'processing': 'Procesando...',
            'search': 'Buscar:',
            'zeroRecords': 'No se encontraron registros coincidentes',
            'paginate': {
              'next': 'Siguiente',
              'previous': 'Anterior'
            },
          },
          fixedColumns: {
            leftColumns: 2
          },

          // Parametro de Botones
          dom: 'Bfrtip',
          // Botones
          buttons: [
            // Boton de excelHtml5
            {
              extend: 'excelHtml5',
              exportOptions: {
                // columns: ':visible',
                columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
                modifier: {
                  selected: null
                }
              },
              title: 'Informe de Contactos' + ' / ' + newFecha,
              text: 'Exportar en Excel',
              customize: function (xlsx) {
                const sheet = xlsx.xl.worksheets['sheet1.xml'];
                $('row:first c', sheet).attr('s', '42');
              },
            },

            // Boton de Imprimir
            {
              extend: 'print',
              utoPrint: false,
              exportOptions: {
                // columns: ':visible',
                columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
                modifier: {
                  selected: null
                }
              },
              customize: function (win) {
                $(win.document.body).find('table').addClass('display').css('font-size', '10px');
                $(win.document.body).find('tr:nth-child(odd) td').each(function (index) {
                  $(this).css('background-color', '#D0D0D0');
                });
                $(win.document.body).find('h1').css('text-align', 'center');
              },
              text: 'Imprimir Todos',
              message: 'Listado de Contactos',
              title: 'Informe de Contactos' + ' / ' + newFecha,
              orientation: 'landscape',
              pageSize: 'A4',
            },

            // Boton de Importar a PDF
            {
              extend: 'pdfHtml5',
              exportOptions: {
                // columns: ':visible',
                columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
                modifier: {
                  selected: null
                }
              },
              orientation: 'landscape',
              pageSize: 'A4',
              title: 'Informe de Contactos' + ' / ' + newFecha,
              text: 'Exportar a PDF',
              messageTop: 'PDF de Contactos.'
            },
          ],
          // Selecciona las Filas
          select: true
        });
        this.loading_tableIn = 'show';
      });
    }, 500);
    // this.loading_tableIn = 'hide';
  } // FIN | FND-00002


  /*****************************************************
  * Funcion: FND-00003
  * Fecha: 11-10-2017
  * Descripcion: Funcion para AutoCompletar y sacar el
  * Id de la Data de la Tabla tblInstituciones
  * Params: $event
  ******************************************************/
  protected onSelected(item: CompleterItem) {
    this.selectedInstitucion = item ? item.originalObject.idInstitucion : '';
    // Seteamos y Parseamos a Int el idInstitucion
    // tslint:disable-next-line: radix
    this.consultaContactos.idInstitucion = parseInt(this.selectedInstitucion);
  } // FIN | FND-00003


  /*****************************************************
  * Funcion: FND-00003.1
  * Fecha: 12-10-2017
  * Descripcion: Funcion para AutoCompletar y sacar el
  * Id de la Data de la Tabla TblFunionarios
  * Params: $event
  ******************************************************/
  protected onSelectedFunc(item: CompleterItem) {
    this.selectedFuncionario = item ? item.originalObject.idFuncionario : '';
    // Seteamos y Parseamos a Int el idContactoSreci
    // tslint:disable-next-line: radix
    this.consultaContactos.idContactoSreci = parseInt(this.selectedFuncionario);
  } // FIN | FND-00003.1


  /*****************************************************
  * Funcion: FND-00004
  * Fecha: 29-07-2017
  * Descripcion: Carga la Imagen de usuario desde el File
  * Objetivo: Obtener la imagen que se carga desde el
  * control File de HTML
  * (fileChangeEvent).
  ******************************************************/
  fileChangeEvent(fileInput: any, optDoc) {
    // console.log('Evento Chge Lanzado'); , codDocumentoIn:string
    this.filesToUpload = <Array<File>>fileInput.target.files;

    // Direccion del Metodo de la API
    const url = this.urlConfigLocal + '/contactos/contacto-upload-perfil';
    // let url = "http://localhost/sicdoc/symfony/web/app_dev.php/contactos/contacto-upload-perfil";
    // let url = "http://172.17.0.250/sicdoc/symfony/web/app.php/contactos/contacto-upload-perfil";

    // Variables del Metodo
    let error: string;
    let status: string;
    let codigoSec: string;


    // Seteamos el valore del Nombre del Documento
    codigoSec = this.consultaContactos.nombre1Contacto + ' ' + this.consultaContactos.apellido1Contacto;

    this.paramsDocs.nombreDocumento = this.consultaContactos.nombre1Contacto + ' '
      + this.consultaContactos.apellido1Contacto;

    this.paramsDocs.optDocumento = optDoc;

    const sendParms = 'json=' + this.paramsDocs;

    console.log(this.paramsDocs);

    // Ejecutamos el Servicio con los Parametros
    this._uploadService.makeFileRequestNoToken(url, ['name_pdf', codigoSec], this.filesToUpload).then(
      (result) => {
        this.resultUpload = result;
        status = this.resultUpload.status;
        console.log(this.resultUpload);
        if (status === 'error') {
          console.log(this.resultUpload);
          alert(this.resultUpload.msg);
        }
        // alert(this.resultUpload.data);
        // Evalua si Sube Imagen / Documento
        if (optDoc === 1) {
          this.consultaContactos.pdfDocumento = this.resultUpload.data;
        } else if (optDoc === 2) {
          this.consultaContactos.imgDocumento = this.resultUpload.data;
        }
        // this.mensajes = this.resultUpload.msg;
      },
      // tslint:disable-next-line: no-shadowed-variable
      (error) => {
        alert(error);
        console.log(error);
      });
  } // FIN : FND-00004


  /*****************************************************
  * Funcion: FND-00005
  * Fecha: 13-10-2017
  * Descripcion: Descarga el PDF
  * Objetivo: Descarga el pdf del Contacto
  * ( downloadDocumento ).
  ******************************************************/
  downloadDocumento(downloadUrl) {
    const url = window.URL.createObjectURL(this.urlComplete + 'uploads/contactos/perfiles/');
    // var url= window.URL.createObjectURL("http://localhost/sicdoc/symfony/web/uploads/contactos/perfiles/");
    // var url= window.URL.createObjectURL("http://172.17.0.250/sicdoc/symfony/web/uploads/contactos/perfiles/");

    const url2 = 'http://localhost/sicdoc/symfony/web/uploads/contactos/perfiles/';
    window.open(url + downloadUrl);
  } // FIN | FND-00005


  /******************************************************
  * Funcion: FND-00004
  * Fecha: 25-09-2017
  * Descripcion: Carga la Lista de los Tipo de Contacto.
  * Objetivo: Obtener la lista de los Tipo de Contacto de la BD,
  * Llamando a la API, por su metodo (tipo-contacto-list).
  *******************************************************/
  getlistaTipoContacto() {
    // Llamar al metodo, de Login para Obtener la Identidad
    this._listasComunes.listasComunes('', 'tipo-contacto-list').subscribe(
      response => {
        // login successful so redirect to return url
        if (response.status == 'error') {
          // Mensaje de alerta del error en cuestion
          this.JsonOutgetlistaTipoContacto = response.data;
          alert(response.msg);
        } else {
          // this.data = JSON.stringify(response.data);
          this.JsonOutgetlistaTipoContacto = response.data;
          // console.log(this.JsonOutgetlistaTipoContacto);
        }
      });
  } // FIN : FND-00004



  /******************************************************
  * Funcion: FND-00004
  * Fecha: 25-09-2017
  * Descripcion: Carga la Lista de los Tipo de Contacto.
  * Objetivo: Obtener la lista de los Tipo de Contacto de la BD,
  * Llamando a la API, por su metodo (tipo-contacto-list).
  *******************************************************/
  getlistaTratoContacto() {
    // Llamar al metodo, de Login para Obtener la Identidad
    this._listasComunes.listasComunes('', 'trato-contacto-list').subscribe(
      response => {
        // login successful so redirect to return url
        if (response.status === 'error') {
          // Mensaje de alerta del error en cuestion
          this.JsonOutgetlistaTratoContacto = response.data;
          // alert(response.msg);
        } else {
          // this.data = JSON.stringify(response.data);
          this.JsonOutgetlistaTratoContacto = response.data;
          // console.log(this.JsonOutgetlistaTratoContacto);
        }
      });
  } // FIN : FND-00004


  /******************************************************
  * Funcion: FND-00005
  * Fecha: 25-09-2017
  * Descripcion: Carga la Lista de los Paises.
  * Objetivo: Obtener la lista de los Paises de la BD,
  * Llamando a la API, por su metodo (paises-list).
  *******************************************************/
  getlistaPaises() {
    // Llamar al metodo, de Login para Obtener la Identidad
    this._listasComunes.listasComunes('', 'paises-list').subscribe(
      response => {
        // login successful so redirect to return url
        if (response.status === 'error') {
          // Mensaje de alerta del error en cuestion
          this.JsonOutgetlistaPaises = response.data;
          // this.addToast(4, 'Error', response.msg);
        } else {
          this.JsonOutgetlistaPaises = response.data;
        }
      });
  } // FIN : FND-00005


} // FIN | Clase

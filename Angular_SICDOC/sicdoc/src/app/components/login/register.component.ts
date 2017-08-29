import { Component, OnInit } from '@angular/core';
import { RouterModule, Routes, ActivatedRoute, Router } from '@angular/router';

import { HttpModule,  Http, Response, Headers } from '@angular/http';

//Importamos los Servicios
import { LoginService } from '../../services/login/login.service'; //Servico del Login
import { ListasComunesService } from '../../services/shared/listas.service'; //Servico Listas Comunes

import { AppComponent } from '../../app.component'; //Servico del Login

// Importamos la CLase Usuarios del Modelo
import { Usuarios } from '../../models/usuarios/usuarios.model'; //Servico del Login

//Importamos los Javascript
//import '../../views/login/register.component';

@Component({
  selector: 'app-register',
  templateUrl: '../../views/login/register.component.html',
  styleUrls: ['../../views/login/style.component.css'],
  providers: [LoginService, ListasComunesService]
})

export class RegisterComponent implements OnInit{
  public titulo:string = "Registro de Usuarios";

  public user:Usuarios;

  public data;

  // Variables de Generacion de las Listas de los Dropdow
  public JsonOutgetlistaEstados:any[];
  public JsonOutgetlistaTipoFuncionario:any[];
  public JsonOutgetlistaDeptosFuncionales:any[];
  // public JsonOut:any[];


  // Definicion del Constructor
  constructor(private _loginService: LoginService,
               private _listasComunes: ListasComunesService,
               private _router: Router,
               private _route: ActivatedRoute,
               private _appComponent: AppComponent, private _http: Http){  }

  // Metodo OnInit
  ngOnInit(){
    // Inicializacion de las Listas
    this.getlistaEstados();
    this.getlistaTipoFuncionario();
    this.getlistaDeptosFuncionales();
    //this.user = new Usuarios(1, "", "", "", "", "",   "", "", "", "", "12/12/2017",  1, 1, 1, 1);
    // this.loadScript('../assets/js/register.component.js');
  }

  /****************************************************
  * Funcion: FND-00001
  * Fecha: 28-07-2017
  * Descripcion: que Carga, Los Script de la Pagina
  *****************************************************/
  public loadScript(url) {
    console.log('preparing to load...')
    let node = document.createElement('script');
    node.src = url;
    node.type = 'text/javascript';
    document.getElementsByTagName('head')[0].appendChild(node);
  } // FIN : 00001


  /*****************************************************
  * Funcion: FND-00002
  * Fecha: 28-07-2017
  * Descripcion: Carga la Lsita de los Estados de la BD
  ******************************************************/
  getlistaEstados() {
    //Llamar al metodo, de Login para Obtener la Identidad
    this._listasComunes.listasComunes("","estadosUsuarioList").subscribe(
        response => {
          // login successful so redirect to return url
          //alert(response.status);
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            alert("Msg Error");
            alert(response.msg);
          }else{
            //alert("Msg Bien");
            //this.data = JSON.stringify(response.data);
            this.JsonOutgetlistaEstados = response.data;
            console.log(response.data);
            //alert(this.data);
      //      JsonOut[] = this.data;
          }
        });
  } // FIN : FND-00002



  /******************************************************
  * Funcion: FND-00003
  * Fecha: 28-07-2017
  * Descripcion: Carga la Lista de los Tipos de
  * Funcionarios
  *******************************************************/
  getlistaTipoFuncionario() {
    //Llamar al metodo, de Login para Obtener la Identidad
    this._listasComunes.listasComunes("","tipoFuncionarioList").subscribe(
        response => {
          // login successful so redirect to return url
          //alert(response.status);
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            alert("Msg Error");
            alert(response.msg);
          }else{
            //alert("Msg Bien");
            //this.data = JSON.stringify(response.data);
            this.JsonOutgetlistaTipoFuncionario = response.data;
            console.log(response.data);
            //alert(this.data);
      //      JsonOut[] = this.data;
          }
        });
  } // FIN : FND-00003


  /*****************************************************
  * Funcion: FND-00004
  * Fecha: 28-07-2017
  * Descripcion: Carga la Lsita de los Departamentos
  * Funcionales de la SRECI
  ******************************************************/
  getlistaDeptosFuncionales() {
    //Llamar al metodo, de Login para Obtener la Identidad
    this._listasComunes.listasComunes("","deptoFuncionalList").subscribe(
        response => {
          // login successful so redirect to return url
          //alert(response.status);
          if(response.status == "error"){
            //Mensaje de alerta del error en cuestion
            alert("Msg Error");
            alert(response.msg);
          }else{
            //alert("Msg Bien");
            //this.data = JSON.stringify(response.data);
            this.JsonOutgetlistaDeptosFuncionales = response.data;
            console.log(response.data);
            //alert(this.data);
      //      JsonOut[] = this.data;
          }
        });
  } // FIN : FND-00004

}

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

  }

  //Metodo para Obtener el Array
  getUsuarios():Usuario[]{
    return this.usuarios;
  }

} // Fin del Component

export interface Usuario{
   nombre:string;
   bio:string;
   img:string;
   aparicion:string;
   casa:string;
}

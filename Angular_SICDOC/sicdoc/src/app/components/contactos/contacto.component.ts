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

declare var $:any;

@Component({
  selector: 'app-contactos',
  templateUrl: '../../views/contactos/contactos.component.html',
  styleUrls: ['../../views/contactos/contactos.component.css'],
  providers: [ ContactosComponent , ContactosService , ListasComunesService, UploadService]
})
export class ContactosComponent implements OnInit {

  constructor( ) {}

  ngOnInit() {

  }

} // FIN | Clase

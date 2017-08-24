import { Injectable } from '@angular/core';

@Injectable()
export class UsuariosService {

  private usuarios:Usuario[] = [{
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


  constructor() {
    console.log("Servicio creado");
   }

   //Metodo para Obtener el Array
   getUsuarios():Usuario[]{
     return this.usuarios;
   }

}

export interface Usuario{
   nombre:string;
   bio:string;
   img:string;
   aparicion:string;
   casa:string;
}

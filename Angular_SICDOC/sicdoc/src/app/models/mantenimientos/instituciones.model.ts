export class Instituciones {

  //Constructor de la Clase
    constructor(
      public idInstitucion:number,
      // Datos Generales
      public codInstitucion:string,
      public descInstitucion:string,
      public perfilInstitucion:string,
      
      // Datos de Comunicacion
      public direccionInstitucion:string,
      public telefonoInstitucion:number,
      public celularInstitucion:number,
      public emailInstitucion:string,
      
      // Datos de Relaciones de Tablas
      public idPaisInstitucion:number,
      public idTipoInstitucion:number,
      public idUsuarioCreador:number,
    
    ){}

}

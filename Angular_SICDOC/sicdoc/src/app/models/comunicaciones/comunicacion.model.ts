export class Comunicaciones{

  //Constructor de la Clase
    constructor(
      public id:number,
      // Datos Generales
      public codCorrespondencia:string,
      public descCorrespondencia:string,
      public codReferenciaSreci:string,

      // Datos de Relaciones de Tablas
      public idInstitucion:number,
      public idUsuario:number,
      public idEstado:number,
      public idDireccionSreci:number,

      // Datos de fechas
      public fechaMaxEntrega:string,
      public fechaModificacion:string,

      // Datos Externos a la Tablas
      public idPais:number

    ){}

}

export class Comunicaciones{

  //Constructor de la Clase
    constructor(
      public id:number,
      // Datos Generales
      public codCorrespondencia:string,
      public descCorrespondencia:string,
      public temaCorrespondencia:string,
      public codReferenciaSreci:string,

      // Datos de Relaciones de Tablas
      public idInstitucion:number,
      public idUsuario:number,
      public idEstado:number,
      public idDireccionSreci:number,

      // Datos de fechas
      public fechaMaxEntrega:Date,
      public fechaModificacion:Date,

      // Datos Externos a la Tablas
      public idPais:number,
      public idTipoInstitucion:number,

      //Datos para Ingresar la Primera Accion
      public idDeptoFuncional:number


    ){}

}

export class Comunicaciones{

  //Constructor de la Clase
    constructor(
      public id:number,
      // Datos Generales
      public codCorrespondencia:string, // Se da por La AutoSecuencia
      public descCorrespondencia:string,
      public temaCorrespondencia:string,
      public codReferenciaSreci:string,

      // Datos de Relaciones de Tablas
      public idInstitucion:number,
      public idUsuario:number,
      public idEstado:number,
      public idDireccionSreci:number,
      public idTipoDocumento:number,

      // Datos de fechas
      public fechaMaxEntrega:string,
      public fechaModificacion:string,

      // Datos Externos a la Tablas
      public idPais:number,
      public idTipoInstitucion:number,

      //Datos para Ingresar la Primera Accion
      public idDeptoFuncional:number,

      // Secuenciales de Tablas
      public secuenciaComunicacionIn:string


    ){}

}

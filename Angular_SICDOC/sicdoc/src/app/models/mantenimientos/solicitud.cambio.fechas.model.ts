export class SolicitudCambioFecha {

  //Constructor de la Clase
    constructor(
      // Datos Generales
      public codCorrespondencia:string,
      public codCorrespondenciaExt:string,
      public justifiacionCom:string,

      // Usuario que creo la Comunicacion
      public idUserCreador:number,
      public emailUserCreador:string,

      public fechaMaxEntrega:string,
      public fechaMaxEntregaNew:string,

      // Datos de la Consulta
      public temaComunicacion:string,
      public descComunicacion:string


    ){}

}

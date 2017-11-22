export class SolicitudCambioFecha {

  //Constructor de la Clase
    constructor(
      // Datos Generales
      public codCorrespondencia:string,
      public justifiacionCom:string,
      public idUserCreador:number,

      public fechaMaxEntrega:string,

      // Datos de la Consulta
      public temaComunicacion:string,
      public descComunicacion:string,


    ){}

}

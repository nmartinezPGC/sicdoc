export class EntradaCorrespondenciaModel{

  //Constructor de la Clase
    constructor(
      public idCorrespondencia:number,
      // Datos Generales
      public codCorrespondencia:string, // Ingresado por el Usuario
      public codReferenciaSreci:string,
      public descCorrespondencia:string,
      public temaCorrespondencia:string,

      // Datos de Relaciones de Tablas
      public idPais:number,
      public idInstitucion:number,
      public idTipoInstitucion:number,

      public idUsuario:string, // Tipo de Dato String, porque no se selecciona
      public idEstado:string, // Tipo de Dato String, porque no se selecciona
      public idDireccionSreci:number,
      public idTipoDocumento:string, // Tipo de Dato String, porque no se selecciona

      // Observaciones Iniciales
      public observaciones:string,

      // Documentos
      public pdfDocumento,

    ){}

}

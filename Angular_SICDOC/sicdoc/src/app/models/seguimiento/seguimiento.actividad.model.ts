export class SeguimientoActividad{

  //Constructor de la Clase
    constructor(
      // Campos de la Tabla
      public codOficioInterno:string,
      public codOficioExterno:string,
      public searchValueSend:string,

      // Funcionario Asignado
      public idFuncionarioAsignado:number,

      //Datos de la Busqueda
      public optUserFind:string,
      public optUserFindId:number,

      // Parametros para los Documentos
      public idCorrespondenciaEnc:number,


    ){}

}

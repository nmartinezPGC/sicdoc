export class ConsultaMaster{

  //Constructor de la Clase
    constructor(
      // Campos de la Tabla
      public codOficioInterno:string,
      public codOficioExterno:string,
      public searchValueSend:string,

      // Funcionario Asignado
      public idFuncionarioAsignado:number,
      public idCorrespondenciaEnc:number,

      //Datos de la Busqueda
      public optUserFind:string,
      public optUserFindId:number,
      public idEstado:any[],
      public idTipoComunicacion:any[],

    ){}

}

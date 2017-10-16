export class ReporteGeneral{

  //Constructor de la Clase
    constructor(
      // Campos de la Tabla
      public fechaInicial:Date,
      public fechaFinal:Date,
      // public searchValueSend:string,

      // Funcionario Asignado
      public idFuncionarioAsignado:number,
      public idDireccion:number,

      //Datos de la Busqueda
      // public optUserFind:string,
      public idEstadoComunicacion:any[],
      public idTipoComunicacion:any[],

      // Condiciones de los envio de Parametros
      public optArrayEstados:number,
      public optArrayTipos:number
    ){}

}

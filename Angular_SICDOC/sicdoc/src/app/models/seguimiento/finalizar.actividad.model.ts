export class FinalizarActividad{

  //Constructor de la Clase
    constructor(
      // Campos de la Tabla
      public codOficioInterno:string,
      public codOficioExterno:string,
      public codOficioRespuesta:string,
      public descripcionOficio:string,
      public actividadOficio:string,

      // Funcionario Asignado
      public idFuncionarioAsigmado:number,
      public nombre1FuncionarioAsigmado:string,
      public nombre2FuncionarioAsigmado:string,
      public apellido1FuncionarioAsigmado:string,
      public apellido2FuncionarioAsigmado:string,
      public temaOficio:string,

      //Datos de la Busqueda
      public buscadorOficio:string,

      //Datos de la Tabla
      public idDeptoFuncional:number,
      public idEstadoAsigna:number,

      // Secuenciales
      public secuenciaComunicacionIn:string,
      public codCorrespondenciaDet:string,
      public codCorrespondenciaNewOfi:string,
      public codCorrespondenciaRespAct:string,
      public secuenciaComunicacionDet:number,
      public secuenciaComunicacionNewOfi:number,
      public secuenciaComunicacionNewOfiAct:number,
      public secuenciaComunicacionNewRespActividad:number


    ){}

}

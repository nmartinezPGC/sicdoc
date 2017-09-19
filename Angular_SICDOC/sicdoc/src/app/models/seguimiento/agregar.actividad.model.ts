export class AgregarActividad{

  //Constructor de la Clase
    constructor(
      // Campos de la Tabla
      public codOficioInterno:string,
      public codOficioExterno:string,

      // Funcionario Asignado
      public idFuncionarioAsigmado:number,
      public nombre1FuncionarioAsigmado:string,
      public nombre2FuncionarioAsigmado:string,
      public apellido1FuncionarioAsigmado:string,
      public apellido2FuncionarioAsigmado:string,

      //Datos de la Busqueda
      public buscadorOficio:string,

      //Datos de la Tabla
      public idDeptoFuncional:number

    ){}

}

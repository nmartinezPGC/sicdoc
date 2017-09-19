export class Usuarios{

  //Constructor de la Clase
    constructor(
      public id:number,
      // Datos Generales
      public codUsuario:string,
      public primerNombre:string,
      public segundoNombre:string,
      public primerApellido:string,
      public segundoApellido:string,

      // Credenciales de Usuarios
      public emailUsuario:string,
      public inicialesUsuario:string,
      public passwordUsuairo:string,
      // public imagenUsuario:string,
      //public fechaCreacion:string,

      // Datos de Relaciones de Tablas
      public idEstado:string,
      public idTipoFuncionario:number,
      public idDeptoFuncional:number,
      public idDireccionSreci:number,
      public idTipoUsuario:number,

      //Variables de Validacion
      public passwordConfirmation:string

      // Variables para la Direccion


    ){}

}

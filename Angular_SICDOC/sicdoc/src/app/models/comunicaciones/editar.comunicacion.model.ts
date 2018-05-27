export class EditarComunicacionModel{

  //Constructor de la Clase
    constructor(
      public idComunicacion:number,

      // Datos Generales
      public codCorrespondencia:string, // Se da por La AutoSecuencia
      public codCorrespondenciaDet:string, // Se da por La AutoSecuencia
      public descCorrespondencia:string,
      public temaCorrespondencia:string,
      public codReferenciaSreci:string,

      // Datos de Relaciones de Tablas
      public idInstitucion:number,
      public idInstitucionAnterior:number,
      public idUsuario:string, // Tipo de Dato String, porque no se selecciona
      public idTipoFuncionario:number, // Tipo de Funcionarios
      public idUsuarioAsaignado:number, // Usuario que se le asigna el Oficio
      public idEstado:string, // Tipo de Dato String, porque no se selecciona
      public idDireccionSreci:number,
      public idDireccionSreciAcom:number,
      public idTipoDocumento:string, // Tipo de Dato String, porque no se selecciona

      // Datos de fechas
      public fechaMaxEntrega:string,
      public fechaModificacion:string,
      public fechaIngreso:string,
      public horaIngreso:string,
      public horaFinalizacion:string,

      // Datos Externos a la Tablas
      public idPais:number,
      public idTipoInstitucion:number,

      //Datos para Ingresar la Primera Accion
      public idDeptoFuncional:number,
      public idDeptoFuncionalAcom:number,

      // Secuenciales de Tablas
      public secuenciaComunicacionIn:string,
      public secuenciaComunicacionInAct:string,
      public secuenciaComunicacionDet:string,
      public secuenciaComunicacionDetAct:string,
      public secuenciaComunicacionSCPI:string, // Secuencial SCPI

      //Envio de correos
      public emailDireccion:string,
      public pdfDocumento,
      public hideCom:string,

      // Observaciones Iniciales
      public observaciones:string,

      //Copia de Correspondencia
      public setTomail:string,

      // Sub Direcciones SRECI
      public subDireccionesSreciAcom,

      public justificacionTraslado:string,

      // Documento a Borrar
      public codDocumentoBorar:string,

      // Datos Generales de los Funcionarios
      public nombre1Usuario:string,
      public nombre2Usuario:string,
      public apellido1Usuario:string,
      public apellido2Usuario:string,
      public nombre1Funcionario:string,
      public nombre2Funcionario:string,
      public apellido1Funcionario:string,
      public apellido2Funcionario:string,

      // Deptos Fucncionales
      public descDeptoFuncional:string,
      public inicialesDeptoFuncional:string,

      // Direcciones Sreci
      public descDireccionSreci:string,
      public inicialesDireccionSreci:string,
      public descEstado:string,

      //Bitacora
      public descComunicacionAnterior:string,
      public temaComunicacionAnterior:string,

    ){}

}

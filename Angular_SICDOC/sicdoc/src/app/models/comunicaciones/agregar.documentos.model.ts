export class AgregarDocumentoModel{

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

      public justificacionNewDocs:string,

      // Documento a Borrar
      public codDocumentoBorar:string

    ){}

}

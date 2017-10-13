export class  Contactos {

  //Constructor de la Clase
    constructor(
      // Campos de la Tabla
      public idContacto:number,
      public codContacto:string,
      public nombre1Contacto:string,
      public nombre2Contacto:string,
      public apellido1Contacto:string,
      public apellido2Contacto:string,

      // Material de Referencia
      public fotoContacto:string,
      public perfilContacto:string,

      // Telefonos
      public telefonoContacto:number,
      public celularContacto:number,

      //Datos de la Busqueda
      public email1Contacto:string,
      public email2Contacto:string,

      // Contacto SRECI
      public idContactoSreci:number,
      public idInstitucion:number,

      // Docuemntos
      public pdfDocumento:string,
      public imgDocumento:string,


    ){}

}

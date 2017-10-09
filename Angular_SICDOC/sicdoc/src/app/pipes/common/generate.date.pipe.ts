import { Pipe, PipeTransform } from '@angular/core';

@Pipe ({ name: 'generateDate' })

export class GenerateDatePipe implements PipeTransform{
  // Transformamos la fecha a Setear
  transform( value ): string {
      // Parametro de convercion
      let date = new Date( value * 1000 );
      // Sacamos los valores de la fecha
      // Seteo del Dia
      let day = date.getDate() + 1; // Dia
      let final_day = day.toString();
      if( day <= 9 ){
        final_day = "0" + final_day;
      }

      // Seteo del Mes
      let mes = date.getMonth() + 1 ; // Mes
      let final_mes = mes.toString();
      if( mes <= 9 ){
        final_mes = "0" + final_mes;
      }

      // Seteo del Anio
      let anio = date.getFullYear(); // Anio

      let result = final_day + "-" + final_mes + "-" + anio;
      // alert( result );
      return result;
  }

}

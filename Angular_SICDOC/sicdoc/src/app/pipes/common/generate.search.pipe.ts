import { Injectable, Pipe, PipeTransform } from '@angular/core';

@Pipe({
 name: 'searchfilter'
})

@Injectable()
export class SearchFilterPipe implements PipeTransform {
 // Metodo para la Busqueda de los Oficios por:
 // 1 ) Codigo del Oficio Interno
 // 1 ) Codigo del Oficio Ingresado
 // 1 ) Codigo del Oficio Tema del Oficio
 transform( item: any, key: string, search:any ): any  {
   if( search === undefined ) return item;
   return item.filter( function( item ){
     return item[ key ].toLowerCase().includes( search.toLowerCase() );
   }) ;
 }
 //

 /*transform( value, key: string, term: string ) {
   return value.filter( (item) => {
     if ( item.hasOwnProperty( key )) {
       if ( term ) {
         let regExp = new RegExp('\\b' + term , 'gi');
         return regExp.test( item[ key ].toLowerCase()  );
       } else {
         return true;
       }
     } else {
       return false;
     }
   });
 }*/

}

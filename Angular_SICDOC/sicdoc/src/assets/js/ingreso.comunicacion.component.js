// Scrip para Agregar o Quitar Items de Direcciones Acompañantes

function agregarItem(){
  // alert('Agregar Direccion Acompañante');

  var valueData = "{{ list.idDeptoFuncional }}";

  var newDirAcompanate = '<select #idDireccionSreci="ngModel" class="form-control" name="idDireccionSreci" id="idDireccionSreci" '
        + 'ngControl="idDireccionSreci" [(ngModel)]="comunicacion.idDireccionSreci"  (change)="getlistaSubDireccionesSRECI()" > '
      + '<option value="0">Dirección SRECI Responsable </option> '
      // + '<option *ngFor=" '+ let list of JsonOutgetlistaDireccionSRECI + ' " value=" ' + {{ list.idDireccionSreci }} + ' "> '
      + '{{ list.descDireccionSreci }}</option> </select>'
      + '<small class="form-text text-muted" *ngIf="idDireccionSreci.value == 0 || idDireccionSreci.value == null " > '
      + ' Debes Seleccionar una Dirección SRECI responsable</small> ';


  var newSubDirAcompanante = '<select #idDeptoFuncional="ngModel" class="form-control" name="idDeptoFuncional" id="idDeptoFuncional" '
        + ' ngControl="idDeptoFuncional" [(ngModel)]="comunicacion.idDeptoFuncional"  > '
        + '<option value="0">Sub Dirección SRECI Acompañante</option> '
        + '<option *ngFor="let list of JsonOutgetlistaSubDireccionSRECI" value="'+ valueData +'">{{ list.descDeptoFuncional }}</option> '
        + '</select> <small class="form-text text-muted" *ngIf="idDeptoFuncional.value == 0 || idDeptoFuncional.value == null" > '
        + 'Debes Seleccionar una Sub Dirección SRECI acompañante</small>';

    $( "#newDirAcom" ).append( newDirAcompanate );
    $( "#newSubDirAcom" ).append( newSubDirAcompanante );

}
       function escuchaEliminarItem(){
          $("#lista").delegate('.eliminar_item', 'click', function(){
             var padre = $(this).parent();
             padre.remove();
          });
       }
       $(document).ready(function(){
          $("#agregar_item").click(function(){
             agregarItem();
          });
          escuchaEliminarItem();
       });

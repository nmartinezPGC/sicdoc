import { Component } from '@angular/core';

@Component({
  selector: 'app-chart-home',
  templateUrl: './chart.home.component.html'
})
export class ChartHomeComponent {

  //Definicion de l Grafico de Barras del HOME
  public barChartOptions:any = {
    scaleShowVerticalLines: false,
    responsive: true
  };
  public barChartLabels:string[] = ['Oficios', 'Memos', 'Notas', 'Circulares', 'Correos', 'Llamadas', 'Reuniones'];
  public barChartType:string = 'bar';
  public barChartLegend:boolean = true;

  //colors of Grafico
  public lineChartColors:Array<any> = [
    { // verde - Ingresado
      backgroundColor: 'rgba(39, 174, 96, 0.6)',
      borderColor: 'rgba(30, 132, 73, 0.2)'
    },
    { // Amarillo - Pendiente
      backgroundColor: 'rgba(241, 196, 15, 0.6)',
      borderColor: 'rgba(247, 220, 111, 0.2)'
    },
    { // Azul - Resuelto
      backgroundColor: 'rgba(93, 173, 226, 0.6)',
      borderColor: 'rgba(46, 134, 193, 0.2)'
    },
    { // Rojo -Anulado
      backgroundColor: 'rgba(241, 148, 138, 0.8)',
      borderColor: 'rgba(192, 57, 43, 0.2)'
    }
  ];

  public barChartData:any[] = [
    {data: [65, 59, 8, 8, 6, 5, 4], label: 'Ingresado'},
    {data: [28, 48, 4, 1, 6, 7, 9], label: 'Pendiente'},
    {data: [8, 8, 4, 9, 8, 2, 9], label: 'Resuelto'},
    {data: [2, 4, 4, 9, 6, 2, 3], label: 'Anulado'}
  ];

  // events
  public chartClicked(e:any):void {
    console.log(e);
  }

  public chartHovered(e:any):void {
    console.log(e);
  }

  public randomize():void {
    // Only Change 3 values
    let data = [
      Math.round(Math.random() * 100),
      59,
      80,
      (Math.random() * 100),
      56,
      (Math.random() * 100),
      40];
    let clone = JSON.parse(JSON.stringify(this.barChartData));
    clone[0].data = data;
    this.barChartData = clone;
    /**
     * (My guess), for Angular to recognize the change in the dataset
     * it has to change the dataset variable directly,
     * so one way around it, is to clone the data, change it and then
     * assign it;
     */
  }

}

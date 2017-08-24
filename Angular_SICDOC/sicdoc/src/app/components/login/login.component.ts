import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-login',
  templateUrl: '../../views/login/login.html',
  styleUrls: ['../../views/login/style.component.css']
})
export class LoginComponent {
  public titulo: string = "Por favor Identificate";

  ngOnInit() {
    //alert('Hola Mundo');
    document.getElementById("testScript").remove();
    var testScript = document.createElement("script");
    testScript.setAttribute("id", "testScript");
    testScript.setAttribute("src", "../../views/login/js.component.js");
    document.body.appendChild(testScript);

  }


}

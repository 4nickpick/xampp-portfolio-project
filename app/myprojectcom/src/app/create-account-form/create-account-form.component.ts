import { Component } from '@angular/core';
import { FormControl } from '@angular/forms';

@Component({
  selector: 'create-account-form',
  templateUrl: './create-account-form.component.html',
  styleUrls: ['./create-account-form.component.css']
})
export class CreateAccountFormComponent {

  firstName = new FormControl(''); 
  lastName = new FormControl(''); 
  email = new FormControl(''); 
  password = new FormControl(''); 
  passwordAgain = new FormControl('');
  
  createAccount() {

  }
}

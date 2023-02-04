import { Component } from '@angular/core';
import { FormGroup, FormControl, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { UserService } from 'data/user.service';
import { PasswordValidators } from 'data/validators/PasswordValidators';

@Component({
  selector: 'create-account-form',
  templateUrl: './create-account-form.component.html',
  styleUrls: ['./create-account-form.component.css']
})
export class CreateAccountFormComponent {

  constructor(public userService: UserService, public router: Router) { 

  }

  createAccountForm : FormGroup = new FormGroup(
    {
      firstName: new FormControl('', [Validators.required]),
      lastName: new FormControl('', [Validators.required]),
      email: new FormControl('', [Validators.required, Validators.email]),
      password: new FormControl('', [Validators.required, Validators.minLength(8)]),
      passwordAgain: new FormControl('', [Validators.required, Validators.minLength(8)])
    },
    {
      validators: PasswordValidators.MatchValidator
    }
  );
  
  get form() {
    return this.createAccountForm.controls;
  }
  
  onSubmit() {
    
    // validate our form entries
    this.userService.createAccount(
      this.createAccountForm.get('firstName')!.value, 
      this.createAccountForm.get('lastName')!.value, 
      this.createAccountForm.get('email')!.value, 
      this.createAccountForm.get('password')!.value, 
      this.createAccountForm.get('passwordAgain')!.value
    )
    .subscribe(resp => {
      if(resp) {
        this.router.navigate(['/dashboard']);
      }
    });
  }

}

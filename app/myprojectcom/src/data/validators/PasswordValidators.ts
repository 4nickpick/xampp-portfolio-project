import { AbstractControl, ValidationErrors, ValidatorFn } from "@angular/forms";

export class PasswordValidators {
  constructor() {}

  static patternValidator(regex: RegExp, error: ValidationErrors): ValidatorFn {
    return (control: AbstractControl): { [key: string]: any } | null => {
      if (!control.value) {
        // if the control value is empty return no error.
        return null;
      }

      // test the value of the control against the regexp supplied.
      const valid = regex.test(control.value);

      // if true, return no error, otherwise return the error object passed in the second parameter.
      return valid ? null : error;
    };
}

static MatchValidator(control: AbstractControl) {
    const passwordControl = control.get("password");
    const passwordAgainControl = control.get("passwordAgain");

    if(passwordControl == null || passwordAgainControl == null) {
        return null; 
    }

    const password: string = passwordControl.value; // get password from our password form control
    const passwordAgain: string = passwordAgainControl.value; // get password from our passwordAgain form control
    
    // if the passwordAgain value is null or empty, don't return an error.
    if (!passwordAgain?.length) {
        return null;
    }

    // if the confirmPassword length is < 8, set the minLength error.
    if (passwordAgain.length < 8) {
        passwordAgainControl.setErrors({ minLength: true });
    } 
    else {
        // compare the passwords and see if they match.
        if (password !== passwordAgain) {
            passwordAgainControl.setErrors({ mismatch: true });
            console.log(password, passwordAgain);
        } else {
            // if passwords match, don't return an error.
            return null;
        }
    }

    return null;

  }
}
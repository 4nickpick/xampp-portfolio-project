import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, throwError } from 'rxjs';
import { catchError, retry } from 'rxjs/operators';

@Injectable()
export class UserService {
  baseUrl = 'http://api.myproject.com'

  constructor(private http: HttpClient) { 
    
  }

  createAccount(firstName: string, lastName: string, email: string, password: string, passwordAgain: string) {
    return this.http.post<CreateAccountResponse>(this.baseUrl + '/users', 
    {
      "firstName": firstName,
      "lastName": lastName,
      "email": email,
      "password": password
    });
  }
}

class CreateAccountResponse {
  
}
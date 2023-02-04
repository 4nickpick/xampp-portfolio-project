import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { ReactiveFormsModule } from '@angular/forms';
import { HttpClientModule } from '@angular/common/http';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { HomeComponent } from './home/home.component';

import { MatIconModule } from '@angular/material/icon';
import { CreateAccountComponent } from './create-account/create-account.component';
import { CreateAccountFormComponent } from './create-account-form/create-account-form.component';
import { LoginComponent } from './login/login.component';
import { LoginFormComponent } from './login-form/login-form.component';
import { ForgotPasswordComponent } from './forgot-password/forgot-password.component';
import { ForgotPasswordFormComponent } from './forgot-password-form/forgot-password-form.component';
import { ResetPasswordComponent } from './reset-password/reset-password.component';
import { ResetPasswordFormComponent } from './reset-password-form/reset-password-form.component';
import { DashboardComponent } from './dashboard/dashboard.component';
import { UserService } from 'data/user.service';

@NgModule({
  declarations: [
    AppComponent,
    HomeComponent,
    CreateAccountComponent,
    CreateAccountFormComponent,
    LoginComponent,
    LoginFormComponent,
    ForgotPasswordComponent,
    ForgotPasswordFormComponent,
    ResetPasswordComponent,
    ResetPasswordFormComponent,
    DashboardComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    MatIconModule,
    ReactiveFormsModule,
    HttpClientModule
  ],
  providers: [
    UserService
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }

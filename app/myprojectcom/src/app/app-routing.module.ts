import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { AppComponent } from './app.component';
import { HomeComponent } from './home/home.component';
import { CreateAccountComponent } from './create-account/create-account.component';
// import { LoginComponent } from './login/login.component';

const routes: Routes = [
  { path: '', component: HomeComponent }
  ,{ path: 'create-account', component: CreateAccountComponent }
  ,{ path: 'login', component: HomeComponent }
  // ,{ path: 'login', component: LoginComponent }
]; // sets up routes constant where you define your routes

// configures NgModule imports and exports
@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
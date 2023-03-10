import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CreateAccountFormComponent } from './create-account-form.component';

describe('CreateAccountFormComponent', () => {
  let component: CreateAccountFormComponent;
  let fixture: ComponentFixture<CreateAccountFormComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ CreateAccountFormComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(CreateAccountFormComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

import { AfterViewInit, Component, ElementRef, ViewChild } from '@angular/core';
import { FormBuilder, FormGroup } from '@angular/forms';

@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.scss']
})
export class RegisterComponent implements AfterViewInit {

  registerForm: FormGroup;

  formControls: HTMLDivElement[] = [];
  formControlIndex = 0;

  @ViewChild('buttonControl')
  buttonControl!: ElementRef<HTMLButtonElement>;

  constructor(private fb: FormBuilder) {
    this.registerForm = fb.group({
      name: fb.control(''),
      username: fb.control(''),
      password: fb.control(''),
      confirmPassword: fb.control('')
    });
  }

  ngAfterViewInit(): void {
    this.formControls = Array.from(document.querySelectorAll('.form-control'));
  }

  handleNextButton(): void {
    if (this.formControlIndex === this.formControls.length - 2) {
      this.buttonControl.nativeElement.textContent = 'Submit';
    } else if (this.formControlIndex === this.formControls.length - 1) {
      this.buttonControl.nativeElement.setAttribute('type', 'submit');
    }

    if (this.formControlIndex + 1 >= this.formControls.length) {
      return;
    }

    this.formControls[this.formControlIndex].classList.add('hidden');
    this.formControls[this.formControlIndex + 1].classList.remove('hidden');

    this.formControlIndex++;
  }

  handleSubmit(): void {
    console.log(this.registerForm.value);
  }
}

import { AfterViewInit, Component, ElementRef, ViewChild } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';

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
      name: fb.control('', [Validators.required]),
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

    const key = Object.keys(this.registerForm.controls)[this.formControlIndex];
    const control = this.registerForm.controls[key];

    if (control.invalid) {
      const input = this.formControls[this.formControlIndex].querySelector('input') as HTMLInputElement;
      const errorSpan = this.formControls[this.formControlIndex].querySelector('.form-control__error') as HTMLSpanElement;
      input.classList.add('input-invalid');
      errorSpan.classList.remove('hidden');
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

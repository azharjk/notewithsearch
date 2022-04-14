import { AfterViewInit, Component, ElementRef, ViewChild } from '@angular/core';

@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.scss']
})
export class RegisterComponent implements AfterViewInit {

  @ViewChild('formControlName')
  formControlName!: ElementRef<HTMLDivElement>;

  @ViewChild('formControlUsername')
  formControlUsername!: ElementRef<HTMLDivElement>;

  @ViewChild('formControlPassword')
  formControlPassword!: ElementRef<HTMLDivElement>;

  formControls: ElementRef<HTMLDivElement>[] = [];

  currentFormControl = 0;

  constructor() { }

  ngAfterViewInit(): void {
    this.formControls = [
      this.formControlName,
      this.formControlUsername,
      this.formControlPassword
    ];
  }

  nextControl(): void {
    if (this.currentFormControl + 1 >= this.formControls.length) {
      return;
    }

    this.formControls[this.currentFormControl]
      .nativeElement.classList.add('hidden');

    this.formControls[this.currentFormControl + 1]
      .nativeElement.classList.remove('hidden');

    this.currentFormControl++;
  }
}

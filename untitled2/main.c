#include<stdio.h>

double calculate_area(double a)
{
    return a*a;
}
double radius;
int main(void)
{
    puts("Podaj długość boku");
    scanf("%lf",&radius);
    //Program sprawdza, czy promień ma wartość większą lub równą zero.
    if(radius>0.0) {
        printf("Pole kwadratu o boku  %f cm wynosi %f,→ cm^2.\n",radius,calculate_area(radius));
        return 0;
    }


    if(radius == 0) {
        printf("nie moze byc bok 0\n");
        return 0;
    }

    else{
        puts("Podana długość boku jest nieprawidłowa.");
        return 0;
    }



}

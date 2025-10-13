#include<stdio.h>
#define PI 3.1415
double calculate_area(double r)
{
return PI*r*r;
}
double radius;
int main(void)
{
   puts("Podaj długość promienia koła");
scanf("%lf",&radius);
//Program sprawdza, czy promień ma wartość większą lub równą zero.
if(radius>0.0)
printf("Pole koła o promieniu %f cm wynosi %f,→ cm^2.\n",radius,calculate_area(radius));
if(radius == 0)
printf("nie moze byc promien 0\n");
else
puts("Podana długość promienia jest nieprawidłowa.");
return 0;

}

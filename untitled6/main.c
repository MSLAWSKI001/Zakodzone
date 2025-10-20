/*#include <stdio.h>
int a,b,c,max=0;
int main(void)
{
    scanf("%d",&a);
    scanf("%d",&b);
    scanf("%d",&c);
    printf("a: %d, b: %d, c:%d \n",a,b,c);
    if(a>max) {
    max = a;
    }
    if(b>max) {
        max = b;
    }
    if(c>max) {
        max = c;
    }

    printf("max: %d\n",max);
    return 0;
}
//5zad

#include <stdio.h>
int a,b,c,max=0;
int main(void)
{
    scanf("%d",&a);
    scanf("%d",&b);
    scanf("%d",&c);
    printf("a: %d, b: %d, c:%d \n",a,b,c);
    if(a>max) {
    max = a;
    }
    if(b>max) {
        max = b;
    }
    if(c>max) {
        max = c;
    }

    printf("max: %d\n",max);
    return 0;
}#include <stdio.h>
int chr1;
int chr2;
char ans;
int main(void)
{
    scanf("%d",&chr1);
    scanf("%d",&chr2);
    unsigned char i = chr1;
    unsigned char i2 = chr2;

    printf("unsigned char: %c\n",i);
    printf("unsigned char: %c\n",i2);
    ans1 = i&&i2;
    ans2 = i||i2;
    printf("%c",ans1);
    return 0;
}

//6zad

#include <stdio.h>
int a;
short int b;
double c;
int main(void)
{
    printf("a: %d\n",a);
    printf("b: %d\n",b);
    printf("c: %lf\n",c);
    return 0;
}


#include <stdio.h>
int chr1;
int chr2;
char ans;
int main(void)
{
    int a,b;
    scanf("%d",&a);
    scanf("%d",&b);
    int diva = a/2;
    int divb = a/2;

    printf("int a: %c\n",diva);
    printf("int b: %c\n",divb);

    printf("%c",ans);
    return 0;
}
#include <stdio.h>
int chr1;
int chr2;
char ans;
int main(void)
{
    scanf("%d",&chr1);
    scanf("%d",&chr2);
    unsigned char i = chr1;
    unsigned char i2 = chr2;

    printf("unsigned char: %c\n",i);
    printf("unsigned char: %c\n",i2);
    ans = i&&i2;
    printf("%c",ans);
    return 0;
}

#include <stdio.h>
int a;
int b;

int main(void)
{
    scanf("%d",&a);
    scanf("%d",&b);

    if(a%b == 0) {
        printf("%d\n",a);
    }
    else {
        printf("%d\n",b);
    }
    return 0;
}
*/
#include <stdio.h>

int a;
int b;

int main(void)
{
    scanf("%d",&a);


    if(a&1) {
        printf("parzyste\n");
    }
    else {
        printf("nieparzyste\n");
    }
    return 0;
}
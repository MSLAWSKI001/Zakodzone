#include <stdio.h>
int chr1;
int chr2;
char ans1;
char ans2;
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

    printf("%c\n",ans1);
    printf("%c\n",ans2);
    return 0;
}
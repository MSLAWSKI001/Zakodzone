'''
    nazwa funkcji: szyfr_gaderypoluki
*
* parametry wejściowe: 'teks_do_szyfrowania' (str) przechowuję tekst podany od urzytkowniaka do przyszłego szyfrowania
* wartość zwracana: funkcja zwraca 'zaszyfrowany_tekst' (str) jest to tekst który został zaszyfrowany zgodnie z zasadami szyfrowaniaa gaderypoluki
* opis funkcji: pętla for sprawdza każdy znak w tekscie i sprawdza czy dany znak jest w kluczu czy nie.Jak tak to go zamienia jak nie to pozostaje taki jaki był
*
* autor: <numer PESEL zdającego>






'''
def szyfr_gaderypoluki(tekst):
    klucz = {'g':'a','a':'g',
             'd':'e', 'e':'d',
             'r':'y' , 'y':'r',
             'p':'o', 'o':'p',
             'l':'u', 'u':'l',
             'k':'i', 'i':'k'}
    zaszyfrowany_tekst=""
    for znak in tekst:
        zaszyfrowany_tekst+= klucz.get(znak,znak)
    return zaszyfrowany_tekst

if __name__ == '__main__':
    tekst_do_szyfrowania = input("podaj tekst do szyfrowania max 20 znaków:  ")
    if len(tekst_do_szyfrowania) > 20:
        print("błąd przekroczono liczbę znaków max(20)")
    else:
        print("Zaszyfrowany tekst to:  "+ szyfr_gaderypoluki(tekst_do_szyfrowania))
input()
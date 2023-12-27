function zmienkolor() {
    var color = document.getElementById('color').value;
    przycisk.style.background = color;
}
function zmientlo() {
    var color2 = document.getElementById('color2').value;
    alewy.style.background = color2;
    aprawy.style.background = color2;
}
function mnozenie() {
    var liczba1 = document.getElementById('liczba1').value;
    var liczba2 = document.getElementById('liczba2').value;
    var wynik;
    wynik = liczba1 * liczba2;
    document.getElementById('wynik').innerHTML = wynik;
}
function changezdj1() {
    var zdj1 = document.getElementById('zdj1');
    zdj1.height
    

}
function changezdj2() {
    var zdj2 = document.getElementById('zdj2');
    zdj2.style.height = 100;
    zdj2.style.width = 100;
}
function changezdj3() {
    var zdj3 = document.getElementById('zdj3');
    zdj3.style.height = 100;
    zdj3.style.width = 100;
}
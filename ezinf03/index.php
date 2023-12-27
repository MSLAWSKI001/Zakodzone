<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <title>Gardian strona</title>
    </head>
    <body>
        <link rel="stylesheet" href="styl.css">
        <header>
            <h1>Moja strona</h1>
        </header>
        <aside class="lewy" id="alewy">
            <table>
                <tr>
                    <th>id</th><th>marka</th><th>model</th><th>kolor</th><th>stan</th>
                </tr>
                <?php
        setcookie("witaj_ponownie","true",time() + 30,"/");
        if (isset($_COOKIE['witaj_ponownie'])) {
            echo "<p>Witaj ponownie!</p>";
        } else {
            echo "<p>Witaj po raz pierwszy!</p>";
        }
        $con = mysqli_connect('localhost','root','','samochody');
        $q='SELECT `id`,`marka`,`model`,`kolor`,`stan` FROM `samochody` WHERE `rocznik` >= 2003 AND `rocznik`<= 2016;';
        $result = mysqli_query($con,$q);
    while ($row = mysqli_fetch_array  ($result))
    {
        echo "<tr>";
        echo "<td>".$row[0]."</td>";
        echo "<td>".$row[1]."</td>";
        echo "<td>".$row[2]."</td>";
        echo "<td>".$row[3]."</td>";
        echo "<td>".$row[4]."</td>";
        echo "</tr>";
    }
        ?>
        </table>
    </aside>
    <section class="glowny">
        <form action="formularz.php" method="post">
            <p>Podaj id: <input type="number" name="id"></p>
            <p>Podaj markę: <input type="text" name="marka"></p>
            <p>Podaj model: <input type="text" name="model"></p>
            <p>Podaj kolor: <input type="text" name="kolor"></p>
            <p>Podaj stan: <input type="text" name="stan"></p>
            <button type="reset">Czyść</button>
            <button type="submit">Dodaj</button>
        </form>
        <img id="zdj1" onclick="changezdj1()" src="kw2.png" alt="obraz1">
        <img id="zdj2" onclick="changezdj2()" src="kw3.png" alt="obraz2">
        <img id="zdj3" onclick="changezdj3()" src="kw4.png" alt="obraz3">
    </section>
    <aside class="prawy" id="aprawy">
        <script src="kolor.js"></script>
        <input type="color" id="color"><br>
        <button onclick="zmienkolor()" id="przycisk">Zmień</button><br>
        <input type="color" id="color2"><br>
        <button onclick="zmientlo()">Zmień</button><br>
        <input type="number" id="liczba1"><br>
        <input type="number" id="liczba2"><br>
        <button onclick="mnozenie()">Oblicz</button><br>
        <p id="wynik"></p>

    </aside>
    <footer>
    <p>Autor: MR.Walentino</p>
    </footer>
</body>
</html>
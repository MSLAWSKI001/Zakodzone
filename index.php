<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Hurtownia szkolna</title>
    <link rel="stylesheet" href="styl.css">
</head>
<body>
    <header>    
    <h1>Hurtownia z najlepszymi cenami</h1>
    </header>
    <aside class="lewy"> 
        <h2>Nasze ceny</h2>
    <table>
    <?php
        $con = mysqli_connect("localhost","root","","sklep");
        $q =  "SELECT `nazwa`,`cena` FROM `towary` WHERE `id` <= 4;";
        $res = mysqli_query($con,$q);
       while ($row = mysqli_fetch_row($res)) 
       {
        echo"<tr>";
        echo"<td>".$row[0]."</td>";
        echo"<td>".$row[1]."</td>";
        echo"</tr>";
       }
        


    ?>
    </table>
    </aside>
    <main>  
    <h2>Koszt zakupów</h2>
    <form method="post">
    <select name="" id=""><br>
        <option value="Zeszyt 60 kartek"></option>
        <option value="Zeszyt 32 kartk"></option>
    </select>
    <input type="number" name="" id="">
    <button type="submit">OBLICZ</button>


    </form>
    </main>
    <aside class="prawy"> 
    <h2>Kontakt</h2>
    <img src="zakupy.png" alt="hurtownia">
    <p><a href="mailto:">hurt@poczta2.pl</a></p>
    </aside>
<footer>
    <h4>Witrynę wykonał 053852346</h4>
</footer>
</body>
</html>
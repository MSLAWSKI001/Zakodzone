<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <
    <title>Panel administratora</title>
</head>
<body>
    <link rel="stylesheet" href="styl4.css">
    <header>
    <h3>Portal Społecznościowy - panel administaratora</h3>
    </header>
    <section class = lewy>
    <h4>Użytkownicy</h4>
    <?php 
    $con = mysqli_connect('localhost','root','','wedkarstwo');
    $q = "SELECT `id`,`imie`,`nazwisko`,(2023-`rok_urodzenia`) FROM `osoby` LIMIT 30;";
    $result = mysqli_query($con,$q);
    
    while ($row = mysqli_fetch_array ($result))
    {
        
        echo $row[0]." ";
        echo $row[1]." ";
        echo $row[2].", ";
        echo $row[3]."<br>";
    }
    ?>
    <a href="settings.html">Inne ustawienia</a>
    </section>
    <section class = prawy>
    <h4>Podaj id urzytkownika</h4>
    <form action="users.php" method="post">
        <input type="number" name="id">
        <button type="submit">ZOBACZ</button>
    </form>
    <hr></hr>
    <?php
        $id = $_POST['id']; 
        $q2 = "SELECT osoby.id,osoby.imie,osoby.nazwisko,osoby.rok_urodzenia,osoby.opis,osoby.zdjecie,hobby.nazwa FROM `osoby` JOIN hobby ON osoby.Hobby_id = hobby.id WHERE osoby.id = $id;";
        $result2 = mysqli_query($con,$q2);
        while ($row2 = mysqli_fetch_array ($result2))
        {
            echo"<h2>$row2[0] $row2[1] $row2[2]</h2>";
            echo"<img src=$row2[5] alt=$row2[0]><br>";
            echo"<p>Rok urodzenia $row2[3]</p>";
            echo"<p>Opis $row2[4]</p>";
            echo"<p>Hobby $row2[6]</p>";
        }
        mysqli_close($con);
        ?>
    </section>
    <footer>
        
        <p>Stronę wykonał</p>
    </footer>
</body>
</html>
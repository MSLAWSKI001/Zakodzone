 <!DOCTYPE html>
 <html lang="pl">
 <head>
    <meta charset="UTF-8">
    <title>Video On Demand</title>
 </head>
 <body>
     <link rel="stylesheet" href="styl3.css">
    <header>
        <section class = 'lewyh'>
            <h1>Internetowa wypożyczalnia filmów</h1>
        </section>
        <section class = 'prawyh'>
        <table>
            <tr><td>Kryminał</td><td>Horror</td><td>Przygodowy</td></tr>
            <tr><td>20</td><td>30</td><td>20</td></tr>
        </table>
        </section>

    </header>
    <section class = 'Polecamy'>
    <h3>Polecamy</h3>
    <?php
    $con = mysqli_connect('localhost','root','','dane3');
    $q1 = "SELECT `id`,`nazwa`,`opis`,`zdjecie` FROM `produkty` WHERE `id` IN(18,22,23,25);";
    $res = mysqli_query($con,$q1);
    while ($row1 = mysqli_fetch_array ($res))
    {
        echo "<div class = 'polecane'>
        <h4>$row1[0] , $row1[1]</h4>
        <img src='$row1[3]' alt='film'>
        <p>$row1[2]</p>
        </div>";
       
    }
    ?>
    </section>
    <section class = 'Filmy'>
    <h3>filmy fantastyczne</h3>
    <?php
    $q2 = "SELECT `id`,`nazwa`,`opis`,`zdjecie` FROM `produkty` WHERE `Rodzaje_id` =12;";
    $result2 = mysqli_query($con,$q2);
    while ($row2 = mysqli_fetch_array  ($result2))
    {
        echo "<div class = 'film'>
        <h4>$row2[0] $row2[1]</h4>
        <img src='$row2[3]' alt='film'>
        <p>$row2[2]</p>
        </div>";
       
    }
    ?>
    </section>
    <footer>
    <a href="mailto:ja@poczta.com">394578357233</a>
    <form action="video.php" method="post">
        <input type="number" name="wfilm">
        <button type="submit">Usuń film</button>    
    </form>
    <?php
    $name = $_POST['wfilm'];
    $q3 = "DELETE FROM `produkty` WHERE `produkty`.`id` = '$name';";
    $result3 = mysqli_query($con,$q3);
    ?>
    </footer>
 </body>
 </html>
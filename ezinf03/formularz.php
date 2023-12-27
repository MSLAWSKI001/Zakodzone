<?php
    $id = $_POST['id'];
    $marka = $_POST['marka'];
    $model = $_POST['model'];
    $kolor = $_POST['kolor'];
    $stan = $_POST['stan'];

    $q2="INSERT INTO `samochody`(id,marka,model,kolor,stan) VALUES (null,'$marka','$model','$kolor','$stan');";
    mysqli_query($con,$q2);
    mysqli_close($con);
?>
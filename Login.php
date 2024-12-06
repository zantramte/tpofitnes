<?php
// Povezava z bazo
$conn = new mysqli("localhost", "tpofitnes_tposkupina", "fujsjelegenda", "tpofitnes_projekt");

if ($conn->connect_error) {
    die("Povezava z bazo ni uspela: " . $conn->connect_error);
}

// Preveri, če je obrazec poslan z metodo POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Preverjanje in čiščenje podatkov
    $FirstName = mysqli_real_escape_string($conn, htmlspecialchars($_POST["FirstName"]));
    $LastName = mysqli_real_escape_string($conn, htmlspecialchars($_POST["LastName"]));
    $EMSO = mysqli_real_escape_string($conn, htmlspecialchars($_POST["EMSO"]));
    
    // Preverjanje, ali so polja prazna
    if (empty($FirstName) || empty($LastName) || empty($EMSO)) {
        // Obvestilo o napaki in preusmeritev
        echo "<script>
            alert('Vsa polja morajo biti izpolnjena!');
            window.location.href = 'StranLogin.htm'; // Preusmeritev nazaj na obrazec
        </script>";
        exit(); // Prekine nadaljnje izvajanje
    }

    // Preverjanje, ali EMSO vsebuje točno 13 števil
    if (!preg_match('/^\d{13}$/', $EMSO)) {
        // Obvestilo o napaki in preusmeritev
        echo "<script>
            alert('EMSO mora biti točno 13 števil!');
            window.location.href = 'StranLogin.htm'; // Preusmeritev nazaj na obrazec
        </script>";
        exit(); // Prekine nadaljnje izvajanje
    }

    // SQL poizvedba za preverjanje uporabnika
    $sql = "SELECT * FROM clan WHERE Ime=? AND Priimek=? AND EMSO=? LIMIT 1";
    $stmt = $conn->prepare($sql);
    
    // Preveri, ali je bilo pripravljeno
    if ($stmt === false) {
        die("Napaka pri pripravi poizvedbe: " . $conn->error);
    }
    
    $stmt->bind_param("sss", $FirstName, $LastName, $EMSO);
    $stmt->execute();
    
    // Preverite, če je bil uporabnik najden
    $result = $stmt->get_result();
    $st = $result->num_rows;
    
    if ($st > 0)
    {
        session_start();
        $_SESSION["Ime"] = $FirstName;
        $_SESSION["Priimek"] = $LastName;
        $_SESSION["prijavljen"] = TRUE;
        $_SESSION["ID"] = $result->fetch_assoc()["ID_Clan"];
        
        // Uspešna prijava - JavaScript alert in preusmeritev
        echo "<script>
            alert('Prijava je uspešna!');
            window.location.href = 'Dobrodosli.php'; // Preusmeritev na Landing stran
        </script>";
        exit();
    } 
    
    else
    {
        // Neuspešna prijava - JavaScript alert in preusmeritev
        echo "<script>
            alert('Prijava ni uspešna! Poskusite znova.');
            window.location.href = 'StranLogin.htm'; // Preusmeritev na stran z registracijo
        </script>";
        exit();
    }

    // Zapiranje
    $stmt->close();
    $conn->close();
}
?>

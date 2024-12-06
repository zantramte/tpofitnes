<?php
// Povezava z bazo
$conn = new mysqli("localhost", "tpofitnes_tposkupina", "fujsjelegenda", "tpofitnes_projekt");

if ($conn->connect_error) {
    die("Unable to connect to database: " . $conn->connect_error);
}

// Spremenljivka za prikaz sporočil
$message = "";

// Preveri, če je obrazec poslan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Pridobi podatke iz obrazca
    $FirstName = mysqli_real_escape_string($conn, htmlspecialchars($_POST["FirstName"]));
    $LastName = mysqli_real_escape_string($conn, htmlspecialchars($_POST["LastName"]));
    $EMSO = mysqli_real_escape_string($conn, htmlspecialchars($_POST["EMSO"]));
    $Email = mysqli_real_escape_string($conn, htmlspecialchars($_POST["Email"]));
    $Aktiven = mysqli_real_escape_string($conn, htmlspecialchars($_POST["Aktiven"]));
    $Telefon = mysqli_real_escape_string($conn, htmlspecialchars($_POST["Telefon"]));

    // Preveri, ali so polja prazna
    if (empty($FirstName) || empty($LastName) || empty($EMSO) || empty($Email) || empty($Aktiven) || empty($Telefon)) {
        $message = "Vsa polja morajo biti izpolnjena!";
    }
    // Preveri, ali je EMSO točno 13 števil
    elseif (!preg_match('/^\d{13}$/', $EMSO)) {
        $message = "EMŠO mora biti točno 13 števil!";
    }
    // Preveri, ali je email v pravilnem formatu
    elseif (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
        $message = "Napačen format e-pošte!";
    } else {
        // Preveri, ali uporabnik že obstaja
        $sql_check = "SELECT * FROM clan WHERE EMSO = ? OR Email = ? OR Telefon = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("sss", $EMSO, $Email, $Telefon);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            // Nastavi sporočilo o napaki
            $message = "Uporabnik z danimi podatki že obstaja. Poskusite z drugimi podatki.";
             echo "<script>
        alert('$message');
        window.location.href = 'StranRegister.htm'; // Preusmeritev na ustrezno stran (ali obdrži uporabnika na trenutni strani)
    </script>";
        } else {
            // Vstavi podatke v bazo
            $sql = "INSERT INTO clan (Ime, Priimek, EMSO, Email, Aktiven, Telefon) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt === false) {
                die("Napaka pri pripravi poizvedbe: " . $conn->error);
            }

            $stmt->bind_param("ssissi", $FirstName, $LastName, $EMSO, $Email, $Aktiven, $Telefon);

            if ($stmt->execute()) {
                // Nastavi sporočilo o uspehu
                $message = "Registracija je uspešna!";
                    echo "<script>
        alert('$message');
        window.location.href = 'StranLogin.htm'; // Preusmeritev na ustrezno stran (ali obdrži uporabnika na trenutni strani)
    </script>";
            } else {
                $message = "Napaka pri registraciji. Poskusite znova.";
                    echo "<script>
        alert('$message');
        window.location.href = 'StranRegister.htm'; // Preusmeritev na ustrezno stran (ali obdrži uporabnika na trenutni strani)
    </script>";
            }
        }

        $stmt_check->close();
        $stmt->close();
    }

    // Zapri povezavo z bazo
    $conn->close();

    // Prikaz sporočila z uporabo JavaScript-a (alert)
    exit();
}
?>

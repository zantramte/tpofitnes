<?php
// Start the session to access session variables
session_start();

// Check if the user is logged in
if (!isset($_SESSION["prijavljen"]) || $_SESSION["prijavljen"] !== TRUE) {
    // Redirect to login page if the user is not logged in
    header("Location: StranLogin.htm");
    exit(); // Make sure to stop further script execution
}
?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Fitnes - izbira trenerja in vadbe</title>
    <style>
        /* Global Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            font-family: 'Roboto', Arial, sans-serif;
            background: #497ae1;
            color: #fff;
        }

        body {
            display: flex;
            flex-direction: column;
        }

        .top-bar {
            background-color: #003cbc;
            padding: 25px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }

        .top-bar .prijavljeni {
            font-size: 1.1rem;
        }

        .top-bar .links a {
            color: white;
            font-size: 1.1rem;
            margin: 0 15px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .top-bar .links a:hover {
            background-color: #ffffff;
            color: #003cbc;
            padding: 5px 15px;
            border-radius: 5px;
        }

        h2 {
            text-align: center;
            margin: 20px 0;
        }

        form {
            background: #003cbc;
            border-radius: 10px;
            padding: 20px;
            max-width: 600px;
            margin: 0 auto 30px;
            color: white;
        }

        form table {
            width: 100%;
        }

        form th,
        form td {
            padding: 10px 5px;
            text-align: left;
        }

        form th {
            font-size: 16px;
            text-align: right;
        }

        form select,
        form input[type="submit"] {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
        }

        form input[type="submit"] {
            background: #ffffff;
            color: #003cbc;
            font-weight: bold;
            margin-top: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        form input[type="submit"]:hover {
            background: #00116f;
            color: #ffffff;
        }

        /* Flexbox za slike */
        .row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 20px;
            margin: 30px auto;
            padding: 0 20px; /* Odmik od robov */
            max-width: 1200px;
        }

        .column {
            flex: 1;
            max-width: 32%;
            margin-bottom: 20px;
        }

        .column img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 10px;
            transition: opacity 0.3s ease;
        }

        .column img:hover {
            opacity: 0.7;
        }

        /* Sticky Footer */
        .footer {
            background-color: #003cbc;
            color: white;
            text-align: center;
            padding: 30px;
            position: relative;
            width: 100%;
            margin-top: auto;
        }

        .footer a {
            color: #ffffff;
            text-decoration: none;
            font-weight: bold;
        }

        .footer a:hover {
            color: #0b23a9;
            background: #fff;
            padding: 5px 15px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        /* Hamburger menu (hidden by default) */
        .hamburger {
            display: none;
            flex-direction: column;
            cursor: pointer;
            gap: 5px;
            height: 30px;
            width: 30px;
        }

        .hamburger div {
            height: 5px;
            background-color: white;
            width: 30px;
            border-radius: 5px;
        }

        /* Mobile responsiveness */
        @media screen and (max-width: 768px) {
            .row {
                flex-direction: column;
                gap: 20px;
                padding: 0 10px; /* manjši odmiki za mobilne naprave */
            }

            .column {
                max-width: 100%;
                flex: 1;
            }

            .images-row {
                flex-direction: column;
                gap: 20px;
            }

            .images-row img {
                width: 100%;
            }

            .image-header {
                font-size: 1.5rem;
            }

            .top-bar .links {
                display: none; /* Hide links on small screens */
            }

            .hamburger {
                display: flex; /* Show hamburger on small screens */
            }
        }

        /* Overlay Menu */
        .overlay {
            height: 100%;
            width: 0;
            position: fixed;
            z-index: 1;
            top: 0;
            left: 0;
            background-color: #00116f;
            overflow-x: hidden;
            transition: 0.5s;
            font-family: 'Roboto', Arial, sans-serif;
        }

        .overlay-content {
            position: relative;
            top: 25%;
            width: 100%;
            text-align: center;
        }

        .overlay a {
            padding: 8px;
            text-decoration: none;
            font-size: 36px;
            color: #818181;
            display: block;
            transition: 0.3s;
            color: #ffffff;
        }

        .overlay a:hover, .overlay a:focus {
            color: #f1f1f1;
        }

        .overlay .closebtn {
            position: absolute;
            top: 20px;
            right: 45px;
            font-size: 60px;
        }

        @media screen and (max-height: 450px) {
            .overlay a {font-size: 20px}
            .overlay .closebtn {
                font-size: 40px;
                top: 15px;
                right: 35px;
            }
        }
    </style>
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="prijavljeni">
            <?php
                if (isset($_SESSION["prijavljen"]) && $_SESSION["prijavljen"] == TRUE) {
                    echo "Prijavljeni ste kot: <strong>{$_SESSION['Ime']} {$_SESSION['Priimek']}</strong>";
                }
            ?>
        </div>
        <div class="links">
            <a href="Dobrodosli.php">Domov</a>
            <a href="Urnik.php">Urnik</a>
            <a href="Odjava.php">Odjava</a>
        </div>
        <!-- Hamburger Menu Icon -->
        <div class="hamburger" onclick="openNav()">
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>

    <h2>Izberite trenerja in vadbo</h2>
    <form method="post" action="IzbiraTrenerja.php">
        <input type="hidden" name="idup" value="<?php echo htmlspecialchars($_SESSION["ID"]); ?>">
        <table>
            <tr>
                <th>Osebni trener:</th>
                <td>
                    <select name="osebnitrener">
                        <option value="0">Tom Lut</option>
                        <option value="1">Matija Novak</option>
                        <option value="2">Ana Pula</option>
                        <option value="3">Mia Lotes</option>
                        <option value="4">Nejc Detar</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th>Program:</th>
                <td>
                    <select name="program">
                        <option value="0">Yoga - Ponedeljek (15:00 - 16:30) - 20€</option>
                        <option value="1">Calisthenics - Torek (15:00 - 16:30) - 25€</option>
                        <option value="2">Dvigovanje uteži - Petek (12:00 - 14:00) - 25€</option>
                        <option value="3">Kondicijski trening - Sreda (12:00 - 13:45) - 30€</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center;">
                    <input type="submit" name="submit" value="Izberi">
                </td>
            </tr>
        </table>
    </form>

    <h3 style="text-align: center;">Najboljši trenerji Tom Lut, Mitja Novak in Nejc Detar</h3>

    <div class="row">
        <div class="column">
            <img src="Images/Trener1.jpg" alt="Tom Lut - Specializiran za jogo in prehrano">
        </div>
        <div class="column">
            <img src="Images/Trener2.png" alt="Nejc Detar - Specializiran za dvigovanje uteži in skupinske treninge">
        </div>
        <div class="column">
            <img src="Images/Trener3.jpg" alt="Matija Novak - Specializiran za kalisteniko in planiranje vadb">
        </div>
    </div>

    <div class="footer">
        <p>
            Tel: 076278902 | 
            <a href="mailto:Gym@gmail.com?subject=Vprašanje%20o%20vadbi&body=Pozdravljeni,%20rad%20bi%20imel%20več%20informacij%20o%20vadbi.">E-mail: Gym@gmail.com</a>
        </p>
    </div>

    <!-- Overlay Menu -->
    <div id="myNav" class="overlay">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <div class="overlay-content">
            <a href="Dobrodosli.php">Domov</a>
            <a href="Urnik.php">Urnik</a>
            <a href="Odjava.php">Odjava</a>
        </div>
    </div>

    <script>
        function openNav() {
            document.getElementById("myNav").style.width = "100%";
        }

        function closeNav() {
            document.getElementById("myNav").style.width = "0%";
        }
    </script>
</body>
</html>

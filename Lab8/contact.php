<link rel="stylesheet" href="css/style.css">
<?php
require_once("cfg.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . "/PHPMailer/src/PHPMailer.php";
require_once __DIR__ . "/PHPMailer/src/SMTP.php";
require_once __DIR__ . "/PHPMailer/src/Exception.php";

function PokazKontakt($komunikat = "")
{
    echo "
    <div class='formularz'>
    <h2>Formularz kontaktowy</h2>

    <form method='post' action='contact.php?send=1'>
        Imię:<br>
        <input type='text' name='name' required><br><br>

        Email:<br>
        <input type='email' name='email' required><br><br>

        Treść wiadomości:<br>
        <textarea name='message' rows='8' cols='50' required></textarea><br><br>

        <input type='submit' value='Wyślij wiadomość'>
    </form>
        <a href='contact.php?remind=1'>
        <button>Przypomnij hasło</button>
        </a>
    
    <div style='color:green;'>$komunikat</div>
    </div>
    ";
}

function WyslijMailKontakt()
{
    if (!isset($_POST['email']) || !isset($_POST['message'])) {
        PokazKontakt("Nie wypełniłeś wszystkich pól.");
        return;
    }

    $name    = htmlspecialchars($_POST['name']);
    $email   = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    global $mail_to;

    $subject = "Wiadomość z formularza kontaktowego od: $name";

    $mail_body = "Imię: $name\nEmail: $email\n\nTreść wiadomości:\n$message";

    mail($mail_to, $subject, $mail_body);
    echo "<div class = 'formularz'><h3>Wiadomość została wysłana!</h3><br>
    <a href='contact.php'><button>Powrót</button></a></div>";
}

function PrzypomnijHaslo()
{
    global $login, $pass;

    echo '
    <div class="formularz">
    <h2>Przypomnienie hasła administratora</h2>

    <form method="post" action="">
        Podaj e-mail administratora:<br>
        <input type="email" name="email_admin" required><br><br>

        <input type="submit" name="przypomnij" value="Wyślij przypomnienie hasła">
    </form>
        <a href="contact.php">
        <button>Powrót</button>
        </a>
    </div>
    ';

    if (isset($_POST['przypomnij'])) {

        $email = $_POST['email_admin'];

        $to = $email;
        $temat = "Przypomnienie hasła do panelu admina";
        $tresc = "Login: $login\nHasło: $pass\n\nUWAGA: nie przekazuj tego maila dalej!";
        $naglowki = "From: no-reply@twojastrona.pl\r\n";

        if (mail($to, $temat, $tresc, $naglowki)) {
            echo "<div style='color:green;'>Hasło zostało wysłane na podany email!</div>";
        } else {
            echo "<div style='color:red;'>Błąd podczas wysyłania maila!</div>";
        }
    }
}

if (isset($_GET['send'])) {
    WyslijMailKontakt();
}
elseif (isset($_GET['remind'])) {
    PrzypomnijHaslo();
}
else {
    PokazKontakt();
}

?>
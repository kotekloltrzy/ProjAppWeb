<?php
/***********************************************************************
 * PLIK: contact.php
 * OPIS: Formularz kontaktowy z obsługą PHPMailer przez SMTP.
 ***********************************************************************/

// 1. Importowanie bibliotek PHPMailer i konfiguracji
require_once("cfg.php");
require_once __DIR__ . "/PHPMailer/src/PHPMailer.php";
require_once __DIR__ . "/PHPMailer/src/SMTP.php";
require_once __DIR__ . "/PHPMailer/src/Exception.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * POMOCNICZA FUNKCJA WYSYŁAJĄCA MAILE PRZEZ SMTP
 * Wypełnij dane SMTP zgodnie z Twoim dostawcą poczty.
 */
function WyslijSMTP($do, $temat, $tresc) {
    $mail = new PHPMailer(true);

    try {
        // --- KONFIGURACJA SERWERA SMTP ---
        $mail->isSMTP();
        $mail->Host       = 'sandbox.smtp.mailtrap.io'; // SERWER (np. smtp.gmail.com lub mailtrap)
        $mail->SMTPAuth   = true;
        $mail->Username   = '280bf9530f72a9';           // TWÓJ LOGIN
        $mail->Password   = 'e453ac5310d2b8';              // TWOJE HASŁO (lub hasło aplikacji)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 2525;
        $mail->CharSet    = "UTF-8";

        // --- ODBIORCY ---
        $mail->setFrom('no-reply@moja-strona.pl', 'Formularz Kontaktowy');
        $mail->addAddress($do);

        // --- TREŚĆ ---
        $mail->isHTML(false);
        $mail->Subject = $temat;
        $mail->Body    = $tresc;

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Logowanie błędu do konsoli serwera w razie problemów
        error_log("Błąd PHPMailer: {$mail->ErrorInfo}");
        return false;
    }
}

/**
 * FUNKCJA: PokazKontakt()
 * Wyświetla formularz HTML.
 */
function PokazKontakt($komunikat = "")
{
    echo "
    <div class='formularz' style='max-width: 500px; margin: 20px auto; padding: 20px; border: 1px solid #ddd;'>
        <h2>Formularz kontaktowy</h2>
        <form method='post' action='contact.php?send=1'>
            Imię:<br>
            <input type='text' name='name' style='width:100%' required><br><br>

            Email:<br>
            <input type='email' name='email' style='width:100%' required><br><br>

            Treść wiadomości:<br>
            <textarea name='message' rows='8' style='width:100%' required></textarea><br><br>

            <input type='submit' value='Wyślij wiadomość' style='padding:10px 20px; cursor:pointer;'>
        </form>
        <br>
        <a href='contact.php?remind=1'><button>Przypomnij hasło administratora</button></a>
        <br><br>
        <div style='color:green; font-weight:bold;'>$komunikat</div>
        <br>
        <a href='index.php'>← Wróć do strony głównej</a>
    </div>";
}

/**
 * FUNKCJA: WyslijMailKontakt()
 * Logika wysyłania zapytania od użytkownika.
 */
function WyslijMailKontakt()
{
    if (!isset($_POST['email']) || !isset($_POST['message'])) {
        PokazKontakt("Błąd: Nie wypełniono pól formularza.");
        return;
    }

    $name    = htmlspecialchars(trim($_POST['name']));
    $email   = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $message = htmlspecialchars(trim($_POST['message']));

    global $mail_to; // Zmienna z cfg.php

    $subject   = "Nowa wiadomość od: $name";
    $mail_body = "Nadawca: $name\nEmail: $email\n\nTreść:\n$message";

    if (WyslijSMTP($mail_to, $subject, $mail_body)) {
        echo "<div class='formularz' style='text-align:center;'>
                <h3>Wiadomość została pomyślnie wysłana!</h3>
                <a href='contact.php'><button>Powrót</button></a>
              </div>";
    } else {
        PokazKontakt("<span style='color:red;'>Błąd wysyłki: Sprawdź konfigurację SMTP.</span>");
    }
}

/**
 * FUNKCJA: PrzypomnijHaslo()
 * Wysyła dane logowania do admina.
 */
function PrzypomnijHaslo()
{
    global $login, $pass; // Dane z cfg.php

    echo '
    <div class="formularz" style="max-width: 500px; margin: 20px auto; padding: 20px; border: 1px solid #ddd;">
        <h2>Przypomnienie hasła</h2>
        <form method="post" action="">
            Podaj e-mail administratora:<br>
            <input type="email" name="email_admin" style="width:100%" required><br><br>
            <input type="submit" name="przypomnij" value="Wyślij przypomnienie">
        </form>
        <br>
        <a href="contact.php"><button>Anuluj</button></a>
    </div>';

    if (isset($_POST['przypomnij'])) {
        $email_admin = filter_input(INPUT_POST, 'email_admin', FILTER_SANITIZE_EMAIL);
        
        $temat = "Odzyskiwanie dostępu do Panelu Admina";
        $tresc = "Twoje dane do logowania:\n\nLogin: $login\nHasło: $pass\n\nZalecamy zmianę hasła po zalogowaniu.";

        if (WyslijSMTP($email_admin, $temat, $tresc)) {
            echo "<p style='color:green; text-align:center;'>Dane zostały wysłane na e-mail: $email_admin</p>";
        } else {
            echo "<p style='color:red; text-align:center;'>Błąd wysyłki. Skontaktuj się z administratorem serwera.</p>";
        }
    }
}

// --- ROUTING (WYBÓR AKCJI) ---
if (isset($_GET['send'])) {
    WyslijMailKontakt();
} elseif (isset($_GET['remind'])) {
    PrzypomnijHaslo();
} else {
    PokazKontakt();
}
?>
<?php
// =============================================
// FICHIER : send_email.php
// API Mailtrap Fonctionnelle
// =============================================

require __DIR__ . '/../../vendor/autoload.php';

// 1. Configuration Mailtrap (TESTÉE avec vos identifiants)
$config = [
    'api_token' => '14606584ace18ec526cdd27091fc8d07', // Votre token fonctionnel
    'inbox_id'  => '3640124',                          // Votre inbox ID correct
    'from'      => 'hello@example.com',                // Adresse test approuvée
    'to'        => 'emnakarray61@gmail.com'            // Votre email de test
];

// 2. Préparation des données (identique à votre test cURL réussi)
$emailData = [
    'from' => [
        'email' => $config['from'],
        'name' => 'Mailtrap Test'
    ],
    'to' => [
        ['email' => $config['to']]
    ],
    'subject' => 'Test PHP Réussi!',
    'text' => 'Félicitations, votre script PHP fonctionne avec Mailtrap!',
    'category' => 'Integration Test'
];

// 3. Envoi via API Mailtrap
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL            => "https://sandbox.api.mailtrap.io/api/send/{$config['inbox_id']}",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_HTTPHEADER     => [
        'Authorization: Bearer ' . $config['api_token'],
        'Content-Type: application/json'
    ],
    CURLOPT_POSTFIELDS     => json_encode($emailData)
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 4. Affichage des résultats
echo "<h2>Résultat de l'envoi</h2>";
echo "<p><strong>Code HTTP :</strong> $httpCode</p>";

if ($httpCode === 200) {
    echo '<div style="color:green;">Email envoyé avec succès!</div>';
    echo '<p>Vérifiez votre inbox Mailtrap :</p>';
    echo '<a href="https://mailtrap.io/inboxes/'.$config['inbox_id'].'/messages" target="_blank">';
    echo 'https://mailtrap.io/inboxes/'.$config['inbox_id'].'/messages';
    echo '</a>';
} else {
    echo '<div style="color:red;">Erreur lors de l\'envoi</div>';
    echo '<pre>'.print_r(json_decode($response, true), true).'</pre>';
}

// Fermeture de la session cURL
curl_close($ch);
?>
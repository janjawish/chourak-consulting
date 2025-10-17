<?php
// config.php — DOIT retourner un tableau (return [...]) et rien afficher.
return [
  'db' => [
    'host'    => 'localhost',   // XAMPP: localhost
    'port'    => 3306,
    'name'    => 'chourak_site',
    'user'    => 'root',        // XAMPP par défaut
    'pass'    => '',            // XAMPP par défaut: vide
    'charset' => 'utf8mb4',
  ],

  'mail_to' => 'contact@chourak-consulting.com',

  'smtp' => [
    'host'   => 'smtp.ionos.fr',
    'port'   => 587,
    'secure' => 'tls',          // 'ssl' si 465
    'user'   => 'contact@chourak-consulting.com',
    'pass'   => 'VOTRE_MDP_SMTP'
  ],
];

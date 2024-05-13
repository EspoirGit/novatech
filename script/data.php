<?php
if (isset($_POST['sub'])) {
  // Initialize empty array for data
  $data = [];

  // Extract Prestataire information
  $data['Prestataire'] = [
    'nom' => $_POST['nomPrestataire'],
    'prenom' => $_POST['prenomPrestataire'],
    'contact' => $_POST['contactPrestataire'],
  ];

  // Extract invoice information
  $data['facture'] = [
    'numero' => $_POST['numeroFacture'],
    'date' => $_POST['dateFacture'],
  ];

  // Extract and calculate article details (assuming a maximum of 2 articles)
  $articles = [];
  for ($i = 1; $i <= 10; $i++) {
    if (isset($_POST['description' . $i]) && !empty($_POST['description' . $i])) {
      $articles[] = [
        'description' => $_POST['description' . $i],
        'quantite' => (int) $_POST['quantite' . $i],
        'prix_unitaire' => (float) $_POST['prixUnitaire' . $i],
        'total' => (float) $_POST['quantite' . $i] * (float) $_POST['prixUnitaire' . $i],
      ];
    }
  }

  // Calculate total for all articles
  $totalGeneral = 0;
  foreach ($articles as $article) {
    $totalGeneral += $article['total'];
  }

  // Add articles and totalGeneral to data array
  $data['articles'] = $articles;
  $data['total_general'] = $totalGeneral;

  // convert total number value in letter in letter
  function nombreEnLettres($nombre)
  {
    $unites = array('', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf');
    $dizaines = array('', 'dix', 'vingt', 'trente', 'quarante', 'cinquante', 'soixante', 'soixante-dix', 'quatre-vingt', 'quatre-vingt-dix');
    $centaines = array('', 'cent', 'deux-cents', 'trois-cents', 'quatre-cents', 'cinq-cents', 'six-cents', 'sept-cents', 'huit-cents', 'neuf-cents');

    $resultat = '';

    // Décomposer le nombre en milliers, centaines et dizaines/unités
    $milliers = floor($nombre / 1000);
    $reste = $nombre % 1000;
    $centaines_part = floor($reste / 100);
    $dizaines_part = floor(($reste % 100) / 10);
    $unites_part = $reste % 10;

    // Gérer les milliers
    if ($milliers > 0) {
      $resultat .= nombreEnLettres($milliers) . ' mille ';
    }

    // Gérer les centaines
    if ($centaines_part > 0) {
      $resultat .= $centaines[$centaines_part] . ' ';
    }

    // Gérer les dizaines et les unités
    if ($dizaines_part > 1) {
      $resultat .= $dizaines[$dizaines_part] . ' ';
      if ($unites_part > 0) {
        $resultat .= $unites[$unites_part] . ' ';
      }
    } elseif ($dizaines_part == 1) {
      if ($unites_part == 0) {
        $resultat .= 'dix ';
      } elseif ($unites_part == 1) {
        $resultat .= 'onze ';
      } elseif ($unites_part == 2) {
        $resultat .= 'douze ';
      } elseif ($unites_part == 3) {
        $resultat .= 'treize ';
      } elseif ($unites_part == 4) {
        $resultat .= 'quatorze ';
      } elseif ($unites_part == 5) {
        $resultat .= 'quinze ';
      } elseif ($unites_part == 6) {
        $resultat .= 'seize ';
      } else {
        $resultat .= $dizaines[1] . ' ' . $unites[$unites_part] . ' ';
      }
    } elseif ($unites_part > 0) {
      $resultat .= $unites[$unites_part] . ' ';
    }

    return $resultat;
  }


  $totalInletter = nombreEnLettres($totalGeneral);
  $data['totalinletter'] = $totalInletter;
  // Encode data to JSON string
  $json_data = json_encode($data, JSON_PRETTY_PRINT);



  function generate($template_id, $api_key, $data)
  {
    $url = "https://rest.apitemplate.io/v2/create-pdf?template_id=" . $template_id;
    $headers = array("X-API-KEY: " . $api_key);
    $curl = curl_init();
    if ($data)
      curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($curl);
    curl_close($curl);
    if (!$result) {
      return null;
    } else {
      $json_result = json_decode($result, 1);
      if ($json_result["status"] == "success") {
        $monUrl = $json_result["download_url"];
        echo '
            <h2 style="text-align: center;">Bravo, votre Facture est prête !</h2>
            <div style="text-align: center; margin-top: 20px;">
                <a href=' . $monUrl . ' style="padding: 10px 20px; background-color: #007bff; color: #fff; text-decoration: none; border-radius: 5px;"> Télécharger </a>
            </div>
            ';
      } else {
        return null;
      }
    }
  }

  $tempate_id = "39477b234de7510e";
  $api_key = "d39aMTkzNTY6MTY0NTg6TWVkREVUcjRtM240RWs0SA=";
  echo generate($tempate_id, $api_key, $json_data);


}

<?php
// fonctions_validation.php - Fonctions de validation serveur de données

if (count(get_included_files()) == 1) {
    exit("Accès direct non autorisé.");
}

/**
 * Valide le format d'une adresse email
 * @param string $email
 * @return bool
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Valide le format d'un numéro de téléphone
 * (Autorise chiffres, espaces, tirets, parenthèses et le +)
 * @param string $phone
 * @return bool
 */
function validatePhone($phone) {
    return preg_match('/^[+\d\s\-\(\)]{8,25}$/', $phone);
}

/**
 * Vérifie si les champs requis sont présents et non vides
 * @param array $fields Liste des champs attendus
 * @param array $data Données reçues ($_POST ou $_GET)
 * @return array Liste des champs manquants
 */
function validateRequiredFields($fields, $data) {
    $missing = [];
    foreach ($fields as $field) {
        if (!isset($data[$field]) || trim((string)$data[$field]) === '') {
            $missing[] = $field;
        }
    }
    return $missing;
}

/**
 * Valide la cohérence de deux dates (date de départ >= aujourd'hui et date de fin >= date de départ)
 * @param string $startDate Date de début au format YYYY-MM-DD
 * @param string $endDate Date de fin optionnelle au format YYYY-MM-DD
 * @return string|bool true si valide, sinon message d'erreur
 */
function validateDates($startDate, $endDate = null) {
    $today = date('Y-m-d');
    
    if ($startDate < $today) {
        return "La date de départ ne peut pas être dans le passé.";
    }
    
    if ($endDate !== null && $endDate !== '') {
        if ($endDate < $startDate) {
            return "La date de retour doit être postérieure ou égale à la date de départ.";
        }
    }
    
    return true;
}
?>

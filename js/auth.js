// auth.js - Connexion / inscription (validation + AJAX)
document.addEventListener("DOMContentLoaded", function() {
    // Validation du formulaire d'inscription
    const registerForm = document.getElementById("registerForm");
    if (registerForm) {
        registerForm.addEventListener("submit", function(e) {
            const mdp = document.getElementById("mot_de_passe").value;
            const confirmMdp = document.getElementById("confirm_mot_de_passe").value;
            const email = document.getElementById("email").value;
            const telephone = document.getElementById("telephone").value;

            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const telPattern = /^[+\d\s\-\(\)]{8,25}$/;

            let errors = [];

            if (!emailPattern.test(email)) {
                errors.push("L'adresse email n'est pas valide.");
            }

            if (!telPattern.test(telephone)) {
                errors.push("Le numéro de téléphone n'est pas valide.");
            }

            if (mdp.length < 8) {
                errors.push("Le mot de passe doit contenir au moins 8 caractères.");
            }

            if (mdp !== confirmMdp) {
                errors.push("Les mots de passe ne correspondent pas.");
            }

            if (errors.length > 0) {
                e.preventDefault();
                // Utilisation d'une alerte standard (peut être remplacée par un bloc d'alerte DOM plus esthétique)
                alert(errors.join("\n"));
            }
        });
    }
});

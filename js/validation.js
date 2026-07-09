// validation.js - Validation front-end universelle de tous les formulaires AIR MAKEN

(function () {
    'use strict';

    /**
     * Affiche une erreur inline sous un champ
     */
    function showFieldError(field, message) {
        clearFieldError(field);
        field.classList.add('field-error');
        var errorEl = document.createElement('span');
        errorEl.className = 'field-error-msg';
        errorEl.textContent = message;
        field.parentNode.appendChild(errorEl);
    }

    /**
     * Efface une erreur inline sous un champ
     */
    function clearFieldError(field) {
        field.classList.remove('field-error', 'field-valid');
        var existing = field.parentNode.querySelector('.field-error-msg');
        if (existing) existing.remove();
    }

    /**
     * Marque un champ comme valide
     */
    function markValid(field) {
        clearFieldError(field);
        field.classList.add('field-valid');
    }

    // Patterns
    var EMAIL_PATTERN  = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    var TEL_PATTERN    = /^[+\d\s\-()]{7,25}$/;
    var MDP_MIN_LEN    = 8;

    /**
     * Valide un champ selon son type / attributs
     * @returns {boolean}
     */
    function validateField(field) {
        var val = field.value.trim();
        var name = field.name || field.id || '';

        // Champ obligatoire vide
        if (field.required && val === '') {
            showFieldError(field, 'Ce champ est obligatoire.');
            return false;
        }

        // Email
        if (field.type === 'email' || name === 'email') {
            if (val !== '' && !EMAIL_PATTERN.test(val)) {
                showFieldError(field, 'Adresse e-mail invalide.');
                return false;
            }
        }

        // Téléphone
        if (name === 'telephone' || name === 'tel') {
            if (val !== '' && !TEL_PATTERN.test(val)) {
                showFieldError(field, 'Numéro de téléphone invalide (ex: +240 333 444 555).');
                return false;
            }
        }

        // Mot de passe
        if ((name === 'mot_de_passe' || name === 'nouveau_mdp') && val !== '' && val.length < MDP_MIN_LEN) {
            showFieldError(field, 'Le mot de passe doit contenir au moins ' + MDP_MIN_LEN + ' caractères.');
            return false;
        }

        // Confirmation de mot de passe
        if (name === 'confirm_mot_de_passe' || name === 'confirm_mdp') {
            var original = document.querySelector('input[name="mot_de_passe"], input[name="nouveau_mdp"]');
            if (original && val !== original.value) {
                showFieldError(field, 'Les mots de passe ne correspondent pas.');
                return false;
            }
        }

        // Minlength natif
        if (field.minLength && field.minLength > 0 && val.length > 0 && val.length < field.minLength) {
            showFieldError(field, 'Ce champ doit contenir au moins ' + field.minLength + ' caractères.');
            return false;
        }

        // Prix / nombre positif
        if (field.type === 'number' && val !== '') {
            var num = parseFloat(val);
            if (isNaN(num) || (field.min !== '' && num < parseFloat(field.min))) {
                showFieldError(field, 'Valeur numérique invalide.');
                return false;
            }
        }

        // Champ valide
        if (val !== '') markValid(field);
        return true;
    }

    document.addEventListener('DOMContentLoaded', function () {

        // ---------------------------------------------------------
        // A) Validation inline au blur (sortie d'un champ)
        // ---------------------------------------------------------
        document.querySelectorAll('input, select, textarea').forEach(function (field) {
            // Ignorer les champs cachés et CSRF
            if (field.type === 'hidden' || field.type === 'submit' || field.type === 'button') return;

            field.addEventListener('blur', function () {
                validateField(field);
            });

            field.addEventListener('input', function () {
                if (field.classList.contains('field-error')) {
                    clearFieldError(field);
                }
            });
        });

        // ---------------------------------------------------------
        // B) Validation complète à la soumission du formulaire
        // ---------------------------------------------------------
        document.querySelectorAll('form').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                var isValid = true;
                var firstError = null;

                form.querySelectorAll('input, select, textarea').forEach(function (field) {
                    if (field.type === 'hidden' || field.type === 'submit' || field.type === 'button') return;
                    var ok = validateField(field);
                    if (!ok) {
                        isValid = false;
                        if (!firstError) firstError = field;
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    if (firstError) {
                        firstError.focus();
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            });
        });

        // ---------------------------------------------------------
        // C) Compteur de caractères pour les textareas avec maxlength
        // ---------------------------------------------------------
        document.querySelectorAll('textarea[maxlength]').forEach(function (ta) {
            var max = parseInt(ta.getAttribute('maxlength'));
            var counter = document.createElement('span');
            counter.className = 'char-counter';
            counter.textContent = '0 / ' + max;
            ta.parentNode.appendChild(counter);

            ta.addEventListener('input', function () {
                var len = ta.value.length;
                counter.textContent = len + ' / ' + max;
                counter.style.color = len > max * 0.9 ? 'var(--danger)' : 'var(--text-muted)';
            });
        });

    });

})();

// reservation.js - Logique du formulaire de réservation (dynamique)
document.addEventListener("DOMContentLoaded", function() {
    const typeServiceSelect = document.getElementById("type_service");

    if (typeServiceSelect) {
        function toggleServiceFields() {
            const selectedType = typeServiceSelect.value;
            const sections = document.querySelectorAll(".dynamic-fields-section");

            sections.forEach(section => {
                const inputs = section.querySelectorAll("input, select, textarea");
                
                if (section.id === "section_" + selectedType) {
                    section.classList.add("active");
                    // Rétablir la validation requise sur les champs visibles
                    inputs.forEach(input => {
                        if (input.hasAttribute("data-required")) {
                            input.setAttribute("required", "required");
                        }
                    });
                } else {
                    section.classList.remove("active");
                    // Supprimer required sur les champs cachés pour ne pas bloquer le formulaire
                    inputs.forEach(input => {
                        input.removeAttribute("required");
                    });
                }
            });
        }

        typeServiceSelect.addEventListener("change", toggleServiceFields);
        
        // Initialisation immédiate au cas où la page se charge avec une pré-sélection (via $_GET)
        toggleServiceFields();
    }
});

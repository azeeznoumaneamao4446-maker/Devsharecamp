document.addEventListener('DOMContentLoaded', () => {
    const filterChips   = document.querySelectorAll('.filter-bar .filter-chip');
    const matiereChips  = document.querySelectorAll('.matiere-chip');
    const searchInput   = document.getElementById('search-input');
    const cards         = document.querySelectorAll('.resource-card');
    const emptyMessage  = document.querySelector('.resource-empty');

    let activeType    = 'tous';
    let activeMatiere = null; // null = toutes les matières

    function applyFilters() {
        const query = searchInput.value.trim().toLowerCase();
        let visibleCount = 0;

        cards.forEach((card) => {
            const typeOk    = activeType === 'tous' || card.dataset.type === activeType;
            const matiereOk = !activeMatiere || card.dataset.matiere === activeMatiere;
            const searchOk  = !query || card.dataset.title.includes(query);
            const show      = typeOk && matiereOk && searchOk;

            card.hidden = !show;
            if (show) visibleCount++;
        });

        if (emptyMessage) {
            emptyMessage.hidden = visibleCount !== 0;
        }
    }

    filterChips.forEach((chip) => {
        chip.addEventListener('click', () => {
            filterChips.forEach((c) => c.classList.remove('filter-chip--active'));
            chip.classList.add('filter-chip--active');
            activeType = chip.dataset.type;
            applyFilters();
        });
    });

    matiereChips.forEach((chip) => {
        chip.addEventListener('click', () => {
            const alreadyActive = chip.classList.contains('matiere-chip--active');
            matiereChips.forEach((c) => c.classList.remove('matiere-chip--active'));

            if (alreadyActive) {
                activeMatiere = null; // on reclique sur la même matière : on annule le filtre
            } else {
                chip.classList.add('matiere-chip--active');
                activeMatiere = chip.dataset.matiere;
            }
            applyFilters();
        });
    });

    if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
    }
});

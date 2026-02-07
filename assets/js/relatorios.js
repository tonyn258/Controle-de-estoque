document.addEventListener('DOMContentLoaded', function() {
    // Animação suave ao carregar os cards
    const cards = document.querySelectorAll('.report-card');
    cards.forEach((card, index) => {
        card.style.animation = `fadeInUp 0.4s ease forwards ${index * 0.1}s`;
        card.style.opacity = '0'; // Inicialmente invisível para animação
    });

    // Adicionar keyframes dinamicamente se necessário, ou assumir CSS global
    const style = document.createElement('style');
    style.innerHTML = `
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
    `;
    document.head.appendChild(style);
});
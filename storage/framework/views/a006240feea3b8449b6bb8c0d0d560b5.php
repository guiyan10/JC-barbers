<style>
    /*
     * Mantemos apenas ajustes seguros e não intrusivos.
     * A organização principal da dashboard agora é controlada
     * pelas colunas/spans nos widgets (PHP), evitando conflitos
     * com o grid interno do Filament.
     */

    .fi-wi-chart canvas {
        max-width: 100%;
    }

    @media (max-width: 768px) {
        .fi-wi-chart {
            min-height: 16rem;
        }
    }
</style>

<?php /**PATH C:\Users\guiya\OneDrive\Área de Trabalho\Projetos\JC barbers\resources\views/filament/custom-styles.blade.php ENDPATH**/ ?>
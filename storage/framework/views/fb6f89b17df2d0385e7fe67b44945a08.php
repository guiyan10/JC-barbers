<style>
    /* Limitar largura máxima dos widgets em telas grandes */
    @media (min-width: 1920px) {
        .fi-wi-widget {
            max-width: 600px;
        }
        
        .fi-wi-stats-overview {
            max-width: 100%;
        }
        
        .fi-wi-stats-overview .fi-stats-overview-stat {
            max-width: 400px;
        }
    }

    /* Limitar largura dos cards de estatísticas */
    .fi-wi-stats-overview {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
    }

    @media (min-width: 1920px) {
        .fi-wi-stats-overview {
            grid-template-columns: repeat(4, minmax(250px, 400px));
            max-width: 1800px;
            margin: 0 auto;
        }
    }

    /* Limitar largura dos gráficos */
    .fi-wi-chart {
        max-width: 100%;
    }

    @media (min-width: 1920px) {
        .fi-wi-chart {
            max-width: 900px;
            margin: 0 auto;
        }
    }

    /* Ajustar selects para não ultrapassarem o container e não quebrarem texto */
    .fi-input-wrapper {
        max-width: 100%;
    }

    .fi-select {
        max-width: 100%;
    }

    .fi-select-input {
        max-width: 100%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Forçar select a não quebrar linha */
    [data-slot="trigger"] {
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }

    /* Ajustar placeholder e texto dos selects */
    .fi-select .fi-input-wrapper-text {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: block;
    }

    /* Limitar largura dos formulários em telas grandes */
    @media (min-width: 1920px) {
        .fi-form {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .fi-section {
            max-width: 100%;
        }
    }

    /* Ajustar colunas dos formulários */
    @media (min-width: 1920px) {
        .fi-section-content-grid {
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        }
    }

    /* Limitar largura das tabelas */
    @media (min-width: 1920px) {
        .fi-ta-content-ctn {
            max-width: 1800px;
            margin: 0 auto;
        }
    }

    /* Ajustar dropdowns dos selects - IMPORTANTE */
    .fi-select-option {
        white-space: normal !important;
        word-wrap: break-word;
        overflow-wrap: break-word;
        line-height: 1.5;
        padding: 0.5rem 0.75rem !important;
    }

    /* Garantir que o dropdown tenha largura adequada */
    [x-ref="panel"] {
        min-width: 300px !important;
        max-width: 500px !important;
    }

    /* Lista de opções do select com largura adequada */
    .fi-select-list {
        min-width: 300px !important;
        max-width: 500px !important;
    }

    /* Opções individuais com altura e padding adequados */
    .fi-select-list-item {
        min-height: 2.5rem !important;
        padding: 0.5rem 0.75rem !important;
        white-space: normal !important;
        line-height: 1.5 !important;
    }

    /* Limitar largura do container principal */
    @media (min-width: 1920px) {
        .fi-main-ctn {
            max-width: 95%;
            margin: 0 auto;
        }
    }

    /* Ajustar selects com searchable */
    .fi-select-search-input {
        max-width: 100%;
    }

    .fi-select-option-list {
        min-width: 300px !important;
        max-width: 500px !important;
    }

    /* Corrigir quebra de linha em selects */
    .fi-fo-select button[type="button"] {
        white-space: nowrap !important;
    }

    .fi-fo-select .fi-input-wrapper {
        display: block;
    }

    /* Ajustar inputs em geral */
    .fi-input {
        max-width: 100%;
    }

    /* Forçar texto inline nos selects */
    .fi-fo-select button span {
        display: inline-block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
    }

    /* Ajustar dropdown panel com largura adequada */
    [role="listbox"] {
        min-width: 300px !important;
        max-width: 500px !important;
    }

    /* Garantir que os itens do dropdown fiquem legíveis */
    [role="option"] {
        white-space: normal !important;
        word-wrap: break-word !important;
        min-height: 2.5rem !important;
        padding: 0.5rem 0.75rem !important;
        line-height: 1.5 !important;
    }

    /* Ajustar scrollbar do dropdown se necessário */
    [role="listbox"] {
        max-height: 300px;
        overflow-y: auto;
    }
</style>

<?php /**PATH C:\Users\user\JC-barbers\resources\views/filament/custom-styles.blade.php ENDPATH**/ ?>
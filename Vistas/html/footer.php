<footer class="custom-dashboard-footer">
    <div class="footer-container">
        <div class="footer-left">
            <span>&copy;
                <?php echo date('Y'); ?> CRM Iglesia AD Rey de Reyes
            </span>
        </div>
        <div class="footer-right">
            <span>Desarrollado con <i class="fas fa-heart text-danger mx-1" style="font-size: 0.8rem;"></i> por
                <strong>Edson Castillo</strong> <span class="version-badge">v1.2.0</span></span>
        </div>
    </div>
</footer>

<style>
    /* Estilo propio para el Footer Softwys Minimalista */
    .custom-dashboard-footer {
        width: calc(100% - 250px);
        margin-left: 250px;
        background-color: transparent;
        padding: 1.5rem 2rem;
        border-top: 1px dashed #e3e6f0;
        margin-top: auto;
        /* Empuja el footer siempre hacia abajo si el contenido es corto */
        color: #8c98a4;
        font-size: 0.85rem;
        letter-spacing: 0.3px;
        transition: all 0.3s ease-in-out;
    }

    .custom-dashboard-footer .footer-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
    }

    .custom-dashboard-footer strong {
        color: #495057;
        font-weight: 600;
    }

    .custom-dashboard-footer .version-badge {
        background-color: #e3e6f0;
        color: #495057;
        padding: 2px 6px;
        border-radius: 4px;
        margin-left: 8px;
        font-size: 0.75rem;
    }

    /* Responsividad para móviles: centrar y apilar en pantallas pequeñas */
    @media (max-width: 768px) {
        .custom-dashboard-footer {
            width: 100%;
            margin-left: 0;
            padding: 1.5rem 1rem;
        }

        .custom-dashboard-footer .footer-container {
            flex-direction: column;
            text-align: center;
            gap: 8px;
        }
    }
</style>
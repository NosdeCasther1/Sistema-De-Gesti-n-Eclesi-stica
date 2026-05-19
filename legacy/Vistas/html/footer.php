<footer class="footer mt-auto py-3 border-top" style="border-color: var(--border-color) !important; background-color: var(--bg-card);">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <span class="text-muted" style="font-size: 0.85rem;">
            &copy; <?= date('Y') ?> CRM Iglesia AD Rey de Reyes
        </span>
        <span class="text-muted" style="font-size: 0.85rem;">
            Desarrollado con <i class="fas fa-heart text-danger"></i> por <strong>Edson Castillo</strong> <span class="badge bg-secondary ms-1">v1.2.0</span>
        </span>
    </div>
</footer>

    </main> <!-- Cerrar .main-content -->
</div> <!-- Cerrar .wrapper -->

<!-- Core Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= BASE_URL ?>/assets/js/theme-controller.js?v=<?= time() ?>"></script>

</body>
</html>
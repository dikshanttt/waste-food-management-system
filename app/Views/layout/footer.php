<?php
// app/Views/layout/footer.php
declare(strict_types=1);
require_once __DIR__ . '/../../../config/config.php';
?>
        </div>
    </main>

    <footer class="site-footer">
        <div class="container footer-content">
            <div>
                <strong><?= APP_NAME ?></strong> &bull; v<?= APP_VERSION ?>
                <div style="font-size: 0.8rem; margin-top: 0.25rem;">Connecting surplus-food donors and recipients to reduce food waste.</div>
            </div>
            <div>
                <span class="badge badge-available">🌿 Sustainable Community</span>
            </div>
        </div>
    </footer>

    <script src="<?= BASE_URL ?>/js/main.js"></script>
</body>
</html>

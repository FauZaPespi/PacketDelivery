<main class="app-page d-flex flex-column min-vh-100">
    <div class="container py-4 d-flex flex-grow-1 align-items-start justify-content-center">
        <div class="app-card card shadow-sm w-100">

            <header class="app-header">
                <div class="app-header-left">
                    <p class="app-greeting">
                        Bonjour
                        <?= htmlspecialchars(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')) ?>
                    </p>
                </div>
                <div class="app-header-logo">
                    <img src="/img/logo.png" alt="Logo Packet Delivery">
                </div>
                <div class="app-header-right">
                    <a href="/" class="app-logout-btn" title="Se déconnecter" aria-label="Se déconnecter">
                        <i class="bi bi-power"></i>
                    </a>
                </div>
            </header>

            <section class="app-body">

                <nav class="app-date-nav d-flex justify-content-center align-items-center gap-3"
                     aria-label="Navigation par date">
                    <button type="button" aria-label="Date précédente">&laquo;</button>
                    <span><?= htmlspecialchars($dateAffichee ?? '') ?></span>
                    <button type="button" aria-label="Date suivante">&raquo;</button>
                </nav>

                <div class="app-map-placeholder" role="img"
                     aria-label="Emplacement de la carte interactive">
                    Carte interactive (Leaflet) — à intégrer
                </div>

                <?php if (!empty($message)): ?>
                    <p class="app-map-message"><?= htmlspecialchars($message) ?></p>
                <?php endif; ?>

            </section>

            <footer class="app-footer">
                <small>PacketDelivery &copy; <?= htmlspecialchars((string)($year ?? date('Y'))) ?></small>
            </footer>

        </div>
    </div>
</main>

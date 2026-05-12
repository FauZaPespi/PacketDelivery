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

            <section class="app-body row g-4">

                <div class="col-md-6">
                    <h2 class="app-panel-title">Paquets</h2>
                    <input type="search" class="form-control app-search"
                           placeholder="Rechercher par n° postal" aria-label="Rechercher un paquet">
                    <ul class="app-list" aria-label="Liste des paquets">
                        <?php foreach (($paquets ?? []) as $paquet): ?>
                            <li class="app-list-item">
                                <?= htmlspecialchars($paquet['numero']) ?>
                                (<?= htmlspecialchars($paquet['statut']) ?>)
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="app-panel-actions">
                        <button type="button" class="app-icon-btn" title="Ajouter un paquet" aria-label="Ajouter un paquet">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                        <button type="button" class="app-icon-btn" title="Modifier / supprimer un paquet" aria-label="Modifier ou supprimer un paquet">
                            <i class="bi bi-pencil"></i>
                        </button>
                    </div>
                </div>

                <div class="col-md-6">
                    <h2 class="app-panel-title">Livreurs</h2>
                    <input type="search" class="form-control app-search"
                           placeholder="Rechercher par nom ou prénom" aria-label="Rechercher un livreur">
                    <ul class="app-list" aria-label="Liste des livreurs">
                        <?php foreach (($livreurs ?? []) as $livreur): ?>
                            <li class="app-list-item">
                                <?= htmlspecialchars(($livreur['nom'] ?? '') . ' ' . ($livreur['prenom'] ?? '')) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="app-panel-actions">
                        <button type="button" class="app-icon-btn" title="Voir les paquets attribués" aria-label="Voir les paquets attribués">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>

            </section>

            <footer class="app-footer">
                <small>PacketDelivery &copy; <?= htmlspecialchars((string)($year ?? date('Y'))) ?></small>
            </footer>

        </div>
    </div>
</main>

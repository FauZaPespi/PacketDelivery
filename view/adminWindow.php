<main class="app-page d-flex flex-column min-vh-100">
    <div class="container py-4 d-flex flex-grow-1 align-items-start justify-content-center">
        <div class="app-card card shadow-sm w-100">

            <nav class="navbar navbar-light app-navbar border-bottom px-3">
                <div class="container-fluid p-0 d-flex align-items-center justify-content-between gap-3">
                    <span class="navbar-text app-greeting mb-0 text-truncate">
                        Bonjour
                        <?= htmlspecialchars(($user->prenom ?? '') . ' ' . ($user->nom ?? '')) ?>
                    </span>
                    <img src="/img/logo.png" alt="Logo Packet Delivery" class="app-navbar-logo">
                    <a href="/logout"
                        class="btn btn-outline-danger btn-sm d-inline-flex align-items-center justify-content-center"
                        title="Se déconnecter" aria-label="Se déconnecter">
                        <i class="bi bi-power"></i>
                    </a>
                </div>
            </nav>

            <section class="app-body row g-4">

                <div class="col-12 col-lg-6">
                    <h2 class="app-panel-title">Paquets</h2>
                    <div class="mb-3">
                        <input type="search" class="form-control"
                            placeholder="Rechercher par n° postal" aria-label="Rechercher un paquet">
                    </div>
                    <ul class="list-group app-list-group mb-0" aria-label="Liste des paquets">
                        <?php foreach (($paquets ?? []) as $paquet): ?>
                            <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" tabindex="0">
                                <span><?= htmlspecialchars($paquet['numero']) ?></span>
                                <span class="badge text-bg-<?= $paquet['statut'] === 'En cours de livraison' ? 'info' : ($paquet['statut'] === 'Livré' ? 'success' : 'warning') ?>">
                                    <span class="small"><?= htmlspecialchars($paquet['statut']) ?></span>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="app-panel-actions">
                        <button type="button" class="btn btn-outline-primary app-icon-btn" title="Ajouter un paquet" aria-label="Ajouter un paquet">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                        <button type="button" class="btn btn-outline-primary app-icon-btn" title="Modifier / supprimer un paquet" aria-label="Modifier ou supprimer un paquet">
                            <i class="bi bi-pencil"></i>
                        </button>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <h2 class="app-panel-title">Livreurs</h2>
                    <div class="mb-3">
                        <input type="search" class="form-control"
                            placeholder="Rechercher par nom ou prénom" aria-label="Rechercher un livreur">
                    </div>
                    <ul class="list-group app-list-group mb-0" aria-label="Liste des livreurs">
                        <?php foreach (($livreurs ?? []) as $livreur): ?>
                            <li class="list-group-item list-group-item-action" tabindex="0">
                                <?= htmlspecialchars(($livreur['prenom'] ?? '') . ' ' . ($livreur['nom'] ?? '')) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="app-panel-actions">
                        <button type="button" class="btn btn-outline-primary app-icon-btn" title="Voir les paquets attribués" aria-label="Voir les paquets attribués">
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
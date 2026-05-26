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

                <!-- Paquets -->
                <div class="col-12 col-lg-6">
                    <h2 class="app-panel-title">Paquets</h2>
                    <form method="get" action="/admin" class="mb-3">
                        <div class="input-group">
                            <input type="search" class="form-control"
                                name="paquet_search"
                                placeholder="Rechercher par n° postal"
                                aria-label="Rechercher un paquet"
                                value="<?= htmlspecialchars($_GET['paquet_search'] ?? '') ?>">
                            <button type="submit" class="btn btn-outline-secondary" title="Rechercher">
                                <i class="bi bi-search"></i>
                            </button>
                            <?php if (!empty($_GET['paquet_search'])): ?>
                                <a href="/admin" class="btn btn-outline-secondary" title="Effacer">
                                    <i class="bi bi-x-lg"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>

                    <form method="post" action="/admin/paquets/search">
                        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf ?? '') ?>">
                        <ul class="list-group app-list-group mb-0" aria-label="Liste des paquets">
                            <?php foreach (($paquets ?? []) as $paquet): ?>
                                <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <label class="d-flex justify-content-between align-items-center w-100 cursor-pointer">
                                        <input type="radio" name="selected_paquet" value="<?= $paquet['numeroPostal'] ?>" class="me-2">
                                        <span><?= htmlspecialchars($paquet['numeroPostal']) ?></span>
                                        <span class="badge text-bg-<?= $paquet['statutLivraison'] === 'En cours de livraison' ? 'info' : ($paquet['statutLivraison'] === 'Livré' ? 'success' : 'warning') ?>">
                                            <span class="small"><?= htmlspecialchars($paquet['statutLivraison']) ?></span>
                                        </span>
                                    </label>
                                </li>
                            <?php endforeach; ?>
                            <?php if (empty($paquets)): ?>
                                <li class="list-group-item text-muted">Aucun paquet trouvé.</li>
                            <?php endif; ?>
                        </ul>
                    </form>

                    <div class="app-panel-actions">
                        <a href="/admin/paquet/add" class="btn btn-outline-primary app-icon-btn" title="Ajouter un paquet" aria-label="Ajouter un paquet">
                            <i class="bi bi-plus-lg"></i>
                        </a>
                        <form method="get" action="/admin/paquet/edit" class="d-inline">
                            <button type="submit" class="btn btn-outline-primary app-icon-btn" title="Modifier / supprimer un paquet" aria-label="Modifier ou supprimer un paquet"
                                onclick="this.form.action = '/admin/paquet/edit/' + getSelectedValue('selected_paquet'); return hasSelectedValue('selected_paquet');">
                                <i class="bi bi-pencil"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Livreurs -->
                <div class="col-12 col-lg-6">
                    <h2 class="app-panel-title">Livreurs</h2>
                    <form method="get" action="/admin" class="mb-3">
                        <div class="input-group">
                            <input type="search" class="form-control"
                                name="livreur_search"
                                placeholder="Rechercher par nom ou prénom"
                                aria-label="Rechercher un livreur"
                                value="<?= htmlspecialchars($_GET['livreur_search'] ?? '') ?>">
                            <button type="submit" class="btn btn-outline-secondary" title="Rechercher">
                                <i class="bi bi-search"></i>
                            </button>
                            <?php if (!empty($_GET['livreur_search'])): ?>
                                <a href="/admin" class="btn btn-outline-secondary" title="Effacer">
                                    <i class="bi bi-x-lg"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>

                    <form method="get" action="/admin/livreur">
                        <ul class="list-group app-list-group mb-0" aria-label="Liste des livreurs">
                            <?php foreach (($livreurs ?? []) as $livreur): ?>
                                <li class="list-group-item list-group-item-action">
                                    <label class="d-flex justify-content-between align-items-center w-100 cursor-pointer">
                                        <input type="radio" name="selected_livreur" value="<?= $livreur['id_employe'] ?>" class="me-2">
                                        <?= htmlspecialchars(($livreur['prenom'] ?? '') . ' ' . ($livreur['nom'] ?? '')) ?>
                                    </label>
                                </li>
                            <?php endforeach; ?>
                            <?php if (empty($livreurs)): ?>
                                <li class="list-group-item text-muted">Aucun livreur trouvé.</li>
                            <?php endif; ?>
                        </ul>
                    </form>

                    <div class="app-panel-actions">
                        <a href="/admin/livreur/{id}/paquets" class="btn btn-outline-primary app-icon-btn" title="Voir les paquets attribués" aria-label="Voir les paquets attribués"
                           onclick="this.href = '/admin/livreur/' + getSelectedValue('selected_livreur') + '/paquets'; return hasSelectedValue('selected_livreur');">
                            <i class="bi bi-search"></i>
                        </a>
                    </div>
                </div>

            </section>

            <footer class="app-footer">
                <small>PacketDelivery &copy; <?= htmlspecialchars((string)($year ?? date('Y'))) ?></small>
            </footer>

        </div>
    </div>
</main>

<script>
function getSelectedValue(name) {
    const selected = document.querySelector('input[name="' + name + '"]:checked');
    return selected ? selected.value : null;
}

function hasSelectedValue(name) {
    return getSelectedValue(name) !== null;
}

document.addEventListener('DOMContentLoaded', function() {

});
</script>
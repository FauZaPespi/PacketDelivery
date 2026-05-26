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
                    <div class="d-flex align-items-center gap-2">
                        <a href="/admin" class="btn btn-outline-secondary btn-sm" title="Retour">
                            <i class="bi bi-arrow-left"></i> Retour
                        </a>
                        <a href="/logout"
                            class="btn btn-outline-danger btn-sm d-inline-flex align-items-center justify-content-center"
                            title="Se déconnecter" aria-label="Se déconnecter">
                            <i class="bi bi-power"></i>
                        </a>
                    </div>
                </div>
            </nav>

            <section class="app-body">
                <h2 class="app-panel-title text-center mb-4">
                    Paquets de <?= htmlspecialchars(($livreur['prenom'] ?? '') . ' ' . ($livreur['nom'] ?? '')) ?>
                </h2>

                <form method="get" action="/admin/livreur/<?= $livreur['id_employe'] ?>/paquets" class="mb-4">
                    <div class="row justify-content-center">
                        <div class="col-md-4">
                            <label for="date" class="form-label">Date de livraison</label>
                            <input type="date" id="date" name="date" class="form-control"
                                value="<?= htmlspecialchars($date ?? '') ?>"
                                min="2024-01-01" max="2030-12-31">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">Changer de date</button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>N° paquet</th>
                                <th>Destinataire</th>
                                <th>Adresse</th>
                                <th>Statut</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (($paquets ?? []) as $paquet): ?>
                                <tr>
                                    <td><?= htmlspecialchars($paquet['numero_postal']) ?></td>
                                    <td><?= htmlspecialchars($paquet['prenomDestinataire'] . ' ' . $paquet['nomDestinataire']) ?></td>
                                    <td><?= htmlspecialchars($paquet['adresseDestinataire']) ?></td>
                                    <td>
                                        <span class="badge text-bg-<?= $paquet['statutLivraison'] === 'En cours de livraison' ? 'info' : ($paquet['statutLivraison'] === 'Livré' ? 'success' : 'warning') ?>">
                                            <?= htmlspecialchars($paquet['statutLivraison']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($paquet['statutLivraison'] !== 'Livré'): ?>
                                            <form method="post" action="/admin/paquet/livre/<?= $paquet['numero_postal'] ?>" class="d-inline">
                                                <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf ?? '') ?>">
                                                <button type="submit" class="btn btn-sm btn-success" title="Marquer comme livré"
                                                    onclick="return confirm('Marquer ce paquet comme livré ?');">
                                                    <i class="bi bi-check-circle"></i> Livré
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-success small fw-bold">Livré</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($paquets)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        Aucun paquet attribué pour cette date.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="text-center mt-3">
                    <a href="/admin" class="btn btn-outline-secondary">Retour à l'accueil administratif</a>
                </div>
            </section>

            <footer class="app-footer">
                <small>PacketDelivery &copy; <?= htmlspecialchars((string)($year ?? date('Y'))) ?></small>
            </footer>

        </div>
    </div>
</main>
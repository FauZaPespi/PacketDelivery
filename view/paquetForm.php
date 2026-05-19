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
                    <?= $mode === 'add' ? 'Ajouter un paquet' : 'Modifier un paquet' ?>
                </h2>

                <?php if (!empty($errors ?? [])): ?>
                    <div class="alert alert-danger" role="alert">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if ($is_view_only ?? false): ?>
                    <div class="alert alert-warning" role="alert">
                        Ce paquet a déjà été livré et ne peut pas être modifié.
                    </div>
                <?php endif; ?>

                <form method="post" action="<?= $mode === 'add' ? '/admin/paquet/add' : '/admin/paquet/edit/' . ($paquet['numeroPostal'] ?? '') ?>" class="app-form">
                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf ?? '') ?>">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="numero_postal" class="form-label">N° postal du paquet *</label>
                            <input type="text" id="numero_postal" name="numero_postal" class="form-control"
                                value="<?= htmlspecialchars($paquet['numeroPostal'] ?? $next_numero ?? '') ?>"
                                <?= ($is_view_only ?? false) ? 'readonly' : '' ?> required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="nom_destinataire" class="form-label">Nom du destinataire *</label>
                            <input type="text" id="nom_destinataire" name="nom_destinataire" class="form-control"
                                value="<?= htmlspecialchars($paquet['nomDestinataire'] ?? '') ?>"
                                <?= ($is_view_only ?? false) ? 'readonly' : '' ?> required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="prenom_destinataire" class="form-label">Prénom du destinataire *</label>
                            <input type="text" id="prenom_destinataire" name="prenom_destinataire" class="form-control"
                                value="<?= htmlspecialchars($paquet['prenomDestinataire'] ?? '') ?>"
                                <?= ($is_view_only ?? false) ? 'readonly' : '' ?> required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="adresse_destinataire" class="form-label">Adresse du destinataire *</label>
                            <div class="input-group">
                                <input type="text" id="adresse_destinataire" name="adresse_destinataire" class="form-control"
                                    value="<?= htmlspecialchars($paquet['adresseDestinataire'] ?? '') ?>"
                                    placeholder="Rue, numéro, code postal, ville"
                                    <?= ($is_view_only ?? false) ? 'readonly' : '' ?> required>
                                <?php if (!($is_view_only ?? false)): ?>
                                    <button type="button" class="btn btn-outline-secondary" onclick="geocodeAddress()" title="Convertir en coordonnées">
                                        <i class="bi bi-geo-alt"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="latitude" class="form-label">Latitude *</label>
                            <input type="number" step="0.00000001" id="latitude" name="latitude" class="form-control"
                                value="<?= htmlspecialchars($paquet['latitudeAdresse'] ?? '') ?>"
                                readonly required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="longitude" class="form-label">Longitude *</label>
                            <input type="number" step="0.00000001" id="longitude" name="longitude" class="form-control"
                                value="<?= htmlspecialchars($paquet['longitudeAdresse'] ?? '') ?>"
                                readonly required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="date_livraison" class="form-label">Date de livraison *</label>
                            <select id="date_livraison" name="date_livraison" class="form-select"
                                <?= ($is_view_only ?? false) ? 'disabled' : '' ?> required>
                                <option value="">Sélectionner une date</option>
                                <?php foreach ($dates_valides as $date): ?>
                                    <option value="<?= $date ?>"
                                        <?= ($paquet['dateLivraison'] ?? '') === $date ? 'selected' : '' ?>>
                                        <?= (new DateTime($date))->format('d/m/Y (l)') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Date comprise entre demain et +3 jours (lundi-vendredi)</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="id_employe_livreur" class="form-label">Livreur attribué</label>
                            <select id="id_employe_livreur" name="id_employe_livreur" class="form-select"
                                <?= ($is_view_only ?? false) ? 'disabled' : '' ?>>
                                <option value="">Sélectionner un livreur</option>
                                <?php foreach ($livreurs as $livreur): ?>
                                    <option value="<?= $livreur['id_employe'] ?>"
                                        <?= ($paquet['id_employe_livreur'] ?? '') == $livreur['id_employe'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($livreur['prenom'] . ' ' . $livreur['nom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Laissez vide si aucun livreur attribué</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="/admin" class="btn btn-outline-secondary">Annuler</a>

                        <?php if ($mode === 'add'): ?>
                            <button type="submit" class="btn btn-primary">Ajouter le paquet</button>
                        <?php else: ?>
                            <div class="d-flex gap-2">
                                <?php if ($can_delete ?? false): ?>
                                    <form method="post" action="/admin/paquet/delete/<?= $paquet['numeroPostal'] ?>" class="d-inline">
                                        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf ?? '') ?>">
                                        <button type="submit" class="btn btn-danger"
                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce paquet ?')">
                                            Supprimer
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <?php if ($can_modify ?? false): ?>
                                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </form>
            </section>

            <footer class="app-footer">
                <small>PacketDelivery &copy; <?= htmlspecialchars((string)($year ?? date('Y'))) ?></small>
            </footer>

        </div>
    </div>
</main>

<script>
async function geocodeAddress() {
    const address = document.getElementById('adresse_destinataire').value;
    if (!address) {
        alert('Veuillez entrer une adresse.');
        return;
    }

    // Use Nominatim API for geocoding (free, no API key needed)
    const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address + ', Genève, Suisse')}`;

    try {
        const response = await fetch(url, {
            headers: {
                'User-Agent': 'PacketDelivery/1.0'
            }
        });
        const data = await response.json();

        if (data && data.length > 0) {
            document.getElementById('latitude').value = data[0].lat;
            document.getElementById('longitude').value = data[0].lon;
        } else {
            alert('Adresse non trouvée. Veuillez vérifier l\'adresse ou entrer les coordonnées manuellement.');
        }
    } catch (error) {
        console.error('Geocoding error:', error);
        alert('Erreur lors de la géolocalisation. Veuillez entrer les coordonnées manuellement.');
    }
}

// Auto-fill coordinates if address is already filled (for edit mode)
document.addEventListener('DOMContentLoaded', function() {
    const addressField = document.getElementById('adresse_destinataire');
    if (addressField.value && !document.getElementById('latitude').value) {
        // For edit mode, coordinates should already be filled
    }
});
</script>
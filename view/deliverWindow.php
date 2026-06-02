<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

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

            <section class="app-body">

                <div class="app-date-nav d-flex justify-content-center align-items-center gap-3 py-3"
                     role="group" aria-label="Navigation par date">
                    <button id="btnPrevDate" type="button" class="btn btn-outline-primary btn-sm"
                            aria-label="Date précédente">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <span class="fw-medium"><?= htmlspecialchars($dateAffichee ?? '') ?></span>
                    <button id="btnNextDate" type="button" class="btn btn-outline-primary btn-sm"
                            aria-label="Date suivante">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>

                <div id="map" style="height:420px; border-radius:12px; margin:0 1rem; border:2px solid #ece5f7;"
                     role="img" aria-label="Carte interactive des livraisons"></div>

                <?php if (empty($paquets)): ?>
                    <p class="app-map-message">Vous n'avez aucun paquet à livrer ce jour.</p>
                <?php elseif ($allDelivered ?? false): ?>
                    <p class="app-map-message">Vous avez terminé la totalité de vos livraisons de ce jour.</p>
                <?php elseif ($deliveriesStarted ?? false): ?>
                    <p class="app-map-message">Les livraisons sont en cours.</p>
                <?php endif; ?>

            </section>

            <footer class="app-footer">
                <small>PacketDelivery &copy; <?= htmlspecialchars((string)($year ?? date('Y'))) ?></small>
            </footer>

        </div>
    </div>
</main>

<!-- ===== Modal : Informations du colis ===== -->
<div class="modal fade" id="modalInfo" tabindex="-1" aria-labelledby="modalInfoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalInfoLabel">
                    <i class="bi bi-info-circle me-2"></i>Informations du colis
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5">Numéro postal</dt>
                    <dd class="col-sm-7 font-monospace" id="infoNumero"></dd>
                    <dt class="col-sm-5">Destinataire</dt>
                    <dd class="col-sm-7" id="infoDestinataire"></dd>
                    <dt class="col-sm-5">Adresse</dt>
                    <dd class="col-sm-7" id="infoAdresse"></dd>
                    <dt class="col-sm-5">Statut</dt>
                    <dd class="col-sm-7"><span id="infoStatut" class="badge"></span></dd>
                    <dt class="col-sm-5">Date prévue</dt>
                    <dd class="col-sm-7" id="infoDatePrevue"></dd>
                </dl>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-arrow-left me-1"></i>Retour
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ===== Modal : Ordre de livraison ===== -->
<div class="modal fade" id="modalOrdre" tabindex="-1" aria-labelledby="modalOrdreLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalOrdreLabel">
                    <i class="bi bi-list-ol me-2"></i>Modifier l'ordre de livraison
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form id="formOrdre" method="post">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf ?? '') ?>">
                <input type="hidden" name="date" id="ordreDate" value="<?= htmlspecialchars($date ?? '') ?>">
                <div class="modal-body">
                    <p class="mb-1">Colis : <strong id="ordreNumero" class="font-monospace"></strong></p>
                    <p class="mb-3 text-muted small">Ordre actuel : <span id="ordreActuel"></span></p>
                    <div class="mb-0">
                        <label for="ordreInput" class="form-label fw-medium">Numéro d'ordre de passage</label>
                        <input type="number" class="form-control" id="ordreInput" name="ordre"
                               min="1" required>
                        <div class="form-text" id="ordreHelpText"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-arrow-left me-1"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Confirmer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===== Modal : Valider livraison ===== -->
<div class="modal fade" id="modalLivraison" tabindex="-1" aria-labelledby="modalLivraisonLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLivraisonLabel">
                    <i class="bi bi-check-circle me-2 text-success"></i>Valider la livraison
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form id="formLivraison" method="post">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf ?? '') ?>">
                <div class="modal-body">
                    <p>Confirmez-vous la livraison du colis&nbsp;:</p>
                    <p class="mb-1">
                        <strong id="livraisonNumero" class="font-monospace"></strong>
                        &mdash; <span id="livraisonDestinataire"></span>
                    </p>
                    <p class="text-muted small mb-0">Cette action est irréversible.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-arrow-left me-1"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i>Confirmer la livraison
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    // ── Données PHP → JS ──────────────────────────────────────────
    var paquets           = <?= json_encode(array_values($paquets ?? [])) ?>;
    var isToday           = <?= json_encode($isToday ?? false) ?>;
    var routeComplete     = <?= json_encode($routeComplete ?? false) ?>;
    var deliveriesStarted = <?= json_encode($deliveriesStarted ?? false) ?>;
    var currentDate       = <?= json_encode($date ?? date('Y-m-d')) ?>;

    // ── Navigation de date (skip week-end) ────────────────────────
    function nextWeekday(dateStr, dir) {
        var parts = dateStr.split('-');
        var d = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
        do { d.setDate(d.getDate() + dir); } while (d.getDay() === 0 || d.getDay() === 6);
        return d.getFullYear() + '-'
            + String(d.getMonth() + 1).padStart(2, '0') + '-'
            + String(d.getDate()).padStart(2, '0');
    }

    document.getElementById('btnPrevDate').addEventListener('click', function () {
        window.location.href = '/delivery?date=' + nextWeekday(currentDate, -1);
    });
    document.getElementById('btnNextDate').addEventListener('click', function () {
        window.location.href = '/delivery?date=' + nextWeekday(currentDate, 1);
    });

    // ── Carte Leaflet ─────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        var map = L.map('map').setView([46.2044, 6.1432], 13);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: 'PacketDelivery &copy; <a href="https://github.com/FauZaPespi">Calvo Oscar</a>',
            maxZoom: 19
        }).addTo(map);

        if (paquets.length === 0) return;

        // Centrer sur le centroïde des colis
        var sumLat = 0, sumLng = 0, n = 0;
        paquets.forEach(function (p) {
            if (p.latitudeAdresse && p.longitudeAdresse) {
                sumLat += parseFloat(p.latitudeAdresse);
                sumLng += parseFloat(p.longitudeAdresse);
                n++;
            }
        });
        if (n > 0) map.setView([sumLat / n, sumLng / n], 14);

        // Ordre minimum pour marqueur bleu
        var minOrdre = paquets.reduce(function (min, p) {
            return p.ordreRouteLivraison !== null
                ? Math.min(min, parseInt(p.ordreRouteLivraison))
                : min;
        }, Infinity);

        function markerColor(p) {
            if (p.statutLivraison === 'Livré') return '#22c55e';
            if (isFinite(minOrdre) && p.ordreRouteLivraison !== null
                    && parseInt(p.ordreRouteLivraison) === minOrdre) return '#3b82f6';
            return '#1f2937';
        }

        function makeIcon(hexColor) {
            return L.divIcon({
                className: '',
                html: '<svg xmlns="http://www.w3.org/2000/svg" width="26" height="34" viewBox="0 0 26 34">'
                    + '<path d="M13 0C5.8 0 0 5.8 0 13c0 10.2 13 21 13 21S26 23.2 26 13C26 5.8 20.2 0 13 0z"'
                    + ' fill="' + hexColor + '"/>'
                    + '<circle cx="13" cy="13" r="5" fill="white"/>'
                    + '</svg>',
                iconSize: [26, 34],
                iconAnchor: [13, 34],
                popupAnchor: [0, -38]
            });
        }

        // Polyligne de la route (colis ordonnés)
        var ordered = paquets
            .filter(function (p) { return p.ordreRouteLivraison !== null; })
            .sort(function (a, b) {
                return parseInt(a.ordreRouteLivraison) - parseInt(b.ordreRouteLivraison);
            });

        if (ordered.length > 1) {
            var coords = ordered.map(function (p) {
                return [parseFloat(p.latitudeAdresse), parseFloat(p.longitudeAdresse)];
            });
            L.polyline(coords, { color: '#3b82f6', weight: 3, opacity: 0.7, dashArray: '8 5' }).addTo(map);
        }

        // Marqueurs
        paquets.forEach(function (p) {
            if (!p.latitudeAdresse || !p.longitudeAdresse) return;
            var marker = L.marker(
                [parseFloat(p.latitudeAdresse), parseFloat(p.longitudeAdresse)],
                { icon: makeIcon(markerColor(p)) }
            ).addTo(map);

            marker.on('click', function () {
                L.popup({ maxWidth: 250 })
                    .setLatLng(marker.getLatLng())
                    .setContent(buildPopupContent(p))
                    .openOn(map);
            });
        });

        // ── Builder du popup ─────────────────────────────────────
        function buildPopupContent(p) {
            var d = document.createElement('div');
            d.style.minWidth = '170px';

            var h = document.createElement('div');
            var strong = document.createElement('strong');
            strong.textContent = p.numeroPostal;
            h.appendChild(strong);
            d.appendChild(h);

            var who = document.createElement('div');
            who.textContent = p.prenomDestinataire + ' ' + p.nomDestinataire;
            d.appendChild(who);

            var adr = document.createElement('div');
            adr.style.cssText = 'color:#666;font-size:.85em;margin-bottom:4px;';
            adr.textContent = p.adresseDestinataire;
            d.appendChild(adr);

            var hr = document.createElement('hr');
            hr.style.margin = '5px 0';
            d.appendChild(hr);

            var btns = document.createElement('div');
            btns.style.display = 'grid';
            btns.style.gap = '4px';

            // Bouton 1 – Informations (toujours visible)
            btns.appendChild(mkBtn('btn-outline-secondary', 'bi-info-circle', 'Informations', function () {
                map.closePopup();
                openInfoModal(p);
            }));

            // Bouton 2 – Modifier l'ordre (aujourd'hui, avant démarrage des livraisons, non livré)
            if (isToday && !deliveriesStarted && p.statutLivraison !== 'Livré') {
                btns.appendChild(mkBtn('btn-outline-primary', 'bi-list-ol', "Modifier l'ordre", function () {
                    map.closePopup();
                    openOrdreModal(p);
                }));
            }

            // Bouton 3 – Indiquer la livraison (route complète, non livré, aujourd'hui)
            if (isToday && routeComplete && p.statutLivraison !== 'Livré') {
                btns.appendChild(mkBtn('btn-success', 'bi-check-circle', 'Indiquer la livraison', function () {
                    map.closePopup();
                    openLivraisonModal(p);
                }));
            }

            d.appendChild(btns);
            return d;
        }

        function mkBtn(cls, icon, label, handler) {
            var b = document.createElement('button');
            b.className = 'btn btn-sm ' + cls;
            var ic = document.createElement('i');
            ic.className = 'bi ' + icon;
            b.appendChild(ic);
            b.appendChild(document.createTextNode(' ' + label));
            b.addEventListener('click', handler);
            return b;
        }
    }); // end DOMContentLoaded

    // ── Fonctions d'ouverture des modaux ─────────────────────────
    var statusBadge = {
        'Livré':               'success',
        'En cours de livraison': 'info',
        'Pas encore livré':    'warning'
    };

    function openInfoModal(p) {
        document.getElementById('infoNumero').textContent     = p.numeroPostal;
        document.getElementById('infoDestinataire').textContent = p.prenomDestinataire + ' ' + p.nomDestinataire;
        document.getElementById('infoAdresse').textContent    = p.adresseDestinataire;
        document.getElementById('infoDatePrevue').textContent = p.dateLivraison;
        var badge = document.getElementById('infoStatut');
        badge.textContent = p.statutLivraison;
        badge.className = 'badge bg-' + (statusBadge[p.statutLivraison] || 'secondary');
        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalInfo')).show();
    }

    function openOrdreModal(p) {
        document.getElementById('ordreNumero').textContent  = p.numeroPostal;
        document.getElementById('ordreActuel').textContent  =
            p.ordreRouteLivraison !== null ? p.ordreRouteLivraison : 'Non défini';
        var input = document.getElementById('ordreInput');
        input.value = p.ordreRouteLivraison !== null ? p.ordreRouteLivraison : '';
        input.max   = paquets.length;
        document.getElementById('ordreHelpText').textContent =
            'Entrez un numéro entre 1 et ' + paquets.length + '.';
        document.getElementById('formOrdre').action =
            '/delivery/paquet/ordre/' + encodeURIComponent(p.numeroPostal);
        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalOrdre')).show();
    }

    function openLivraisonModal(p) {
        document.getElementById('livraisonNumero').textContent      = p.numeroPostal;
        document.getElementById('livraisonDestinataire').textContent =
            p.prenomDestinataire + ' ' + p.nomDestinataire;
        document.getElementById('formLivraison').action =
            '/delivery/paquet/livre/' + encodeURIComponent(p.numeroPostal);
        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalLivraison')).show();
    }

})();
</script>

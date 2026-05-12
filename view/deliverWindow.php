<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
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
                    <button type="button" class="btn btn-outline-primary btn-sm" aria-label="Date précédente">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <span class="fw-medium"><?= htmlspecialchars($dateAffichee ?? '') ?></span>
                    <button type="button" class="btn btn-outline-primary btn-sm" aria-label="Date suivante">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>

                <div id="map" class="app-map-placeholder" role="img"
                    aria-label="Emplacement de la carte interactive">

                </div>
                <script>
                    var map = L.map('map').setView([51.505, -0.09], 13);

                    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: 'Packet Delivery &copy; made by <a href="https://github.com/FauZaPespi">Calvo Oscar</a>'
                    }).addTo(map);

                    L.marker([51.5, -0.09]).addTo(map)
                        .bindPopup('A pretty CSS popup.<br> Easily customizable.')
                        .openPopup();
                </script>
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
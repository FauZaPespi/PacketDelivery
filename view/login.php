<main class="welcome-page d-flex flex-column min-vh-100">
    <div class="welcome-container container py-5 d-flex flex-grow-1 align-items-center justify-content-center">
        <div class="welcome-card card shadow-sm">
            <div class="card-body p-4 p-md-5">

                <?php if (!empty($message)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>

                <header class="welcome-header text-center mb-4">
                    <img src="/img/logo.png" alt="Logo Packet Delivery" class="welcome-logo mb-3">
                    <h1 class="welcome-title h2 mb-0">Packet Delivery</h1>
                </header>

                <form action="/login" method="post" class="welcome-form" novalidate>
                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf ?? '') ?>">
                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse email</label>
                        <input type="email" id="email" name="email" class="form-control"
                            placeholder="exemple@packetdelivery.ch" autocomplete="email" required>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" id="password" name="password" class="form-control"
                            autocomplete="current-password" required>
                    </div>

                    <div class="d-grid gap-2 mb-3">
                        <button type="submit" class="btn btn-primary">Se connecter</button>
                        <button type="button" class="btn btn-outline-secondary">S'inscrire</button>
                    </div>

                    <div class="text-center">
                        <a href="#" class="welcome-link small">Mot de passe oublié ?</a>
                    </div>
                </form>

                <hr class="my-4">

                <section class="welcome-description">
                    <p class="mb-0 small text-muted">
                        Packet Delivery est l'application web de gestion des livraisons de colis
                        à Genève. Elle permet aux employés administratifs de créer, modifier et
                        attribuer des paquets aux livreurs, et aux livreurs d'élaborer leur route
                        de livraison sur une carte interactive et de valider la livraison de
                        chaque paquet.
                    </p>
                </section>

            </div>
        </div>
    </div>

    <footer class="welcome-footer text-center py-3">
        <small>PacketDelivery &copy; <?= htmlspecialchars((string)($year ?? date('Y'))) ?></small>
    </footer>
</main>
<footer style="background-color: #050505; border-top: 1px solid #222; padding-top: 4rem; margin-top: auto;">
    <div class="container pb-4">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <h5 class="text-white text-uppercase letter-spacing-2 mb-3" style="font-family: 'Cormorant Garamond', serif;">Domaine Prestige</h5>
                <p style="color: #b0b0b0; line-height: 1.6;">
                    Vignoble familial depuis 1892. Quatre générations de vignerons passionnés 
                    cultivent l'excellence dans le respect de la tradition et du terroir bordelais. 
                </p>
                <div class="mt-3">
                    <a href="#" class="text-white me-3 hover-gold"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-white me-3 hover-gold"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-white me-3 hover-gold"><i class="fab fa-twitter"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <h5 class="text-white text-uppercase letter-spacing-2 mb-3" style="font-family: 'Cormorant Garamond', serif;">Horaires Caveau</h5>
                <ul class="list-unstyled">
                    <li class="d-flex justify-content-between mb-2" style="border-bottom: 1px solid #222; padding-bottom: 5px;">
                        <span style="color: #ccc;">Lundi - Jeudi</span>
                        <span style="color: var(--gold);">10h - 18h</span>
                    </li>
                    <li class="d-flex justify-content-between mb-2" style="border-bottom: 1px solid #222; padding-bottom: 5px;">
                        <span style="color: #ccc;">Vendredi</span>
                        <span style="color: var(--gold);">10h - 20h</span>
                    </li>
                    <li class="d-flex justify-content-between mb-2" style="border-bottom: 1px solid #222; padding-bottom: 5px;">
                        <span style="color: #ccc;">Samedi</span>
                        <span style="color: var(--gold);">09h - 19h</span>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span style="color: #666;">Dimanche</span>
                        <span style="color: #666;">Fermé</span>
                    </li>
                </ul>
            </div>

            <div class="col-lg-2 col-md-6">
                <h5 class="text-white text-uppercase letter-spacing-2 mb-3" style="font-family: 'Cormorant Garamond', serif;">Navigation</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="index.php" style="color: #b0b0b0; text-decoration: none;">Accueil</a></li>
                    <li class="mb-2"><a href="articles.php" style="color: #b0b0b0; text-decoration: none;">Nos Vins</a></li>
                    <li class="mb-2"><a href="qui-sommes-nous.php" style="color: #b0b0b0; text-decoration: none;">Le Domaine</a></li>
                    <?php if (isset($_SESSION['user'])): ?>
                        <li class="mb-2"><a href="logout.php" style="color: #b0b0b0; text-decoration: none;">Déconnexion</a></li>
                    <?php else: ?>
                        <li class="mb-2"><a href="login.php" style="color: #b0b0b0; text-decoration: none;">Connexion</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6">
                <h5 class="text-white text-uppercase letter-spacing-2 mb-3" style="font-family: 'Cormorant Garamond', serif;">Contact</h5>
                <ul class="list-unstyled" style="color: #b0b0b0;">
                    <li class="mb-3 d-flex">
                        <i class="fas fa-map-marker-alt mt-1 me-3" style="color: var(--gold);"></i>
                        <span>Route des Grands Crus<br>33000 Bordeaux</span>
                    </li>
                    <li class="mb-3 d-flex">
                        <i class="fas fa-phone mt-1 me-3" style="color: var(--gold);"></i>
                        <a href="tel:+33556000000" style="color: #b0b0b0; text-decoration: none;">05 56 00 00 00</a>
                    </li>
                    <li class="mb-3 d-flex">
                        <i class="fas fa-envelope mt-1 me-3" style="color: var(--gold);"></i>
                        <a href="mailto:contact@domaineprestige.fr" style="color: #b0b0b0; text-decoration: none;">contact@domaine.fr</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="py-3 text-center" style="background-color: #000; border-top: 1px solid #222;">
        <div class="container">
            <p class="m-0 small" style="color: #666;">
                &copy; <?= date('Y') ?> Domaine Prestige. 
                <span class="ms-2" style="color: #b8964e;"><i class="fas fa-wine-glass-alt me-1"></i> L'abus d'alcool est dangereux pour la santé.</span>
            </p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
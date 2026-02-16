<?php
/**
 * Page Qui Sommes-Nous - Domaine Prestige
 * Présente l'histoire, les valeurs et l'équipe du domaine.
 * Cette page statique est une exigence du cahier des charges.
 */
require_once 'config/db.php';
require_once 'includes/functions.php';

$page_title = 'Qui Sommes-Nous';

// Inclusion du header global (navigation)
require_once 'includes/header.php';
?>

<section class="hero-header" style="
    background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.8)), url('https://images.unsplash.com/photo-1506377247377-2a5b3b417ebb?q=80&w=2070'); 
    background-size: cover; 
    background-position: center; 
    min-height: 60vh;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;">
    
    <div class="container hero-content">
        <p class="text-uppercase mb-3" style="color: #c9a961; font-weight: 600; letter-spacing: 3px;">Héritage & Tradition</p>
        <h1 class="display-2 text-white font-serif mb-4">NOTRE HISTOIRE</h1>
        <p class="lead text-white mx-auto" style="max-width: 700px; font-weight: 300;">
            Une passion familiale pour l'excellence, transmise de génération en génération depuis 1892.
        </p>
    </div>
</section>

<section class="section-padding" style="background-color: #000; padding: 100px 0;">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="position-relative p-3 border border-secondary">
                    <img src="https://images.unsplash.com/photo-1560493676-04071c5f467b?w=800&q=80" 
                         class="img-fluid w-100" 
                         alt="Vignoble Domaine Prestige"
                         style="filter: brightness(0.9);">
                    <div class="position-absolute bottom-0 start-0 bg-black p-4 m-4 border-start border-warning border-4 shadow">
                        <h4 class="text-white m-0 font-serif">1892</h4>
                        <span style="color: #c9a961; font-size: 0.9rem; text-transform: uppercase;">Fondation du Domaine</span>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <h2 class="mb-4 font-serif text-white">Racines & Terroir</h2>
                <p class="mb-4" style="color: #e0e0e0; font-size: 1.1rem; line-height: 1.8;">
                    Fondé en <strong style="color: #c9a961;">1892</strong>, le Domaine Prestige est bien plus qu'une entreprise : c'est une affaire de famille. 
                    Depuis quatre générations, nous cultivons la passion du vin et l'excellence du terroir bordelais avec une rigueur inébranlable.
                </p>
                <p class="mb-4" style="color: #e0e0e0; font-size: 1.1rem; line-height: 1.8;">
                    Notre vignoble de <strong style="color: #c9a961;">35 hectares</strong> s'étend sur les coteaux les plus prestigieux, 
                    bénéficiant d'un ensoleillement optimal et d'un sol argilo-calcaire unique.
                </p>
                <p style="color: #e0e0e0; font-size: 1.1rem; line-height: 1.8;">
                    <i class="fas fa-quote-left me-2" style="color: #c9a961;"></i>
                    <em>Chaque millésime est le fruit d'un travail minutieux, où la main de l'homme accompagne la nature sans jamais la brusquer.</em>
                </p>
            </div>
        </div>
    </div>
</section>

<section class="section-padding" style="background: linear-gradient(180deg, #121212 0%, #1a1a1a 100%); padding: 100px 0;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="font-serif text-white">Les Visages du Domaine</h2>
            <p class="text-uppercase" style="color: #c9a961; letter-spacing: 2px;">Des passionnés au service de l'excellence</p>
            <hr class="mx-auto mt-4" style="width: 60px; border-color: #c9a961; opacity: 1;">
        </div>
        
        <div class="row g-4 justify-content-center">
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 text-center p-4" style="background: rgba(255,255,255,0.02); border: 1px solid #333;">
                    <div class="mx-auto mb-4 p-1 rounded-circle" style="width: 150px; height: 150px; border: 2px solid #c9a961;">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=300&h=300&fit=crop" 
                             alt="Louis Prestige" 
                             class="rounded-circle w-100 h-100 object-fit-cover">
                    </div>
                    <h3 class="h4 text-white font-serif mb-1">Louis Prestige</h3>
                    <p class="text-uppercase small mb-3" style="color: #c9a961;">Maître de Chai</p>
                    <p style="color: #ccc; font-size: 0.95rem;">
                        Descendant des fondateurs, Louis veille personnellement sur chaque barrique. Son palais absolu garantit notre signature unique.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 text-center p-4" style="background: rgba(255,255,255,0.02); border: 1px solid #333;">
                    <div class="mx-auto mb-4 p-1 rounded-circle" style="width: 150px; height: 150px; border: 2px solid #c9a961;">
                        <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=300&h=300&fit=crop" 
                             alt="Sophie Prestige" 
                             class="rounded-circle w-100 h-100 object-fit-cover">
                    </div>
                    <h3 class="h4 text-white font-serif mb-1">Sophie Prestige</h3>
                    <p class="text-uppercase small mb-3" style="color: #c9a961;">Œnologue</p>
                    <p style="color: #ccc; font-size: 0.95rem;">
                        Scientifique et artiste, Sophie assemble les cépages avec précision pour créer des vins complexes et taillés pour la garde.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 text-center p-4" style="background: rgba(255,255,255,0.02); border: 1px solid #333;">
                    <div class="mx-auto mb-4 p-1 rounded-circle" style="width: 150px; height: 150px; border: 2px solid #c9a961;">
                        <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=300&h=300&fit=crop" 
                             alt="Thomas Prestige" 
                             class="rounded-circle w-100 h-100 object-fit-cover">
                    </div>
                    <h3 class="h4 text-white font-serif mb-1">Thomas Prestige</h3>
                    <p class="text-uppercase small mb-3" style="color: #c9a961;">Chef de Culture</p>
                    <p style="color: #ccc; font-size: 0.95rem;">
                        Gardien de la terre, Thomas supervise le vignoble en agriculture raisonnée, favorisant la biodiversité locale.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-padding" style="background: url('https://images.unsplash.com/photo-1516594915697-87eb3b1c14ea?w=1920') fixed center/cover; position: relative; padding: 100px 0;">
    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.85);"></div>
    <div class="container position-relative" style="z-index: 1;">
        <div class="row text-center g-5">
            <div class="col-6 col-md-3">
                <div class="display-3 fw-bold" style="color: #c9a961;">130</div>
                <div class="text-white text-uppercase small" style="letter-spacing: 2px;">Ans d'histoire</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="display-3 fw-bold" style="color: #c9a961;">35</div>
                <div class="text-white text-uppercase small" style="letter-spacing: 2px;">Hectares</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="display-3 fw-bold" style="color: #c9a961;">15</div>
                <div class="text-white text-uppercase small" style="letter-spacing: 2px;">Cuvées</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="display-3 fw-bold" style="color: #c9a961;">4</div>
                <div class="text-white text-uppercase small" style="letter-spacing: 2px;">Générations</div>
            </div>
        </div>
    </div>
</section>

<section class="section-padding" style="background-color: #000; padding: 100px 0;">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <h2 class="mb-4 font-serif text-white">
                    <i class="fas fa-door-open me-3" style="color: #c9a961;"></i>Les Portes du Domaine
                </h2>
                <p class="mb-4" style="color: #ccc; font-size: 1.1rem; line-height: 1.8;">
                    Nous serions ravis de vous accueillir pour vous faire découvrir notre savoir-faire. 
                    Le caveau historique du XVIIIe siècle est ouvert toute l'année pour des dégustations inoubliables.
                </p>
                
                <div class="p-4 mb-4" style="background: #121212; border: 1px solid #333; border-left: 3px solid #c9a961;">
                    <h5 class="text-white mb-3 text-uppercase small" style="letter-spacing: 2px;">
                        <i class="far fa-clock me-2 text-warning"></i>Horaires d'ouverture
                    </h5>
                    <ul class="list-unstyled mb-0" style="color: #bbb;">
                        <li class="d-flex justify-content-between border-bottom border-dark py-2">
                            <span>Lundi - Jeudi</span> <span class="text-white">10h - 18h</span>
                        </li>
                        <li class="d-flex justify-content-between border-bottom border-dark py-2">
                            <span>Vendredi</span> <span class="text-white">10h - 20h <small class="text-warning">(Nocturne)</small></span>
                        </li>
                        <li class="d-flex justify-content-between border-bottom border-dark py-2">
                            <span>Samedi</span> <span class="text-white">09h - 19h</span>
                        </li>
                        <li class="d-flex justify-content-between py-2 text-white-50">
                            <span>Dimanche</span> <span>Fermé</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="col-lg-6">
                <img src="https://images.unsplash.com/photo-1510812431401-41d2bd2722f3?q=80&w=2070" 
                     class="img-fluid w-100 shadow-lg" 
                     alt="Caveau du Domaine">
            </div>
        </div>
    </div>
</section>

<?php 
// Inclusion du footer global
require_once 'includes/footer.php'; 
?>
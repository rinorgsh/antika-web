/* ============================================================
   MODE ÉVÉNEMENT
   Si config.json => event.enabled = true, toute page du menu bascule
   vers le PDF de l'événement (config.json => event.file).

   Inclus par les CINQ pages — index, carte, menu, boisson, dessert.
   C'est indispensable : un client qui arrive par un favori, par
   l'historique ou en appuyant sur « retour » ne passe pas par la page
   d'accueil. Sans cette vérification partout, il verrait la carte
   normale, avec les prix normaux, pendant une soirée à tarif spécial.

   Lecture en no-store + horodatage : dès que config.json est publié,
   l'effet est immédiat, aucun cache à purger.
   Si la lecture échoue (réseau capricieux), on retombe volontairement
   sur le menu normal plutôt que de bloquer le client sur un écran vide.
   ============================================================ */
(function () {
  var loading = document.getElementById('loading');   /* présent sur index.html seulement */

  function afficherLaPage() {
    if (loading) loading.classList.add('hidden');
  }

  fetch('config.json?t=' + Date.now(), { cache: 'no-store' })
    .then(function (r) { return r.ok ? r.json() : null; })
    .then(function (cfg) {
      if (cfg && cfg.event && cfg.event.enabled) {
        window.location.replace(cfg.event.file || 'event.pdf');
      } else {
        afficherLaPage();
      }
    })
    .catch(afficherLaPage);
})();

/* ============================================================
   Icônes (ligne, dorées) par type de boisson / produit.
   Utilisé par boisson.html, dessert.html et le rappel desserts
   de menu.html pour les articles sans photo. Clé = id de section.
   ============================================================ */
(function(){
  function svg(inner){
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">'+inner+'</svg>';
  }
  window.DRINK_ICONS = {
    // cafés & boissons chaudes
    hotdrinks: svg('<path d="M4 8.5h13v3.5a5 5 0 0 1-5 5H9a5 5 0 0 1-5-5V8.5Z"/><path d="M17 9.5h1.5a2.5 2.5 0 0 1 0 5H17"/><path d="M8 2.6c-.6 1 .6 2 0 3M11.6 2.6c-.6 1 .6 2 0 3"/>'),
    specialcoffee: svg('<path d="M4 8.5h13v3.5a5 5 0 0 1-5 5H9a5 5 0 0 1-5-5V8.5Z"/><path d="M17 9.5h1.5a2.5 2.5 0 0 1 0 5H17"/><path d="M4 20.5h13"/><path d="M9.5 2.6c-.6 1 .6 2 0 3"/>'),
    // desserts (cupcake)
    desserts: svg('<path d="M6 10h12l-1.2 8.2a2 2 0 0 1-2 1.8H9.2a2 2 0 0 1-2-1.8L6 10Z"/><path d="M6.4 10a4 4 0 0 1 3-4.9 3.2 3.2 0 0 1 5.8-.3A3.5 3.5 0 0 1 17.6 10"/><path d="M12 2.6v1.6"/>'),
    // digestif (verre ballon)
    digestif: svg('<path d="M7 5h10l-1.2 5.6a4 4 0 0 1-7.6 0L7 5Z"/><path d="M12 15v4"/><path d="M8.5 20h7"/>'),
    // apéro (spritz avec paille)
    apero: svg('<path d="M6.5 4h11l-1.4 6.3a4.2 4.2 0 0 1-8.2 0L6.5 4Z"/><path d="M12 15v5"/><path d="M8 21h8"/><path d="M16.3 3l-2.6 7.3"/>'),
    // cocktails (verre triangle + pique)
    cocktails: svg('<path d="M4 5h16l-8 8-8-8Z"/><path d="M12 13v6"/><path d="M8.5 21h7"/><path d="M17.2 5l-2.4 2.4"/>'),
    // vins (verre à vin)
    wine: svg('<path d="M7 4h10l-1 6.6a4 4 0 0 1-8 0L7 4Z"/><path d="M12 15v4"/><path d="M8.5 20h7"/>'),
    // carte des vins (même verre à vin)
    wijnkaart: svg('<path d="M7 4h10l-1 6.6a4 4 0 0 1-8 0L7 4Z"/><path d="M12 15v4"/><path d="M8.5 20h7"/>'),
    // bulles (flûte)
    bubbles: svg('<path d="M9.5 4h5l-.6 8.6a1.9 1.9 0 0 1-3.8 0L9.5 4Z"/><path d="M12 15v5"/><path d="M9.5 21h5"/><path d="M12 7.3v.01M11 9.3v.01M13 9.6v.01"/>'),
    // bières (chope)
    beer: svg('<path d="M6.5 7h8.5v12.5H6.5Z"/><path d="M15 9h2.4a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H15"/><path d="M7 7a2.5 2.5 0 0 1 2.6-2.5 3 3 0 0 1 5 .5"/>'),
    // softs (bouteille)
    soft: svg('<path d="M10 3h4v2.2l1 2.3V19a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2V7.5l1-2.3V3Z"/><path d="M9 11h6"/>'),
    // spiritueux (verre tumbler)
    spirits: svg('<path d="M6.5 6h11l-1 12.4a1.5 1.5 0 0 1-1.5 1.4H9a1.5 1.5 0 0 1-1.5-1.4L6.5 6Z"/><path d="M7.2 12.5h9.6"/>'),
    // secours
    _default: svg('<path d="M7 5h10l-1 13.5a1.5 1.5 0 0 1-1.5 1.5h-5A1.5 1.5 0 0 1 8 18.5L7 5Z"/>')
  };

  window.drinkIcon = function(id){
    return (window.DRINK_ICONS[id] || window.DRINK_ICONS._default);
  };

  /* Pastille :
       img:"fichier.jpg"  → photo produit (dossier drinks/, remplit la pastille)
       logo:"fichier.png" → logo de marque (dossier logos/, contenu sur fond blanc)
       sinon              → icône de catégorie. */
  window.brandTile = function(logo, secId, alt, img){
    var a = (alt || "").replace(/"/g, "");
    if(img){
      return '<span class="ic-tile photo"><img src="drinks/'+img+'" alt="'+a+'" loading="lazy"></span>';
    }
    if(logo){
      return '<span class="ic-tile logo"><img src="logos/'+logo+'" alt="'+a+'" loading="lazy"></span>';
    }
    return '<span class="ic-tile">'+window.drinkIcon(secId)+'</span>';
  };
})();

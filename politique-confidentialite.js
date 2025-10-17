// Date dynamique (footer + "dernière mise à jour")
(function(){
  const y = new Date().getFullYear();
  const d = new Date().toLocaleDateString('fr-FR');
  document.querySelectorAll('[data-year]').forEach(n => n.textContent = y);
  const span = document.querySelector('[data-today]');
  if (span) span.textContent = d;
})();

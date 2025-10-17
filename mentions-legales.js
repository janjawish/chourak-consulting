// Date dynamique (footer + en-tête "dernière mise à jour")
(function () {
  const y = new Date().getFullYear();
  const dateFull = new Date().toLocaleDateString('fr-FR');
  document.querySelectorAll('[data-year]').forEach(n => n.textContent = y);
  const today = document.querySelector('[data-today]');
  if (today) today.textContent = dateFull;
})();

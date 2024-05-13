const inputsQuantite = document.querySelectorAll('input[type="number"][name^="quantite"]');
const inputsPrixUnitaire = document.querySelectorAll('input[type="number"][name^="prixUnitaire"]');
const spansTotal = document.querySelectorAll('span.total');
const totalGeneralSpan = document.getElementById('totalGeneral');

function updateTotal(index) {
  const quantite = inputsQuantite[index].value || 0;
  const prixUnitaire = inputsPrixUnitaire[index].value || 0;
  const total = quantite * prixUnitaire;
  spansTotal[index].textContent = total.toFixed(2);

  let totalGeneral = 0;
  for (const span of spansTotal) {
    totalGeneral += parseFloat(span.textContent);
  }
  totalGeneralSpan.textContent = totalGeneral.toFixed(2);
}

inputsQuantite.forEach((input, index) => {
  input.addEventListener('change', () => updateTotal(index));
});

inputsPrixUnitaire.forEach((input, index) => {
  input.addEventListener('change', () => updateTotal(index));
});

document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-confirm]').forEach(el => el.addEventListener('click', e => {
    if (!confirm(el.dataset.confirm || 'Are you sure?')) e.preventDefault();
  }));
  document.querySelectorAll('[data-progress]').forEach(el => {
    const value = Math.max(0, Math.min(100, Number(el.dataset.progress || 0)));
    requestAnimationFrame(() => el.style.width = value + '%');
  });
  setTimeout(() => document.querySelectorAll('.flash').forEach(x => x.classList.add('fade')), 4500);
});

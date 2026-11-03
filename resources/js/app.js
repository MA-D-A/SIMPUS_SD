document.addEventListener('submit', (event) => {
    const pesan = event.target.dataset?.konfirmasi;

    if (pesan && !window.confirm(pesan)) {
        event.preventDefault();
    }
});

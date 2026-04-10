document.addEventListener('DOMContentLoaded', () => {
  const deleteForms = document.querySelectorAll('[data-delete-form]');
  const audioUrlInput = document.querySelector('[data-audio-url]');
  const audioFileInput = document.querySelector('[data-audio-file]');
  const audioHint = document.querySelector('[data-audio-hint]');

  deleteForms.forEach((form) => {
    form.addEventListener('submit', (event) => {
      if (!window.confirm('Delete this song from the library?')) {
        event.preventDefault();
      }
    });
  });

  function updateHint() {
    if (!audioHint) {
      return;
    }

    if (audioFileInput?.files?.length) {
      audioHint.textContent = 'A local file will be uploaded to the server.';
      return;
    }

    if (audioUrlInput?.value.trim()) {
      audioHint.textContent = 'The track will use the provided URL.';
      return;
    }

    audioHint.textContent = 'Provide either an audio URL or upload a file.';
  }

  audioUrlInput?.addEventListener('input', updateHint);
  audioFileInput?.addEventListener('change', updateHint);
  updateHint();
});

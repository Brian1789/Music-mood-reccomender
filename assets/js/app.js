document.addEventListener('DOMContentLoaded', () => {
  const moodButtons = Array.from(document.querySelectorAll('[data-mood]'));
  const resultsContainer = document.querySelector('[data-results]');
  const statusText = document.querySelector('[data-status]');
  const emptyState = document.querySelector('[data-empty]');
  const audioPlayer = document.querySelector('#music-player');
  const playerTitle = document.querySelector('[data-player-title]');
  const playerArtist = document.querySelector('[data-player-artist]');
  const playButton = document.querySelector('[data-action="play"]');
  const pauseButton = document.querySelector('[data-action="pause"]');
  const nextButton = document.querySelector('[data-action="next"]');
  const previousButton = document.querySelector('[data-action="previous"]');

  if (!resultsContainer || !statusText || !audioPlayer) {
    return;
  }

  let playlist = [];
  let currentIndex = -1;

  function setStatus(message, tone = 'muted') {
    statusText.textContent = message;
    statusText.dataset.tone = tone;
  }

  function renderEmpty(message) {
    if (emptyState) {
      emptyState.textContent = message;
      emptyState.classList.remove('hidden');
    }
    resultsContainer.innerHTML = '';
  }

  function renderSongs(songs) {
    playlist = songs;
    currentIndex = songs.length ? 0 : -1;

    if (!songs.length) {
      renderEmpty('No songs found for this mood yet. Add more tracks from the admin panel.');
      setStatus('Waiting for a mood pick');
      clearPlayer();
      return;
    }

    if (emptyState) {
      emptyState.classList.add('hidden');
    }

    resultsContainer.innerHTML = songs.map((song, index) => `
      <article class="song-card">
        <div class="song-meta">
          <span class="song-badge">${escapeHtml(song.mood)}</span>
          <h3>${escapeHtml(song.title)}</h3>
          <span>${escapeHtml(song.artist)}</span>
        </div>
        <div class="song-footer">
          <span class="muted">Track ${index + 1}</span>
          <button class="button-secondary" data-play-index="${index}">Play</button>
        </div>
      </article>
    `).join('');

    setStatus(`${songs.length} track${songs.length === 1 ? '' : 's'} ready`);
    attachSongHandlers();
    loadTrack(0, false);
  }

  function attachSongHandlers() {
    resultsContainer.querySelectorAll('[data-play-index]').forEach((button) => {
      button.addEventListener('click', () => {
        const index = Number(button.dataset.playIndex);
        loadTrack(index, true);
      });
    });
  }

  function clearPlayer() {
    audioPlayer.removeAttribute('src');
    audioPlayer.load();
    if (playerTitle) playerTitle.textContent = 'Pick a mood to begin';
    if (playerArtist) playerArtist.textContent = 'Your recommendations will appear here';
  }

  function loadTrack(index, autoplay = true) {
    if (!playlist.length || index < 0 || index >= playlist.length) {
      return;
    }

    currentIndex = index;
    const song = playlist[index];
    audioPlayer.src = song.audio_url;
    audioPlayer.load();

    if (playerTitle) playerTitle.textContent = song.title;
    if (playerArtist) playerArtist.textContent = song.artist;

    if (autoplay) {
      audioPlayer.play().catch(() => {
        setStatus('Press play to start playback');
      });
    }
  }

  function nextTrack() {
    if (!playlist.length) {
      return;
    }

    const nextIndex = (currentIndex + 1) % playlist.length;
    loadTrack(nextIndex, true);
  }

  function previousTrack() {
    if (!playlist.length) {
      return;
    }

    const previousIndex = currentIndex <= 0 ? playlist.length - 1 : currentIndex - 1;
    loadTrack(previousIndex, true);
  }

  function escapeHtml(value) {
    return String(value)
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#039;');
  }

  async function loadMood(mood) {
    moodButtons.forEach((button) => {
      button.classList.toggle('active', button.dataset.mood === mood);
    });

    setStatus(`Loading ${mood.toLowerCase()} songs...`);
    if (emptyState) {
      emptyState.classList.add('hidden');
    }

    try {
      const response = await fetch(`/recommend.php?mood=${encodeURIComponent(mood)}`, {
        headers: {
          Accept: 'application/json',
        },
      });

      const payload = await response.json();

      if (!response.ok || !payload.success) {
        throw new Error(payload.message || 'Failed to fetch songs');
      }

      renderSongs(payload.songs || []);
    } catch (error) {
      renderEmpty('Unable to load recommendations right now.');
      setStatus(error.message, 'error');
    }
  }

  moodButtons.forEach((button) => {
    button.addEventListener('click', () => loadMood(button.dataset.mood));
  });

  playButton?.addEventListener('click', () => {
    if (audioPlayer.src) {
      audioPlayer.play();
    } else if (playlist.length) {
      loadTrack(currentIndex >= 0 ? currentIndex : 0, true);
    }
  });

  pauseButton?.addEventListener('click', () => audioPlayer.pause());
  nextButton?.addEventListener('click', nextTrack);
  previousButton?.addEventListener('click', previousTrack);
  audioPlayer.addEventListener('ended', nextTrack);

  const defaultMood = moodButtons.find((button) => button.classList.contains('active'));
  if (defaultMood) {
    loadMood(defaultMood.dataset.mood);
  } else {
    setStatus('Choose a mood to discover songs');
  }
});

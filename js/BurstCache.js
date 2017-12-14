function burstCache() {
  if (!navigator.onLine) {
    document.body.innerHTML = 'Loading...';
    window.location = '/index.php';
  }
}

burstCache();
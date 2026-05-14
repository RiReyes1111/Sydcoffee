<button class="btt-btn" id="bttBtn" onclick="window.scrollTo({top:0,behavior:'smooth'})" aria-label="Back to top">
  ↑
</button>

<style>
  .btt-btn {
    position: fixed;
    bottom: 32px; right: 32px;
    z-index: 998;
    width: 46px; height: 46px;
    border-radius: 50%;
    background: var(--espresso);
    color: var(--white);
    border: none;
    font-size: 18px;
    cursor: pointer;
    box-shadow: 0 4px 18px rgba(30,18,10,0.22);
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s, visibility 0.3s, background 0.3s, transform 0.3s;
  }
  .btt-btn:hover {
    background: var(--brown-mid);
    transform: translateY(-3px);
  }
  .btt-btn.visible {
    opacity: 1;
    visibility: visible;
  }

  /* shift up above cart button if cart exists on page */
  body:has(.cart-toggle) .btt-btn {
    bottom: 90px;
  }

  /* white variant for light-background pages */
  .light-btt .btt-btn {
    background: var(--white);
    color: var(--brown-mid);
    box-shadow: 0 4px 18px rgba(30,18,10,0.15);
    border: 1.5px solid var(--cream-dark);
  }
  .light-btt .btt-btn:hover {
    background: var(--cream-dark);
    color: var(--espresso);
  }
</style>

<script>
  (function() {
    const btn = document.getElementById('bttBtn');
    window.addEventListener('scroll', function() {
      btn.classList.toggle('visible', window.scrollY > 300);
    }, { passive: true });
  })();
</script>
/**
 * ChallengeHub — Main JavaScript
 */

'use strict';

// ── Auto-dismiss flash messages ───────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  const flash = document.querySelector('[data-flash]');
  if (flash) {
    setTimeout(() => {
      flash.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
      flash.style.opacity    = '0';
      flash.style.transform  = 'translateY(-10px)';
      setTimeout(() => flash.remove(), 600);
    }, 4000);
  }

  // ── Animate on scroll ──────────────────────────────────
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.opacity    = '1';
        entry.target.style.transform  = 'translateY(0)';
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1 });

  document.querySelectorAll('.animate-in').forEach(el => {
    el.style.opacity   = '0';
    el.style.transform = 'translateY(16px)';
    el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
    observer.observe(el);
  });

  // ── Character counter for textareas ───────────────────
  document.querySelectorAll('textarea[data-min]').forEach(ta => {
    const min  = parseInt(ta.dataset.min);
    const hint = document.createElement('div');
    hint.style.cssText = 'font-size:0.72rem;margin-top:4px;color:var(--text-muted);';
    ta.parentNode.appendChild(hint);

    const update = () => {
      const len  = ta.value.length;
      const left = Math.max(0, min - len);
      hint.textContent = left > 0 ? `${left} more characters needed` : `✓ ${len} characters`;
      hint.style.color = left > 0 ? 'var(--text-muted)' : 'var(--accent-green)';
    };

    ta.addEventListener('input', update);
    update();
  });

  // ── Confirm delete buttons ────────────────────────────
  document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', (e) => {
      if (!confirm(el.dataset.confirm)) e.preventDefault();
    });
  });

  // ── Date field: set min to tomorrow ───────────────────
  document.querySelectorAll('input[type="date"]').forEach(input => {
    if (!input.min) {
      const tomorrow = new Date();
      tomorrow.setDate(tomorrow.getDate() + 1);
      input.min = tomorrow.toISOString().split('T')[0];
    }
  });

  // ── Navbar active link highlight ──────────────────────
  const currentPage = new URLSearchParams(window.location.search).get('page') || 'home';
  document.querySelectorAll('.navbar-nav a').forEach(link => {
    const href = new URLSearchParams(link.search || '').get('page');
    if (href === currentPage) link.classList.add('active');
  });
});

// ── Image preview helper (global) ─────────────────────────
function previewImage(input, previewId = 'uploadPreview') {
  if (input.files && input.files[0]) {
    const file = input.files[0];
    if (file.size > 5 * 1024 * 1024) {
      alert('File is too large. Maximum size is 5MB.');
      input.value = '';
      return;
    }
    const reader = new FileReader();
    reader.onload = e => {
      const el = document.getElementById(previewId);
      if (el) {
        el.innerHTML = `<img src="${e.target.result}"
          style="max-height:150px;border-radius:8px;display:block;margin:auto;">
          <div style="font-size:0.75rem;color:var(--text-muted);text-align:center;margin-top:6px;">${file.name}</div>`;
      }
    };
    reader.readAsDataURL(file);
  }
}

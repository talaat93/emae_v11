document.addEventListener('DOMContentLoaded', () => {
  const navToggle = document.querySelector('.nav-toggle');
  const nav = document.querySelector('.site-nav');

  if (navToggle && nav) {
    navToggle.addEventListener('click', () => {
      const isOpen = nav.classList.toggle('is-open');
      navToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
  }

  const reveals = document.querySelectorAll('.reveal');
  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
        }
      });
    }, { threshold: 0.14 });

    reveals.forEach((element) => observer.observe(element));
  } else {
    reveals.forEach((element) => element.classList.add('is-visible'));
  }

  const chatWidget = document.querySelector('.chat-widget');
  document.querySelectorAll('[data-chat-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
      if (!chatWidget) return;
      if (chatWidget.hasAttribute('hidden')) {
        chatWidget.removeAttribute('hidden');
      } else {
        chatWidget.setAttribute('hidden', 'hidden');
      }
    });
  });

  const chatResponse = document.querySelector('[data-chat-response]');
  document.querySelectorAll('.chat-pill').forEach((button) => {
    button.addEventListener('click', () => {
      if (chatResponse) {
        chatResponse.textContent = button.dataset.chatAnswer || '';
      }
    });
  });

  document.querySelectorAll('[data-faq]').forEach((item) => {
    const trigger = item.querySelector('.faq-question');
    const answer = item.querySelector('.faq-answer');

    if (!trigger || !answer) return;

    trigger.addEventListener('click', () => {
      const expanded = trigger.getAttribute('aria-expanded') === 'true';
      trigger.setAttribute('aria-expanded', expanded ? 'false' : 'true');
      if (expanded) {
        answer.setAttribute('hidden', 'hidden');
      } else {
        answer.removeAttribute('hidden');
      }
    });
  });
});

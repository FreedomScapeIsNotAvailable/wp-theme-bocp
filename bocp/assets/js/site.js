/* BOCP theme — mobile drawer + scroll-reveal.
   Vanilla JS port of the behaviour from the original React mockup
   (site.jsx: Nav open/close state + useReveal IntersectionObserver). */
(function () {
  "use strict";

  function initDrawer() {
    var burger = document.querySelector(".nav-burger");
    var closeBtn = document.querySelector(".drawer-head .nav-burger");
    var drawer = document.querySelector(".drawer");
    var scrim = document.querySelector(".drawer-scrim");
    if (!burger || !drawer || !scrim) return;

    function open() {
      drawer.classList.add("open");
      scrim.classList.add("open");
      document.body.style.overflow = "hidden";
      drawer.setAttribute("aria-hidden", "false");
    }
    function close() {
      drawer.classList.remove("open");
      scrim.classList.remove("open");
      document.body.style.overflow = "";
      drawer.setAttribute("aria-hidden", "true");
    }

    burger.addEventListener("click", open);
    if (closeBtn) closeBtn.addEventListener("click", close);
    scrim.addEventListener("click", close);
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape") close();
    });
  }

  function initDrawerSubmenus() {
    var toggles = document.querySelectorAll(".drawer-submenu-toggle");
    toggles.forEach(function (toggle) {
      toggle.addEventListener("click", function () {
        var item = toggle.closest(".drawer-item");
        if (!item) return;
        var isOpen = item.classList.toggle("open");
        toggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
      });
    });
  }

  function initReveal() {
    var items = document.querySelectorAll(".reveal");
    if (!items.length) return;

    if (window.matchMedia("(prefers-reduced-motion: reduce)").matches || !("IntersectionObserver" in window)) {
      items.forEach(function (el) { el.classList.add("in"); });
      return;
    }

    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add("in");
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12 });

    items.forEach(function (el) { io.observe(el); });
  }

  /* Embedded BOCP forms (e.g. the contact page sign-up form) load a fixed
     iframe height as a placeholder, then post their real content height via
     window.postMessage once rendered (and again on resize) — needed because
     the form reflows taller on narrow/mobile widths where fields stack.
     Without this listener the iframe stays at its placeholder height and
     clips the bottom of the form (submit button included) on mobile. */
  function initBocpFormEmbeds() {
    var frames = document.querySelectorAll('iframe[src^="https://forms.bocp.eu/"]');
    if (!frames.length) return;

    window.addEventListener("message", function (e) {
      if (e.origin !== "https://forms.bocp.eu") return;
      var data = e.data;
      if (!data || !data.bocp_form_height) return;

      frames.forEach(function (frame) {
        if (frame.contentWindow === e.source) {
          frame.style.height = data.bocp_form_height + "px";
        }
      });
    });
  }

  document.addEventListener("DOMContentLoaded", function () {
    initDrawer();
    initDrawerSubmenus();
    initReveal();
    initBocpFormEmbeds();
  });
})();

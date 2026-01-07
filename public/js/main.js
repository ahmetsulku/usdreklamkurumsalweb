/**
 * USD Reklam - Ana JavaScript
 */

(function () {
  "use strict";

  // DOM Ready
  document.addEventListener("DOMContentLoaded", function () {
    initMobileMenu();
    initSlider();
    initQuoteModal();
    initQuoteForms();
    initSmoothScroll();
    initLazyLoading();
    initDropdowns();
    initGallery();
  });

  /**
   * Mobile Menu
   */
  function initMobileMenu() {
    const toggle = document.getElementById("mobile-menu-toggle");
    const nav = document.getElementById("main-nav");

    if (!toggle || !nav) return;

    // Overlay oluştur
    let overlay = document.querySelector(".menu-overlay");
    if (!overlay) {
      overlay = document.createElement("div");
      overlay.className = "menu-overlay";
      document.body.appendChild(overlay);
    }

    toggle.addEventListener("click", function () {
      document.body.classList.toggle("menu-open");
    });

    overlay.addEventListener("click", function () {
      document.body.classList.remove("menu-open");
    });

    // Dropdown toggle for mobile
    const dropdownItems = nav.querySelectorAll(".has-dropdown > a");
    dropdownItems.forEach(function (item) {
      item.addEventListener("click", function (e) {
        if (window.innerWidth < 992) {
          e.preventDefault();
          this.parentElement.classList.toggle("dropdown-open");
        }
      });
    });
  }

  /**
   * Hero Slider
   */
  function initSlider() {
    const slider = document.querySelector(".hero-slider");
    if (!slider) return;

    const container = slider.querySelector(".slider-container");
    const slides = slider.querySelectorAll(".slide");
    const prevBtn = slider.querySelector(".slider-prev");
    const nextBtn = slider.querySelector(".slider-next");
    const dotsContainer = slider.querySelector(".slider-dots");

    if (slides.length < 2) return;

    let currentIndex = 0;
    let autoplayInterval;

    // Dots oluştur
    if (dotsContainer) {
      slides.forEach((_, i) => {
        const dot = document.createElement("button");
        dot.className = "slider-dot" + (i === 0 ? " active" : "");
        dot.setAttribute("aria-label", "Slide " + (i + 1));
        dot.addEventListener("click", () => goToSlide(i));
        dotsContainer.appendChild(dot);
      });
    }

    function goToSlide(index) {
      currentIndex = index;
      if (currentIndex >= slides.length) currentIndex = 0;
      if (currentIndex < 0) currentIndex = slides.length - 1;

      container.style.transform = "translateX(-" + currentIndex * 100 + "%)";

      // Dots güncelle
      const dots = dotsContainer?.querySelectorAll(".slider-dot");
      dots?.forEach((dot, i) => {
        dot.classList.toggle("active", i === currentIndex);
      });
    }

    function nextSlide() {
      goToSlide(currentIndex + 1);
    }

    function prevSlide() {
      goToSlide(currentIndex - 1);
    }

    // Controls
    if (prevBtn) prevBtn.addEventListener("click", prevSlide);
    if (nextBtn) nextBtn.addEventListener("click", nextSlide);

    // Autoplay
    function startAutoplay() {
      autoplayInterval = setInterval(nextSlide, 5000);
    }

    function stopAutoplay() {
      clearInterval(autoplayInterval);
    }

    startAutoplay();

    slider.addEventListener("mouseenter", stopAutoplay);
    slider.addEventListener("mouseleave", startAutoplay);

    // Touch events
    let touchStartX = 0;
    let touchEndX = 0;

    slider.addEventListener(
      "touchstart",
      function (e) {
        touchStartX = e.changedTouches[0].screenX;
        stopAutoplay();
      },
      { passive: true }
    );

    slider.addEventListener(
      "touchend",
      function (e) {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
        startAutoplay();
      },
      { passive: true }
    );

    function handleSwipe() {
      const diff = touchStartX - touchEndX;
      if (Math.abs(diff) > 50) {
        if (diff > 0) nextSlide();
        else prevSlide();
      }
    }
  }

  /**
   * Quote Modal
   */
  function initQuoteModal() {
    const modal = document.getElementById("quote-modal");
    if (!modal) return;

    // Modal aç butonları
    document.querySelectorAll("[data-quote-modal]").forEach(function (btn) {
      btn.addEventListener("click", function (e) {
        e.preventDefault();

        const itemType = this.dataset.itemType || "";
        const itemId = this.dataset.itemId || "";
        const itemName = this.dataset.itemName || "";

        openQuoteModal(itemType, itemId, itemName);
      });
    });

    // Modal kapat
    modal.querySelectorAll("[data-modal-close]").forEach(function (el) {
      el.addEventListener("click", closeQuoteModal);
    });

    // ESC ile kapat
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && modal.classList.contains("active")) {
        closeQuoteModal();
      }
    });

    // Tab switching
    modal.querySelectorAll(".quote-tab").forEach(function (tab) {
      tab.addEventListener("click", function () {
        const tabName = this.dataset.tab;
        switchQuoteTab(tabName);
      });
    });

    // Multiple dimensions checkbox
    ["email", "whatsapp"].forEach(function (type) {
      const checkbox = document.getElementById(type + "-multiple");
      const notesGroup = document.getElementById(type + "-notes-group");

      if (checkbox && notesGroup) {
        checkbox.addEventListener("change", function () {
          notesGroup.style.display = this.checked ? "block" : "none";
        });
      }
    });
  }

  function openQuoteModal(itemType, itemId, itemName) {
    const modal = document.getElementById("quote-modal");

    // Form alanlarını doldur
    document.getElementById("quote-item-name").textContent =
      itemName || "Genel Teklif";

    ["call", "email", "whatsapp"].forEach(function (type) {
      const typeInput = document.getElementById(type + "-item-type");
      const idInput = document.getElementById(type + "-item-id");
      const nameInput = document.getElementById(type + "-item-name");

      if (typeInput) typeInput.value = itemType;
      if (idInput) idInput.value = itemId;
      if (nameInput) nameInput.value = itemName;
    });

    // Formu sıfırla
    modal.querySelectorAll("form").forEach(function (form) {
      form.reset();
    });

    // Success mesajını gizle
    document.getElementById("quote-success").style.display = "none";
    document.querySelector(".quote-tab-content").style.display = "block";
    document.querySelector(".quote-tabs").style.display = "flex";

    // İlk tab'a dön
    switchQuoteTab("call");

    // Modal aç
    modal.classList.add("active");
    document.body.style.overflow = "hidden";
  }

  function closeQuoteModal() {
    const modal = document.getElementById("quote-modal");
    modal.classList.remove("active");
    document.body.style.overflow = "";
  }

  function switchQuoteTab(tabName) {
    // Tab butonları
    document.querySelectorAll(".quote-tab").forEach(function (tab) {
      tab.classList.toggle("active", tab.dataset.tab === tabName);
    });

    // Form içerikleri
    document.querySelectorAll(".quote-form").forEach(function (form) {
      form.style.display = form.dataset.type === tabName ? "block" : "none";
    });
  }

  /**
   * Quote Forms (AJAX)
   */
  function initQuoteForms() {
    document.querySelectorAll(".quote-form").forEach(function (form) {
      form.addEventListener("submit", function (e) {
        e.preventDefault();
        submitQuoteForm(this);
      });
    });
  }

  function submitQuoteForm(form) {
    const type = form.dataset.type;
    const submitBtn = form.querySelector('button[type="submit"]');
    const btnText = submitBtn.querySelector(".btn-text");
    const btnLoading = submitBtn.querySelector(".btn-loading");

    // Loading state
    submitBtn.disabled = true;
    btnText.style.display = "none";
    btnLoading.style.display = "inline";

    const formData = new FormData(form);

    fetch("/api/quote/" + type, {
      method: "POST",
      body: formData,
      headers: {
        "X-Requested-With": "XMLHttpRequest",
      },
    })
      .then(function (response) {
        return response.json();
      })
      .then(function (data) {
        if (data.success) {
          // WhatsApp ise yönlendir
          if (type === "whatsapp" && data.whatsapp_url) {
            window.open(data.whatsapp_url, "_blank");
          }

          // Başarı mesajı göster
          showQuoteSuccess(data.message, data.reference_no);

          // Önceki talepler varsa göster
          if (data.existing_requests && data.existing_requests.length > 0) {
            document.getElementById("quote-existing").style.display = "block";
          }
        } else {
          alert(data.message || "Bir hata oluştu.");
        }
      })
      .catch(function (error) {
        console.error("Error:", error);
        alert("Bir hata oluştu. Lütfen tekrar deneyin.");
      })
      .finally(function () {
        submitBtn.disabled = false;
        btnText.style.display = "inline";
        btnLoading.style.display = "none";
      });
  }

  function showQuoteSuccess(message, refNo) {
    document.querySelector(".quote-tabs").style.display = "none";
    document.querySelector(".quote-tab-content").style.display = "none";

    const successDiv = document.getElementById("quote-success");
    document.getElementById("quote-success-message").textContent = message;
    document.getElementById("quote-ref-no").textContent = refNo;
    successDiv.style.display = "block";
  }

  /**
   * Smooth Scroll
   */
  function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
      anchor.addEventListener("click", function (e) {
        const targetId = this.getAttribute("href");
        if (targetId === "#") return;

        const target = document.querySelector(targetId);
        if (target) {
          e.preventDefault();
          target.scrollIntoView({
            behavior: "smooth",
            block: "start",
          });
        }
      });
    });
  }

  /**
   * Lazy Loading
   */
  function initLazyLoading() {
    const lazyImages = document.querySelectorAll("img[data-src]");

    if ("IntersectionObserver" in window) {
      const imageObserver = new IntersectionObserver(
        function (entries) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              const img = entry.target;
              img.src = img.dataset.src;
              img.classList.add("loaded");
              imageObserver.unobserve(img);
            }
          });
        },
        {
          rootMargin: "50px 0px",
        }
      );

      lazyImages.forEach(function (img) {
        imageObserver.observe(img);
      });
    } else {
      // Fallback
      lazyImages.forEach(function (img) {
        img.src = img.dataset.src;
      });
    }
  }

  /**
   * Dropdown menus (desktop hover fix)
   */
  function initDropdowns() {
    // Desktop'ta hover delay
    const dropdowns = document.querySelectorAll(".has-dropdown");
    let timeout;

    dropdowns.forEach(function (dropdown) {
      dropdown.addEventListener("mouseenter", function () {
        clearTimeout(timeout);
      });

      dropdown.addEventListener("mouseleave", function () {
        const menu = this.querySelector(".dropdown-menu");
        timeout = setTimeout(function () {
          // CSS ile yönetiliyor
        }, 200);
      });
    });
  }

  /**
   * Product Gallery
   */
  function initGallery() {
    const mainImage = document.querySelector(".product-main-image img");
    const thumbnails = document.querySelectorAll(".product-thumb");

    if (!mainImage || thumbnails.length === 0) return;

    thumbnails.forEach(function (thumb) {
      thumb.addEventListener("click", function () {
        const newSrc = this.querySelector("img").src;
        mainImage.src = newSrc;

        thumbnails.forEach(function (t) {
          t.classList.remove("active");
        });
        this.classList.add("active");
      });
    });

    // Work gallery lightbox (basit)
    const workItems = document.querySelectorAll(".work-gallery-item");
    workItems.forEach(function (item) {
      item.addEventListener("click", function () {
        const imgSrc = this.querySelector("img").src;
        openLightbox(imgSrc);
      });
    });
  }

  /**
   * Simple Lightbox
   */
  function openLightbox(src) {
    let lightbox = document.getElementById("lightbox");

    if (!lightbox) {
      lightbox = document.createElement("div");
      lightbox.id = "lightbox";
      lightbox.innerHTML = `
                <div class="lightbox-overlay"></div>
                <div class="lightbox-content">
                    <img src="" alt="">
                    <button class="lightbox-close">&times;</button>
                </div>
            `;
      lightbox.style.cssText = `
                position: fixed; top: 0; left: 0; width: 100%; height: 100%;
                z-index: 3000; display: none; align-items: center; justify-content: center;
            `;
      lightbox.querySelector(".lightbox-overlay").style.cssText = `
                position: absolute; top: 0; left: 0; width: 100%; height: 100%;
                background: rgba(0,0,0,0.9);
            `;
      lightbox.querySelector(".lightbox-content").style.cssText = `
                position: relative; max-width: 90%; max-height: 90%;
            `;
      lightbox.querySelector(".lightbox-content img").style.cssText = `
                max-width: 100%; max-height: 90vh; border-radius: 8px;
            `;
      lightbox.querySelector(".lightbox-close").style.cssText = `
                position: absolute; top: -40px; right: 0; background: none; border: none;
                color: #fff; font-size: 32px; cursor: pointer;
            `;
      document.body.appendChild(lightbox);

      lightbox
        .querySelector(".lightbox-overlay")
        .addEventListener("click", closeLightbox);
      lightbox
        .querySelector(".lightbox-close")
        .addEventListener("click", closeLightbox);
    }

    lightbox.querySelector("img").src = src;
    lightbox.style.display = "flex";
    document.body.style.overflow = "hidden";
  }

  function closeLightbox() {
    const lightbox = document.getElementById("lightbox");
    if (lightbox) {
      lightbox.style.display = "none";
      document.body.style.overflow = "";
    }
  }

  // ESC ile lightbox kapat
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      closeLightbox();
    }
  });
})();

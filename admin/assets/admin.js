/**
 * Admin Panel JavaScript
 */

document.addEventListener("DOMContentLoaded", function () {
  // Mobile menu toggle
  const menuToggle = document.getElementById("menu-toggle");
  const sidebar = document.getElementById("sidebar");
  const sidebarClose = document.getElementById("sidebar-close");

  if (menuToggle && sidebar) {
    menuToggle.addEventListener("click", function () {
      sidebar.classList.add("open");
    });
  }

  if (sidebarClose && sidebar) {
    sidebarClose.addEventListener("click", function () {
      sidebar.classList.remove("open");
    });
  }

  // Click outside to close
  document.addEventListener("click", function (e) {
    if (sidebar && sidebar.classList.contains("open")) {
      if (!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
        sidebar.classList.remove("open");
      }
    }
  });

  // Delete confirmation
  document
    .querySelectorAll(".delete-btn, .action-btn.delete")
    .forEach(function (btn) {
      btn.addEventListener("click", function (e) {
        if (!confirm("Bu öğeyi silmek istediğinizden emin misiniz?")) {
          e.preventDefault();
        }
      });
    });

  // File upload preview
  document.querySelectorAll(".file-upload").forEach(function (upload) {
    const input = upload.querySelector('input[type="file"]');
    const preview = upload.querySelector(".file-preview");

    upload.addEventListener("click", function () {
      input.click();
    });

    if (input) {
      input.addEventListener("change", function () {
        if (this.files && this.files[0]) {
          const reader = new FileReader();
          reader.onload = function (e) {
            if (preview) {
              preview.innerHTML =
                '<img src="' + e.target.result + '" alt="Preview">';
            }
          };
          reader.readAsDataURL(this.files[0]);
        }
      });
    }
  });

  // Auto-generate slug
  const titleInput =
    document.getElementById("title") || document.getElementById("name");
  const slugInput = document.getElementById("slug");

  if (titleInput && slugInput && !slugInput.value) {
    titleInput.addEventListener("blur", function () {
      if (!slugInput.value) {
        slugInput.value = slugify(this.value);
      }
    });
  }

  function slugify(text) {
    const turkishChars = {
      ı: "i",
      İ: "i",
      ğ: "g",
      Ğ: "g",
      ü: "u",
      Ü: "u",
      ş: "s",
      Ş: "s",
      ö: "o",
      Ö: "o",
      ç: "c",
      Ç: "c",
    };

    text = text.toLowerCase();

    for (let char in turkishChars) {
      text = text.replace(new RegExp(char, "g"), turkishChars[char]);
    }

    return text
      .replace(/[^a-z0-9\s-]/g, "")
      .replace(/[\s-]+/g, "-")
      .replace(/^-+|-+$/g, "");
  }

  // CSRF token for AJAX
  window.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

  // Alert auto-dismiss
  document.querySelectorAll(".alert").forEach(function (alert) {
    setTimeout(function () {
      alert.style.opacity = "0";
      setTimeout(function () {
        alert.remove();
      }, 300);
    }, 5000);
  });
});

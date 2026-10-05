/**
 * Agriculture Training Institute - Authentication & Registration Interactive Scripts
 */

document.addEventListener('DOMContentLoaded', () => {
  initAuthTabs();
  initCategorySelector();
  initPasswordToggles();
  initPasswordStrengthAndMatch();
  initAuthModals();
});

/* ==========================================================================
   1. Tab Switcher (Sign In vs Create Account)
   ========================================================================== */
function initAuthTabs() {
  const tabSignIn = document.getElementById('tabBtnSignIn');
  const tabRegister = document.getElementById('tabBtnRegister');
  const panelSignIn = document.getElementById('panelSignIn');
  const panelRegister = document.getElementById('panelRegister');

  function switchTab(target) {
    if (target === 'login') {
      tabSignIn.classList.add('active');
      tabSignIn.setAttribute('aria-selected', 'true');
      tabRegister.classList.remove('active');
      tabRegister.setAttribute('aria-selected', 'false');

      panelSignIn.classList.add('active');
      panelRegister.classList.remove('active');

      window.history.replaceState(null, '', '?tab=login');
    } else {
      tabRegister.classList.add('active');
      tabRegister.setAttribute('aria-selected', 'true');
      tabSignIn.classList.remove('active');
      tabSignIn.setAttribute('aria-selected', 'false');

      panelRegister.classList.add('active');
      panelSignIn.classList.remove('active');

      window.history.replaceState(null, '', '?tab=register');
    }
  }

  if (tabSignIn) {
    tabSignIn.addEventListener('click', () => switchTab('login'));
  }

  if (tabRegister) {
    tabRegister.addEventListener('click', () => switchTab('register'));
  }

  document.querySelectorAll('.js-switch-to-signin').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      switchTab('login');
    });
  });

  document.querySelectorAll('.js-switch-to-register').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      switchTab('register');
    });
  });
}

/* ==========================================================================
   2. Account Category Dynamic Selector
   ========================================================================== */
function initCategorySelector() {
  const categoryCards = document.querySelectorAll('.category-card');
  const officeLabel = document.getElementById('labelOffice');
  const officeInput = document.getElementById('regOffice');
  const emailInput = document.getElementById('regEmail');
  const hintText = document.getElementById('categoryHintText');

  const categoryConfigs = {
    ati: {
      label: 'Office / Division / Regional Center',
      placeholder: 'e.g. Information Services Division (ISD) / RTC IV-A',
      emailPlaceholder: 'juan.delacruz@ati.da.gov.ph',
      hint: 'For ATI Central Office & Regional Training Centers (RTC) staff members.'
    },
    gov: {
      label: 'Government Agency / Bureau Name',
      placeholder: 'e.g. Department of Agriculture - Central / BSWM / BPI',
      emailPlaceholder: 'juan.delacruz@agency.gov.ph',
      hint: 'For other Department of Agriculture attached agencies and national government offices.'
    },
    private: {
      label: 'Organization / Company / Cooperative',
      placeholder: 'e.g. Agri-Enterprises Corp. / Farmer Cooperative',
      emailPlaceholder: 'juan.delacruz@gmail.com',
      hint: 'For private organizations, civil society, agricultural cooperatives, and guest partners.'
    }
  };

  categoryCards.forEach(card => {
    card.addEventListener('click', () => {
      categoryCards.forEach(c => c.classList.remove('active'));
      card.classList.add('active');

      const catKey = card.dataset.category;
      const config = categoryConfigs[catKey];

      if (config) {
        if (officeLabel) officeLabel.textContent = config.label;
        if (officeInput) officeInput.placeholder = config.placeholder;
        if (emailInput) emailInput.placeholder = config.emailPlaceholder;
        if (hintText) hintText.textContent = config.hint;
      }
    });
  });
}

/* ==========================================================================
   3. Password Visibility Toggles
   ========================================================================== */
function initPasswordToggles() {
  const toggleButtons = document.querySelectorAll('.auth-toggle-pwd');

  toggleButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      const targetId = btn.dataset.target;
      const input = document.getElementById(targetId);
      if (!input) return;

      const isPassword = input.type === 'password';
      input.type = isPassword ? 'text' : 'password';

      // Switch icon
      btn.innerHTML = isPassword
        ? `<svg viewBox="0 0 24 24" fill="none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>`
        : `<svg viewBox="0 0 24 24" fill="none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>`;
    });
  });
}

/* ==========================================================================
   4. Password Strength Meter & Live Match Checker
   ========================================================================== */
function initPasswordStrengthAndMatch() {
  const pwdInput = document.getElementById('regPassword');
  const confirmPwdInput = document.getElementById('regConfirmPassword');
  const strengthFill = document.getElementById('pwdStrengthFill');
  const strengthLabel = document.getElementById('pwdStrengthLabel');
  const matchIndicator = document.getElementById('pwdMatchIndicator');

  if (pwdInput && strengthFill && strengthLabel) {
    pwdInput.addEventListener('input', () => {
      const val = pwdInput.value;
      let score = 0;

      if (val.length >= 8) score += 1;
      if (/[A-Z]/.test(val) && /[a-z]/.test(val)) score += 1;
      if (/[0-9]/.test(val)) score += 1;
      if (/[^A-Za-z0-9]/.test(val)) score += 1;

      if (val.length === 0) {
        strengthFill.style.width = '0%';
        strengthLabel.textContent = '';
      } else if (score <= 1) {
        strengthFill.style.width = '25%';
        strengthFill.style.backgroundColor = '#e53e3e';
        strengthLabel.textContent = 'Weak';
        strengthLabel.style.color = '#e53e3e';
      } else if (score === 2) {
        strengthFill.style.width = '50%';
        strengthFill.style.backgroundColor = '#dd6b20';
        strengthLabel.textContent = 'Fair';
        strengthLabel.style.color = '#dd6b20';
      } else if (score === 3) {
        strengthFill.style.width = '75%';
        strengthFill.style.backgroundColor = '#3182ce';
        strengthLabel.textContent = 'Good';
        strengthLabel.style.color = '#3182ce';
      } else {
        strengthFill.style.width = '100%';
        strengthFill.style.backgroundColor = '#38a169';
        strengthLabel.textContent = 'Strong';
        strengthLabel.style.color = '#38a169';
      }

      checkPasswordMatch();
    });
  }

  if (confirmPwdInput) {
    confirmPwdInput.addEventListener('input', checkPasswordMatch);
  }

  function checkPasswordMatch() {
    if (!matchIndicator || !pwdInput || !confirmPwdInput) return;

    const pwd = pwdInput.value;
    const confirm = confirmPwdInput.value;

    if (confirm.length === 0) {
      matchIndicator.style.display = 'none';
      return;
    }

    if (pwd === confirm) {
      matchIndicator.className = 'pwd-match-indicator matching';
      matchIndicator.textContent = '✓ Passwords match';
    } else {
      matchIndicator.className = 'pwd-match-indicator mismatch';
      matchIndicator.textContent = '✕ Passwords do not match';
    }
  }
}

/* ==========================================================================
   5. Modals for Terms and Privacy Policy
   ========================================================================== */
function initAuthModals() {
  const termsModal = document.getElementById('termsModal');
  const privacyModal = document.getElementById('privacyModal');
  const closeButtons = document.querySelectorAll('.js-close-modal');

  document.querySelectorAll('.js-open-terms').forEach(el => {
    el.addEventListener('click', (e) => {
      e.preventDefault();
      if (termsModal) termsModal.classList.add('active');
    });
  });

  document.querySelectorAll('.js-open-privacy').forEach(el => {
    el.addEventListener('click', (e) => {
      e.preventDefault();
      if (privacyModal) privacyModal.classList.add('active');
    });
  });

  closeButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      if (termsModal) termsModal.classList.remove('active');
      if (privacyModal) privacyModal.classList.remove('active');
    });
  });

  [termsModal, privacyModal].forEach(modal => {
    if (!modal) return;
    modal.addEventListener('click', (e) => {
      if (e.target === modal) {
        modal.classList.remove('active');
      }
    });
  });
}

/* ==========================================================================
   6. Form Handlers (Frontend Simulation / Backend Ready)
   ========================================================================== */
function handleRegistrationSubmit(event) {
  event.preventDefault();
  const pwd = document.getElementById('regPassword')?.value;
  const confirmPwd = document.getElementById('regConfirmPassword')?.value;

  if (pwd !== confirmPwd) {
    alert('Please ensure your passwords match before submitting.');
    return;
  }

  const activeCategory = document.querySelector('.category-card.active .category-card-name')?.textContent;
  const name = document.getElementById('regFullName')?.value;

  alert(`Account Registration Successful!\n\nName: ${name}\nCategory: ${activeCategory}\n\nYour account has been submitted for official authorization. You can now sign in.`);
  
  // Switch to sign in tab
  const tabSignIn = document.getElementById('tabBtnSignIn');
  if (tabSignIn) tabSignIn.click();
}

function handleLoginSubmit(event) {
  event.preventDefault();
  const email = document.getElementById('loginEmailInput')?.value;
  alert(`Sign in simulation successful for: ${email}\nRedirecting to reservation portal...`);
  window.location.href = 'home.php';
}

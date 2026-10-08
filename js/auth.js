/**
 * Agriculture Training Institute - Authentication & Registration Interactive Scripts
 */

document.addEventListener('DOMContentLoaded', () => {
  initAuthTabs();
  initCategorySelector();
  initPasswordToggles();
  initPasswordStrengthAndMatch();
  initAuthModals();
  initCustomSelect();
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
      placeholder: 'Select your Division',
      emailPlaceholder: 'juan.delacruz@ati.da.gov.ph',
      hint: 'For ATI Central Office & Regional Training Centers (RTC) staff members.',
      options: [
        "OD – Office of the Director",
        "OAD – Office of the Assistant Director",
        "AFU / AFD – Administrative and Finance Division (or Unit)",
        "CDMD – Career Development and Management Division",
        "ISD – Information Services Division",
        "PAD – Partnerships and Accreditation Division",
        "PPD – Policy and Planning Division",
        "ATI-RTC CAR – Cordillera Administrative Region",
        "ATI-RTC I – Ilocos Region",
        "ATI-RTC II – Cagayan Valley",
        "ATI-RTC III – Central Luzon",
        "ATI-RTC IV-A – CALABARZON",
        "ATI-RTC IV-B – MIMAROPA",
        "ATI-RTC V – Bicol Region",
        "ATI-RTC VI – Western Visayas",
        "ATI-RTC VII – Central Visayas",
        "ATI-RTC VIII – Eastern Visayas",
        "ATI-RTC IX – Zamboanga Peninsula",
        "ATI-RTC X – Northern Mindanao",
        "ATI-RTC XI – Davao Region",
        "ATI-RTC XII – SOCCSKSARGEN",
        "ATI-RTC XIII – Caraga Region",
        "ATI-ITCPH – International Training Center on Pig Husbandry",
        "AFU – Administrative and Finance Unit",
        "CDMS – Career Development and Management Section",
        "ISS – Information Services Section",
        "PAS – Partnerships and Accreditation Section",
        "PMEU – Planning, Monitoring and Evaluation Unit"
      ]
    },
    gov: {
      label: 'Government Agency / Bureau Name',
      placeholder: 'Select your Agency / Office',
      emailPlaceholder: 'juan.delacruz@agency.gov.ph',
      hint: 'For other Department of Agriculture attached agencies and national government offices.',
      options: [
        "BAI – Bureau of Animal Industry",
        "BAR – Bureau of Agricultural Research",
        "BAFS – Bureau of Agriculture and Fisheries Standards",
        "BFAR – Bureau of Fisheries and Aquatic Resources",
        "BPI – Bureau of Plant Industry",
        "BSWM – Bureau of Soils and Water Management",
        "ACPC – Agricultural Credit Policy Council",
        "FPA – Fertilizer and Pesticide Authority",
        "NMIS – National Meat Inspection Service",
        "PCAF – Philippine Council for Agriculture and Fisheries",
        "PCC – Philippine Carabao Center",
        "PhilMech – Philippine Center for Postharvest Development and Mechanization",
        "NDA – National Dairy Authority",
        "NFA – National Food Authority",
        "NIA – National Irrigation Administration",
        "PCA – Philippine Coconut Authority",
        "PCIC – Philippine Crop Insurance Corporation",
        "PhilFIDA – Philippine Fiber Industry Development Authority",
        "PhilRice – Philippine Rice Research Institute",
        "SRA – Sugar Regulatory Administration",
        "CHED – Commission on Higher Education",
        "DAR – Department of Agrarian Reform",
        "DENR – Department of Environment and Natural Resources",
        "DepEd – Department of Education",
        "DILG – Department of the Interior and Local Government",
        "DOST – Department of Science and Technology",
        "DOST-PCAARRD – Philippine Council for Agriculture, Aquatic and Natural Resources Research and Development",
        "DTI – Department of Trade and Industry",
        "TESDA – Technical Education and Skills Development Authority",
        "CAO – City Agriculture Office",
        "CVO – City Veterinary Office",
        "MAO – Municipal Agriculture Office",
        "MVO – Municipal Veterinary Office",
        "PAO – Provincial Agriculture Office",
        "PVO – Provincial Veterinary Office",
        "BLGU – Barangay Local Government Unit",
        "BSU – Benguet State University",
        "CLSU – Central Luzon State University",
        "CMU – Central Mindanao University",
        "MMSU – Mariano Marcos State University",
        "UPLB – University of the Philippines Los Baños",
        "USM – University of Southern Mindanao",
        "VSU – Visayas State University"
      ]
    },
    private: {
      label: 'Organization / Company / Cooperative',
      placeholder: 'e.g. Agri-Enterprises Corp. / Farmer Cooperative',
      emailPlaceholder: 'juan.delacruz@gmail.com',
      hint: 'For private organizations, civil society, agricultural cooperatives, and guest partners.',
      options: null
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

        // Handle Custom Select Mode vs Text Input Mode
        const selectContainer = document.getElementById('officeSelectContainer');
        const optionsPanel = document.getElementById('officeOptionsPanel');
        if (officeInput && selectContainer && optionsPanel) {
          const arrow = selectContainer.querySelector('.custom-select-arrow');
          
          if (config.options && config.options.length > 0) {
            // It's a dropdown (ATI or Gov)
            officeInput.readOnly = true;
            officeInput.classList.add('custom-select-input');
            officeInput.style.cursor = 'pointer';
            selectContainer.classList.remove('disabled-select');
            if (arrow) arrow.style.display = 'block';
            
            // Populate the dropdown options
            optionsPanel.innerHTML = '';
            config.options.forEach(optVal => {
              const optDiv = document.createElement('div');
              optDiv.className = 'custom-option';
              optDiv.dataset.value = optVal;
              optDiv.textContent = optVal;
              optionsPanel.appendChild(optDiv);
            });
            // Clear current value
            officeInput.value = '';
          } else {
            // It's a free text input (Private)
            officeInput.readOnly = false;
            officeInput.classList.remove('custom-select-input');
            officeInput.style.cursor = 'text';
            selectContainer.classList.add('disabled-select');
            if (arrow) arrow.style.display = 'none';
            optionsPanel.innerHTML = ''; // Empty panel
            officeInput.value = '';
          }
        }
      }
    });
  });

  // Trigger click on initially active category to build the dropdown on load
  const activeCard = document.querySelector('.category-card.active');
  if (activeCard) {
    activeCard.click();
  }
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

function handleRegistrationSubmit(event) {
  event.preventDefault();
  const alertBox = document.getElementById('registerAlert');
  const alertText = document.getElementById('registerAlertText');
  if (alertBox) alertBox.style.display = 'none';

  const pwd = document.getElementById('regPassword')?.value;
  const confirmPwd = document.getElementById('regConfirmPassword')?.value;

  if (pwd !== confirmPwd) {
    if (alertBox && alertText) {
      alertText.textContent = 'Please ensure your passwords match before submitting.';
      alertBox.className = 'auth-alert-banner auth-alert-error auth-alert-shake';
      alertBox.style.display = 'flex';
    } else {
      alert('Please ensure your passwords match before submitting.');
    }
    return;
  }

  const activeCategory = document.querySelector('.category-card.active .category-card-name')?.textContent;
  const name = document.getElementById('regFullName')?.value;
  const office = document.getElementById('regOffice')?.value;
  const email = document.getElementById('regEmail')?.value;
  
  const submitBtn = document.getElementById('btnRegisterSubmit');
  submitBtn.disabled = true;
  submitBtn.innerHTML = '<span>Registering...</span>';

  const formData = new FormData();
  formData.append('action', 'register');
  formData.append('full_name', name);
  formData.append('email', email);
  formData.append('password', pwd);
  formData.append('office_agency', office);
  formData.append('category', activeCategory);

  fetch('ajax/auth_action.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    submitBtn.disabled = false;
    submitBtn.innerHTML = `<svg viewBox="0 0 24 24" fill="none"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg><span>Register Account</span>`;
    
    if (data.status === 'success') {
      document.getElementById('registrationForm').reset();
      const tabSignIn = document.getElementById('tabBtnSignIn');
      if (tabSignIn) tabSignIn.click();

      // Show success message on the login card
      const loginAlertBox = document.getElementById('loginAlert');
      const loginAlertText = document.getElementById('loginAlertText');
      if (loginAlertBox && loginAlertText) {
        loginAlertText.textContent = data.message + ' You may now sign in.';
        loginAlertBox.className = 'auth-alert-banner auth-alert-success';
        loginAlertBox.style.display = 'flex';
      }
    } else {
      if (alertBox && alertText) {
        alertText.textContent = data.message || 'Registration failed. Please check your inputs.';
        alertBox.className = 'auth-alert-banner auth-alert-error auth-alert-shake';
        alertBox.style.display = 'flex';
      } else {
        alert(data.message);
      }
    }
  })
  .catch(error => {
    console.error('Error:', error);
    submitBtn.disabled = false;
    submitBtn.innerHTML = `<svg viewBox="0 0 24 24" fill="none"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg><span>Register Account</span>`;
    if (alertBox && alertText) {
      alertText.textContent = 'An error occurred during registration. Please try again.';
      alertBox.className = 'auth-alert-banner auth-alert-error auth-alert-shake';
      alertBox.style.display = 'flex';
    } else {
      alert('An error occurred. Please try again.');
    }
  });
}

function handleLoginSubmit(event) {
  event.preventDefault();
  const alertBox = document.getElementById('loginAlert');
  const alertText = document.getElementById('loginAlertText');
  if (alertBox) {
    alertBox.style.display = 'none';
    alertBox.classList.remove('auth-alert-shake');
  }

  const email = document.getElementById('loginEmailInput')?.value.trim();
  const password = document.getElementById('loginPwdInput')?.value;
  
  const submitBtn = event.target.querySelector('button[type="submit"]');
  submitBtn.disabled = true;
  submitBtn.innerHTML = '<span>Signing In...</span>';

  const formData = new FormData();
  formData.append('action', 'login');
  formData.append('email', email);
  formData.append('password', password);

  fetch('ajax/auth_action.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    submitBtn.disabled = false;
    submitBtn.innerHTML = `<svg viewBox="0 0 24 24" fill="none"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg><span>Sign In to Account</span>`;

    if (data.status === 'success') {
      localStorage.setItem('ati_user_profile', JSON.stringify({
        fullName: data.user.full_name,
        role: data.user.role
      }));
      window.location.href = data.redirect;
    } else {
      if (alertBox && alertText) {
        alertText.textContent = data.message || 'Invalid email or password.';
        alertBox.className = 'auth-alert-banner auth-alert-error auth-alert-shake';
        alertBox.style.display = 'flex';
      } else {
        alert(data.message);
      }
    }
  })
  .catch(error => {
    console.error('Error:', error);
    submitBtn.disabled = false;
    submitBtn.innerHTML = `<svg viewBox="0 0 24 24" fill="none"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg><span>Sign In to Account</span>`;
    if (alertBox && alertText) {
      alertText.textContent = 'A connection error occurred. Please try again.';
      alertBox.className = 'auth-alert-banner auth-alert-error auth-alert-shake';
      alertBox.style.display = 'flex';
    } else {
      alert('An error occurred during login. Please try again.');
    }
  });
}

// Clear error banner on user input
['loginEmailInput', 'loginPwdInput'].forEach(id => {
  document.getElementById(id)?.addEventListener('input', () => {
    const alertBox = document.getElementById('loginAlert');
    if (alertBox && alertBox.style.display !== 'none') {
      alertBox.style.display = 'none';
    }
  });
});

/* ==========================================================================
   6. Custom Select Dropdown UI Initialization
   ========================================================================== */
function initCustomSelect() {
  const selectContainer = document.getElementById('officeSelectContainer');
  const selectInput = document.getElementById('regOffice');
  const optionsPanel = document.getElementById('officeOptionsPanel');

  if (!selectContainer || !selectInput || !optionsPanel) return;

  // Toggle dropdown
  selectInput.addEventListener('click', (e) => {
    if (selectContainer.classList.contains('disabled-select')) return;
    e.preventDefault();
    selectContainer.classList.toggle('open');
  });

  // Select an option using event delegation (so it works with dynamic options)
  optionsPanel.addEventListener('click', (e) => {
    const option = e.target.closest('.custom-option');
    if (!option) return;
    
    // Remove selected class from all
    const allOptions = optionsPanel.querySelectorAll('.custom-option');
    allOptions.forEach(opt => opt.classList.remove('selected'));
    
    // Add selected class to current
    option.classList.add('selected');
    
    // Update input value
    selectInput.value = option.dataset.value;
    
    // Close dropdown
    selectContainer.classList.remove('open');
    
    // Trigger change event to clear errors if needed
    selectInput.dispatchEvent(new Event('input'));
  });

  // Close when clicking outside
  document.addEventListener('click', (e) => {
    if (!selectContainer.contains(e.target)) {
      selectContainer.classList.remove('open');
    }
  });
}

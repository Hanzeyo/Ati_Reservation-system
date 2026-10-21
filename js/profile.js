/**
 * ATI Reservation System - Profile Controller
 * Clean, lightweight, real MySQL synchronization and credential management.
 */

document.addEventListener('DOMContentLoaded', () => {
  initProfileDropdown();
  loadSavedAvatar();
  initPhotoUpload();
  initEditWorkflow();
  initPasswordModal();
});

/* ==========================================================================
   1. Profile Dropdown Navigation
   ========================================================================== */
function initProfileDropdown() {
  const badge = document.getElementById('userProfileBadge');
  const dropdown = document.getElementById('profileDropdown');
  if (!badge || !dropdown) return;

  badge.addEventListener('click', (e) => {
    e.stopPropagation();
    dropdown.classList.toggle('show');
  });

  document.addEventListener('click', () => {
    dropdown.classList.remove('show');
  });
}

/* ==========================================================================
   2. Avatar Photo Management
   ========================================================================== */
function getInitials(name) {
  if (!name) return 'ATI';
  const parts = name.trim().split(/\s+/);
  if (parts.length === 1) return parts[0].substring(0, 2).toUpperCase();
  return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
}

function loadSavedAvatar() {
  const savedAvatar = localStorage.getItem('ati_user_avatar');
  const heroImg = document.getElementById('heroAvatarImg');
  const heroInitials = document.getElementById('heroAvatarInitials');
  const topCircle = document.getElementById('topbarAvatarCircle');
  const drawCircle = document.getElementById('drawerAvatarCircle');
  const btnRemove = document.getElementById('btnRemovePhoto');

  if (savedAvatar) {
    if (heroImg) {
      heroImg.src = savedAvatar;
      heroImg.style.display = 'block';
    }
    if (heroInitials) heroInitials.style.display = 'none';
    if (topCircle) topCircle.innerHTML = `<img src="${savedAvatar}" alt="Avatar" style="width:100%;height:100%;border-radius:50%;object-fit:cover;">`;
    if (drawCircle) drawCircle.innerHTML = `<img src="${savedAvatar}" alt="Avatar" style="width:100%;height:100%;border-radius:50%;object-fit:cover;">`;
    if (btnRemove) btnRemove.style.display = 'inline-flex';
  } else {
    if (heroImg) heroImg.style.display = 'none';
    if (heroInitials) heroInitials.style.display = 'flex';
    if (btnRemove) btnRemove.style.display = 'none';
  }
}

function initPhotoUpload() {
  const btnUpload = document.getElementById('btnTriggerPhotoUpload');
  const fileInput = document.getElementById('profilePicInput');
  const btnRemove = document.getElementById('btnRemovePhoto');

  if (btnUpload && fileInput) {
    btnUpload.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', (e) => {
      const file = e.target.files[0];
      if (!file) return;

      if (!file.type.startsWith('image/')) {
        showToast('Please select a valid image file (PNG, JPG, WEBP).', true);
        return;
      }

      if (file.size > 5 * 1024 * 1024) {
        showToast('Image file too large. Maximum size is 5MB.', true);
        return;
      }

      const reader = new FileReader();
      reader.onload = (event) => {
        const base64 = event.target.result;
        localStorage.setItem('ati_user_avatar', base64);
        loadSavedAvatar();
        showToast('Profile photo updated successfully!');
      };
      reader.readAsDataURL(file);
    });
  }

  if (btnRemove) {
    btnRemove.addEventListener('click', () => {
      localStorage.removeItem('ati_user_avatar');
      loadSavedAvatar();
      showToast('Profile photo removed.');
    });
  }
}

/* ==========================================================================
   3. Edit Profile Workflow (Real AJAX to MySQL)
   ========================================================================== */
function initEditWorkflow() {
  const btnEdit = document.getElementById('btnStartEditing');
  const btnCancel = document.getElementById('btnCancelEditing');
  const btnBannerCancel = document.getElementById('btnBannerCancel');
  const btnSave = document.getElementById('btnSaveProfile');
  const btnBannerSave = document.getElementById('btnBannerSave');
  const editIndicator = document.getElementById('editModeIndicator');

  function enterEditMode() {
    document.body.classList.add('is-editing');
    if (editIndicator) editIndicator.style.display = 'inline-flex';
  }

  function exitEditMode() {
    document.body.classList.remove('is-editing');
    if (editIndicator) editIndicator.style.display = 'none';
    const form = document.getElementById('profileDetailsForm');
    if (form) form.reset();
  }

  if (btnEdit) btnEdit.addEventListener('click', enterEditMode);
  if (btnCancel) btnCancel.addEventListener('click', exitEditMode);
  if (btnBannerCancel) btnBannerCancel.addEventListener('click', exitEditMode);

  if (btnSave) {
    btnSave.addEventListener('click', () => submitProfileForm());
  }
  if (btnBannerSave) {
    btnBannerSave.addEventListener('click', () => submitProfileForm());
  }
}

function handleProfileSubmit(e) {
  e.preventDefault();
  submitProfileForm();
}

function submitProfileForm() {
  const form = document.getElementById('profileDetailsForm');
  if (!form) return;

  const fullName = document.getElementById('inpFullName').value.trim();
  const email = document.getElementById('inpEmail').value.trim();
  const phone = document.getElementById('inpPhone').value.trim();
  const agency = document.getElementById('inpAgency').value.trim();

  if (!fullName) {
    showToast('Full name cannot be blank.', true);
    return;
  }
  if (!email) {
    showToast('Email address is required.', true);
    return;
  }

  const formData = new FormData();
  formData.append('action', 'update_profile');
  formData.append('full_name', fullName);
  formData.append('email', email);
  formData.append('contact_number', phone);
  formData.append('office_agency', agency);

  // Disable save buttons while submitting
  const saveBtns = [document.getElementById('btnSaveProfile'), document.getElementById('btnBannerSave')];
  saveBtns.forEach(b => { if (b) b.disabled = true; });

  fetch('ajax/update_profile.php', {
    method: 'POST',
    body: formData
  })
    .then(res => res.json())
    .then(data => {
      saveBtns.forEach(b => { if (b) b.disabled = false; });

      if (!data.success) {
        showToast(data.message || 'Failed to update profile.', true);
        return;
      }

      // Update UI displays
      const dName = document.getElementById('displayFullName');
      const vName = document.getElementById('valFullName');
      const vEmail = document.getElementById('valEmail');
      const vPhone = document.getElementById('valPhone');
      const vAgency = document.getElementById('valAgency');
      const dAgency = document.getElementById('displayAgencyText');
      const topName = document.getElementById('topbarUserName');
      const dropName = document.getElementById('dropdownUserName');
      const drawName = document.getElementById('drawerUserName');
      const dropRole = document.getElementById('dropdownUserRole');
      const heroInitials = document.getElementById('heroAvatarInitials');
      const topCircle = document.getElementById('topbarAvatarCircle');
      const drawCircle = document.getElementById('drawerAvatarCircle');

      if (dName) dName.textContent = fullName;
      if (vName) vName.textContent = fullName;
      if (vEmail) vEmail.textContent = email;
      if (vPhone) vPhone.textContent = phone || 'Not specified';
      if (vAgency) vAgency.textContent = agency;
      if (dAgency) dAgency.textContent = agency;

      if (topName) topName.textContent = fullName;
      if (dropName) dropName.textContent = fullName;
      if (drawName) drawName.textContent = fullName;
      if (dropRole) dropRole.innerHTML = `<span class="user-verified-dot"></span> ${agency}`;

      const newInitials = getInitials(fullName);
      if (heroInitials) heroInitials.textContent = newInitials;
      if (!localStorage.getItem('ati_user_avatar')) {
        if (topCircle) topCircle.textContent = newInitials;
        if (drawCircle) drawCircle.textContent = newInitials;
      }

      document.body.classList.remove('is-editing');
      const editIndicator = document.getElementById('editModeIndicator');
      if (editIndicator) editIndicator.style.display = 'none';

      showToast(data.message || 'Profile updated successfully!');
    })
    .catch(err => {
      saveBtns.forEach(b => { if (b) b.disabled = false; });
      console.error('Profile update error:', err);
      showToast('Network error while saving profile. Please try again.', true);
    });
}

/* ==========================================================================
   4. Change Password Modal (Real AJAX to MySQL)
   ========================================================================== */
function showPwdAlert(msg, isError = true) {
  const alertBox = document.getElementById('pwdModalAlert');
  if (!alertBox) return;
  alertBox.className = 'profile-modal-alert ' + (isError ? 'error' : 'success');
  alertBox.innerHTML = `
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="flex-shrink:0;">
      ${isError 
        ? '<circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line>' 
        : '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline>'}
    </svg>
    <span>${msg}</span>
  `;
  alertBox.style.display = 'flex';
}

function hidePwdAlert() {
  const alertBox = document.getElementById('pwdModalAlert');
  if (alertBox) {
    alertBox.style.display = 'none';
    alertBox.textContent = '';
  }
}

function initPasswordModal() {
  const modal = document.getElementById('changePasswordModal');
  const btnOpen = document.getElementById('btnOpenPasswordModal');
  const btnClose = document.getElementById('btnClosePasswordModal');
  const btnCancel = document.getElementById('btnCancelPasswordModal');
  const form = document.getElementById('changePasswordForm');

  if (!modal) return;

  function openModal() {
    if (form) form.reset();
    hidePwdAlert();

    // Reset password field types
    ['inpCurrentPwd', 'inpNewPwd', 'inpConfirmPwd'].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.type = 'password';
    });

    modal.style.display = 'flex';
    void modal.offsetHeight; // force reflow for smooth animation
    modal.classList.add('show');

    setTimeout(() => {
      const firstInput = document.getElementById('inpCurrentPwd');
      if (firstInput) firstInput.focus();
    }, 150);
  }

  function closeModal() {
    modal.classList.remove('show');
    hidePwdAlert();
    setTimeout(() => {
      modal.style.display = 'none';
      if (form) form.reset();
    }, 220);
  }

  if (btnOpen) btnOpen.addEventListener('click', openModal);
  if (btnClose) btnClose.addEventListener('click', closeModal);
  if (btnCancel) btnCancel.addEventListener('click', closeModal);

  modal.addEventListener('click', (e) => {
    if (e.target === modal) closeModal();
  });

  // Password visibility toggle buttons
  document.querySelectorAll('.btn-toggle-pwd').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const targetId = btn.getAttribute('data-target');
      const input = document.getElementById(targetId);
      if (!input) return;

      if (input.type === 'password') {
        input.type = 'text';
        btn.innerHTML = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>`;
      } else {
        input.type = 'password';
        btn.innerHTML = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>`;
      }
    });
  });

  if (form) {
    form.addEventListener('submit', handlePasswordSubmit);
  }
}

function handlePasswordSubmit(e) {
  if (e && e.preventDefault) e.preventDefault();

  const currentPwd = (document.getElementById('inpCurrentPwd')?.value || '').trim();
  const newPwd = document.getElementById('inpNewPwd')?.value || '';
  const confirmPwd = document.getElementById('inpConfirmPwd')?.value || '';
  const btnSubmit = document.getElementById('btnSubmitPassword');

  hidePwdAlert();

  if (!currentPwd) {
    showPwdAlert('Please enter your current password.', true);
    document.getElementById('inpCurrentPwd')?.focus();
    return;
  }

  if (newPwd.length < 8) {
    showPwdAlert('New password must be at least 8 characters long.', true);
    document.getElementById('inpNewPwd')?.focus();
    return;
  }

  if (newPwd !== confirmPwd) {
    showPwdAlert('New password and confirm password do not match.', true);
    document.getElementById('inpConfirmPwd')?.focus();
    return;
  }

  const formData = new FormData();
  formData.append('action', 'change_password');
  formData.append('current_password', currentPwd);
  formData.append('new_password', newPwd);
  formData.append('confirm_password', confirmPwd);

  if (btnSubmit) {
    btnSubmit.disabled = true;
    btnSubmit.textContent = 'Updating...';
  }

  fetch('ajax/update_profile.php', {
    method: 'POST',
    body: formData
  })
    .then(res => res.json())
    .then(data => {
      if (btnSubmit) {
        btnSubmit.disabled = false;
        btnSubmit.textContent = 'Update Password';
      }

      if (!data.success) {
        showPwdAlert(data.message || 'Failed to update password.', true);
        return;
      }

      // Success
      const modal = document.getElementById('changePasswordModal');
      if (modal) {
        modal.classList.remove('show');
        setTimeout(() => {
          modal.style.display = 'none';
          const form = document.getElementById('changePasswordForm');
          if (form) form.reset();
        }, 220);
      }

      showToast(data.message || 'Password updated successfully!');
    })
    .catch(err => {
      if (btnSubmit) {
        btnSubmit.disabled = false;
        btnSubmit.textContent = 'Update Password';
      }
      console.error('Password update error:', err);
      showPwdAlert('Network or server error while updating password.', true);
    });
}

/* ==========================================================================
   5. Toast Notification
   ========================================================================== */
function showToast(message, isError = false) {
  const toast = document.getElementById('profileToast');
  const toastMsg = document.getElementById('profileToastMsg');
  if (!toast || !toastMsg) {
    alert(message);
    return;
  }

  toastMsg.textContent = message;
  toast.style.borderColor = isError ? '#f87171' : '#86efac';
  toast.style.backgroundColor = isError ? '#7f1d1d' : '#143823';

  toast.classList.add('show');
  clearTimeout(window._toastTimeout);
  window._toastTimeout = setTimeout(() => {
    toast.classList.remove('show');
  }, 3500);
}

/**
 * ATI Reservation System - Staff Profile Controller
 * Handles View/Edit modes, client-side photo uploads & persistence,
 * copy tools, and credential modals.
 */

document.addEventListener('DOMContentLoaded', () => {
  initProfileDropdown();
  loadSavedProfile();
  loadSavedAvatar();
  initEditWorkflow();
  initPhotoUpload();
  initCopyTools();
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
   2. Profile Data Loading & Persistence (localStorage)
   ========================================================================== */
function getInitials(name) {
  if (!name) return 'JD';
  const parts = name.trim().split(/\s+/);
  if (parts.length === 1) return parts[0].substring(0, 2).toUpperCase();
  return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
}

function loadSavedProfile() {
  const savedData = localStorage.getItem('ati_user_profile');
  if (!savedData) return;

  try {
    const profile = JSON.parse(savedData);

    // Identity
    if (profile.fullName) {
      document.getElementById('displayFullName').textContent = profile.fullName;
      document.getElementById('valFullName').textContent = profile.fullName;
      document.getElementById('inpFullName').value = profile.fullName;
      
      const topName = document.getElementById('topbarUserName');
      const dropName = document.getElementById('dropdownUserName');
      const drawName = document.getElementById('drawerUserName');
      if (topName) topName.textContent = profile.fullName;
      if (dropName) dropName.textContent = profile.fullName;
      if (drawName) drawName.textContent = profile.fullName;
    }

    if (profile.position && profile.division) {
      const subtitle = `${profile.position} • ${profile.division}`;
      document.getElementById('displayRoleSubtitle').textContent = subtitle;
      
      const topRole = document.getElementById('topbarUserRole');
      const dropRole = document.getElementById('dropdownUserRole');
      const drawRole = document.getElementById('drawerUserRole');
      if (topRole) topRole.textContent = `ATI Staff (${profile.division.includes('CDD') ? 'CDD' : 'Staff'})`;
      if (dropRole) dropRole.innerHTML = `<span class="user-verified-dot"></span> ATI Personnel &bull; ${profile.division}`;
      if (drawRole) drawRole.textContent = `ATI Staff (${profile.division})`;
    }

    // Employment
    if (profile.employeeId) {
      document.getElementById('valEmployeeId').textContent = profile.employeeId;
      document.getElementById('inpEmployeeId').value = profile.employeeId;
    }
    if (profile.position) {
      document.getElementById('valPosition').textContent = profile.position;
      document.getElementById('inpPosition').value = profile.position;
    }
    if (profile.division) {
      document.getElementById('valDivision').textContent = profile.division;
      document.getElementById('inpDivision').value = profile.division;
    }
    if (profile.empStatus) {
      document.getElementById('valEmpStatus').textContent = profile.empStatus;
      document.getElementById('inpEmpStatus').value = profile.empStatus;
    }
    if (profile.supervisor) {
      document.getElementById('valSupervisor').textContent = profile.supervisor;
      document.getElementById('inpSupervisor').value = profile.supervisor;
    }
    if (profile.station) {
      document.getElementById('valStation').textContent = profile.station;
      document.getElementById('inpStation').value = profile.station;
    }

    // Contact
    if (profile.email) {
      document.getElementById('valEmail').textContent = profile.email;
      document.getElementById('inpEmail').value = profile.email;
    }
    if (profile.phone) {
      document.getElementById('valPhone').textContent = profile.phone;
      document.getElementById('inpPhone').value = profile.phone;
    }
    if (profile.landline) {
      document.getElementById('valLandline').textContent = profile.landline;
      document.getElementById('inpLandline').value = profile.landline;
    }
    if (profile.extension) {
      document.getElementById('valExtension').textContent = profile.extension;
      document.getElementById('inpExtension').value = profile.extension;
    }
    if (profile.desk) {
      document.getElementById('valDesk').textContent = profile.desk;
      document.getElementById('inpDesk').value = profile.desk;
    }

    // Update initials if no image
    if (!localStorage.getItem('ati_user_avatar') && profile.fullName) {
      const initials = getInitials(profile.fullName);
      document.getElementById('heroAvatarInitials').textContent = initials;
      const topInit = document.getElementById('topbarAvatarCircle');
      const drawInit = document.getElementById('drawerAvatarCircle');
      if (topInit) topInit.textContent = initials;
      if (drawInit) drawInit.textContent = initials;
    }
  } catch (e) {
    console.error('Error parsing stored profile', e);
  }
}

/* ==========================================================================
   3. Profile Photo Upload & Management
   ========================================================================== */
function loadSavedAvatar() {
  const avatarData = localStorage.getItem('ati_user_avatar');
  const heroImg = document.getElementById('heroAvatarImg');
  const heroInitials = document.getElementById('heroAvatarInitials');
  const btnRemove = document.getElementById('btnRemovePhoto');
  const topCircle = document.getElementById('topbarAvatarCircle');
  const drawerCircle = document.getElementById('drawerAvatarCircle');

  if (avatarData) {
    if (heroImg) {
      heroImg.src = avatarData;
      heroImg.style.display = 'block';
    }
    if (heroInitials) heroInitials.style.display = 'none';
    if (btnRemove) btnRemove.style.display = 'inline-flex';

    if (topCircle) {
      topCircle.innerHTML = `<img src="${avatarData}" alt="Avatar" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">`;
    }
    if (drawerCircle) {
      drawerCircle.innerHTML = `<img src="${avatarData}" alt="Avatar" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">`;
    }
  } else {
    if (heroImg) heroImg.style.display = 'none';
    if (heroInitials) heroInitials.style.display = 'block';
    if (btnRemove) btnRemove.style.display = 'none';
  }
}

function initPhotoUpload() {
  const triggerBtn = document.getElementById('btnTriggerPhotoUpload');
  const fileInput = document.getElementById('profilePicInput');
  const removeBtn = document.getElementById('btnRemovePhoto');

  if (triggerBtn && fileInput) {
    triggerBtn.addEventListener('click', () => {
      fileInput.click();
    });

    fileInput.addEventListener('change', (e) => {
      const file = e.target.files[0];
      if (!file) return;

      if (!file.type.match('image.*')) {
        showToast('Please select a valid image file (PNG, JPG, WEBP).');
        return;
      }

      if (file.size > 5 * 1024 * 1024) {
        showToast('Image size exceeds 5MB limit.');
        return;
      }

      const reader = new FileReader();
      reader.onload = (event) => {
        const dataUrl = event.target.result;
        try {
          localStorage.setItem('ati_user_avatar', dataUrl);
          loadSavedAvatar();
          showToast('Profile photo updated successfully!');
        } catch (err) {
          showToast('Image too large to store in browser storage.');
        }
      };
      reader.readAsDataURL(file);
    });
  }

  if (removeBtn) {
    removeBtn.addEventListener('click', () => {
      localStorage.removeItem('ati_user_avatar');
      
      const currentName = document.getElementById('displayFullName').textContent || 'Juan Dela Cruz';
      const initials = getInitials(currentName);

      const topCircle = document.getElementById('topbarAvatarCircle');
      const drawerCircle = document.getElementById('drawerAvatarCircle');
      if (topCircle) topCircle.textContent = initials;
      if (drawerCircle) drawerCircle.textContent = initials;

      loadSavedAvatar();
      showToast('Profile photo removed.');
    });
  }
}

/* ==========================================================================
   4. Edit Mode Workflow
   ========================================================================== */
function initEditWorkflow() {
  const btnStart = document.getElementById('btnStartEditing');
  const btnSave = document.getElementById('btnSaveProfile');
  const btnCancel = document.getElementById('btnCancelEditing');
  const indicator = document.getElementById('editModeIndicator');

  if (btnStart) {
    btnStart.addEventListener('click', () => {
      document.body.classList.add('is-editing');
      if (indicator) indicator.classList.add('show');
      
      // Sync input values with current displayed text
      syncInputsFromDisplays();

      // Focus first input
      const firstInput = document.getElementById('inpFullName');
      if (firstInput) firstInput.focus();
    });
  }

  if (btnCancel) {
    btnCancel.addEventListener('click', () => {
      document.body.classList.remove('is-editing');
      if (indicator) indicator.classList.remove('show');
      syncInputsFromDisplays();
      showToast('Profile edits cancelled.');
    });
  }

  if (btnSave) {
    btnSave.addEventListener('click', () => {
      saveProfileEdits();
    });
  }
}

function syncInputsFromDisplays() {
  const fields = [
    { inp: 'inpFullName', val: 'valFullName' },
    { inp: 'inpEmployeeId', val: 'valEmployeeId' },
    { inp: 'inpPosition', val: 'valPosition' },
    { inp: 'inpDivision', val: 'valDivision' },
    { inp: 'inpEmpStatus', val: 'valEmpStatus' },
    { inp: 'inpSupervisor', val: 'valSupervisor' },
    { inp: 'inpStation', val: 'valStation' },
    { inp: 'inpEmail', val: 'valEmail' },
    { inp: 'inpPhone', val: 'valPhone' },
    { inp: 'inpLandline', val: 'valLandline' },
    { inp: 'inpExtension', val: 'valExtension' },
    { inp: 'inpDesk', val: 'valDesk' }
  ];

  fields.forEach(({ inp, val }) => {
    const inputEl = document.getElementById(inp);
    const valEl = document.getElementById(val);
    if (inputEl && valEl) {
      inputEl.value = valEl.textContent.trim();
    }
  });
}

function saveProfileEdits() {
  const fullName = document.getElementById('inpFullName')?.value.trim() || 'Juan Dela Cruz';
  const employeeId = document.getElementById('inpEmployeeId')?.value.trim() || 'ATI-EMP-2024-0891';
  const position = document.getElementById('inpPosition')?.value.trim() || 'Training Specialist III';
  const division = document.getElementById('inpDivision')?.value.trim() || 'Career Development Division (CDD)';
  const empStatus = document.getElementById('inpEmpStatus')?.value.trim() || 'Permanent Regular Staff';
  const supervisor = document.getElementById('inpSupervisor')?.value.trim() || 'Dr. Ma. Cecilia Villacorta';
  const station = document.getElementById('inpStation')?.value.trim() || 'ATI Central Office • Elliptical Road, Diliman, QC';
  const email = document.getElementById('inpEmail')?.value.trim() || 'juan.delacruz@ati.da.gov.ph';
  const phone = document.getElementById('inpPhone')?.value.trim() || '+63 917 842 5901';
  const landline = document.getElementById('inpLandline')?.value.trim() || '(02) 8929-8541';
  const extension = document.getElementById('inpExtension')?.value.trim() || 'Local Ext. 214';
  const desk = document.getElementById('inpDesk')?.value.trim() || 'CDD Wing, 2nd Flr, Desk 204';

  // Apply to displays
  document.getElementById('displayFullName').textContent = fullName;
  document.getElementById('valFullName').textContent = fullName;
  document.getElementById('displayRoleSubtitle').textContent = `${position} • ${division}`;

  document.getElementById('valEmployeeId').textContent = employeeId;
  document.getElementById('valPosition').textContent = position;
  document.getElementById('valDivision').textContent = division;
  document.getElementById('valEmpStatus').textContent = empStatus;
  document.getElementById('valSupervisor').textContent = supervisor;
  document.getElementById('valStation').textContent = station;

  document.getElementById('valEmail').textContent = email;
  document.getElementById('valPhone').textContent = phone;
  document.getElementById('valLandline').textContent = landline;
  document.getElementById('valExtension').textContent = extension;
  document.getElementById('valDesk').textContent = desk;

  // Sync to Topbar and Drawers
  const topName = document.getElementById('topbarUserName');
  const dropName = document.getElementById('dropdownUserName');
  const drawName = document.getElementById('drawerUserName');
  if (topName) topName.textContent = fullName;
  if (dropName) dropName.textContent = fullName;
  if (drawName) drawName.textContent = fullName;

  const topRole = document.getElementById('topbarUserRole');
  const dropRole = document.getElementById('dropdownUserRole');
  const drawRole = document.getElementById('drawerUserRole');
  if (topRole) topRole.textContent = `ATI Staff (${division.includes('CDD') ? 'CDD' : 'Staff'})`;
  if (dropRole) dropRole.innerHTML = `<span class="user-verified-dot"></span> ATI Personnel &bull; ${division}`;
  if (drawRole) drawRole.textContent = `ATI Staff (${division})`;

  // If no uploaded photo, update initials
  if (!localStorage.getItem('ati_user_avatar')) {
    const initials = getInitials(fullName);
    document.getElementById('heroAvatarInitials').textContent = initials;
    const topCircle = document.getElementById('topbarAvatarCircle');
    const drawerCircle = document.getElementById('drawerAvatarCircle');
    if (topCircle) topCircle.textContent = initials;
    if (drawerCircle) drawerCircle.textContent = initials;
  }

  // Persist to localStorage
  const profileObject = {
    fullName,
    employeeId,
    position,
    division,
    empStatus,
    supervisor,
    station,
    email,
    phone,
    landline,
    extension,
    desk
  };
  localStorage.setItem('ati_user_profile', JSON.stringify(profileObject));

  // Exit edit mode
  document.body.classList.remove('is-editing');
  const indicator = document.getElementById('editModeIndicator');
  if (indicator) indicator.classList.remove('show');

  showToast('Profile information updated successfully!');
}

/* ==========================================================================
   5. Copy Tools
   ========================================================================== */
function initCopyTools() {
  const btnCopyEmp = document.getElementById('btnCopyEmpId');
  if (btnCopyEmp) {
    btnCopyEmp.addEventListener('click', () => {
      const empId = document.getElementById('valEmployeeId')?.textContent.trim() || 'ATI-EMP-2024-0891';
      navigator.clipboard.writeText(empId).then(() => {
        showToast(`Copied Employee ID: ${empId}`);
      }).catch(() => {
        showToast(`Employee ID: ${empId}`);
      });
    });
  }
}

/* ==========================================================================
   6. Password Modal & Form
   ========================================================================== */
function initPasswordModal() {
  const modal = document.getElementById('changePasswordModal');
  const btnOpen = document.getElementById('btnOpenPasswordModal');
  const btnClose = document.getElementById('btnClosePasswordModal');
  const btnCancel = document.getElementById('btnCancelPasswordModal');

  if (!modal) return;

  function openModal() {
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
    const firstInput = document.getElementById('inpCurrentPwd');
    if (firstInput) firstInput.focus();
  }

  function closeModal() {
    modal.classList.remove('show');
    document.body.style.overflow = '';
    const form = document.getElementById('changePasswordForm');
    if (form) form.reset();
  }

  if (btnOpen) btnOpen.addEventListener('click', openModal);
  if (btnClose) btnClose.addEventListener('click', closeModal);
  if (btnCancel) btnCancel.addEventListener('click', closeModal);

  modal.addEventListener('click', (e) => {
    if (e.target === modal) closeModal();
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && modal.classList.contains('show')) closeModal();
  });
}

window.handlePasswordSubmit = function(e) {
  e.preventDefault();
  const newPwd = document.getElementById('inpNewPwd')?.value;
  const confirmPwd = document.getElementById('inpConfirmPwd')?.value;

  if (newPwd !== confirmPwd) {
    showToast('New passwords do not match. Please re-check.');
    return;
  }

  const modal = document.getElementById('changePasswordModal');
  if (modal) modal.classList.remove('show');
  document.body.style.overflow = '';

  const form = document.getElementById('changePasswordForm');
  if (form) form.reset();

  showToast('Account password updated successfully!');
};

/* ==========================================================================
   7. Helper Toast Notification
   ========================================================================== */
function showToast(message) {
  let toast = document.getElementById('profileToast');
  let msgEl = document.getElementById('profileToastMsg');

  if (!toast) return;
  if (msgEl) msgEl.textContent = message;

  toast.classList.add('show');
  setTimeout(() => {
    toast.classList.remove('show');
  }, 3200);
}

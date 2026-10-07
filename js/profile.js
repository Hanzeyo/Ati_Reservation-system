/**
 * ATI Reservation System - Staff Profile Controller
 * Handles View/Edit modes, client-side photo uploads & persistence,
 * accidental change prevention, interactive KPI stat modals, and credential management.
 */

document.addEventListener('DOMContentLoaded', () => {
  initProfileDropdown();
  loadSavedProfile();
  loadSavedAvatar();
  initEditWorkflow();
  initSaveAndDiscardModals();
  initKpiModals();
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
      const displayFullName = document.getElementById('displayFullName');
      const valFullName = document.getElementById('valFullName');
      const inpFullName = document.getElementById('inpFullName');
      if (displayFullName) displayFullName.textContent = profile.fullName;
      if (valFullName) valFullName.textContent = profile.fullName;
      if (inpFullName) inpFullName.value = profile.fullName;
      
      const topName = document.getElementById('topbarUserName');
      const dropName = document.getElementById('dropdownUserName');
      const drawName = document.getElementById('drawerUserName');
      if (topName) topName.textContent = profile.fullName;
      if (dropName) dropName.textContent = profile.fullName;
      if (drawName) drawName.textContent = profile.fullName;
    }

    if (profile.position && profile.division) {
      const subtitle = `${profile.position} • ${profile.division}`;
      const displaySubtitle = document.getElementById('displayRoleSubtitle');
      if (displaySubtitle) displaySubtitle.textContent = subtitle;
      
      const topRole = document.getElementById('topbarUserRole');
      const dropRole = document.getElementById('dropdownUserRole');
      const drawRole = document.getElementById('drawerUserRole');
      if (topRole) topRole.textContent = `ATI Staff (${profile.division.includes('CDD') ? 'CDD' : 'Staff'})`;
      if (dropRole) dropRole.innerHTML = `<span class="user-verified-dot"></span> ATI Personnel &bull; ${profile.division}`;
      if (drawRole) drawRole.textContent = `ATI Staff (${profile.division})`;
    }

    // Employment
    if (profile.employeeId) {
      const el = document.getElementById('valEmployeeId');
      const inp = document.getElementById('inpEmployeeId');
      if (el) el.textContent = profile.employeeId;
      if (inp) inp.value = profile.employeeId;
    }
    if (profile.position) {
      const el = document.getElementById('valPosition');
      const inp = document.getElementById('inpPosition');
      if (el) el.textContent = profile.position;
      if (inp) inp.value = profile.position;
    }
    if (profile.division) {
      const el = document.getElementById('valDivision');
      const inp = document.getElementById('inpDivision');
      if (el) el.textContent = profile.division;
      if (inp) inp.value = profile.division;
    }
    if (profile.dateHired) {
      const el = document.getElementById('valDateHired');
      const inp = document.getElementById('inpDateHired');
      const tag = document.getElementById('displayHiredTag');
      const stat = document.getElementById('statHiredText');
      if (el) el.textContent = `${profile.dateHired} (Regular)`;
      if (inp) inp.value = profile.dateHired;
      if (tag) tag.textContent = `Hired: ${profile.dateHired}`;
      if (stat) stat.textContent = `Hired: ${profile.dateHired}`;
    }
    if (profile.empStatus) {
      const el = document.getElementById('valEmpStatus');
      const inp = document.getElementById('inpEmpStatus');
      if (el) el.textContent = profile.empStatus;
      if (inp) inp.value = profile.empStatus;
    }
    if (profile.supervisor) {
      const el = document.getElementById('valSupervisor');
      const inp = document.getElementById('inpSupervisor');
      if (el) el.textContent = profile.supervisor;
      if (inp) inp.value = profile.supervisor;
    }
    if (profile.station) {
      const el = document.getElementById('valStation');
      const inp = document.getElementById('inpStation');
      if (el) el.textContent = profile.station;
      if (inp) inp.value = profile.station;
    }

    // Contact
    if (profile.email) {
      const el = document.getElementById('valEmail');
      const inp = document.getElementById('inpEmail');
      if (el) el.textContent = profile.email;
      if (inp) inp.value = profile.email;
    }
    if (profile.altEmail) {
      const el = document.getElementById('valAltEmail');
      const inp = document.getElementById('inpAltEmail');
      if (el) el.textContent = profile.altEmail;
      if (inp) inp.value = profile.altEmail;
    }
    if (profile.phone) {
      const el = document.getElementById('valPhone');
      const inp = document.getElementById('inpPhone');
      if (el) el.textContent = profile.phone;
      if (inp) inp.value = profile.phone;
    }
    if (profile.emergencyContact) {
      const el = document.getElementById('valEmergencyContact');
      const inp = document.getElementById('inpEmergencyContact');
      if (el) el.textContent = profile.emergencyContact;
      if (inp) inp.value = profile.emergencyContact;
    }
    if (profile.landline) {
      const el = document.getElementById('valLandline');
      const inp = document.getElementById('inpLandline');
      if (el) el.textContent = profile.landline;
      if (inp) inp.value = profile.landline;
    }
    if (profile.extension) {
      const el = document.getElementById('valExtension');
      const inp = document.getElementById('inpExtension');
      if (el) el.textContent = profile.extension;
      if (inp) inp.value = profile.extension;
    }
    if (profile.building) {
      const el = document.getElementById('valBuilding');
      const inp = document.getElementById('inpBuilding');
      if (el) el.textContent = profile.building;
      if (inp) inp.value = profile.building;
    }
    if (profile.desk) {
      const el = document.getElementById('valDesk');
      const inp = document.getElementById('inpDesk');
      if (el) el.textContent = profile.desk;
      if (inp) inp.value = profile.desk;
    }

    // Update initials if no image
    if (!localStorage.getItem('ati_user_avatar') && profile.fullName) {
      const initials = getInitials(profile.fullName);
      const heroInit = document.getElementById('heroAvatarInitials');
      if (heroInit) heroInit.textContent = initials;
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
          showToast('Image too large to store in browser cache.');
        }
      };
      reader.readAsDataURL(file);
    });
  }

  if (removeBtn) {
    removeBtn.addEventListener('click', () => {
      localStorage.removeItem('ati_user_avatar');
      
      const currentName = document.getElementById('displayFullName')?.textContent || 'Juan Dela Cruz';
      const initials = getInitials(currentName);

      const topCircle = document.getElementById('topbarAvatarCircle');
      const drawerCircle = document.getElementById('drawerAvatarCircle');
      if (topCircle) topCircle.textContent = initials;
      if (drawerCircle) drawerCircle.textContent = initials;

      loadSavedAvatar();
      showToast('Profile photo removed. Restored initials.');
    });
  }
}

/* ==========================================================================
   4. Edit Mode Workflow & Accidental Change Prevention
   ========================================================================== */
function initEditWorkflow() {
  const btnStart = document.getElementById('btnStartEditing');
  const btnSaveHero = document.getElementById('btnSaveProfile');
  const btnSaveBanner = document.getElementById('btnBannerSave');
  const btnCancelHero = document.getElementById('btnCancelEditing');
  const btnCancelBanner = document.getElementById('btnBannerCancel');
  const indicator = document.getElementById('editModeIndicator');

  // Start editing
  if (btnStart) {
    btnStart.addEventListener('click', () => {
      document.body.classList.add('is-editing');
      if (indicator) indicator.classList.add('show');
      syncInputsFromDisplays();

      // Focus first editable input
      const firstInput = document.getElementById('inpFullName');
      if (firstInput) {
        firstInput.focus();
        firstInput.select();
      }

      // Smooth scroll to banner
      const banner = document.getElementById('profileEditBanner');
      if (banner) {
        banner.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
      }
    });
  }

  // Trigger Save Confirmation modal
  function requestSave() {
    const modal = document.getElementById('saveConfirmModal');
    if (modal) {
      modal.classList.add('show');
      document.body.style.overflow = 'hidden';
    } else {
      saveProfileEdits();
    }
  }

  if (btnSaveHero) btnSaveHero.addEventListener('click', requestSave);
  if (btnSaveBanner) btnSaveBanner.addEventListener('click', requestSave);

  // Trigger Cancel / Discard
  function requestCancel() {
    const hasChanges = checkIfInputsChanged();
    if (hasChanges) {
      const discardModal = document.getElementById('discardConfirmModal');
      if (discardModal) {
        discardModal.classList.add('show');
        document.body.style.overflow = 'hidden';
        return;
      }
    }
    // No changes, directly exit
    exitEditMode();
    showToast('Editing cancelled. No changes were made.');
  }

  if (btnCancelHero) btnCancelHero.addEventListener('click', requestCancel);
  if (btnCancelBanner) btnCancelBanner.addEventListener('click', requestCancel);
}

function checkIfInputsChanged() {
  const fields = [
    { inp: 'inpFullName', val: 'valFullName' },
    { inp: 'inpPosition', val: 'valPosition' },
    { inp: 'inpDivision', val: 'valDivision' },
    { inp: 'inpEmpStatus', val: 'valEmpStatus' },
    { inp: 'inpSupervisor', val: 'valSupervisor' },
    { inp: 'inpStation', val: 'valStation' },
    { inp: 'inpEmail', val: 'valEmail' },
    { inp: 'inpAltEmail', val: 'valAltEmail' },
    { inp: 'inpPhone', val: 'valPhone' },
    { inp: 'inpEmergencyContact', val: 'valEmergencyContact' },
    { inp: 'inpLandline', val: 'valLandline' },
    { inp: 'inpExtension', val: 'valExtension' },
    { inp: 'inpBuilding', val: 'valBuilding' },
    { inp: 'inpDesk', val: 'valDesk' }
  ];

  for (const { inp, val } of fields) {
    const inputEl = document.getElementById(inp);
    const valEl = document.getElementById(val);
    if (inputEl && valEl) {
      const currentVal = inputEl.value.trim();
      const displayVal = valEl.textContent.trim().replace(/•/g, '&bull;').replace(/\s+/g, ' ');
      // Simple difference check
      if (currentVal !== valEl.textContent.trim()) {
        return true;
      }
    }
  }
  return false;
}

function syncInputsFromDisplays() {
  const fields = [
    { inp: 'inpFullName', val: 'valFullName' },
    { inp: 'inpEmployeeId', val: 'valEmployeeId' },
    { inp: 'inpPosition', val: 'valPosition' },
    { inp: 'inpDivision', val: 'valDivision' },
    { inp: 'inpDateHired', val: 'valDateHired' },
    { inp: 'inpEmpStatus', val: 'valEmpStatus' },
    { inp: 'inpSupervisor', val: 'valSupervisor' },
    { inp: 'inpStation', val: 'valStation' },
    { inp: 'inpEmail', val: 'valEmail' },
    { inp: 'inpAltEmail', val: 'valAltEmail' },
    { inp: 'inpPhone', val: 'valPhone' },
    { inp: 'inpEmergencyContact', val: 'valEmergencyContact' },
    { inp: 'inpLandline', val: 'valLandline' },
    { inp: 'inpExtension', val: 'valExtension' },
    { inp: 'inpBuilding', val: 'valBuilding' },
    { inp: 'inpDesk', val: 'valDesk' }
  ];

  fields.forEach(({ inp, val }) => {
    const inputEl = document.getElementById(inp);
    const valEl = document.getElementById(val);
    if (inputEl && valEl) {
      let rawText = valEl.textContent.trim();
      // Clean up parentheses or extra notes if needed
      if (inp === 'inpDateHired') {
        rawText = rawText.replace(/\s*\(Regular\)\s*/i, '').trim();
      }
      inputEl.value = rawText;
    }
  });
}

function exitEditMode() {
  document.body.classList.remove('is-editing');
  const indicator = document.getElementById('editModeIndicator');
  if (indicator) indicator.classList.remove('show');
  syncInputsFromDisplays();
}

function initSaveAndDiscardModals() {
  // Save confirmation modal
  const saveModal = document.getElementById('saveConfirmModal');
  const btnCloseSave = document.getElementById('btnCloseSaveModal');
  const btnCancelSave = document.getElementById('btnCancelSaveModal');
  const btnExecSave = document.getElementById('btnExecuteSaveProfile');

  function closeSaveModal() {
    if (saveModal) saveModal.classList.remove('show');
    document.body.style.overflow = '';
  }

  if (btnCloseSave) btnCloseSave.addEventListener('click', closeSaveModal);
  if (btnCancelSave) btnCancelSave.addEventListener('click', closeSaveModal);
  if (saveModal) {
    saveModal.addEventListener('click', (e) => {
      if (e.target === saveModal) closeSaveModal();
    });
  }

  if (btnExecSave) {
    btnExecSave.addEventListener('click', () => {
      closeSaveModal();
      saveProfileEdits();
    });
  }

  // Discard confirmation modal
  const discardModal = document.getElementById('discardConfirmModal');
  const btnCloseDiscard = document.getElementById('btnCloseDiscardModal');
  const btnKeepEditing = document.getElementById('btnKeepEditing');
  const btnExecDiscard = document.getElementById('btnExecuteDiscard');

  function closeDiscardModal() {
    if (discardModal) discardModal.classList.remove('show');
    document.body.style.overflow = '';
  }

  if (btnCloseDiscard) btnCloseDiscard.addEventListener('click', closeDiscardModal);
  if (btnKeepEditing) btnKeepEditing.addEventListener('click', closeDiscardModal);
  if (discardModal) {
    discardModal.addEventListener('click', (e) => {
      if (e.target === discardModal) closeDiscardModal();
    });
  }

  if (btnExecDiscard) {
    btnExecDiscard.addEventListener('click', () => {
      closeDiscardModal();
      exitEditMode();
      showToast('Profile edits discarded.');
    });
  }
}

function saveProfileEdits() {
  const fullName = document.getElementById('inpFullName')?.value.trim() || 'Juan Dela Cruz';
  const employeeId = document.getElementById('inpEmployeeId')?.value.trim() || 'ATI-EMP-2024-0891';
  const position = document.getElementById('inpPosition')?.value.trim() || 'Training Specialist III';
  const division = document.getElementById('inpDivision')?.value.trim() || 'Career Development Division (CDD)';
  const dateHired = document.getElementById('inpDateHired')?.value.trim() || 'March 15, 2018';
  const empStatus = document.getElementById('inpEmpStatus')?.value.trim() || 'Permanent Regular Staff';
  const supervisor = document.getElementById('inpSupervisor')?.value.trim() || 'Dr. Ma. Cecilia Villacorta';
  const station = document.getElementById('inpStation')?.value.trim() || 'ATI Central Office • Diliman, QC';
  
  const email = document.getElementById('inpEmail')?.value.trim() || 'juan.delacruz@ati.da.gov.ph';
  const altEmail = document.getElementById('inpAltEmail')?.value.trim() || 'jdelacruz.ati@gmail.com';
  const phone = document.getElementById('inpPhone')?.value.trim() || '+63 917 842 5901';
  const emergencyContact = document.getElementById('inpEmergencyContact')?.value.trim() || 'Maria Dela Cruz (Spouse) • 0918 123 4567';
  const landline = document.getElementById('inpLandline')?.value.trim() || '(02) 8929-8541';
  const extension = document.getElementById('inpExtension')?.value.trim() || 'Local Ext. 214';
  const building = document.getElementById('inpBuilding')?.value.trim() || 'ATI Central Bldg • 2nd Floor';
  const desk = document.getElementById('inpDesk')?.value.trim() || 'CDD Wing • Desk 204';

  // Apply to displays
  const dispName = document.getElementById('displayFullName');
  const valName = document.getElementById('valFullName');
  const dispSub = document.getElementById('displayRoleSubtitle');
  if (dispName) dispName.textContent = fullName;
  if (valName) valName.textContent = fullName;
  if (dispSub) dispSub.textContent = `${position} • ${division}`;

  const valEmp = document.getElementById('valEmployeeId');
  const valPos = document.getElementById('valPosition');
  const valDiv = document.getElementById('valDivision');
  const valHired = document.getElementById('valDateHired');
  const valStat = document.getElementById('valEmpStatus');
  const valSup = document.getElementById('valSupervisor');
  const valStn = document.getElementById('valStation');

  if (valEmp) valEmp.textContent = employeeId;
  if (valPos) valPos.textContent = position;
  if (valDiv) valDiv.textContent = division;
  if (valHired) valHired.textContent = `${dateHired} (Regular)`;
  if (valStat) valStat.textContent = empStatus;
  if (valSup) valSup.textContent = supervisor;
  if (valStn) valStn.textContent = station;

  const valEm = document.getElementById('valEmail');
  const valAlt = document.getElementById('valAltEmail');
  const valPh = document.getElementById('valPhone');
  const valEmg = document.getElementById('valEmergencyContact');
  const valLand = document.getElementById('valLandline');
  const valExt = document.getElementById('valExtension');
  const valBld = document.getElementById('valBuilding');
  const valDsk = document.getElementById('valDesk');

  if (valEm) valEm.textContent = email;
  if (valAlt) valAlt.textContent = altEmail;
  if (valPh) valPh.textContent = phone;
  if (valEmg) valEmg.textContent = emergencyContact;
  if (valLand) valLand.textContent = landline;
  if (valExt) valExt.textContent = extension;
  if (valBld) valBld.textContent = building;
  if (valDsk) valDsk.textContent = desk;

  const tagHired = document.getElementById('displayHiredTag');
  const statHired = document.getElementById('statHiredText');
  if (tagHired) tagHired.textContent = `Hired: ${dateHired}`;
  if (statHired) statHired.textContent = `Hired: ${dateHired}`;

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
    const heroInit = document.getElementById('heroAvatarInitials');
    if (heroInit) heroInit.textContent = initials;
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
    dateHired,
    empStatus,
    supervisor,
    station,
    email,
    altEmail,
    phone,
    emergencyContact,
    landline,
    extension,
    building,
    desk
  };
  localStorage.setItem('ati_user_profile', JSON.stringify(profileObject));

  // Exit edit mode
  document.body.classList.remove('is-editing');
  const indicator = document.getElementById('editModeIndicator');
  if (indicator) indicator.classList.remove('show');

  showToast('Profile information safely saved to your record!');
}

/* ==========================================================================
   5. Interactive Stat Card Modals (KPI Detail View)
   ========================================================================== */
function initKpiModals() {
  const statBoxes = document.querySelectorAll('.profile-stat-box[data-kpi]');
  const modal = document.getElementById('statDetailModal');
  const titleEl = document.getElementById('statModalTitle');
  const subEl = document.getElementById('statModalSubtitle');
  const bodyEl = document.getElementById('statModalBody');
  const btnClose = document.getElementById('btnCloseStatModal');
  const btnCloseFooter = document.getElementById('btnCloseStatModalFooter');

  if (!modal || !bodyEl) return;

  function closeModal() {
    modal.classList.remove('show');
    document.body.style.overflow = '';
  }

  if (btnClose) btnClose.addEventListener('click', closeModal);
  if (btnCloseFooter) btnCloseFooter.addEventListener('click', closeModal);
  modal.addEventListener('click', (e) => {
    if (e.target === modal) closeModal();
  });
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && modal.classList.contains('show')) closeModal();
  });

  const kpiData = {
    total: {
      title: 'Total Reservations Filed (5 Bookings)',
      subtitle: 'Complete chronological history of official facility & dormitory bookings',
      html: `
        <div class="stat-modal-section-title">Staff Booking Log (Juan Dela Cruz)</div>
        <div class="stat-res-list">
          <div class="stat-res-item">
            <div class="stat-res-main">
              <h5>Executive Training Hall A</h5>
              <div class="stat-res-meta">
                <span class="stat-res-code">#ATI-RES-2024-1102</span>
                <span>Dec 12 &ndash; 15, 2024</span>
                <span>Capacity: 80 pax</span>
              </div>
            </div>
            <span class="stat-status-badge approved">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
              <span>Confirmed</span>
            </span>
          </div>

          <div class="stat-res-item">
            <div class="stat-res-main">
              <h5>Dormitory Male Wing &bull; Room 204</h5>
              <div class="stat-res-meta">
                <span class="stat-res-code">#ATI-RES-2024-1088</span>
                <span>Nov 20 &ndash; 22, 2024</span>
                <span>2 Beds &bull; Regular Staff</span>
              </div>
            </div>
            <span class="stat-status-badge approved">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
              <span>Confirmed</span>
            </span>
          </div>

          <div class="stat-res-item">
            <div class="stat-res-main">
              <h5>Audio-Visual Conference Center</h5>
              <div class="stat-res-meta">
                <span class="stat-res-code">#ATI-RES-2025-0014</span>
                <span>Jan 08 &ndash; 09, 2025</span>
                <span>National Extension Workshop</span>
              </div>
            </div>
            <span class="stat-status-badge pending">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
              <span>Under Review</span>
            </span>
          </div>

          <div class="stat-res-item">
            <div class="stat-res-main">
              <h5>Multi-Purpose Demonstration Hall</h5>
              <div class="stat-res-meta">
                <span class="stat-res-code">#ATI-RES-2025-0021</span>
                <span>Jan 15, 2025</span>
                <span>Staff Orientation Program</span>
              </div>
            </div>
            <span class="stat-status-badge pending">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
              <span>Under Review</span>
            </span>
          </div>

          <div class="stat-res-item">
            <div class="stat-res-main">
              <h5>Dormitory Guest Suite 101</h5>
              <div class="stat-res-meta">
                <span class="stat-res-code">#ATI-RES-2024-0945</span>
                <span>Oct 14 &ndash; 16, 2024</span>
                <span>Visiting Regional Trainer</span>
              </div>
            </div>
            <span class="stat-status-badge completed">
              <span>Completed</span>
            </span>
          </div>
        </div>
      `
    },
    approved: {
      title: 'Confirmed & Approved Reservations (2)',
      subtitle: 'Official reservations cleared by Property Custodian with gate pass generated',
      html: `
        <div class="stat-modal-section-title">Active Confirmed Bookings</div>
        <div class="stat-res-list">
          <div class="stat-res-item">
            <div class="stat-res-main">
              <h5>Executive Training Hall A</h5>
              <div class="stat-res-meta">
                <span class="stat-res-code">#ATI-RES-2024-1102</span>
                <span>Dec 12 &ndash; 15, 2024 &bull; 8:00 AM &ndash; 5:00 PM</span>
              </div>
              <div style="font-size:0.75rem; color:#166534; margin-top:0.35rem; font-weight:600;">
                &bull; Security Gate Pass Generated &bull; Audio/Video Equipment Approved
              </div>
            </div>
            <span class="stat-status-badge approved">Approved</span>
          </div>

          <div class="stat-res-item">
            <div class="stat-res-main">
              <h5>Dormitory Male Wing &bull; Room 204</h5>
              <div class="stat-res-meta">
                <span class="stat-res-code">#ATI-RES-2024-1088</span>
                <span>Nov 20 &ndash; 22, 2024 &bull; Check-in 2:00 PM</span>
              </div>
              <div style="font-size:0.75rem; color:#166534; margin-top:0.35rem; font-weight:600;">
                &bull; Keycard Clearance Ready at Reception Desk
              </div>
            </div>
            <span class="stat-status-badge approved">Approved</span>
          </div>
        </div>
      `
    },
    pending: {
      title: 'Reservations Under Admin Review (2)',
      subtitle: 'Currently routed through the required division sign-off pipeline',
      html: `
        <div class="stat-modal-section-title">Pending Routing Verification</div>
        <div class="stat-res-list">
          <div class="stat-res-item">
            <div class="stat-res-main">
              <h5>Audio-Visual Conference Center</h5>
              <div class="stat-res-meta">
                <span class="stat-res-code">#ATI-RES-2025-0014</span>
                <span>Jan 08 &ndash; 09, 2025</span>
              </div>
              <div style="font-size:0.75rem; color:#92400e; margin-top:0.35rem; font-weight:600;">
                Current Step: Step 2 of 3 &bull; Immediate Division Chief Sign-off
              </div>
            </div>
            <span class="stat-status-badge pending">Under Review</span>
          </div>

          <div class="stat-res-item">
            <div class="stat-res-main">
              <h5>Multi-Purpose Demonstration Hall</h5>
              <div class="stat-res-meta">
                <span class="stat-res-code">#ATI-RES-2025-0021</span>
                <span>Jan 15, 2025</span>
              </div>
              <div style="font-size:0.75rem; color:#92400e; margin-top:0.35rem; font-weight:600;">
                Current Step: Step 1 of 3 &bull; Property Custodian Calendar Clearance
              </div>
            </div>
            <span class="stat-status-badge pending">Under Review</span>
          </div>
        </div>
      `
    },
    service: {
      title: 'Official ATI Civil Service & Tenure Record',
      subtitle: 'Official Civil Service Commission (CSC) appointment & Plantilla records',
      html: `
        <div class="stat-modal-section-title">Official Government Service Record</div>
        <div class="service-record-grid">
          <div class="service-record-cell">
            <div class="service-record-label">Date Originally Appointed / Hired</div>
            <div class="service-record-val" style="color: #166534;">March 15, 2018</div>
          </div>
          <div class="service-record-cell">
            <div class="service-record-label">Total ATI Government Tenure</div>
            <div class="service-record-val">8 Years, 7 Months</div>
          </div>
          <div class="service-record-cell">
            <div class="service-record-label">Civil Service Classification</div>
            <div class="service-record-val">Permanent Career Service</div>
          </div>
          <div class="service-record-cell">
            <div class="service-record-label">Plantilla Item Number</div>
            <div class="service-record-val">ATI-OSEC-TS3-042</div>
          </div>
          <div class="service-record-cell">
            <div class="service-record-label">Civil Service Eligibility</div>
            <div class="service-record-val">Career Service Professional (RA 1080)</div>
          </div>
          <div class="service-record-cell">
            <div class="service-record-label">Current Salary Grade &amp; Step</div>
            <div class="service-record-val">SG-18, Step 4</div>
          </div>
          <div class="service-record-note">
            <strong>Official Record Notice:</strong> This personnel service record is maintained and verified by the ATI Administrative and Finance Unit (AFU) Personnel Section in accordance with Civil Service Commission guidelines.
          </div>
        </div>
      `
    }
  };

  statBoxes.forEach((box) => {
    box.addEventListener('click', () => {
      const kpiKey = box.getAttribute('data-kpi');
      const data = kpiData[kpiKey];
      if (!data) return;

      titleEl.textContent = data.title;
      subEl.textContent = data.subtitle;
      bodyEl.innerHTML = data.html;

      const btnGo = document.getElementById('btnModalGoToBookings');
      if (btnGo) {
        if (kpiKey === 'approved') {
          btnGo.href = 'my_reservations.php?filter=approved';
          btnGo.innerHTML = '<span>View 2 Approved Bookings &rarr;</span>';
        } else if (kpiKey === 'pending') {
          btnGo.href = 'my_reservations.php?filter=pending';
          btnGo.innerHTML = '<span>View 2 Pending Bookings &rarr;</span>';
        } else if (kpiKey === 'total') {
          btnGo.href = 'my_reservations.php';
          btnGo.innerHTML = '<span>Go to My Reservations &rarr;</span>';
        } else if (kpiKey === 'service') {
          btnGo.href = 'my_reservations.php';
          btnGo.innerHTML = '<span>View My Bookings &rarr;</span>';
        }
      }

      modal.classList.add('show');
      document.body.style.overflow = 'hidden';
    });

    // Also support keyboard Enter / Space
    box.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        box.click();
      }
    });
  });
}

/* ==========================================================================
   6. Copy Tools
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
   7. Password Modal & Form
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
   8. Helper Toast Notification
   ========================================================================== */
function showToast(message) {
  let toast = document.getElementById('profileToast');
  let msgEl = document.getElementById('profileToastMsg');

  if (!toast) return;
  if (msgEl) msgEl.textContent = message;

  toast.classList.add('show');
  setTimeout(() => {
    toast.classList.remove('show');
  }, 3400);
}

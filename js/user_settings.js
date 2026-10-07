/**
 * Agriculture Training Institute - User Settings Modal Controller
 * Provides an in-page simple Settings window for staff and administrative shortcuts
 */

function openUserSettingsModal() {
  let modal = document.getElementById('userSettingsModal');
  if (!modal) {
    createUserSettingsModalDOM();
    modal = document.getElementById('userSettingsModal');
  }
  
  // Close any open profile dropdown
  const dropdown = document.getElementById('profileDropdown');
  if (dropdown) {
    dropdown.classList.remove('show');
    dropdown.classList.remove('active');
  }

  loadUserSettingsIntoForm();
  modal.style.display = 'flex';
  document.body.style.overflow = 'hidden';
}

function closeUserSettingsModal() {
  const modal = document.getElementById('userSettingsModal');
  if (modal) {
    modal.style.display = 'none';
    document.body.style.overflow = '';
  }
}

function createUserSettingsModalDOM() {
  const modalHtml = `
  <div class="user-settings-modal-overlay" id="userSettingsModal" style="display: none;" onclick="if(event.target===this) closeUserSettingsModal();">
    <div class="user-settings-modal-card" role="dialog" aria-modal="true" aria-labelledby="userSettingsTitle">
      <div class="user-settings-modal-header">
        <div>
          <h3 id="userSettingsTitle">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <circle cx="12" cy="12" r="3"></circle>
              <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
            </svg>
            <span>Account &amp; System Settings</span>
          </h3>
          <p>Personal contact details, routing alerts, and workstation preferences</p>
        </div>
        <button type="button" class="user-settings-close-btn" onclick="closeUserSettingsModal()" aria-label="Close dialog">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
      </div>

      <form id="userSettingsForm" onsubmit="handleUserSettingsSave(event)">
        <div class="user-settings-modal-body">
          <!-- Section 1: Staff Details -->
          <div>
            <div class="user-settings-section-title">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
              <span>User Profile &amp; Contact Information</span>
            </div>
            <div class="user-settings-grid">
              <div class="user-settings-field">
                <label class="user-settings-label" for="usrSetFullName">Full Legal Name</label>
                <input type="text" id="usrSetFullName" class="user-settings-input" value="Juan Dela Cruz" required>
              </div>
              <div class="user-settings-field">
                <label class="user-settings-label" for="usrSetEmail">Official Gov Email</label>
                <input type="email" id="usrSetEmail" class="user-settings-input" value="juan.delacruz@ati.da.gov.ph" required>
              </div>
              <div class="user-settings-field">
                <label class="user-settings-label" for="usrSetPhone">Mobile / SMS Contact</label>
                <input type="tel" id="usrSetPhone" class="user-settings-input" value="+63 917 842 5901">
              </div>
              <div class="user-settings-field">
                <label class="user-settings-label" for="usrSetDivision">Division / Unit</label>
                <input type="text" id="usrSetDivision" class="user-settings-input" value="Career Development Division (CDD)">
              </div>
            </div>
          </div>

          <!-- Section 2: Preferences -->
          <div>
            <div class="user-settings-section-title">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
              <span>Notification &amp; Approval Alerts</span>
            </div>
            <div style="display: flex; flex-direction: column; gap: 0.65rem;">
              <div class="user-settings-switch-item">
                <div class="user-settings-switch-text">
                  <h5>SMS Notifications for Status Updates</h5>
                  <p>Receive text alerts when your booking or reservation is endorsed or approved</p>
                </div>
                <label class="toggle-switch">
                  <input type="checkbox" id="usrSetSmsAlerts" checked>
                  <span class="slider-round"></span>
                </label>
              </div>

              <div class="user-settings-switch-item">
                <div class="user-settings-switch-text">
                  <h5>Auto-Email Official Gate Pass PDF</h5>
                  <p>Automatically receive printable QR-coded security pass upon clearance</p>
                </div>
                <label class="toggle-switch">
                  <input type="checkbox" id="usrSetAutoGatePass" checked>
                  <span class="slider-round"></span>
                </label>
              </div>
            </div>
          </div>

          <!-- Section 3: Admin Shortcut for Authorized Staff -->
          <div class="user-settings-admin-box">
            <div class="user-settings-admin-box-text">
              <h5>Institutional Administration Controls</h5>
              <p>Configure facility rates, advance booking windows, and system policies</p>
            </div>
            <a href="admin_settings.php" class="btn-admin-portal-link" title="Open Administrative Settings Portal">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
              <span>Admin Settings &rarr;</span>
            </a>
          </div>
        </div>

        <div class="user-settings-modal-footer">
          <button type="button" class="btn-user-settings-cancel" onclick="closeUserSettingsModal()">Cancel</button>
          <button type="submit" class="btn-user-settings-save">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
            <span>Save Preferences</span>
          </button>
        </div>
      </form>
    </div>
  </div>
  `;

  document.body.insertAdjacentHTML('beforeend', modalHtml);
}

function loadUserSettingsIntoForm() {
  const saved = localStorage.getItem('ati_user_settings');
  if (saved) {
    try {
      const data = JSON.parse(saved);
      if (data.fullName) setElVal('usrSetFullName', data.fullName);
      if (data.email) setElVal('usrSetEmail', data.email);
      if (data.phone) setElVal('usrSetPhone', data.phone);
      if (data.division) setElVal('usrSetDivision', data.division);
      if (typeof data.smsAlerts === 'boolean') setElChecked('usrSetSmsAlerts', data.smsAlerts);
      if (typeof data.autoGatePass === 'boolean') setElChecked('usrSetAutoGatePass', data.autoGatePass);
    } catch (e) {
      console.warn('Could not parse user settings', e);
    }
  }
}

function handleUserSettingsSave(event) {
  event.preventDefault();
  const fullName = getElVal('usrSetFullName', 'Juan Dela Cruz');
  const email = getElVal('usrSetEmail', 'juan.delacruz@ati.da.gov.ph');
  const phone = getElVal('usrSetPhone', '+63 917 842 5901');
  const division = getElVal('usrSetDivision', 'Career Development Division (CDD)');
  const smsAlerts = getElChecked('usrSetSmsAlerts');
  const autoGatePass = getElChecked('usrSetAutoGatePass');

  const payload = { fullName, email, phone, division, smsAlerts, autoGatePass };
  localStorage.setItem('ati_user_settings', JSON.stringify(payload));

  // Update topbar & dropdown name labels dynamically if elements exist
  document.querySelectorAll('#topbarUserName, .dropdown-user-name, #displayFullName').forEach(el => {
    el.textContent = fullName;
  });
  document.querySelectorAll('.dropdown-user-email, #valEmail').forEach(el => {
    if (el.id === 'valEmail') el.textContent = email;
    else el.textContent = email;
  });

  closeUserSettingsModal();
  showSimpleSettingsToast('Settings updated successfully!');
}

function showSimpleSettingsToast(msg) {
  let toast = document.createElement('div');
  toast.style.position = 'fixed';
  toast.style.bottom = '24px';
  toast.style.right = '24px';
  toast.style.background = '#174d2f';
  toast.style.color = '#ffffff';
  toast.style.padding = '0.75rem 1.25rem';
  toast.style.borderRadius = '10px';
  toast.style.fontSize = '0.86rem';
  toast.style.fontWeight = '700';
  toast.style.boxShadow = '0 8px 24px rgba(0,0,0,0.2)';
  toast.style.zIndex = '9999';
  toast.style.display = 'flex';
  toast.style.alignItems = 'center';
  toast.style.gap = '0.5rem';
  toast.innerHTML = `
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
    <span>${msg}</span>
  `;
  document.body.appendChild(toast);
  setTimeout(() => {
    toast.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(10px)';
    setTimeout(() => toast.remove(), 300);
  }, 3200);
}

function setElVal(id, v) { const el = document.getElementById(id); if (el) el.value = v; }
function setElChecked(id, v) { const el = document.getElementById(id); if (el) el.checked = Boolean(v); }
function getElVal(id, def) { const el = document.getElementById(id); return el ? el.value : def; }
function getElChecked(id) { const el = document.getElementById(id); return el ? el.checked : false; }

// Auto-create on DOM ready
document.addEventListener('DOMContentLoaded', function() {
  createUserSettingsModalDOM();
});

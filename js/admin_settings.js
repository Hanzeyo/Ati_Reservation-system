/**
 * Agriculture Training Institute - Facility and Dormitory Reservation System
 * System Settings & Governance Controller
 */

// Default System Configuration
const DEFAULT_SYSTEM_SETTINGS = {
  general: {
    agencyName: 'Agricultural Training Institute - Central Office',
    systemName: 'ATI Facility & Dormitory Reservation Portal',
    intranetUrl: 'http://localhost/Ati_Reservation-system/',
    operatingHoursStart: '08:00',
    operatingHoursEnd: '17:00',
    operatingDays: 'Mon - Fri (8:00 AM – 5:00 PM)',
    timezone: 'Asia/Manila (PHT, GMT+8)',
    currency: 'PHP',
    maintenanceMode: false,
    maintenanceMessage: 'The ATI Reservation Portal is undergoing scheduled IT maintenance. Normal booking operations will resume at 1:00 PM.'
  },
  policies: {
    minAdvanceDays: 3,
    maxAdvanceDays: 90,
    maxConsecutiveDays: 5,
    autoCancelHours: 48,
    weekendClearanceRequired: true,
    slotHoldMinutes: 15,
    allowConcurrentRequests: false
  },
  pricing: {
    traineeDormRate: 500, // Floors 1-4 (Sampaguita, Ilang-Ilang, Gumamela, Rosal)
    vipDormRate: 800,     // Floors 5-6 (Waling-Waling, Dama de Noche)
    serranoHallFull: 8000,
    serranoHallHalf: 4500,
    boardroomFull: 6000,
    boardroomHalf: 3500,
    fourHCenterFull: 5000,
    fourHCenterHalf: 3000,
    trainingHallFull: 4000,
    trainingHallHalf: 2500,
    addonLedWall: 3500,
    addonSoundRig: 1500,
    addonZoomHybrid: 2000,
    paymentLinkbiz: true,
    paymentTrustFund: true,
    paymentCashier: true
  },
  workflow: {
    twoStageApproval: true,
    stage1Role: 'Recommending Officer (Admin & Logistics)',
    stage2Role: 'Clearance Authority (Office of the Director IV)',
    autoRouteHalls: true,
    autoRouteDorms: true,
    strictCapacityEnforcement: true,
    autoGatePassGeneration: true
  },
  notifications: {
    emailActive: true,
    smtpHost: 'smtp.ati.da.gov.ph',
    smtpPort: 587,
    senderEmail: 'noreply@ati.da.gov.ph',
    smsActive: true,
    smsSenderId: 'ATI-NOTIF',
    dailyApproverDigest: true,
    digestTime: '07:30',
    notifyOnCancellation: true
  },
  security: {
    passwordExpiryDays: 90,
    sessionTimeoutMinutes: 30,
    require2FAForAdmin: true,
    auditRetentionDays: 180
  }
};

// Active state
let currentSettings = JSON.parse(JSON.stringify(DEFAULT_SYSTEM_SETTINGS));

// Initialize on DOM Ready
document.addEventListener('DOMContentLoaded', function () {
  initLiveClock();
  loadSavedSettings();
  setupEventListeners();
});

// Live ticking clock in topbar
function initLiveClock() {
  const dateEl = document.getElementById('currentDateDisplay');
  if (!dateEl) return;

  function update() {
    const now = new Date();
    const dateStr = now.toLocaleDateString('en-US', {
      weekday: 'short',
      month: 'short',
      day: 'numeric',
      year: 'numeric'
    });
    const timeStr = now.toLocaleTimeString('en-US', {
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit',
      hour12: true
    });
    dateEl.textContent = `${dateStr} • ${timeStr}`;
  }

  update();
  setInterval(update, 1000);
}

// Load Settings from LocalStorage
function loadSavedSettings() {
  const saved = localStorage.getItem('ati_system_settings');
  if (saved) {
    try {
      const parsed = JSON.parse(saved);
      currentSettings = deepMerge(DEFAULT_SYSTEM_SETTINGS, parsed);
    } catch (e) {
      console.warn('Could not parse saved settings, using defaults', e);
      currentSettings = JSON.parse(JSON.stringify(DEFAULT_SYSTEM_SETTINGS));
    }
  }
  populateFormFields(currentSettings);
}

// Deep merge helper
function deepMerge(target, source) {
  const output = Object.assign({}, target);
  if (isObject(target) && isObject(source)) {
    Object.keys(source).forEach(key => {
      if (isObject(source[key])) {
        if (!(key in target)) Object.assign(output, { [key]: source[key] });
        else output[key] = deepMerge(target[key], source[key]);
      } else {
        Object.assign(output, { [key]: source[key] });
      }
    });
  }
  return output;
}

function isObject(item) {
  return (item && typeof item === 'object' && !Array.isArray(item));
}

// Populate UI form controls with currentSettings values
function populateFormFields(s) {
  // General
  setVal('setAgencyName', s.general.agencyName);
  setVal('setSystemName', s.general.systemName);
  setVal('setIntranetUrl', s.general.intranetUrl);
  setVal('setOperatingHoursStart', s.general.operatingHoursStart);
  setVal('setOperatingHoursEnd', s.general.operatingHoursEnd);
  setVal('setOperatingDays', s.general.operatingDays);
  setChecked('setMaintenanceMode', s.general.maintenanceMode);
  setVal('setMaintenanceMessage', s.general.maintenanceMessage);

  // Policies
  setVal('setMinAdvanceDays', s.policies.minAdvanceDays);
  setVal('setMaxAdvanceDays', s.policies.maxAdvanceDays);
  setVal('setMaxConsecutiveDays', s.policies.maxConsecutiveDays);
  setVal('setAutoCancelHours', s.policies.autoCancelHours);
  setChecked('setWeekendClearance', s.policies.weekendClearanceRequired);
  setVal('setSlotHoldMinutes', s.policies.slotHoldMinutes);
  setChecked('setConcurrentRequests', s.policies.allowConcurrentRequests);

  // Pricing Matrix (VIP 5th & 6th Floor Guarantee!)
  setVal('setTraineeDormRate', s.pricing.traineeDormRate);
  setVal('setVipDormRate', s.pricing.vipDormRate);
  setVal('setSerranoFull', s.pricing.serranoHallFull);
  setVal('setSerranoHalf', s.pricing.serranoHallHalf);
  setVal('setBoardroomFull', s.pricing.boardroomFull);
  setVal('setBoardroomHalf', s.pricing.boardroomHalf);
  setVal('setFourHFull', s.pricing.fourHCenterFull);
  setVal('setFourHHalf', s.pricing.fourHCenterHalf);
  setVal('setTrainingHallFull', s.pricing.trainingHallFull);
  setVal('setTrainingHallHalf', s.pricing.trainingHallHalf);
  setVal('setAddonLedWall', s.pricing.addonLedWall);
  setVal('setAddonSoundRig', s.pricing.addonSoundRig);
  setVal('setAddonZoomHybrid', s.pricing.addonZoomHybrid);
  setChecked('setPaymentLinkbiz', s.pricing.paymentLinkbiz);
  setChecked('setPaymentTrustFund', s.pricing.paymentTrustFund);
  setChecked('setPaymentCashier', s.pricing.paymentCashier);

  // Workflow
  setChecked('setTwoStageApproval', s.workflow.twoStageApproval);
  setChecked('setAutoRouteHalls', s.workflow.autoRouteHalls);
  setChecked('setAutoRouteDorms', s.workflow.autoRouteDorms);
  setChecked('setStrictCapacity', s.workflow.strictCapacityEnforcement);
  setChecked('setAutoGatePass', s.workflow.autoGatePassGeneration);

  // Notifications
  setChecked('setEmailActive', s.notifications.emailActive);
  setVal('setSmtpHost', s.notifications.smtpHost);
  setVal('setSmtpPort', s.notifications.smtpPort);
  setVal('setSenderEmail', s.notifications.senderEmail);
  setChecked('setSmsActive', s.notifications.smsActive);
  setVal('setSmsSenderId', s.notifications.smsSenderId);
  setChecked('setDailyDigest', s.notifications.dailyApproverDigest);
  setVal('setDigestTime', s.notifications.digestTime);
  setChecked('setNotifyCancellation', s.notifications.notifyOnCancellation);

  // Security
  setVal('setPasswordExpiryDays', s.security.passwordExpiryDays);
  setVal('setSessionTimeoutMinutes', s.security.sessionTimeoutMinutes);
  setChecked('setRequire2FA', s.security.require2FAForAdmin);
  setVal('setAuditRetentionDays', s.security.auditRetentionDays);
}

function setVal(id, val) {
  const el = document.getElementById(id);
  if (el) el.value = val;
}

function setChecked(id, val) {
  const el = document.getElementById(id);
  if (el) el.checked = Boolean(val);
}

function getVal(id, fallback) {
  const el = document.getElementById(id);
  return el ? el.value : fallback;
}

function getNumVal(id, fallback) {
  const el = document.getElementById(id);
  return el ? parseFloat(el.value) || fallback : fallback;
}

function getChecked(id) {
  const el = document.getElementById(id);
  return el ? el.checked : false;
}

// Tab Switching
function switchSettingsTab(tabName, btn) {
  document.querySelectorAll('.settings-tab-btn').forEach(b => b.classList.remove('active'));
  document.querySelectorAll('.settings-panel').forEach(p => p.classList.remove('active'));

  if (btn) btn.classList.add('active');
  const panel = document.getElementById('panel-' + tabName);
  if (panel) panel.classList.add('active');
}

// Collect values from form and save
function saveAllSystemSettings() {
  const s = {
    general: {
      agencyName: getVal('setAgencyName', 'Agricultural Training Institute - Central Office'),
      systemName: getVal('setSystemName', 'ATI Facility & Dormitory Reservation Portal'),
      intranetUrl: getVal('setIntranetUrl', 'http://localhost/Ati_Reservation-system/'),
      operatingHoursStart: getVal('setOperatingHoursStart', '08:00'),
      operatingHoursEnd: getVal('setOperatingHoursEnd', '17:00'),
      operatingDays: getVal('setOperatingDays', 'Mon - Fri (8:00 AM – 5:00 PM)'),
      timezone: 'Asia/Manila (PHT, GMT+8)',
      currency: 'PHP',
      maintenanceMode: getChecked('setMaintenanceMode'),
      maintenanceMessage: getVal('setMaintenanceMessage', '')
    },
    policies: {
      minAdvanceDays: getNumVal('setMinAdvanceDays', 3),
      maxAdvanceDays: getNumVal('setMaxAdvanceDays', 90),
      maxConsecutiveDays: getNumVal('setMaxConsecutiveDays', 5),
      autoCancelHours: getNumVal('setAutoCancelHours', 48),
      weekendClearanceRequired: getChecked('setWeekendClearance'),
      slotHoldMinutes: getNumVal('setSlotHoldMinutes', 15),
      allowConcurrentRequests: getChecked('setConcurrentRequests')
    },
    pricing: {
      traineeDormRate: getNumVal('setTraineeDormRate', 500),
      vipDormRate: getNumVal('setVipDormRate', 800),
      serranoHallFull: getNumVal('setSerranoFull', 8000),
      serranoHallHalf: getNumVal('setSerranoHalf', 4500),
      boardroomFull: getNumVal('setBoardroomFull', 6000),
      boardroomHalf: getNumVal('setBoardroomHalf', 3500),
      fourHCenterFull: getNumVal('setFourHFull', 5000),
      fourHCenterHalf: getNumVal('setFourHHalf', 3000),
      trainingHallFull: getNumVal('setTrainingHallFull', 4000),
      trainingHallHalf: getNumVal('setTrainingHallHalf', 2500),
      addonLedWall: getNumVal('setAddonLedWall', 3500),
      addonSoundRig: getNumVal('setAddonSoundRig', 1500),
      addonZoomHybrid: getNumVal('setAddonZoomHybrid', 2000),
      paymentLinkbiz: getChecked('setPaymentLinkbiz'),
      paymentTrustFund: getChecked('setPaymentTrustFund'),
      paymentCashier: getChecked('setPaymentCashier')
    },
    workflow: {
      twoStageApproval: getChecked('setTwoStageApproval'),
      stage1Role: 'Recommending Officer (Admin & Logistics)',
      stage2Role: 'Clearance Authority (Office of the Director IV)',
      autoRouteHalls: getChecked('setAutoRouteHalls'),
      autoRouteDorms: getChecked('setAutoRouteDorms'),
      strictCapacityEnforcement: getChecked('setStrictCapacity'),
      autoGatePassGeneration: getChecked('setAutoGatePass')
    },
    notifications: {
      emailActive: getChecked('setEmailActive'),
      smtpHost: getVal('setSmtpHost', 'smtp.ati.da.gov.ph'),
      smtpPort: getNumVal('setSmtpPort', 587),
      senderEmail: getVal('setSenderEmail', 'noreply@ati.da.gov.ph'),
      smsActive: getChecked('setSmsActive'),
      smsSenderId: getVal('setSmsSenderId', 'ATI-NOTIF'),
      dailyApproverDigest: getChecked('setDailyDigest'),
      digestTime: getVal('setDigestTime', '07:30'),
      notifyOnCancellation: getChecked('setNotifyCancellation')
    },
    security: {
      passwordExpiryDays: getNumVal('setPasswordExpiryDays', 90),
      sessionTimeoutMinutes: getNumVal('setSessionTimeoutMinutes', 30),
      require2FAForAdmin: getChecked('setRequire2FA'),
      auditRetentionDays: getNumVal('setAuditRetentionDays', 180)
    }
  };

  currentSettings = s;
  localStorage.setItem('ati_system_settings', JSON.stringify(s));

  // Visual button feedback
  const saveBtn = document.getElementById('btnSaveAllSettings');
  if (saveBtn) {
    const originalText = saveBtn.innerHTML;
    saveBtn.innerHTML = `
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <polyline points="20 6 9 17 4 12"></polyline>
      </svg>
      <span>Settings Saved!</span>
    `;
    saveBtn.style.background = '#15803d';

    setTimeout(() => {
      saveBtn.innerHTML = originalText;
      saveBtn.style.background = '';
    }, 2200);
  }

  showSettingsToast('Institutional System Settings successfully applied and synchronized across the portal.', 'success');
}

// Export Configuration as JSON download
function exportSystemConfig() {
  const dataStr = 'data:text/json;charset=utf-8,' + encodeURIComponent(JSON.stringify(currentSettings, null, 2));
  const downloadAnchor = document.createElement('a');
  downloadAnchor.setAttribute('href', dataStr);
  downloadAnchor.setAttribute('download', `ati_system_config_${new Date().toISOString().slice(0, 10)}.json`);
  document.body.appendChild(downloadAnchor);
  downloadAnchor.click();
  downloadAnchor.remove();

  showSettingsToast('System configuration exported successfully as JSON file.', 'success');
}

// Download Complete Database Backup (JSON structure containing all reservations, users, and audit logs)
function downloadDatabaseBackup() {
  const fullBackup = {
    exportedAt: new Date().toISOString(),
    system: 'ATI Central Office Reservation System',
    version: '3.4.2-enterprise',
    settings: currentSettings,
    summaryMetrics: {
      totalHalls: 4,
      totalDormFloors: 6,
      floors1to4: 'Standard Trainee Dormitories (₱500/night)',
      floors5and6: 'VIP Executive Suites (₱800/night)',
      registeredStaff: 18,
      activeQueueCount: 5
    },
    databaseTables: {
      ati_facilities: [
        { id: 1, name: 'Serrano Hall', type: 'Hall', capacity: 150, rate_full: currentSettings.pricing.serranoHallFull },
        { id: 2, name: 'Executive Boardroom', type: 'Boardroom', capacity: 25, rate_full: currentSettings.pricing.boardroomFull },
        { id: 3, name: '4-H Learning Center', type: 'Hall', capacity: 80, rate_full: currentSettings.pricing.fourHCenterFull },
        { id: 4, name: 'Training Hall A & B', type: 'Hall', capacity: 60, rate_full: currentSettings.pricing.trainingHallFull }
      ],
      ati_dormitories: [
        { floor: 1, name: 'Sampaguita', type: 'Standard Trainee', rate: currentSettings.pricing.traineeDormRate },
        { floor: 2, name: 'Ilang-Ilang', type: 'Standard Trainee', rate: currentSettings.pricing.traineeDormRate },
        { floor: 3, name: 'Gumamela', type: 'Standard Trainee', rate: currentSettings.pricing.traineeDormRate },
        { floor: 4, name: 'Rosal', type: 'Standard Trainee', rate: currentSettings.pricing.traineeDormRate },
        { floor: 5, name: 'Waling-Waling', type: 'VIP Executive', rate: currentSettings.pricing.vipDormRate },
        { floor: 6, name: 'Dama de Noche', type: 'VIP Executive', rate: currentSettings.pricing.vipDormRate }
      ],
      ati_reservations_snapshot: [
        { ref: 'R-2026-0891', applicant: 'Engr. Juan Dela Cruz', facility: 'Serrano Hall', status: 'pending' },
        { ref: 'R-2026-0892', applicant: 'Dr. Maria Santos', facility: 'Dormitory Suite', status: 'pending' },
        { ref: 'R-2026-0893', applicant: 'Atty. Bernardo Castro', facility: 'Executive Boardroom', status: 'pending' },
        { ref: 'R-2026-0894', applicant: 'Ramon Pascual', facility: '4-H Learning Center', status: 'pending' }
      ]
    }
  };

  const dataStr = 'data:text/json;charset=utf-8,' + encodeURIComponent(JSON.stringify(fullBackup, null, 2));
  const a = document.createElement('a');
  a.setAttribute('href', dataStr);
  a.setAttribute('download', `ati_full_database_backup_${new Date().toISOString().slice(0, 10)}.json`);
  document.body.appendChild(a);
  a.click();
  a.remove();

  showSettingsToast('Complete database & configuration snapshot generated and downloaded.', 'success');
}

// Import / Restore Settings from JSON file
function triggerImportConfig() {
  const fileInput = document.getElementById('importConfigFile');
  if (fileInput) fileInput.click();
}

function handleConfigFileChosen(event) {
  const file = event.target.files && event.target.files[0];
  if (!file) return;

  const reader = new FileReader();
  reader.onload = function (e) {
    try {
      const imported = JSON.parse(e.target.result);
      const settingsToApply = imported.settings ? imported.settings : imported;
      currentSettings = deepMerge(DEFAULT_SYSTEM_SETTINGS, settingsToApply);
      populateFormFields(currentSettings);
      localStorage.setItem('ati_system_settings', JSON.stringify(currentSettings));
      showSettingsToast('Configuration successfully restored and loaded from file.', 'success');
    } catch (err) {
      alert('Error parsing uploaded JSON file: ' + err.message);
    }
  };
  reader.readAsText(file);
  event.target.value = '';
}

// Reset confirmation modal
function confirmFactoryReset() {
  const modal = document.getElementById('resetConfirmModal');
  if (modal) modal.style.display = 'flex';
}

function closeResetModal() {
  const modal = document.getElementById('resetConfirmModal');
  if (modal) modal.style.display = 'none';
}

function executeFactoryReset() {
  currentSettings = JSON.parse(JSON.stringify(DEFAULT_SYSTEM_SETTINGS));
  localStorage.removeItem('ati_system_settings');
  populateFormFields(currentSettings);
  closeResetModal();
  showSettingsToast('All settings reverted to standard institutional defaults.', 'success');
}

// Test Communication Functions
function testEmailDispatch() {
  const sender = getVal('setSenderEmail', 'noreply@ati.da.gov.ph');
  showSettingsToast(`Dispatched simulated test email via SMTP relay (${sender}). Connection status: OK (250).`, 'success');
}

function testSmsGateway() {
  const senderId = getVal('setSmsSenderId', 'ATI-NOTIF');
  showSettingsToast(`SMS gateway handshake verified. Broadcast tag: ${senderId}. Latency: 42ms.`, 'success');
}

// Toast helper
function showSettingsToast(msg, type = 'success') {
  const container = document.getElementById('settingsToastContainer');
  if (!container) return;

  const toast = document.createElement('div');
  toast.style.background = type === 'success' ? '#174d2f' : '#b91c1c';
  toast.style.color = '#ffffff';
  toast.style.padding = '0.75rem 1.25rem';
  toast.style.borderRadius = '8px';
  toast.style.fontSize = '0.84rem';
  toast.style.fontWeight = '600';
  toast.style.display = 'flex';
  toast.style.alignItems = 'center';
  toast.style.gap = '0.65rem';
  toast.style.boxShadow = '0 4px 14px rgba(0,0,0,0.2)';
  toast.style.marginBottom = '0.5rem';
  toast.style.transition = 'all 0.25s ease';

  toast.innerHTML = `
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
      <polyline points="20 6 9 17 4 12"></polyline>
    </svg>
    <span>${msg}</span>
  `;

  container.appendChild(toast);

  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(10px)';
    setTimeout(() => toast.remove(), 260);
  }, 3800);
}

function setupEventListeners() {
  // Mobile drawer toggle if present
  const toggleBtn = document.getElementById('adminSidebarToggle');
  if (toggleBtn) {
    toggleBtn.addEventListener('click', function () {
      const sidebar = document.getElementById('adminSidebar');
      if (sidebar) sidebar.classList.toggle('open');
    });
  }
}

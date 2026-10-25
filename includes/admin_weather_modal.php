<?php
/**
 * ATI Reservation System - PAGASA Bad Weather Suspension Control Modal Component
 * Included in Admin Calendar / Schedule Operations & Executive Dashboard
 */
?>
<!-- PAGASA Bad Weather Suspension Controller Modal -->
<div class="admin-modal-overlay" id="adminWeatherSuspensionModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(6px); z-index: 9999; align-items: center; justify-content: center; padding: 1rem; overflow-y: auto;">
  <div class="admin-modal-card" style="background: #ffffff; width: 100%; max-width: 660px; border-radius: 14px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3); overflow: hidden; border: 1px solid #e2e8f0; animation: modalPop 0.25s ease-out;">
    
    <!-- Modal Header -->
    <div style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); padding: 1.15rem 1.4rem; display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid #ea580c;">
      <div style="display: flex; align-items: center; gap: 0.75rem;">
        <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(234, 88, 12, 0.2); border: 1px solid rgba(249, 115, 22, 0.5); display: flex; align-items: center; justify-content: center; color: #fb923c;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <path d="M19 16.9A5 5 0 0 0 18 7h-1.26a8 8 0 1 0-11.62 9"></path>
            <polyline points="13 11 9 17 15 17 11 23"></polyline>
          </svg>
        </div>
        <div>
          <h3 style="margin: 0; font-size: 1.05rem; font-weight: 800; color: #ffffff; letter-spacing: -0.01em;">PAGASA Bad Weather Suspension Directorate</h3>
          <p style="margin: 0.15rem 0 0; font-size: 0.75rem; color: #94a3b8;">Republic of the Philippines • Civil Service & EO 66 Directives</p>
        </div>
      </div>
      <button type="button" onclick="closeWeatherSuspensionModal()" style="background: rgba(255, 255, 255, 0.1); border: none; color: #cbd5e1; width: 30px; height: 30px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease;" aria-label="Close modal">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>
    </div>

    <!-- Modal Body -->
    <div style="padding: 1.25rem 1.4rem; max-height: calc(85vh - 130px); overflow-y: auto;">
      
      <!-- Current Status Indicator Box -->
      <div id="weatherStatusBox" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.85rem 1rem; margin-bottom: 1.15rem; display: flex; align-items: center; justify-content: space-between; gap: 0.75rem;">
        <div style="display: flex; align-items: center; gap: 0.65rem;">
          <span id="weatherStatusIndicatorDot" style="width: 10px; height: 10px; border-radius: 50%; background: #22c55e; display: inline-block; box-shadow: 0 0 8px rgba(34, 197, 94, 0.6);"></span>
          <div>
            <strong id="weatherStatusTitle" style="font-size: 0.84rem; color: #1e293b; display: block;">Standard Office Operations Active</strong>
            <span id="weatherStatusSubtitle" style="font-size: 0.74rem; color: #64748b;">No active PAGASA work suspension declared. Regular physical counter hours apply.</span>
          </div>
        </div>
        <span id="weatherStatusBadge" style="font-size: 0.7rem; font-weight: 800; text-transform: uppercase; padding: 0.25rem 0.65rem; border-radius: 999px; background: rgba(34, 197, 94, 0.12); color: #16a34a; border: 1px solid rgba(34, 197, 94, 0.3);">NORMAL</span>
      </div>

      <!-- Quick Preset Buttons -->
      <div style="margin-bottom: 1.15rem;">
        <label style="display: block; font-size: 0.76rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: #475569; margin-bottom: 0.45rem;">
          Quick Presets (1-Click Fill)
        </label>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(135px, 1fr)); gap: 0.5rem;">
          <button type="button" class="btn-weather-preset" onclick="applyWeatherPreset('signal2')" style="background: #fff7ed; border: 1px solid #fdba74; color: #c2410c; padding: 0.45rem 0.55rem; border-radius: 6px; font-size: 0.73rem; font-weight: 700; cursor: pointer; text-align: left; transition: all 0.2s ease;">
            🌪️ TCWS Signal No. 2 (Auto EO 66)
          </button>
          <button type="button" class="btn-weather-preset" onclick="applyWeatherPreset('signal3')" style="background: #fff1f2; border: 1px solid #fda4af; color: #be123c; padding: 0.45rem 0.55rem; border-radius: 6px; font-size: 0.73rem; font-weight: 700; cursor: pointer; text-align: left; transition: all 0.2s ease;">
            🌀 Signal No. 3 / Severe Typhoon
          </button>
          <button type="button" class="btn-weather-preset" onclick="applyWeatherPreset('heavyrain')" style="background: #f0fdf4; border: 1px solid #86efac; color: #15803d; padding: 0.45rem 0.55rem; border-radius: 6px; font-size: 0.73rem; font-weight: 700; cursor: pointer; text-align: left; transition: all 0.2s ease;">
            🌧️ Heavy Rainfall Red Warning
          </button>
          <button type="button" class="btn-weather-preset" onclick="applyWeatherPreset('malacanang')" style="background: #f8fafc; border: 1px solid #cbd5e1; color: #334155; padding: 0.45rem 0.55rem; border-radius: 6px; font-size: 0.73rem; font-weight: 700; cursor: pointer; text-align: left; transition: all 0.2s ease;">
            🏛️ Malacañang Directive (OP)
          </button>
        </div>
      </div>

      <!-- Suspension Configuration Form -->
      <form id="weatherSuspensionForm" onsubmit="handleSaveWeatherSuspension(event)">
        
        <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 0.85rem; margin-bottom: 0.85rem;">
          <div>
            <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #1e293b; margin-bottom: 0.35rem;">
              Tropical Cyclone / Disturbance Name
            </label>
            <input type="text" id="wsTyphoonName" placeholder="e.g. Typhoon Pepito / Habagat" required style="width: 100%; padding: 0.6rem 0.75rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.86rem; font-family: inherit;">
          </div>
          <div>
            <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #1e293b; margin-bottom: 0.35rem;">
              Warning Level / Category
            </label>
            <select id="wsSignalLevel" onchange="updateWeatherPreview()" style="width: 100%; padding: 0.6rem 0.75rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.86rem; font-family: inherit; background: #ffffff;">
              <option value="TCWS Signal No. 2">TCWS Signal No. 2 (62-88 km/h)</option>
              <option value="TCWS Signal No. 3">TCWS Signal No. 3 (89-117 km/h)</option>
              <option value="TCWS Signal No. 4">TCWS Signal No. 4 (118-184 km/h)</option>
              <option value="TCWS Signal No. 1">TCWS Signal No. 1 (39-61 km/h)</option>
              <option value="Heavy Rainfall Red Warning">Heavy Rainfall Red Warning (Torrential)</option>
              <option value="Heavy Rainfall Orange Warning">Heavy Rainfall Orange Warning (Intense)</option>
              <option value="Southwest Monsoon Flooding">Severe Habagat / Monsoon Inundation</option>
              <option value="Executive Work Suspension">Presidential Work Suspension Order</option>
            </select>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.85rem; margin-bottom: 0.85rem;">
          <div>
            <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #1e293b; margin-bottom: 0.35rem;">
              Effective Start Date
            </label>
            <input type="date" id="wsStartDate" value="<?= date('Y-m-d') ?>" required style="width: 100%; padding: 0.6rem 0.75rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.86rem; font-family: inherit;">
          </div>
          <div>
            <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #1e293b; margin-bottom: 0.35rem;">
              Effective End Date (Inclusive)
            </label>
            <input type="date" id="wsEndDate" value="<?= date('Y-m-d') ?>" required style="width: 100%; padding: 0.6rem 0.75rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.86rem; font-family: inherit;">
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 0.85rem; margin-bottom: 0.85rem;">
          <div>
            <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #1e293b; margin-bottom: 0.35rem;">
              Official Directive / Memo Reference
            </label>
            <input type="text" id="wsMemoRef" placeholder="e.g. Executive Order No. 66 / NDRRMC Memo #14" required style="width: 100%; padding: 0.6rem 0.75rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.86rem; font-family: inherit;">
          </div>
          <div>
            <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #1e293b; margin-bottom: 0.35rem;">
              Advisory Badge Tag
            </label>
            <input type="text" id="wsBadgeText" value="PAGASA SUSPENSION" required style="width: 100%; padding: 0.6rem 0.75rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.86rem; font-family: inherit;">
          </div>
        </div>

        <div style="margin-bottom: 0.85rem;">
          <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #1e293b; margin-bottom: 0.35rem;">
            Public Advisory Announcement Description
          </label>
          <textarea id="wsDescription" rows="3" style="width: 100%; padding: 0.6rem 0.75rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.82rem; font-family: inherit; line-height: 1.45;" placeholder="Enter details to be displayed on the public landing page cards and top announcement banner..."></textarea>
        </div>

        <div style="margin-bottom: 1.15rem;">
          <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #1e293b; margin-bottom: 0.35rem;">
            Resumption Timeline Note
          </label>
          <input type="text" id="wsResumptionNote" placeholder="e.g. Regular on-site counter operations resume Monday at 8:00 AM (PST)." style="width: 100%; padding: 0.6rem 0.75rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.86rem; font-family: inherit;">
        </div>

        <!-- Live Preview Box -->
        <div style="background: #0f172a; border: 1px solid rgba(249, 115, 22, 0.4); border-radius: 10px; padding: 0.85rem 1rem; margin-bottom: 1.25rem;">
          <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
            <span style="font-size: 0.68rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; color: #ea580c;">Live Portal Card Preview</span>
            <span style="font-size: 0.65rem; color: #94a3b8;">How visitors will see it</span>
          </div>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
            <!-- Left Card Preview -->
            <div style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(249, 115, 22, 0.4); border-radius: 8px; padding: 0.6rem; font-size: 0.72rem;">
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                <span style="color: #a7f3d0; font-weight: 800;">OFFICE HOURS</span>
                <span style="background: #ea580c; color: #fff; font-size: 0.62rem; font-weight: 900; padding: 0.15rem 0.45rem; border-radius: 999px;">SUSPENDED</span>
              </div>
              <strong id="previewLeftTitle" style="color: #ffffff; display: block; font-size: 0.75rem;">Inclement Weather Suspension</strong>
              <span id="previewLeftDesc" style="color: rgba(255, 255, 255, 0.85); display: block; font-size: 0.7rem;">Suspended per PAGASA Weather Advisory</span>
            </div>
            <!-- Right Card Preview -->
            <div style="background: rgba(234, 88, 12, 0.14); border: 1px solid rgba(249, 115, 22, 0.5); border-radius: 8px; padding: 0.6rem; font-size: 0.72rem;">
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                <span style="color: #fbbf24; font-weight: 800;">PHILIPPINES</span>
                <span id="previewRightBadge" style="background: rgba(234, 88, 12, 0.3); color: #fed7aa; font-size: 0.62rem; font-weight: 800; padding: 0.15rem 0.45rem; border-radius: 999px;">PAGASA SUSPENSION</span>
              </div>
              <strong id="previewRightTitle" style="color: #fb923c; display: block; font-size: 0.75rem;">Work Suspended: PAGASA Advisory</strong>
              <span style="color: rgba(255, 255, 255, 0.78); font-size: 0.66rem;">Online requests remain 24/7 active</span>
            </div>
          </div>
        </div>

        <!-- Modal Footer Actions -->
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.65rem; pt: 0.5rem; border-top: 1px solid #e2e8f0; padding-top: 1rem;">
          <button type="button" onclick="handleLiftWeatherSuspension()" id="btnLiftSuspension" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 0.55rem 1rem; border-radius: 8px; font-weight: 700; font-size: 0.82rem; cursor: pointer; transition: all 0.2s ease;">
            Lift Suspension (Restore Regular Hours)
          </button>
          <div style="display: flex; align-items: center; gap: 0.5rem;">
            <button type="button" onclick="closeWeatherSuspensionModal()" style="background: #ffffff; color: #64748b; border: 1px solid #cbd5e1; padding: 0.55rem 1rem; border-radius: 8px; font-weight: 700; font-size: 0.82rem; cursor: pointer;">
              Cancel
            </button>
            <button type="submit" style="background: #ea580c; color: #ffffff; border: 1px solid #c2410c; padding: 0.55rem 1.25rem; border-radius: 8px; font-weight: 800; font-size: 0.84rem; cursor: pointer; box-shadow: 0 4px 12px rgba(234, 88, 12, 0.35); display: inline-flex; align-items: center; gap: 0.4rem;">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
                <path d="M19 16.9A5 5 0 0 0 18 7h-1.26a8 8 0 1 0-11.62 9"></path>
                <polyline points="13 11 9 17 15 17 11 23"></polyline>
              </svg>
              <span>Broadcast Weather Suspension</span>
            </button>
          </div>
        </div>

      </form>
    </div>
  </div>
</div>

<script>
// ==========================================================================
// PAGASA WEATHER SUSPENSION ADMIN CONTROLLER
// ==========================================================================

function openWeatherSuspensionModal() {
  const modal = document.getElementById('adminWeatherSuspensionModal');
  if (!modal) return;
  modal.style.display = 'flex';
  document.body.classList.add('admin-modal-open');

  // Load current status from backend
  fetch('ajax/weather_suspension_actions.php?action=get_status')
    .then(res => res.json())
    .then(res => {
      if (res && res.success && res.data) {
        populateWeatherForm(res.data);
      }
    })
    .catch(err => {
      console.warn('Failed loading weather status:', err);
    });
}

function closeWeatherSuspensionModal() {
  const modal = document.getElementById('adminWeatherSuspensionModal');
  if (modal) modal.style.display = 'none';
  document.body.classList.remove('admin-modal-open');
}

function populateWeatherForm(data) {
  const isActive = !!data.active;
  const statusBox = document.getElementById('weatherStatusBox');
  const dot = document.getElementById('weatherStatusIndicatorDot');
  const title = document.getElementById('weatherStatusTitle');
  const subtitle = document.getElementById('weatherStatusSubtitle');
  const badge = document.getElementById('weatherStatusBadge');
  const btnLift = document.getElementById('btnLiftSuspension');

  if (isActive) {
    if (dot) dot.style.background = '#ea580c';
    if (title) title.textContent = 'PAGASA Weather Suspension Currently Active';
    if (subtitle) subtitle.textContent = `Office work suspended: ${data.typhoon_name || 'Severe Weather'} (${data.signal_level || 'PAGASA Alert'}). Broadcasted on public portal.`;
    if (badge) {
      badge.textContent = 'SUSPENDED';
      badge.style.background = 'rgba(234, 88, 12, 0.15)';
      badge.style.color = '#ea580c';
      badge.style.borderColor = 'rgba(234, 88, 12, 0.4)';
    }
    if (btnLift) btnLift.style.display = 'inline-block';
  } else {
    if (dot) dot.style.background = '#22c55e';
    if (title) title.textContent = 'Standard Office Operations Active';
    if (subtitle) subtitle.textContent = 'No active PAGASA work suspension declared. Regular physical counter hours apply.';
    if (badge) {
      badge.textContent = 'NORMAL';
      badge.style.background = 'rgba(34, 197, 94, 0.12)';
      badge.style.color = '#16a34a';
      badge.style.borderColor = 'rgba(34, 197, 94, 0.3)';
    }
    if (btnLift) btnLift.style.display = 'none';
  }

  // Populate inputs
  if (data.typhoon_name) document.getElementById('wsTyphoonName').value = data.typhoon_name;
  if (data.signal_level) document.getElementById('wsSignalLevel').value = data.signal_level;
  if (data.start_date) document.getElementById('wsStartDate').value = data.start_date;
  if (data.end_date) document.getElementById('wsEndDate').value = data.end_date;
  if (data.memo_ref) document.getElementById('wsMemoRef').value = data.memo_ref;
  if (data.badge_text) document.getElementById('wsBadgeText').value = data.badge_text;
  if (data.description) document.getElementById('wsDescription').value = data.description;
  if (data.resumption_note) document.getElementById('wsResumptionNote').value = data.resumption_note;

  updateWeatherPreview();
}

function applyWeatherPreset(presetKey) {
  const today = new Date().toISOString().split('T')[0];
  const nameInput = document.getElementById('wsTyphoonName');
  const levelSelect = document.getElementById('wsSignalLevel');
  const memoInput = document.getElementById('wsMemoRef');
  const descArea = document.getElementById('wsDescription');
  const badgeInput = document.getElementById('wsBadgeText');
  const resumptionInput = document.getElementById('wsResumptionNote');

  if (presetKey === 'signal2') {
    if (nameInput && !nameInput.value) nameInput.value = 'Severe Tropical Storm';
    if (levelSelect) levelSelect.value = 'TCWS Signal No. 2';
    if (memoInput) memoInput.value = 'Executive Order No. 66 (s. 2012) / NDRRMC Memo';
    if (badgeInput) badgeInput.value = 'PAGASA SUSPENSION';
    if (descArea) descArea.value = 'Pursuant to PAGASA Tropical Cyclone Wind Signal (TCWS) No. 2 and Executive Order No. 66, work in government offices at ATI Central Office is officially suspended today. Online reservation portal remains open 24/7.';
    if (resumptionInput) resumptionInput.value = 'Regular counter operations resume on the next business day at 8:00 AM (PST).';
  } else if (presetKey === 'signal3') {
    if (nameInput && !nameInput.value) nameInput.value = 'Typhoon';
    if (levelSelect) levelSelect.value = 'TCWS Signal No. 3';
    if (memoInput) memoInput.value = 'Executive Order No. 66 / PAGASA Severe Bulletin';
    if (badgeInput) badgeInput.value = 'TYPHOON ALERT';
    if (descArea) descArea.value = 'PAGASA has raised TCWS Signal No. 3 over Metro Manila. Physical operations and counter services at ATI Central Office are completely suspended for public safety. Online facility inquiries remain active.';
    if (resumptionInput) resumptionInput.value = 'Operations will resume upon lifting of PAGASA storm signals.';
  } else if (presetKey === 'heavyrain') {
    if (nameInput) nameInput.value = 'Heavy Monsoon Rainfall';
    if (levelSelect) levelSelect.value = 'Heavy Rainfall Red Warning';
    if (memoInput) memoInput.value = 'PAGASA Heavy Rainfall Warning / QC-DRRMO Advisory';
    if (badgeInput) badgeInput.value = 'RAIN SUSPENSION';
    if (descArea) descArea.value = 'Due to torrential rains and widespread flooding threat under PAGASA Red Rainfall Warning, ATI Central Office operations are suspended. Online bookings remain active.';
    if (resumptionInput) resumptionInput.value = 'On-site services resume tomorrow at 8:00 AM (PST).';
  } else if (presetKey === 'malacanang') {
    if (nameInput) nameInput.value = 'Inclement Weather & Flooding';
    if (levelSelect) levelSelect.value = 'Executive Work Suspension';
    if (memoInput) memoInput.value = 'Office of the President / Malacañang MC Directive';
    if (badgeInput) badgeInput.value = 'GOV SUSPENSION';
    if (descArea) descArea.value = 'Pursuant to Memorandum Circular from the Office of the President, work in government offices is suspended due to inclement weather. Digital reservation desk remains operational.';
    if (resumptionInput) resumptionInput.value = 'Regular operations resume on the next official working day.';
  }

  updateWeatherPreview();
}

function updateWeatherPreview() {
  const typhoonName = document.getElementById('wsTyphoonName')?.value || 'Tropical Cyclone';
  const signal = document.getElementById('wsSignalLevel')?.value || 'TCWS Signal No. 2';
  const badgeText = document.getElementById('wsBadgeText')?.value || 'PAGASA SUSPENSION';

  const previewLeftDesc = document.getElementById('previewLeftDesc');
  const previewRightBadge = document.getElementById('previewRightBadge');
  const previewRightTitle = document.getElementById('previewRightTitle');

  if (previewLeftDesc) previewLeftDesc.textContent = `${typhoonName} (${signal})`;
  if (previewRightBadge) previewRightBadge.textContent = badgeText;
  if (previewRightTitle) previewRightTitle.textContent = `Work Suspended: ${typhoonName}`;
}

// Live typing preview
document.getElementById('wsTyphoonName')?.addEventListener('input', updateWeatherPreview);
document.getElementById('wsBadgeText')?.addEventListener('input', updateWeatherPreview);

function handleSaveWeatherSuspension(e) {
  if (e) e.preventDefault();

  const typhoonName = document.getElementById('wsTyphoonName').value.trim();
  const signalLevel = document.getElementById('wsSignalLevel').value;
  const startDate = document.getElementById('wsStartDate').value;
  const endDate = document.getElementById('wsEndDate').value || startDate;
  const memoRef = document.getElementById('wsMemoRef').value.trim();
  const badgeText = document.getElementById('wsBadgeText').value.trim();
  const description = document.getElementById('wsDescription').value.trim();
  const resumptionNote = document.getElementById('wsResumptionNote').value.trim();

  const formData = new FormData();
  formData.append('action', 'save_suspension');
  formData.append('active', '1');
  formData.append('typhoon_name', typhoonName);
  formData.append('signal_level', signalLevel);
  formData.append('start_date', startDate);
  formData.append('end_date', endDate);
  formData.append('memo_ref', memoRef);
  formData.append('badge_text', badgeText);
  formData.append('description', description);
  formData.append('resumption_note', resumptionNote);

  fetch('ajax/weather_suspension_actions.php', {
    method: 'POST',
    body: formData
  })
    .then(res => res.json())
    .then(res => {
      if (res && res.success) {
        if (typeof showAdminToast === 'function') {
          showAdminToast('PAGASA Weather Suspension successfully broadcasted to public portal!', 'confirm');
        } else {
          alert('PAGASA Weather Suspension successfully broadcasted to public portal!');
        }
        closeWeatherSuspensionModal();
        setTimeout(() => window.location.reload(), 1200);
      } else {
        alert(res.message || 'Failed to save weather suspension.');
      }
    })
    .catch(err => {
      alert('Network error while saving weather suspension.');
    });
}

function handleLiftWeatherSuspension() {
  if (!confirm('Are you sure you want to lift the PAGASA weather suspension and restore normal office operations?')) {
    return;
  }

  const formData = new FormData();
  formData.append('action', 'lift_suspension');

  fetch('ajax/weather_suspension_actions.php', {
    method: 'POST',
    body: formData
  })
    .then(res => res.json())
    .then(res => {
      if (res && res.success) {
        if (typeof showAdminToast === 'function') {
          showAdminToast('Weather suspension lifted. Regular office hours restored.', 'confirm');
        } else {
          alert('Weather suspension lifted. Regular office hours restored.');
        }
        closeWeatherSuspensionModal();
        setTimeout(() => window.location.reload(), 1200);
      } else {
        alert(res.message || 'Failed to lift suspension.');
      }
    })
    .catch(err => {
      alert('Network error while lifting weather suspension.');
    });
}
</script>

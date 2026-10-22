<!-- ==========================================================================
     MODAL: OFFICIAL ATI RESERVATION FORM TEMPLATES MANAGER
     ========================================================================== -->
<div class="admin-modal-overlay" id="manageFormTemplatesModal" role="dialog" aria-modal="true" aria-labelledby="formTemplatesModalTitle" style="display: none; z-index: 1050;">
  <div class="admin-modal-card" style="max-width: 900px; width: 95%;">
    <!-- Modal Header -->
    <div class="admin-modal-header" style="background: linear-gradient(135deg, #174d2f, #246a42); color: #ffffff; padding: 1.25rem 1.6rem; border-top-left-radius: 16px; border-top-right-radius: 16px;">
      <div class="modal-title-wrap">
        <div style="display: inline-flex; align-items: center; gap: 7px; background: rgba(255,255,255,0.16); padding: 0.22rem 0.65rem; border-radius: 9999px; margin-bottom: 0.35rem; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #bbf7d0;">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            <polyline points="14 2 14 8 20 8"></polyline>
          </svg>
          <span>QMS / ISO Document Control</span>
        </div>
        <h4 id="formTemplatesModalTitle" style="color: #ffffff; font-size: 1.22rem; font-weight: 800; margin: 0 0 0.25rem; font-family: 'Playfair Display', Georgia, serif;">
          Official Reservation Form Templates
        </h4>
        <p style="margin: 0; font-size: 0.8rem; color: #d1fae5; line-height: 1.4;">
          Manage the downloadable reservation forms available to applicants on <strong>booking.php</strong> and <strong>dormitory_booking.php</strong>. When updated, users automatically download the new official template.
        </p>
      </div>
      <button type="button" class="admin-modal-close" onclick="closeManageFormTemplatesModal()" aria-label="Close dialog" style="color: #ffffff; background: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.25); border-radius: 50%; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>
    </div>

    <!-- Modal Body -->
    <div class="admin-modal-body" style="padding: 1.5rem; max-height: 76vh; overflow-y: auto; background: #f8fafc;">
      
      <!-- Notice Banner -->
      <div style="background: #ecfdf5; border: 1.5px solid #a7f3d0; border-radius: 12px; padding: 0.85rem 1.15rem; margin-bottom: 1.35rem; display: flex; align-items: flex-start; gap: 0.85rem;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.2" style="flex-shrink: 0; margin-top: 2px;">
          <circle cx="12" cy="12" r="10"></circle>
          <line x1="12" y1="16" x2="12" y2="12"></line>
          <line x1="12" y1="8" x2="12.01" y2="8"></line>
        </svg>
        <div style="font-size: 0.82rem; color: #065f46; line-height: 1.45;">
          <strong>Annual Revision Notice:</strong> Upload official PDF or Word documents (.pdf, .docx, .doc) certified by the Property & Facilities Management Unit. If a custom form is not uploaded, the system will seamlessly fallback to generating the standard printable template.
        </div>
      </div>

      <!-- Two Template Cards Grid -->
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(380px, 1fr)); gap: 1.25rem;">

        <!-- ================= CARD 1: FACILITY RESERVATION FORM ================= -->
        <div class="template-manager-card" id="cardFacilityTemplate" style="background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 14px; padding: 1.25rem; box-shadow: 0 4px 14px rgba(0,0,0,0.03); display: flex; flex-direction: column; justify-content: space-between;">
          <div>
            <!-- Card Header -->
            <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 0.75rem; margin-bottom: 0.85rem;">
              <div style="display: flex; align-items: center; gap: 0.65rem;">
                <div style="width: 42px; height: 42px; border-radius: 10px; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                  </svg>
                </div>
                <div>
                  <h5 style="margin: 0; font-size: 1rem; font-weight: 800; color: #0f172a;">Training Facility Form</h5>
                  <span style="font-size: 0.72rem; color: #64748b;">Linked to: <code style="background: #f1f5f9; padding: 2px 5px; border-radius: 4px; color: #0369a1;">booking.php</code></span>
                </div>
              </div>
              <span id="badgeFacilityStatus" style="font-size: 0.72rem; font-weight: 700; padding: 0.25rem 0.65rem; border-radius: 9999px; background: #f1f5f9; color: #475569;">
                Checking...
              </span>
            </div>

            <!-- Current File Status Info Box -->
            <div id="infoFacilityBox" style="background: #f8fafc; border: 1.2px solid #e2e8f0; border-radius: 10px; padding: 0.85rem 1rem; margin-bottom: 1rem;">
              <div style="display: flex; align-items: center; gap: 0.65rem;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.8">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                  <polyline points="14 2 14 8 20 8"></polyline>
                </svg>
                <div style="flex: 1; min-width: 0;">
                  <div id="txtFacilityFileName" style="font-size: 0.86rem; font-weight: 700; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    Loading template info...
                  </div>
                  <div id="txtFacilityMeta" style="font-size: 0.74rem; color: #64748b; margin-top: 2px;">
                    -
                  </div>
                </div>
              </div>

              <!-- Quick File Actions -->
              <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.75rem; padding-top: 0.65rem; border-top: 1px dashed #e2e8f0; flex-wrap: wrap;">
                <a id="btnFacilityDownload" href="download_form.php" target="_blank" style="font-size: 0.76rem; font-weight: 700; color: #15803d; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; background: #dcfce7; padding: 0.35rem 0.7rem; border-radius: 6px;">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                  <span>Download / Preview</span>
                </a>
                <a href="download_form.php?mode=html" target="_blank" style="font-size: 0.76rem; color: #475569; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; background: #e2e8f0; padding: 0.35rem 0.7rem; border-radius: 6px;">
                  <span>Printable Web View</span>
                </a>
                <button type="button" id="btnFacilityReset" onclick="confirmResetTemplate('facility')" style="display: none; font-size: 0.76rem; color: #b91c1c; background: #fee2e2; border: none; padding: 0.35rem 0.7rem; border-radius: 6px; cursor: pointer; font-weight: 600;">
                  <span>Reset to Default</span>
                </button>
              </div>
            </div>

            <!-- Upload Area Form -->
            <form id="formFacilityUpload" onsubmit="handleUploadTemplate(event, 'facility')" style="margin-top: 0.5rem;">
              <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">
                Upload New Official Form (.pdf, .docx, .doc)
              </label>
              
              <div class="template-drop-zone" id="dropZoneFacility" onclick="document.getElementById('inputFacilityFile').click()" style="border: 2px dashed #93c5fd; background: #f0f9ff; border-radius: 10px; padding: 1.15rem 1rem; text-align: center; cursor: pointer; transition: all 0.2s;">
                <input type="file" id="inputFacilityFile" accept=".pdf,.doc,.docx" style="display: none;" onchange="onFileSelected(this, 'facility')">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="2" style="margin-bottom: 0.35rem;">
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                  <polyline points="17 8 12 3 7 8"></polyline>
                  <line x1="12" y1="3" x2="12" y2="15"></line>
                </svg>
                <div id="dropTxtFacility" style="font-size: 0.8rem; font-weight: 600; color: #0369a1;">
                  Click or drag new official file here
                </div>
                <div style="font-size: 0.7rem; color: #64748b; margin-top: 2px;">
                  Max file size: 15MB • PDF, DOCX, DOC
                </div>
              </div>

              <!-- Submit button (hidden until file is chosen) -->
              <div id="submitWrapFacility" style="display: none; margin-top: 0.75rem;">
                <button type="submit" id="btnSubmitFacility" class="btn-system-primary" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 0.45rem; padding: 0.65rem; font-size: 0.84rem; background: #0284c7; border-color: #0284c7;">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><polyline points="20 6 9 17 4 12"></polyline></svg>
                  <span>Upload &amp; Apply as Official Facility Form</span>
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- ================= CARD 2: DORMITORY RESERVATION FORM ================= -->
        <div class="template-manager-card" id="cardDormitoryTemplate" style="background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 14px; padding: 1.25rem; box-shadow: 0 4px 14px rgba(0,0,0,0.03); display: flex; flex-direction: column; justify-content: space-between;">
          <div>
            <!-- Card Header -->
            <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 0.75rem; margin-bottom: 0.85rem;">
              <div style="display: flex; align-items: center; gap: 0.65rem;">
                <div style="width: 42px; height: 42px; border-radius: 10px; background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M2 4v16"></path>
                    <path d="M2 8h18a2 2 0 0 1 2 2v10"></path>
                    <path d="M2 17h20"></path>
                    <path d="M6 8v9"></path>
                  </svg>
                </div>
                <div>
                  <h5 style="margin: 0; font-size: 1rem; font-weight: 800; color: #0f172a;">Dormitory Form (ATI-QF/DTS-22)</h5>
                  <span style="font-size: 0.72rem; color: #64748b;">Linked to: <code style="background: #f1f5f9; padding: 2px 5px; border-radius: 4px; color: #15803d;">dormitory_booking.php</code></span>
                </div>
              </div>
              <span id="badgeDormitoryStatus" style="font-size: 0.72rem; font-weight: 700; padding: 0.25rem 0.65rem; border-radius: 9999px; background: #f1f5f9; color: #475569;">
                Checking...
              </span>
            </div>

            <!-- Current File Status Info Box -->
            <div id="infoDormitoryBox" style="background: #f8fafc; border: 1.2px solid #e2e8f0; border-radius: 10px; padding: 0.85rem 1rem; margin-bottom: 1rem;">
              <div style="display: flex; align-items: center; gap: 0.65rem;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="1.8">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                  <polyline points="14 2 14 8 20 8"></polyline>
                </svg>
                <div style="flex: 1; min-width: 0;">
                  <div id="txtDormitoryFileName" style="font-size: 0.86rem; font-weight: 700; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    Loading template info...
                  </div>
                  <div id="txtDormitoryMeta" style="font-size: 0.74rem; color: #64748b; margin-top: 2px;">
                    -
                  </div>
                </div>
              </div>

              <!-- Quick File Actions -->
              <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.75rem; padding-top: 0.65rem; border-top: 1px dashed #e2e8f0; flex-wrap: wrap;">
                <a id="btnDormitoryDownload" href="download_dormitory_form.php" target="_blank" style="font-size: 0.76rem; font-weight: 700; color: #15803d; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; background: #dcfce7; padding: 0.35rem 0.7rem; border-radius: 6px;">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                  <span>Download / Preview</span>
                </a>
                <a href="download_dormitory_form.php?mode=html" target="_blank" style="font-size: 0.76rem; color: #475569; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; background: #e2e8f0; padding: 0.35rem 0.7rem; border-radius: 6px;">
                  <span>Printable Web View</span>
                </a>
                <button type="button" id="btnDormitoryReset" onclick="confirmResetTemplate('dormitory')" style="display: none; font-size: 0.76rem; color: #b91c1c; background: #fee2e2; border: none; padding: 0.35rem 0.7rem; border-radius: 6px; cursor: pointer; font-weight: 600;">
                  <span>Reset to Default</span>
                </button>
              </div>
            </div>

            <!-- Upload Area Form -->
            <form id="formDormitoryUpload" onsubmit="handleUploadTemplate(event, 'dormitory')" style="margin-top: 0.5rem;">
              <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">
                Upload New Official Form (.pdf, .docx, .doc)
              </label>
              
              <div class="template-drop-zone" id="dropZoneDormitory" onclick="document.getElementById('inputDormitoryFile').click()" style="border: 2px dashed #86efac; background: #f0fdf4; border-radius: 10px; padding: 1.15rem 1rem; text-align: center; cursor: pointer; transition: all 0.2s;">
                <input type="file" id="inputDormitoryFile" accept=".pdf,.doc,.docx" style="display: none;" onchange="onFileSelected(this, 'dormitory')">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" style="margin-bottom: 0.35rem;">
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                  <polyline points="17 8 12 3 7 8"></polyline>
                  <line x1="12" y1="3" x2="12" y2="15"></line>
                </svg>
                <div id="dropTxtDormitory" style="font-size: 0.8rem; font-weight: 600; color: #15803d;">
                  Click or drag new official file here
                </div>
                <div style="font-size: 0.7rem; color: #64748b; margin-top: 2px;">
                  Max file size: 15MB • PDF, DOCX, DOC
                </div>
              </div>

              <!-- Submit button (hidden until file is chosen) -->
              <div id="submitWrapDormitory" style="display: none; margin-top: 0.75rem;">
                <button type="submit" id="btnSubmitDormitory" class="btn-system-primary" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 0.45rem; padding: 0.65rem; font-size: 0.84rem; background: #16a34a; border-color: #16a34a;">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><polyline points="20 6 9 17 4 12"></polyline></svg>
                  <span>Upload &amp; Apply as Official Dormitory Form</span>
                </button>
              </div>
            </form>
          </div>
        </div>

      </div>

    </div>

    <!-- Modal Footer -->
    <div class="admin-modal-footer" style="padding: 1rem 1.6rem; background: #ffffff; border-top: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
      <div style="font-size: 0.78rem; color: #64748b;">
        Changes take effect immediately on public reservation portals.
      </div>
      <button type="button" class="btn-system-secondary" onclick="closeManageFormTemplatesModal()" style="padding: 0.55rem 1.25rem;">
        Done / Close
      </button>
    </div>
  </div>
</div>

{{-- resources/views/components/admin/modal-archive-submission.blade --}}
@props(['portalCode'])

<div id="modalArchiveSubmission" 
     class="sub-pdf-overlay" 
     data-portal="{{ $portalCode }}"
     role="dialog"
     aria-modal="true"
     aria-labelledby="subModalTitle"
     onclick="if(event.target === this) window.closeArchiveSubmission()">
    
    <div class="sub-pdf-card">
        {{-- HEADER MODAL --}}
        <div class="sub-pdf-header">
            <div class="sub-pdf-icon-box"><i class="bi bi-file-earmark-check"></i></div>
            <h2 id="subModalTitle">DOKUMEN PENGAJUAN DIGITAL</h2>
            <div class="sub-pdf-header-actions">
                <button onclick="window.printSubmission()" class="sub-pdf-header-btn" aria-label="Cetak berkas" title="Cetak Berkas">
                    <i class="bi bi-printer"></i>
                </button>
                <button onclick="window.closeArchiveSubmission()" class="sub-pdf-close-x" aria-label="Tutup">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>

        {{-- BODY MODAL --}}
        <div class="sub-pdf-body custom-scrollbar-sub">
            {{-- SKELETON LOADER --}}
            <div id="sub-skeleton" class="sub-pdf-skeleton">
                <div class="sub-sk-row sub-sk-kop"></div>
                <div class="sub-sk-row sub-sk-line" style="width:40%"></div>
                <div class="sub-sk-row sub-sk-line" style="width:70%; height:18px; margin-top:14px"></div>
                <div class="sub-sk-grid">
                    <div class="sub-sk-row sub-sk-block-sm"></div>
                    <div class="sub-sk-row sub-sk-block-sm"></div>
                </div>
            </div>

            {{-- AREA KERTAS DOKUMEN --}}
            <div id="subPrintableArea" class="sub-pdf-paper" style="display:none;">
                <div class="sub-pdf-watermark" aria-hidden="true">
                    <svg viewBox="0 0 200 200" width="280" height="280">
                        <circle cx="100" cy="100" r="92" fill="none" stroke="#0A1E33" stroke-width="2"/>
                        <circle cx="100" cy="100" r="78" fill="none" stroke="#0A1E33" stroke-width="1"/>
                        <text x="100" y="60" text-anchor="middle" font-size="9" font-weight="800" letter-spacing="2" fill="#0A1E33">PEMERINTAH KOTA</text>
                        <text x="100" y="145" text-anchor="middle" font-size="9" font-weight="800" letter-spacing="2" fill="#0A1E33">SUKABUMI</text>
                        <text x="100" y="105" text-anchor="middle" font-size="14" font-weight="900" fill="#0A1E33">SOA</text>
                    </svg>
                </div>

                <div class="sub-pdf-ribbon"><span id="sub-val-status-ribbon">Memuat...</span></div>

                <div class="sub-pdf-kop">
                    <img src="{{ asset('image/logo-sukabumi.png') }}" alt="Logo" onerror="this.style.display='none'">
                    <div class="sub-pdf-kop-text">
                        <h4 id="sub-val-instansi-name">PEMERINTAH KOTA SUKABUMI</h4>
                        <h5>Sukabumi One Access &mdash; Layanan Mandiri Terpadu</h5>
                    </div>
                    <div class="sub-pdf-ticket-badge" id="sub-val-nomor">#SOA...</div>
                </div>

                <div class="sub-pdf-divider-double">
                    <div class="line-thick"></div>
                    <div class="line-thin"></div>
                </div>

                <div class="sub-pdf-content">
                    <h3 class="sub-pdf-doc-title" id="sub-val-category-name">Ringkasan Pengajuan</h3>
                    <div class="sub-pdf-grid">
                        <div class="sub-pdf-item">
                            <label>Jenis Layanan</label>
                            <p id="sub-val-layanan-name">Memuat...</p>
                        </div>
                        <div class="sub-pdf-item">
                            <label>Waktu Pengajuan</label>
                            <p class="mono" id="sub-val-time">Memuat...</p>
                        </div>
                    </div>

                    <div class="sub-data-section">
                        <label class="section-label">Informasi Pemohon &amp; Data Input</label>
                        <div class="sub-data-table" id="sub-val-parameters"></div>
                    </div>

                    <div class="sub-data-section" id="sub-document-section">
                        <label class="section-label">Lampiran Berkas Persyaratan</label>
                        <div class="sub-doc-grid" id="sub-val-documents"></div>
                    </div>

                    <div class="sub-pdf-signature-row">
                        <div class="sub-pdf-verify-block">
                            <div id="sub-qrcode" class="sub-pdf-qr-mini"></div>
                            <div class="sub-pdf-verify-text">
                                <p>Hash Otoritas</p>
                                <strong id="sub-verify-code" class="mono">------</strong>
                            </div>
                        </div>
                        <div class="sub-pdf-stamp-area">
                            <img src="{{ asset('image/stempel-digital.png') }}" class="sub-pdf-stamp-img" onerror="this.style.display='none'">
                            <p>Validasi Berkas Digital</p>
                            <strong id="sub-footer-date">{{ date('d/m/Y') }}</strong>
                        </div>
                    </div>

                    <p class="sub-pdf-legal-note">
                        Dokumen ini dihasilkan secara elektronik oleh sistem Sukabumi One Access dan sah tanpa tanda tangan basah.
                        Keabsahan dapat diperiksa dengan memindai kode QR di atas.
                    </p>
                </div>
            </div>
        </div>

        <div class="sub-pdf-footer">
            <button type="button" class="sub-btn-print" onclick="window.printSubmission()">
                <i class="bi bi-printer"></i> Cetak Berkas
            </button>
            <button type="button" class="sub-btn-close" onclick="window.closeArchiveSubmission()">
                Selesai Meninjau
            </button>
        </div>
    </div>
</div>

@once
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Source+Serif+4:wght@600;700;900&family=Plus+Jakarta+Sans:wght@500;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
<style>
    :root { --sub-navy: #0A1E33; --sub-gold: #E8A33D; --sub-paper: #FFFDF9; }
    .sub-pdf-overlay { position: fixed; inset: 0; background: rgba(10, 30, 51, 0.92); backdrop-filter: blur(6px); display: none; align-items: center; justify-content: center; z-index: 10000; padding: 20px; opacity: 0; font-family: 'Plus Jakarta Sans', sans-serif; }
    .sub-pdf-overlay.is-active { display: flex; animation: subFade 0.3s ease forwards; }
    .sub-pdf-overlay.is-active .sub-pdf-card { animation: subSpring 0.45s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
    .sub-pdf-overlay.is-closing { animation: subFadeOut 0.25s ease forwards; }
    .sub-pdf-card { background: #fff; width: 100%; max-width: 500px; border-radius: 22px; overflow: hidden; box-shadow: 0 30px 60px -12px rgba(0,0,0,0.55); display: flex; flex-direction: column; max-height: 92vh; }
    .sub-pdf-header { background: linear-gradient(135deg, #0A1E33, #123457); padding: 14px 16px 14px 20px; border-bottom: 3px solid var(--sub-gold); display: flex; align-items: center; gap: 12px; position: relative; }
    .sub-pdf-header h2 { color: #fff; font-size: 11px; font-weight: 800; margin: 0; letter-spacing: 2.5px; flex: 1; }
    .sub-pdf-icon-box { color: var(--sub-gold); font-size: 18px; }
    .sub-pdf-header-actions { display: flex; align-items: center; gap: 4px; }
    .sub-pdf-header-btn, .sub-pdf-close-x { color: rgba(255,255,255,0.55); background: rgba(255,255,255,0.06); border: none; cursor: pointer; width: 28px; height: 28px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 12px; transition: 0.15s; }
    .sub-pdf-header-btn:hover, .sub-pdf-close-x:hover { color: #fff; background: rgba(255,255,255,0.15); }
    .sub-pdf-body { padding: 16px; background: #EEF2F7; overflow-y: auto; }
    .custom-scrollbar-sub::-webkit-scrollbar { display: none; }
    .sub-pdf-paper { background: var(--sub-paper); padding: 26px 24px; border-radius: 10px; border: 1px solid #e7e2d6; min-height: 500px; position: relative; overflow: hidden; box-shadow: inset 0 0 0 1px rgba(10,30,51,0.02); }
    .sub-pdf-watermark { position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); opacity: 0.045; pointer-events: none; z-index: 0; }
    .sub-pdf-ribbon { position: absolute; top: 14px; right: -38px; transform: rotate(40deg); background: linear-gradient(135deg, var(--sub-gold), #d68f28); color: #3a2404; font-size: 7.5px; font-weight: 900; letter-spacing: 1px; text-transform: uppercase; padding: 4px 46px; box-shadow: 0 2px 6px rgba(0,0,0,0.2); z-index: 2; }
    .sub-pdf-ribbon span { display: flex; align-items: center; justify-content: center; gap: 3px; }
    .sub-pdf-kop { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; position: relative; z-index: 1; }
    .sub-pdf-kop img { width: 34px; }
    .sub-pdf-kop-text h4 { font-family: 'Source Serif 4', serif; font-size: 10px; font-weight: 900; margin: 0; color: #0A1E33; letter-spacing: 0.4px; }
    .sub-pdf-kop-text h5 { font-size: 7.5px; font-weight: 600; margin: 2px 0 0; color: #64748b; }
    .sub-pdf-ticket-badge { margin-left: auto; background: #123457; color: var(--sub-gold); padding: 4px 9px; border-radius: 6px; font-size: 9px; font-weight: 800; font-family: 'JetBrains Mono', monospace; }
    .sub-pdf-divider-double { margin: 0 0 20px; position: relative; z-index: 1; }
    .sub-pdf-divider-double .line-thick { height: 2.5px; background: #0A1E33; }
    .sub-pdf-divider-double .line-thin { height: 1px; background: #0A1E33; margin-top: 2px; opacity: 0.45; }
    .sub-pdf-content { position: relative; z-index: 1; }
    .sub-pdf-doc-title { font-family: 'Source Serif 4', serif; text-align: center; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 22px; color: #0A1E33; position: relative; }
    .sub-pdf-doc-title::after { content: ''; display: block; width: 36px; height: 2px; background: var(--sub-gold); margin: 8px auto 0; }
    .sub-pdf-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 6px; }
    .sub-pdf-item { margin-bottom: 16px; }
    .sub-pdf-item label { display: block; font-size: 7.5px; font-weight: 800; color: #94a3b8; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.8px; }
    .sub-pdf-item p { font-size: 10px; font-weight: 700; color: #1e293b; margin: 0; }
    .mono { font-family: 'JetBrains Mono', monospace !important; }
    .section-label { display: block; font-size: 8.5px; font-weight: 900; color: var(--sub-gold); text-transform: uppercase; letter-spacing: 0.8px; border-bottom: 1px solid #eef2f7; padding-bottom: 6px; margin: 18px 0 12px; }
    .sub-data-table { display: flex; flex-direction: column; gap: 8px; }
    .data-row { display: flex; justify-content: space-between; gap: 12px; padding-bottom: 6px; border-bottom: 1px dashed #f0ece0; }
    .data-row span:first-child { font-size: 9.5px; font-weight: 600; color: #94a3b8; }
    .data-row span:last-child { font-size: 9.5px; font-weight: 800; color: #1e293b; text-align: right; }
    .sub-doc-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; }
    .doc-preview-item { background: #FAF9F4; border: 1px solid #f0ece0; border-radius: 10px; padding: 8px; display: flex; align-items: center; gap: 10px; text-decoration: none; }
    .doc-preview-icon { width: 30px; height: 30px; border-radius: 8px; background: #fff; display: flex; align-items: center; justify-content: center; color: #123457; border: 1px solid #e2e8f0; flex-shrink: 0; }
    .doc-preview-info p { margin: 0; font-size: 9px; font-weight: 800; color: #1e293b; line-height: 1.2; }
    .doc-preview-info span { font-size: 7px; color: #94a3b8; font-weight: 700; }
    .sub-pdf-signature-row { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 26px; padding-top: 16px; border-top: 1px dashed #d8d2c2; }
    .sub-pdf-verify-block { display: flex; align-items: center; gap: 10px; }
    .sub-pdf-qr-mini { width: 48px; height: 48px; border: 1px solid #e2e8f0; border-radius: 6px; display: flex; align-items: center; justify-content: center; background: #fff; padding: 3px; }
    .sub-pdf-qr-mini img, .sub-pdf-qr-mini canvas { width: 100% !important; height: 100% !important; }
    .sub-pdf-verify-text p { margin: 0; font-size: 7px; font-weight: 800; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.5px; }
    .sub-pdf-verify-text strong { font-size: 11px; color: #0A1E33; letter-spacing: 1px; }
    .sub-pdf-stamp-area { text-align: center; position: relative; }
    .sub-pdf-stamp-area p { font-size: 8px; color: #64748b; margin: 0; font-weight: 600; }
    .sub-pdf-stamp-area strong { font-size: 10px; color: #0A1E33; font-family: 'JetBrains Mono', monospace; }
    .sub-pdf-stamp-img { position: absolute; width: 62px; top: -34px; left: 50%; transform: translateX(-50%) rotate(-15deg); opacity: 0.3; }
    .sub-pdf-legal-note { margin-top: 18px; font-size: 7.5px; line-height: 1.6; color: #a1a9b5; text-align: center; font-style: italic; }
    .sub-pdf-footer { padding: 14px 16px; background: #fff; border-top: 1px solid #f1f5f9; display: flex; gap: 10px; }
    .sub-btn-print, .sub-btn-close { border: none; padding: 11px; border-radius: 12px; font-size: 10px; font-weight: 800; cursor: pointer; transition: 0.2s; text-transform: uppercase; }
    .sub-btn-print { flex: 1; background: #f1f5f9; color: #123457; display: flex; align-items: center; justify-content: center; gap: 6px; }
    .sub-btn-print:hover { background: #e2e8f0; }
    .sub-btn-close { flex: 2; background: #123457; color: #fff; }
    .sub-btn-close:hover { background: #0A1E33; }
    @keyframes subFade { from { opacity: 0; } to { opacity: 1; } }
    @keyframes subSpring { 0% { opacity: 0; transform: scale(0.92) translateY(20px); } 100% { opacity: 1; transform: scale(1) translateY(0); } }
    @keyframes subFadeOut { from { opacity: 1; } to { opacity: 0; } }
    .sub-pdf-skeleton { background: var(--sub-paper); border-radius: 10px; border: 1px solid #e7e2d6; padding: 26px 24px; min-height: 500px; }
    .sub-sk-row { border-radius: 6px; background: linear-gradient(90deg, #edeae0 25%, #f5f3ec 37%, #edeae0 63%); background-size: 400% 100%; animation: subShimmer 1.4s ease infinite; }
    .sub-sk-kop { height: 34px; width: 60%; margin-bottom: 22px; }
    .sub-sk-line { height: 12px; margin-bottom: 10px; }
    .sub-sk-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-top: 14px; }
    .sub-sk-block-sm { height: 110px; }
    @keyframes subShimmer { 0%{ background-position: 100% 0; } 100%{ background-position: 0 0; } }
    @media print {
        html, body, #subPrintableArea, #subPrintableArea * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; color-adjust: exact !important; }
        body > *:not(#print-mount) { display: none !important; }
        #print-mount { display: block !important; width: 100% !important; margin: 0 !important; padding: 0 !important; }
        #print-mount, #print-mount * { visibility: visible !important; }
        #print-mount .sub-pdf-paper { position: static !important; display: block !important; width: 100% !important; max-width: none !important; min-height: 0 !important; margin: 0 !important; border: none !important; border-radius: 0 !important; box-shadow: none !important; }
        .doc-preview-item, .sub-pdf-signature-row { break-inside: avoid; page-break-inside: avoid; }
        @page { size: A4; margin: 15mm 14mm; }
    }
</style>

<script>
    if (typeof window.archiveSubmissionModalInitialized === 'undefined') {
        window.archiveSubmissionModalInitialized = true;

        window.openArchiveSubmission = function(id) {
            const modal = document.getElementById('modalArchiveSubmission');
            if (!modal) return;
            
            const portal = modal.getAttribute('data-portal');
            const baseUrl = window.location.origin;
            const skeleton = document.getElementById('sub-skeleton');
            const paper = document.getElementById('subPrintableArea');

            // Reset tampilan ke Loading
            skeleton.style.display = 'block'; 
            paper.style.display = 'none';
            modal.classList.add('is-active'); 
            modal.classList.remove('is-closing');
            document.body.style.overflow = 'hidden';

            // Ambil data JSON
            fetch(`${baseUrl}/${portal}/submissions/${id}/json-detail`)
                .then(res => {
                    if (!res.ok) throw new Error("Gagal mengambil data");
                    return res.json();
                })
                .then(data => {
                    // --- MAPPING DATA DASAR (DENGAN PROTEKSI NULL) ---
                    document.getElementById('sub-val-nomor').textContent = '#' + (data.nomor_pengajuan || '---');
                    document.getElementById('sub-val-status-ribbon').textContent = data.status || 'diajukan';
                    document.getElementById('sub-val-category-name').textContent = data.category?.nama || 'Kategori';
                    document.getElementById('sub-val-layanan-name').textContent = data.layanan?.nama || 'Layanan';
                    document.getElementById('sub-val-instansi-name').textContent = data.layanan?.instansi?.nama?.toUpperCase() || 'PEMERINTAH KOTA SUKABUMI';
                    
                    if(data.created_at) {
                        document.getElementById('sub-val-time').textContent = new Date(data.created_at).toLocaleString('id-ID', {day:'numeric', month:'long', year:'numeric', hour:'2-digit', minute:'2-digit'});
                    }

                    // --- MAPPING PARAMETER (LOOPING) ---
                    const paramBox = document.getElementById('sub-val-parameters');
                    paramBox.innerHTML = '';
                    if(data.data && Array.isArray(data.data)) {
                        data.data.forEach(p => {
                            const label = p.parameter.split('_').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
                            paramBox.innerHTML += `<div class="data-row"><span>${label}</span><span>${p.value || '-'}</span></div>`;
                        });
                    }

                    // --- MAPPING DOKUMEN ---
                    const docBox = document.getElementById('sub-val-documents');
                    const docSection = document.getElementById('sub-document-section');
                    docBox.innerHTML = '';
                    if(data.dokumen && data.dokumen.length > 0) {
                        docSection.style.display = 'block';
                        data.dokumen.forEach(d => {
                            const isImg = d.mime_type?.includes('image');
                            docBox.innerHTML += `
                                <a href="${baseUrl}/storage/${d.file_path}" target="_blank" class="doc-preview-item">
                                    <div class="doc-preview-icon"><i class="bi ${isImg ? 'bi-image' : 'bi-file-earmark-pdf'}"></i></div>
                                    <div class="doc-preview-info">
                                        <p class="truncate" style="max-width:120px;">${d.nama_dokumen}</p>
                                        <span>${(d.file_size / 1024 / 1024).toFixed(2)} MB</span>
                                    </div>
                                </a>`;
                        });
                    } else { 
                        docSection.style.display = 'none'; 
                    }

                    // --- QR CODE & VERIFY HASH ---
                    const verifyCode = `SOA-SUB-${data.id}-${Math.random().toString(36).substring(2, 5).toUpperCase()}`;
                    document.getElementById('sub-verify-code').textContent = verifyCode;
                    const qrBox = document.getElementById('sub-qrcode');
                    qrBox.innerHTML = '';
                    if(window.QRCode) { 
                        new QRCode(qrBox, { text: verifyCode, width: 44, height: 44, colorDark: "#0A1E33", colorLight: "#ffffff", correctLevel: QRCode.CorrectLevel.M }); 
                    }

                    // Selesai Loading, Tampilkan Kertas
                    skeleton.style.display = 'none'; 
                    paper.style.display = 'block';
                })
                .catch(err => { 
                    console.error("Detail Error:", err);
                    alert("Gagal memuat data detail pengajuan.");
                    // JANGAN panggil closeDetailSubmission() di sini agar modal tidak menutup paksa saat error
                });
        };

        window.closeArchiveSubmission = function() {
            const modal = document.getElementById('modalArchiveSubmission');
            if (modal) {
                modal.classList.add('is-closing');
                setTimeout(() => { 
                    modal.classList.remove('is-active', 'is-closing'); 
                    document.body.style.overflow = 'auto'; 
                    // Opsional: Reload jika status berubah
                    // location.reload(); 
                }, 300);
            }
        };

        window.printSubmission = function() {
            const source = document.getElementById('subPrintableArea');
            if (!source) { window.print(); return; }

            // Bersihkan sisa mount lama kalau ada (jaga-jaga)
            const oldMount = document.getElementById('print-mount');
            if (oldMount) oldMount.remove();

            // Salin dokumen ke elemen baru langsung sebagai anak <body>,
            // supaya print CSS tidak bergantung pada struktur DOM
            // dashboard/layout yang membungkusnya
            const mount = document.createElement('div');
            mount.id = 'print-mount';
            const clone = source.cloneNode(true);
            clone.style.display = 'block'; // pastikan clone tidak ikut display:none dari style inline aslinya
            mount.appendChild(clone);
            document.body.appendChild(mount);

            const cleanup = () => {
                mount.remove();
                window.removeEventListener('afterprint', cleanup);
            };
            window.addEventListener('afterprint', cleanup);

            window.print();

            // Fallback untuk browser yang tidak fire 'afterprint'
            setTimeout(cleanup, 3000);
        };

        document.addEventListener('keydown', (e) => {
            const modal = document.getElementById('modalArchiveSubmission');
            if (e.key === 'Escape' && modal && modal.classList.contains('is-active')) {
                window.closeArchiveSubmission();
            }
        });
    }
</script>
@endonce
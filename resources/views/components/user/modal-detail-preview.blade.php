{{-- resources/views/components/user/modal-detail-preview.blade.php --}}
@props([])
<div id="modalDetailPdfUser" 
     class="pdf-overlay" 
     role="dialog" 
     aria-modal="true" 
     aria-labelledby="pdfModalTitle" 
     onclick="if(event.target === this) window.closePreviewModal()">
    
    <div class="pdf-card">
        {{-- HEADER MODAL --}}
        <div class="pdf-header">
            <div class="pdf-icon-box"><i class="bi bi-file-earmark-pdf"></i></div>
            <h2 id="pdfModalTitle">BERITA ACARA DIGITAL</h2>
            <div class="pdf-header-actions">
                <button onclick="window.printDocument()" class="pdf-header-btn" aria-label="Cetak dokumen" title="Cetak">
                    <i class="bi bi-printer"></i>
                </button>
                <button onclick="window.closePreviewModal()" class="pdf-close-x" aria-label="Tutup">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>

        {{-- BODY MODAL (Scrollable) --}}
        <div class="pdf-body custom-scrollbar-pdf">

            {{-- SKELETON LOADER --}}
            <div id="pdf-skeleton" class="pdf-skeleton">
                <div class="sk-row sk-kop"></div>
                <div class="sk-row sk-line" style="width:40%"></div>
                <div class="sk-row sk-line" style="width:70%; height:18px; margin-top:14px"></div>
                <div class="sk-row sk-block" style="margin-top:10px"></div>
                <div class="sk-grid">
                    <div class="sk-row sk-block-sm"></div>
                    <div class="sk-row sk-block-sm"></div>
                </div>
            </div>

            <div id="printableArea" class="pdf-paper" style="display:none;">

                {{-- Watermark emblem --}}
                <div class="pdf-watermark" aria-hidden="true">
                    <svg viewBox="0 0 200 200" width="260" height="260">
                        <circle cx="100" cy="100" r="92" fill="none" stroke="#0A1E33" stroke-width="2"/>
                        <circle cx="100" cy="100" r="78" fill="none" stroke="#0A1E33" stroke-width="1"/>
                        <text x="100" y="60" text-anchor="middle" font-size="9" font-weight="800" letter-spacing="2" fill="#0A1E33">PEMERINTAH KOTA</text>
                        <text x="100" y="145" text-anchor="middle" font-size="9" font-weight="800" letter-spacing="2" fill="#0A1E33">SUKABUMI</text>
                        <text x="100" y="105" text-anchor="middle" font-size="14" font-weight="900" fill="#0A1E33">SOA</text>
                    </svg>
                </div>

                {{-- Ribbon status --}}
                <div class="pdf-ribbon"><span>Terverifikasi Sistem</span></div>

                {{-- Kop Surat --}}
                <div class="pdf-kop">
                    <img src="{{ asset('image/logo-sukabumi.png') }}" alt="Logo" onerror="this.style.display='none'">
                    <div class="pdf-kop-text">
                        <h4>PEMERINTAH KOTA SUKABUMI</h4>
                        <h5>Sukabumi One Access &mdash; Layanan Pengaduan Digital</h5>
                    </div>
                    <div class="pdf-ticket-badge" id="pdf-ticket-id">#ID...</div>
                </div>
                <div class="pdf-divider-double">
                    <div class="line-thick"></div>
                    <div class="line-thin"></div>
                </div>

                {{-- Konten Dokumen --}}
                <div class="pdf-content">
                    <h3 class="pdf-doc-title">Ringkasan Pengaduan Masyarakat</h3>

                    <div class="pdf-grid">
                        <div class="pdf-item">
                            <label>Kategori</label>
                            <p><span class="pdf-tag" id="pdf-val-category">Memuat...</span></p>
                        </div>
                        <div class="pdf-item">
                            <label>Waktu Lapor</label>
                            <p class="mono" id="pdf-val-time">Memuat...</p>
                        </div>
                    </div>

                    <div class="pdf-item">
                        <label>Subjek Laporan</label>
                        <p class="pdf-title-text" id="pdf-val-title">Memuat...</p>
                    </div>

                    <div class="pdf-item">
                        <label>Uraian Kejadian</label>
                        <div class="pdf-desc-box" id="pdf-val-description">Memuat deskripsi laporan...</div>
                    </div>

                    <div class="pdf-grid pdf-avoid-break">
                        <div class="pdf-item">
                            <label>Foto Bukti</label>
                            <div class="pdf-img-frame">
                                <img id="pdf-val-attachment" src="" alt="Bukti" crossorigin="anonymous">
                            </div>
                        </div>
                        <div class="pdf-item">
                            <label>Lokasi Kejadian</label>
                            <div class="pdf-loc-box">
                                <p id="pdf-val-address" class="pdf-address-text">Mencari lokasi...</p>
                                <div class="pdf-coords mono">
                                    <i class="bi bi-geo-alt-fill"></i>
                                    <span id="pdf-val-lat">0</span>, <span id="pdf-val-lng">0</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Signature Area --}}
                    <div class="pdf-signature-row pdf-avoid-break">
                        <div class="pdf-verify-block">
                            <div id="pdf-qrcode" class="pdf-qr-mini"></div>
                            <div class="pdf-verify-text">
                                <p>Kode Verifikasi</p>
                                <strong id="pdf-verify-code" class="mono">------</strong>
                            </div>
                        </div>
                        <div class="pdf-stamp-area">
                            <img src="{{ asset('image/stempel-digital.png') }}" class="pdf-stamp-img" onerror="this.style.display='none'">
                            <p>Divalidasi Sistem SOA</p>
                            <strong id="pdf-footer-date">{{ date('d/m/Y') }}</strong>
                        </div>
                    </div>

                    <p class="pdf-legal-note">
                        Dokumen ini dihasilkan secara elektronik oleh sistem Sukabumi One Access dan sah tanpa tanda tangan basah.
                        Keabsahan dapat diperiksa dengan memindai kode QR di atas.
                    </p>
                </div>
            </div>
        </div>

        {{-- FOOTER MODAL --}}
        <div class="pdf-footer">
            <button type="button" class="btn-pdf-print" onclick="window.printDocument()">
                <i class="bi bi-printer"></i> Cetak Dokumen
            </button>
            <button type="button" class="btn-pdf-close" onclick="window.closePreviewModal()">
                Tutup Pratinjau
            </button>
        </div>
    </div>
</div>

@once
{{-- QRCode.js untuk generate QR asli dari ticket id + hash verifikasi --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Source+Serif+4:wght@600;700;900&family=Plus+Jakarta+Sans:wght@500;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">

<style>
    :root{
        --navy-950:#0A1E33;
        --navy-700:#123457;
        --gold:#E8A33D;
        --paper:#FFFDF9;
        --ink:#1e293b;
        --muted:#64748b;
    }

    .pdf-overlay { position: fixed; inset: 0; background: rgba(10, 30, 51, 0.92); backdrop-filter: blur(6px); display: none; align-items: center; justify-content: center; z-index: 10000; padding: 20px; opacity: 0; font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
    .pdf-overlay.is-active { display: flex; animation: pdfFade 0.3s ease forwards; }
    .pdf-overlay.is-active .pdf-card { animation: pdfSpring 0.45s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
    .pdf-overlay.is-closing { animation: pdfFadeOut 0.25s ease forwards; }

    .pdf-card { background: #fff; width: 100%; max-width: 460px; border-radius: 22px; overflow: hidden; box-shadow: 0 30px 60px -12px rgba(0,0,0,0.55); display: flex; flex-direction: column; max-height: 90vh; }

    /* HEADER */
    .pdf-header { background: linear-gradient(135deg, var(--navy-950), var(--navy-700)); padding: 14px 16px 14px 20px; border-bottom: 3px solid var(--gold); display: flex; align-items: center; gap: 12px; position: relative; }
    .pdf-header h2 { color: #fff; font-size: 11px; font-weight: 800; margin: 0; letter-spacing: 2.5px; flex: 1; }
    .pdf-icon-box { color: var(--gold); font-size: 18px; }
    .pdf-header-actions { display: flex; align-items: center; gap: 4px; }
    .pdf-header-btn, .pdf-close-x { color: rgba(255,255,255,0.55); background: rgba(255,255,255,0.06); border: none; cursor: pointer; width: 28px; height: 28px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 12px; transition: 0.15s; }
    .pdf-header-btn:hover, .pdf-close-x:hover { color: #fff; background: rgba(255,255,255,0.15); }

    /* BODY / SCROLL */
    .pdf-body { padding: 16px; background: #EEF2F7; overflow-y: auto; }
    .custom-scrollbar-pdf::-webkit-scrollbar { display: none; }

    /* PAPER */
    .pdf-paper { background: var(--paper); padding: 26px 24px; border-radius: 10px; border: 1px solid #e7e2d6; min-height: 500px; position: relative; overflow: hidden; box-shadow: inset 0 0 0 1px rgba(10,30,51,0.02); }

    .pdf-watermark { position:absolute; top:50%; left:50%; transform: translate(-50%,-50%); opacity:0.045; pointer-events:none; z-index:0; }

    .pdf-ribbon { position:absolute; top:14px; right:-38px; transform: rotate(40deg); background: linear-gradient(135deg, var(--gold), #d68f28); color:#3a2404; font-size:7.5px; font-weight:900; letter-spacing:1px; text-transform:uppercase; padding:4px 46px; box-shadow:0 2px 6px rgba(0,0,0,0.2); z-index:2; }
    .pdf-ribbon span{ display:flex; align-items:center; justify-content:center; gap:3px; }

    .pdf-kop { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; position: relative; z-index: 1; }
    .pdf-kop img { width: 34px; }
    .pdf-kop-text h4 { font-family:'Source Serif 4', serif; font-size: 10px; font-weight: 900; margin: 0; color: var(--navy-950); letter-spacing: 0.4px; }
    .pdf-kop-text h5 { font-size: 7.5px; font-weight: 600; margin: 2px 0 0; color: var(--muted); }
    .pdf-ticket-badge { margin-left: auto; background: var(--navy-700); color: var(--gold); padding: 4px 9px; border-radius: 6px; font-size: 9px; font-weight: 800; font-family:'JetBrains Mono', monospace; }

    .pdf-divider-double { margin: 0 0 20px; position: relative; z-index: 1; }
    .line-thick { height: 2.5px; background: var(--navy-950); }
    .line-thin { height: 1px; background: var(--navy-950); margin-top: 2px; opacity: 0.45; }

    .pdf-content { position: relative; z-index: 1; }
    .pdf-doc-title { font-family:'Source Serif 4', serif; text-align: center; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 22px; color: var(--navy-950); position: relative; }
    .pdf-doc-title::after{ content:''; display:block; width:36px; height:2px; background: var(--gold); margin: 8px auto 0; }

    .pdf-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .pdf-item { margin-bottom: 16px; }
    .pdf-item label { display: block; font-size: 7.5px; font-weight: 800; color: #94a3b8; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.8px; }
    .pdf-item p { font-size: 10px; font-weight: 700; color: var(--ink); margin: 0; }
    .mono { font-family:'JetBrains Mono', monospace !important; }

    .pdf-tag { display:inline-block; background:#F1F5F9; color: var(--navy-700); border: 1px solid #e2e8f0; padding: 3px 9px; border-radius: 20px; font-size: 9px; font-weight: 700; }

    .pdf-title-text { font-family:'Source Serif 4', serif; font-size: 13.5px !important; color: var(--navy-950) !important; line-height: 1.4; }
    .pdf-desc-box { background: #FAF9F4; padding: 12px; border-radius: 8px; font-size: 9.5px; line-height: 1.65; color: #475569; border: 1px solid #f0ece0; min-height: 60px; }

    .pdf-img-frame { width: 100%; aspect-ratio: 4/3; background: #f1f5f9; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0; }
    .pdf-img-frame img { width: 100%; height: 100%; object-fit: cover; }

    .pdf-loc-box { background:#FAF9F4; border:1px solid #f0ece0; border-radius:8px; padding:10px; }
    .pdf-address-text { font-size: 9.5px !important; line-height:1.5; margin-bottom: 6px !important; }
    .pdf-coords { font-size: 8.5px; color: var(--muted); display:flex; align-items:center; gap:4px; }

    .pdf-signature-row { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 26px; padding-top: 16px; border-top: 1px dashed #d8d2c2; }
    .pdf-verify-block { display:flex; align-items:center; gap: 10px; }
    .pdf-qr-mini { width: 48px; height: 48px; border: 1px solid #e2e8f0; border-radius: 6px; display: flex; align-items: center; justify-content: center; background:#fff; padding: 3px; }
    .pdf-qr-mini img, .pdf-qr-mini canvas { width: 100% !important; height: 100% !important; }
    .pdf-verify-text p { margin:0; font-size: 7px; font-weight: 800; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.5px; }
    .pdf-verify-text strong { font-size: 11px; color: var(--navy-950); letter-spacing: 1px; }

    .pdf-stamp-area { text-align: center; position: relative; }
    .pdf-stamp-area p{ font-size: 8px; color: var(--muted); margin: 0; font-weight: 600; }
    .pdf-stamp-area strong{ font-size: 10px; color: var(--navy-950); font-family:'JetBrains Mono', monospace; }
    .pdf-stamp-img { position: absolute; width: 62px; top: -34px; left: 50%; transform: translateX(-50%) rotate(-15deg); opacity: 0.3; }

    .pdf-legal-note { margin-top: 18px; font-size: 7.5px; line-height: 1.6; color: #a1a9b5; text-align: center; font-style: italic; }

    /* SKELETON */
    .pdf-skeleton { background: var(--paper); border-radius: 10px; border: 1px solid #e7e2d6; padding: 26px 24px; min-height: 500px; }
    .sk-row { border-radius: 6px; background: linear-gradient(90deg, #edeae0 25%, #f5f3ec 37%, #edeae0 63%); background-size: 400% 100%; animation: shimmer 1.4s ease infinite; }
    .sk-kop { height: 34px; width: 60%; margin-bottom: 22px; }
    .sk-line { height: 12px; margin-bottom: 10px; }
    .sk-block { height: 90px; }
    .sk-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-top: 14px; }
    .sk-block-sm { height: 110px; }
    @keyframes shimmer { 0%{ background-position: 100% 0; } 100%{ background-position: 0 0; } }

    /* FOOTER */
    .pdf-footer { padding: 14px 16px; background: #fff; border-top: 1px solid #f1f5f9; display: flex; gap: 10px; }
    .btn-pdf-print { flex: 1; background: #f1f5f9; color: var(--navy-700); border: none; padding: 11px; border-radius: 12px; font-size: 10px; font-weight: 800; cursor: pointer; transition: 0.2s; display:flex; align-items:center; justify-content:center; gap:6px; }
    .btn-pdf-print:hover { background: #e2e8f0; }
    .btn-pdf-close { flex: 2; background: var(--navy-700); color: #fff; border: none; padding: 11px; border-radius: 12px; font-size: 10px; font-weight: 800; cursor: pointer; transition: 0.2s; }
    .btn-pdf-close:hover { background: var(--navy-950); }

    @keyframes pdfFade { from { opacity: 0; } to { opacity: 1; } }
    @keyframes pdfSpring { 0% { opacity: 0; transform: scale(0.92) translateY(20px); } 100% { opacity: 1; transform: scale(1) translateY(0); } }
    @keyframes pdfFadeOut { from { opacity: 1; } to { opacity: 0; } }

    /* =========================================================
       PRINT MODE
       Tujuan: preview cetak identik dengan tampilan di modal,
       full A4, rapi, dan TIDAK terpotong walau kontennya panjang.
       ========================================================= */
    @media print {

        /* Paksa semua warna/gradient/background ikut ke-print
           (browser default mem-strip ini kalau tidak dipaksa) */
        html, body, #printableArea, #printableArea * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }

        /* Sembunyikan TOTAL semua anak <body> kecuali #print-mount.
           Beda dengan visibility:hidden, display:none benar-benar
           menghapus elemen dari layout (nggak nyisain ruang kosong),
           jadi tidak ada lagi "halaman kosong nyempil" akibat elemen
           dashboard/modal yang invisible tapi tetap makan tinggi
           (misalnya .pdf-card yang di-set 210mm x 297mm). */
        body > *:not(#print-mount) {
            display: none !important;
        }

        #print-mount {
            display: block !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        #print-mount, #print-mount * {
            visibility: visible !important;
        }

        /* Kertas dokumen mengisi lebar halaman cetak penuh, tanpa
           sisa styling modal (max-width, shadow, border radius) */
        #print-mount .pdf-paper {
            position: static !important;
            display: block !important;
            width: 100% !important;
            max-width: none !important;
            min-height: 0 !important;
            margin: 0 !important;
            border: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
        }

        /* Cegah bagian penting (foto, tanda tangan) kepotong
           tepat di batas halaman */
        .pdf-avoid-break,
        .pdf-img-frame,
        .pdf-signature-row {
            break-inside: avoid;
            page-break-inside: avoid;
        }

        @page {
            size: A4;
            margin: 15mm 14mm;
        }
    }
</style>

<script>
    if (typeof window.modalInitialized === 'undefined') {
        window.modalInitialized = true;

        const formatIDDate = (dateString) => {
            if(!dateString) return '-';
            const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
            return new Date(dateString).toLocaleDateString('id-ID', options);
        };

        const genVerifyCode = (id) => {
            const base = (id || '').toString().toUpperCase();
            const rand = Math.random().toString(36).substring(2, 6).toUpperCase();
            return `SOA-${base}-${rand}`;
        };

        window.openPreviewModal = function(id) {
            const modal = document.getElementById('modalDetailPdfUser'); // Sesuaikan ID
            if (!modal) return;

            const baseUrl = window.location.origin;

            const skeleton = document.getElementById('pdf-skeleton');
            const paper = document.getElementById('printableArea');
            skeleton.style.display = 'block';
            paper.style.display = 'none';

            modal.classList.add('is-active');
            document.body.style.overflow = 'hidden';

            // BAGIAN PENTING: URL diubah ke route user dan fetch mark-as-review DIHAPUS
            fetch(`{{ url('/riwayat-pengaduan') }}/${id}/json`)
                .then(res => {
                    if (!res.ok) throw new Error('Data tidak ditemukan');
                    return res.json();
                })
                .then(data => {
                    // 1. Mapping Data Teks
                    document.getElementById('pdf-ticket-id').textContent = '#' + data.ticket_id;
                    document.getElementById('pdf-val-category').textContent = data.category || 'Lainnya';
                    document.getElementById('pdf-val-time').textContent = formatIDDate(data.created_at);
                    document.getElementById('pdf-val-title').textContent = data.title;
                    document.getElementById('pdf-val-description').textContent = data.description;
                    document.getElementById('pdf-val-address').textContent = data.location_address || 'Alamat tidak tersedia';
                    document.getElementById('pdf-val-lat').textContent = data.latitude || '0';
                    document.getElementById('pdf-val-lng').textContent = data.longitude || '0';

                    // 2. Mapping Gambar Bukti
                    const img = document.getElementById('pdf-val-attachment');
                    img.onerror = function () {
                        this.onerror = null;
                        this.src = `${baseUrl}/image/no-image.png`;
                    };
                    img.src = data.attachment
                        ? `${baseUrl}/storage/${data.attachment}`
                        : `${baseUrl}/image/no-image.png`;

                    document.getElementById('pdf-footer-date').textContent = new Date().toLocaleDateString('id-ID');

                    // 3. Generate Kode Verifikasi & QR
                    const verifyCode = genVerifyCode(data.ticket_id);
                    const verifyCodeEl = document.getElementById('pdf-verify-code');
                    if (verifyCodeEl) verifyCodeEl.textContent = verifyCode;

                    const qrBox = document.getElementById('pdf-qrcode');
                    if (qrBox && window.QRCode) {
                        qrBox.innerHTML = '';
                        new QRCode(qrBox, {
                            text: `${baseUrl}/verify/${data.ticket_id}?code=${verifyCode}`,
                            width: 44,
                            height: 44,
                            colorDark: "#0A1E33",
                            colorLight: "#ffffff",
                            correctLevel: QRCode.CorrectLevel.M
                        });
                    }

                    // 4. Tampilkan Kertas Dokumen
                    skeleton.style.display = 'none';
                    paper.style.display = 'block';
                })
                .catch(err => {
                    console.error(err);
                    alert("Gagal memuat data laporan.");
                    window.closePreviewModal(); // Gunakan fungsi tutup khusus user
                });
        };

        window.closePreviewModal = function() {
            const modal = document.getElementById('modalDetailPdfUser'); // Sesuaikan ID
            if (modal) {
                modal.classList.add('is-closing');
                setTimeout(() => {
                    modal.classList.remove('is-active', 'is-closing');
                    document.body.style.overflow = 'auto';
                    
                    // window.location.reload(); <-- BARIS INI DIHAPUS
                    // Agar user tidak kembali ke posisi paling atas halaman (UX lebih lancar)
                }, 300);
            }
        };

        window.printDocument = function() {
            const source = document.getElementById('printableArea');
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
            // Ganti 'modalDetailPdf' menjadi 'modalDetailPdfUser'
            const modal = document.getElementById('modalDetailPdfUser'); 
            
            // Ganti 'window.closeDetailModal()' menjadi 'window.closePreviewModal()'
            if (e.key === 'Escape' && modal && modal.classList.contains('is-active')) {
                window.closePreviewModal();
            }
        });
    }
</script>
@endonce
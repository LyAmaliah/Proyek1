/* ============================================================
 * HealthyHabit — Script Utama (vanilla JavaScript)
 * ============================================================ */

document.addEventListener('DOMContentLoaded', function () {

    /* ---------- 1. Auto-tutup alert setelah 6 detik ---------- */
    document.querySelectorAll('.alert-dismissible').forEach(function (alert) {
        setTimeout(function () {
            try {
                var bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                bsAlert.close();
            } catch (e) { /* bootstrap belum termuat */ }
        }, 6000);
    });

    /* ---------- 2. Konfirmasi hapus data ---------- */
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!window.confirm(form.getAttribute('data-confirm'))) {
                e.preventDefault();
            }
        });
    });
    document.querySelectorAll('a[data-confirm]').forEach(function (link) {
        link.addEventListener('click', function (e) {
            if (!window.confirm(link.getAttribute('data-confirm'))) {
                e.preventDefault();
            }
        });
    });

    /* ---------- 3. Sidebar admin (mobile) ---------- */
    var sidebar = document.getElementById('sidebarMenu');
    var backdrop = document.getElementById('sidebarBackdrop');
    function closeSidebar() {
        if (sidebar) sidebar.classList.remove('show');
        if (backdrop) backdrop.classList.remove('show');
    }
    var openBtn = document.getElementById('openSidebar');
    if (openBtn && sidebar) {
        openBtn.addEventListener('click', function () {
            sidebar.classList.add('show');
            if (backdrop) backdrop.classList.add('show');
        });
    }
    var closeBtn = document.getElementById('closeSidebar');
    if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
    if (backdrop) backdrop.addEventListener('click', closeSidebar);

    /* ---------- 4. Tombol cetak / ekspor PDF ---------- */
    document.querySelectorAll('[data-print]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            window.print();
        });
    });

    /* ---------- 5. KALKULATOR GIZI (FR-11) ----------
     * Menghitung total zat gizi berdasarkan gramasi input pengguna
     * dari nilai dasar per 100 gram.
     * Elemen: #giziMakanan (select), #giziGram (number),
     *         #giziHasil (container), #giziNama, #giziKalori, dst.
     */
    var selectMakanan = document.getElementById('giziMakanan');
    var inputGram = document.getElementById('giziGram');
    var boxHasil = document.getElementById('giziHasil');

    function formatAngka(n) {
        return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 1 }).format(n);
    }

    function hitungGizi() {
        if (!selectMakanan || !inputGram || !boxHasil) return;

        var opsi = selectMakanan.options[selectMakanan.selectedIndex];
        var gram = parseFloat(inputGram.value) || 0;

        if (!opsi || !opsi.value || gram <= 0) {
            boxHasil.classList.add('d-none');
            return;
        }

        var faktor = gram / 100; // nilai tabel = per 100 gram
        var kalori = parseFloat(opsi.dataset.kalori || 0) * faktor;
        var karbo = parseFloat(opsi.dataset.karbohidrat || 0) * faktor;
        var protein = parseFloat(opsi.dataset.protein || 0) * faktor;
        var lemak = parseFloat(opsi.dataset.lemak || 0) * faktor;

        var set = function (id, val) {
            var el = document.getElementById(id);
            if (el) el.textContent = formatAngka(val);
        };
        set('giziHasilNama', opsi.textContent.trim() + ' — ' + gram + ' gram');
        set('giziKalori', kalori);
        set('giziKarbohidrat', karbo);
        set('giziProtein', protein);
        set('giziLemak', lemak);

        boxHasil.classList.remove('d-none');
    }

    if (selectMakanan) selectMakanan.addEventListener('change', hitungGizi);
    if (inputGram) inputGram.addEventListener('input', hitungGizi);

    /* ---------- 6. Pratinjau langsung BMI ---------- */
    var bmiBerat = document.getElementById('bmiBerat');
    var bmiTinggi = document.getElementById('bmiTinggi');
    var bmiPreview = document.getElementById('bmiPreview');

    function previewBmi() {
        if (!bmiBerat || !bmiTinggi || !bmiPreview) return;
        var berat = parseFloat(bmiBerat.value) || 0;
        var tinggi = parseFloat(bmiTinggi.value) || 0;
        if (berat <= 0 || tinggi <= 0) {
            bmiPreview.classList.add('d-none');
            return;
        }
        var m = tinggi / 100;
        var bmi = berat / (m * m);
        var kategori = bmi < 18.5 ? 'Kurus'
            : bmi < 25 ? 'Normal'
            : bmi < 30 ? 'Kelebihan Berat'
            : 'Obesitas';

        document.getElementById('bmiPreviewNilai').textContent = bmi.toFixed(1);
        document.getElementById('bmiPreviewKategori').textContent = kategori;
        bmiPreview.classList.remove('d-none');
    }
    if (bmiBerat) bmiBerat.addEventListener('input', previewBmi);
    if (bmiTinggi) bmiTinggi.addEventListener('input', previewBmi);

    /* ---------- 7. Widget rating bintang ---------- */
    var ratingLabels = document.querySelectorAll('.rating-label');
    ratingLabels.forEach(function (label) {
        label.addEventListener('click', function () {
            // beri jeda agar radio checked terlebih dahulu
            setTimeout(function () {
                var checked = document.querySelector('input[name="rating"]:checked');
                if (!checked) return;
                var nilai = parseInt(checked.value, 10);
                ratingLabels.forEach(function (l) {
                    var v = parseInt(l.getAttribute('data-value'), 10);
                    l.classList.toggle('active', v <= nilai);
                });
            }, 0);
        });
    });

    /* ---------- 8. Pratinjau gambar sebelum upload ---------- */
    document.querySelectorAll('input[type="file"][data-preview]').forEach(function (input) {
        var target = document.querySelector(input.getAttribute('data-preview'));
        if (!target) return;

        input.addEventListener('change', function () {
            var file = input.files && input.files[0];
            if (!file) return;
            if (!/^image\//.test(file.type)) return;

            var reader = new FileReader();
            reader.onload = function (ev) {
                target.src = ev.target.result;
                target.classList.remove('d-none');
                // Sembunyikan badge "Foto saat ini" karena pratinjau sudah foto baru
                var badge = target.parentElement.querySelector('.badge');
                if (badge) badge.classList.add('d-none');
            };
            reader.readAsDataURL(file);
        });

        // Jika opsi "Hapus foto" dicentang, kosongkan input & tampilkan placeholder
        var hapus = document.getElementById('hapus_' + input.name);
        if (hapus) {
            hapus.addEventListener('change', function () {
                if (!hapus.checked) return;
                input.value = '';
                var placeholder = input.getAttribute('data-preview-placeholder') || '';
                if (placeholder) target.src = placeholder;
            });
        }
    });
});

{{-- Cropper ya picha ya profile: applicant anasogeza/anazoom picha na kuona sehemu itakayoonekana
     (ndani ya duara) kabla ya kuipakia. Inatumia canvas: hakuna maktaba ya nje. --}}
<div class="crop-modal" id="cropModal" hidden role="dialog" aria-modal="true" aria-labelledby="cropTitle">
    <div class="crop-box">
        <h3 id="cropTitle">Adjust your photo</h3>
        <p class="crop-hint">Drag the photo to move it and use the slider to zoom. Only the part inside the circle will be visible.</p>

        <div class="crop-stage" id="cropStage">
            <canvas id="cropCanvas" width="320" height="320"></canvas>
            <div class="crop-mask" aria-hidden="true"></div>
        </div>

        <div class="crop-zoom">
            <i class="bi bi-zoom-out"></i>
            <input type="range" id="cropZoom" min="1" max="3" step="0.01" value="1" aria-label="Zoom">
            <i class="bi bi-zoom-in"></i>
        </div>

        <div class="crop-actions">
            <button type="button" class="ap-btn ghost" id="cropCancel">Cancel</button>
            <button type="button" class="ap-btn primary" id="cropSave"><i class="bi bi-check-lg"></i> Use this photo</button>
        </div>
    </div>
</div>

<script>
(function () {
    var STAGE = 320;          // ukubwa wa kisanduku cha kuonyesha
    var CIRCLE = 280;         // kipenyo cha duara (sehemu itakayoonekana)
    var OFF = (STAGE - CIRCLE) / 2;
    var OUT = 512;            // ukubwa wa picha ya mwisho (px)

    var modal  = document.getElementById('cropModal');
    var canvas = document.getElementById('cropCanvas');
    var zoom   = document.getElementById('cropZoom');
    var ctx    = canvas.getContext('2d');

    var img = null, scale = 1, minScale = 1, ox = 0, oy = 0;
    var dragging = false, lastX = 0, lastY = 0;
    var srcInput = null, objectUrl = null;

    function clamp() {
        var w = img.width * scale, h = img.height * scale;
        ox = Math.min(OFF, Math.max(OFF + CIRCLE - w, ox));
        oy = Math.min(OFF, Math.max(OFF + CIRCLE - h, oy));
    }

    function draw() {
        ctx.fillStyle = '#111827';
        ctx.fillRect(0, 0, STAGE, STAGE);
        ctx.drawImage(img, ox, oy, img.width * scale, img.height * scale);
    }

    function setZoom(z) {
        var prev = scale;
        scale = minScale * z;
        var c = STAGE / 2;                       // zoom kuzunguka katikati
        ox = c - (c - ox) * (scale / prev);
        oy = c - (c - oy) * (scale / prev);
        clamp();
        draw();
    }

    function close(resetInput) {
        modal.hidden = true;
        document.body.style.overflow = '';
        if (objectUrl) { URL.revokeObjectURL(objectUrl); objectUrl = null; }
        if (resetInput && srcInput) srcInput.value = '';
        img = null;
    }

    // Inaitwa na <input type="file" onchange="apPhotoChosen(this)"> kwenye sidebar
    window.apPhotoChosen = function (input) {
        if (!input.files || !input.files[0]) return;
        var file = input.files[0];

        if (!/^image\/(jpeg|png|webp)$/.test(file.type)) {
            alert('Choose a JPG, PNG or WEBP image.');
            input.value = '';
            return;
        }
        if (file.size > 15 * 1024 * 1024) {
            alert('This photo is too large. Choose a photo smaller than 15MB.');
            input.value = '';
            return;
        }

        srcInput = input;
        objectUrl = URL.createObjectURL(file);

        var im = new Image();
        im.onload = function () {
            img = im;
            minScale = Math.max(CIRCLE / im.width, CIRCLE / im.height);   // picha ijaze duara
            scale = minScale;
            ox = OFF + (CIRCLE - im.width * scale) / 2;
            oy = OFF + (CIRCLE - im.height * scale) / 2;
            zoom.value = 1;
            draw();
            modal.hidden = false;
            document.body.style.overflow = 'hidden';
        };
        im.onerror = function () {
            alert('This image could not be read. Try another photo.');
            close(true);
        };
        im.src = objectUrl;
    };

    // Kusogeza kwa kuburuta (panya au kidole)
    canvas.addEventListener('pointerdown', function (e) {
        if (!img) return;
        dragging = true; lastX = e.clientX; lastY = e.clientY;
        canvas.setPointerCapture(e.pointerId);
    });
    canvas.addEventListener('pointermove', function (e) {
        if (!dragging || !img) return;
        var k = STAGE / canvas.getBoundingClientRect().width;
        ox += (e.clientX - lastX) * k;
        oy += (e.clientY - lastY) * k;
        lastX = e.clientX; lastY = e.clientY;
        clamp(); draw();
    });
    function stopDrag() { dragging = false; }
    canvas.addEventListener('pointerup', stopDrag);
    canvas.addEventListener('pointercancel', stopDrag);

    // Zoom kwa slider na kwa gurudumu la panya
    zoom.addEventListener('input', function () { if (img) setZoom(parseFloat(zoom.value)); });
    canvas.addEventListener('wheel', function (e) {
        if (!img) return;
        e.preventDefault();
        var z = Math.min(3, Math.max(1, parseFloat(zoom.value) + (e.deltaY < 0 ? 0.08 : -0.08)));
        zoom.value = z;
        setZoom(z);
    }, { passive: false });

    document.getElementById('cropCancel').addEventListener('click', function () { close(true); });
    modal.addEventListener('click', function (e) { if (e.target === modal) close(true); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !modal.hidden) close(true); });

    // Hifadhi: kata sehemu ya duara, tengeneza picha ya mraba, kisha tuma fomu
    document.getElementById('cropSave').addEventListener('click', function () {
        if (!img || !srcInput) return;

        var out = document.createElement('canvas');
        out.width = out.height = OUT;
        var octx = out.getContext('2d');
        octx.fillStyle = '#ffffff';
        octx.fillRect(0, 0, OUT, OUT);

        var sx = (OFF - ox) / scale, sy = (OFF - oy) / scale, sw = CIRCLE / scale;
        octx.drawImage(img, sx, sy, sw, sw, 0, 0, OUT, OUT);

        out.toBlob(function (blob) {
            if (!blob) { alert('Could not prepare the photo. Try again.'); return; }

            var file = new File([blob], 'avatar.jpg', { type: 'image/jpeg' });
            var dt = new DataTransfer();
            dt.items.add(file);
            srcInput.files = dt.files;

            document.getElementById('photoForm').submit();
        }, 'image/jpeg', 0.9);
    });
})();
</script>

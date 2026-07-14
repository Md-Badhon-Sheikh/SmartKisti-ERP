{{-- Shared camera-capture modal. Include once per page; trigger via window.CameraCapture.open(callback). --}}
<div class="modal fade" id="cameraCaptureModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Capture Photo') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <video id="cameraVideo" autoplay playsinline muted class="w-100 rounded bg-dark" style="max-height:360px;"></video>
                <canvas id="cameraCanvas" class="d-none"></canvas>
                <div id="cameraError" class="text-danger mt-2" style="display:none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-primary" id="btnTakeSnapshot">
                    <i class="fas fa-camera me-1"></i>{{ __('Take Photo') }}
                </button>
            </div>
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
    (function () {
        var cameraStream    = null;
        var activeCallback  = null;

        window.CameraCapture = {
            open: function (callback) {
                activeCallback = callback;
                $('#cameraCaptureModal').modal('show');
            }
        };

        $(document).on('shown.bs.modal', '#cameraCaptureModal', function () {
            $('#cameraError').hide().text('');

            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                $('#cameraError').show().text('{{ __('Camera is not supported on this device/browser.') }}');
                return;
            }

            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false })
                .then(function (stream) {
                    cameraStream = stream;
                    document.getElementById('cameraVideo').srcObject = stream;
                })
                .catch(function () {
                    $('#cameraError').show().text('{{ __('Unable to access camera.') }}');
                });
        });

        $(document).on('hidden.bs.modal', '#cameraCaptureModal', function () {
            if (cameraStream) {
                cameraStream.getTracks().forEach(function (track) { track.stop(); });
                cameraStream = null;
            }
            activeCallback = null;
        });

        $(document).on('click', '#btnTakeSnapshot', function () {
            var video  = document.getElementById('cameraVideo');
            var canvas = document.getElementById('cameraCanvas');

            if (!video.videoWidth) return;

            canvas.width  = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);

            canvas.toBlob(function (blob) {
                if (!blob) return;

                var file = new File([blob], 'captured_' + Date.now() + '.jpg', { type: 'image/jpeg' });
                var url  = URL.createObjectURL(blob);
                var callback = activeCallback;

                $('#cameraCaptureModal').modal('hide');

                if (typeof callback === 'function') {
                    callback(file, url);
                }
            }, 'image/jpeg', 0.92);
        });
    })();
</script>
@endpush
@endonce

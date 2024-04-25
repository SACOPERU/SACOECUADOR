// public/js/custom-scripts.js
function previewImage(event, querySelector) {
    const input = event.target;
    const $imgPreview = document.querySelector(querySelector);

    if (!input.files.length) return;

    const file = input.files[0];
    const objectURL = URL.createObjectURL(file);

    $imgPreview.src = objectURL;
}

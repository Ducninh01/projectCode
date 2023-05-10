// preview image when uplaod image

function previewImage() {
    var fileInput = document.getElementById('fileInput');
    var preview = document.getElementById('preview');

    var file = fileInput.files[0];
    var reader = new FileReader();

    reader.onloadend = function() {
        preview.src = reader.result;
        preview.style.display = 'block';
    }

    if (file) {
        reader.readAsDataURL(file);
    } else {
        preview.src = "";
    }
}

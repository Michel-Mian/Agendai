window.previewProfileImage = function(event) {
    const reader = new FileReader();
    reader.onload = function(){
        document.getElementById('profileImagePreview').src = reader.result;
    }
    reader.readAsDataURL(event.target.files[0]);
}

window.clearProfileForm = function() {
    document.getElementById('name').value = '';
    document.getElementById('email').value = '';
    document.getElementById('profile_photo').value = '';
    document.getElementById('profileImagePreview').src = defaultProfileImage;
}


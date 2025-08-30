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
document.getElementById('btnProfile').onclick = function() {
    document.getElementById('cardProfile').classList.remove('hidden');
    document.getElementById('cardPreferences').classList.add('hidden');
    this.classList.add('bg-white', 'text-blue-900', 'shadow');
    this.classList.remove('bg-transparent', 'text-blue-700');
    document.getElementById('btnPreferences').classList.remove('bg-white', 'text-blue-900', 'shadow');
    document.getElementById('btnPreferences').classList.add('bg-transparent', 'text-blue-700');
};

document.getElementById('btnPreferences').onclick = function() {
    document.getElementById('cardProfile').classList.add('hidden');
    document.getElementById('cardPreferences').classList.remove('hidden');
    this.classList.add('bg-white', 'text-blue-900', 'shadow');
    this.classList.remove('bg-transparent', 'text-blue-700');
    document.getElementById('btnProfile').classList.remove('bg-white', 'text-blue-900', 'shadow');
    document.getElementById('btnProfile').classList.add('bg-transparent', 'text-blue-700');
};

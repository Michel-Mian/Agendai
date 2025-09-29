window.previewProfileImage = function(event) {
    const reader = new FileReader();
    reader.onload = function(){
        const preview = document.getElementById('profileImagePreview');
        if (preview) {
            preview.src = reader.result;
        }
    }
    reader.readAsDataURL(event.target.files[0]);
}

window.clearProfileForm = function() {
    const name = document.getElementById('name');
    const email = document.getElementById('email');
    const photo = document.getElementById('profile_photo');
    const preview = document.getElementById('profileImagePreview');
    
    if (name) name.value = '';
    if (email) email.value = '';
    if (photo) photo.value = '';
    if (preview && typeof defaultProfileImage !== 'undefined') {
        preview.src = defaultProfileImage;
    }
}

// Só executar se estivermos na página de perfil
document.addEventListener('DOMContentLoaded', function() {
    const btnProfile = document.getElementById('btnProfile');
    const btnPreferences = document.getElementById('btnPreferences');
    
    if (btnProfile) {
        btnProfile.onclick = function() {
            const cardProfile = document.getElementById('cardProfile');
            const cardPreferences = document.getElementById('cardPreferences');
            
            if (cardProfile) cardProfile.classList.remove('hidden');
            if (cardPreferences) cardPreferences.classList.add('hidden');
            
            this.classList.add('bg-white', 'text-blue-900', 'shadow');
            this.classList.remove('bg-transparent', 'text-blue-700');
            
            if (btnPreferences) {
                btnPreferences.classList.remove('bg-white', 'text-blue-900', 'shadow');
                btnPreferences.classList.add('bg-transparent', 'text-blue-700');
            }
        };
    }

    if (btnPreferences) {
        btnPreferences.onclick = function() {
            const cardProfile = document.getElementById('cardProfile');
            const cardPreferences = document.getElementById('cardPreferences');
            
            if (cardProfile) cardProfile.classList.add('hidden');
            if (cardPreferences) cardPreferences.classList.remove('hidden');
            
            this.classList.add('bg-white', 'text-blue-900', 'shadow');
            this.classList.remove('bg-transparent', 'text-blue-700');
            
            if (btnProfile) {
                btnProfile.classList.remove('bg-white', 'text-blue-900', 'shadow');
                btnProfile.classList.add('bg-transparent', 'text-blue-700');
            }
        };
    }
});

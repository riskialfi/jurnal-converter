document.getElementById('uploadForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const progressBar = document.getElementById('progressBar');
    const progressContainer = document.getElementById('progressContainer');
    
    // Tampilkan progress bar
    progressContainer.classList.remove('hidden');
    
    try {
        // Kirim data via AJAX
        const response = await fetch('app/process.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (!result.success) {
            throw new Error(result.error || 'Proses gagal');
        }
        
        // Update progress bar
        progressBar.style.width = '100%';
        
        // Tampilkan hasil
        const downloadLink = document.getElementById('downloadLink');
        downloadLink.href = result.download_url;
        
        document.getElementById('previewSection').classList.remove('hidden');
        
    } catch (error) {
        alert(`Error: ${error.message}`);
        progressBar.style.width = '0%';
    }
});


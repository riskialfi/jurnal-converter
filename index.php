<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jurnal Converter Pro</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .file-upload:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        .progress-bar {
            transition: width 0.5s ease-in-out;
        }
    </style>
</head>
<body class="bg-gradient-to-r from-blue-50 to-indigo-50 min-h-screen">
    <div class="container mx-auto px-4 py-12">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-indigo-800 mb-2">Jurnal Converter Pro</h1>
            <p class="text-lg text-gray-600">Ubah jurnal Anda sesuai template dalam hitungan detik!</p>
        </div>

        <!-- Upload Card -->
        <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden p-8">
            <form id="uploadForm" action="app/process.php" method="post" enctype="multipart/form-data" class="space-y-6">
                <!-- Journal Upload -->
                <div class="file-upload bg-blue-50 p-6 rounded-lg border-2 border-dashed border-blue-200 transition-all duration-300">
                    <label class="block text-lg font-semibold text-blue-800 mb-3">
                        <i class="fas fa-file-pdf mr-2"></i>Upload Jurnal
                    </label>
                    <div class="flex items-center justify-center">
                        <label class="cursor-pointer bg-white py-2 px-4 rounded-lg border border-blue-300 hover:bg-blue-50 transition">
                            <span class="text-blue-600 font-medium">Pilih File</span>
                            <input type="file" name="journal" accept=".pdf,.docx" class="hidden" required>
                        </label>
                        <span id="journalName" class="ml-3 text-gray-600">Belum ada file dipilih</span>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">Format: PDF atau DOCX (maks. 10MB)</p>
                </div>

                <!-- Template Upload -->
                <div class="file-upload bg-purple-50 p-6 rounded-lg border-2 border-dashed border-purple-200 transition-all duration-300">
                    <label class="block text-lg font-semibold text-purple-800 mb-3">
                        <i class="fas fa-file-alt mr-2"></i>Upload Template
                    </label>
                    <div class="flex items-center justify-center">
                        <label class="cursor-pointer bg-white py-2 px-4 rounded-lg border border-purple-300 hover:bg-purple-50 transition">
                            <span class="text-purple-600 font-medium">Pilih File</span>
                            <input type="file" name="template" accept=".docx,.json" class="hidden" required>
                        </label>
                        <span id="templateName" class="ml-3 text-gray-600">Belum ada file dipilih</span>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">Format: DOCX atau JSON</p>
                </div>

                <!-- Progress Bar (Awalnya tersembunyi) -->
                <div id="progressContainer" class="hidden">
                    <div class="mb-1 text-sm font-medium text-indigo-700">Memproses...</div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div id="progressBar" class="progress-bar bg-indigo-600 h-2.5 rounded-full" style="width: 0%"></div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg shadow-md transition duration-300 flex items-center justify-center">
                    <i class="fas fa-magic mr-2"></i> Konversi Sekarang
                </button>
            </form>
        </div>

        <!-- Preview Section (Akan muncul setelah konversi) -->
        <div id="previewSection" class="max-w-3xl mx-auto mt-8 hidden">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-file-download mr-2"></i>Hasil Konversi
                </h3>
                <a id="downloadLink" href="#" class="inline-block bg-green-500 hover:bg-green-600 text-white py-2 px-6 rounded-lg font-medium transition duration-300">
                    <i class="fas fa-download mr-2"></i>Download Jurnal
                </a>
            </div>
        </div>
    </div>

    <!-- JavaScript untuk Interaktivitas -->
    <script>
        // Update nama file yang dipilih
        document.querySelector('input[name="journal"]').addEventListener('change', function(e) {
            document.getElementById('journalName').textContent = e.target.files[0].name;
        });

        document.querySelector('input[name="template"]').addEventListener('change', function(e) {
            document.getElementById('templateName').textContent = e.target.files[0].name;
        });

        // Animasi saat form dikirim
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Tampilkan progress bar
            document.getElementById('progressContainer').classList.remove('hidden');
            const progressBar = document.getElementById('progressBar');
            
            // Simulasi progress (nanti diganti dengan AJAX real)
            let progress = 0;
            const interval = setInterval(() => {
                progress += 10;
                progressBar.style.width = `${progress}%`;
                
                if (progress >= 100) {
                    clearInterval(interval);
                    // Submit form setelah animasi selesai
                    this.submit();
                    
                    // Tampilkan preview section (contoh saja, di real case gunakan AJAX)
                    setTimeout(() => {
                        document.getElementById('previewSection').classList.remove('hidden');
                    }, 500);
                }
            }, 200);
        });
    </script>
</body>
</html>
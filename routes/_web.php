// ==================== ROUTE UNTUK Autentikasi Admin, User, dan Guest ====================
// Tidak digunakan lagi karena tidak terlindungi, sekarang sudah ada di group middleware masing-masing
// Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
// Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');


// ==================== ROUTE UNTUK CRUD BACKEND ====================
// Tidak digunakan lagi karena tidak terlindungi, sekarang sudah ada di group middleware masing-masing

// Route::resource('about', AboutController::class);
// Route::get('about/{id}/delete', [AboutController::class, 'confirmDelete'])->name('about.confirmDelete');

// Route::resource('info_statistik', InfoStatistikController::class);
// Route::get('info_statistik/{id}/delete', [InfoStatistikController::class, 'confirmDelete'])->name('info_statistik.confirmDelete');

// Route::resource('destinasi', DestinasiController::class);
// Route::get('destinasi/{id}/delete', [DestinasiController::class, 'confirmDelete'])->name('destinasi.confirmDelete');

// Route::resource('galeri', GaleriController::class);
// Route::get('galeri/{id}/delete', [GaleriController::class, 'confirmDelete'])->name('galeri.confirmDelete');

// Route::resource('kolaborasi', KolaborasiController::class);
// Route::get('kolaborasi/{id}/delete', [KolaborasiController::class, 'confirmDelete'])->name('kolaborasi.confirmDelete');
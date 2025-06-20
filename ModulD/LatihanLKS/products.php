<?php
include 'config.php';
include 'header.php';

// Jika user belum login, redirect ke login
if (!isset($_SESSION['user_id'])) {
    redirect('login.php');
}

// Pesan dari session
$message = get_message();

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    redirect('login.php');
}

// Tambah produk
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    
    // Validasi
    if (empty($name) || empty($description) || empty($price) || empty($category)) {
        display_message('Semua field harus diisi!', 'error');
    } else {
        try {
            // Handle file upload
            $imagePath = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $fileExt = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $fileName = uniqid() . '.' . $fileExt;
                $targetPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $imagePath = $targetPath;
                }
            }
            
            // Simpan ke database
            $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image, category) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $description, $price, $imagePath, $category]);
            
            if ($stmt->rowCount() > 0) {
                display_message('Produk berhasil ditambahkan!');
            } else {
                display_message('Terjadi kesalahan. Silakan coba lagi.', 'error');
            }
        } catch (PDOException $e) {
            display_message('Terjadi kesalahan: ' . $e->getMessage(), 'error');
        }
    }
    redirect('products.php');
}

// Update produk
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    
    try {
        // Handle file upload jika ada gambar baru
        $imagePath = $_POST['current_image'];
        if (isset($_FILES['image'])) {
            if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $fileExt = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $fileName = uniqid() . '.' . $fileExt;
                $targetPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    // Hapus gambar lama jika ada
                    if ($imagePath && file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                    $imagePath = $targetPath;
                }
            } elseif ($_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
                // Tidak ada file yang diupload, tetap gunakan gambar yang ada
            } else {
                throw new Exception('Error uploading file: ' . $_FILES['image']['error']);
            }
        }
        
        $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, image = ?, category = ? WHERE id = ?");
        $stmt->execute([$name, $description, $price, $imagePath, $category, $id]);
        
        if ($stmt->rowCount() > 0) {
            display_message('Produk berhasil diperbarui!');
        } else {
            display_message('Tidak ada perubahan data.', 'info');
        }
    } catch (PDOException $e) {
        display_message('Terjadi kesalahan: ' . $e->getMessage(), 'error');
    } catch (Exception $e) {
        display_message($e->getMessage(), 'error');
    }
    redirect('products.php');
}

// Hapus produk
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    try {
        // Ambil path gambar sebelum menghapus
        $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        
        if ($product && $product['image'] && file_exists($product['image'])) {
            unlink($product['image']);
        }
        
        // Hapus dari database
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() > 0) {
            display_message('Produk berhasil dihapus!');
        } else {
            display_message('Produk tidak ditemukan.', 'error');
        }
    } catch (PDOException $e) {
        display_message('Terjadi kesalahan: ' . $e->getMessage(), 'error');
    }
    redirect('products.php');
}

// Ambil semua produk dengan error handling yang lebih baik
try {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
    if ($stmt === false) {
        throw new Exception("Gagal menjalankan query produk");
    }
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Jika tidak ada produk, set array kosong
    if ($products === false) {
        $products = [];
    }
} catch (PDOException $e) {
    $products = [];
    error_log("Error mengambil produk: " . $e->getMessage());
    display_message('Error mengambil data produk', 'error');
} catch (Exception $e) {
    $products = [];
    error_log("Error umum: " . $e->getMessage());
    display_message($e->getMessage(), 'error');
}

// Statistik sederhana tanpa orders
$product_count = count($products);
$order_count = 0;
$customer_count = 0;
$revenue = 0;

// Setelah query produk
error_log("Jumlah produk ditemukan: " . count($products));
error_log("Contoh produk pertama: " . print_r($products[0] ?? null, true));
?>


        <div class="card" id="products">
            <div class="card-header">
                <h2>Tambah Produk Baru</h2>
            </div>
            <form action="products.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Nama Produk</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Deskripsi</label>
                    <textarea id="description" name="description" class="form-control" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="price">Harga (Rp)</label>
                    <input type="number" id="price" name="price" class="form-control" min="0" step="1000" required>
                </div>
                
                <div class="form-group">
                    <label for="image">Gambar Produk</label>
                    <input type="file" id="image" name="image" class="form-control" accept="image/*" onchange="previewImage(this, 'addPreview')">
                    <div class="image-preview-container">
                        <img id="addPreview" class="image-preview" alt="Preview Gambar">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="category">Kategori</label>
                    <select id="category" name="category" class="form-control" required>
                        <option value="">Pilih Kategori</option>
                        <option value="Laptop">Laptop</option>
                        <option value="Handphone">Handphone</option>
                        <option value="Aksesoris">Aksesoris</option>
                        <option value="Smartwatch">Smartwatch</option>
                        <option value="Audio">Audio</option>
                    </select>
                </div>
                
                <button type="submit" name="add_product" class="btn"><i class="fas fa-plus"></i> Tambah Produk</button>
            </form>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2>Daftar Produk</h2>
            </div>
            
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Produk</th>
                            <th>Deskripsi</th>
                            <th>Harga</th>
                            <th>Kategori</th>
                            <th>Gambar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($products) && is_array($products) && !empty($products)): ?>
                            <?php 
                            $counter = 1;
                            foreach ($products as $product): ?>
                                <tr data-id="<?php echo $product['id'] ?? 0; ?>" data-image="<?php echo !empty($product['image']) ? htmlspecialchars($product['image']) : ''; ?>">
                                    <td><?php echo $counter++; ?></td>
                                    <td><?php echo htmlspecialchars($product['name'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars(substr($product['description'] ?? '', 0, 50)); ?>...</td>
                                    <td>Rp. <?php echo isset($product['price']) ? number_format($product['price'], 0, ',', '.') : '0'; ?></td>
                                    <td><?php echo htmlspecialchars($product['category'] ?? ''); ?></td>
                                    <td>
                                        <?php if (!empty($product['image'])): ?>
                                            <img src="<?php echo htmlspecialchars($product['image']); ?>" width="50" height="50" style="object-fit: cover; border-radius: 4px;">
                                        <?php else: ?>
                                            <span>No Image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <button class="btn-icon" onclick="openEditModal(
                                                <?php echo $product['id'] ?? 0; ?>,
                                                '<?php echo isset($product['name']) ? addslashes($product['name']) : ''; ?>',
                                                '<?php echo isset($product['description']) ? addslashes($product['description']) : ''; ?>',
                                                <?php echo $product['price'] ?? 0; ?>,
                                                '<?php echo $product['category'] ?? ''; ?>',
                                                '<?php echo !empty($product['image']) ? htmlspecialchars($product['image']) : ''; ?>'
                                            )">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn-icon btn-delete" onclick="confirmDelete(<?php echo $product['id'] ?? 0; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center;">Tidak ada produk</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Modal untuk edit produk -->
        <div id="editModal" class="edit-modal">
            <div class="edit-modal-content">
                <span class="close" onclick="closeEditModal()">×</span>
                <h2>Edit Produk</h2>
                
                <form id="editForm" action="products.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="editId">
                    <input type="hidden" name="update_product" value="1">
                    <input type="hidden" name="current_image" id="currentImage">
                    
                    <div class="form-group">
                        <label for="editName">Nama Produk</label>
                        <input type="text" id="editName" name="name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="editDescription">Deskripsi</label>
                        <textarea id="editDescription" name="description" class="form-control" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="editPrice">Harga (Rp)</label>
                        <input type="number" id="editPrice" name="price" class="form-control" min="0" step="1000" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="editImage">Gambar Produk</label>
                        <input type="file" id="editImage" name="image" class="form-control" accept="image/*" onchange="previewImage(this, 'editPreview')">
                        <div class="image-preview-container">
                            <img id="editPreview" class="image-preview" alt="Preview Gambar">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="editCategory">Kategori</label>
                        <select id="editCategory" name="category" class="form-control" required>
                            <option value="">Pilih Kategori</option>
                            <option value="Laptop">Laptop</option>
                            <option value="Handphone">Handphone</option>
                            <option value="Aksesoris">Aksesoris</option>
                            <option value="Smartwatch">Smartwatch</option>
                            <option value="Audio">Audio</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn"><i class="fas fa-save"></i> Simpan Perubahan</button>
                </form>
            </div>
        </div>
        
        <!-- Modal konfirmasi hapus -->
        <div id="confirmModal" class="confirm-modal">
            <div class="confirm-modal-content">
                <h3>Konfirmasi Hapus</h3>
                <p>Anda yakin ingin menghapus produk ini?</p>
                <div class="confirm-buttons">
                    <button class="confirm-btn confirm-yes" id="confirmYes">Ya, Hapus</button>
                    <button class="confirm-btn confirm-no" onclick="closeConfirmModal()">Batal</button>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Sidebar toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileToggle = document.getElementById('mobileToggle');
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');

            mobileToggle.addEventListener('click', function() {
                sidebar.classList.toggle('active');
                mainContent.classList.toggle('sidebar-active');
            });

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                if (window.innerWidth <= 992 && sidebar.classList.contains('active') && !sidebar.contains(event.target) && !mobileToggle.contains(event.target)) {
                    sidebar.classList.remove('active');
                    mainContent.classList.remove('sidebar-active');
                }
            });
        });

        // Modal edit
        function openEditModal(id, name, description, price, category, image) {
            document.getElementById('editId').value = id;
            document.getElementById('editName').value = name;
            document.getElementById('editDescription').value = description;
            document.getElementById('editPrice').value = price;
            document.getElementById('editCategory').value = category;
            document.getElementById('currentImage').value = image;
            
            const preview = document.getElementById('editPreview');
            if (image) {
                preview.src = image;
                preview.style.display = 'block';
            } else {
                preview.src = '';
                preview.style.display = 'none';
            }
            
            document.getElementById('editModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Modal konfirmasi hapus
        let productIdToDelete = null;

        function confirmDelete(id) {
            productIdToDelete = id;
            document.getElementById('confirmModal').style.display = 'block';
        }

        function closeConfirmModal() {
            productIdToDelete = null;
            document.getElementById('confirmModal').style.display = 'none';
        }

        document.getElementById('confirmYes').addEventListener('click', function() {
            if (productIdToDelete) {
                window.location.href = 'products.php?delete=' + productIdToDelete;
            }
        });

        // Tutup modal ketika klik di luar modal
        window.onclick = function(event) {
            const editModal = document.getElementById('editModal');
            const confirmModal = document.getElementById('confirmModal');
            
            if (event.target == editModal) {
                closeEditModal();
            }
            
            if (event.target == confirmModal) {
                closeConfirmModal();
            }
        };

        // Preview gambar
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            const file = input.files[0];
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                
                reader.readAsDataURL(file);
            } else {
                preview.src = '';
                preview.style.display = 'none';
            }
        }
    </script>
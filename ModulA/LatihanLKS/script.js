
        
        async function toggleFavorite(productId, heartIcon) {
            try {
                
                
                await new Promise(resolve => setTimeout(resolve, 300));
                
                
                const heart = heartIcon.querySelector('i');
                heart.classList.toggle('active');
                
                if (heart.classList.contains('active')) {
                    showNotification('Produk ditambahkan ke favorit');
                } else {
                    showNotification('Produk dihapus dari favorit');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Gagal mengupdate favorit', 'error');
            }
        }

        
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.classList.add('fade-out');
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        
        document.addEventListener('click', function(event) {
            const favoriteIcon = event.target.closest('.favorite-icon');
            if (favoriteIcon) {
                event.preventDefault();
                event.stopPropagation();
                
                const productId = favoriteIcon.dataset.productId;
                const heartIcon = favoriteIcon.querySelector('i');
                
                
                heartIcon.classList.toggle('active');
                
                
                toggleFavorite(productId, favoriteIcon);
            }
        });

        
        document.addEventListener('DOMContentLoaded', function() {
            
            document.getElementById('hamburger').addEventListener('click', function() {
                document.getElementById('mobileSidebar').classList.add('active');
                document.body.style.overflow = 'hidden';
            });

            document.getElementById('closeSidebar').addEventListener('click', function() {
                document.getElementById('mobileSidebar').classList.remove('active');
                document.body.style.overflow = 'auto';
            });

            
            document.getElementById('searchToggle').addEventListener('click', function() {
                const searchForm = document.getElementById('searchForm');
                searchForm.classList.toggle('active');
                if (searchForm.classList.contains('active')) {
                    searchForm.querySelector('input').focus();
                }
            });

            
            let nextDom = document.getElementById('next');
            let prevDom = document.getElementById('prev');
            let carouselDom = document.querySelector('.carousel');
            let SliderDom = carouselDom.querySelector('.carousel .list');
            let thumbnailBorderDom = document.querySelector('.carousel .thumbnail');
            let thumbnailItemsDom = thumbnailBorderDom.querySelectorAll('.item');
            let timeDom = document.querySelector('.carousel .time');
            
            thumbnailBorderDom.appendChild(thumbnailItemsDom[0]);
            
            let timeRunning = 3000;
            let timeAutoNext = 7000;
            let runTimeOut;
            let runNextAuto = setTimeout(() => nextDom.click(), timeAutoNext);
            
            nextDom.onclick = function() { showSlider('next'); }
            prevDom.onclick = function() { showSlider('prev'); }
            
            function showSlider(type) {
                let SliderItemsDom = SliderDom.querySelectorAll('.carousel .list .item');
                let thumbnailItemsDom = document.querySelectorAll('.carousel .thumbnail .item');
                
                if (type === 'next') {
                    SliderDom.appendChild(SliderItemsDom[0]);
                    thumbnailBorderDom.appendChild(thumbnailItemsDom[0]);
                    carouselDom.classList.add('next');
                } else {
                    SliderDom.prepend(SliderItemsDom[SliderItemsDom.length - 1]);
                    thumbnailBorderDom.prepend(thumbnailItemsDom[thumbnailItemsDom.length - 1]);
                    carouselDom.classList.add('prev');
                }
                
                clearTimeout(runTimeOut);
                runTimeOut = setTimeout(() => {
                    carouselDom.classList.remove('next');
                    carouselDom.classList.remove('prev');
                }, timeRunning);
                
                clearTimeout(runNextAuto);
                runNextAuto = setTimeout(() => nextDom.click(), timeAutoNext);
            }
        });

        
        function openOrderModal(id, name, price, image) {
            document.getElementById('modalProductId').value = id;
            document.getElementById('modalProductName').textContent = name;
            document.getElementById('modalProductPrice').textContent = 'Rp ' + price.toLocaleString('id-ID');
            document.getElementById('orderModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('orderModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        
        document.getElementById('orderForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            
            const quantity = document.getElementById('quantity').value;
            const productName = document.getElementById('modalProductName').textContent;
            
            showNotification(`${quantity} ${productName} ditambahkan ke keranjang`);
            closeModal();
            
            
            const cartCount = document.getElementById('cartCount');
            cartCount.textContent = parseInt(cartCount.textContent) + parseInt(quantity);
        });
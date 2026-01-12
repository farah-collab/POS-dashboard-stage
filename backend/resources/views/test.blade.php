<!DOCTYPE html>
<html>
<head>
    <title>Test Auth & CRUD</title>
</head>
<body>
    <h1>Test Complete Flow - Products & Categories</h1>
    
    <!-- SECTION 1: REGISTER -->
    <div id="registerSection">
        <h2>REGISTER</h2>
        <input type="text" id="regName" placeholder="Name" value="Test User"><br><br>
        <input type="email" id="regEmail" placeholder="Email" value="test@test.com"><br><br>
        <input type="password" id="regPassword" placeholder="Password" value="password123"><br><br>
        <button onclick="register()">Register</button>
        <p>Already have account? <button onclick="showLogin()">Go to Login</button></p>
        <div id="registerMessage"></div>
    </div>
    
    <!-- SECTION 2: LOGIN -->
    <div id="loginSection" style="display:none;">
        <h2>LOGIN</h2>
        <input type="email" id="loginEmail" placeholder="Email" value="test@test.com"><br><br>
        <input type="password" id="loginPassword" placeholder="Password" value="password123"><br><br>
        <button onclick="login()">Login</button>
        <p>Don't have account? <button onclick="showRegister()">Go to Register</button></p>
        <div id="loginMessage"></div>
    </div>
    
    <!-- SECTION 3: DASHBOARD -->
    <div id="dashboardSection" style="display:none;">
        <h2>DASHBOARD</h2>
        <p id="welcomeMsg"></p>
        <button onclick="logout()">Logout</button>
        
        <hr>
        
        <!-- TABS pour switcher entre Catégories et Produits -->
        <div id="tabs">
            <button onclick="showTab('categories')">Categories</button>
            <button onclick="showTab('products')">Products</button>
        </div>
        
        <!-- SECTION CATÉGORIES -->
        <div id="categoriesTab">
            <h3>Categories Management</h3>
            
            <h4>Create New Category</h4>
            <input type="text" id="catName" placeholder="Category Name"><br><br>
            <input type="text" id="catDesc" placeholder="Description"><br><br>
            <button onclick="createCategory()">Create Category</button>
            
            <div id="catEditForm" style="display:none; border:1px solid black; padding:10px; margin:20px 0;">
                <h4>Edit Category</h4>
                <input type="hidden" id="catEditId">
                <input type="text" id="catEditName" placeholder="Category Name"><br><br>
                <input type="text" id="catEditDesc" placeholder="Description"><br><br>
                <button onclick="updateCategory()">Update</button>
                <button onclick="cancelCatEdit()">Cancel</button>
            </div>
            
            <div id="categoryMessage"></div>
            
            <h4>All Categories</h4>
            <div id="categoriesList">
                <p>Loading categories...</p>
            </div>
        </div>
        
        <!-- SECTION PRODUITS -->
        <div id="productsTab" style="display:none;">
            <h3>Products Management</h3>
            
            <h4>Create New Product</h4>
            <input type="text" id="prodName" placeholder="Product Name" value="Test Product"><br><br>
            
            <!-- IMPORTANT: Sélection de catégorie -->
            <select id="prodCategoryId">
                <option value="">Select Category</option>
                <!-- Les options seront remplies par JavaScript -->
            </select><br><br>
            
            <input type="number" id="prodPrice" placeholder="Price" value="99.99" step="0.01"><br><br>
            <input type="number" id="prodStock" placeholder="Stock Quantity" value="100"><br><br>
            <input type="text" id="prodImage" placeholder="Image URL (optional)" value="https://via.placeholder.com/150"><br><br>
            <input type="text" id="prodQrCode" placeholder="QR Code (optional)" value="PROD123"><br><br>
            
            <button onclick="createProduct()">Create Product</button>
            
            <div id="prodEditForm" style="display:none; border:1px solid black; padding:10px; margin:20px 0;">
                <h4>Edit Product</h4>
                <input type="hidden" id="prodEditId">
                <input type="text" id="prodEditName" placeholder="Product Name"><br><br>
                
                <select id="prodEditCategoryId">
                    <option value="">Select Category</option>
                    <!-- Les options seront remplies par JavaScript -->
                </select><br><br>
                
                <input type="number" id="prodEditPrice" placeholder="Price" step="0.01"><br><br>
                <input type="number" id="prodEditStock" placeholder="Stock Quantity"><br><br>
                <input type="text" id="prodEditImage" placeholder="Image URL"><br><br>
                <input type="text" id="prodEditQrCode" placeholder="QR Code"><br><br>
                
                <button onclick="updateProduct()">Update</button>
                <button onclick="cancelProdEdit()">Cancel</button>
            </div>
            
            <div id="productMessage"></div>
            
            <h4>All Products</h4>
            <div id="productsList">
                <p>Loading products...</p>
            </div>
        </div>
    </div>

    <script>
        let token = '';
        let user = null;
        let categories = []; // Pour stocker les catégories
        
        // ========== FONCTIONS D'AFFICHAGE ==========
        function showRegister() {
            document.getElementById('registerSection').style.display = 'block';
            document.getElementById('loginSection').style.display = 'none';
            document.getElementById('dashboardSection').style.display = 'none';
        }
        
        function showLogin() {
            document.getElementById('registerSection').style.display = 'none';
            document.getElementById('loginSection').style.display = 'block';
            document.getElementById('dashboardSection').style.display = 'none';
        }
        
        function showDashboard() {
            document.getElementById('registerSection').style.display = 'none';
            document.getElementById('loginSection').style.display = 'none';
            document.getElementById('dashboardSection').style.display = 'block';
            document.getElementById('welcomeMsg').innerHTML = '<strong>Welcome, ' + user.name + '!</strong>';
            
            showTab('categories'); // Par défaut montrer les catégories
            getCategories(); // Charger les catégories pour le select
            getProducts(); // Charger les produits
        }
        
        function showTab(tabName) {
            // Cacher tous les tabs
            document.getElementById('categoriesTab').style.display = 'none';
            document.getElementById('productsTab').style.display = 'none';
            
            // Montrer le tab sélectionné
            if (tabName === 'categories') {
                document.getElementById('categoriesTab').style.display = 'block';
            } else if (tabName === 'products') {
                document.getElementById('productsTab').style.display = 'block';
                populateCategorySelects(); // Remplir les selects quand on montre les produits
            }
        }
        
        function showMessage(elementId, message, isError) {
            document.getElementById(elementId).innerHTML = '<p>' + (isError ? '❌ ERROR: ' : '✅ SUCCESS: ') + message + '</p>';
        }
        
        // ========== AUTHENTICATION ==========
        async function register() {
            const name = document.getElementById('regName').value;
            const email = document.getElementById('regEmail').value;
            const password = document.getElementById('regPassword').value;
            
            try {
                const response = await fetch('/api/register', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ name, email, password })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    token = data.token;
                    user = data.user;
                    showMessage('registerMessage', 'Registration successful!', false);
                    setTimeout(() => showDashboard(), 1000);
                } else {
                    showMessage('registerMessage', data.message || 'Registration failed', true);
                }
            } catch (error) {
                showMessage('registerMessage', error.message, true);
            }
        }
        
        async function login() {
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;
            
            try {
                const response = await fetch('/api/login', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ email, password })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    token = data.token;
                    user = data.user;
                    showMessage('loginMessage', 'Login successful!', false);
                    setTimeout(() => showDashboard(), 1000);
                } else {
                    showMessage('loginMessage', data.message || 'Login failed', true);
                }
            } catch (error) {
                showMessage('loginMessage', error.message, true);
            }
        }
        
        async function logout() {
            try {
                await fetch('/api/logout', {
                    method: 'POST',
                    headers: { 
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });
                
                token = '';
                user = null;
                showLogin();
            } catch (error) {
                console.error('Logout error:', error);
            }
        }
        
        // ========== CATEGORIES FUNCTIONS ==========
        async function getCategories() {
            try {
                const response = await fetch('/api/categories', {
                    headers: { 
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    // Stocker les catégories pour les selects
                    if (data.categories) categories = data.categories;
                    else if (data.data) categories = data.data;
                    
                    displayCategories(categories);
                } else {
                    document.getElementById('categoriesList').innerHTML = '<p>Error loading categories: ' + (data.message || 'Unknown error') + '</p>';
                }
            } catch (error) {
                document.getElementById('categoriesList').innerHTML = '<p>Error: ' + error.message + '</p>';
            }
        }
        
        function displayCategories(categories) {
            if (categories.length === 0) {
                document.getElementById('categoriesList').innerHTML = '<p>No categories yet. Create one!</p>';
                return;
            }
            
            let html = '<table border="1"><tr><th>ID</th><th>Name</th><th>Description</th><th>Actions</th></tr>';
            
            categories.forEach(cat => {
                html += '<tr>';
                html += '<td>' + cat.id + '</td>';
                html += '<td>' + cat.name + '</td>';
                html += '<td>' + (cat.description || '') + '</td>';
                html += '<td>';
                html += '<button onclick="editCategory(' + cat.id + ', \'' + cat.name.replace(/'/g, "\\'") + '\', \'' + (cat.description || '').replace(/'/g, "\\'") + '\')">Edit</button> ';
                html += '<button onclick="deleteCategory(' + cat.id + ')">Delete</button>';
                html += '</td>';
                html += '</tr>';
            });
            
            html += '</table>';
            document.getElementById('categoriesList').innerHTML = html;
        }
        
        async function createCategory() {
            const name = document.getElementById('catName').value;
            const desc = document.getElementById('catDesc').value;
            
            if (!name) {
                showMessage('categoryMessage', 'Please enter a category name', true);
                return;
            }
            
            try {
                const response = await fetch('/api/categories', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ name: name, description: desc })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    showMessage('categoryMessage', 'Category created successfully!', false);
                    document.getElementById('catName').value = '';
                    document.getElementById('catDesc').value = '';
                    getCategories(); // Rafraîchir la liste
                } else {
                    showMessage('categoryMessage', data.message || 'Create failed', true);
                }
            } catch (error) {
                showMessage('categoryMessage', error.message, true);
            }
        }
        
        function editCategory(id, name, description) {
            document.getElementById('catEditForm').style.display = 'block';
            document.getElementById('catEditId').value = id;
            document.getElementById('catEditName').value = name;
            document.getElementById('catEditDesc').value = description;
        }
        
        function cancelCatEdit() {
            document.getElementById('catEditForm').style.display = 'none';
            document.getElementById('catEditId').value = '';
            document.getElementById('catEditName').value = '';
            document.getElementById('catEditDesc').value = '';
        }
        
        async function updateCategory() {
            const id = document.getElementById('catEditId').value;
            const name = document.getElementById('catEditName').value;
            const desc = document.getElementById('catEditDesc').value;
            
            if (!name) {
                showMessage('categoryMessage', 'Please enter a category name', true);
                return;
            }
            
            try {
                const response = await fetch('/api/categories/' + id, {
                    method: 'PUT',
                    headers: { 
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ name: name, description: desc })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    showMessage('categoryMessage', 'Category updated successfully!', false);
                    cancelCatEdit();
                    getCategories();
                } else {
                    showMessage('categoryMessage', data.message || 'Update failed', true);
                }
            } catch (error) {
                showMessage('categoryMessage', error.message, true);
            }
        }
        
        async function deleteCategory(id) {
            if (!confirm('Are you sure you want to delete this category?')) {
                return;
            }
            
            try {
                const response = await fetch('/api/categories/' + id, {
                    method: 'DELETE',
                    headers: { 
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    showMessage('categoryMessage', 'Category deleted successfully!', false);
                    getCategories();
                } else {
                    showMessage('categoryMessage', data.message || 'Delete failed', true);
                }
            } catch (error) {
                showMessage('categoryMessage', error.message, true);
            }
        }
        
        // ========== PRODUCTS FUNCTIONS ==========
        // Remplir les selects de catégories
        function populateCategorySelects() {
            const select1 = document.getElementById('prodCategoryId');
            const select2 = document.getElementById('prodEditCategoryId');
            
            // Réinitialiser
            select1.innerHTML = '<option value="">Select Category</option>';
            select2.innerHTML = '<option value="">Select Category</option>';
            
            // Ajouter les options
            categories.forEach(cat => {
                const option1 = document.createElement('option');
                option1.value = cat.id;
                option1.textContent = cat.name;
                select1.appendChild(option1);
                
                const option2 = document.createElement('option');
                option2.value = cat.id;
                option2.textContent = cat.name;
                select2.appendChild(option2);
            });
        }
        
        async function getProducts() {
            try {
                const response = await fetch('/api/products', {
                    headers: { 
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    if (data.products) {
                        displayProducts(data.products);
                    } else if (data.data) {
                        displayProducts(data.data);
                    }
                } else {
                    document.getElementById('productsList').innerHTML = '<p>Error loading products: ' + (data.message || 'Unknown error') + '</p>';
                }
            } catch (error) {
                document.getElementById('productsList').innerHTML = '<p>Error: ' + error.message + '</p>';
            }
        }
        
        function displayProducts(products) {
            if (products.length === 0) {
                document.getElementById('productsList').innerHTML = '<p>No products yet. Create one!</p>';
                return;
            }
            
            let html = '<table border="1"><tr><th>ID</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Image</th><th>QR Code</th><th>Actions</th></tr>';
            
            products.forEach(prod => {
                html += '<tr>';
                html += '<td>' + prod.id + '</td>';
                html += '<td>' + prod.name + '</td>';
                html += '<td>' + (prod.category ? prod.category.name : 'N/A') + '</td>';
                html += '<td>$' + parseFloat(prod.price).toFixed(2) + '</td>';
                html += '<td>' + prod.stock_quantity + '</td>';
                html += '<td>' + (prod.image ? '<img src="' + prod.image + '" width="50">' : 'No image') + '</td>';
                html += '<td>' + (prod.qr_code || 'N/A') + '</td>';
                html += '<td>';
                html += '<button onclick="editProduct(' + prod.id + ', \'' + prod.name.replace(/'/g, "\\'") + '\', ' + prod.category_id + ', ' + prod.price + ', ' + prod.stock_quantity + ', \'' + (prod.image || '').replace(/'/g, "\\'") + '\', \'' + (prod.qr_code || '').replace(/'/g, "\\'") + '\')">Edit</button> ';
                html += '<button onclick="deleteProduct(' + prod.id + ')">Delete</button>';
                html += '</td>';
                html += '</tr>';
            });
            
            html += '</table>';
            document.getElementById('productsList').innerHTML = html;
        }
        
        async function createProduct() {
            const name = document.getElementById('prodName').value;
            const categoryId = document.getElementById('prodCategoryId').value;
            const price = document.getElementById('prodPrice').value;
            const stock = document.getElementById('prodStock').value;
            const image = document.getElementById('prodImage').value;
            const qrCode = document.getElementById('prodQrCode').value;
            
            // Validation simple
            if (!name || !categoryId || !price) {
                showMessage('productMessage', 'Please fill required fields: Name, Category, Price', true);
                return;
            }
            
            try {
                const response = await fetch('/api/products', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ 
                        name, 
                        category_id: categoryId,
                        price: parseFloat(price),
                        stock_quantity: parseInt(stock),
                        image: image || null,
                        qr_code: qrCode || null
                    })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    showMessage('productMessage', 'Product created successfully!', false);
                    
                    // Réinitialiser le formulaire
                    document.getElementById('prodName').value = '';
                    document.getElementById('prodCategoryId').value = '';
                    document.getElementById('prodPrice').value = '99.99';
                    document.getElementById('prodStock').value = '100';
                    document.getElementById('prodImage').value = '';
                    document.getElementById('prodQrCode').value = '';
                    
                    getProducts(); // Rafraîchir la liste
                } else {
                    showMessage('productMessage', data.message || 'Create failed', true);
                }
            } catch (error) {
                showMessage('productMessage', error.message, true);
            }
        }
        
        function editProduct(id, name, categoryId, price, stock, image, qrCode) {
            document.getElementById('prodEditForm').style.display = 'block';
            document.getElementById('prodEditId').value = id;
            document.getElementById('prodEditName').value = name;
            document.getElementById('prodEditCategoryId').value = categoryId;
            document.getElementById('prodEditPrice').value = price;
            document.getElementById('prodEditStock').value = stock;
            document.getElementById('prodEditImage').value = image || '';
            document.getElementById('prodEditQrCode').value = qrCode || '';
        }
        
        function cancelProdEdit() {
            document.getElementById('prodEditForm').style.display = 'none';
            document.getElementById('prodEditId').value = '';
            document.getElementById('prodEditName').value = '';
            document.getElementById('prodEditCategoryId').value = '';
            document.getElementById('prodEditPrice').value = '';
            document.getElementById('prodEditStock').value = '';
            document.getElementById('prodEditImage').value = '';
            document.getElementById('prodEditQrCode').value = '';
        }
        
        async function updateProduct() {
            const id = document.getElementById('prodEditId').value;
            const name = document.getElementById('prodEditName').value;
            const categoryId = document.getElementById('prodEditCategoryId').value;
            const price = document.getElementById('prodEditPrice').value;
            const stock = document.getElementById('prodEditStock').value;
            const image = document.getElementById('prodEditImage').value;
            const qrCode = document.getElementById('prodEditQrCode').value;
            
            // Validation
            if (!name || !categoryId || !price) {
                showMessage('productMessage', 'Please fill required fields: Name, Category, Price', true);
                return;
            }
            
            try {
                const response = await fetch('/api/products/' + id, {
                    method: 'PUT',
                    headers: { 
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ 
                        name, 
                        category_id: categoryId,
                        price: parseFloat(price),
                        stock_quantity: parseInt(stock),
                        image: image || null,
                        qr_code: qrCode || null
                    })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    showMessage('productMessage', 'Product updated successfully!', false);
                    cancelProdEdit();
                    getProducts();
                } else {
                    showMessage('productMessage', data.message || 'Update failed', true);
                }
            } catch (error) {
                showMessage('productMessage', error.message, true);
            }
        }
        
        async function deleteProduct(id) {
            if (!confirm('Are you sure you want to delete this product?')) {
                return;
            }
            
            try {
                const response = await fetch('/api/products/' + id, {
                    method: 'DELETE',
                    headers: { 
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    showMessage('productMessage', 'Product deleted successfully!', false);
                    getProducts();
                } else {
                    showMessage('productMessage', data.message || 'Delete failed', true);
                }
            } catch (error) {
                showMessage('productMessage', error.message, true);
            }
        }
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Portfolio | Photoshop Designer</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-dark text-light">
  <nav class="navbar navbar-expand-lg fixed-top py-3">
    <div class="container">
      <a class="navbar-brand fw-bold" href="index.html">PS<span class="text-accent">.</span></a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navMenu">
        <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link" href="index.html">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="about.html">About</a></li>
          <li class="nav-item"><a class="nav-link" href="services.html">Services</a></li>
          <li class="nav-item"><a class="nav-link active" href="portfolio.php">Portfolio</a></li>
          <li class="nav-item"><a class="nav-link" href="contact.html">Contact</a></li>
          <li class="nav-item"><a class="nav-link text-accent" href="admin/login.php">Admin</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <section class="py-5 mt-5">
    <div class="container">
      <h2 class="text-center mb-5 fw-bold">My Portfolio</h2>

      <!-- Main Category Filters -->
      <div class="d-flex flex-wrap justify-content-center gap-2 mb-4" id="categoryFilters">
        <button class="btn btn-outline-light active" data-category="all">All</button>
        <!-- Will be populated by JS -->
      </div>

      <!-- Subcategory Filters -->
      <div class="d-flex flex-wrap justify-content-center gap-2 mb-5" id="subcategoryFilters">
        <!-- Will be populated when a main category is selected -->
      </div>

      <!-- Portfolio Grid -->
      <div class="row g-4" id="portfolioGrid">
        <div class="col-12 text-center py-5">Loading portfolio...</div>
      </div>
    </div>
  </section>

  <!-- Modal -->
  <div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content bg-secondary border-0">
        <div class="modal-header border-0">
          <h5 class="modal-title" id="modalTitle">Work Title</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-center">
          <img src="" class="img-fluid rounded" id="modalImage" alt="Portfolio item">
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    let allData = null;

    document.addEventListener('DOMContentLoaded', async () => {
      try {
        const res = await fetch('admin/includes/fetch_portfolio.php');
        allData = await res.json();
        renderCategories();
        renderImages(allData.images, 'all');
      } catch (err) {
        document.getElementById('portfolioGrid').innerHTML = 
          '<div class="col-12 text-center text-danger">Failed to load portfolio.</div>';
      }
    });

    function renderCategories() {
      const catFilter = document.getElementById('categoryFilters');
      catFilter.innerHTML = '';
      
      // Add "All" button
      const allBtn = document.createElement('button');
      allBtn.className = 'btn btn-outline-light active';
      allBtn.textContent = 'All';
      allBtn.dataset.category = 'all';
      allBtn.onclick = () => {
        activateButton(allBtn, '#categoryFilters .btn');
        document.getElementById('subcategoryFilters').innerHTML = '';
        renderImages(allData.images, 'all');
      };
      catFilter.appendChild(allBtn);

      // Add main categories
      allData.categories.forEach(cat => {
        const btn = document.createElement('button');
        btn.className = 'btn btn-outline-light';
        btn.textContent = cat.name;
        btn.dataset.category = cat.id;
        btn.onclick = () => {
          activateButton(btn, '#categoryFilters .btn');
          loadSubcategories(cat.id);
        };
        catFilter.appendChild(btn);
      });
    }

    function activateButton(activeBtn, selector) {
      document.querySelectorAll(selector).forEach(btn => btn.classList.remove('active'));
      activeBtn.classList.add('active');
    }

    async function loadSubcategories(catId) {
      const subFilter = document.getElementById('subcategoryFilters');
      subFilter.innerHTML = '';

      // Add "All" subcategory button
      const allSubBtn = document.createElement('button');
      allSubBtn.className = 'btn btn-outline-light active';
      allSubBtn.textContent = 'All';
      allSubBtn.onclick = () => {
        activateButton(allSubBtn, '#subcategoryFilters .btn');
        const filtered = allData.images.filter(img => img.category_id == catId);
        renderImages(filtered, catId);
      };
      subFilter.appendChild(allSubBtn);

      // Fetch and add subcategories for this category
      try {
        const res = await fetch(`admin/includes/fetch_subcategories.php?category_id=${catId}`);
        const subcats = await res.json();
        
        subcats.forEach(sub => {
          const btn = document.createElement('button');
          btn.className = 'btn btn-outline-light';
          btn.textContent = sub.name;
          btn.onclick = () => {
            activateButton(btn, '#subcategoryFilters .btn');
            const filtered = allData.images.filter(img => 
              img.category_id == catId && img.subcategory_id == sub.id
            );
            renderImages(filtered, catId, sub.id);
          };
          subFilter.appendChild(btn);
        });

        // Initially show all items under this main category
        const initialFiltered = allData.images.filter(img => img.category_id == catId);
        renderImages(initialFiltered, catId);

      } catch (err) {
        console.error('Error loading subcategories:', err);
      }
    }

    function renderImages(images, categoryId, subcategoryId = null) {
  const grid = document.getElementById('portfolioGrid');
  
  if (images.length === 0) {
    grid.innerHTML = '<div class="col-12 text-center py-5">No items found.</div>';
    return;
  }

  grid.innerHTML = images.map(img => {
    const safeTitle = img.title.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    const safeSrc = `assets/uploads/${encodeURIComponent(img.filename)}`;
    return `
      <div class="col-md-6 col-lg-4">
        <div class="card bg-dark border border-muted h-100">
          <img src="${safeSrc}" 
               class="card-img-top" 
               alt="${safeTitle}"
               style="aspect-ratio: 4/3; object-fit: cover;">
          <div class="card-body">
            <h5 class="card-title">${safeTitle}</h5>
            <button class="btn btn-sm btn-primary mt-2" 
                    onclick="openModal('${safeTitle}', '${safeSrc}')">
              View
            </button>
          </div>
        </div>
      </div>
    `;
  }).join('');
}

    function openModal(title, src) {
  document.getElementById('modalTitle').textContent = title;
  document.getElementById('modalImage').src = src;
  const modal = new bootstrap.Modal(document.getElementById('imageModal'));
  modal.show();
}
  </script>
</body>
</html>
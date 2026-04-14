// Rotating placeholder text for search bar
const searchInput = document.getElementById('hero-search');
const trendingSearches = [
  'Search for trending treatments, medications or health concerns...',
  'Search for Wegovy...',
  'Search for Finasteride...',
  'Search for Viagra...',
  'Search for HRT...',
  'Search for Hair Loss Treatment...'
];

let currentIndex = 0;

function rotatePlaceholder() {
  if (searchInput && document.activeElement !== searchInput) {
    searchInput.placeholder = trendingSearches[currentIndex];
    currentIndex = (currentIndex + 1) % trendingSearches.length;
  }
}

// Initial rotation
if (searchInput) {
  // Rotate every 3 seconds
  setInterval(rotatePlaceholder, 3000);
  
  // Stop rotation when user focuses on input
  searchInput.addEventListener('focus', () => {
    searchInput.placeholder = 'Search treatments, medications, or health concerns...';
    showSearchDropdown();
  });
  
  // Resume rotation when user leaves input (if empty)
  searchInput.addEventListener('blur', () => {
    if (searchInput.value === '') {
      setTimeout(() => {
        rotatePlaceholder();
        hideSearchDropdown();
      }, 200);
    }
  });

  // Show dropdown on input
  searchInput.addEventListener('input', () => {
    showSearchDropdown();
  });
}

// Search dropdown functionality
function showSearchDropdown() {
  let dropdown = document.getElementById('search-dropdown');
  
  if (!dropdown) {
    dropdown = createSearchDropdown();
    searchInput.parentElement.parentElement.appendChild(dropdown);
  }
  
  dropdown.classList.remove('hidden');
  dropdown.classList.add('search-dropdown-show');
}

function hideSearchDropdown() {
  const dropdown = document.getElementById('search-dropdown');
  if (dropdown) {
    dropdown.classList.remove('search-dropdown-show');
    dropdown.classList.add('hidden');
  }
}

function createSearchDropdown() {
  const dropdown = document.createElement('div');
  dropdown.id = 'search-dropdown';
  dropdown.className = 'absolute bg-white shadow-2xl rounded-2xl border border-gray-100 mt-2 w-full z-50 overflow-hidden hidden';
  
  dropdown.innerHTML = `
    <div class="p-6">
      <div class="mb-6">
        <p class="text-sm font-bold text-neutral-500 uppercase tracking-wider mb-3">Popular Searches</p>
        <div class="space-y-2">
          <a href="/pages/weight-loss" class="flex items-center gap-3 p-3 rounded-lg hover:bg-teal-50 transition-all group">
            <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center group-hover:bg-teal-500 transition-all">
              <svg class="w-5 h-5 text-teal-500 group-hover:text-white" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2C10.3431 2 9 3.34315 9 5C9 6.65685 10.3431 8 12 8C13.6569 8 15 6.65685 15 5C15 3.34315 13.6569 2 12 2Z" fill="currentColor"/>
                <path d="M12 10C8.68629 10 6 12.6863 6 16V22H18V16C18 12.6863 15.3137 10 12 10Z" fill="currentColor"/>
              </svg>
            </div>
            <div class="flex-1">
              <p class="font-semibold text-neutral-900 group-hover:text-teal-500">Wegovy</p>
              <p class="text-xs text-neutral-500">Weight Management</p>
            </div>
            <svg class="w-5 h-5 text-neutral-300 group-hover:text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </a>
          
          <a href="/pages/hair-loss" class="flex items-center gap-3 p-3 rounded-lg hover:bg-teal-50 transition-all group">
            <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center group-hover:bg-teal-500 transition-all">
              <svg class="w-5 h-5 text-teal-500 group-hover:text-white" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2C12 2 8 6 8 10C8 12.2091 9.79086 14 12 14C14.2091 14 16 12.2091 16 10C16 6 12 2 12 2Z" fill="currentColor"/>
                <path d="M12 16C9.79086 16 8 17.7909 8 20V22H16V20C16 17.7909 14.2091 16 12 16Z" fill="currentColor"/>
              </svg>
            </div>
            <div class="flex-1">
              <p class="font-semibold text-neutral-900 group-hover:text-teal-500">Finasteride</p>
              <p class="text-xs text-neutral-500">Hair Loss Treatment</p>
            </div>
            <svg class="w-5 h-5 text-neutral-300 group-hover:text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </a>
          
          <a href="/pages/erectile-dysfunction" class="flex items-center gap-3 p-3 rounded-lg hover:bg-teal-50 transition-all group">
            <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center group-hover:bg-teal-500 transition-all">
              <svg class="w-5 h-5 text-teal-500 group-hover:text-white" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" fill="currentColor"/>
                <path d="M12 14C7.58172 14 4 17.5817 4 22H20C20 17.5817 16.4183 14 12 14Z" fill="currentColor"/>
              </svg>
            </div>
            <div class="flex-1">
              <p class="font-semibold text-neutral-900 group-hover:text-teal-500">Viagra</p>
              <p class="text-xs text-neutral-500">Erectile Dysfunction</p>
            </div>
            <svg class="w-5 h-5 text-neutral-300 group-hover:text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </a>
          
          <a href="/pages/womens-health" class="flex items-center gap-3 p-3 rounded-lg hover:bg-teal-50 transition-all group">
            <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center group-hover:bg-teal-500 transition-all">
              <svg class="w-5 h-5 text-teal-500 group-hover:text-white" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19 3H5C3.89543 3 3 3.89543 3 5V19C3 20.1046 3.89543 21 5 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3Z" stroke="currentColor" stroke-width="2"/>
                <path d="M9 9H15M9 13H15M9 17H13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              </svg>
            </div>
            <div class="flex-1">
              <p class="font-semibold text-neutral-900 group-hover:text-teal-500">HRT</p>
              <p class="text-xs text-neutral-500">Hormone Replacement</p>
            </div>
            <svg class="w-5 h-5 text-neutral-300 group-hover:text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </a>
        </div>
      </div>
      
      <div class="border-t border-gray-100 pt-4">
        <p class="text-sm font-bold text-neutral-500 uppercase tracking-wider mb-3">Browse by Category</p>
        <div class="grid grid-cols-2 gap-2">
          <a href="/pages/weight-loss" class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 transition-all">
            <span class="text-sm text-neutral-700 hover:text-teal-500">Weight Management</span>
          </a>
          <a href="/pages/hair-loss" class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 transition-all">
            <span class="text-sm text-neutral-700 hover:text-teal-500">Hair Loss</span>
          </a>
          <a href="/pages/erectile-dysfunction" class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 transition-all">
            <span class="text-sm text-neutral-700 hover:text-teal-500">Men's Health</span>
          </a>
          <a href="/pages/womens-health" class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 transition-all">
            <span class="text-sm text-neutral-700 hover:text-teal-500">Women's Health</span>
          </a>
        </div>
      </div>
    </div>
  `;
  
  return dropdown;
}

// Close dropdown when clicking outside
document.addEventListener('click', (e) => {
  const dropdown = document.getElementById('search-dropdown');
  if (dropdown && searchInput && !searchInput.contains(e.target) && !dropdown.contains(e.target)) {
    hideSearchDropdown();
  }
});

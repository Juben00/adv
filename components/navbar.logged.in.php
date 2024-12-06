<?php
require_once './classes/account.class.php';

$account = new Account();

$profilePic = $account->getProfilePic($_SESSION['user']['id']);
?>

<nav id="main-nav" class="bg-transparent backdrop-blur-xl z-40 fixed w-full px-2 lg:px-8">
  <!-- logged in -->
  <div class="flex items-center justify-between md:px-4">
    <!-- Left Section -->
    <a href="./" class="flex items-center">
      <img src="./images/icoco_black_ico.png" alt="Icoco_Logo" class="h-[80px]" />
      <span class="text-4xl font-semibold">Icoco <span class="text-sm text-neutral-800">Resort Management
          System</span></span>
    </a>
    <!-- Right Section -->
    <button class="flex items-center space-x-4" id="menutabtrigger">
      <div class="relative flex items-center space-x-2 bg-slate-50 shadow-md rounded-full ps-4 p-1">
        <i class="fas fa-bars text-gray-600"> </i>
        <div class="relative">
          <div class="h-8 w-8 rounded-full bg-black text-white flex items-center justify-center">
            <?php
            if (isset($_SESSION['user']) && empty($profilePic)) {
              echo $_SESSION['user']['firstname'][0];
            } else {
              echo '<img id="profileImage" name="profile_image" src="./' . htmlspecialchars($profilePic) . '" alt="Profile Picture" class="w-full h-full rounded-full object-cover">';
            }
            ?>
          </div>
        </div>
      </div>
    </button>
  </div>
</nav>

<script>
  // Function to update navigation color based on scroll position
  function updateNavColor() {
    const mainNav = document.getElementById("main-nav");
    const firstSection = document.querySelector(".first-section");
    const bottomSearch = document.getElementById("bottom-search");
    const navButtons = mainNav.querySelectorAll(".nav-buttons");

    if (firstSection) {
      const firstSectionBottom = firstSection.offsetTop + firstSection.offsetHeight;
      const scrollPosition = window.scrollY;

      // Update nav buttons' color based on scroll position
      if (scrollPosition >= firstSectionBottom - mainNav.offsetHeight / 2) {
        navButtons.forEach((button) => {
          button.style.color = "#4A5568"; // Dark color
        });
      } else {
        navButtons.forEach((button) => {
          button.style.color = "white";
        });
      }

      // Show/hide the search bar based on scroll position
      if (scrollPosition >= firstSectionBottom - mainNav.offsetHeight / 2) {
        bottomSearch.classList.add("flex");
        bottomSearch.classList.remove("hidden");
      } else {
        bottomSearch.classList.add("hidden");
        bottomSearch.classList.remove("flex");
      }
    }
  }

  // Add scroll event listener
  window.addEventListener("scroll", () => updateNavColor());
  // Initial call to set correct color on page load
  updateNavColor();

  document.getElementById('guestsButton').addEventListener('click', function () {
    const dropdown = document.getElementById('guestDropdown');
    dropdown.classList.toggle('hidden');
  });

  // Update guest count when + or - buttons are clicked
  document.querySelectorAll('.guest-plus, .guest-minus').forEach(button => {
    button.addEventListener('click', function () {
      const countElement = this.parentElement.querySelector('.guest-count');
      let count = parseInt(countElement.textContent);

      if (this.classList.contains('guest-plus')) {
        count++;
      } else if (count > 0) {
        count--;
      }

      countElement.textContent = count;
      updateTotalGuestCount();
    });
  });

  function updateTotalGuestCount() {
    const counts = Array.from(document.querySelectorAll('.guest-count'))
      .map(el => parseInt(el.textContent));
    const total = counts.reduce((sum, current) => sum + current, 0);

    const guestCountElement = document.getElementById('guestCount');
    if (total === 0) {
      guestCountElement.textContent = 'Add guests';
    } else {
      guestCountElement.textContent = `${total} guest${total !== 1 ? 's' : ''}`;
    }
  }

  // Close dropdown when clicking outside
  document.addEventListener('click', function (event) {
    const dropdown = document.getElementById('guestDropdown');
    const guestsButton = document.getElementById('guestsButton');

    if (!dropdown.contains(event.target) && !guestsButton.contains(event.target)) {
      dropdown.classList.add('hidden');
    }
  });
</script>
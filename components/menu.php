<div class="fixed inset-0 bg-black bg-opacity-30 z-40 hidden" id="menutab">
  <div
    class="z-50 w-64 bg-white rounded-lg shadow-lg absolute right-8 md:right-12 top-20 overflow-hidden"
  >
    <ul class="divide-y divide-gray-200 hover:cursor-pointer">
      <li
        class="px-4 py-3 flex justify-between items-center hover:bg-slate-100 hover:font-semibold duration-150"
        data-url="rent-history.php"
        id="profileBtn"
      >
        <span>Profile</span>
      </li>
      <li
        class="px-4 py-3 flex justify-between items-center hover:bg-slate-100 hover:font-semibold duration-150"
      >
        <span>Notifications</span>
      </li>
      <li
        class="px-4 py-3 flex justify-between items-center hover:bg-slate-100 hover:font-semibold duration-150"
        data-url="host-account-application.php"
        id="hostAccountBtn"
      >
        <span>Host Account</span>
      </li>
      <li
        class="px-4 py-3 flex justify-between items-center hover:bg-slate-100 hover:font-semibold duration-150" 
      >
        <span>Help Center</span>
      </li>
      <li
        class="px-4 py-3 flex justify-between items-center hover:bg-slate-100 hover:font-semibold duration-150"
      >
        <span>Settings</span>
      </li>
      <li
        class="px-4 py-3 flex justify-between items-center hover:bg-slate-100 hover:font-semibold duration-150"
        id="logoutBtn"
      >
        <span>Log out</span>
      </li>
    </ul>
  </div>
</div>

<script>
  const logoutBtn = document.getElementById("logoutBtn");
  const profileBtn = document.getElementById("profileBtn");

  function logout() {
    window.location.href = "logout.php";
  }

  logoutBtn.addEventListener("click", () => {
    confirmshowModal("Are you sure you want to log out?", logout, "./images/black_ico.png");
  });

  profileBtn.addEventListener("click", () => {
    window.location.href = profileBtn.getAttribute("data-url");
  });
</script>

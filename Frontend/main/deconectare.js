function deconectare() {
    if (confirm("Are you sure you want to sign out?")) {
      document.cookie = "token=; expires=Thu, 01 Jan 2020 00:00:00 UTC; path=/;";
      window.location.href = "../sign/sign.html";
    }
}
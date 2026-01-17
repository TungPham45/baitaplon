function toggleSearchMessage() {
    const box = document.getElementById("chatSearch");
    const input = box.querySelector("input");

        if (!box || !input) return;

        if (box.style.display === "block") {
            box.style.display = "none";
        } else {
            box.style.display = "block";
            input.focus();
        }
}

function closeSearchMessage() {
    const box = document.getElementById("chatSearch");
    const input = document.getElementById("messageSearchInput");

    if (box) box.style.display = "none";
    if (input) input.value = "";

    window.location.href = "/baitaplon/chat";
}
document.addEventListener("DOMContentLoaded", function () {
    const input = document.getElementById("messageSearchInput");
    const searchBox = document.getElementById("chatSearch");

    if (!input || !searchBox) return;

    input.addEventListener("keydown", function (e) {

        if (e.key === "Enter") {
            return; // browser submit form
        }

        if (e.key === "Escape") {
            e.preventDefault();

            searchBox.style.display = "none";

            window.location.href = "/baitaplon/chat";
        }
    });
});
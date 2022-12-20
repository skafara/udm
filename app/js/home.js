document.getElementById("searchForm").addEventListener("submit", function (e) {
    e.preventDefault();
    const searchInput = document.getElementById("searchInput");
    let location = ".?page=search";
    if (searchInput.value !== "") {
        location += "&q=" + searchInput.value;
    }
    window.location = location;
})

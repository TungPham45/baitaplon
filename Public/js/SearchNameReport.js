function filterTable() {
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    
    const table = document.getElementById('reportTable');
    const tr = table.getElementsByTagName('tr');

    for (let i = 1; i < tr.length; i++) {
        let row = tr[i];
        
        // Bỏ qua nếu là dòng trống hoặc header (đề phòng)
        if (row.getElementsByTagName('td').length === 0) continue; 
        if (row.classList.contains('empty-state')) continue;

        const reporterName = row.querySelector('.reporter-name')?.textContent.toLowerCase() || '';
        const reportedName = row.querySelector('.reported-name')?.textContent.toLowerCase() || '';
        const rowStatus = row.getAttribute('data-status');

        const matchesSearch = reporterName.includes(searchInput) || reportedName.includes(searchInput);
        const matchesStatus = (statusFilter === 'all') || (rowStatus === statusFilter);

        if (matchesSearch && matchesStatus) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    }
}
document.addEventListener("DOMContentLoaded", function () {
    // DELETE button
    const deleteButtons = document.querySelectorAll(".deleteBtn");
    deleteButtons.forEach(button => {
        button.addEventListener("click", function () {
            const userId = this.dataset.userid;

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you really want to delete this account?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('../Api/approveUser.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `action=delete&user_id=${encodeURIComponent(userId)}`
                    })
                    .then(res => res.text())
                    .then(() => {
                        const row = button.closest("tr");
                        row.remove();
                        Swal.fire('Deleted!', 'The account has been deleted.', 'success');
                    })
                    .catch(() => Swal.fire('Error!', 'Something went wrong.', 'error'));
                }
            });
        });
    });

    // APPROVE button
    const approveButtons = document.querySelectorAll(".approveBtn");

    approveButtons.forEach(button => {
        button.addEventListener("click", function () {
            const userId = this.dataset.userid;
            const row = this.closest("tr");
            const roleSelect = row.querySelector(".roleSelect");
            const role = roleSelect.value;

            if (!role) {
                Swal.fire('Error', 'Please select a role first!', 'warning');
                return;
            }

            Swal.fire({
                title: 'Confirm Approval',
                text: `Are you sure you want to approve this user as ${role}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#aaa',
                confirmButtonText: 'Yes, approve',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // AJAX request
                    fetch('../Api/approveUser.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `action=approve&user_id=${encodeURIComponent(userId)}&role=${encodeURIComponent(role)}`
                    })
                    .then(res => res.text())
                    .then(() => {
                        // Remove row after approve
                        row.remove();
                        Swal.fire('Approved!', 'The account has been approved.', 'success');
                    })
                    .catch(() => Swal.fire('Error!', 'Something went wrong.', 'error'));
                }
            });
        });
    });
});
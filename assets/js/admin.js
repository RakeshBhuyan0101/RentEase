$(document).ready(function () {

    let cachedProperties = [];
    let cachedUsers = [];

    // ==========================================
    // 1. TAB SWITCHING
    // ==========================================
    window.switchAdminTab = function (tab) {
        $(".dash-tab").removeClass("active");
        $("#tabAdminPropertiesSection").addClass("d-none");
        $("#tabAdminUsersSection").addClass("d-none");
        $("#tabAdminStatsSection").addClass("d-none");

        if (tab === "properties") {
            $("#tabAdminPropsBtn").addClass("active");
            $("#tabAdminPropertiesSection").removeClass("d-none");
            $("#adminMainHeading").text("Property Approvals");
            loadAdminProperties();
        } else if (tab === "users") {
            $("#tabAdminUsersBtn").addClass("active");
            $("#tabAdminUsersSection").removeClass("d-none");
            $("#adminMainHeading").text("User Management");
            loadAdminUsers();
        } else if (tab === "stats") {
            $("#tabAdminStatsBtn").addClass("active");
            $("#tabAdminStatsSection").removeClass("d-none");
            $("#adminMainHeading").text("Platform Overview");
            loadAdminStats();
        }
    };


    // ==========================================
    // 2. PROPERTIES MANAGEMENT
    // ==========================================
    function loadAdminProperties() {
        $.ajax({
            url: "/RentEase/api/admin/getProperties.php",
            type: "GET",
            dataType: "json",
            success: function (res) {
                if (!res.success) {
                    console.error("Failed to load properties:", res.message);
                    return;
                }

                cachedProperties = res.properties || [];
                const counts = res.counts || { all: 0, pending: 0, approved: 0, rejected: 0 };

                // Update badge and filter pills
                $("#adminPendingPropBadge").text(counts.pending || 0);
                $("#adminPropFilterPills").html(`
                    <button class="filter-pill active" onclick="filterAdminProperties('all', this)">All (${counts.all})</button>
                    <button class="filter-pill" onclick="filterAdminProperties('pending', this)">Pending (${counts.pending})</button>
                    <button class="filter-pill" onclick="filterAdminProperties('approved', this)">Approved (${counts.approved})</button>
                    <button class="filter-pill" onclick="filterAdminProperties('rejected', this)">Rejected (${counts.rejected})</button>
                `);

                renderProperties(cachedProperties);
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error loading properties:", error);
            }
        });
    }

    function renderProperties(list) {
        const tbody = $("#adminPropertiesTableBody");
        tbody.empty();

        if (!list || list.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="bi bi-buildings fs-1 d-block mb-2 text-secondary"></i>
                        No properties found matching current criteria.
                    </td>
                </tr>
            `);
            return;
        }

        list.forEach(function (prop) {
            const status = (prop.approval_status || "pending").toLowerCase();
            const statusClass = "badge-" + status;
            const initial = (prop.title || "P").charAt(0).toUpperCase();
            const rentFormatted = Number(prop.monthly_rent || 0).toLocaleString("en-IN");

            let actionHtml = `
                <div class="d-flex justify-content-end gap-1">
                    <button class="btn btn-approve-sm btn-action-prop" data-id="${prop.property_id}" data-action="approved">Approve</button>
                    <button class="btn btn-reject-sm btn-action-prop" data-id="${prop.property_id}" data-action="rejected">Reject</button>
                </div>
            `;

            if (status === "approved") {
                actionHtml = `
                    <div class="d-flex justify-content-end gap-1">
                        <button class="btn btn-reject-sm btn-action-prop" data-id="${prop.property_id}" data-action="rejected">Reject</button>
                    </div>
                `;
            } else if (status === "rejected") {
                actionHtml = `
                    <div class="d-flex justify-content-end gap-1">
                        <button class="btn btn-approve-sm btn-action-prop" data-id="${prop.property_id}" data-action="approved">Approve</button>
                    </div>
                `;
            }

            const row = `
                <tr data-status="${escapeHtml(status)}">
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div class="prop-avatar avatar-blue">${initial}</div>
                            <div>
                                <div class="prop-title">${escapeHtml(prop.title)}</div>
                                <div class="prop-sub">${escapeHtml(prop.locality || "")}, ${escapeHtml(prop.city || "")}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="fw-bold">${escapeHtml(prop.owner_name)}</div>
                        <div class="text-muted small">${escapeHtml(prop.owner_email)}</div>
                    </td>
                    <td>
                        <div class="fw-bold">₹${rentFormatted}<span class="text-muted fw-normal">/mo</span></div>
                        <div class="text-muted small">${escapeHtml(prop.bhk_type)} • ${escapeHtml(prop.furnishing)}</div>
                    </td>
                    <td>
                        <span class="badge-status ${statusClass}">${escapeHtml(status.toUpperCase())}</span>
                    </td>
                    <td class="text-end">
                        ${actionHtml}
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
    }

    // Property Approve / Reject Handler
    $(document).on("click", ".btn-action-prop", function () {
        const propId = $(this).data("id");
        const action = $(this).data("action");

        $.ajax({
            url: "/RentEase/api/admin/updatePropertyStatus.php",
            type: "POST",
            data: {
                property_id: propId,
                status: action
            },
            dataType: "json",
            success: function (res) {
                if (res.success) {
                    loadAdminProperties();
                } else {
                    alert("Error: " + res.message);
                }
            },
            error: function (xhr, status, error) {
                alert("Request error: " + error);
            }
        });
    });


    // ==========================================
    // 3. USER MANAGEMENT
    // ==========================================
    function loadAdminUsers() {
        $.ajax({
            url: "/RentEase/api/admin/getUsers.php",
            type: "GET",
            dataType: "json",
            success: function (res) {
                if (!res.success) {
                    console.error("Failed to load users:", res.message);
                    return;
                }

                cachedUsers = res.users || [];
                const counts = res.counts || { all: 0, tenant: 0, owner: 0, active: 0, blocked: 0 };

                $("#adminUserFilterPills").html(`
                    <button class="filter-pill active" onclick="filterAdminUsers('all', this)">All (${counts.all})</button>
                    <button class="filter-pill" onclick="filterAdminUsers('tenant', this)">Tenants (${counts.tenant})</button>
                    <button class="filter-pill" onclick="filterAdminUsers('owner', this)">Owners (${counts.owner})</button>
                    <button class="filter-pill" onclick="filterAdminUsers('active', this)">Active (${counts.active})</button>
                    <button class="filter-pill" onclick="filterAdminUsers('blocked', this)">Blocked (${counts.blocked})</button>
                `);

                renderUsers(cachedUsers);
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error loading users:", error);
            }
        });
    }

    function renderUsers(list) {
        const tbody = $("#adminUsersTableBody");
        tbody.empty();

        if (!list || list.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="bi bi-people fs-1 d-block mb-2 text-secondary"></i>
                        No users found.
                    </td>
                </tr>
            `);
            return;
        }

        list.forEach(function (user) {
            const role = (user.role || "tenant").toLowerCase();
            const status = (user.STATUS || "active").toLowerCase();
            const initial = (user.full_name || "U").charAt(0).toUpperCase();

            const isBlocked = status === "blocked";
            const btnClass = isBlocked ? "btn-success" : "btn-outline-danger";
            const btnText = isBlocked ? "Unblock" : "Block";
            const newStatus = isBlocked ? "active" : "blocked";

            let actionHtml = `
                <button class="btn btn-sm ${btnClass} btn-toggle-block" data-id="${user.user_id}" data-status="${newStatus}">
                    ${btnText}
                </button>
            `;

            if (role === "admin") {
                actionHtml = `<span class="badge bg-secondary">System Admin</span>`;
            }

            const row = `
                <tr data-role="${escapeHtml(role)}" data-status="${escapeHtml(status)}">
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div class="prop-avatar avatar-purple">${initial}</div>
                            <div>
                                <div class="prop-title">${escapeHtml(user.full_name)}</div>
                                <div class="prop-sub">${escapeHtml(user.email)}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="fw-semibold">${escapeHtml(user.phone || "—")}</div>
                        <div class="text-muted small">Joined: ${escapeHtml(user.created_at.substring(0, 10))}</div>
                    </td>
                    <td>
                        <span class="badge-status badge-${role}">${escapeHtml(role.toUpperCase())}</span>
                    </td>
                    <td>
                        <span class="badge-status badge-${status}">${escapeHtml(status.toUpperCase())}</span>
                    </td>
                    <td class="text-end">
                        ${actionHtml}
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
    }

    // User Block / Unblock Toggle Handler
    $(document).on("click", ".btn-toggle-block", function () {
        const userId = $(this).data("id");
        const status = $(this).data("status");
        const actionVerb = status === "blocked" ? "block" : "unblock";

        if (!confirm(`Are you sure you want to ${actionVerb} this user?`)) {
            return;
        }

        $.ajax({
            url: "/RentEase/api/admin/updateUserStatus.php",
            type: "POST",
            data: {
                user_id: userId,
                status: status
            },
            dataType: "json",
            success: function (res) {
                if (res.success) {
                    loadAdminUsers();
                } else {
                    alert("Error: " + res.message);
                }
            },
            error: function (xhr, status, error) {
                alert("Request error: " + error);
            }
        });
    });


    // ==========================================
    // 4. PLATFORM STATS
    // ==========================================
    function loadAdminStats() {
        $.ajax({
            url: "/RentEase/api/admin/getDashboardStats.php",
            type: "GET",
            dataType: "json",
            success: function (res) {
                if (!res.success) return;
                const stats = res.stats;

                $("#statTotalUsers").text(stats.users.total);
                $("#statUserBreakdown").text(`${stats.users.tenants} Tenants • ${stats.users.owners} Owners • ${stats.users.blocked} Blocked`);

                $("#statTotalProps").text(stats.properties.total);
                $("#statPropBreakdown").text(`${stats.properties.approved} Approved • ${stats.properties.pending} Pending • ${stats.properties.rejected} Rejected`);

                $("#statPendingProps").text(stats.properties.pending);

                $("#statTotalBookings").text(stats.bookings.total);
                $("#statBookingBreakdown").text(`${stats.bookings.pending} Pending • ${stats.bookings.approved} Approved`);
            }
        });
    }


    // ==========================================
    // 5. LIVE SEARCH & PILL FILTERS
    // ==========================================
    $("#adminPropSearchInput").on("input", function () {
        const q = $(this).val().toLowerCase().trim();
        $("#adminPropertiesTableBody tr").each(function () {
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.indexOf(q) > -1);
        });
    });

    window.filterAdminProperties = function (status, elem) {
        $("#adminPropFilterPills .filter-pill").removeClass("active");
        $(elem).addClass("active");

        $("#adminPropertiesTableBody tr").each(function () {
            const rowStatus = $(this).data("status");
            if (status === "all" || rowStatus === status) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    };

    $("#adminUserSearchInput").on("input", function () {
        const q = $(this).val().toLowerCase().trim();
        $("#adminUsersTableBody tr").each(function () {
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.indexOf(q) > -1);
        });
    });

    window.filterAdminUsers = function (type, elem) {
        $("#adminUserFilterPills .filter-pill").removeClass("active");
        $(elem).addClass("active");

        $("#adminUsersTableBody tr").each(function () {
            const rowRole = $(this).data("role");
            const rowStatus = $(this).data("status");

            if (type === "all") {
                $(this).show();
            } else if (type === "tenant" || type === "owner") {
                $(this).toggle(rowRole === type);
            } else if (type === "active" || type === "blocked") {
                $(this).toggle(rowStatus === type);
            }
        });
    };


    // Helper for HTML escaping
    function escapeHtml(str) {
        if (!str) return "";
        return String(str)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Initial load
    loadAdminProperties();

});


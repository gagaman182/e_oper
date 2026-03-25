<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<style>
/* Slide-in animation for modal */
.slide-in-modal {
    transform: translateX(-100%);
    transition: transform 0.4s ease-out;
}

.modal.show .slide-in-modal {
    transform: translateX(0);
}
</style>
<?php
include './main_top_panel_head.php';
include('./db/connect_pmk.php'); // Oracle connection
include('./db/connection.php');  // MySQL connection (mysqli)

// กำหนดประเภทและ SQL map
$type = isset($_GET['type']) ? $_GET['type'] : 'L';
$column_map = [
    'L' => "SELECT LABCODE AS CODE, REF_CODE, LABNAME AS NAME, FUND_UNIT_PRICE, COPAY_UNIT_PRICE, MIN_PRICE, MAX_PRICE FROM LABCODES",
    'X' => "SELECT XRAY_CODE AS CODE, REF_CODE, NAME, FUND_UNIT_PRICE, COPAY_UNIT_PRICE, MIN_PRICE, MAX_PRICE FROM XRAY_CODES",
    'P' => "SELECT OPER_CODE AS CODE, REF_CODE, NAME, FUND_UNIT_PRICE, COPAY_UNIT_PRICE, MIN_PRICE, MAX_PRICE FROM OPERATION_CODES",
    'S' => "SELECT CODE, REF_CODE, NAME, FUND_UNIT_PRICE, COPAY_UNIT_PRICE, MIN_PRICE, MAX_PRICE FROM SERVICE_CODES",
    'D' => "SELECT CODE, REF_CODE, NAME, FUND_UNIT_PRICE, INTEND_FUND_UNIT_PRICE AS COPAY_UNIT_PRICE, SELL_UNIT_PRICE AS MIN_PRICE, IPD_SELL_UNIT_PRICE AS MAX_PRICE FROM DRUGCODES",
    'O' => "SELECT MISC_CODE AS CODE, REF_CODE, NAME, FUND_UNIT_PRICE, COPAY_UNIT_PRICE, MIN_PRICE, MAX_PRICE FROM MISC_CODES"
];
if (!isset($column_map[$type])) $type = 'L';
?>

<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-primary"><i class="bi bi-clipboard-data"></i> รายการข้อมูล PMK
            (<?php echo htmlspecialchars($type); ?>)</h2>
        <div class="d-flex align-items-center gap-2">
            <label class="mb-0">ประเภท:</label>
            <select id="typeSelect" class="form-select form-select-sm shadow">
                <option value="L" <?= $type == 'L' ? 'selected' : '' ?>>L - Lab</option>
                <option value="X" <?= $type == 'X' ? 'selected' : '' ?>>X - X-ray</option>
                <option value="P" <?= $type == 'P' ? 'selected' : '' ?>>P - ผ่าตัด</option>
                <option value="S" <?= $type == 'S' ? 'selected' : '' ?>>S - ค่าบริการ</option>
                <option value="D" <?= $type == 'D' ? 'selected' : '' ?>>D - ค่ายา</option>
                <option value="O" <?= $type == 'O' ? 'selected' : '' ?>>O - ค่าอื่น ๆ</option>
            </select>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <table id="dataTable" class="table table-hover table-striped w-100 text-center"></table>
        </div>
    </div>
</div>

<!-- Modal แสดงรายละเอียด pmk_items -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable slide-in-modal">
        <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="detailModalLabel">📋 รายละเอียดรายการที่แก้ไขแล้ว</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="ปิด"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-hover table-striped mb-0">
                    <tbody id="detailBody">
                        <!-- เติมข้อมูลด้วย JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include './modal_form.php'; ?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let dataTable;

function loadDataTable(type) {
    if (dataTable) {
        dataTable.destroy();
        $('#dataTable').empty();
    }

    dataTable = $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'data_sys_process_server.php',
            type: 'GET',
            data: function(d) {
                d.type = type;
            }
        },
        columns: [{
                title: '#',
                data: 'no',
                className: 'text-center'
            },
            {
                title: 'รหัส',
                data: 'code',
                className: 'text-center'
            },
            {
                title: 'REF_CODE',
                data: 'ref_code',
                className: 'text-center'
            },
            {
                title: 'ชื่อรายการ',
                data: 'name',
                className: 'text-center'
            },
            {
                title: 'ราคาทุน',
                data: 'fund_price',
                className: 'text-center'
            },
            {
                title: 'ร่วมจ่าย',
                data: 'copay_price',
                className: 'text-center'
            },
            {
                title: 'ราคาต่ำสุด',
                data: 'min_price',
                className: 'text-center'
            },
            {
                title: 'ราคาสูงสุด',
                data: 'max_price',
                className: 'text-center'
            },
            {
                title: 'จัดการ',
                data: null,
                className: 'text-center',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return `
        <button 
            class="btn btn-sm btn-primary d-flex align-items-center gap-1 shadow-sm rounded-pill px-3" 
            style="font-size: 0.875rem;"
            onclick="openEditModal('${row.code}', '${row.type}')"
            title="แก้ไขรายการ">
            ✏️ แก้ไข
        </button>
    `;
                }
            },
            {
                title: 'สถานะ',
                data: null,
                className: 'text-center',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return `<button class="btn btn-sm btn-outline-secondary status-btn" id="status-btn-${row.code}" data-code="${row.code}" data-type="${row.type}" disabled>กำลังโหลด...</button>`;
                }
            }
        ],
        language: {
            url: 'js/th.json'
        },
        responsive: true,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, 'ทั้งหมด']
        ],
        dom: '<"row mb-2"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
            '<"row"<"col-sm-12"tr>>' +
            '<"row mt-2"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        drawCallback: function(settings) {
            $('.status-btn').each(function() {
                const btn = $(this);
                const code = btn.data('code');
                const type = btn.data('type');
                checkActiveStatus(code, type, btn);
            });
        }
    });
}

$('#typeSelect').on('change', function() {
    loadDataTable(this.value);
});

$(document).ready(function() {
    loadDataTable($('#typeSelect').val());
});

function checkActiveStatus(code, type, button) {
    $.ajax({
        url: 'check_pmk_item.php',
        type: 'POST',
        dataType: 'json',
        data: {
            code: code,
            type: type
        },
        success: function(res) {
            if (res.exists) {
                button.removeClass('btn-outline-secondary').addClass('btn-success').text('Active').prop(
                    'disabled', false);
                button.off('click').on('click', function() {
                    showItemDetails(code, type);
                });
            } else {
                button.hide();
            }
        },
        error: function() {
            button.hide();
        }
    });
}

function showItemDetails(code, type) {
    $.ajax({
        url: 'get_item.php',
        type: 'POST',
        data: {
            code: code,
            type: type
        },
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                let d = res.data;
                Swal.fire({
                    title: `🧾 รายการ: ${d.name}`,
                    html: `
                        <div class="container text-start">
                            <div class="mb-3">
                                <h5 class="text-primary">
                                    <i class="fa fa-scalpel me-2"></i> ${d.name}
                                </h5>
                                <p><i class="fa fa-barcode me-2 text-muted"></i> <strong>รหัส:</strong> ${d.code}</p>
                                <p><i class="fa fa-key me-2 text-secondary"></i> <strong>Ref Code:</strong> ${d.gpo_code || '-'}</p>
                                <p><i class="fa fa-info-circle me-2 text-warning"></i> <strong>ข้อบ่งชี้:</strong> ${d.indication || '-'}</p>
                                <p><i class="fa fa-cube me-2 text-success"></i> <strong>หน่วยละ:</strong> ${d.unit || '-'}</p>
                                <p><i class="fa fa-cube me-2 text-success"></i> <strong>ICD-9 :</strong> ${d.icd_9_code || '-'}</p>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="border rounded p-3 bg-light">
                                        <h6 class="text-dark mb-2"><i class="fa fa-tags me-2 text-primary"></i> ราคาทั่วไป</h6>
                                        <ul class="list-unstyled mb-0">
                                            <li><strong>ราคาทุน:</strong> ${d.fund_price}</li>
                                            <li><strong>ราคาร่วมจ่าย:</strong> ${d.copay_price}</li>
                                            <li><strong>ราคาเริ่มต้น:</strong> ${d.min_price}</li>
                                            <li><strong>ราคาสูงสุด:</strong> ${d.max_price}</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="border rounded p-3 bg-light">
                                        <h6 class="text-dark mb-2"><i class="fa fa-coins me-2 text-success"></i> ยอดส่งเบิก สกส.</h6>
                                        <p class="fs-5 fw-bold text-end text-primary mb-0">${d.price_sks}</p>
                                    </div>
                                </div>
                            </div>

                            <h6 class="text-secondary mb-2"><i class="fa fa-file-invoice-dollar me-2"></i> ราคาตามสิทธิ์การรักษา</h6>
                            <table class="table table-sm table-bordered text-center align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>สิทธิ์</th>
                                        <th>ผู้ป่วยนอก (OPD)</th>
                                        <th>ผู้ป่วยใน (IPD)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-start"><i class="fa fa-user-tie me-1 text-primary"></i> ข้าราชการ</td>
                                        <td>${d.price_officer_opd}</td>
                                        <td>${d.price_officer_ipd}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-start"><i class="fa fa-hospital-user me-1 text-success"></i> UC</td>
                                        <td>${d.price_uc_opd}</td>
                                        <td>${d.price_uc_ipd}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-start"><i class="fa fa-briefcase-medical me-1 text-info"></i> ประกันสังคม</td>
                                        <td>${d.price_sss_opd}</td>
                                        <td>${d.price_sss_ipd}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-start"><i class="fa fa-globe-asia me-1 text-danger"></i> ต่างชาติ</td>
                                        <td>${d.price_foreign_opd}</td>
                                        <td>${d.price_foreign_ipd}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    `,
                    icon: 'info',
                    confirmButtonText: 'ปิด',
                    customClass: {
                        popup: 'swal-wide'
                    }
                });
            } else {
                Swal.fire('ไม่พบข้อมูล', '', 'warning');
            }
        },
        error: function() {
            Swal.fire('เกิดข้อผิดพลาดในการเชื่อมต่อ', '', 'error');
        }
    });
}

function openEditModal(code, type) {
    if (!code || !type) {
        Swal.fire('ข้อผิดพลาด', 'ไม่พบรหัสหรือประเภทของรายการ', 'error');
        return;
    }

    $.ajax({
        url: 'get_item.php',
        type: 'POST',
        dataType: 'json',
        data: {
            code,
            type
        },
        success: function(response) {
            if (response.status === 'success' && response.data) {
                const d = response.data;

                $('#recordId').val(d.id || '');
                $('#code').val(d.code);
                $('#type').val(d.type);
                $('#name').val(d.name);
                $('#fund_price').val(d.fund_price);
                $('#copay_price').val(d.copay_price);
                $('#min_price').val(d.min_price);
                $('#max_price').val(d.max_price);
                $('#ref_code').val(d.gpo_code);
                $('#unit').val(d.unit);
                $('#indication').val(d.indication);
                $('#price_uc_opd').val(d.price_uc_opd);
                $('#price_uc_ipd').val(d.price_uc_ipd);
                $('#price_officer_opd').val(d.price_officer_opd);
                $('#price_officer_ipd').val(d.price_officer_ipd);
                $('#price_sss_opd').val(d.price_sss_opd);
                $('#price_sss_ipd').val(d.price_sss_ipd);
                $('#price_foreign_opd').val(d.price_foreign_opd);
                $('#price_foreign_opd').val(d.price_foreign_opd);
                $('#price_foreign_ipd').val(d.price_forprice_foreign_ipdeign_opd);
                $('#price_sks').val(d.price_sks);
                // $('#gpo_code').val(d.gpo_code);

                // ✅ Bootstrap 5 Modal ใช้แบบนี้
                const modalElement = document.getElementById('modalForm');
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            } else {
                Swal.fire('ไม่พบข้อมูล', response.message || 'ไม่พบรายการในระบบ', 'warning');
            }
        },
        error: function() {
            Swal.fire('ผิดพลาด', 'ไม่สามารถโหลดข้อมูลได้', 'error');
        }
    });
}

// ส่งฟอร์มผ่าน AJAX
$(document).ready(function() {
    $('#itemForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: 'save_item.php',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                            icon: 'success',
                            title: 'สำเร็จ',
                            text: response.message,
                            timer: 1200,
                            showConfirmButton: false
                        })
                        .then(() => {
                            bootstrap.Modal.getInstance(document.getElementById(
                                'modalForm')).hide();
                            $('#dataTable').DataTable().ajax.reload(null, false);
                        });
                } else {
                    Swal.fire('ผิดพลาด', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('ผิดพลาด', 'เกิดข้อผิดพลาดในการส่งข้อมูล', 'error');
            }
        });
    });
});
</script>
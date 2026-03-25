<?php
require_once("./db/connect_pmk.php");         // ข้อมูลหลักจากระบบ
include('main_top_panel_head.php');          // ส่วนหัว
?>
<style>
.swal-wide {
    width: 950px !important;
    max-width: 98vw;
}
</style>

<!-- ส่วนแสดงผล -->
<div class="container-fluid py-4">
    <div class="card p-4">
        <h4 class="mb-3 text-primary">
            <i class="fa fa-vials me-2"></i> รายการ LAB
        </h4>
        <div class="table-responsive">
            <table id="labTable" class="table table-bordered table-hover table-striped nowrap" style="width:100%">
                <thead class="table-light text-center align-middle">
                    <tr>
                        <th style="width: 4%;">#</th>
                        <th style="width: 9%;">รหัส</th>
                        <th style="width: 10%;">รหัสกรมบัญชีกลาง</th>
                        <th style="width: 9%;">รหัสสปสช</th>
                        <th style="width: 28%;">ชื่อรายการ</th>
                        <th style="width: 10%;">ตรวจสอบ</th>
                        <th style="width: 8%;">ราคาทุน</th>
                        <th style="width: 8%;">ร่วมจ่าย</th>
                        <th style="width: 7%;">ต่ำสุด</th>
                        <th style="width: 7%;">สูงสุด</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
var existingCodes = [];

// โหลดรายการรหัสที่มีอยู่ใน pmk_items
$.ajax({
    url: 'check_lab_exists.php',
    type: 'GET',
    dataType: 'json',
    async: false,
    success: function(res) {
        if (res.status === 'success') {
            existingCodes = res.codes || [];
        }
    },
    error: function() {
        Swal.fire('ผิดพลาด', 'ไม่สามารถโหลดข้อมูลรายการที่มีได้', 'error');
    }
});

$(document).ready(function() {
    $('#labTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: './lab_data_server.php',
        responsive: true,
        pageLength: 20,
        order: [
            [4, 'asc']
        ],
        dom: '<"d-flex justify-content-between align-items-center mb-3"Bf>rt<"d-flex justify-content-between align-items-center mt-3"lip>',
        buttons: [{
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel me-1"></i> Excel',
                className: 'btn btn-success btn-sm rounded-pill'
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print me-1"></i> พิมพ์',
                className: 'btn btn-outline-dark btn-sm rounded-pill'
            }
        ],
        language: {
            processing: "กำลังโหลดข้อมูล...",
            search: "ค้นหา:",
            lengthMenu: "แสดง _MENU_ รายการ",
            info: "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
            infoEmpty: "ไม่มีข้อมูล",
            infoFiltered: "(กรองจากทั้งหมด _MAX_ รายการ)",
            paginate: {
                first: "หน้าแรก",
                previous: "ก่อนหน้า",
                next: "ถัดไป",
                last: "หน้าสุดท้าย"
            }
        },
        columns: [{
                data: 0
            },
            {
                data: 1
            },
            {
                data: 2
            },
            {
                data: 3
            },
            {
                data: 4
            },
            {
                data: null,
                className: 'text-center',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    var code = row[1];
                    var isEnabled = existingCodes.indexOf(code) !== -1;
                    return `<button
    class="btn btn-sm ${isEnabled ? 'btn-success' : 'btn-primary'} d-flex align-items-center gap-1 shadow-sm rounded-pill px-3"
    style="font-size: 0.875rem;"
    onclick="openStatusModal('${code}')" ${isEnabled ? '' : 'disabled'}
    title="ตรวจสอบข้อบ่งชี้และราคาตามสิทธิ์การรักษา">
    <i class="fa fa-search-plus"></i>
    CHECK ข้อมูลสิทธิ์
</button>`;
                }
            },
            {
                data: 5
            },
            {
                data: 6
            },
            {
                data: 7
            },
            {
                data: 8
            }
        ],
        columnDefs: [{
                targets: [0, 1, 2, 3, 5],
                className: 'text-center'
            },
            {
                targets: [6, 7, 8, 9],
                className: 'text-end'
            }
        ]
    });
});

function openStatusModal(code) {
    console.log("ส่ง code ไปยัง PHP:", code); // << ลองดูค่าที่ console
    $.ajax({
        url: 'get_item_lab.php',
        type: 'POST',
        data: {
            code: code
        },
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                let d = res.data;
                Swal.fire({
                    title: `🧪 รายการ: ${d.name}`,
                    html: `
                        <div class="container text-start">
                            <div class="mb-3">
                                <h5 class="text-primary">
                                    <i class="fa fa-vial me-2"></i> ${d.name}
                                </h5>
                                <p><i class="fa fa-barcode me-2 text-muted"></i> <strong>รหัส:</strong> ${d.code}</p>
                                <p><i class="fa fa-key me-2 text-secondary"></i> <strong>รหัสกรมบัญชีกลาง:</strong> ${d.ref_code || '-'}</p>
                                <p><i class="fa fa-key me-2 text-info"></i> <strong>รหัสสปสช (30 บาท):</strong> ${d.code_30 || '-'}</p>
                                <p><i class="fa fa-info-circle me-2 text-warning"></i> <strong>ข้อบ่งชี้:</strong> ${d.indication || '-'}</p>
                                <p><i class="fa fa-cube me-2 text-success"></i> <strong>หน่วยละ:</strong> ${d.unit || '-'}</p>
                                <p><i class="fa fa-cube me-2 text-success"></i> <strong>ICD-9 :</strong> ${d.icd_9_code || '-'}</p>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="border rounded p-3 bg-light">
                                        <h6 class="text-dark mb-2"><i class="fa fa-tags me-2 text-primary"></i> ราคาทั่วไป</h6>
                                        <ul class="list-unstyled mb-0">
                                            <li><strong>ราคาทุน:</strong> ${d.cost_price}</li>
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
</script>
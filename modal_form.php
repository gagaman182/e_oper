<!-- modal_form.php -->
<div class="modal fade" id="modalForm" tabindex="-1" aria-labelledby="modalFormLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form id="itemForm" class="modal-content" autocomplete="off">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalFormLabel">🔧 เพิ่ม / แก้ไข รายการ PMK</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="ปิด"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="recordId" name="id" value="">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="code" class="form-label">รหัส (Code) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="code" name="code" required readonly>
                    </div>

                    <div class="col-md-6">
                        <label for="ref_code" class="form-label">รหัสกรมบัญชีกลาง</label>
                        <input type="text" class="form-control" id="ref_code" name="ref_code" maxlength="50">
                    </div>

                    <div class="col-md-6">
                        <label for="type" class="form-label">ประเภท (Type) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="type" name="type" readonly>
                    </div>

                    <div class="col-md-6">
                        <label for="name" class="form-label">ชื่อรายการ (Name) <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required maxlength="255">
                    </div>

                    <div class="col-md-4">
                        <label for="fund_price" class="form-label">ราคาทุน</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="fund_price" name="fund_price"
                            value="0">
                    </div>

                    <div class="col-md-4">
                        <label for="copay_price" class="form-label">ร่วมจ่าย</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="copay_price"
                            name="copay_price" value="0">
                    </div>

                    <div class="col-md-4">
                        <label for="min_price" class="form-label">ราคาต่ำสุด</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="min_price" name="min_price"
                            value="0">
                    </div>

                    <div class="col-md-4">
                        <label for="max_price" class="form-label">ราคาสูงสุด</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="max_price" name="max_price"
                            value="0">
                    </div>

                    <div class="col-md-4">
                        <label for="opd_price" class="form-label">UC OPD ราคา</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="opd_price" name="price_uc_opd"
                            value="0">
                    </div>

                    <div class="col-md-4">
                        <label for="ipd_price" class="form-label">UC IPD ราคา</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="ipd_price" name="price_uc_ipd"
                            value="0">
                    </div>

                    <div class="col-md-4">
                        <label for="gov_price" class="form-label">ข้าราชการ OPD</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="gov_price"
                            name="price_officer_opd" value="0">
                    </div>

                    <div class="col-md-4">
                        <label for="gov_ipd_price" class="form-label">ข้าราชการ IPD</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="gov_ipd_price"
                            name="price_officer_ipd" value="0">
                    </div>

                    <div class="col-md-4">
                        <label for="sso_price" class="form-label">ประกันสังคม OPD</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="sso_price"
                            name="price_sss_opd" value="0">
                    </div>

                    <div class="col-md-4">
                        <label for="sso_ipd_price" class="form-label">ประกันสังคม IPD</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="sso_ipd_price"
                            name="price_sss_ipd" value="0">
                    </div>

                    <div class="col-md-4">
                        <label for="foreigner_price" class="form-label">ราคาต่างชาติ OPD</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="foreigner_price"
                            name="price_foreign_opd" value="0">
                    </div>

                    <div class="col-md-4">
                        <label for="foreigner_ipd_price" class="form-label">ราคาต่างชาติ IPD</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="foreigner_ipd_price"
                            name="price_foreign_ipd" value="0">
                    </div>

                    <div class="col-md-4">
                        <label for="price_sks" class="form-label">ยอดส่งเบิก สกส.</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="price_sks" name="price_sks"
                            value="0">
                    </div>

                    <div class="col-md-4">
                        <label for="unit" class="form-label">หน่วยละ</label>
                        <input type="text" class="form-control" id="unit" name="unit" maxlength="50">
                    </div>

                    <div class="col-md-4">
                        <label for="gpo_code" class="form-label">ICD-9</label>
                        <input type="text" class="form-control" id="icd_9" name="icd_9" maxlength="20">
                    </div>

                    <div class="col-md-12">
                        <label for="indication" class="form-label">ข้อบ่งชี้</label>
                        <textarea class="form-control" id="indication" name="indication" rows="3"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">💾 บันทึก</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">❌ ปิด</button>
            </div>
        </form>
    </div>
</div>
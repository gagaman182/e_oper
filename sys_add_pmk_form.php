<?php
// เชื่อมต่อ MySQL
include('db/connection.php');
// เชื่อมต่อ Oracle
include('db/connect_pmk.php');
// กลุ่ม Oracle Table Mapping
$oracleTables = [
  'lab' => 'LABCODES',
  'xray' => 'XRAY_CODES',
  'surgery' => 'OPERATION_CODES',
  'drug' => 'DRUGCODES',
  'service' => 'SERVICE_CODES',
  'other' => 'MISC_CODES'
];

// ฟังก์ชันดึงรายการจาก Oracle ตามกลุ่ม
function getOracleItems($conn_oracle, $group, $oracleTables) {
  $table = $oracleTables[$group];
  $sql = "SELECT CODE, NAME, REQUESTED_UNIT, REF_CODE, FUND_UNIT_PRICE, MAX_PRICE 
          FROM $table WHERE DEL_FLAG IS NULL ORDER BY NAME";
  $stid = oci_parse($conn_oracle, $sql);
  oci_execute($stid);
  
  $rows = [];
  while (($row = oci_fetch_assoc($stid)) != false) {
    $rows[] = $row;
  }
  return $rows;
}

// เริ่ม Session และตรวจสอบ POST
session_start();
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // รับข้อมูลจากฟอร์ม
  $group_code = $_POST['group_code'];
  $item_code = $_POST['item_code'];
  $item_name = $_POST['item_name'];
  $unit = $_POST['unit'];
  $ref_code = $_POST['ref_code'];
  $fund_unit_price = $_POST['fund_unit_price'];
  $max_price = $_POST['max_price'];
  $gov_right = $_POST['gov_right'];
  $uc_right = $_POST['uc_right'];
  $oks_right = $_POST['oks_right'];
  $foreign_right = $_POST['foreign_right'];
  $indication = $_POST['indication'];

  // เช็คว่ามี id แปลว่าแก้ไข
  if (isset($_POST['id']) && $_POST['id'] != '') {
    $id = intval($_POST['id']);
    // อัปเดตข้อมูล
    $stmt = $mysqli->prepare("UPDATE medical_items SET
      group_code=?, item_code=?, item_name=?, unit=?, ref_code=?, fund_unit_price=?, max_price=?,
      gov_right=?, uc_right=?, oks_right=?, foreign_right=?, indication=?, updated_at=NOW()
      WHERE id=?");
    $stmt->bind_param("ssssdddddddsi", $group_code, $item_code, $item_name, $unit, $ref_code, $fund_unit_price, $max_price,
      $gov_right, $uc_right, $oks_right, $foreign_right, $indication, $id);
    $stmt->execute();
    $message = "อัปเดตข้อมูลสำเร็จ";
  } else {
    // เพิ่มข้อมูลใหม่
    $stmt = $mysqli->prepare("INSERT INTO medical_items
      (group_code, item_code, item_name, unit, ref_code, fund_unit_price, max_price,
      gov_right, uc_right, oks_right, foreign_right, indication)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssdddddddd", $group_code, $item_code, $item_name, $unit, $ref_code, $fund_unit_price, $max_price,
      $gov_right, $uc_right, $oks_right, $foreign_right, $indication);
    $stmt->execute();
    $message = "เพิ่มข้อมูลสำเร็จ";
  }
}

// ดึงรายการวัสดุทั้งหมดแสดงในตาราง
$result_all = $mysqli->query("SELECT * FROM medical_items ORDER BY id DESC");

// ค่าเริ่มต้นกลุ่ม
$selectedGroup = isset($_POST['group_code']) ? $_POST['group_code'] : 'lab';
$oracleItems = getOracleItems($conn_oracle, $selectedGroup, $oracleTables);
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>จัดการวัสดุทางการแพทย์</title>
<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<!-- FontAwesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
<!-- Niramit font -->
<style>
  body { font-family: 'Niramit', sans-serif; font-size: 18px; }
</style>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="container py-4">
  <h2 class="mb-4 text-primary"><i class="fa fa-cubes me-2"></i> จัดการวัสดุทางการแพทย์</h2>

  <?php if ($message): ?>
  <script>
    Swal.fire({
      icon: 'success',
      title: 'สำเร็จ',
      text: '<?php echo $message; ?>',
      timer: 2000,
      showConfirmButton: false
    });
  </script>
  <?php endif; ?>

  <form method="post" id="medicalForm">
    <div class="row mb-3">
      <div class="col-md-3">
        <label for="group_code" class="form-label">กลุ่มรายการวัสดุ</label>
        <select name="group_code" id="group_code" class="form-select" onchange="this.form.submit()">
          <option value="lab" <?php echo $selectedGroup=='lab'?'selected':''; ?>>Lab</option>
          <option value="xray" <?php echo $selectedGroup=='xray'?'selected':''; ?>>X-ray</option>
          <option value="surgery" <?php echo $selectedGroup=='surgery'?'selected':''; ?>>ค่าผ่าตัด</option>
          <option value="drug" <?php echo $selectedGroup=='drug'?'selected':''; ?>>ค่ายา</option>
          <option value="service" <?php echo $selectedGroup=='service'?'selected':''; ?>>ค่าบริการ</option>
          <option value="other" <?php echo $selectedGroup=='other'?'selected':''; ?>>ค่าอื่น ๆ</option>
        </select>
      </div>
      <div class="col-md-3">
        <label for="item_code" class="form-label">รหัสจาก Oracle</label>
        <select name="item_code" id="item_code" class="form-select" required onchange="updateOracleFields()">
          <option value="">-- เลือกรายการ --</option>
          <?php foreach ($oracleItems as $item): ?>
            <option value="<?php echo htmlspecialchars($item['CODE']); ?>" 
              data-name="<?php echo htmlspecialchars($item['NAME']); ?>" 
              data-unit="<?php echo htmlspecialchars($item['REQUESTED_UNIT']); ?>" 
              data-ref="<?php echo htmlspecialchars($item['REF_CODE']); ?>" 
              data-fund_price="<?php echo $item['FUND_UNIT_PRICE']; ?>"
              data-max_price="<?php echo $item['MAX_PRICE']; ?>"
              <?php if(isset($_POST['item_code']) && $_POST['item_code'] == $item['CODE']) echo 'selected'; ?>>
              <?php echo htmlspecialchars($item['CODE'].' - '.$item['NAME']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6">
        <label for="item_name" class="form-label">ชื่อรายการ</label>
        <input type="text" name="item_name" id="item_name" class="form-control" required 
          value="<?php echo isset($_POST['item_name']) ? htmlspecialchars($_POST['item_name']) : ''; ?>" />
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-2">
        <label for="unit" class="form-label">หน่วยนับ</label>
        <input type="text" name="unit" id="unit" class="form-control" readonly
          value="<?php echo isset($_POST['unit']) ? htmlspecialchars($_POST['unit']) : ''; ?>" />
      </div>
      <div class="col-md-2">
        <label for="ref_code" class="form-label">รหัสกลุ่มบัญชีกลาง</label>
        <input type="text" name="ref_code" id="ref_code" class="form-control" readonly
          value="<?php echo isset($_POST['ref_code']) ? htmlspecialchars($_POST['ref_code']) : ''; ?>" />
      </div>
      <div class="col-md-2">
        <label for="fund_unit_price" class="form-label">ราคาทุน</label>
        <input type="number" step="0.01" name="fund_unit_price" id="fund_unit_price" class="form-control" 
          value="<?php echo isset($_POST['fund_unit_price']) ? htmlspecialchars($_POST['fund_unit_price']) : ''; ?>" />
      </div>
      <div class="col-md-2">
        <label for="max_price" class="form-label">ราคาขาย</label>
        <input type="number" step="0.01" name="max_price" id="max_price" class="form-control" 
          value="<?php echo isset($_POST['max_price']) ? htmlspecialchars($_POST['max_price']) : ''; ?>" />
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-2">
        <label for="gov_right" class="form-label">สิทธิข้าราชการ</label>
        <input type="number" step="0.01" name="gov_right" id="gov_right" class="form-control"
          value="<?php echo isset($_POST['gov_right']) ? htmlspecialchars($_POST['gov_right']) : '0'; ?>" />
      </div>
      <div class="col-md-2">
        <label for="uc_right" class="form-label">สิทธิ UC</label>
        <input type="number" step="0.01" name="uc_right" id="uc_right" class="form-control"
          value="<?php echo isset($_POST['uc_right']) ? htmlspecialchars($_POST['uc_right']) : '0'; ?>" />
      </div>
      <div class="col-md-2">
        <label for="oks_right" class="form-label">สิทธิ ปกส</label>
        <input type="number" step="0.01" name="oks_right" id="oks_right" class="form-control"
          value="<?php echo isset($_POST['oks_right']) ? htmlspecialchars($_POST['oks_right']) : '0'; ?>" />
      </div>
      <div class="col-md-2">
        <label for="foreign_right" class="form-label">สิทธิ ต่างชาติ</label>
        <input type="number" step="0.01" name="foreign_right" id="foreign_right" class="form-control"
          value="<?php echo isset($_POST['foreign_right']) ? htmlspecialchars($_POST['foreign_right']) : '0'; ?>" />
      </div>
    </div>

    <div class="mb-3">
      <label for="indication" class="form-label">ข้อบ่งชี้</label>
      <textarea name="indication" id="indication" class="form-control" rows="3"><?php echo isset($_POST['indication']) ? htmlspecialchars($_POST['indication']) : ''; ?></textarea>
    </div>

    <input type="hidden" name="id" id="id" value="<?php echo isset($_POST['id']) ? intval($_POST['id']) : ''; ?>" />

    <button type="submit" class="btn btn-primary"><i class="fa fa-save me-2"></i> บันทึกข้อมูล</button>
  </form>

  <hr class="my-4" />

  <h4>รายการวัสดุทั้งหมด</h4>
  <table class="table table-striped table-bordered" style="font-size: 18px;">
    <thead class="table-primary text-center">
      <tr>
        <th>ลำดับ</th>
        <th>กลุ่ม</th>
        <th>รหัส</th>
        <th>ชื่อรายการ</th>
        <th>หน่วย</th>
        <th>ราคาทุน</th>
        <th>ราคาขาย</th>
        <th>สิทธิข้าราชการ</th>
        <th>สิทธิ UC</th>
        <th>สิทธิ ปกส</th>
        <th>สิทธิ ต่างชาติ</th>
        <th>ข้อบ่งชี้</th>
      </tr>
    </thead>
    <tbody>
      <?php $i=1; while ($row = $result_all->fetch_assoc()) : ?>
      <tr>
        <td class="text-center"><?php echo $i++; ?></td>
        <td class="text-center"><?php echo htmlspecialchars($row['group_code']); ?></td>
        <td class="text-center"><?php echo htmlspecialchars($row['item_code']); ?></td>
        <td><?php echo htmlspecialchars($row['item_name']); ?></td>
        <td><?php echo htmlspecialchars($row['unit']); ?></td>
        <td class="text-end"><?php echo number_format($row['fund_unit_price'], 2); ?></td>
        <td class="text-end"><?php echo number_format($row['max_price'], 2); ?></td>
        <td class="text-end"><?php echo number_format($row['gov_right'], 2); ?></td>
        <td class="text-end"><?php echo number_format($row['uc_right'], 2); ?></td>
        <td class="text-end"><?php echo number_format($row['oks_right'], 2); ?></td>
        <td class="text-end"><?php echo number_format($row['foreign_right'], 2); ?></td>
        <td><?php echo nl2br(htmlspecialchars($row['indication'])); ?></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<script>
// ฟังก์ชันเติมข้อมูลในฟอร์มจาก Oracle เมื่อเลือก item_code
function updateOracleFields() {
  var select = document.getElementById('item_code');
  var option = select.options[select.selectedIndex];
  if (option.value === "") {
    document.getElementById('item_name').value = "";
    document.getElementById('unit').value = "";
    document.getElementById('ref_code').value = "";
    document.getElementById('fund_unit_price').value = "";
    document.getElementById('max_price').value = "";
  } else {
    document.getElementById('item_name').value = option.getAttribute('data-name');
    document.getElementById('unit').value = option.getAttribute('data-unit');
    document.getElementById('ref_code').value = option.getAttribute('data-ref');
    document.getElementById('fund_unit_price').value = option.getAttribute('data-fund_price');
    document.getElementById('max_price').value = option.getAttribute('data-max_price');
  }
}
</script>

</body>
</html>

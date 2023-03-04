<?php
session_start();
$uid = $_SESSION['uid'];
require '../connection.php';
require '../../../vendor/autoload.php';
$response = array();
if (isset($_FILES['file'])) {

  $excel = $_FILES['file']['tmp_name'];
  $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($excel);
  $worksheet = $spreadsheet->getActiveSheet();
  $rows = $worksheet->toArray();


  $columns = [
    'Infrastructure' => 'criteria',
    'System' => 'location',
    'Server Name' => 'hostname',
    'Request' => 'prob_descript',
    'Date Requested' => 'date_requested',
    'Time Requested' => 'time_requested',
    'Requested by' => 'fullname',
    'Approved By' => 'reciever',
    'Date Approved' => 'rec_date',
    'Date Verified' => 'ver2_date'
  ];

    // ============================================================================================================================
    // This will used to validate if the columns in excel is matched with the $columns[] Array 
    // Read the first row of the excel file
    $firstRow = $spreadsheet->getActiveSheet()->rangeToArray('A1:'.$spreadsheet->getActiveSheet()->getHighestColumn().'1')[0];
    // Check if each required column header exists in the first row of the excel file
    $missingColumns = [];
    foreach($columns as $excelColumn => $tableColumn) {
    if(!in_array($excelColumn, $firstRow)) {
        $missingColumns[] = $excelColumn;
    }
    }

    // If any required column headers are missing, display an error message
    if(count($missingColumns) > 0) {
    die("The following required column headers are missing from the excel file: " . implode(", ", $missingColumns));
    }
    // ============================================================================================================================

  $header = array_shift($rows);
  foreach ($header as $key => $value) {
    if (array_key_exists($value, $columns)) {
      $header[$key] = $columns[$value];
    }
  }

  $data = [];
  foreach ($rows as $row) {
    $data[] = array_combine($header, $row);
  }


  if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
  }

  foreach ($data as $item) {
    $criteria = $item['criteria'];
    $location = $item['location'];
    $hostname = $item['hostname'];
    $prob_descript = $item['prob_descript'];
    $date_requested = $item['date_requested'];
    $time_requested = $item['time_requested'];
    $fullname = $item['fullname'];

    $datetime = new DateTime("$date_requested $time_requested");
    $date_requested = $datetime->format('Y-m-d H:i:s');

    $sql = "INSERT INTO tbl_tci (uid, form_type, criteria, location, hostname, prob_descript, date_requested, fullname, status, reciever, rec_status, rec_date, verifier_2, ver2_status, ver2_date)
            VALUES ('$uid','2','$criteria', '$location', '$hostname', '$prob_descript', '$date_requested', '$fullname','7', '$fullname', '1', '$date_requested', '$fullname', '1','$date_requested')";
    $query = mysqli_query($conn, $sql);

  }

  if ($query) {
    // echo "New record created successfully";
    $response['status'] = 'created';
    $response['message'] = 'New record created successfully';
  } else {
    $response['status'] = 'error';
    $response['message'] = 'Error: '.$sql.mysqli_error($conn);
    // echo "Error: " . $sql . "<br>" . mysqli_error($conn);
  }
  echo json_encode($response);

  mysqli_close($conn);
}
?>
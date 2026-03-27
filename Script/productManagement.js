
let API = '/BSIT-2E-G1-Group1-EduTrack/Api/productManagement.php';
let table;
let rowIndex = null;

// ================= TYPE =================
function checkType(select) {
    const isOther = select.value === "Other";
    if (isOther) {
        $("#otherTypeContainer").show();
    } else {
        $("#otherTypeContainer").hide();
        $("#otherType").val("");
    }
}

function checkSizeByType() {
    let type = $("#type").val();

    if (type === "Uniform" || type === "Merchandise") {
        $("#size").prop("disabled", false).val("");
    } else {
        $("#size").val("None").prop("disabled", true);
    }
}

// ================= DATATABLE =================
$(document).ready(function () {
    table = $('#myTable').DataTable({
        ajax: {
            url: API,
            type: "POST",
            data: { action: "get" },
            dataSrc: "data"
        },
        dom: '<"top-bar"fB>t',
        buttons: [
            {
                extend: 'collection',
                text: 'Export',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                container: $('body')
            }
        ],
        paging: false,
        info: false,
        lengthChange: false,
        ordering: false
    });
});
// ================= MODAL =================
function openModal() {
  var modal = new bootstrap.Modal(document.getElementById('productModal'));
  modal.show();
}

function closeModal() {
  var modalEl = document.getElementById('productModal');
  var modal = bootstrap.Modal.getInstance(modalEl);
  if(modal) modal.hide();
  clearForm();
}

// ================= VALIDATION =================
function validateForm() {
  let isValid = true;

  $("small.text-danger").text("");

  let code = $("#code").val().trim();
  let typeSel = $("#type").val();
  let other = $("#otherType").val().trim();
  let dept = $("#dept").val();
  let price = $("#price").val().trim();
  let size = $("#size").val();

  if (!code) {
    $("#codeError").text("Product code is required");
    isValid = false;
  }

  if (!typeSel) {
    $("#typeError").text("Type is required");
    isValid = false;
  } else if (typeSel === "Other" && !other) {
    $("#otherTypeError").text("Please specify the type");
    isValid = false;
  }

  if (!dept) {
    $("#deptError").text("Department is required");
    isValid = false;
  }

if ($("#incoming").val() === "" || parseInt($("#incoming").val()) < 0) {
  $("#incomingError").text("Enter a valid incoming quantity");
  isValid = false;
}


  // Price
  if (!price) {
    $("#priceError").text("Price is required");
    isValid = false;
  } else if (isNaN(price) || parseFloat(price) < 0) {
    $("#priceError").text("Enter a valid price");
    isValid = false;
  }

  return isValid;
}

$("#productForm input, #productForm select").on("input change", function() {
  let id = $(this).attr("id");
  $("#" + id + "Error").text("");
});

var productModalEl = document.getElementById('productModal');

productModalEl.addEventListener('hidden.bs.modal', function () {
  clearForm();
  $(".text-danger").text("");
  rowIndex = null;
});

// ================= SAVE =================
function store() {

  if (!validateForm()) return;

  let code = $("#code").val().trim();
  let product_type = $("#type").val();
  let other = $("#otherType").val().trim();
  let size = $("#size").val();
  let department = $("#dept").val();
  let incoming_qty = parseInt($("#incoming").val()) || 0;
  let price = $("#price").val().trim();

  let type = (product_type === "Other") ? other : product_type;

  if (type !== "Uniform" && type !== "Merchandise") {
    size = "None";
  }

  let exists = false;
  table.rows().every(function () {
    if (this.data()[0] === code) exists = true;
  });

  if (exists && rowIndex === null) {
    $("#codeError").text("Product code already exists");
    return;
  }

 let status = incoming_qty > 0 ? "On the Way":"Successfully";

  let payload = {
    code: code,
    product_type: type,
    size: size,
    department: department,
    incoming_qty: incoming_qty,
    quantity: 0,
    price: price,
    status: status
  };

  let action = (rowIndex === null) ? "store" : "update";

  $.ajax({
    url: API, 
    type: 'POST',
    data:{
      payload: JSON.stringify(payload),
      action : action
    },
    dataType: 'json',
    success: function(response) {
      alert(response.message);
      closeModal();
      lucide.createIcons();
       table.ajax.reload();
    },
    
error: function(err) {
  alert(err.message);   
}
  });
}

// ================= EDIT =================
function update(btn) {
  let row = $(btn).closest('tr');
  rowIndex = table.row(row);
  let data = table.row(row).data();

  openModal();

  $("#code").val(data[0]).prop("disabled", true);
  $("#type").val(data[1]).prop("disabled", false);

  
  $("#size").val(data[2]).prop("disabled", false);
  $("#dept").val(data[3]).prop("disabled", false);
  $("#price").val(data[6]).prop("disabled", false);
  $("#incoming").val(data[5]).prop("disabled", false);


}

// ================= RECEIVE STOCK =================
function receiveStock(btn) {
  let row = $(btn).closest('tr');
  let data = table.row(row).data();

  let code = data[0];

  if (confirm("Receive this stock?")) {
    $.ajax({
      url: API,
      type: "POST",
      data: {
        action: "receive",
        code: code
      },
      dataType: "json",
      success: function(res) {
        alert(res.message);

        table.ajax.reload();
      },
      error: function(err) {
        console.log(err.responseText);
        alert("Error receiving stock");
      }
    });
  }
}
// ================= DELETE =================
function deleteRow(btn) {
  let row = $(btn).closest('tr');
  let data = table.row(row).data();

  let code = data[0];

  if (confirm("Delete this product?")) {
    $.ajax({
      url: API,
      type: "POST",
      data: {
        action: "drop",
        code: code
      },
      dataType: "json",
      success: function(res) {
        alert(res.message);

        table.ajax.reload();
      },
      error: function(err) {
        console.log(err.responseText);
        alert("Error deleting product");
      }
    });
  }
}

function clearForm() {
  $("#productForm")[0].reset();
  $("#otherTypeContainer").hide(); 
  $(".text-danger").text(""); 
  $("input, select").prop("disabled", false);
}

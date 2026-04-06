
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

  Swal.fire({
    title: 'Processing...',
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });

  $.ajax({
    url: API, 
    type: 'POST',
    data:{
      payload: JSON.stringify(payload),
      action : action
    },
    dataType: 'json',
    success: function(response) {
      Swal.fire({
        icon: 'success',
        title: 'Success',
        text: response.message,
        confirmButtonColor: '#2a5d9f'
      });

      closeModal();
      lucide.createIcons();
       table.ajax.reload();
    },
    
error: function(err) {
  Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Something went wrong!'
      });   
}
  });
}

// ================= EDIT =================
function update(btn) {
  let row = $(btn).closest('tr');
  rowIndex = table.row(row);
  let data = table.row(row).data();
  let incomingVal = data[5] || 0;
  

  openModal();

  $("#code").val(data[0]).prop("disabled", true);
  $("#type").val(data[1]).prop("disabled", false);

  
  $("#size").val(data[2]).prop("disabled", false);
  $("#dept").val(data[3]).prop("disabled", false);
  $("#price").val(data[6]).prop("disabled", false);
  $("#incoming").val(incomingVal).prop("disabled", false);

  $("#incoming").off("focus").on("focus", function() {
    if ($(this).val() === "0") {
      $(this).val("");
    }
  });
}

// ================= RECEIVE STOCK =================
function receiveStock(btn) {
  let row = $(btn).closest('tr');
  let data = table.row(row).data();
  let code = data[0];
  let incoming_qty = parseInt(data[5]) || 0; 

  if (incoming_qty <= 0) {
    Swal.fire({
      icon: 'info',
      title: 'No incoming stock',
      text: 'There is no incoming stock to receive for this product.'
    });
    return;
  }
  Swal.fire({
    title: 'Receive stock?',
    text: "This will add incoming stock.",
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#2a5d9f',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, receive it'
    }).then((result) => {
      if (result.isConfirmed) {
      $.ajax({
        url: API,
        type: "POST",
        data: {
          action: "receive",
          code: code
        },
        dataType: "json",
        success: function(res) {
            Swal.fire({
              icon: 'success',
              title: 'Success',
              text: res.message
            });

          table.ajax.reload();
        },
        error: function(err) {
          Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Error receiving stock'
            });
        }
      });
    }
    });
}
// ================= DELETE =================
function deleteRow(btn) {
  let row = $(btn).closest('tr');
  let data = table.row(row).data();

  let code = data[0];

  Swal.fire({
    title: 'Are you sure?',
    text: "Delete this product?",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, delete it!'
  }).then((result) => {
    if (result.isConfirmed) {
    $.ajax({
      url: API,
      type: "POST",
      data: {
        action: "drop",
        code: code
      },
      dataType: "json",
      success: function(res) {
          Swal.fire({
            icon: 'success',
            title: 'Deleted!',
            text: res.message
          });

        table.ajax.reload();
      },
      error: function() {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error deleting product'
           });
        }
      });

    }
  });
}

function clearForm() {
  $("#productForm")[0].reset();
  $("#otherTypeContainer").hide(); 
  $(".text-danger").text(""); 
  $("input, select").prop("disabled", false);
}

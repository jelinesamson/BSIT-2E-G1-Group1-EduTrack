let table;
let rowIndex = null;
let counter = 1;

$(document).ready(function () {
  table = $('#myTable').DataTable({
    dom: '<"top-bar"fB>t',
    buttons: [
      { extend: 'collection', text: 'Export', buttons: ['copy', 'csv', 'excel', 'pdf', 'print'] }
    ],
    paging: false,
    info: false,
    lengthChange: false,
    ordering: false
  });
});

function openModal() { document.getElementById("productModal").style.display = "block"; }
function closeModal() { document.getElementById("productModal").style.display = "none"; clearForm(); rowIndex = null; }

function checkType(select) { document.getElementById("otherType").style.display = (select.value === "Other") ? "block" : "none"; }

function saveProduct() {
  let code = document.getElementById("code").value;
  let typeSel = document.getElementById("type").value;
  let other = document.getElementById("otherType").value;
  let size = document.getElementById("size").value;
  let dept = document.getElementById("dept").value;
  let price = document.getElementById("price").value;
  let type = (typeSel === "Other") ? other : typeSel;

  if (!code || !type || !size || !dept || !price) { alert("Fill all fields!"); return; }

  if (rowIndex === null) {
    table.row.add([
      counter++, code, type, size, dept, price,
      '<button class="edit-btn" onclick="editRow(this)">Edit</button>' +
      '<button class="delete-btn" onclick="deleteRow(this)">Delete</button>'
    ]).draw();
  } else {
    table.row(rowIndex).data([
      table.row(rowIndex).data()[0], code, type, size, dept, price,
      '<button class="edit-btn" onclick="editRow(this)">Edit</button>' +
      '<button class="delete-btn" onclick="deleteRow(this)">Delete</button>'
    ]).draw();
  }
  closeModal();
}

function editRow(btn) {
  let row = $(btn).closest('tr');
  rowIndex = table.row(row);
  let data = table.row(row).data();

  openModal();
  document.getElementById("code").value = data[1];
  document.getElementById("size").value = data[3];
  document.getElementById("dept").value = data[4];
  document.getElementById("price").value = data[5];

  let type = data[2];
  let select = document.getElementById("type");

  if ([...select.options].some(o => o.value === type)) {
    select.value = type; document.getElementById("otherType").style.display = "none";
  } else {
    select.value = "Other"; document.getElementById("otherType").style.display = "block"; document.getElementById("otherType").value = type;
  }
}

function deleteRow(btn) { if(confirm("Delete this product?")) { table.row($(btn).closest('tr')).remove().draw(); } }

function clearForm() {
  document.getElementById("code").value = "";
  document.getElementById("type").value = "";
  document.getElementById("otherType").value = "";
  document.getElementById("otherType").style.display = "none";
  document.getElementById("size").value = "";
  document.getElementById("dept").value = "";
  document.getElementById("price").value = "";
}
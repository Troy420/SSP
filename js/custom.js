// Close Button on Details Page
function closemodal() {
  let detailWrapper = document.getElementsByClassName("detail-wrapper")[0];
  let mother = document.getElementById("featured");
  // detailWrapper.parentNode.removeChild(detailWrapper);
  mother.removeChild(detailWrapper);
}

function detailsmodal(id) {
  let data = { id: id };

  jQuery.ajax({
    method: "POST",
    url: "/ssp/includes/details_page.php",
    data: data,
    success: function(data) {
      jQuery("#featured").append(data);
    },
    error: function() {
      alert("data error");
    }
  });
}

function update_cart(mode, edit_id, edit_size) {
  var data = { mode: mode, edit_id: edit_id, edit_size: edit_size };
  jQuery.ajax({
    url: "/ssp/admin/parser/update_cart.php",
    method: "POST",
    data: data,
    success: function() {
      location.reload();
    },
    error: function() {
      alert("Something went wrong in update_cart.php");
    }
  });
}

function add_to_cart() {
  jQuery("#modal_errors").html("");
  var size = jQuery("#size-id").val();
  var quantity = jQuery("#quantity-id").val();
  var available = parseInt(jQuery("#available-id").val());
  var error = "";
  var data = jQuery("#add_product_form").serialize();

  if (size == "" || quantity == "" || quantity == 0) {
    error +=
      "<p class='text-white text-center bg-danger'>You must choose a size and quantity</p>";
    jQuery("#modal_errors").html(error);
    return;
  } else if (quantity > available) {
    error +=
      "<p class='text-white text-center bg-danger'>There are only " +
      available +
      " available</p>";
    jQuery("#modal_errors").html(error);
    return;
  } else {
    jQuery.ajax({
      method: "post",
      url: "/ssp/admin/parser/add_cart.php",
      data: data,
      success: function() {
        location.reload();
      },
      error: function() {
        alert("Something is wrong");
      }
    });
  }
}

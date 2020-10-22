function showSnackbar(msg) {
  // Get the snackbar DIV
  var x = document.getElementById("snackbar");

  $("#snackbar").html(msg);

  // Add the "show" class to DIV
  x.className = "show";

  // After 3 seconds, remove the show class from DIV
  setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
}

function showSnackBarFromErrors(data)
{
  var errors = data.responseJSON;
  var msg = '';

  for (let field in errors.errors)
  {
      for (let err_msg in errors.errors[field])
      {
        msg += errors.errors[field][err_msg] + '<br>';
      }
  }

  showSnackbar(msg);
}

function showSnackBarFromAxiosErrors(response)
{
  var msg = '';

  for (let field in response.response.data.errors)
  {
    msg += response.response.data.errors[field] + '<br>';
  }

  showSnackbar(msg);
}